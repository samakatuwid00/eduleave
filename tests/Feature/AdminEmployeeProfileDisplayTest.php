<?php

use App\Models\PersonnelType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin user tables display employee numbers from employee profiles', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $personnelType = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();

    collect([
        'pending' => 'EMP-PENDING',
        'active' => 'EMP-ACTIVE',
        'rejected' => 'EMP-REJECTED',
    ])->each(function (string $employeeNumber, string $status) use ($personnelType) {
        $user = User::factory()->create(['status' => $status]);
        $user->employeeProfile()->create([
            'employee_number' => $employeeNumber,
            'personnel_type_id' => $personnelType->getKey(),
            'position' => 'Teacher I',
            'date_employed' => '2020-01-15',
            'sex' => 'Female',
            'date_of_birth' => '1990-05-20',
            'place_of_birth' => 'Manila',
            'station' => 'Central School',
            'civil_status' => 'Single',
        ]);
    });

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('EMP-PENDING');

    $this->get('/admin/users/view-all_users')
        ->assertOk()
        ->assertSee('EMP-PENDING')
        ->assertSee('EMP-ACTIVE')
        ->assertSee('EMP-REJECTED');

    $this->get('/admin/users/view-pending_users')
        ->assertOk()
        ->assertSee('EMP-PENDING');

    $this->get('/admin/users/view-approved_users')
        ->assertOk()
        ->assertSee('EMP-ACTIVE');

    $this->get('/admin/users/view-rejected_users')
        ->assertOk()
        ->assertSee('EMP-REJECTED');

    $this->get('/admin/teacher_leave_cards')
        ->assertOk()
        ->assertSee('EMP-ACTIVE');
});

test('admin details modal receives normalized employee profile fields', function () {
    $personnelType = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $user = User::factory()->create(['status' => 'active']);

    $user->employeeProfile()->create([
        'employee_number' => 'EMP-1001',
        'personnel_type_id' => $personnelType->getKey(),
        'position' => 'Teacher II',
        'date_employed' => '2021-06-01',
        'sex' => 'Female',
        'date_of_birth' => '1991-08-12',
        'place_of_birth' => 'Quezon City',
        'station' => 'North Elementary School',
        'civil_status' => 'Married',
    ]);

    $this->getJson('/get-user-details?id='.$user->getKey())
        ->assertOk()
        ->assertJson([
            'name' => $user->name,
            'email' => $user->email,
            'employee_number' => 'EMP-1001',
            'personnel_type' => 'Teaching',
            'position' => 'Teacher II',
            'date_employed' => '2021-06-01',
            'sex' => 'Female',
            'date_of_birth' => '1991-08-12',
            'place_of_birth' => 'Quezon City',
            'station' => 'North Elementary School',
            'civil_status' => 'Married',
            'status' => 'active',
        ]);
});

test('admin can view a teaching leave card through the employee profile', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $personnelType = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $user = User::factory()->create(['status' => 'active']);
    $profile = $user->employeeProfile()->create([
        'employee_number' => 'EMP-TEACHING',
        'personnel_type_id' => $personnelType->getKey(),
    ]);
    $profile->teachingLeaveCards()->create([
        'inclusive_period' => 'June 2026',
        'nature_of_activity' => 'Summer training',
        'days_credited' => 5,
        'vacation_service_dso_number' => 'DSO-T-100',
        'inclusive_leave_dates' => '2026-07-01',
        'days_with_pay' => 1,
        'service_credit_balance' => 4,
        'days_without_pay' => 0,
        'nature_of_leave' => 'Vacation',
        'record_of_leave_dso_number' => 'ROL-T-100',
        'remarks' => 'Approved',
    ]);

    $this->actingAs($admin)
        ->get('/admin/leave_card/EMP-TEACHING')
        ->assertOk()
        ->assertSee('Teaching Leave Card')
        ->assertSee('EMP-TEACHING')
        ->assertSee('Summer training')
        ->assertSee('DSO-T-100');
});

test('admin can view a non teaching leave card through the employee profile', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $personnelType = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();
    $user = User::factory()->create(['status' => 'active']);
    $profile = $user->employeeProfile()->create([
        'employee_number' => 'EMP-NON-TEACHING',
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

    $this->actingAs($admin)
        ->get('/admin/leave_card/EMP-NON-TEACHING')
        ->assertOk()
        ->assertSee('Non-Teaching Leave Card')
        ->assertSee('EMP-NON-TEACHING')
        ->assertSee('Vacation leave')
        ->assertSee('Approved by SDS');
});
