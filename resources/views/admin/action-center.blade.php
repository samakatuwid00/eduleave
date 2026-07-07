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
        <div class="content container-fluid action-center-page">
            <div class="page-header action-center-heading">
                <div>
                    <h3 class="page-title mb-1"><strong>Action Center</strong></h3>
                    <p class="text-muted mb-0">Live administrative issues calculated from current records.</p>
                </div>
            </div>

            <div class="row action-center-counts">
                <div class="col-6 col-lg-3"><div class="card action-count"><small>Open alerts</small><strong>{{ $counts['total'] }}</strong></div></div>
                <div class="col-6 col-lg-3"><div class="card action-count critical"><small>Critical</small><strong>{{ $counts['critical'] }}</strong></div></div>
                <div class="col-6 col-lg-3"><div class="card action-count high"><small>High</small><strong>{{ $counts['high'] }}</strong></div></div>
                <div class="col-6 col-lg-3"><div class="card action-count medium"><small>Medium</small><strong>{{ $counts['medium'] }}</strong></div></div>
            </div>

            <form class="card action-center-filters" method="GET" action="{{ route('admin.action-center') }}" aria-label="Action Center filters">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-sm-6 col-lg-3 mb-3">
                            <label for="action_category">Category</label>
                            <select class="form-control" id="action_category" name="category">
                                <option value="">All categories</option>
                                <option value="pending_approval" @selected($filters['category'] === 'pending_approval')>Pending approvals</option>
                                <option value="missing_profile" @selected($filters['category'] === 'missing_profile')>Missing profiles</option>
                                <option value="missing_card" @selected($filters['category'] === 'missing_card')>Missing leave cards</option>
                                <option value="low_balance" @selected($filters['category'] === 'low_balance')>Low balances</option>
                                <option value="data_quality" @selected($filters['category'] === 'data_quality')>Data quality</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-3">
                            <label for="action_severity">Priority</label>
                            <select class="form-control" id="action_severity" name="severity">
                                <option value="">All priorities</option>
                                <option value="critical" @selected($filters['severity'] === 'critical')>Critical</option>
                                <option value="high" @selected($filters['severity'] === 'high')>High</option>
                                <option value="medium" @selected($filters['severity'] === 'medium')>Medium</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-3">
                            <label for="action_personnel">Personnel</label>
                            <select class="form-control" id="action_personnel" name="personnel_type">
                                <option value="">All personnel</option>
                                <option value="teaching" @selected($filters['personnel_type'] === 'teaching')>Teaching</option>
                                <option value="non_teaching" @selected($filters['personnel_type'] === 'non_teaching')>Non-Teaching</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-3">
                            <label for="action_age">Minimum age</label>
                            <select class="form-control" id="action_age" name="age_days">
                                <option value="">Any age</option>
                                <option value="1" @selected((int) $filters['age_days'] === 1)>1+ days</option>
                                <option value="3" @selected((int) $filters['age_days'] === 3)>3+ days</option>
                                <option value="7" @selected((int) $filters['age_days'] === 7)>7+ days</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-lg-2 mb-3 action-center-filter-actions">
                            <button class="btn btn-primary" type="submit">Apply</button>
                            <a class="btn btn-light" href="{{ route('admin.action-center') }}">Reset</a>
                        </div>
                    </div>
                </div>
            </form>

            <section class="card action-center-list">
                <div class="card-header action-center-list-heading">
                    <h4>Open issues</h4>
                    <small>Read-only queue; resolving the underlying record removes the alert automatically.</small>
                </div>
                <div class="card-body p-0">
                    @if ($alerts->isEmpty())
                        <div class="action-center-empty">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            <strong>No matching alerts</strong>
                            <span>There is nothing requiring attention for the selected filters.</span>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="actionCenterTable" class="display action-center-table" style="width: 100%">
                                <thead>
                                <tr><th>Priority</th><th>Issue</th><th>Employee</th><th>Personnel</th><th>Age</th><th>Evidence</th><th></th></tr>
                                </thead>
                                <tbody>
                                @foreach ($alerts as $alert)
                                    <tr data-alert-fingerprint="{{ $alert['fingerprint'] }}">
                                        <td data-order="{{ ['medium' => 1, 'high' => 2, 'critical' => 3][$alert['severity']] }}"><span class="action-severity {{ $alert['severity'] }}">{{ ucfirst($alert['severity']) }}</span></td>
                                        <td>
                                            <strong>{{ $alert['title'] }}</strong>
                                            <small class="d-block text-muted">{{ str_replace('_', ' ', $alert['category']) }}</small>
                                        </td>
                                        <td>{{ $alert['employee'] }}</td>
                                        <td>{{ $alert['personnel_type'] ? str_replace('_', '-', ucfirst($alert['personnel_type'])) : 'N/A' }}</td>
                                        <td data-order="{{ $alert['age_days'] }}">{{ $alert['age_days'] }} day(s)</td>
                                        <td class="action-evidence">{{ $alert['evidence'] }}</td>
                                        <td class="text-right"><a class="btn btn-sm btn-outline-primary" href="{{ $alert['url'] }}">Review</a></td>
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
</body>
</html>
