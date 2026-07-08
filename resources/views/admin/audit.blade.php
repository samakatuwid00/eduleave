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
        <div class="content container-fluid audit-page">
            <div class="page-header audit-heading">
                <div>
                    <h3 class="page-title mb-1"><strong>Audit and Governance</strong></h3>
                    <p class="text-muted mb-0">Append-only administrative history with redacted change details.</p>
                </div>
                <a class="btn btn-success" href="{{ route('admin.audit.export', $filters) }}"><i class="fas fa-file-excel"></i> Export filtered log</a>
            </div>

            @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if ($errors->any())
                <div class="alert alert-danger">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
            @endif

            <form class="card audit-filter-card" method="GET" action="{{ route('admin.audit') }}">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-sm-6 col-lg-2 mb-3"><label>From</label><input class="form-control" type="date" name="from" value="{{ $filters['from'] }}"></div>
                        <div class="col-sm-6 col-lg-2 mb-3"><label>To</label><input class="form-control" type="date" name="to" value="{{ $filters['to'] }}"></div>
                        <div class="col-sm-6 col-lg-2 mb-3">
                            <label>Actor</label>
                            <select class="form-control" name="actor"><option value="">All actors</option>@foreach ($admins as $admin)<option value="{{ $admin->id }}" @selected((string) $filters['actor'] === (string) $admin->id)>{{ $admin->name }}</option>@endforeach</select>
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-3">
                            <label>Action</label>
                            <select class="form-control" name="action"><option value="">All actions</option>@foreach ($actions as $action)<option value="{{ $action }}" @selected($filters['action'] === $action)>{{ $action }}</option>@endforeach</select>
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-3">
                            <label>Target type</label>
                            <select class="form-control" name="target_type"><option value="">All types</option>@foreach ($targetTypes as $type)<option value="{{ $type }}" @selected($filters['target_type'] === $type)>{{ $type }}</option>@endforeach</select>
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-3"><label>Target ID</label><input class="form-control" name="target_id" value="{{ $filters['target_id'] }}"></div>
                        <div class="col-sm-6 col-lg-3 mb-3"><label>Employee number</label><input class="form-control" name="employee_number" value="{{ $filters['employee_number'] }}"></div>
                        <div class="col-sm-6 col-lg-4 mb-3"><label>Correlation ID</label><input class="form-control" name="correlation_id" value="{{ $filters['correlation_id'] }}"></div>
                        <div class="col-sm-12 col-lg-3 mb-3 audit-filter-actions"><button class="btn btn-primary" type="submit">Apply</button><a class="btn btn-light" href="{{ route('admin.audit') }}">Reset</a></div>
                    </div>
                </div>
            </form>

            @if (auth()->user()->isSuperAdmin())
                <section class="card audit-roles-card">
                    <div class="card-header"><h4>Administrator roles</h4><small>Existing unassigned admins retain full access for compatibility.</small></div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($admins as $admin)
                                <div class="col-xl-4 mb-3">
                                    <form class="audit-role-form" method="POST" action="{{ route('admin.audit.role', $admin) }}">
                                        @csrf @method('PUT')
                                        <strong>{{ $admin->name }}</strong><small>{{ $admin->email }}</small>
                                        <select class="form-control form-control-sm" name="admin_role">
                                            <option value="super_admin" @selected($admin->effectiveAdminRole() === 'super_admin')>Full Administrator</option>
                                            <option value="records_admin" @selected($admin->effectiveAdminRole() === 'records_admin')>Records Administrator</option>
                                            <option value="auditor" @selected($admin->effectiveAdminRole() === 'auditor')>Auditor</option>
                                        </select>
                                        <input class="form-control form-control-sm" name="change_reason" maxlength="500" placeholder="Reason for role change" required>
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Update role</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <section class="card audit-log-card">
                <div class="card-header"><h4>Audit events</h4><small>{{ $events->total() }} matching event(s)</small></div>
                <div class="card-body p-0">
                    @if ($events->isEmpty())
                        <div class="audit-empty">No audit events match the selected filters.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped audit-table mb-0">
                                <thead><tr><th>Timestamp</th><th>Actor / Action</th><th>Target</th><th>Reason</th><th>Change</th><th>Correlation</th><th>Hold</th></tr></thead>
                                <tbody>
                                @foreach ($events as $event)
                                    <tr>
                                        <td>{{ $event->created_at->format('M j, Y g:i:s A') }}<small class="d-block text-muted">{{ $event->source }}</small></td>
                                        <td><strong>{{ $event->actor?->name ?? $event->actor_label ?? 'System' }}</strong><small class="d-block">{{ $event->action }}</small></td>
                                        <td>{{ $event->target_type }} #{{ $event->target_id ?? '—' }}<small class="d-block text-muted">{{ $event->target_label }} @if($event->employee_number)· {{ $event->employee_number }}@endif</small></td>
                                        <td class="audit-reason">{{ $event->reason ?: '—' }}</td>
                                        <td>
                                            <details><summary>View redacted diff</summary><div class="audit-diff"><strong>Before</strong><pre>{{ json_encode($event->previous_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre><strong>After</strong><pre>{{ json_encode($event->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></div></details>
                                        </td>
                                        <td><code>{{ $event->correlation_id }}</code></td>
                                        <td>
                                            @if ($event->is_held)<span class="audit-hold-badge">Held</span>@endif
                                            @if (auth()->user()->isSuperAdmin())
                                                <form class="audit-hold-form" method="POST" action="{{ route('admin.audit.hold', $event) }}">
                                                    @csrf
                                                    <input type="hidden" name="is_held" value="{{ $event->is_held ? 0 : 1 }}">
                                                    <input class="form-control form-control-sm" name="change_reason" maxlength="500" placeholder="Hold reason" required>
                                                    <button class="btn btn-sm btn-outline-secondary" type="submit">{{ $event->is_held ? 'Release' : 'Hold' }}</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="audit-pagination">{{ $events->links() }}</div>
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
