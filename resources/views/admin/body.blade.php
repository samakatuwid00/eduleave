<div class="page-wrapper">
    <div class="content container-fluid admin-dashboard">
        <div class="page-header dashboard-heading">
            <div>
                <h3 class="page-title mb-1"><strong>Dashboard</strong></h3>
                <p class="text-muted mb-0">Operational overview · Updated {{ $generated_at }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="card dashboard-filters" aria-label="Dashboard filters">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-sm-6 col-lg-2 mb-3">
                        <label for="dashboard_from">From</label>
                        <input id="dashboard_from" class="form-control" type="date" name="from" value="{{ $filters['from'] }}">
                    </div>
                    <div class="col-sm-6 col-lg-2 mb-3">
                        <label for="dashboard_to">To</label>
                        <input id="dashboard_to" class="form-control" type="date" name="to" value="{{ $filters['to'] }}">
                    </div>
                    <div class="col-sm-6 col-lg-2 mb-3">
                        <label for="dashboard_personnel">Personnel</label>
                        <select id="dashboard_personnel" class="form-control" name="personnel_type">
                            <option value="">All personnel</option>
                            <option value="teaching" @selected($filters['personnel_type'] === 'teaching')>Teaching</option>
                            <option value="non_teaching" @selected($filters['personnel_type'] === 'non_teaching')>Non-Teaching</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-lg-2 mb-3">
                        <label for="dashboard_status">Account status</label>
                        <select id="dashboard_status" class="form-control" name="user_status">
                            <option value="">All statuses</option>
                            <option value="pending" @selected($filters['user_status'] === 'pending')>Pending</option>
                            <option value="active" @selected($filters['user_status'] === 'active')>Approved</option>
                            <option value="rejected" @selected($filters['user_status'] === 'rejected')>Rejected</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-lg-2 mb-3">
                        <label for="dashboard_leave_type">Leave type</label>
                        <select id="dashboard_leave_type" class="form-control" name="leave_type">
                            <option value="">All leave types</option>
                            @foreach ($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->code }}" @selected($filters['leave_type'] === $leaveType->code)>
                                    {{ $leaveType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-lg-2 mb-3 dashboard-filter-actions">
                        <button class="btn btn-primary" type="submit">Apply</button>
                        <a class="btn btn-light" href="{{ route('admin.dashboard') }}">Reset</a>
                    </div>
                </div>
                @error('from')<p class="text-danger mb-0">{{ $message }}</p>@enderror
                @error('to')<p class="text-danger mb-0">{{ $message }}</p>@enderror
            </div>
        </form>

        <div class="row dashboard-kpis">
            <div class="col-sm-6 col-xl-3">
                <a class="dashboard-kpi card" href="{{ url('/admin/users/view-all_users') }}">
                    <span class="dashboard-kpi-icon kpi-blue"><i class="fas fa-users" aria-hidden="true"></i></span>
                    <span><small>Employees</small><strong>{{ number_format($kpis['employees']) }}</strong></span>
                </a>
            </div>
            <div class="col-sm-6 col-xl-3">
                <a class="dashboard-kpi card" href="{{ url('/admin/users/view-pending_users') }}">
                    <span class="dashboard-kpi-icon kpi-amber"><i class="fas fa-user-clock" aria-hidden="true"></i></span>
                    <span><small>Verified pending</small><strong>{{ number_format($kpis['pending']) }}</strong></span>
                </a>
            </div>
            <div class="col-sm-6 col-xl-3">
                <a class="dashboard-kpi card" href="{{ url('/admin/teacher_leave_cards') }}?attention=missing">
                    <span class="dashboard-kpi-icon kpi-red"><i class="fas fa-folder-open" aria-hidden="true"></i></span>
                    <span><small>Missing leave cards</small><strong>{{ number_format($kpis['missing_cards']) }}</strong></span>
                </a>
            </div>
            <div class="col-sm-6 col-xl-3">
                <a class="dashboard-kpi card" href="{{ url('/admin/teacher_leave_cards') }}?attention=low-balance">
                    <span class="dashboard-kpi-icon kpi-green"><i class="fas fa-battery-quarter" aria-hidden="true"></i></span>
                    <span><small>Low balances (≤ {{ number_format(config('analytics.low_balance_threshold'), 0) }})</small><strong>{{ number_format($kpis['low_balances']) }}</strong></span>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <section class="card dashboard-panel">
                    <div class="card-header"><h4>Registration and decisions</h4></div>
                    <div class="card-body">
                        <div class="dashboard-chart"><canvas id="activityChart" aria-label="Monthly registration, approval, and rejection activity"></canvas></div>
                        <div class="table-responsive dashboard-chart-table">
                            <table class="table table-sm">
                                <caption class="sr-only">Monthly registration and decision counts</caption>
                                <thead><tr><th>Month</th><th>Registrations</th><th>Approvals</th><th>Rejections</th></tr></thead>
                                <tbody>
                                @foreach ($activity['labels'] as $index => $label)
                                    <tr><th>{{ $label }}</th><td>{{ $activity['registrations'][$index] }}</td><td>{{ $activity['approvals'][$index] }}</td><td>{{ $activity['rejections'][$index] }}</td></tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-4">
                <section class="card dashboard-panel">
                    <div class="card-header"><h4>Approval pipeline</h4></div>
                    <div class="card-body">
                        <div class="dashboard-chart dashboard-chart-small"><canvas id="pipelineChart" aria-label="Employee approval pipeline"></canvas></div>
                        <ul class="dashboard-summary-list">
                            @foreach ($pipeline['labels'] as $index => $label)
                                <li><span>{{ $label }}</span><strong>{{ $pipeline['values'][$index] }}</strong></li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <section class="card dashboard-panel">
                    <div class="card-header dashboard-panel-title">
                        <h4>Monthly leave usage</h4>
                        <small>{{ $leave_trend['excluded'] }} unparseable row(s) excluded</small>
                    </div>
                    <div class="card-body">
                        <div class="dashboard-chart"><canvas id="leaveChart" aria-label="Monthly paid and unpaid leave quantities"></canvas></div>
                        <div class="table-responsive dashboard-chart-table">
                            <table class="table table-sm">
                                <caption class="sr-only">Monthly leave quantities</caption>
                                <thead><tr><th>Month</th><th>Vacation</th><th>Sick</th><th>Other</th><th>Unpaid</th></tr></thead>
                                <tbody>
                                @foreach ($leave_trend['labels'] as $index => $label)
                                    <tr>
                                        <th>{{ $label }}</th>
                                        <td>{{ number_format($leave_trend['vacation'][$index], 2) }}</td>
                                        <td>{{ number_format($leave_trend['sick'][$index], 2) }}</td>
                                        <td>{{ number_format($leave_trend['other'][$index], 2) }}</td>
                                        <td>{{ number_format($leave_trend['unpaid'][$index], 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-4">
                <section class="card dashboard-panel">
                    <div class="card-header"><h4>Personnel composition</h4></div>
                    <div class="card-body">
                        <div class="dashboard-chart dashboard-chart-small"><canvas id="personnelChart" aria-label="Teaching, non-teaching, and unclassified employees"></canvas></div>
                        <ul class="dashboard-summary-list">
                            @foreach ($personnel['labels'] as $index => $label)
                                <li><span>{{ $label }}</span><strong>{{ $personnel['values'][$index] }}</strong></li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            </div>
        </div>

        <section class="card dashboard-panel">
            <div class="card-header dashboard-panel-title">
                <h4>Needs attention</h4>
                <a href="{{ route('admin.action-center') }}">Open Action Center</a>
            </div>
            <div class="card-body p-0">
                @if ($attention->isEmpty())
                    <div class="dashboard-empty">No verified registrations currently need approval.</div>
                @else
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead><tr><th>Priority</th><th>Item</th><th>Employee</th><th>Age</th><th></th></tr></thead>
                            <tbody>
                            @foreach ($attention as $item)
                                <tr>
                                    <td><span class="dashboard-priority {{ strtolower($item['priority']) }}">{{ $item['priority'] }}</span></td>
                                    <td>{{ $item['item'] }}</td>
                                    <td>{{ $item['employee'] }}</td>
                                    <td>{{ $item['age'] }}</td>
                                    <td class="text-right"><a class="btn btn-sm btn-outline-primary" href="{{ $item['url'] }}">Review</a></td>
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

<script src="{{ asset('admincss/assets/plugins/chartjs/chart.min.js') }}"></script>
<script>
    (() => {
        if (typeof Chart === 'undefined') return;

        const textColor = getComputedStyle(document.documentElement).getPropertyValue('--dashboard-text').trim() || '#475569';
        Chart.defaults.color = textColor;
        const common = { responsive: true, maintainAspectRatio: false };

        new Chart(document.getElementById('activityChart'), {
            type: 'line',
            data: {
                labels: @json($activity['labels']),
                datasets: [
                    { label: 'Registrations', data: @json($activity['registrations']), borderColor: '#2563eb', backgroundColor: 'transparent' },
                    { label: 'Approvals', data: @json($activity['approvals']), borderColor: '#16a34a', backgroundColor: 'transparent' },
                    { label: 'Rejections', data: @json($activity['rejections']), borderColor: '#dc2626', backgroundColor: 'transparent' }
                ]
            },
            options: common
        });

        new Chart(document.getElementById('pipelineChart'), {
            type: 'bar',
            data: { labels: @json($pipeline['labels']), datasets: [{ data: @json($pipeline['values']), backgroundColor: ['#94a3b8', '#f59e0b', '#22c55e', '#ef4444'] }] },
            options: { ...common, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        new Chart(document.getElementById('leaveChart'), {
            type: 'bar',
            data: {
                labels: @json($leave_trend['labels']),
                datasets: [
                    { label: 'Vacation', data: @json($leave_trend['vacation']), backgroundColor: '#2563eb' },
                    { label: 'Sick', data: @json($leave_trend['sick']), backgroundColor: '#8b5cf6' },
                    { label: 'Other', data: @json($leave_trend['other']), backgroundColor: '#64748b' },
                    { label: 'Unpaid', data: @json($leave_trend['unpaid']), backgroundColor: '#ef4444' }
                ]
            },
            options: { ...common, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } } }
        });

        new Chart(document.getElementById('personnelChart'), {
            type: 'doughnut',
            data: { labels: @json($personnel['labels']), datasets: [{ data: @json($personnel['values']), backgroundColor: ['#2563eb', '#14b8a6', '#94a3b8'] }] },
            options: common
        });
    })();
</script>

@include('admin.sidebar.more_info')
