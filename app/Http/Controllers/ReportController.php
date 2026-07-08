<?php

namespace App\Http\Controllers;

use App\Models\EmployeeProfile;
use App\Models\LeaveType;
use App\Models\PersonnelType;
use App\Services\AuditService;
use App\Services\ReportExcelService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\HeaderUtils;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports): View
    {
        [$code, $filters] = $this->validatedSelection($request, $reports);

        return view('admin.reports', [
            'registry' => $reports->registry(),
            'report' => $reports->build($code, $filters),
            'filters' => $filters,
            'employees' => EmployeeProfile::query()->with('user')->orderBy('employee_number')->get(),
            'leaveTypes' => LeaveType::query()->where('is_active', true)->orderBy('name')->get(['code', 'name']),
        ]);
    }

    public function export(
        Request $request,
        string $report,
        ReportService $reports,
        ReportExcelService $excel,
        AuditService $audit,
    ): Response {
        $request->merge(['report' => $report]);
        [$code, $filters] = $this->validatedSelection($request, $reports);
        $data = $reports->build($code, $filters);
        $filename = Str::slug($data['label']).'-'.now()->format('Y-m-d-His').'.xlsx';
        $content = $excel->generate($data);
        $audit->record(
            'report.exported',
            'report',
            $code,
            $data['label'],
            after: ['format' => 'xlsx', 'row_count' => $data['row_count']],
            metadata: ['filters' => $data['filters_applied'], 'file_name' => $filename],
            employeeNumber: $filters['employee_number'],
        );

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $filename),
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    private function validatedSelection(Request $request, ReportService $reports): array
    {
        $leaveTypes = LeaveType::query()->where('is_active', true)->pluck('code')->all();
        $validated = $request->validate([
            'report' => ['nullable', Rule::in(array_keys($reports->registry()))],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'personnel_type' => ['nullable', Rule::in([PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])],
            'leave_type' => ['nullable', Rule::in([...$leaveTypes, 'unclassified'])],
            'parse_state' => ['nullable', Rule::in(['parsed', 'partial', 'unparseable', 'not_applicable'])],
            'employee_number' => [
                'nullable',
                Rule::requiredIf(fn () => $request->input('report') === 'employee_ledger'),
                'exists:employee_profiles,employee_number',
            ],
        ]);
        $code = $validated['report'] ?? 'monthly_summary';
        $filters = [
            'from' => $validated['from'] ?? now()->startOfYear()->toDateString(),
            'to' => $validated['to'] ?? now()->endOfYear()->toDateString(),
            'personnel_type' => $validated['personnel_type'] ?? null,
            'leave_type' => $validated['leave_type'] ?? null,
            'parse_state' => $validated['parse_state'] ?? null,
            'employee_number' => $validated['employee_number'] ?? null,
        ];

        return [$code, $filters];
    }
}
