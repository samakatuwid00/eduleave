<?php

use App\Models\ImportBatch;
use App\Models\LeaveType;
use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;
use App\Services\LeaveAnalyticsService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Shuchkin\SimpleXLSX;

uses(RefreshDatabase::class);

test('monthly report preview reconciles with leave analytics', function () {
    [$admin, $teachingEmployee, $nonTeachingEmployee, $vacation] = reportFixture();
    TeachingLeaveCard::query()->create([
        'employee_profile_id' => $teachingEmployee->getKey(),
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'leave_type_id' => $vacation->getKey(),
        'nature_of_leave' => 'Vacation',
        'days_with_pay' => 2,
        'days_without_pay' => 1,
        'service_credit_balance' => 4,
        'parse_state' => 'parsed',
    ]);
    NonTeachingLeaveCard::query()->create([
        'employee_profile_id' => $nonTeachingEmployee->getKey(),
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'leave_type_id' => $vacation->getKey(),
        'particulars' => 'Vacation',
        'vacation_leave_with_pay_value' => 4,
        'vacation_leave_without_pay' => 2,
        'vacation_leave_balance_value' => 8,
        'parse_state' => 'parsed',
    ]);
    $filters = [
        'from' => '2026-01-01',
        'to' => '2026-12-31',
        'personnel_type' => null,
        'leave_type' => null,
        'parse_state' => null,
    ];
    $analytics = app(LeaveAnalyticsService::class)->build($filters);

    $response = $this->actingAs($admin)->get(route('admin.reports', [
        'report' => 'monthly_summary',
        ...$filters,
    ]));

    $response->assertOk()
        ->assertSee('Reports and Exports')
        ->assertSee('reportsPreviewTable', false)
        ->assertSee('Export Excel');
    $report = $response->viewData('report');
    expect($report['totals']['Included records'])->toBe($analytics['kpis']['records'])
        ->and($report['totals']['Paid units'])->toBe($analytics['kpis']['paid'])
        ->and($report['totals']['Unpaid units'])->toBe($analytics['kpis']['unpaid'])
        ->and($report['rows']->sum('paid_total'))->toBe($analytics['kpis']['paid'])
        ->and($report['rows']->sum('unpaid'))->toBe($analytics['kpis']['unpaid']);
});

test('excel export contains metadata, typed data, frozen headers, and safe text', function () {
    [$admin, $teachingEmployee] = reportFixture();
    ImportBatch::query()->create([
        'admin_user_id' => $admin->getKey(),
        'employee_profile_id' => $teachingEmployee->getKey(),
        'card_type' => PersonnelType::CODE_TEACHING,
        'parser_version' => '1',
        'original_name' => '=SUM(1,1).xlsx',
        'file_hash' => str_repeat('a', 64),
        'status' => 'completed',
        'row_count' => 3,
        'committed_at' => '2026-07-08 08:00:00',
        'created_at' => '2026-07-08 08:00:00',
        'updated_at' => '2026-07-08 08:00:00',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.reports.export', [
        'report' => 'import_history',
        'from' => '2026-07-01',
        'to' => '2026-07-31',
    ]));

    $response->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    expect($response->headers->get('cache-control'))->toContain('private')->toContain('no-store');
    $xlsx = SimpleXLSX::parseData($response->getContent());
    expect($xlsx)->not->toBeFalse()
        ->and($xlsx->sheetsCount())->toBe(2)
        ->and($xlsx->sheetName(0))->toBe('Metadata')
        ->and($xlsx->sheetName(1))->toBe('Data')
        ->and($xlsx->rows(0)[0][0])->toBe('EduLeave Report')
        ->and($xlsx->rows(1)[1][4])->toBe('=SUM(1,1).xlsx')
        ->and($xlsx->rowsEx(1)[1][4]['type'])->toBe('s')
        ->and($xlsx->rows(1)[1][6])->toBe(3);
    $this->assertDatabaseHas('audit_events', ['action' => 'report.exported', 'target_id' => 'import_history']);
});

test('employee ledger requires and applies an employee selection', function () {
    [$admin, $teachingEmployee, $nonTeachingEmployee] = reportFixture();
    TeachingLeaveCard::query()->create([
        'employee_profile_id' => $teachingEmployee->getKey(),
        'period_start' => '2026-07-01',
        'days_with_pay' => 2,
        'parse_state' => 'parsed',
    ]);
    NonTeachingLeaveCard::query()->create([
        'employee_profile_id' => $nonTeachingEmployee->getKey(),
        'period_start' => '2026-07-01',
        'vacation_leave_with_pay_value' => 5,
        'parse_state' => 'parsed',
    ]);

    $this->actingAs($admin)
        ->from(route('admin.reports'))
        ->get(route('admin.reports', ['report' => 'employee_ledger']))
        ->assertRedirect(route('admin.reports'))
        ->assertSessionHasErrors('employee_number');

    $response = $this->get(route('admin.reports', [
        'report' => 'employee_ledger',
        'employee_number' => 'REPORT-T',
        'from' => '2026-01-01',
        'to' => '2026-12-31',
    ]))->assertOk();

    expect($response->viewData('report')['rows'])->toHaveCount(1)
        ->and($response->viewData('report')['totals']['Paid units'])->toBe(2.0);
});

test('all registered report providers return valid datasets including empty reports', function () {
    [, $teachingEmployee] = reportFixture();
    $reports = app(ReportService::class);
    $filters = [
        'from' => '2026-01-01',
        'to' => '2026-12-31',
        'personnel_type' => null,
        'leave_type' => null,
        'parse_state' => null,
        'employee_number' => $teachingEmployee->employeeProfile->employee_number,
    ];

    expect($reports->registry())->toHaveCount(8);
    foreach (array_keys($reports->registry()) as $code) {
        $report = $reports->build($code, $filters);
        expect($report['code'])->toBe($code)
            ->and($report['columns'])->not->toBeEmpty()
            ->and($report['rows'])->toBeInstanceOf(Collection::class);
    }
});

test('reports and exports remain admin only and validate unknown report codes', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $normalUser = User::factory()->create(['usertype' => 'user']);

    $this->actingAs($admin)
        ->from(route('admin.reports'))
        ->get(route('admin.reports', ['report' => 'unknown']))
        ->assertRedirect(route('admin.reports'))
        ->assertSessionHasErrors('report');

    $this->actingAs($normalUser)
        ->get(route('admin.reports'))
        ->assertRedirect('/welcome');
    $this->get(route('admin.reports.export', ['report' => 'monthly_summary']))
        ->assertRedirect('/welcome');
});

function reportFixture(): array
{
    $admin = User::factory()->create(['usertype' => 'admin']);
    $teaching = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $nonTeaching = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();
    $vacation = LeaveType::query()->where('code', 'vacation')->firstOrFail();
    $teachingEmployee = reportEmployee('REPORT-T', $teaching->getKey());
    $nonTeachingEmployee = reportEmployee('REPORT-NT', $nonTeaching->getKey());

    return [$admin, $teachingEmployee, $nonTeachingEmployee, $vacation];
}

function reportEmployee(string $employeeNumber, int $personnelTypeId): User
{
    $user = User::factory()->create(['status' => 'active']);
    $user->employeeProfile()->create([
        'employee_number' => $employeeNumber,
        'personnel_type_id' => $personnelTypeId,
    ]);

    return $user->load('employeeProfile');
}
