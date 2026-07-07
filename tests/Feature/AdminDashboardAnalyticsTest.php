<?php

use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;

test('admin dashboard presents reconciled operational metrics and chart data', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $teaching = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $nonTeaching = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();

    dashboardEmployee('DASH-PENDING', 'pending', $teaching->getKey());
    User::factory()->unverified()->create(['status' => 'pending']);
    $teachingUser = dashboardEmployee('DASH-TEACHING', 'active', $teaching->getKey(), [
        'processed_at' => '2026-07-03 08:00:00',
    ]);
    $nonTeachingUser = dashboardEmployee('DASH-NON-TEACHING', 'active', $nonTeaching->getKey());
    User::factory()->create(['status' => 'rejected', 'processed_at' => '2026-07-04 08:00:00']);

    TeachingLeaveCard::query()->create([
        'employee_profile_id' => $teachingUser->getKey(),
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'days_with_pay' => 2,
        'days_without_pay' => 1,
        'service_credit_balance' => 5,
        'parse_state' => 'parsed',
    ]);
    NonTeachingLeaveCard::query()->create([
        'employee_profile_id' => $nonTeachingUser->getKey(),
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'vacation_leave_with_pay_value' => 1,
        'vacation_leave_balance_value' => 10,
        'sick_leave_with_pay' => 1,
        'sick_leave_balance_value' => 8,
        'parse_state' => 'parsed',
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard?from=2026-01-01&to=2026-12-31');

    $response->assertOk()
        ->assertSee('Operational overview')
        ->assertSee('id="activityChart"', false)
        ->assertSee('id="pipelineChart"', false)
        ->assertSee('id="leaveChart"', false)
        ->assertSee('id="personnelChart"', false)
        ->assertSee('DASH-PENDING');

    expect($response->viewData('kpis'))->toBe([
        'employees' => 5,
        'pending' => 1,
        'missing_cards' => 1,
        'low_balances' => 1,
    ])->and($response->viewData('personnel')['values'])->toBe([2, 1, 2])
        ->and(array_sum($response->viewData('activity')['approvals']))->toBe(1)
        ->and(array_sum($response->viewData('activity')['rejections']))->toBe(1)
        ->and(array_sum($response->viewData('leave_trend')['unpaid']))->toBe(1.0);
});

test('dashboard personnel filter is applied to snapshot metrics', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $teaching = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $nonTeaching = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();
    dashboardEmployee('FILTER-TEACHING', 'active', $teaching->getKey());
    dashboardEmployee('FILTER-NON-TEACHING', 'active', $nonTeaching->getKey());

    $response = $this->actingAs($admin)->get('/admin/dashboard?personnel_type=teaching');

    $response->assertOk();

    expect($response->viewData('kpis')['employees'])->toBe(1)
        ->and($response->viewData('personnel')['values'])->toBe([1, 0, 0]);
});

test('dashboard rejects unsupported filter values', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);

    $this->actingAs($admin)
        ->from('/admin/dashboard')
        ->get('/admin/dashboard?user_status=unknown')
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHasErrors('user_status');
});

test('dashboard remains protected from normal users', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/dashboard')
        ->assertRedirect('/welcome');
});

function dashboardEmployee(string $number, string $status, int $personnelTypeId, array $attributes = []): User
{
    $user = User::factory()->create(array_merge(['status' => $status], $attributes));
    $user->employeeProfile()->create([
        'employee_number' => $number,
        'personnel_type_id' => $personnelTypeId,
    ]);

    return $user;
}
