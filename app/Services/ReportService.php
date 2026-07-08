<?php

namespace App\Services;

use App\Models\ImportBatch;
use App\Models\PersonnelType;
use InvalidArgumentException;

class ReportService
{
    public function __construct(
        private readonly LeaveAnalyticsService $analytics,
        private readonly ActionCenterService $actionCenter,
    ) {}

    public function registry(): array
    {
        return [
            'monthly_summary' => [
                'label' => 'Monthly Leave Summary',
                'description' => 'Monthly paid and unpaid units by leave category.',
                'definition' => 'Uses the same included rows and month-of-period logic as Leave Analytics.',
                'filters' => ['from', 'to', 'personnel_type', 'leave_type', 'parse_state'],
            ],
            'employee_ledger' => [
                'label' => 'Employee Leave Ledger',
                'description' => 'Filtered leave-card history for one employee.',
                'definition' => 'Shows normalized leave rows and preserves unavailable values as blank.',
                'filters' => ['from', 'to', 'employee_number', 'leave_type', 'parse_state'],
            ],
            'low_balance' => [
                'label' => 'Balance Risk Report',
                'description' => 'Latest low, zero, negative, or unavailable balance in the selected range.',
                'definition' => 'Balances are never summed; only each employee’s latest included row is classified.',
                'filters' => ['from', 'to', 'personnel_type', 'leave_type', 'parse_state'],
            ],
            'leave_without_pay' => [
                'label' => 'Leave Without Pay',
                'description' => 'Leave rows with recorded unpaid units.',
                'definition' => 'Includes only normalized rows with unpaid units greater than zero.',
                'filters' => ['from', 'to', 'personnel_type', 'leave_type', 'parse_state'],
            ],
            'personnel_comparison' => [
                'label' => 'Personnel-Type Comparison',
                'description' => 'Teaching and non-teaching measures shown on separate rows.',
                'definition' => 'Recorded units remain separate by personnel format and are not treated as policy-equivalent balances.',
                'filters' => ['from', 'to', 'leave_type', 'parse_state'],
            ],
            'approval_aging' => [
                'label' => 'Approval Aging',
                'description' => 'Verified registrations waiting at least one day for approval.',
                'definition' => 'Uses the same age and priority rules as the Action Center.',
                'filters' => ['personnel_type'],
            ],
            'missing_records' => [
                'label' => 'Missing Profiles and Leave Cards',
                'description' => 'Active employees with missing profiles or leave-card records.',
                'definition' => 'Uses the live missing-record rules from the Action Center.',
                'filters' => ['personnel_type'],
            ],
            'import_history' => [
                'label' => 'Import History',
                'description' => 'Workbook batches, actors, status, row counts, and rollback reasons.',
                'definition' => 'Includes import batches only; adjustment audit history belongs to the governance module.',
                'filters' => ['from', 'to', 'personnel_type', 'employee_number'],
            ],
        ];
    }

    public function build(string $code, array $filters): array
    {
        $definition = $this->registry()[$code] ?? null;

        if (! $definition) {
            throw new InvalidArgumentException("Unknown report [{$code}].");
        }

        $effectiveFilters = $filters;
        foreach (['personnel_type', 'leave_type', 'parse_state', 'employee_number'] as $filter) {
            if (! in_array($filter, $definition['filters'], true)) {
                $effectiveFilters[$filter] = null;
            }
        }

        $data = match ($code) {
            'monthly_summary' => $this->monthlySummary($effectiveFilters),
            'employee_ledger' => $this->employeeLedger($effectiveFilters),
            'low_balance' => $this->lowBalance($effectiveFilters),
            'leave_without_pay' => $this->leaveWithoutPay($effectiveFilters),
            'personnel_comparison' => $this->personnelComparison($effectiveFilters),
            'approval_aging' => $this->approvalAging($effectiveFilters),
            'missing_records' => $this->missingRecords($effectiveFilters),
            'import_history' => $this->importHistory($effectiveFilters),
        };
        $limit = max(1, (int) config('reports.max_sync_rows', 5000));
        $allRows = collect($data['rows']);
        $truncated = max(0, $allRows->count() - $limit);

        return [
            'code' => $code,
            ...$definition,
            'columns' => $data['columns'],
            'rows' => $allRows->take($limit)->values(),
            'row_count' => $allRows->count(),
            'excluded_count' => $data['excluded_count'] ?? 0,
            'truncated_count' => $truncated,
            'totals' => $data['totals'] ?? [],
            'filters_applied' => collect($effectiveFilters)
                ->only($definition['filters'])
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->all(),
            'generated_at' => now(config('automation.timezone', 'Asia/Manila')),
        ];
    }

