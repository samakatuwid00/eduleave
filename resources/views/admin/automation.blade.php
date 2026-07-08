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
        <div class="content container-fluid automation-page">
            <div class="page-header automation-heading">
                <div>
                    <h3 class="page-title mb-1"><strong>Automation</strong></h3>
                    <p class="text-muted mb-0">Configure safe summaries and monitor scheduled runs.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <div class="row">
                <div class="col-xl-7">
                    <section class="card automation-settings-card">
                        <div class="card-header"><h4>Settings</h4></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.automation.update') }}">
                                @csrf
                                @method('PUT')

                                <label class="automation-switch-row">
                                    <span><strong>Enable automation</strong><small>Master switch for scheduled rules and employee notices.</small></span>
                                    <input type="checkbox" name="automation_enabled" value="1" @checked($settings->automation_enabled)>
                                </label>
                                <label class="automation-switch-row">
                                    <span><strong>Daily admin digest</strong><small>Sends the current Action Center totals each morning.</small></span>
                                    <input type="checkbox" name="daily_digest_enabled" value="1" @checked($settings->daily_digest_enabled)>
                                </label>
                                <label class="automation-switch-row">
                                    <span><strong>Weekly admin summary</strong><small>Sends a Monday summary of open administrative issues.</small></span>
                                    <input type="checkbox" name="weekly_summary_enabled" value="1" @checked($settings->weekly_summary_enabled)>
                                </label>
                                <label class="automation-switch-row">
                                    <span><strong>Employee leave-card notices</strong><small>One email per manual update or completed import, disabled by default.</small></span>
                                    <input type="checkbox" name="employee_notifications_enabled" value="1" @checked($settings->employee_notifications_enabled)>
                                </label>

                                <div class="form-group mt-3">
                                    <label for="automation_recipients"><strong>Admin recipients</strong></label>
                                    <textarea class="form-control" id="automation_recipients" name="recipient_emails" rows="3" placeholder="admin@example.com, records@example.com">{{ old('recipient_emails', implode(', ', $settings->recipient_emails ?? [])) }}</textarea>
                                    <small class="form-text text-muted">Separate addresses with commas or new lines. Leave blank to use all admin accounts.</small>
                                </div>

                                <div class="form-group">
                                    <label for="automation_change_reason"><strong>Reason for this settings change</strong></label>
                                    <input class="form-control" id="automation_change_reason" name="change_reason" maxlength="500" required placeholder="Explain why these automation settings are changing">
                                </div>

                                <button class="btn btn-primary" type="submit">Save settings</button>
                                <small class="ml-2 text-muted">Settings version {{ $settings->version }}</small>
                            </form>
                        </div>
                    </section>
                </div>
                <div class="col-xl-5">
                    <section class="card automation-schedule-card">
                        <div class="card-header"><h4>Schedule</h4></div>
                        <div class="card-body">
                            <dl class="automation-schedule-list">
                                <div><dt>Alert evaluation</dt><dd>Daily at {{ config('automation.evaluation_time') }}</dd></div>
                                <div><dt>Admin digest</dt><dd>Daily at {{ config('automation.daily_digest_time') }}</dd></div>
                                <div><dt>Weekly summary</dt><dd>Monday at {{ config('automation.weekly_summary_time') }}</dd></div>
                                <div><dt>Timezone</dt><dd>{{ config('automation.timezone') }}</dd></div>
                            </dl>
                            <p class="text-muted small">Times are deployment configuration. Every rule has overlap and duplicate protection.</p>

                            <form class="automation-run-buttons" method="POST" action="{{ route('admin.automation.run') }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary" name="rule" value="action_center_evaluation" type="submit">Evaluate now</button>
                                <button class="btn btn-sm btn-outline-primary" name="rule" value="daily_admin_digest" type="submit">Send digest now</button>
                            </form>
                        </div>
                    </section>
                </div>
            </div>

            <section class="card automation-runs-card">
                <div class="card-header">
                    <h4>Run history</h4>
                    <small>Completed means the notification was safely handed to the bounded mail queue.</small>
                </div>
                <div class="card-body p-0">
                    @if ($runs->isEmpty())
                        <div class="automation-empty">No automation runs have been recorded.</div>
                    @else
                        <div class="table-responsive">
                            <table id="automationRunsTable" class="display automation-runs-table" style="width: 100%">
                                <thead>
                                <tr><th>Started</th><th>Rule</th><th>Window</th><th>Items</th><th>Recipients</th><th>Attempt</th><th>Status</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                @foreach ($runs as $run)
                                    <tr>
                                        <td data-order="{{ $run->started_at?->timestamp ?? 0 }}">{{ $run->started_at?->format('M j, Y g:i A') ?? '—' }}</td>
                                        <td>{{ str_replace('_', ' ', ucfirst($run->rule_code)) }}</td>
                                        <td>{{ $run->window_key }}</td>
                                        <td>{{ $run->item_count }}</td>
                                        <td>{{ $run->audience_count }}</td>
                                        <td>{{ $run->attempt }}</td>
                                        <td>
                                            <span class="automation-status automation-status-{{ $run->status }}">{{ ucfirst($run->status) }}</span>
                                            @if ($run->error_summary)<small class="d-block text-muted mt-1">{{ $run->error_summary }}</small>@endif
                                        </td>
                                        <td>
                                            @if ($run->status === 'failed')
                                                <form method="POST" action="{{ route('admin.automation.retry', $run) }}">
                                                    @csrf
                                                    <input class="form-control form-control-sm mb-1" name="audit_reason" maxlength="500" required placeholder="Retry reason">
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">Retry</button>
                                                </form>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>

            @if ($failedJobs->isNotEmpty())
                <section class="card automation-failed-card">
                    <div class="card-header"><h4>Terminal queue failures</h4></div>
                    <div class="card-body">
                        <p class="mb-2">{{ $failedJobs->count() }} recent queued job(s) exhausted their retry limit.</p>
                        <small class="text-muted">Technical payloads and message bodies are intentionally not displayed here. Resolve the underlying mail/queue issue, then use Laravel's queue retry command.</small>
                    </div>
                </section>
            @endif
        </div>
    </div>

    @include('admin.loader')
    @include('admin.footer')
</div>
</body>
</html>
