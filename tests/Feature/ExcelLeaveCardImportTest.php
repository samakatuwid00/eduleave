<?php

use App\Models\PersonnelType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

uses(RefreshDatabase::class);

test('teaching workbook imports only into teaching leave cards', function () {
    [$admin, $user] = employeeForExcelImport(PersonnelType::CODE_TEACHING, 'EMP-EXCEL-TEACHING');
    $file = leaveCardWorkbook([
        teachingExcelHeaders(),
        ['June 2026', 'Summer training', 5, 'DSO-T-1', '2026-07-01', 1, 4, 0, 'Vacation', 'ROL-T-1', 'Approved'],
    ]);

    $this->actingAs($admin)
        ->post(route('admin.leave-card.import', [PersonnelType::CODE_TEACHING, 'EMP-EXCEL-TEACHING']), [
            'excel_file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('teaching_leave_cards', [
        'employee_profile_id' => $user->getKey(),
        'nature_of_activity' => 'Summer training',
        'days_credited' => 5,
        'vacation_service_dso_number' => 'DSO-T-1',
    ]);
    $this->assertDatabaseCount('non_teaching_leave_cards', 0);
});

test('non teaching workbook imports only into non teaching leave cards', function () {
    [$admin, $user] = employeeForExcelImport(PersonnelType::CODE_NON_TEACHING, 'EMP-EXCEL-NON-TEACHING');
    $file = leaveCardWorkbook([
        nonTeachingExcelHeaders(),
        ['July 2026', 'Vacation leave', 1.25, '1 day', '10 days', 0, 1.25, 0, '8 days', 'None', 'Approved by SDS'],
    ]);

    $this->actingAs($admin)
        ->post(route('admin.leave-card.import', [PersonnelType::CODE_NON_TEACHING, 'EMP-EXCEL-NON-TEACHING']), [
            'excel_file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('non_teaching_leave_cards', [
        'employee_profile_id' => $user->getKey(),
        'particulars' => 'Vacation leave',
        'vacation_leave_earned' => 1.25,
        'leave_application_action' => 'Approved by SDS',
    ]);
    $this->assertDatabaseCount('teaching_leave_cards', 0);
});

test('import rejects a workbook for the other card format without partial inserts', function () {
    [$admin] = employeeForExcelImport(PersonnelType::CODE_TEACHING, 'EMP-WRONG-WORKBOOK');
    $file = leaveCardWorkbook([
        nonTeachingExcelHeaders(),
        ['July 2026', 'Vacation leave', 1.25, '1 day', '10 days', 0, 1.25, 0, '8 days', 'None', 'Approved'],
    ]);

    $this->actingAs($admin)
        ->from('/admin/leave_card/EMP-WRONG-WORKBOOK')
        ->post(route('admin.leave-card.import', [PersonnelType::CODE_TEACHING, 'EMP-WRONG-WORKBOOK']), [
            'excel_file' => $file,
        ])
        ->assertRedirect('/admin/leave_card/EMP-WRONG-WORKBOOK')
        ->assertSessionHasErrors('excel_file');

    $this->assertDatabaseCount('teaching_leave_cards', 0);
    $this->assertDatabaseCount('non_teaching_leave_cards', 0);
});

test('import rejects a card type that does not match the employee profile', function () {
    [$admin] = employeeForExcelImport(PersonnelType::CODE_TEACHING, 'EMP-WRONG-TYPE');
    $file = leaveCardWorkbook([
        nonTeachingExcelHeaders(),
        ['July 2026', 'Vacation leave', 1.25, '1 day', '10 days', 0, 1.25, 0, '8 days', 'None', 'Approved'],
    ]);

    $this->actingAs($admin)
        ->post(route('admin.leave-card.import', [PersonnelType::CODE_NON_TEACHING, 'EMP-WRONG-TYPE']), [
            'excel_file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('card_type');

    $this->assertDatabaseCount('teaching_leave_cards', 0);
    $this->assertDatabaseCount('non_teaching_leave_cards', 0);
});

test('template download returns the headers for the employee card format', function () {
    [$admin] = employeeForExcelImport(PersonnelType::CODE_TEACHING, 'EMP-TEMPLATE-TEACHING');
    employeeForExcelImport(PersonnelType::CODE_NON_TEACHING, 'EMP-TEMPLATE-NON-TEACHING', $admin);

    $teachingResponse = $this->actingAs($admin)
        ->get(route('admin.leave-card.template', [PersonnelType::CODE_TEACHING, 'EMP-TEMPLATE-TEACHING']))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $nonTeachingResponse = $this
        ->get(route('admin.leave-card.template', [PersonnelType::CODE_NON_TEACHING, 'EMP-TEMPLATE-NON-TEACHING']))
        ->assertOk();

    expect(SimpleXLSX::parseData($teachingResponse->getContent())->rows()[0])->toBe(teachingExcelHeaders())
        ->and(SimpleXLSX::parseData($nonTeachingResponse->getContent())->rows()[0])->toBe(nonTeachingExcelHeaders());
});

function employeeForExcelImport(string $cardType, string $employeeNumber, ?User $admin = null): array
{
    $admin ??= User::factory()->create(['usertype' => 'admin']);
    $personnelType = PersonnelType::query()->where('code', $cardType)->firstOrFail();
    $user = User::factory()->create(['status' => 'active']);
    $user->employeeProfile()->create([
        'employee_number' => $employeeNumber,
        'personnel_type_id' => $personnelType->getKey(),
    ]);

    return [$admin, $user];
}

function leaveCardWorkbook(array $rows): UploadedFile
{
    return UploadedFile::fake()->createWithContent(
        'leave-card.xlsx',
        (string) SimpleXLSXGen::fromArray($rows),
    );
}

function teachingExcelHeaders(): array
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

function nonTeachingExcelHeaders(): array
{
    return [
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
    ];
}
