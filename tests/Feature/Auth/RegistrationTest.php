<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    config()->set('services.turnstile.enabled', false);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'position' => 'Teacher I',
        'date_employed' => '2025-01-01',
        'sex' => 'Male',
        'date_of_birth' => '1990-01-01',
        'place_of_birth' => 'Naga City',
        'employee_number' => 'TEST-001',
        'personnel' => 'Teaching',
        'station' => 'Test School',
        'civil_status' => 'Single',
        'email' => 'test@example.com',
        'phone' => '09123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/user/dashboard/warning');
    $this->assertDatabaseHas('employee_profiles', [
        'employee_number' => 'TEST-001',
        'personnel_type_id' => 1,
    ]);
});
