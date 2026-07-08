<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.head')
</head>
<body>
<div class="main-wrapper">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid leave-analytics-page">
            <div class="page-header leave-analytics-heading">
                <div>
                    <h3 class="page-title mb-1"><strong>Leave Analytics</strong></h3>
                    <p class="text-muted mb-0">Usage is assigned to the month where each record starts.</p>
                </div>
            </div>

            <form class="card leave-analytics-filters" method="GET" action="{{ route('admin.leave-analytics') }}" aria-label="Leave analytics filters">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-sm-6 col-lg mb-3">
                            <label for="analytics_from">From</label>
                            <input id="analytics_from" class="form-control" type="date" name="from" value="{{ $filters['from'] }}">
                        </div>
                        <div class="col-sm-6 col-lg mb-3">
                            <label for="analytics_to">To</label>
                            <input id="analytics_to" class="form-control" type="date" name="to" value="{{ $filters['to'] }}">
                        </div>
                        <div class="col-sm-6 col-lg mb-3">
                            <label for="analytics_personnel">Personnel</label>
                            <select id="analytics_personnel" class="form-control" name="personnel_type">
                                <option value="">All personnel</option>
                                <option value="teaching" @selected($filters['personnel_type'] === 'teaching')>Teaching</option>
                                <option value="non_teaching" @selected($filters['personnel_type'] === 'non_teaching')>Non-Teaching</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg mb-3">
                            <label for="analytics_leave_type">Leave type</label>
                            <select id="analytics_leave_type" class="form-control" name="leave_type">
                                <option value="">All leave types</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->code }}" @selected($filters['leave_type'] === $leaveType->code)>{{ $leaveType->name }}</option>
                                @endforeach
                                <option value="unclassified" @selected($filters['leave_type'] === 'unclassified')>Unclassified</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg mb-3">
                            <label for="analytics_parse_state">Data state</label>
                            <select id="analytics_parse_state" class="form-control" name="parse_state">
                                <option value="">All states</option>
                                <option value="parsed" @selected($filters['parse_state'] === 'parsed')>Parsed</option>
                                <option value="partial" @selected($filters['parse_state'] === 'partial')>Partial</option>
                                <option value="unparseable" @selected($filters['parse_state'] === 'unparseable')>Unparseable</option>
                                <option value="not_applicable" @selected($filters['parse_state'] === 'not_applicable')>Not applicable</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-auto mb-3 leave-analytics-filter-actions">
                            <button class="btn btn-primary" type="submit">Apply</button>
                            <a class="btn btn-light" href="{{ route('admin.leave-analytics') }}">Reset</a>
                        </div>
                    </div>
                    @error('from')<p class="text-danger mb-0">{{ $message }}</p>@enderror
                    @error('to')<p class="text-danger mb-0">{{ $message }}</p>@enderror
                </div>
            </form>

            <div class="row leave-analytics-kpis">
                <div class="col-6 col-xl-2"><div class="card analytics-kpi"><small>Employees represented</small><strong>{{ $kpis['employees'] }}</strong></div></div>
                <div class="col-6 col-xl-2"><div class="card analytics-kpi"><small>Records in range</small><strong>{{ $kpis['records'] }}</strong></div></div>
                <div class="col-6 col-xl-2"><div class="card analytics-kpi"><small>Paid units</small><strong>{{ number_format($kpis['paid'], 2) }}</strong></div></div>
                <div class="col-6 col-xl-2"><div class="card analytics-kpi"><small>Unpaid units</small><strong>{{ number_format($kpis['unpaid'], 2) }}</strong></div></div>
                <div class="col-6 col-xl-2"><div class="card analytics-kpi"><small>Low/zero balances</small><strong>{{ $kpis['low_balances'] }}</strong></div></div>
                <div class="col-6 col-xl-2"><div class="card analytics-kpi warning"><small>Excluded rows</small><strong>{{ $kpis['excluded'] }}</strong></div></div>
            </div>

            <div class="analytics-definition alert alert-light" role="note">
                <strong>How to read this page:</strong> paid and unpaid values are recorded units from the selected personnel card format. Running balances are never summed; balance bands use only each employee’s latest included record. Rows without a usable reporting date are shown below but excluded from totals.
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <section class="card analytics-panel">
                        <div class="card-header"><h4>Monthly leave usage</h4></div>
                        <div class="card-body"><div class="analytics-chart"><canvas id="analyticsMonthlyChart" aria-label="Monthly leave usage by category"></canvas></div></div>
                    </section>
                </div>
                <div class="col-xl-4">
                    <section class="card analytics-panel">
                        <div class="card-header"><h4>Current balance distribution</h4></div>
                        <div class="card-body">
                            <div class="analytics-chart analytics-chart-small"><canvas id="analyticsBalanceChart" aria-label="Current leave balance distribution"></canvas></div>
                            <ul class="dashboard-summary-list">
                                <li><span>Healthy (&gt; {{ config('analytics.low_balance_threshold') }})</span><strong>{{ $balances['healthy'] }}</strong></li>
                                <li><span>Low</span><strong>{{ $balances['low'] }}</strong></li>
                                <li><span>Zero</span><strong>{{ $balances['zero'] }}</strong></li>
                                <li><span>Negative</span><strong>{{ $balances['negative'] }}</strong></li>
                                <li><span>Unavailable</span><strong>{{ $balances['unavailable'] }}</strong></li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>

            <section class="card analytics-panel">
                <div class="card-header"><h4>Recorded usage by category</h4></div>
                <div class="card-body"><div class="analytics-chart analytics-chart-medium"><canvas id="analyticsCategoryChart" aria-label="Recorded leave usage by category"></canvas></div></div>
            </section>

            <section class="card analytics-panel analytics-detail-panel">
                <div class="card-header analytics-panel-heading">
                    <h4>Leave-record details</h4>
                    <small>{{ $kpis['excluded'] }} row(s) excluded from summary calculations</small>
                </div>
                <div class="card-body p-0">
                    @if ($rows->isEmpty())
                        <div class="analytics-empty">No leave-card rows match the selected filters.</div>
                    @else
                        <div class="table-responsive">
                            <table id="leaveAnalyticsTable" class="display leave-analytics-table" style="width: 100%">
                                <thead>
                                <tr><th>Employee</th><th>Name</th><th>Personnel</th><th>Period</th><th>Leave type</th><th>Paid</th><th>Unpaid</th><th>Balance</th><th>Data state</th><th></th></tr>
                                </thead>
                                <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ $row['employee_number'] }}</td>
                                        <td>{{ $row['employee_name'] }}</td>
                                        <td>{{ $row['personnel_type'] === 'non_teaching' ? 'Non-Teaching' : 'Teaching' }}</td>
                                        <td data-order="{{ $row['period_start'] ?? '' }}">
                                            {{ $row['period_start'] ? $row['period_start'].' – '.($row['period_end'] ?? $row['period_start']) : 'Unknown' }}
                                        </td>
                                        <td>{{ $row['leave_type'] }}</td>
                                        <td data-order="{{ $row['paid'] ?? '' }}">{{ $row['paid'] === null ? '—' : number_format($row['paid'], 2) }}</td>
                                        <td data-order="{{ $row['unpaid'] ?? '' }}">{{ $row['unpaid'] === null ? '—' : number_format($row['unpaid'], 2) }}</td>
                                        <td data-order="{{ $row['balance'] ?? '' }}">{{ $row['balance_display'] }}</td>
                                        <td>
                                            <span class="analytics-state {{ $row['parse_state'] }}">{{ str_replace('_', ' ', ucfirst($row['parse_state'])) }}</span>
                                            @if ($row['parse_note'])<small class="d-block text-muted">{{ $row['parse_note'] }}</small>@endif
                                        </td>
                                        <td><a class="btn btn-sm btn-outline-primary" href="{{ $row['url'] }}">View</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    @include('admin.loader')
    @include('admin.footer')
