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
        <div class="content container-fluid import-center-page">
            <div class="page-header import-center-heading">
                <div>
                    <h3 class="page-title mb-1"><strong>Import Center</strong></h3>
                    <p class="text-muted mb-0">Preview Excel leave-card rows before they are added.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>The request could not be completed.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="card import-upload-card">
                <div class="card-header">
                    <h4>New import</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.import-center.preview') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-lg-5 mb-3">
                                <label for="import_employee">Employee</label>
                                <select class="form-control" id="import_employee" name="employee_number" required>
                                    <option value="">Select an employee</option>
                                    @foreach ($employees as $employee)
                                        <option
                                            value="{{ $employee->employee_number }}"
                                            data-template-url="{{ route('admin.leave-card.template', [$employee->personnelType->code, $employee->employee_number]) }}"
                                            @selected(old('employee_number', $selectedEmployee) === $employee->employee_number)
                                        >
                                            {{ $employee->employee_number }} — {{ $employee->user->name }} ({{ $employee->personnelType->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="import_file">Excel workbook</label>
                                <input class="form-control" id="import_file" name="excel_file" type="file" accept=".xlsx" required>
                                <small class="form-text text-muted">XLSX only, up to 10 MB.</small>
                            </div>
                            <div class="col-lg-3 mb-3 import-upload-actions">
                                <a class="btn btn-outline-secondary disabled" id="importTemplateLink" href="#" aria-disabled="true">
                                    <i class="fas fa-download" aria-hidden="true"></i> Template
                                </a>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search" aria-hidden="true"></i> Preview
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            @if ($preview)
                @php
                    $previewRows = data_get($preview->preview_data, 'rows', []);
                    $warningCount = collect($previewRows)->sum(fn ($row) => count($row['warnings'] ?? []));
                    $canConfirm = $preview->status === 'validated'
                        && $preview->error_count === 0
                        && $preview->expires_at?->isFuture();
                @endphp
                <section class="card import-preview-card">
                    <div class="card-header import-preview-heading">
                        <div>
                            <h4>Preview: {{ $preview->original_name }}</h4>
                            <small>
                                {{ $preview->employeeProfile->employee_number }} — {{ $preview->employeeProfile->user->name }}
                                · {{ $preview->employeeProfile->personnelType->name }}
                            </small>
                        </div>
                        <span class="import-status import-status-{{ $preview->status }}">{{ str_replace('_', ' ', ucfirst($preview->status)) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="import-preview-summary">
                            <span><strong>{{ $preview->row_count }}</strong> row(s)</span>
                            <span><strong>{{ $preview->error_count }}</strong> blocking error(s)</span>
                            <span><strong>{{ $warningCount }}</strong> warning(s)</span>
                            @if ($preview->expires_at)
                                <span>Expires {{ $preview->expires_at->format('M j, Y g:i A') }}</span>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped import-preview-table">
                                <thead>
                                <tr>
                                    <th>Excel row</th>
                                    <th>Period</th>
                                    <th>Description</th>
                                    <th>Data quality</th>
                                    <th>Proposed action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($previewRows as $row)
                                    <tr>
                                        <td>{{ $row['row_number'] }}</td>
                                        <td>{{ $row['period'] ?: '—' }}</td>
                                        <td>{{ $row['description'] ?: '—' }}</td>
                                        <td>
                                            @foreach ($row['errors'] ?? [] as $error)
                                                <span class="d-block text-danger"><i class="fas fa-times-circle" aria-hidden="true"></i> {{ $error }}</span>
                                            @endforeach
                                            @foreach ($row['warnings'] ?? [] as $warning)
                                                <span class="d-block text-warning"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> {{ $warning }}</span>
                                            @endforeach
                                            @if (empty($row['errors']) && empty($row['warnings']))
                                                <span class="text-success"><i class="fas fa-check-circle" aria-hidden="true"></i> Ready</span>
                                            @endif
                                        </td>
                                        <td>{{ empty($row['errors']) ? 'Insert' : 'Blocked' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($canConfirm)
                            <form class="import-confirm-form" method="POST" action="{{ route('admin.import-center.confirm', $preview) }}">
                                @csrf
                                @if ($warningCount > 0)
                                    <label class="import-warning-check">
                                        <input type="checkbox" name="warnings_acknowledged" value="1" required>
                                        I reviewed the warnings and want to continue.
                                    </label>
                                @endif
                                <button class="btn btn-success" type="submit" onclick="return confirm('Import these rows into the employee leave card?')">
                                    Confirm import
                                </button>
                            </form>
                        @elseif ($preview->error_count > 0)
                            <div class="alert alert-warning mb-0">Fix the blocking errors in the workbook, then upload it again.</div>
                        @else
                            <div class="alert alert-warning mb-0">This preview is no longer available. Upload the workbook again.</div>
                        @endif
                    </div>
                </section>
            @endif

            <section class="card import-history-card">
                <div class="card-header">
                    <h4>Import history</h4>
                    <small>Completed, failed, expired, and rolled-back batches are retained for traceability.</small>
                </div>
                <div class="card-body p-0">
                    @if ($history->isEmpty())
                        <div class="import-history-empty">No imports have been recorded yet.</div>
                    @else
                        <div class="table-responsive">
                            <table id="importHistoryTable" class="display import-history-table" style="width: 100%">
                                <thead>
                                <tr>
                                    <th>Created</th>
                                    <th>Employee</th>
                                    <th>Format</th>
                                    <th>File</th>
                                    <th>Admin</th>
                                    <th>Rows</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($history as $batch)
                                    <tr>
                                        <td data-order="{{ $batch->created_at->timestamp }}">{{ $batch->created_at->format('M j, Y g:i A') }}</td>
                                        <td>
                                            {{ $batch->employeeProfile?->employee_number ?? 'Removed' }}
                                            <small class="d-block text-muted">{{ $batch->employeeProfile?->user?->name }}</small>
                                        </td>
                                        <td>{{ $batch->employeeProfile?->personnelType?->name ?? str_replace('_', '-', ucfirst($batch->card_type)) }}</td>
                                        <td class="import-filename" title="{{ $batch->original_name }}">{{ $batch->original_name }}</td>
                                        <td>{{ $batch->admin?->name ?? 'Removed' }}</td>
                                        <td>{{ $batch->row_count }}</td>
                                        <td>
                                            <span class="import-status import-status-{{ $batch->status }}">{{ str_replace('_', ' ', ucfirst($batch->status)) }}</span>
                                            @if ($batch->status === 'failed' && data_get($batch->preview_data, 'errors.0'))
                                                <small class="d-block text-danger mt-1">{{ data_get($batch->preview_data, 'errors.0') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($batch->status === 'completed' && $batch->admin_user_id === auth()->id())
                                                <form class="import-rollback-form" method="POST" action="{{ route('admin.import-center.rollback', $batch) }}" onsubmit="return confirm('Roll back this import? Its imported rows will be deleted.')">
                                                    @csrf
                                                    <input class="form-control form-control-sm" name="rollback_reason" type="text" maxlength="500" placeholder="Rollback reason" required aria-label="Rollback reason">
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">Rollback</button>
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
        </div>
    </div>

    @include('admin.loader')
    @include('admin.footer')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const employee = document.getElementById('import_employee');
        const templateLink = document.getElementById('importTemplateLink');

        function updateTemplateLink() {
            const option = employee.options[employee.selectedIndex];
            const url = option ? option.dataset.templateUrl : '';
            templateLink.href = url || '#';
            templateLink.classList.toggle('disabled', !url);
            templateLink.setAttribute('aria-disabled', url ? 'false' : 'true');
        }

        employee.addEventListener('change', updateTemplateLink);
        updateTemplateLink();
    });
</script>
</body>
</html>