    private function monthlySummary(array $filters): array
    {
        $analytics = $this->analytics->build($this->analyticsFilters($filters));
        $rows = collect($analytics['monthly']['labels'])->map(fn (string $label, int $index) => [
            'month' => $label,
            'vacation' => (float) $analytics['monthly']['vacation'][$index],
            'sick' => (float) $analytics['monthly']['sick'][$index],
            'other' => (float) $analytics['monthly']['other'][$index],
            'unclassified' => (float) $analytics['monthly']['unclassified'][$index],
            'paid_total' => round(
                (float) $analytics['monthly']['vacation'][$index]
                + (float) $analytics['monthly']['sick'][$index]
                + (float) $analytics['monthly']['other'][$index]
                + (float) $analytics['monthly']['unclassified'][$index],
                2,
            ),
            'unpaid' => (float) $analytics['monthly']['unpaid'][$index],
        ]);

        return [
            'columns' => $this->columns([
                'month' => 'Month', 'vacation' => 'Vacation Units', 'sick' => 'Sick Units',
                'other' => 'Other Units', 'unclassified' => 'Unclassified Units',
                'paid_total' => 'Paid Units', 'unpaid' => 'Unpaid Units',
            ], ['vacation', 'sick', 'other', 'unclassified', 'paid_total', 'unpaid']),
            'rows' => $rows,
            'excluded_count' => $analytics['kpis']['excluded'],
            'totals' => [
                'Employees represented' => $analytics['kpis']['employees'],
                'Included records' => $analytics['kpis']['records'],
                'Paid units' => $analytics['kpis']['paid'],
                'Unpaid units' => $analytics['kpis']['unpaid'],
            ],
        ];
    }

    private function employeeLedger(array $filters): array
    {
        $analytics = $this->analytics->build($this->analyticsFilters($filters));
        $rows = $analytics['rows']->where('employee_number', $filters['employee_number'])->map(fn (array $row) => [
            'period_start' => $row['period_start'],
            'period_end' => $row['period_end'],
            'leave_type' => $row['leave_type'],
            'paid' => $row['paid'],
            'unpaid' => $row['unpaid'],
            'balance' => $row['balance'],
            'data_state' => $row['parse_state'],
            'note' => $row['parse_note'],
            'included' => $row['included'],
        ]);
        $included = $rows->where('included', true);

        return [
            'columns' => $this->columns([
                'period_start' => 'Period Start', 'period_end' => 'Period End', 'leave_type' => 'Leave Type',
                'paid' => 'Paid Units', 'unpaid' => 'Unpaid Units', 'balance' => 'Balance',
                'data_state' => 'Data State', 'note' => 'Normalization Note',
            ], ['paid', 'unpaid', 'balance'], ['period_start', 'period_end']),
            'rows' => $rows,
            'excluded_count' => $rows->where('included', false)->count(),
            'totals' => [
                'Rows' => $rows->count(),
                'Paid units' => round((float) $included->sum('paid'), 2),
                'Unpaid units' => round((float) $included->sum('unpaid'), 2),
            ],
        ];
    }

