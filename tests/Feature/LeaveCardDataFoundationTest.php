<?php

use App\Data\LeaveCardAnalyticsRow;
use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

test('non teaching import dual writes canonical analytics values', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $user = User::factory()->create(['status' => 'active']);
    $type = PersonnelType::query()->where('code', PersonnelType::CODE_NON_TEACHING)->firstOrFail();
    $user->employeeProfile()->create([
        'employee_number' => 'EMP-FOUNDATION-1',
        'personnel_type_id' => $type->getKey(),
    ]);
    $file = leaveCardWorkbook([
        nonTeachingExcelHeaders(),
        ['July 2026', 'Vacation', 1.25, '1 day', '10 days', 0, 1.25, 0, '8 days', 'None', 'Approved by SDS'],
    ]);

    $this->actingAs($admin)->post(route('admin.leave-card.import', [
        PersonnelType::CODE_NON_TEACHING,
        'EMP-FOUNDATION-1',
    ]), ['excel_file' => $file])->assertSessionHas('success');

    $card = NonTeachingLeaveCard::query()->with('leaveType')->firstOrFail();

    expect($card->period_start->toDateString())->toBe('2026-07-01')
        ->and($card->period_end->toDateString())->toBe('2026-07-31')
        ->and($card->vacation_leave_with_pay_value)->toBe('1.00')
        ->and($card->vacation_leave_balance_value)->toBe('10.00')
        ->and($card->sick_leave_balance_value)->toBe('8.00')
        ->and($card->sick_leave_without_pay_value)->toBe('0.00')
        ->and($card->application_action_code)->toBe('approved')
        ->and($card->leaveType?->code)->toBe('vacation')
        ->and($card->parse_state)->toBe('parsed');
});

test('normalization dry run reports without changing a record', function () {
    $card = teachingCardForFoundation(['inclusive_period' => 'July 2026']);

    Artisan::call('leave-cards:normalize', ['--dry-run' => true]);

    expect($card->fresh()->period_start)->toBeNull()
        ->and(Artisan::output())->toContain('Dry run complete');
});

test('normalization command backfills canonical fields and common rows expose null for unavailable measures', function () {
    $card = teachingCardForFoundation([
        'inclusive_period' => 'July 2026',
        'nature_of_leave' => 'Sick',
        'days_without_pay' => 2,
    ]);

    Artisan::call('leave-cards:normalize');
    $card = $card->fresh()->load('leaveType');
    $row = LeaveCardAnalyticsRow::fromTeaching($card);

    expect($card->period_start->toDateString())->toBe('2026-07-01')
        ->and($card->leaveType?->code)->toBe('sick')
        ->and($row->vacationBalance)->toBeNull()
        ->and($row->totalUnpaid)->toBe(2.0)
        ->and($row->parseState)->toBe('parsed');
});

test('a partial teaching edit preserves canonical period values', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $card = teachingCardForFoundation([
        'inclusive_period' => 'July 2026',
        'remarks' => 'Original',
    ]);

    Artisan::call('leave-cards:normalize');

    $this->actingAs($admin)->put(route('admin.card-info.update', [
        'cardType' => PersonnelType::CODE_TEACHING,
        'id' => $card->getKey(),
    ]), ['remarks' => 'Updated', 'change_reason' => 'Corrected the recorded remarks.'])->assertOk();

    $card->refresh();

    expect($card->period_start->toDateString())->toBe('2026-07-01')
        ->and($card->remarks)->toBe('Updated');
});

test('approval records who processed the decision without changing the existing response', function () {
    $admin = User::factory()->create(['usertype' => 'admin']);
    $user = User::factory()->create(['status' => 'pending']);

    $this->actingAs($admin)
        ->post(route('admin.users.approve', $user), ['decision_reason' => 'Documents verified.'])
        ->assertOk()
        ->assertJsonPath('status', 'active');

    $user->refresh();

    expect($user->processed_by)->toBe($admin->getKey())
        ->and($user->processed_at)->not->toBeNull()
        ->and($user->decision_reason)->toBe('Documents verified.');
});

function teachingCardForFoundation(array $attributes = []): TeachingLeaveCard
{
    $type = PersonnelType::query()->where('code', PersonnelType::CODE_TEACHING)->firstOrFail();
    $user = User::factory()->create(['status' => 'active']);
    $profile = $user->employeeProfile()->create([
        'employee_number' => 'EMP-'.fake()->unique()->numerify('#####'),
        'personnel_type_id' => $type->getKey(),
    ]);

    return $profile->teachingLeaveCards()->create($attributes);
}
