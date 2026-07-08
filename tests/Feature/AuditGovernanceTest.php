<?php

use App\Models\AuditEvent;
use App\Models\AutomationRun;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Shuchkin\SimpleXLSX;

uses(RefreshDatabase::class);

test('rejection requires a reason and records an attributable correlated event', function () {
    Mail::fake();
    $admin = User::factory()->create(['usertype' => 'admin']);
    $user = auditEmployee('AUDIT-REJECT');
    $user->forceFill(['status' => 'pending'])->save();

    $this->actingAs($admin)
        ->post(route('admin.users.reject', $user))
        ->assertSessionHasErrors('decision_reason');
    expect($user->fresh()->status)->toBe('pending');
    expect(AuditEvent::query()->count())->toBe(0);

    $this->withHeader('X-Request-ID', 'audit-request-1234')
        ->post(route('admin.users.reject', $user), [
            'decision_reason' => 'Required documents were incomplete.',
        ])->assertOk();

    $event = AuditEvent::query()->sole();
    expect($event->actor_user_id)->toBe($admin->getKey())
        ->and($event->action)->toBe('user.rejected')
        ->and($event->employee_number)->toBe('AUDIT-REJECT')
        ->and($event->reason)->toBe('Required documents were incomplete.')
        ->and($event->previous_values['status'])->toBe('pending')
        ->and($event->new_values['status'])->toBe('rejected')
        ->and($event->correlation_id)->toBe('audit-request-1234');
});

test('leave-card correction and deletion require reasons and capture changed values', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $employee = auditEmployee('AUDIT-CARD');
    $card = TeachingLeaveCard::query()->create([
        'employee_profile_id' => $employee->getKey(),
        'inclusive_period' => 'July 2026',
        'remarks' => 'Original',
    ]);

    $this->actingAs($admin)->put(route('admin.card-info.update', [
        'cardType' => PersonnelType::CODE_TEACHING,
        'id' => $card->getKey(),
    ]), ['remarks' => 'Changed'])->assertSessionHasErrors('change_reason');
    expect($card->fresh()->remarks)->toBe('Original');

    $this->put(route('admin.card-info.update', [
        'cardType' => PersonnelType::CODE_TEACHING,
        'id' => $card->getKey(),
    ]), [
        'remarks' => 'Changed',
        'change_reason' => 'Corrected a transcription error.',
    ])->assertOk();

    $updated = AuditEvent::query()->where('action', 'leave_card.updated')->sole();
    expect($updated->previous_values)->toHaveKey('remarks', 'Original')
        ->and($updated->new_values)->toHaveKey('remarks', 'Changed')
        ->and($updated->new_values)->not->toHaveKey('updated_at');

    $this->delete(route('admin.card-info.destroy', [
        'cardType' => PersonnelType::CODE_TEACHING,
        'id' => $card->getKey(),
    ]))->assertSessionHasErrors('audit_reason');
    $this->assertDatabaseHas('teaching_leave_cards', ['id' => $card->getKey()]);

    $this->delete(route('admin.card-info.destroy', [
        'cardType' => PersonnelType::CODE_TEACHING,
        'id' => $card->getKey(),
    ]), ['audit_reason' => 'Duplicate row confirmed against the source card.'])->assertOk();
    $this->assertDatabaseMissing('teaching_leave_cards', ['id' => $card->getKey()]);
    expect(AuditEvent::query()->where('action', 'leave_card.deleted')->sole()->reason)
        ->toBe('Duplicate row confirmed against the source card.');
});

test('audit payloads redact secrets and application model operations are append-only', function () {
    $event = app(AuditService::class)->record(
        'security.test',
        'user',
        10,
        after: [
            'name' => 'Safe Name',
            'password' => 'secret-password',
            'remember_token' => 'secret-token',
            'nested' => ['verification_token' => 'verify-me', 'safe' => 'visible'],
            'stored_path' => 'private/workbook.xlsx',
        ],
    );

    expect($event->new_values['name'])->toBe('Safe Name')
        ->and($event->new_values['password'])->toBe('[redacted]')
        ->and($event->new_values['remember_token'])->toBe('[redacted]')
        ->and($event->new_values['nested']['verification_token'])->toBe('[redacted]')
        ->and($event->new_values['nested']['safe'])->toBe('visible')
        ->and($event->new_values['stored_path'])->toBe('[redacted]');

    expect(fn () => $event->update(['reason' => 'tampered']))->toThrow(LogicException::class)
        ->and(fn () => $event->delete())->toThrow(LogicException::class);
});

test('a failed audit write rolls back the sensitive business mutation', function () {
    Mail::fake();
    $admin = User::factory()->create(['usertype' => 'admin']);
    $user = User::factory()->create(['status' => 'pending']);
    Schema::drop('audit_events');

    $this->actingAs($admin)
        ->post(route('admin.users.approve', $user), ['decision_reason' => 'Documents verified.'])
        ->assertServerError();

    expect($user->fresh()->status)->toBe('pending');
    Mail::assertNothingQueued();
});

