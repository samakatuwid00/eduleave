<?php

use App\Models\LeaveType;
use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;

test('leave analytics reconciles summary metrics with teaching and non teaching rows', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $teaching = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $nonTeaching = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();
    $vacation = LeaveType::query()->where('code', 'vacation')->firstOrFail();

    $teachingUser = analyticsEmployee('AN-T-LOW', $teaching->getKey());
    $negativeUser = analyticsEmployee('AN-NT-NEG', $nonTeaching->getKey());
    $unavailableUser = analyticsEmployee('AN-NT-NA', $nonTeaching->getKey());
    $badUser = analyticsEmployee('AN-T-BAD', $teaching->getKey());

    TeachingLeaveCard::query()->create([
        'employee_profile_id' => $teachingUser->getKey(),
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'nature_of_leave' => 'Vacation',
        'leave_type_id' => $vacation->getKey(),
        'days_with_pay' => 2,
        'days_without_pay' => 1,
        'service_credit_balance' => 5,
        'parse_state' => 'parsed',
    ]);
    NonTeachingLeaveCard::query()->create([
        'employee_profile_id' => $negativeUser->getKey(),
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'particulars' => 'Vacation',
        'leave_type_id' => $vacation->getKey(),
        'vacation_leave_with_pay_value' => 1,
        'sick_leave_with_pay' => 3,
        'vacation_leave_without_pay' => 2,
        'sick_leave_without_pay_value' => 0,
        'vacation_leave_balance_value' => 10,
        'sick_leave_balance_value' => -1,
        'parse_state' => 'parsed',
    ]);
    NonTeachingLeaveCard::query()->create([
        'employee_profile_id' => $unavailableUser->getKey(),
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'particulars' => 'Vacation',
        'leave_type_id' => $vacation->getKey(),
        'vacation_leave_with_pay_value' => 1,
        'parse_state' => 'parsed',
    ]);
    TeachingLeaveCard::query()->create([
        'employee_profile_id' => $badUser->getKey(),
        'inclusive_period' => 'unknown',
        'nature_of_leave' => 'Unknown',
        'parse_state' => 'unparseable',
        'parse_note' => 'Reporting period could not be parsed.',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.leave-analytics', [
        'from' => '2026-01-01',
        'to' => '2026-12-31',
    ]));

    $response->assertOk()
        ->assertSee('Leave Analytics')
        ->assertSee('id="analyticsMonthlyChart"', false)
        ->assertSee('id="analyticsBalanceChart"', false)
        ->assertSee('id="analyticsCategoryChart"', false)
        ->assertSee('id="leaveAnalyticsTable"', false)
        ->assertSee('AN-T-BAD');

    expect($response->viewData('kpis'))->toBe([
        'employees' => 3,
        'records' => 3,
        'paid' => 7.0,
        'unpaid' => 3.0,
        'low_balances' => 2,
        'excluded' => 1,
    ])->and($response->viewData('balances'))->toBe([
        'healthy' => 0,
        'low' => 1,
        'zero' => 0,
        'negative' => 1,
        'unavailable' => 1,
    ])->and(array_sum($response->viewData('monthly')['vacation']))->toBe(4.0)
        ->and(array_sum($response->viewData('monthly')['sick']))->toBe(3.0)
        ->and(array_sum($response->viewData('monthly')['unpaid']))->toBe(3.0);
});

test('leave analytics applies personnel and data-state filters', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $teaching = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $nonTeaching = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();
    $teachingUser = analyticsEmployee('FILTER-AN-T', $teaching->getKey());
    $nonTeachingUser = analyticsEmployee('FILTER-AN-NT', $nonTeaching->getKey());

    TeachingLeaveCard::query()->create([
        'employee_profile_id' => $teachingUser->getKey(),
        'period_start' => '2026-07-01',
        'days_with_pay' => 2,
        'parse_state' => 'parsed',
    ]);
    NonTeachingLeaveCard::query()->create([
        'employee_profile_id' => $nonTeachingUser->getKey(),
        'period_start' => '2026-07-01',
        'vacation_leave_with_pay_value' => 4,
        'parse_state' => 'parsed',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.leave-analytics', [
        'from' => '2026-01-01',
        'to' => '2026-12-31',
        'personnel_type' => 'teaching',
        'parse_state' => 'parsed',
    ]));

    $response->assertOk()
        ->assertSee('FILTER-AN-T')
        ->assertDontSee('FILTER-AN-NT');

    expect($response->viewData('kpis')['paid'])->toBe(2.0);
});

test('leave analytics validates filters and remains admin only', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);

    $this->actingAs($admin)
        ->from(route('admin.leave-analytics'))
        ->get(route('admin.leave-analytics').'?parse_state=invalid')
        ->assertRedirect(route('admin.leave-analytics'))
        ->assertSessionHasErrors('parse_state');

    $this->actingAs(User::factory()->create())
        ->get(route('admin.leave-analytics'))
        ->assertRedirect('/welcome');
});

function analyticsEmployee(string $employeeNumber, int $personnelTypeId): User
{
    $user = User::factory()->create(['status' => 'active']);
    $user->employeeProfile()->create([
        'employee_number' => $employeeNumber,
        'personnel_type_id' => $personnelTypeId,
    ]);

    return $user;
}
