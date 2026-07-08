<?php

namespace App\Http\Controllers;

use App\Models\EmployeeProfile;
use App\Models\ImportBatch;
use App\Services\AutomationService;
use App\Services\LeaveCardImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportCenterController extends Controller
{
    public function index(Request $request): View
    {
        $employees = EmployeeProfile::query()
            ->with(['user', 'personnelType'])
            ->whereHas('user', fn ($query) => $query->where('usertype', '!=', 'admin'))
            ->orderBy('employee_number')
            ->get();
        $selectedEmployee = $request->string('employee')->toString();
        $preview = null;

        if ($request->filled('batch')) {
            $preview = ImportBatch::query()
                ->with(['employeeProfile.user', 'employeeProfile.personnelType'])
                ->where('admin_user_id', $request->user()->getKey())
                ->findOrFail($request->string('batch')->toString());

            if ($preview->status === 'validated' && $preview->expires_at?->isPast()) {
                $preview->update(['status' => 'expired']);
            }
            $selectedEmployee = $preview->employeeProfile?->employee_number ?? $selectedEmployee;
        }

        $history = ImportBatch::query()
            ->with(['admin', 'employeeProfile.user', 'employeeProfile.personnelType'])
            ->latest()
            ->limit(250)
            ->get();

        return view('admin.import-center', compact('employees', 'selectedEmployee', 'preview', 'history'));
    }

    public function preview(Request $request, LeaveCardImportService $imports): RedirectResponse
    {
        $validated = $request->validate([
            'employee_number' => ['required', 'exists:employee_profiles,employee_number'],
            'excel_file' => ['required', 'file', 'extensions:xlsx', 'max:10240'],
        ]);
        $profile = $imports->profile($validated['employee_number']);
        $batch = $imports->createPreview($request->file('excel_file'), $profile, $request->user());

        return redirect()->route('admin.import-center', ['batch' => $batch->getKey()]);
    }

    public function confirm(
        Request $request,
        ImportBatch $batch,
        LeaveCardImportService $imports,
        AutomationService $automation,
    ): RedirectResponse {
        $imports->authorizeBatch($batch, $request->user());
        $hasWarnings = collect(data_get($batch->preview_data, 'rows', []))
            ->contains(fn (array $row) => ! empty($row['warnings']));

        if ($hasWarnings) {
            $request->validate([
                'warnings_acknowledged' => ['accepted'],
            ], [
                'warnings_acknowledged.accepted' => 'Please acknowledge the preview warnings before importing.',
            ]);
        }

        $batch = $imports->confirm($batch, $request->user());
        $automation->notifyEmployeeChange(
            $batch->employeeProfile()->with('user')->firstOrFail(),
            $batch->row_count.' leave-card row(s) were imported by an administrator.',
        );

        return redirect()->route('admin.import-center')
            ->with('success', $batch->row_count.' leave-card row(s) imported successfully.');
    }

    public function rollback(Request $request, ImportBatch $batch, LeaveCardImportService $imports): RedirectResponse
    {
        $imports->authorizeBatch($batch, $request->user());
        $validated = $request->validate([
            'rollback_reason' => ['required', 'string', 'max:500'],
        ]);
        $imports->rollback($batch, $request->user(), $validated['rollback_reason']);

        return redirect()->route('admin.import-center')->with('success', 'Import rolled back successfully.');
    }
}
