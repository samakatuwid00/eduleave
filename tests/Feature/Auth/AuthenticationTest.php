<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200)
        ->assertSee('placeholder="example@gmail.com"', false)
        ->assertSee('placeholder="Example: SecurePass1!"', false);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create(['status' => 'active']);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('user/dashboard', absolute: false));
});

test('unverified users are redirected to the verification notice after login', function () {
    $user = User::factory()->unverified()->create(['status' => 'pending']);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('verification.notice', absolute: false));
});

test('verified pending users are redirected to their warning dashboard after login', function () {
    $user = User::factory()->create(['status' => 'pending']);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('/user/dashboard/warning', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/welcome');
});