</div>

<script src="{{ asset('admincss/assets/plugins/chartjs/chart.min.js') }}"></script>
<script>
    (() => {
        if (typeof Chart === 'undefined') return;
        Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--dashboard-text').trim() || '#475569';
        const common = { responsive: true, maintainAspectRatio: false };

        new Chart(document.getElementById('analyticsMonthlyChart'), {
            type: 'bar',
            data: {
                labels: @json($monthly['labels']),
                datasets: [
                    { label: 'Vacation', data: @json($monthly['vacation']), backgroundColor: '#2563eb' },
                    { label: 'Sick', data: @json($monthly['sick']), backgroundColor: '#8b5cf6' },
                    { label: 'Other', data: @json($monthly['other']), backgroundColor: '#14b8a6' },
                    { label: 'Unclassified', data: @json($monthly['unclassified']), backgroundColor: '#94a3b8' },
                    { label: 'Unpaid', data: @json($monthly['unpaid']), backgroundColor: '#ef4444' }
                ]
            },
            options: { ...common, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } } }
        });

        new Chart(document.getElementById('analyticsBalanceChart'), {
            type: 'doughnut',
            data: {
                labels: ['Healthy', 'Low', 'Zero', 'Negative', 'Unavailable'],
                datasets: [{ data: @json(array_values($balances)), backgroundColor: ['#22c55e', '#f59e0b', '#64748b', '#ef4444', '#cbd5e1'] }]
            },
            options: common
        });

        new Chart(document.getElementById('analyticsCategoryChart'), {
            type: 'bar',
            data: { labels: @json($categories['labels']), datasets: [{ label: 'Recorded units', data: @json($categories['values']), backgroundColor: '#2563eb' }] },
            options: { ...common, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
        });
    })();
</script>
</body>
</html>