test('admin roles enforce capabilities while legacy admins retain full access', function () {
    $legacyAdmin = User::factory()->create(['usertype' => 'admin', 'admin_role' => null]);
    $recordsAdmin = User::factory()->create(['usertype' => 'admin', 'admin_role' => User::ROLE_RECORDS_ADMIN]);
    $auditor = User::factory()->create(['usertype' => 'admin', 'admin_role' => User::ROLE_AUDITOR]);

    expect($legacyAdmin->hasAdminPermission('view_audit'))->toBeTrue()
        ->and($recordsAdmin->hasAdminPermission('manage_imports'))->toBeTrue()
        ->and($recordsAdmin->hasAdminPermission('view_audit'))->toBeFalse()
        ->and($auditor->hasAdminPermission('view_audit'))->toBeTrue()
        ->and($auditor->hasAdminPermission('manage_imports'))->toBeFalse();

    $this->actingAs($recordsAdmin)->get(route('admin.import-center'))->assertOk();
    $this->get(route('admin.audit'))->assertForbidden();
    $this->actingAs($auditor)->get(route('admin.audit'))->assertOk();
    $this->get(route('admin.import-center'))->assertForbidden();

    $this->actingAs($legacyAdmin)->put(route('admin.audit.role', $recordsAdmin), [
        'admin_role' => User::ROLE_AUDITOR,
        'change_reason' => 'Moved to independent review duties.',
    ])->assertSessionHas('success');
    expect($recordsAdmin->fresh()->admin_role)->toBe(User::ROLE_AUDITOR)
        ->and(AuditEvent::query()->where('action', 'admin.role_updated')->exists())->toBeTrue();
});

test('the final full administrator cannot be demoted', function () {
    $admin = User::factory()->create(['usertype' => 'admin', 'admin_role' => User::ROLE_SUPER_ADMIN]);

    $this->actingAs($admin)->put(route('admin.audit.role', $admin), [
        'admin_role' => User::ROLE_AUDITOR,
        'change_reason' => 'Attempted self-demotion.',
    ])->assertSessionHasErrors('admin_role');

    expect($admin->fresh()->admin_role)->toBe(User::ROLE_SUPER_ADMIN);
});

test('audit search and export use authorized filters', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    app(AuditService::class)->record(
        'leave_card.updated',
        'TeachingLeaveCard',
        1,
        employeeNumber: 'FILTER-AUDIT-1',
    );
    app(AuditService::class)->record(
        'report.exported',
        'report',
        'monthly_summary',
        employeeNumber: 'FILTER-AUDIT-2',
    );

    $response = $this->actingAs($admin)->get(route('admin.audit', [
        'employee_number' => 'FILTER-AUDIT-1',
    ]))->assertOk()->assertSee('leave_card.updated');
    expect($response->viewData('events')->total())->toBe(1)
        ->and($response->viewData('events')->first()->action)->toBe('leave_card.updated');

    $export = $this->get(route('admin.audit.export', [
        'employee_number' => 'FILTER-AUDIT-1',
    ]))->assertOk();
    $xlsx = SimpleXLSX::parseData($export->getContent());
    expect($xlsx->rows(1)[1][5])->toBe('FILTER-AUDIT-1')
        ->and(AuditEvent::query()->where('action', 'audit.exported')->exists())->toBeTrue();
});

test('retention dry run and execution preserve held audit events', function () {
    config()->set('governance.audit_retention_days', 30);
    config()->set('governance.automation_retention_days', 30);
    $old = now()->subDays(40);
    $deletable = AuditEvent::query()->create([
        'action' => 'old.event', 'target_type' => 'test', 'correlation_id' => 'old-delete-1234',
        'is_held' => false, 'created_at' => $old,
    ]);
    $held = AuditEvent::query()->create([
        'action' => 'held.event', 'target_type' => 'test', 'correlation_id' => 'old-hold-12345',
        'is_held' => true, 'created_at' => $old,
    ]);
    $run = AutomationRun::query()->create([
        'rule_code' => 'test', 'window_key' => 'old', 'idempotency_key' => hash('sha256', 'old-run'),
        'status' => 'completed',
    ]);
    DB::table('automation_runs')->where('id', $run->getKey())->update(['created_at' => $old, 'updated_at' => $old]);

    Artisan::call('governance:prune', ['--dry-run' => true]);
    expect($deletable->fresh())->not->toBeNull()->and($run->fresh())->not->toBeNull();

    Artisan::call('governance:prune');
    expect($deletable->fresh())->toBeNull()
        ->and($held->fresh())->not->toBeNull()
        ->and($run->fresh())->toBeNull()
        ->and(AuditEvent::query()->where('action', 'governance.retention_pruned')->exists())->toBeTrue();
});

function auditEmployee(string $employeeNumber): User
{
    $type = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $user = User::factory()->create(['status' => 'active']);
    $user->employeeProfile()->create([
        'employee_number' => $employeeNumber,
        'personnel_type_id' => $type->getKey(),
    ]);

    return $user->load('employeeProfile');
}
