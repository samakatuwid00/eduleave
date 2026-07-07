<?php

use App\Models\LeaveType;
use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-10 09:00:00');
});

afterEach(function () {
    CarbonImmutable::setTestNow();
});

test('action center evaluates core administrative alerts with stable priorities', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $teaching = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $nonTeaching = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();
    $vacation = LeaveType::query()->where('code', 'vacation')->firstOrFail();

    actionCenterUser('PENDING-1', 'pending', $teaching->getKey(), ['created_at' => now()->subDay()]);
    actionCenterUser('PENDING-3', 'pending', $teaching->getKey(), ['created_at' => now()->subDays(3)]);
    actionCenterUser('PENDING-7', 'pending', $teaching->getKey(), ['created_at' => now()->subDays(7)]);
    User::factory()->create(['status' => 'active', 'processed_at' => now()->subDay()]);

    actionCenterUser('MISSING-CARD', 'active', $teaching->getKey(), [
        'processed_at' => now()->subDays(8),
    ]);
    $lowBalance = actionCenterUser('LOW-BALANCE', 'active', $teaching->getKey());
    $badData = actionCenterUser('BAD-DATA', 'active', $nonTeaching->getKey());

    TeachingLeaveCard::query()->create([
        'employee_profile_id' => $lowBalance->getKey(),
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'leave_type_id' => $vacation->getKey(),
        'service_credit_balance' => 5,
        'parse_state' => 'parsed',
    ]);
    NonTeachingLeaveCard::query()->create([
        'employee_profile_id' => $badData->getKey(),
        'period' => 'unknown period',
        'parse_state' => 'unparseable',
        'parse_note' => 'Reporting period could not be parsed.',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.action-center'));

    $response->assertOk()
        ->assertSee('Action Center')
        ->assertSee('id="actionCenterTable"', false)
        ->assertSee('PENDING-1')
        ->assertSee('PENDING-3')
        ->assertSee('PENDING-7')
        ->assertSee('MISSING-CARD')
        ->assertSee('LOW-BALANCE')
        ->assertSee('BAD-DATA');

    expect($response->viewData('counts'))->toBe([
        'total' => 7,
        'critical' => 1,
        'high' => 3,
        'medium' => 3,
    ])->and($response->viewData('alerts')->pluck('fingerprint')->unique()->count())->toBe(7);
});

test('action center filters by severity category personnel and age', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $teaching = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $nonTeaching = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();

    actionCenterUser('CRITICAL-TEACHING', 'pending', $teaching->getKey(), ['created_at' => now()->subDays(8)]);
    actionCenterUser('MEDIUM-NON-TEACHING', 'pending', $nonTeaching->getKey(), ['created_at' => now()->subDay()]);

    $response = $this->actingAs($admin)->get(route('admin.action-center', [
        'category' => 'pending_approval',
        'severity' => 'critical',
        'personnel_type' => 'teaching',
        'age_days' => 7,
    ]));

    $response->assertOk()
        ->assertSee('CRITICAL-TEACHING')
        ->assertDontSee('MEDIUM-NON-TEACHING');

    expect($response->viewData('counts')['total'])->toBe(1);
});

test('resolving the source condition removes a live alert', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $teaching = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $user = actionCenterUser('RESOLVED-PENDING', 'pending', $teaching->getKey(), [
        'created_at' => now()->subDays(3),
    ]);

    $this->actingAs($admin)->get(route('admin.action-center'))->assertSee('RESOLVED-PENDING');

    $user->forceFill(['status' => 'active'])->save();

    $this->get(route('admin.action-center').'?category=pending_approval')
        ->assertOk()
        ->assertDontSee('RESOLVED-PENDING');
});

test('action center validates filters and rejects normal users', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);

    $this->actingAs($admin)
        ->from(route('admin.action-center'))
        ->get(route('admin.action-center').'?severity=urgent')
        ->assertRedirect(route('admin.action-center'))
        ->assertSessionHasErrors('severity');

    $this->actingAs(User::factory()->create())
        ->get(route('admin.action-center'))
        ->assertRedirect('/welcome');
});

function actionCenterUser(string $number, string $status, int $personnelTypeId, array $attributes = []): User
{
    $user = User::factory()->create(array_merge(['status' => $status], $attributes));
    $user->employeeProfile()->create([
        'employee_number' => $number,
        'personnel_type_id' => $personnelTypeId,
    ]);

    return $user;
}
