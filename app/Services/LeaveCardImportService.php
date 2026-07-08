<?php

namespace App\Services;

use App\Models\EmployeeProfile;
use App\Models\ImportBatch;
use App\Models\PersonnelType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Shuchkin\SimpleXLSX;
use Throwable;

class LeaveCardImportService
{
    private const PARSER_VERSION = '1';

    public function __construct(
        private readonly LeaveCardNormalizer $normalizer,
        private readonly AuditService $audit,
    ) {}

    public function profile(string $employeeNumber): EmployeeProfile
    {
        return EmployeeProfile::with(['user', 'personnelType'])
            ->where('employee_number', $employeeNumber)
            ->firstOrFail();
    }

    public function profileForCardType(string $employeeNumber, string $cardType): EmployeeProfile
    {
        $profile = $this->profile($employeeNumber);

        if ($profile->personnelType->code !== $cardType) {
            throw ValidationException::withMessages([
                'card_type' => 'The selected leave-card format does not match this employee profile.',
            ]);
        }

        return $profile;
    }

    /** @return list<string> */
    public function headersFor(string $cardType): array
    {
        return match ($cardType) {
            PersonnelType::CODE_TEACHING => [
                'Inclusive Period',
                'Nature of Activity',
                'No. of Days Credited',
                'DSO No. (Vacation Service)',
                'Inclusive Leave Dates',
                'Days With Pay',
                'Service Credit Balance',
                'Days Without Pay',
                'Nature of Leave',
                'DSO No. (Record of Leave)',
                'Remarks',
            ],
            PersonnelType::CODE_NON_TEACHING => [
                'Period',
                'Particulars',
                'Vacation Leave Earned',
                'Vacation Leave With Pay',
                'Vacation Leave Balance',
                'Vacation Leave Without Pay',
                'Sick Leave Earned',
                'Sick Leave With Pay',
                'Sick Leave Balance',
                'Sick Leave Without Pay',
                'Date & Action On Application For Leave',
            ],
        };
    }

    public function createPreview(UploadedFile $file, EmployeeProfile $profile, User $admin): ImportBatch
    {
        $batch = new ImportBatch([
            'admin_user_id' => $admin->getKey(),
            'employee_profile_id' => $profile->getKey(),
            'card_type' => $profile->personnelType->code,
            'parser_version' => self::PARSER_VERSION,
            'original_name' => Str::limit($file->getClientOriginalName(), 255, ''),
            'file_hash' => hash_file('sha256', $file->getRealPath()),
            'status' => 'validated',
            'expires_at' => now()->addMinutes(config('analytics.import_preview_minutes', 30)),
        ]);
        $batch->save();
        $path = $file->storeAs("import-previews/{$batch->getKey()}", 'workbook.xlsx', 'local');
        try {
            $parsed = $this->parse(Storage::disk('local')->path($path), $profile);
        } catch (Throwable $exception) {
            Storage::disk('local')->deleteDirectory("import-previews/{$batch->getKey()}");
            $errors = $exception instanceof ValidationException
                ? collect($exception->errors())->flatten()->values()->all()
                : ['The workbook could not be processed.'];
            $batch->update([
                'status' => 'failed',
                'error_count' => count($errors),
                'preview_data' => ['errors' => $errors],
            ]);
            $this->audit->record(
                'import.preview_failed',
                'ImportBatch',
                $batch->getKey(),
                $batch->original_name,
                after: ['status' => 'failed', 'error_count' => count($errors), 'card_type' => $batch->card_type],
                metadata: ['file_name' => $batch->original_name],
                employeeNumber: $profile->employee_number,
                actor: $admin,
            );

            throw $exception;
        }
        $batch->update([
            'stored_path' => $path,
            'row_count' => count($parsed['rows']),
            'error_count' => collect($parsed['rows'])->filter(fn (array $row) => ! empty($row['errors']))->count(),
            'preview_data' => $parsed,
        ]);
        $this->audit->record(
            'import.previewed',
            'ImportBatch',
            $batch->getKey(),
            $batch->original_name,
            after: [
                'status' => $batch->status,
                'card_type' => $batch->card_type,
                'row_count' => $batch->row_count,
                'error_count' => $batch->error_count,
            ],
            metadata: ['file_name' => $batch->original_name],
            employeeNumber: $profile->employee_number,
            actor: $admin,
        );

        return $batch->fresh(['employeeProfile.user', 'employeeProfile.personnelType']);
    }

