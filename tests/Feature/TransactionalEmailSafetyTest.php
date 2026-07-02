<?php

use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;
use App\Models\User;
use App\Notifications\QueuedResetPassword;
use App\Notifications\QueuedVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

test('approval changes a pending user and queues only one email', function () {
    Mail::fake();

    $admin = User::factory()->create(['usertype' => 'admin']);
    $user = User::factory()->create(['status' => 'pending']);

    $this->actingAs($admin)
        ->post(route('admin.users.approve', $user))
        ->assertOk()
        ->assertJsonPath('notification_queued', true);

    $this->post(route('admin.users.approve', $user))
        ->assertOk()
        ->assertJsonPath('notification_queued', false);

    expect($user->fresh()->status)->toBe('active');
    Mail::assertQueued(UserApprovedMail::class, 1);
});

test('rejection changes a pending user and queues only one email', function () {
    Mail::fake();

    $admin = User::factory()->create(['usertype' => 'admin']);
    $user = User::factory()->create(['status' => 'pending']);

    $this->actingAs($admin)
        ->post(route('admin.users.reject', $user))
        ->assertOk()
        ->assertJsonPath('notification_queued', true);

    $this->post(route('admin.users.reject', $user))
        ->assertOk()
        ->assertJsonPath('notification_queued', false);

    expect($user->fresh()->status)->toBe('rejected');
    Mail::assertQueued(UserRejectedMail::class, 1);
});

test('standalone status email endpoints no longer exist', function () {
    $user = User::factory()->create();

    $this->post('/admin/users/send-approval-email/'.$user->id)->assertNotFound();
    $this->post('/admin/users/send-rejection-email/'.$user->id)->assertNotFound();
});

test('verification resend is limited to one request per minute', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect();

    $this->post(route('verification.send'))->assertTooManyRequests();

    Notification::assertSentToTimes($user, QueuedVerifyEmail::class, 1);
});

test('public email forms reject repeated requests', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email])->assertRedirect();
    $this->post(route('password.email'), ['email' => $user->email])->assertRedirect();
    $this->post(route('password.email'), ['email' => $user->email])->assertTooManyRequests();

    $registration = ['email' => 'limited-registration@example.com'];

    $this->post(route('register'), $registration)->assertRedirect();
    $this->post(route('register'), $registration)->assertRedirect();
    $this->post(route('register'), $registration)->assertTooManyRequests();
});

test('registration rejects an invalid turnstile token when enabled', function () {
    config()->set('services.turnstile.enabled', true);
    config()->set('services.turnstile.secret_key', 'test-secret');

    Http::fake([
        'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
            'success' => false,
        ]),
    ]);

    $this->from('/register')->post('/register', [
        'name' => 'Blocked Bot',
        'position' => 'Teacher I',
        'date_employed' => '2025-01-01',
        'sex' => 'Male',
        'date_of_birth' => '1990-01-01',
        'place_of_birth' => 'Naga City',
        'employee_number' => 'BOT-001',
        'personnel' => 'Teaching',
        'station' => 'Test School',
        'civil_status' => 'Single',
        'email' => 'blocked-bot@example.com',
        'phone' => '09123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
        'cf-turnstile-response' => 'invalid-token',
    ])->assertRedirect('/register')
        ->assertSessionHasErrors('cf-turnstile-response');

    $this->assertDatabaseMissing('users', ['email' => 'blocked-bot@example.com']);

    Http::assertSent(fn ($request) => $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
        && $request['secret'] === 'test-secret'
        && $request['response'] === 'invalid-token');
});

test('transactional notifications use the mail queue with limited retries', function () {
    $user = User::factory()->create();
    $mail = [
        new QueuedVerifyEmail,
        new QueuedResetPassword('test-token'),
        new UserApprovedMail($user),
        new UserRejectedMail($user),
    ];

    foreach ($mail as $message) {
        expect($message)
            ->toBeInstanceOf(ShouldQueue::class)
            ->and($message->queue)->toBe('mail')
            ->and($message->tries)->toBe(3)
            ->and($message->backoff)->toBe([60, 300]);
    }
});
