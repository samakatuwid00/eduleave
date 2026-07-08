<?php

namespace App\Services;

use App\Models\AutomationRun;
use App\Models\EmployeeProfile;
use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActionCenterService
{
    public function build(array $filters): array
    {
        $alerts = collect()
            ->concat($this->pendingApprovals($filters))
            ->concat($this->missingProfiles())
            ->concat($this->missingCards($filters))
            ->concat($this->lowBalances($filters))
            ->concat($this->dataQuality($filters))
            ->concat($this->automationFailures())
            ->when($filters['category'], fn (Collection $items, string $category) => $items->where('category', $category))
            ->when($filters['severity'], fn (Collection $items, string $severity) => $items->where('severity', $severity))
            ->when($filters['personnel_type'], fn (Collection $items, string $type) => $items->where('personnel_type', $type))
            ->when($filters['age_days'], fn (Collection $items, int $days) => $items->where('age_days', '>=', $days))
            ->sortByDesc(fn (array $alert) => [$this->severityWeight($alert['severity']), $alert['age_days']])
            ->values();

        return [
            'alerts' => $alerts,
            'counts' => [
                'total' => $alerts->count(),
                'critical' => $alerts->where('severity', 'critical')->count(),
                'high' => $alerts->where('severity', 'high')->count(),
                'medium' => $alerts->where('severity', 'medium')->count(),
            ],
        ];
    }

    private function pendingApprovals(array $filters): Collection
    {
        return User::query()
            ->with('employeeProfile.personnelType')
            ->where('usertype', '!=', 'admin')
            ->where('status', 'pending')
            ->whereNotNull('email_verified_at')
            ->when($filters['personnel_type'], function (Builder $query, string $type) {
                $query->whereHas('employeeProfile.personnelType', fn (Builder $typeQuery) => $typeQuery->where('code', $type));
            })
            ->get()
            ->map(function (User $user) {
                $age = max(0, (int) floor($user->created_at->diffInDays(now())));

                if ($age < 1) {
                    return null;
                }

                $severity = $age >= config('analytics.action_center.pending_critical_days', 7)
                    ? 'critical'
                    : ($age >= config('analytics.action_center.pending_high_days', 3) ? 'high' : 'medium');

                return $this->alert(
                    'pending_approval',
                    'pending_approval',
                    $severity,
                    'Verified registration awaiting approval',
                    $user->employeeProfile?->employee_number ?? $user->name,
                    $user->employeeProfile?->personnelType?->code,
                    $age,
                    "Verified {$age} day(s) ago and still pending.",
                    url('/admin/users/view-pending_users'),
                    'user',
                    $user->getKey(),
                );
            })
            ->filter();
    }

    private function missingProfiles(): Collection
    {
        return User::query()
            ->where('usertype', '!=', 'admin')
            ->where('status', 'active')
            ->whereDoesntHave('employeeProfile')
            ->get()
            ->map(function (User $user) {
                $age = $this->ageFrom($user->processed_at ?? $user->updated_at);

                return $this->alert(
                    'missing_profile',
                    'missing_profile',
                    'high',
                    'Approved employee has no profile',
                    $user->name,
                    null,
                    $age,
                    'The account is active but has no employee profile or personnel classification.',
                    url('/admin/users/view-approved_users'),
                    'user',
                    $user->getKey(),
                );
            });
    }

    private function missingCards(array $filters): Collection
    {
        $graceDays = (int) config('analytics.action_center.missing_card_grace_days', 7);

        return EmployeeProfile::query()
            ->with(['user', 'personnelType'])
            ->withCount(['teachingLeaveCards', 'nonTeachingLeaveCards'])
            ->whereHas('user', fn (Builder $query) => $query->where('status', 'active')->where('usertype', '!=', 'admin'))
            ->when($filters['personnel_type'], function (Builder $query, string $type) {
                $query->whereHas('personnelType', fn (Builder $typeQuery) => $typeQuery->where('code', $type));
            })
            ->get()
            ->filter(function (EmployeeProfile $profile) use ($graceDays) {
                $count = $profile->personnelType?->code === PersonnelType::CODE_TEACHING
                    ? $profile->teaching_leave_cards_count
                    : $profile->non_teaching_leave_cards_count;

                return $count === 0 && $this->ageFrom($profile->user->processed_at ?? $profile->user->updated_at) >= $graceDays;
            })
            ->map(function (EmployeeProfile $profile) {
                $age = $this->ageFrom($profile->user->processed_at ?? $profile->user->updated_at);

                return $this->alert(
                    'missing_leave_card',
                    'missing_card',
                    'medium',
                    'Approved employee has no leave card',
                    $profile->employee_number,
                    $profile->personnelType?->code,
                    $age,
                    'No leave-card rows exist for this employee personnel type.',
                    route('leave_card.show', $profile->employee_number),
                    'employee_profile',
                    $profile->getKey(),
                );
            });
    }

    private function lowBalances(array $filters): Collection
    {
        $threshold = (float) config('analytics.low_balance_threshold', 5);
        $alerts = collect();

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_TEACHING) {
            $latest = TeachingLeaveCard::query()
                ->selectRaw('MAX(id)')
                ->groupBy('employee_profile_id');

            $cards = TeachingLeaveCard::query()
                ->with('employeeProfile.personnelType')
                ->whereIn('id', $latest)
                ->whereNotNull('service_credit_balance')
                ->where('service_credit_balance', '<=', $threshold)
                ->get();

            $alerts = $alerts->concat($cards->map(fn (TeachingLeaveCard $card) => $this->balanceAlert(
                $card->employeeProfile,
                (float) $card->service_credit_balance,
                'Service-credit balance',
                $card->getKey(),
            )));
        }

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_NON_TEACHING) {
            $latest = NonTeachingLeaveCard::query()
                ->selectRaw('MAX(id)')
                ->groupBy('employee_profile_id');

            $cards = NonTeachingLeaveCard::query()
                ->with('employeeProfile.personnelType')
                ->whereIn('id', $latest)
                ->where(function (Builder $query) use ($threshold) {
                    $query->where('vacation_leave_balance_value', '<=', $threshold)
                        ->orWhere('sick_leave_balance_value', '<=', $threshold);
                })
                ->get();

            $alerts = $alerts->concat($cards->map(function (NonTeachingLeaveCard $card) {
                $balances = collect([
                    'Vacation balance' => $card->vacation_leave_balance_value,
                    'Sick balance' => $card->sick_leave_balance_value,
                ])->filter(fn ($value) => $value !== null);
                $value = (float) $balances->min();
                $label = (string) $balances->search($balances->min(), true);

                return $this->balanceAlert($card->employeeProfile, $value, $label, $card->getKey());
            }));
        }

        return $alerts;
    }

    private function dataQuality(array $filters): Collection
    {
        $alerts = collect();

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_TEACHING) {
            $cards = TeachingLeaveCard::query()
                ->with('employeeProfile.personnelType')
                ->where(function (Builder $query) {
                    $query->whereIn('parse_state', ['partial', 'unparseable'])
                        ->orWhereNull('period_start');
                })
                ->get();
            $alerts = $alerts->concat($cards->map(fn (TeachingLeaveCard $card) => $this->qualityAlert($card)));
        }

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_NON_TEACHING) {
            $cards = NonTeachingLeaveCard::query()
                ->with('employeeProfile.personnelType')
                ->where(function (Builder $query) {
                    $query->whereIn('parse_state', ['partial', 'unparseable'])
                        ->orWhereNull('period_start');
                })
                ->get();
            $alerts = $alerts->concat($cards->map(fn (NonTeachingLeaveCard $card) => $this->qualityAlert($card)));
        }

        return $alerts;
    }

    private function automationFailures(): Collection
    {
        $runs = AutomationRun::query()
            ->where('status', 'failed')
            ->latest()
            ->limit(25)
            ->get()
            ->map(fn (AutomationRun $run) => $this->alert(
                'automation_run_failed',
                'automation_failure',
                'high',
                'Automation run needs attention',
                str_replace('_', ' ', $run->rule_code),
                null,
                $this->ageFrom($run->updated_at),
                'The run failed. Review its safe error summary and retry after correcting the configuration.',
                route('admin.automation'),
                'automation_run',
                $run->getKey(),
            ));

        $failedJob = DB::table('failed_jobs')->latest('failed_at')->first();

        if ($failedJob) {
            $runs->push($this->alert(
                'queue_job_failed',
                'automation_failure',
                'high',
                'A queued job exhausted its retries',
                'Mail queue',
                null,
                max(0, (int) CarbonImmutable::parse($failedJob->failed_at)->diffInDays(now())),
                'A queued job failed permanently. Review it from the Automation page.',
                route('admin.automation'),
                'failed_job',
                (int) $failedJob->id,
            ));
        }

        return $runs;
    }

    private function balanceAlert(EmployeeProfile $profile, float $value, string $label, int $cardId): array
    {
        return $this->alert(
            'low_balance',
            'low_balance',
            $value <= 0 ? 'high' : 'medium',
            'Employee leave balance needs review',
            $profile->employee_number,
            $profile->personnelType?->code,
            0,
            "{$label}: ".number_format($value, 2),
            route('leave_card.show', $profile->employee_number),
            'leave_card',
            $cardId,
        );
    }

    private function qualityAlert(TeachingLeaveCard|NonTeachingLeaveCard $card): array
    {
        $profile = $card->employeeProfile;

        return $this->alert(
            'leave_card_data_quality',
            'data_quality',
            $card->parse_state === 'unparseable' ? 'high' : 'medium',
            'Leave-card row cannot be fully analyzed',
            $profile->employee_number,
            $profile->personnelType?->code,
            $this->ageFrom($card->updated_at),
            $card->parse_note ?: 'The reporting period or canonical values are incomplete.',
            route('leave_card.show', $profile->employee_number),
            'leave_card',
            $card->getKey(),
        );
    }

    private function alert(
        string $rule,
        string $category,
        string $severity,
        string $title,
        string $employee,
        ?string $personnelType,
        int $ageDays,
        string $evidence,
        string $url,
        string $subjectType,
        int $subjectId,
    ): array {
        return [
            'rule' => $rule,
            'category' => $category,
            'severity' => $severity,
            'title' => $title,
            'employee' => $employee,
            'personnel_type' => $personnelType,
            'age_days' => $ageDays,
            'evidence' => $evidence,
            'url' => $url,
            'fingerprint' => hash('sha256', "{$rule}:{$subjectType}:{$subjectId}"),
        ];
    }

    private function ageFrom($date): int
    {
        return max(0, (int) floor($date->diffInDays(now())));
    }

    private function severityWeight(string $severity): int
    {
        return match ($severity) {
            'critical' => 3,
            'high' => 2,
            default => 1,
        };
    }
}
