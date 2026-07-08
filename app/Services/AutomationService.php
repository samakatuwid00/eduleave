<?php

namespace App\Services;

use App\Models\AutomationRun;
use App\Models\AutomationSetting;
use App\Models\EmployeeProfile;
use App\Models\User;
use App\Notifications\AdminAutomationDigest;
use App\Notifications\EmployeeLeaveCardChanged;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class AutomationService
{
    public const ACTION_CENTER_EVALUATION = 'action_center_evaluation';

    public const DAILY_ADMIN_DIGEST = 'daily_admin_digest';

    public const WEEKLY_ADMIN_SUMMARY = 'weekly_admin_summary';

    public function __construct(
        private readonly ActionCenterService $actionCenter,
        private readonly AuditService $audit,
    ) {}

    public function run(string $rule, ?string $window = null): AutomationRun
    {
        $this->validateRule($rule);
        $settings = AutomationSetting::current();
        $window ??= $this->windowFor($rule);
        $recipients = $rule === self::ACTION_CENTER_EVALUATION ? [] : $this->recipients($settings);
        $audience = $recipients === [] ? 'system' : implode('|', $recipients);
        $key = hash('sha256', implode(':', [
            $rule,
            $window,
            hash('sha256', $audience),
            config('automation.payload_version', 1),
        ]));

        $run = AutomationRun::query()->firstOrCreate(
            ['idempotency_key' => $key],
            [
                'rule_code' => $rule,
                'window_key' => $window,
                'status' => 'running',
                'audience_count' => count($recipients),
                'started_at' => now(),
            ],
        );

        if (! $run->wasRecentlyCreated) {
            return $run;
        }

        $run = $this->execute($run, $settings, $recipients);
        $this->audit->record(
            'automation.run_completed',
            'AutomationRun',
            $run->getKey(),
            $run->rule_code,
            after: [
                'status' => $run->status,
                'window' => $run->window_key,
                'items' => $run->item_count,
                'recipients' => $run->audience_count,
            ],
            metadata: ['attempt' => $run->attempt],
        );

        return $run;
    }

    public function retry(AutomationRun $run): AutomationRun
    {
        if ($run->status !== 'failed') {
            throw new InvalidArgumentException('Only failed automation runs can be retried.');
        }

        $settings = AutomationSetting::current();
        $recipients = $run->rule_code === self::ACTION_CENTER_EVALUATION ? [] : $this->recipients($settings);
        $run->update([
            'status' => 'running',
            'attempt' => $run->attempt + 1,
            'audience_count' => count($recipients),
            'started_at' => now(),
            'finished_at' => null,
            'error_summary' => null,
        ]);

        return $this->execute($run, $settings, $recipients);
    }

    public function notifyEmployeeChange(EmployeeProfile $profile, string $summary): bool
    {
        $settings = AutomationSetting::current();
        $user = $profile->user;

        if (! $settings->automation_enabled || ! $settings->employee_notifications_enabled || ! $user?->email) {
            return false;
        }

        try {
            $user->notify(new EmployeeLeaveCardChanged($summary));
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        return true;
    }

    /** @return list<string> */
    public function recipients(AutomationSetting $settings): array
    {
        $configured = collect($settings->recipient_emails ?? [])
            ->map(fn ($email) => Str::lower(trim((string) $email)))
            ->filter(fn (string $email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique();

        if ($configured->isNotEmpty()) {
            return $configured->sort()->values()->all();
        }

        return User::query()
            ->where('usertype', 'admin')
            ->whereNotNull('email')
            ->pluck('email')
            ->map(fn ($email) => Str::lower(trim((string) $email)))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function execute(AutomationRun $run, AutomationSetting $settings, array $recipients): AutomationRun
    {
        if (! $this->isEnabled($run->rule_code, $settings)) {
            $run->update([
                'status' => 'skipped',
                'finished_at' => now(),
                'error_summary' => 'Rule disabled by an administrator.',
            ]);

            return $run->fresh();
        }

        try {
            $snapshot = $this->actionCenter->build($this->emptyFilters());
            $items = $snapshot['alerts']->take(5)->map(fn (array $alert) => [
                'title' => $alert['title'],
                'employee' => $alert['employee'],
            ])->values()->all();

            if ($run->rule_code !== self::ACTION_CENTER_EVALUATION) {
                if ($recipients === []) {
                    throw new RuntimeException('No valid admin notification recipients are configured.');
                }

                foreach ($recipients as $email) {
                    Notification::route('mail', $email)->notify(new AdminAutomationDigest(
                        $run->rule_code,
                        $run->window_key,
                        $snapshot['counts'],
                        $items,
                    ));
                }
            }

            $run->update([
                'status' => 'completed',
                'item_count' => $snapshot['counts']['total'],
                'metadata' => ['counts' => $snapshot['counts']],
                'finished_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
            $run->update([
                'status' => 'failed',
                'finished_at' => now(),
                'error_summary' => Str::limit($exception->getMessage(), 500, ''),
            ]);
        }

        return $run->fresh();
    }

    private function isEnabled(string $rule, AutomationSetting $settings): bool
    {
        if (! $settings->automation_enabled) {
            return false;
        }

        return match ($rule) {
            self::DAILY_ADMIN_DIGEST => $settings->daily_digest_enabled,
            self::WEEKLY_ADMIN_SUMMARY => $settings->weekly_summary_enabled,
            default => true,
        };
    }

    private function windowFor(string $rule): string
    {
        $now = now(config('automation.timezone', 'Asia/Manila'));

        return $rule === self::WEEKLY_ADMIN_SUMMARY
            ? $now->format('o-\WW')
            : $now->toDateString();
    }

    private function validateRule(string $rule): void
    {
        if (! in_array($rule, [
            self::ACTION_CENTER_EVALUATION,
            self::DAILY_ADMIN_DIGEST,
            self::WEEKLY_ADMIN_SUMMARY,
        ], true)) {
            throw new InvalidArgumentException("Unknown automation rule [{$rule}].");
        }
    }

    private function emptyFilters(): array
    {
        return [
            'category' => null,
            'severity' => null,
            'personnel_type' => null,
            'age_days' => null,
        ];
    }
}
