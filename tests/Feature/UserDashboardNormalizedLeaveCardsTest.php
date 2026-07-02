<?php

use App\Models\PersonnelType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('verified pending user warning loads the normalized employee profile without querying legacy cards', function () {
    $personnelType = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $user = User::factory()->create(['status' => 'pending']);
    $user->employeeProfile()->create([
        'employee_number' => 'EMP-PENDING-WARNING',
        'personnel_type_id' => $personnelType->getKey(),
    ]);

    $this->actingAs($user)
        ->get('/user/dashboard/warning')
        ->assertOk()
        ->assertSee('EMP-PENDING-WARNING');
});

test('active teaching user dashboard reads the teaching leave card table', function () {
    $personnelType = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $user = User::factory()->create(['status' => 'active']);
    $profile = $user->employeeProfile()->create([
        'employee_number' => 'EMP-USER-TEACHING',
        'personnel_type_id' => $personnelType->getKey(),
    ]);
    $profile->teachingLeaveCards()->create([
        'inclusive_period' => 'June 2026',
        'nature_of_activity' => 'Summer training',
        'days_credited' => 5,
        'vacation_service_dso_number' => 'USER-DSO-100',
        'inclusive_leave_dates' => '2026-07-01',
        'days_with_pay' => 1,
        'service_credit_balance' => 4,
        'days_without_pay' => 0,
        'nature_of_leave' => 'Vacation',
        'record_of_leave_dso_number' => 'USER-ROL-100',
        'remarks' => 'Approved',
    ]);

    $this->actingAs($user->refresh())
        ->get('/user/dashboard')
        ->assertOk()
        ->assertSee('EMP-USER-TEACHING')
        ->assertSee('Summer training')
        ->assertSee('USER-DSO-100');
});

test('active non teaching user dashboard reads the non teaching leave card table', function () {
    $personnelType = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();
    $user = User::factory()->create(['status' => 'active']);
    $profile = $user->employeeProfile()->create([
        'employee_number' => 'EMP-USER-NON-TEACHING',
        'personnel_type_id' => $personnelType->getKey(),
    ]);
    $profile->nonTeachingLeaveCards()->create([
        'period' => 'July 2026',
        'particulars' => 'Vacation leave',
        'vacation_leave_earned' => 1.25,
        'vacation_leave_with_pay' => '1 day',
        'vacation_leave_balance' => '10 days',
        'vacation_leave_without_pay' => 0,
        'sick_leave_earned' => 1.25,
        'sick_leave_with_pay' => 0,
        'sick_leave_balance' => '8 days',
        'sick_leave_without_pay' => 'None',
        'leave_application_action' => 'Approved by SDS',
    ]);

    $this->actingAs($user->refresh())
        ->get('/user/dashboard')
        ->assertOk()
        ->assertSee('EMP-USER-NON-TEACHING')
        ->assertSee('Vacation leave')
        ->assertSee('Approved by SDS');
});