    private function lowBalance(array $filters): array
    {
        $analytics = $this->analytics->build($this->analyticsFilters($filters));
        $threshold = (float) config('analytics.low_balance_threshold', 5);
        $rows = $analytics['rows']->where('included', true)
            ->sortByDesc(fn (array $row) => [$row['period_start'], $row['id']])
            ->unique('employee_profile_id')
            ->filter(fn (array $row) => $row['balance'] === null || $row['balance'] <= $threshold)
            ->map(fn (array $row) => [
                'employee_number' => $row['employee_number'],
                'employee_name' => $row['employee_name'],
                'personnel_type' => $this->personnelLabel($row['personnel_type']),
                'period_start' => $row['period_start'],
                'balance' => $row['balance'],
                'risk_band' => match (true) {
                    $row['balance'] === null => 'Unavailable',
                    $row['balance'] < 0 => 'Negative',
                    $row['balance'] == 0 => 'Zero',
                    default => 'Low',
                },
            ]);

        return [
            'columns' => $this->columns([
                'employee_number' => 'Employee Number', 'employee_name' => 'Employee Name',
                'personnel_type' => 'Personnel Type', 'period_start' => 'Latest Period',
                'balance' => 'Balance', 'risk_band' => 'Risk Band',
            ], ['balance'], ['period_start']),
            'rows' => $rows,
            'excluded_count' => $analytics['kpis']['excluded'],
            'totals' => ['Employees at risk' => $rows->count(), 'Threshold' => $threshold],
        ];
    }

    private function leaveWithoutPay(array $filters): array
    {
        $analytics = $this->analytics->build($this->analyticsFilters($filters));
        $rows = $analytics['rows']->where('included', true)
            ->filter(fn (array $row) => (float) ($row['unpaid'] ?? 0) > 0)
            ->map(fn (array $row) => [
                'employee_number' => $row['employee_number'],
                'employee_name' => $row['employee_name'],
                'personnel_type' => $this->personnelLabel($row['personnel_type']),
                'period_start' => $row['period_start'],
                'period_end' => $row['period_end'],
                'leave_type' => $row['leave_type'],
                'unpaid' => $row['unpaid'],
            ]);

        return [
            'columns' => $this->columns([
                'employee_number' => 'Employee Number', 'employee_name' => 'Employee Name',
                'personnel_type' => 'Personnel Type', 'period_start' => 'Period Start',
                'period_end' => 'Period End', 'leave_type' => 'Leave Type', 'unpaid' => 'Unpaid Units',
            ], ['unpaid'], ['period_start', 'period_end']),
            'rows' => $rows,
            'excluded_count' => $analytics['kpis']['excluded'],
            'totals' => ['Rows' => $rows->count(), 'Unpaid units' => round((float) $rows->sum('unpaid'), 2)],
        ];
    }

    private function personnelComparison(array $filters): array
    {
        $rows = collect([
            PersonnelType::CODE_TEACHING => 'Teaching',
            PersonnelType::CODE_NON_TEACHING => 'Non-Teaching',
        ])->map(function (string $label, string $code) use ($filters) {
            $data = $this->analytics->build([
                ...$this->analyticsFilters($filters),
                'personnel_type' => $code,
            ]);

            return [
                'personnel_type' => $label,
                'employees' => $data['kpis']['employees'],
                'records' => $data['kpis']['records'],
                'paid' => $data['kpis']['paid'],
                'unpaid' => $data['kpis']['unpaid'],
                'low_balances' => $data['kpis']['low_balances'],
                'excluded' => $data['kpis']['excluded'],
            ];
        })->values();

        return [
            'columns' => $this->columns([
                'personnel_type' => 'Personnel Type', 'employees' => 'Employees', 'records' => 'Included Records',
                'paid' => 'Paid Units', 'unpaid' => 'Unpaid Units',
                'low_balances' => 'Low/Zero/Negative', 'excluded' => 'Excluded Rows',
            ], ['employees', 'records', 'paid', 'unpaid', 'low_balances', 'excluded']),
            'rows' => $rows,
            'excluded_count' => $rows->sum('excluded'),
            'totals' => [],
        ];
    }

    private function approvalAging(array $filters): array
    {
        $alerts = $this->actionCenter->build([
            'category' => 'pending_approval',
            'severity' => null,
            'personnel_type' => $filters['personnel_type'],
            'age_days' => null,
        ])['alerts'];
        $rows = $alerts->map(fn (array $alert) => [
            'employee' => $alert['employee'],
            'personnel_type' => $this->personnelLabel($alert['personnel_type']),
            'age_days' => $alert['age_days'],
            'priority' => ucfirst($alert['severity']),
            'evidence' => $alert['evidence'],
        ]);

        return [
            'columns' => $this->columns([
                'employee' => 'Employee', 'personnel_type' => 'Personnel Type',
                'age_days' => 'Age in Days', 'priority' => 'Priority', 'evidence' => 'Evidence',
            ], ['age_days']),
            'rows' => $rows,
            'totals' => ['Pending approvals' => $rows->count()],
        ];
    }

