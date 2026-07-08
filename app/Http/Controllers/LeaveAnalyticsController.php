<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\PersonnelType;
use App\Services\LeaveAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveAnalyticsController extends Controller
{
    public function index(Request $request, LeaveAnalyticsService $analytics)
    {
        $leaveTypes = LeaveType::query()->where('is_active', true)->orderBy('name')->get(['code', 'name']);
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'personnel_type' => ['nullable', Rule::in([PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])],
            'leave_type' => ['nullable', Rule::in([...$leaveTypes->pluck('code')->all(), 'unclassified'])],
            'parse_state' => ['nullable', Rule::in(['parsed', 'partial', 'unparseable', 'not_applicable'])],
        ]);
        $filters = [
            'from' => $validated['from'] ?? now()->startOfYear()->toDateString(),
            'to' => $validated['to'] ?? now()->endOfYear()->toDateString(),
            'personnel_type' => $validated['personnel_type'] ?? null,
            'leave_type' => $validated['leave_type'] ?? null,
            'parse_state' => $validated['parse_state'] ?? null,
        ];

        return view('admin.leave-analytics', [
            ...$analytics->build($filters),
            'filters' => $filters,
            'leaveTypes' => $leaveTypes,
        ]);
    }
}
