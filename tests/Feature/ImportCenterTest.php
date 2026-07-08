<?php

use App\Models\ImportBatch;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Shuchkin\SimpleXLSXGen;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

test('an admin can preview a workbook without inserting leave rows', function () {
    [$admin] = importCenterEmployee('teaching', 'IMPORT-PREVIEW');

    $response = $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-PREVIEW',
        'excel_file' => importCenterTeachingWorkbook(),
    ]);

    $batch = ImportBatch::query()->firstOrFail();
    $response->assertRedirect(route('admin.import-center', ['batch' => $batch->getKey()]));
    expect($batch->status)->toBe('validated')
        ->and($batch->row_count)->toBe(1)
        ->and($batch->error_count)->toBe(0)
        ->and(data_get($batch->preview_data, 'rows.0.row_number'))->toBe(2);
    $this->assertDatabaseHas('audit_events', ['action' => 'import.previewed', 'target_id' => $batch->getKey()]);
    $this->assertDatabaseCount('teaching_leave_cards', 0);
    Storage::disk('local')->assertExists($batch->stored_path);

    $this->get(route('admin.import-center', ['batch' => $batch->getKey()]))
        ->assertOk()
        ->assertSee('IMPORT-PREVIEW')
        ->assertSee('importHistoryTable', false);
});

test('confirm imports the staged rows with batch lineage and removes the staged file', function () {
    [$admin, $employee] = importCenterEmployee('teaching', 'IMPORT-CONFIRM');
    $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-CONFIRM',
        'excel_file' => importCenterTeachingWorkbook(),
    ]);
    $batch = ImportBatch::query()->firstOrFail();
    $stagedPath = $batch->stored_path;

    $this->post(route('admin.import-center.confirm', $batch))
        ->assertRedirect(route('admin.import-center'))
        ->assertSessionHas('success');

    $batch->refresh();
    expect($batch->status)->toBe('completed')
        ->and($batch->stored_path)->toBeNull()
        ->and($batch->committed_at)->not->toBeNull();
    $this->assertDatabaseHas('teaching_leave_cards', [
        'employee_profile_id' => $employee->getKey(),
        'import_batch_id' => $batch->getKey(),
        'source_row_number' => 2,
        'nature_of_activity' => 'Training',
    ]);
    $this->assertDatabaseHas('audit_events', ['action' => 'import.committed', 'target_id' => $batch->getKey()]);
    Storage::disk('local')->assertMissing($stagedPath);
});

test('exact existing and in-workbook duplicates block confirmation', function () {
    [$admin, $employee] = importCenterEmployee('teaching', 'IMPORT-DUPLICATE');
    TeachingLeaveCard::query()->create([
        'employee_profile_id' => $employee->getKey(),
        'inclusive_period' => 'July 2026',
        'nature_of_activity' => 'Training',
        'days_credited' => 5,
        'inclusive_leave_dates' => '2026-07-08',
        'days_with_pay' => 1,
        'days_without_pay' => 0,
        'nature_of_leave' => 'Vacation',
    ]);

    $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-DUPLICATE',
        'excel_file' => importCenterTeachingWorkbook(2),
    ]);
    $batch = ImportBatch::query()->firstOrFail();

    expect($batch->error_count)->toBe(2)
        ->and(data_get($batch->preview_data, 'rows.0.errors.0'))->toContain('existing leave-card row')
        ->and(data_get($batch->preview_data, 'rows.1.errors.0'))->toContain('duplicates row 2');

    $this->post(route('admin.import-center.confirm', $batch))
        ->assertSessionHasErrors('batch');
    $this->assertDatabaseCount('teaching_leave_cards', 1);
});

test('warnings require acknowledgement before confirmation', function () {
    [$admin] = importCenterEmployee('teaching', 'IMPORT-WARNING');
    $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-WARNING',
        'excel_file' => importCenterTeachingWorkbook(1, 'Legacy period text'),
    ]);
    $batch = ImportBatch::query()->firstOrFail();

    expect(data_get($batch->preview_data, 'rows.0.warnings.0'))->toContain('Reporting period');
    $this->post(route('admin.import-center.confirm', $batch))
        ->assertSessionHasErrors('warnings_acknowledged');
    $this->assertDatabaseCount('teaching_leave_cards', 0);

    $this->post(route('admin.import-center.confirm', $batch), ['warnings_acknowledged' => '1'])
        ->assertSessionHas('success');
    $this->assertDatabaseCount('teaching_leave_cards', 1);
});

test('malformed numeric values are shown as row-level blocking errors', function () {
    [$admin] = importCenterEmployee('teaching', 'IMPORT-NUMERIC');
    $workbook = UploadedFile::fake()->createWithContent(
        'leave-card.xlsx',
        (string) SimpleXLSXGen::fromArray([
            importCenterTeachingHeaders(),
            ['July 2026', 'Training', 'five', 'DSO-1', '2026-07-08', 1, 4, 0, 'Vacation', 'ROL-1', 'Approved'],
        ]),
    );

    $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-NUMERIC',
        'excel_file' => $workbook,
    ]);
    $batch = ImportBatch::query()->firstOrFail();

    expect($batch->status)->toBe('validated')
        ->and($batch->error_count)->toBe(1)
        ->and(data_get($batch->preview_data, 'rows.0.errors.0'))->toContain('Row 2');
    $this->assertDatabaseCount('teaching_leave_cards', 0);
});

