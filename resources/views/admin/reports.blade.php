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
        <div class="content container-fluid reports-page">
            <div class="page-header reports-heading">
                <div>
                    <h3 class="page-title mb-1"><strong>Reports and Exports</strong></h3>
                    <p class="text-muted mb-0">Preview authoritative administrative datasets before exporting them to Excel.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form class="card reports-filter-card" method="GET" action="{{ route('admin.reports') }}">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-lg-4 mb-3">
                            <label for="report_code">Report</label>
                            <select class="form-control" id="report_code" name="report">
                                @foreach ($registry as $code => $definition)
                                    <option value="{{ $code }}" @selected($report['code'] === $code)>{{ $definition['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-3">
                            <label for="report_from">From</label>
                            <input class="form-control" id="report_from" type="date" name="from" value="{{ $filters['from'] }}">
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-3">
                            <label for="report_to">To</label>
                            <input class="form-control" id="report_to" type="date" name="to" value="{{ $filters['to'] }}">
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-3">
                            <label for="report_personnel">Personnel</label>
                            <select class="form-control" id="report_personnel" name="personnel_type">
                                <option value="">All personnel</option>
                                <option value="teaching" @selected($filters['personnel_type'] === 'teaching')>Teaching</option>
                                <option value="non_teaching" @selected($filters['personnel_type'] === 'non_teaching')>Non-Teaching</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-3">
                            <label for="report_leave_type">Leave type</label>
                            <select class="form-control" id="report_leave_type" name="leave_type">
                                <option value="">All leave types</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->code }}" @selected($filters['leave_type'] === $leaveType->code)>{{ $leaveType->name }}</option>
                                @endforeach
                                <option value="unclassified" @selected($filters['leave_type'] === 'unclassified')>Unclassified</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-3">
                            <label for="report_employee">Employee <small>(ledger/import only)</small></label>
                            <select class="form-control" id="report_employee" name="employee_number">
                                <option value="">All employees</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->employee_number }}" @selected($filters['employee_number'] === $employee->employee_number)>
                                        {{ $employee->employee_number }} — {{ $employee->user?->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-3">
                            <label for="report_state">Data state</label>
                            <select class="form-control" id="report_state" name="parse_state">
                                <option value="">All states</option>
                                <option value="parsed" @selected($filters['parse_state'] === 'parsed')>Parsed</option>
                                <option value="partial" @selected($filters['parse_state'] === 'partial')>Partial</option>
                                <option value="unparseable" @selected($filters['parse_state'] === 'unparseable')>Unparseable</option>
                                <option value="not_applicable" @selected($filters['parse_state'] === 'not_applicable')>Not applicable</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-lg-3 mb-3 reports-filter-actions">
                            <button class="btn btn-primary" type="submit">Preview report</button>
                            <a class="btn btn-light" href="{{ route('admin.reports') }}">Reset</a>
                        </div>
                    </div>
                </div>
            </form>

            <section class="card report-preview-card">
                <div class="card-header report-preview-heading">
                    <div>
                        <h4>{{ $report['label'] }}</h4>
                        <small>{{ $report['description'] }}</small>
                    </div>
                    <a class="btn btn-success" href="{{ route('admin.reports.export', ['report' => $report['code'], ...$filters]) }}">
                        <i class="fas fa-file-excel" aria-hidden="true"></i> Export Excel
                    </a>
                </div>
                <div class="card-body report-metadata">
                    <p class="mb-2"><strong>Definition:</strong> {{ $report['definition'] }}</p>
                    <div class="report-meta-items">
                        <span>Generated: <strong>{{ $report['generated_at']->format('M j, Y g:i A T') }}</strong></span>
                        <span>Matched rows: <strong>{{ $report['row_count'] }}</strong></span>
                        <span>Excluded data: <strong>{{ $report['excluded_count'] }}</strong></span>
                    </div>
                    @if ($report['filters_applied'])
                        <div class="report-filter-badges">
                            @foreach ($report['filters_applied'] as $key => $value)
                                <span>{{ str_replace('_', ' ', ucfirst($key)) }}: {{ $value }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if ($report['totals'])
                        <div class="report-totals">
                            @foreach ($report['totals'] as $label => $value)
                                <div><small>{{ $label }}</small><strong>{{ is_numeric($value) ? number_format((float) $value, 2) : $value }}</strong></div>
                            @endforeach
                        </div>
                    @endif
                    @if ($report['truncated_count'] > 0)
                        <div class="alert alert-warning mt-3 mb-0">{{ $report['truncated_count'] }} row(s) exceed the synchronous export limit and are omitted.</div>
                    @endif
                </div>

                <div class="card-body p-0">
                    @if ($report['rows']->isEmpty())
                        <div class="reports-empty">No records matched the selected report and filters.</div>
                    @else
                        <div class="table-responsive">
                            <table id="reportsPreviewTable" class="display reports-preview-table" style="width: 100%">
                                <thead>
                                <tr>
                                    @foreach ($report['columns'] as $column)<th>{{ $column['label'] }}</th>@endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($report['rows']->take(250) as $row)
                                    <tr>
                                        @foreach ($report['columns'] as $column)
                                            @php($value = $row[$column['key']] ?? null)
                                            <td data-order="{{ $value }}">
                                                @if ($value === null || $value === '')
                                                    —
                                                @elseif ($column['type'] === 'number')
                                                    {{ number_format((float) $value, 2) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($report['rows']->count() > 250)
                            <p class="text-muted small p-3 mb-0">Preview shows the first 250 rows. Excel includes up to {{ config('reports.max_sync_rows') }} rows.</p>
                        @endif
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
