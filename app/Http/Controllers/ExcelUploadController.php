<?php

namespace App\Http\Controllers;

use App\Models\EmployeeProfile;
use App\Models\PersonnelType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;
use Symfony\Component\HttpFoundation\HeaderUtils;

class ExcelUploadController extends Controller
{
    public function upload(Request $request, string $cardType, string $employeeNumber): RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'extensions:xlsx', 'max:10240'],
        ]);

        $profile = $this->profileForCardType($employeeNumber, $cardType);
        $xlsx = SimpleXLSX::parse($request->file('excel_file')->getRealPath());

        if (! $xlsx) {
            throw ValidationException::withMessages([
                'excel_file' => 'The Excel file could not be read. Please use the downloaded template.',
            ]);
        }

        $rows = $xlsx->rows();
        $headers = $this->headersFor($cardType);

        if ($rows === [] || ! $this->headersMatch($rows[0], $headers)) {
            throw ValidationException::withMessages([
                'excel_file' => "The workbook does not match the {$profile->personnelType->name} leave-card template.",
            ]);
        }

        $records = [];

        foreach (array_slice($rows, 1) as $index => $row) {
            if ($this->rowIsEmpty($row)) {
                continue;
            }

            $rowNumber = $index + 2;
            $records[] = match ($cardType) {
                PersonnelType::CODE_TEACHING => $this->teachingRecord($row, $rowNumber),
                PersonnelType::CODE_NON_TEACHING => $this->nonTeachingRecord($row, $rowNumber),
            };
        }

        if ($records === []) {
            throw ValidationException::withMessages([
                'excel_file' => 'The workbook contains no leave-card rows to import.',
            ]);
        }

        DB::transaction(function () use ($profile, $cardType, $records) {
            match ($cardType) {
                PersonnelType::CODE_TEACHING => $profile->teachingLeaveCards()->createMany($records),
                PersonnelType::CODE_NON_TEACHING => $profile->nonTeachingLeaveCards()->createMany($records),
            };
        });

        return back()->with('success', count($records).' leave-card row(s) imported successfully.');
    }

    public function downloadTemplate(string $cardType, string $employeeNumber): Response
    {
        $profile = $this->profileForCardType($employeeNumber, $cardType);
        $xlsx = SimpleXLSXGen::fromArray([$this->headersFor($cardType)]);
        $filename = "{$profile->user->name} - {$profile->personnelType->name} Leave Card Template.xlsx";

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $filename,
                Str::ascii($filename),
            ),
        ]);
    }

    private function profileForCardType(string $employeeNumber, string $cardType): EmployeeProfile
    {
        $profile = EmployeeProfile::with(['user', 'personnelType'])
            ->where('employee_number', $employeeNumber)
            ->firstOrFail();

        if ($profile->personnelType->code !== $cardType) {
            throw ValidationException::withMessages([
                'card_type' => 'The selected leave-card format does not match this employee profile.',
            ]);
        }

        return $profile;
    }

    /** @return list<string> */
    private function headersFor(string $cardType): array
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

    private function headersMatch(array $actual, array $expected): bool
    {
        $actual = array_slice(array_pad($actual, count($expected), null), 0, count($expected));

        return array_map($this->normalizeHeader(...), $actual)
            === array_map($this->normalizeHeader(...), $expected);
    }

    private function normalizeHeader(mixed $value): string
    {
        return (string) Str::of((string) $value)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '');
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function teachingRecord(array $row, int $rowNumber): array
    {
        $row = array_pad($row, 11, null);

        return [
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
        ];
    }

    private function nonTeachingRecord(array $row, int $rowNumber): array
    {
        $row = array_pad($row, 11, null);

        return [
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
        ];
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
            throw ValidationException::withMessages([
                'excel_file' => "Row {$rowNumber}: {$column} must be numeric.",
            ]);
        }

        return $value + 0;
    }
}
