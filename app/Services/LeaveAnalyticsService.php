<?php

namespace App\Services;

use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LeaveAnalyticsService
{
    public function build(array $filters): array
    {
        $rows = collect();

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_TEACHING) {
            $rows = $rows->concat($this->teachingRows($filters));
        }

        if (! $filters['personnel_type'] || $filters['personnel_type'] === PersonnelType::CODE_NON_TEACHING) {
            $rows = $rows->concat($this->nonTeachingRows($filters));
        }

        $rows = $rows->sortByDesc(fn (array $row) => [$row['period_start'] ?? '', $row['id']])->values();
        $included = $rows->where('included', true)->values();
        $balanceDistribution = $this->balanceDistribution($included);

        return [
            'rows' => $rows,
            'kpis' => [
                'employees' => $included->pluck('employee_profile_id')->unique()->count(),
                'records' => $included->count(),
                'paid' => round((float) $included->sum(fn (array $row) => $row['paid'] ?? 0), 2),
                'unpaid' => round((float) $included->sum(fn (array $row) => $row['unpaid'] ?? 0), 2),
                'low_balances' => $balanceDistribution['low'] + $balanceDistribution['zero'] + $balanceDistribution['negative'],
                'excluded' => $rows->where('included', false)->count(),
            ],
            'monthly' => $this->monthlyUsage($included, $filters),
            'categories' => $this->categoryUsage($included),
            'balances' => $balanceDistribution,
        ];
    }

    private function teachingRows(array $filters): Collection
    {
        return $this->applyFilters(
            TeachingLeaveCard::query()->with(['employeeProfile.user', 'employeeProfile.personnelType', 'leaveType']),
            $filters,
        )->get()->map(function (TeachingLeaveCard $card) {
            $category = $card->leaveType?->code ?? 'unclassified';
            $paid = $card->days_with_pay === null ? null : (float) $card->days_with_pay;

            return $this->row(
                $card,
                PersonnelType::CODE_TEACHING,
                $card->nature_of_leave,
                $paid,
                $card->days_without_pay === null ? null : (float) $card->days_without_pay,
                $card->service_credit_balance === null ? null : (float) $card->service_credit_balance,
                [
                    'vacation' => $category === 'vacation' ? (float) ($paid ?? 0) : 0.0,
                    'sick' => $category === 'sick' ? (float) ($paid ?? 0) : 0.0,
                    'other' => $category === 'other' ? (float) ($paid ?? 0) : 0.0,
                    'unclassified' => ! in_array($category, ['vacation', 'sick', 'other'], true) ? (float) ($paid ?? 0) : 0.0,
                ],
            );
        });
    }

    private function nonTeachingRows(array $filters): Collection
    {
        return $this->applyFilters(
            NonTeachingLeaveCard::query()->with(['employeeProfile.user', 'employeeProfile.personnelType', 'leaveType']),
            $filters,
        )->get()->map(function (NonTeachingLeaveCard $card) {
            $paidValues = collect([$card->vacation_leave_with_pay_value, $card->sick_leave_with_pay])
                ->filter(fn ($value) => $value !== null);
            $unpaidValues = collect([$card->vacation_leave_without_pay, $card->sick_leave_without_pay_value])
                ->filter(fn ($value) => $value !== null);
            $balances = collect([$card->vacation_leave_balance_value, $card->sick_leave_balance_value])
                ->filter(fn ($value) => $value !== null)
                ->map(fn ($value) => (float) $value);

            return $this->row(
                $card,
                PersonnelType::CODE_NON_TEACHING,
                $card->particulars,
                $paidValues->isEmpty() ? null : (float) $paidValues->sum(),
                $unpaidValues->isEmpty() ? null : (float) $unpaidValues->sum(),
                $balances->isEmpty() ? null : (float) $balances->min(),
                [
                    'vacation' => (float) ($card->vacation_leave_with_pay_value ?? 0),
                    'sick' => (float) ($card->sick_leave_with_pay ?? 0),
                    'other' => 0.0,
                    'unclassified' => 0.0,
                ],
                $balances->isEmpty()
                    ? 'Unavailable'
                    : 'Vacation '.($card->vacation_leave_balance_value ?? '—').' / Sick '.($card->sick_leave_balance_value ?? '—'),
            );
        });
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        $query->whereHas('employeeProfile.user', fn (Builder $user) => $user->where('usertype', '!=', 'admin'));

        if ($filters['parse_state']) {
            $query->where('parse_state', $filters['parse_state']);
        }

        if ($filters['leave_type'] === 'unclassified') {
            $query->whereNull('leave_type_id');
        } elseif ($filters['leave_type']) {
            $query->whereHas('leaveType', fn (Builder $type) => $type->where('code', $filters['leave_type']));
        }

        return $query->where(function (Builder $dates) use ($filters) {
            $dates->whereBetween('period_start', [$filters['from'], $filters['to']]);

            if (! $filters['parse_state'] || $filters['parse_state'] !== 'parsed') {
                $dates->orWhereNull('period_start');
            }
        });
    }

    private function row(
        TeachingLeaveCard|NonTeachingLeaveCard $card,
        string $personnelType,
        ?string $rawType,
        ?float $paid,
        ?float $unpaid,
        ?float $balance,
        array $usage,
        ?string $balanceDisplay = null,
    ): array {
        $profile = $card->employeeProfile;
        $included = $card->period_start !== null && ! in_array($card->parse_state, ['unparseable', 'not_applicable'], true);

        return [
            'id' => $card->getKey(),
            'employee_profile_id' => $card->employee_profile_id,
            'employee_number' => $profile->employee_number,
            'employee_name' => $profile->user?->name ?? 'Unknown',
            'personnel_type' => $personnelType,
            'period_start' => $card->period_start?->toDateString(),
            'period_end' => $card->period_end?->toDateString(),
            'leave_type' => $card->leaveType?->name ?? $rawType ?? 'Unclassified',
            'leave_type_code' => $card->leaveType?->code ?? 'unclassified',
            'paid' => $paid,
            'unpaid' => $unpaid,
            'balance' => $balance,
            'balance_display' => $balanceDisplay ?? ($balance === null ? 'Unavailable' : number_format($balance, 2)),
            'parse_state' => $card->parse_state,
            'parse_note' => $card->parse_note,
            'included' => $included,
            'usage' => $usage,
            'url' => route('leave_card.show', $profile->employee_number),
        ];
    }

    private function monthlyUsage(Collection $rows, array $filters): array
    {
        $months = collect();
        $current = CarbonImmutable::parse($filters['from'])->startOfMonth();
        $end = CarbonImmutable::parse($filters['to'])->startOfMonth();

        while ($current->lte($end)) {
            $months->put($current->format('Y-m'), [
                'label' => $current->format('M Y'),
                'vacation' => 0.0,
                'sick' => 0.0,
                'other' => 0.0,
                'unclassified' => 0.0,
                'unpaid' => 0.0,
            ]);
            $current = $current->addMonth();
        }

        foreach ($rows as $row) {
            $key = substr($row['period_start'], 0, 7);

            if (! $months->has($key)) {
                continue;
            }

            $values = $months->get($key);
            foreach (['vacation', 'sick', 'other', 'unclassified'] as $category) {
                $values[$category] += $row['usage'][$category];
            }
            $values['unpaid'] += (float) ($row['unpaid'] ?? 0);
            $months->put($key, $values);
        }

        return [
            'labels' => $months->pluck('label')->all(),
            'vacation' => $months->pluck('vacation')->all(),
            'sick' => $months->pluck('sick')->all(),
            'other' => $months->pluck('other')->all(),
            'unclassified' => $months->pluck('unclassified')->all(),
            'unpaid' => $months->pluck('unpaid')->all(),
        ];
    }

    private function categoryUsage(Collection $rows): array
    {
        $totals = collect([
            'Vacation' => (float) $rows->sum(fn (array $row) => $row['usage']['vacation']),
            'Sick' => (float) $rows->sum(fn (array $row) => $row['usage']['sick']),
            'Other' => (float) $rows->sum(fn (array $row) => $row['usage']['other']),
            'Unclassified' => (float) $rows->sum(fn (array $row) => $row['usage']['unclassified']),
        ])->sortDesc();

        return ['labels' => $totals->keys()->all(), 'values' => $totals->values()->all()];
    }

    private function balanceDistribution(Collection $rows): array
    {
        $latest = $rows
            ->sortByDesc(fn (array $row) => [$row['period_start'], $row['id']])
            ->unique('employee_profile_id');
        $threshold = (float) config('analytics.low_balance_threshold', 5);
        $bands = ['healthy' => 0, 'low' => 0, 'zero' => 0, 'negative' => 0, 'unavailable' => 0];

        foreach ($latest as $row) {
            $balance = $row['balance'];
            $band = match (true) {
                $balance === null => 'unavailable',
                $balance < 0 => 'negative',
                $balance == 0 => 'zero',
                $balance <= $threshold => 'low',
                default => 'healthy',
            };
            $bands[$band]++;
        }

        return $bands;
    }
}