test('tampered and expired previews cannot be confirmed', function () {
    [$admin] = importCenterEmployee('teaching', 'IMPORT-SAFE');
    $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-SAFE',
        'excel_file' => importCenterTeachingWorkbook(),
    ]);
    $batch = ImportBatch::query()->firstOrFail();
    Storage::disk('local')->put($batch->stored_path, 'changed');

    $this->post(route('admin.import-center.confirm', $batch))
        ->assertSessionHasErrors('batch');
    $this->assertDatabaseCount('teaching_leave_cards', 0);

    $batch->update(['expires_at' => now()->subMinute()]);
    $this->post(route('admin.import-center.confirm', $batch))
        ->assertSessionHasErrors('batch');
    $this->assertDatabaseCount('teaching_leave_cards', 0);
});

test('an import preview is bound to the admin who uploaded it', function () {
    [$admin] = importCenterEmployee('teaching', 'IMPORT-OWNER');
    $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-OWNER',
        'excel_file' => importCenterTeachingWorkbook(),
    ]);
    $batch = ImportBatch::query()->firstOrFail();
    $otherAdmin = User::factory()->create(['usertype' => 'admin']);

    $this->actingAs($otherAdmin)
        ->post(route('admin.import-center.confirm', $batch))
        ->assertNotFound();
    $this->assertDatabaseCount('teaching_leave_cards', 0);
});

test('a completed unchanged import can be rolled back with a reason', function () {
    [$admin] = importCenterEmployee('teaching', 'IMPORT-ROLLBACK');
    $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-ROLLBACK',
        'excel_file' => importCenterTeachingWorkbook(),
    ]);
    $batch = ImportBatch::query()->firstOrFail();
    $this->post(route('admin.import-center.confirm', $batch));

    $this->post(route('admin.import-center.rollback', $batch), [
        'rollback_reason' => 'Uploaded to the wrong employee.',
    ])->assertSessionHas('success');

    $batch->refresh();
    expect($batch->status)->toBe('rolled_back')
        ->and($batch->rollback_reason)->toBe('Uploaded to the wrong employee.')
        ->and($batch->rolled_back_at)->not->toBeNull();
    $this->assertDatabaseHas('audit_events', [
        'action' => 'import.rolled_back',
        'target_id' => $batch->getKey(),
        'reason' => 'Uploaded to the wrong employee.',
    ]);
    $this->assertDatabaseCount('teaching_leave_cards', 0);
});

test('rollback is blocked after an imported row changes', function () {
    [$admin] = importCenterEmployee('teaching', 'IMPORT-CHANGED');
    $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-CHANGED',
        'excel_file' => importCenterTeachingWorkbook(),
    ]);
    $batch = ImportBatch::query()->firstOrFail();
    $this->post(route('admin.import-center.confirm', $batch));
    $batch->refresh();
    DB::table('teaching_leave_cards')
        ->where('import_batch_id', $batch->getKey())
        ->update(['updated_at' => $batch->committed_at->copy()->addMinute()]);

    $this->post(route('admin.import-center.rollback', $batch), [
        'rollback_reason' => 'Testing protected rollback.',
    ])->assertSessionHasErrors('batch');

    expect($batch->fresh()->status)->toBe('completed');
    $this->assertDatabaseCount('teaching_leave_cards', 1);
});

test('the cleanup command expires abandoned previews and removes private files', function () {
    [$admin] = importCenterEmployee('teaching', 'IMPORT-EXPIRED');
    $this->actingAs($admin)->post(route('admin.import-center.preview'), [
        'employee_number' => 'IMPORT-EXPIRED',
        'excel_file' => importCenterTeachingWorkbook(),
    ]);
    $batch = ImportBatch::query()->firstOrFail();
    $stagedPath = $batch->stored_path;
    $batch->update(['expires_at' => now()->subMinute()]);

    Artisan::call('imports:cleanup');

    $batch->refresh();
    expect($batch->status)->toBe('expired')
        ->and($batch->stored_path)->toBeNull()
        ->and($batch->preview_data)->toBeNull();
    Storage::disk('local')->assertMissing($stagedPath);
});

function importCenterEmployee(string $cardType, string $employeeNumber): array
{
    $admin = User::factory()->create(['usertype' => 'admin']);
    $personnelType = PersonnelType::query()->where('code', $cardType)->firstOrFail();
    $employee = User::factory()->create(['status' => 'active']);
    $employee->employeeProfile()->create([
        'employee_number' => $employeeNumber,
        'personnel_type_id' => $personnelType->getKey(),
    ]);

    return [$admin, $employee];
}

function importCenterTeachingWorkbook(int $rows = 1, string $period = 'July 2026'): UploadedFile
{
    $data = [importCenterTeachingHeaders()];

    for ($index = 0; $index < $rows; $index++) {
        $data[] = [$period, 'Training', 5, 'DSO-1', '2026-07-08', 1, 4, 0, 'Vacation', 'ROL-1', 'Approved'];
    }

    return UploadedFile::fake()->createWithContent(
        'leave-card.xlsx',
        (string) SimpleXLSXGen::fromArray($data),
    );
}

function importCenterTeachingHeaders(): array
{
    return [
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
    ];
}
