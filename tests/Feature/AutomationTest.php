<?php

use App\Models\AutomationRun;
use App\Models\AutomationSetting;
use App\Models\PersonnelType;
use App\Models\User;
use App\Notifications\AdminAutomationDigest;
use App\Notifications\EmployeeLeaveCardChanged;
use App\Services\ActionCenterService;
use App\Services\AutomationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
});

test('daily digest is queued once per recipient and window', function () {
    User::factory()->count(2)->create(['usertype' => 'admin']);

    expect(Artisan::call('automation:run', [
        'rule' => AutomationService::DAILY_ADMIN_DIGEST,
        '--window' => '2026-07-08',
    ]))->toBe(0);
    expect(Artisan::call('automation:run', [
        'rule' => AutomationService::DAILY_ADMIN_DIGEST,
        '--window' => '2026-07-08',
    ]))->toBe(0);

    Notification::assertCount(2);
    Notification::assertSentOnDemand(AdminAutomationDigest::class);
    expect(AutomationRun::query()->count())->toBe(1);
    $run = AutomationRun::query()->firstOrFail();
    expect($run->status)->toBe('completed')
        ->and($run->audience_count)->toBe(2)
        ->and($run->attempt)->toBe(1);
});

test('a disabled rule records a skipped run without sending mail', function () {
    User::factory()->create(['usertype' => 'admin']);
    AutomationSetting::current()->update(['daily_digest_enabled' => false]);

    Artisan::call('automation:run', [
        'rule' => AutomationService::DAILY_ADMIN_DIGEST,
        '--window' => '2026-07-09',
    ]);

    Notification::assertNothingSent();
    expect(AutomationRun::query()->firstOrFail()->status)->toBe('skipped');
});

test('missing recipients creates a visible failed run that can be retried', function () {
    $automation = app(AutomationService::class);
    $run = $automation->run(AutomationService::DAILY_ADMIN_DIGEST, '2026-07-10');

    expect($run->status)->toBe('failed')
        ->and($run->error_summary)->toContain('No valid admin notification recipients');
    $alerts = app(ActionCenterService::class)->build([
        'category' => 'automation_failure',
        'severity' => null,
        'personnel_type' => null,
        'age_days' => null,
    ]);
    expect($alerts['counts']['total'])->toBe(1)
        ->and($alerts['alerts']->first()['rule'])->toBe('automation_run_failed');

    User::factory()->create(['usertype' => 'admin']);
    $run = $automation->retry($run);

    expect($run->status)->toBe('completed')
        ->and($run->attempt)->toBe(2);
    Notification::assertCount(1);
});

test('admins can update validated automation settings and normal users cannot', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);

    $this->actingAs($admin)
        ->put(route('admin.automation.update'), [
            'automation_enabled' => '1',
            'daily_digest_enabled' => '1',
            'employee_notifications_enabled' => '1',
            'recipient_emails' => "records@example.com\nadmin@example.com",
            'change_reason' => 'Enable the initial automation pilot.',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $settings = AutomationSetting::current();
    expect($settings->automation_enabled)->toBeTrue()
        ->and($settings->daily_digest_enabled)->toBeTrue()
        ->and($settings->weekly_summary_enabled)->toBeFalse()
        ->and($settings->employee_notifications_enabled)->toBeTrue()
        ->and($settings->recipient_emails)->toBe(['records@example.com', 'admin@example.com'])
        ->and($settings->updated_by)->toBe($admin->getKey())
        ->and($settings->version)->toBe(2);
    $this->assertDatabaseHas('audit_events', [
        'action' => 'automation.settings_updated',
        'reason' => 'Enable the initial automation pilot.',
    ]);

    $this->put(route('admin.automation.update'), [
        'recipient_emails' => 'not-an-email',
        'change_reason' => 'Test invalid recipient validation.',
    ])->assertSessionHasErrors('emails.0');

    $normalUser = User::factory()->create(['usertype' => 'user']);
    $this->actingAs($normalUser)
        ->get(route('admin.automation'))
        ->assertRedirect('welcome');
});

test('employee leave-card notification is opt-in and queued once per material summary', function () {
    $type = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $employee = User::factory()->create(['status' => 'active']);
    $profile = $employee->employeeProfile()->create([
        'employee_number' => 'AUTO-EMPLOYEE',
        'personnel_type_id' => $type->getKey(),
    ]);
    $automation = app(AutomationService::class);

    expect($automation->notifyEmployeeChange($profile->load('user'), 'A row changed.'))->toBeFalse();
    Notification::assertNothingSent();

    AutomationSetting::current()->update([
        'automation_enabled' => true,
        'employee_notifications_enabled' => true,
    ]);
    expect($automation->notifyEmployeeChange($profile, 'Three rows were imported.'))->toBeTrue();

    Notification::assertSentToTimes($employee, EmployeeLeaveCardChanged::class, 1);
    Notification::assertSentTo($employee, EmployeeLeaveCardChanged::class, function ($notification) {
        return $notification instanceof ShouldQueue
            && $notification->queue === 'mail'
            && $notification->tries === 3
            && $notification->summary === 'Three rows were imported.';
    });
});

test('automation page shows settings and run history with a datatable', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    AutomationRun::query()->create([
        'rule_code' => AutomationService::ACTION_CENTER_EVALUATION,
        'window_key' => '2026-07-08',
        'idempotency_key' => hash('sha256', 'automation-page-test'),
        'status' => 'completed',
        'started_at' => now(),
        'finished_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.automation'))
        ->assertOk()
        ->assertSee('Automation')
        ->assertSee('Asia/Manila')
        ->assertSee('automationRunsTable', false);
});