    public function confirm(ImportBatch $batch, User $admin): ImportBatch
    {
        return DB::transaction(function () use ($batch, $admin) {
            $batch = ImportBatch::query()->lockForUpdate()->findOrFail($batch->getKey());
            $this->authorizeBatch($batch, $admin);

            if ($batch->status !== 'validated' || $batch->error_count > 0) {
                throw ValidationException::withMessages(['batch' => 'This preview cannot be confirmed.']);
            }

            if ($batch->parser_version !== self::PARSER_VERSION) {
                throw ValidationException::withMessages(['batch' => 'This preview was created by an older importer. Please upload the workbook again.']);
            }

            if (! $batch->expires_at || $batch->expires_at->isPast()) {
                throw ValidationException::withMessages(['batch' => 'This preview has expired. Please upload the workbook again.']);
            }

            if (! $batch->stored_path || ! Storage::disk('local')->exists($batch->stored_path)) {
                throw ValidationException::withMessages(['batch' => 'The staged workbook is no longer available.']);
            }

            $path = Storage::disk('local')->path($batch->stored_path);
            if (! hash_equals($batch->file_hash, hash_file('sha256', $path))) {
                throw ValidationException::withMessages(['batch' => 'The staged workbook failed its integrity check.']);
            }

            $profile = $batch->employeeProfile()->with(['user', 'personnelType'])->firstOrFail();
            $parsed = $this->parse($path, $profile);

            if (collect($parsed['rows'])->contains(fn (array $row) => ! empty($row['errors']))) {
                throw ValidationException::withMessages(['batch' => 'The workbook now contains blocking validation errors.']);
            }

            $batch->update(['status' => 'committing']);
            $records = collect($parsed['rows'])->map(fn (array $row) => [
                ...$row['attributes'],
                'import_batch_id' => $batch->getKey(),
                'source_row_number' => $row['row_number'],
            ])->all();

            match ($batch->card_type) {
                PersonnelType::CODE_TEACHING => $profile->teachingLeaveCards()->createMany($records),
                PersonnelType::CODE_NON_TEACHING => $profile->nonTeachingLeaveCards()->createMany($records),
            };

            $batch->update([
                'status' => 'completed',
                'row_count' => count($records),
                'committed_at' => now(),
                'preview_data' => null,
            ]);
            Storage::disk('local')->delete($batch->stored_path);
            Storage::disk('local')->deleteDirectory("import-previews/{$batch->getKey()}");
            $batch->update(['stored_path' => null]);
            $this->audit->record(
                'import.committed',
                'ImportBatch',
                $batch->getKey(),
                $batch->original_name,
                ['status' => 'validated'],
                ['status' => 'completed', 'row_count' => count($records), 'card_type' => $batch->card_type],
                metadata: ['file_name' => $batch->original_name],
                employeeNumber: $profile->employee_number,
                actor: $admin,
            );

            return $batch->fresh();
        });
    }

    public function importImmediately(UploadedFile $file, EmployeeProfile $profile, User $admin): ImportBatch
    {
        $parsed = $this->parse($file->getRealPath(), $profile);
        $errors = collect($parsed['rows'])->filter(fn (array $row) => ! empty($row['errors']));

        if ($errors->isNotEmpty()) {
            throw ValidationException::withMessages([
                'excel_file' => $errors->first()['errors'][0],
            ]);
        }

        return DB::transaction(function () use ($file, $profile, $admin, $parsed) {
            $batch = ImportBatch::query()->create([
                'admin_user_id' => $admin->getKey(),
                'employee_profile_id' => $profile->getKey(),
                'card_type' => $profile->personnelType->code,
                'parser_version' => self::PARSER_VERSION,
                'original_name' => Str::limit($file->getClientOriginalName(), 255, ''),
                'file_hash' => hash_file('sha256', $file->getRealPath()),
                'status' => 'completed',
                'row_count' => count($parsed['rows']),
                'error_count' => 0,
                'committed_at' => now(),
            ]);
            $records = collect($parsed['rows'])->map(fn (array $row) => [
                ...$row['attributes'],
                'import_batch_id' => $batch->getKey(),
                'source_row_number' => $row['row_number'],
            ])->all();
            match ($profile->personnelType->code) {
                PersonnelType::CODE_TEACHING => $profile->teachingLeaveCards()->createMany($records),
                PersonnelType::CODE_NON_TEACHING => $profile->nonTeachingLeaveCards()->createMany($records),
            };
            $this->audit->record(
                'import.committed',
                'ImportBatch',
                $batch->getKey(),
                $batch->original_name,
                after: ['status' => 'completed', 'row_count' => count($records), 'card_type' => $batch->card_type],
                metadata: ['file_name' => $batch->original_name, 'legacy_direct_import' => true],
                employeeNumber: $profile->employee_number,
                actor: $admin,
            );

            return $batch;
        });
    }

