<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(200);
});

test('email can be verified', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('user/dashboard', absolute: false).'?verified=1');
});

test('signed verification link is resumed after login and pending user is redirected correctly', function () {
    $user = User::factory()->unverified()->create(['status' => 'pending']);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $this->get($verificationUrl)->assertRedirect(route('login'));

    $loginResponse = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $loginResponse->assertRedirect($verificationUrl);

    $this->get($verificationUrl)
        ->assertRedirect(route('/user/dashboard/warning', absolute: false).'?verified=1');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('verified pending users never see the verification prompt or resend notification', function () {
    Notification::fake();

    $user = User::factory()->create(['status' => 'pending']);

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertRedirect(route('/user/dashboard/warning', absolute: false));

    $this->post(route('verification.send'))
        ->assertRedirect(route('/user/dashboard/warning', absolute: false));

    Notification::assertNothingSent();
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