    private function missingRecords(array $filters): array
    {
        $alerts = $this->actionCenter->build([
            'category' => null,
            'severity' => null,
            'personnel_type' => $filters['personnel_type'],
            'age_days' => null,
        ])['alerts']->whereIn('category', ['missing_profile', 'missing_card']);
        $rows = $alerts->map(fn (array $alert) => [
            'issue' => $alert['category'] === 'missing_profile' ? 'Missing profile' : 'Missing leave card',
            'employee' => $alert['employee'],
            'personnel_type' => $this->personnelLabel($alert['personnel_type']),
            'age_days' => $alert['age_days'],
            'priority' => ucfirst($alert['severity']),
            'evidence' => $alert['evidence'],
        ]);

        return [
            'columns' => $this->columns([
                'issue' => 'Issue', 'employee' => 'Employee', 'personnel_type' => 'Personnel Type',
                'age_days' => 'Age in Days', 'priority' => 'Priority', 'evidence' => 'Evidence',
            ], ['age_days']),
            'rows' => $rows,
            'totals' => ['Missing records' => $rows->count()],
        ];
    }

    private function importHistory(array $filters): array
    {
        $query = ImportBatch::query()
            ->with(['admin', 'employeeProfile.user', 'employeeProfile.personnelType'])
            ->whereBetween('created_at', [$filters['from'].' 00:00:00', $filters['to'].' 23:59:59'])
            ->when($filters['personnel_type'], fn ($query, string $type) => $query->where('card_type', $type))
            ->when($filters['employee_number'], fn ($query, string $number) => $query->whereHas(
                'employeeProfile', fn ($profile) => $profile->where('employee_number', $number),
            ))
            ->latest();
        $rows = $query->get()->map(fn (ImportBatch $batch) => [
            'created_at' => $batch->created_at?->format('Y-m-d H:i:s'),
            'employee_number' => $batch->employeeProfile?->employee_number,
            'employee_name' => $batch->employeeProfile?->user?->name,
            'personnel_type' => $this->personnelLabel($batch->card_type),
            'file_name' => $batch->original_name,
            'admin' => $batch->admin?->name,
            'rows' => $batch->row_count,
            'errors' => $batch->error_count,
            'status' => $batch->status,
            'rollback_reason' => $batch->rollback_reason,
        ]);

        return [
            'columns' => $this->columns([
                'created_at' => 'Imported At', 'employee_number' => 'Employee Number',
                'employee_name' => 'Employee Name', 'personnel_type' => 'Personnel Type',
                'file_name' => 'File Name', 'admin' => 'Admin', 'rows' => 'Rows',
                'errors' => 'Errors', 'status' => 'Status', 'rollback_reason' => 'Rollback Reason',
            ], ['rows', 'errors'], ['created_at']),
            'rows' => $rows,
            'totals' => ['Batches' => $rows->count(), 'Imported rows recorded' => $rows->sum('rows')],
        ];
    }

    private function analyticsFilters(array $filters): array
    {
        return [
            'from' => $filters['from'],
            'to' => $filters['to'],
            'personnel_type' => $filters['personnel_type'],
            'leave_type' => $filters['leave_type'],
            'parse_state' => $filters['parse_state'],
        ];
    }

    private function columns(array $labels, array $numeric = [], array $dates = []): array
    {
        return collect($labels)->map(fn (string $label, string $key) => [
            'key' => $key,
            'label' => $label,
            'type' => in_array($key, $numeric, true) ? 'number' : (in_array($key, $dates, true) ? 'date' : 'text'),
        ])->values()->all();
    }

    private function personnelLabel(?string $code): string
    {
        return match ($code) {
            PersonnelType::CODE_TEACHING => 'Teaching',
            PersonnelType::CODE_NON_TEACHING => 'Non-Teaching',
            default => 'Unavailable',
        };
    }
}