    public function rollback(ImportBatch $batch, User $admin, string $reason): ImportBatch
    {
        return DB::transaction(function () use ($batch, $admin, $reason) {
            $batch = ImportBatch::query()->lockForUpdate()->findOrFail($batch->getKey());
            $this->authorizeBatch($batch, $admin);

            if ($batch->status !== 'completed') {
                throw ValidationException::withMessages(['batch' => 'Only completed imports can be rolled back.']);
            }

            $relation = $batch->card_type === PersonnelType::CODE_TEACHING
                ? $batch->teachingLeaveCards()
                : $batch->nonTeachingLeaveCards();
            $rows = $relation->get();

            if ($rows->count() !== $batch->row_count || $rows->contains(fn ($row) => $row->updated_at->gt($batch->committed_at))) {
                throw ValidationException::withMessages([
                    'batch' => 'Rollback is unavailable because an imported row was changed or removed.',
                ]);
            }

            $relation->delete();
            $batch->update([
                'status' => 'rolled_back',
                'rolled_back_at' => now(),
                'rollback_reason' => $reason,
            ]);
            $this->audit->record(
                'import.rolled_back',
                'ImportBatch',
                $batch->getKey(),
                $batch->original_name,
                ['status' => 'completed', 'row_count' => $batch->row_count],
                ['status' => 'rolled_back', 'deleted_rows' => $rows->count()],
                $reason,
                metadata: ['file_name' => $batch->original_name],
                employeeNumber: $batch->employeeProfile?->employee_number,
                actor: $admin,
            );

            return $batch->fresh();
        });
    }

    public function authorizeBatch(ImportBatch $batch, User $admin): void
    {
        abort_unless($batch->admin_user_id === $admin->getKey(), 404);
    }

    /** @return array{rows: list<array<string, mixed>>} */
    public function parse(string $path, EmployeeProfile $profile): array
    {
        $xlsx = SimpleXLSX::parse($path);
        if (! $xlsx) {
            throw ValidationException::withMessages([
                'excel_file' => 'The Excel file could not be read. Please use the downloaded template.',
            ]);
        }

        $source = $xlsx->rows();
        $headers = $this->headersFor($profile->personnelType->code);
        if ($source === [] || ! $this->headersMatch($source[0], $headers)) {
            throw ValidationException::withMessages([
                'excel_file' => "The workbook does not match the {$profile->personnelType->name} leave-card template.",
            ]);
        }

        if (count($source) - 1 > config('analytics.import_max_rows', 5000)) {
            throw ValidationException::withMessages([
                'excel_file' => 'The workbook exceeds the 5,000-row import limit.',
            ]);
        }

        $rows = [];
        $seen = [];
        $existing = $this->existingFingerprints($profile);

        foreach (array_slice($source, 1) as $index => $sourceRow) {
            if ($this->rowIsEmpty($sourceRow)) {
                continue;
            }

            $rowNumber = $index + 2;
            try {
                $attributes = match ($profile->personnelType->code) {
                    PersonnelType::CODE_TEACHING => $this->teachingRecord($sourceRow, $rowNumber),
                    PersonnelType::CODE_NON_TEACHING => $this->nonTeachingRecord($sourceRow, $rowNumber),
                };
            } catch (ValidationException $exception) {
                $rows[] = [
                    'row_number' => $rowNumber,
                    'attributes' => [],
                    'period' => $this->nullableText($sourceRow[0] ?? null),
                    'description' => $this->nullableText($sourceRow[1] ?? null),
                    'parse_state' => LeaveCardNormalizer::UNPARSEABLE,
                    'warnings' => [],
                    'errors' => collect($exception->errors())->flatten()->values()->all(),
                    'fingerprint' => null,
                ];

                continue;
            }
            $fingerprint = $this->fingerprint($profile->personnelType->code, $attributes);
            $errors = [];

            if (isset($seen[$fingerprint])) {
                $errors[] = "Row {$rowNumber} duplicates row {$seen[$fingerprint]} in this workbook.";
            } elseif ($existing->contains($fingerprint)) {
                $errors[] = "Row {$rowNumber} exactly matches an existing leave-card row.";
            }
            $seen[$fingerprint] = $rowNumber;

            $warnings = $attributes['parse_state'] === LeaveCardNormalizer::UNPARSEABLE
                ? [$attributes['parse_note'] ?? 'Some values could not be normalized.']
                : [];
            $rows[] = [
                'row_number' => $rowNumber,
                'attributes' => $attributes,
                'period' => $attributes['inclusive_period'] ?? $attributes['period'] ?? null,
                'description' => $attributes['nature_of_leave'] ?? $attributes['particulars'] ?? $attributes['nature_of_activity'] ?? null,
                'parse_state' => $attributes['parse_state'],
                'warnings' => $warnings,
                'errors' => $errors,
                'fingerprint' => $fingerprint,
            ];
        }

        if ($rows === []) {
            throw ValidationException::withMessages(['excel_file' => 'The workbook contains no leave-card rows to import.']);
        }

        return ['rows' => $rows];
    }

