<?php

namespace App\Services;

use App\Models\EmployeeProfile;
use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AdminDashboardService
{
    public function build(array $filters): array
    {
        $users = $this->users($filters);
        $profiles = $this->profiles($filters);
        $profileIds = (clone $profiles)->pluck('user_id');

        return [
            'filters' => $filters,
            'kpis' => [
                'employees' => (clone $users)->count(),
                'pending' => (clone $users)
                    ->where('status', 'pending')
                    ->whereNotNull('email_verified_at')
                    ->count(),
                'missing_cards' => $this->missingCardCount($filters),
                'low_balances' => $this->lowBalanceCount($profileIds, $filters),
            ],
            'pipeline' => $this->pipeline($filters),
            'personnel' => $this->personnelComposition($filters),
            'activity' => $this->activity($filters),
            'leave_trend' => $this->leaveTrend($profileIds, $filters),
            'attention' => $this->attention($filters),
            'generated_at' => now()->timezone(config('app.timezone'))->format('M j, Y g:i A'),
        ];
    }

    private function users(array $filters): Builder
    {
        return User::query()
            ->where('usertype', '!=', 'admin')
            ->when($filters['user_status'], fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['personnel_type'], function (Builder $query, string $type) {
                $query->whereHas('employeeProfile.personnelType', fn (Builder $typeQuery) => $typeQuery->where('code', $type));
            });
    }

    private function profiles(array $filters): Builder
    {
        return EmployeeProfile::query()
            ->whereHas('user', function (Builder $query) use ($filters) {
                $query->where('usertype', '!=', 'admin')
                    ->when($filters['user_status'], fn (Builder $statusQuery, string $status) => $statusQuery->where('status', $status));
            })
            ->when($filters['personnel_type'], function (Builder $query, string $type) {
                $query->whereHas('personnelType', fn (Builder $typeQuery) => $typeQuery->where('code', $type));
            });
    }

    private function pipeline(array $filters): array
    {
        $users = $this->users($filters);

        return [
            'labels' => ['Unverified', 'Verified pending', 'Approved', 'Rejected'],
            'values' => [
                (clone $users)->whereNull('email_verified_at')->count(),
                (clone $users)->where('status', 'pending')->whereNotNull('email_verified_at')->count(),
                (clone $users)->where('status', 'active')->count(),
                (clone $users)->where('status', 'rejected')->count(),
            ],
        ];
    }

    private function personnelComposition(array $filters): array
    {
        $users = $this->users($filters);
        $total = (clone $users)->count();
        $teaching = (clone $users)->whereHas(
            'employeeProfile.personnelType',
            fn (Builder $query) => $query->where('code', PersonnelType::CODE_TEACHING),
        )->count();
        $nonTeaching = (clone $users)->whereHas(
            'employeeProfile.personnelType',
            fn (Builder $query) => $query->where('code', PersonnelType::CODE_NON_TEACHING),
        )->count();

        return [
            'labels' => ['Teaching', 'Non-Teaching', 'Missing classification'],
            'values' => [$teaching, $nonTeaching, max(0, $total - $teaching - $nonTeaching)],
        ];
    }

    private function missingCardCount(array $filters): int
    {
        $profiles = $this->profiles($filters);
        $teaching = 0;
        $nonTeaching = 0;

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_TEACHING) {
            $teaching = (clone $profiles)
                ->whereHas('personnelType', fn (Builder $query) => $query->where('code', PersonnelType::CODE_TEACHING))
                ->whereDoesntHave('teachingLeaveCards')
                ->count();
        }

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_NON_TEACHING) {
            $nonTeaching = (clone $profiles)
                ->whereHas('personnelType', fn (Builder $query) => $query->where('code', PersonnelType::CODE_NON_TEACHING))
                ->whereDoesntHave('nonTeachingLeaveCards')
                ->count();
        }

        return $teaching + $nonTeaching;
    }

    private function lowBalanceCount(Collection $profileIds, array $filters): int
    {
        $threshold = (float) config('analytics.low_balance_threshold', 5);
        $employeeIds = collect();

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_TEACHING) {
            $latestIds = TeachingLeaveCard::query()
                ->selectRaw('MAX(id)')
                ->whereIn('employee_profile_id', $profileIds)
                ->groupBy('employee_profile_id');
            $employeeIds = $employeeIds->merge(
                TeachingLeaveCard::query()
                    ->whereIn('id', $latestIds)
                    ->whereNotNull('service_credit_balance')
                    ->where('service_credit_balance', '<=', $threshold)
                    ->pluck('employee_profile_id'),
            );
        }

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_NON_TEACHING) {
            $latestIds = NonTeachingLeaveCard::query()
                ->selectRaw('MAX(id)')
                ->whereIn('employee_profile_id', $profileIds)
                ->groupBy('employee_profile_id');
            $employeeIds = $employeeIds->merge(
                NonTeachingLeaveCard::query()
                    ->whereIn('id', $latestIds)
                    ->where(function (Builder $query) use ($threshold) {
                        $query->where('vacation_leave_balance_value', '<=', $threshold)
                            ->orWhere('sick_leave_balance_value', '<=', $threshold);
                    })
                    ->pluck('employee_profile_id'),
            );
        }

        return $employeeIds->unique()->count();
    }

    private function activity(array $filters): array
    {
        $months = $this->months($filters);
        $created = $this->users($filters)
            ->whereBetween('created_at', [$filters['from'], $filters['to'].' 23:59:59'])
            ->get(['created_at']);
        $processed = $this->users($filters)
            ->whereNotNull('processed_at')
            ->whereBetween('processed_at', [$filters['from'], $filters['to'].' 23:59:59'])
            ->get(['status', 'processed_at']);

        return [
            'labels' => $months->pluck('label')->all(),
            'registrations' => $months->map(fn (array $month) => $created->filter(
                fn (User $user) => $user->created_at->format('Y-m') === $month['key'],
            )->count())->all(),
            'approvals' => $months->map(fn (array $month) => $processed->filter(
                fn (User $user) => $user->status === 'active' && $user->processed_at->format('Y-m') === $month['key'],
            )->count())->all(),
            'rejections' => $months->map(fn (array $month) => $processed->filter(
                fn (User $user) => $user->status === 'rejected' && $user->processed_at->format('Y-m') === $month['key'],
            )->count())->all(),
        ];
    }

    private function leaveTrend(Collection $profileIds, array $filters): array
    {
        $months = $this->months($filters);
        $totals = $months->mapWithKeys(fn (array $month) => [$month['key'] => [
            'vacation' => 0.0,
            'sick' => 0.0,
            'other' => 0.0,
            'unpaid' => 0.0,
        ]]);

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_TEACHING) {
            TeachingLeaveCard::query()
                ->with('leaveType:id,code')
                ->whereIn('employee_profile_id', $profileIds)
                ->whereBetween('period_start', [$filters['from'], $filters['to']])
                ->when($filters['leave_type'], fn (Builder $query, string $type) => $query->whereHas('leaveType', fn (Builder $leaveType) => $leaveType->where('code', $type)))
                ->get()
                ->each(function (TeachingLeaveCard $card) use (&$totals) {
                    $month = $card->period_start->format('Y-m');

                    if (! $totals->has($month)) {
                        return;
                    }

                    $category = in_array($card->leaveType?->code, ['vacation', 'sick'], true)
                        ? $card->leaveType->code
                        : 'other';
                    $values = $totals->get($month);
                    $values[$category] += (float) ($card->days_with_pay ?? 0);
                    $values['unpaid'] += (float) ($card->days_without_pay ?? 0);
                    $totals->put($month, $values);
                });
        }

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_NON_TEACHING) {
            NonTeachingLeaveCard::query()
                ->whereIn('employee_profile_id', $profileIds)
                ->whereBetween('period_start', [$filters['from'], $filters['to']])
                ->when($filters['leave_type'], fn (Builder $query, string $type) => $query->whereHas('leaveType', fn (Builder $leaveType) => $leaveType->where('code', $type)))
                ->get()
                ->each(function (NonTeachingLeaveCard $card) use (&$totals) {
                    $month = $card->period_start->format('Y-m');

                    if (! $totals->has($month)) {
                        return;
                    }

                    $values = $totals->get($month);
                    $values['vacation'] += (float) ($card->vacation_leave_with_pay_value ?? 0);
                    $values['sick'] += (float) ($card->sick_leave_with_pay ?? 0);
                    $values['unpaid'] += (float) ($card->vacation_leave_without_pay ?? 0)
                        + (float) ($card->sick_leave_without_pay_value ?? 0);
                    $totals->put($month, $values);
                });
        }

        $excluded = TeachingLeaveCard::query()->whereIn('employee_profile_id', $profileIds)->where('parse_state', 'unparseable')->count()
            + NonTeachingLeaveCard::query()->whereIn('employee_profile_id', $profileIds)->where('parse_state', 'unparseable')->count();

        return [
            'labels' => $months->pluck('label')->all(),
            'vacation' => $totals->pluck('vacation')->all(),
            'sick' => $totals->pluck('sick')->all(),
            'other' => $totals->pluck('other')->all(),
            'unpaid' => $totals->pluck('unpaid')->all(),
            'excluded' => $excluded,
        ];
    }

    private function attention(array $filters): Collection
    {
        $pending = $this->users($filters)
            ->with('employeeProfile')
            ->where('status', 'pending')
            ->whereNotNull('email_verified_at')
            ->oldest('created_at')
            ->limit(6)
            ->get()
            ->map(fn (User $user) => [
                'priority' => $user->created_at->lte(now()->subDays(7)) ? 'High' : 'Normal',
                'item' => 'Pending approval',
                'employee' => $user->employeeProfile?->employee_number ?? $user->name,
                'age' => $user->created_at->diffForHumans(short: true),
                'url' => url('/admin/users/view-pending_users'),
            ]);

        return $pending;
    }

    private function months(array $filters): Collection
    {
        $current = CarbonImmutable::parse($filters['from'])->startOfMonth();
        $end = CarbonImmutable::parse($filters['to'])->startOfMonth();
        $months = collect();

        while ($current->lte($end)) {
            $months->push(['key' => $current->format('Y-m'), 'label' => $current->format('M Y')]);
            $current = $current->addMonth();
        }

        return $months;
    }
}