    private function existingFingerprints(EmployeeProfile $profile): Collection
    {
        return $profile->leaveCardQuery()->get()->map(
            fn ($card) => $this->fingerprint($profile->personnelType->code, $card->getAttributes()),
        );
    }

    private function fingerprint(string $cardType, array $attributes): string
    {
        $keys = $cardType === PersonnelType::CODE_TEACHING
            ? ['inclusive_period', 'nature_of_activity', 'days_credited', 'inclusive_leave_dates', 'days_with_pay', 'days_without_pay', 'nature_of_leave']
            : ['period', 'particulars', 'vacation_leave_earned', 'vacation_leave_with_pay', 'vacation_leave_balance', 'vacation_leave_without_pay', 'sick_leave_earned', 'sick_leave_with_pay', 'sick_leave_balance', 'sick_leave_without_pay'];
        $values = collect($keys)->mapWithKeys(fn (string $key) => [
            $key => $this->fingerprintValue($key, $attributes[$key] ?? null),
        ]);

        return hash('sha256', json_encode($values->all(), JSON_PRESERVE_ZERO_FRACTION));
    }

    private function fingerprintValue(string $key, mixed $value): mixed
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = Str::of((string) $value)->lower()->squish()->toString();

        if ($key === 'inclusive_leave_dates' && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return substr($value, 0, 10);
        }

        return $value;
    }

    private function headersMatch(array $actual, array $expected): bool
    {
        $actual = array_slice(array_pad($actual, count($expected), null), 0, count($expected));

        return array_map($this->normalizeHeader(...), $actual)
            === array_map($this->normalizeHeader(...), $expected);
    }

    private function normalizeHeader(mixed $value): string
    {
        return (string) Str::of((string) $value)->lower()->replaceMatches('/[^a-z0-9]+/', '');
    }

    private function rowIsEmpty(array $row): bool
    {
        return collect($row)->every(fn ($value) => $value === null || trim((string) $value) === '');
    }

    private function teachingRecord(array $row, int $rowNumber): array
    {
        $row = array_pad($row, 11, null);

        return $this->normalizer->teaching([
            'inclusive_period' => $this->nullableText($row[0]),
            'nature_of_activity' => $this->nullableText($row[1]),
            'days_credited' => $this->nullableNumber($row[2], $rowNumber, 'No. of Days Credited'),
            'vacation_service_dso_number' => $this->nullableText($row[3]),
            'inclusive_leave_dates' => $this->nullableText($row[4]),
            'days_with_pay' => $this->nullableNumber($row[5], $rowNumber, 'Days With Pay'),
            'service_credit_balance' => $this->nullableNumber($row[6], $rowNumber, 'Service Credit Balance'),
            'days_without_pay' => $this->nullableNumber($row[7], $rowNumber, 'Days Without Pay'),
            'nature_of_leave' => $this->nullableText($row[8]),
            'record_of_leave_dso_number' => $this->nullableText($row[9]),
            'remarks' => $this->nullableText($row[10]),
        ]);
    }

    private function nonTeachingRecord(array $row, int $rowNumber): array
    {
        $row = array_pad($row, 11, null);

        return $this->normalizer->nonTeaching([
            'period' => $this->nullableText($row[0]),
            'particulars' => $this->nullableText($row[1]),
            'vacation_leave_earned' => $this->nullableNumber($row[2], $rowNumber, 'Vacation Leave Earned'),
            'vacation_leave_with_pay' => $this->nullableText($row[3]),
            'vacation_leave_balance' => $this->nullableText($row[4]),
            'vacation_leave_without_pay' => $this->nullableNumber($row[5], $rowNumber, 'Vacation Leave Without Pay'),
            'sick_leave_earned' => $this->nullableNumber($row[6], $rowNumber, 'Sick Leave Earned'),
            'sick_leave_with_pay' => $this->nullableNumber($row[7], $rowNumber, 'Sick Leave With Pay'),
            'sick_leave_balance' => $this->nullableText($row[8]),
            'sick_leave_without_pay' => $this->nullableText($row[9]),
            'leave_application_action' => $this->nullableText($row[10]),
        ]);
    }

    private function nullableText(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function nullableNumber(mixed $value, int $rowNumber, string $column): int|float|null
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (! is_numeric($value)) {
            throw ValidationException::withMessages(['excel_file' => "Row {$rowNumber}: {$column} must be numeric."]);
        }

        return $value + 0;
    }
}
