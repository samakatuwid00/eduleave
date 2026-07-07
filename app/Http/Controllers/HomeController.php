<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\PersonnelType;
use App\Services\AdminDashboardService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    public function index(Request $request, AdminDashboardService $dashboard)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'personnel_type' => ['nullable', Rule::in([
                PersonnelType::CODE_TEACHING,
                PersonnelType::CODE_NON_TEACHING,
            ])],
            'user_status' => ['nullable', Rule::in(['pending', 'active', 'rejected'])],
            'leave_type' => ['nullable', 'exists:leave_types,code'],
        ]);

        $filters = [
            'from' => $validated['from'] ?? now()->startOfYear()->toDateString(),
            'to' => $validated['to'] ?? now()->endOfYear()->toDateString(),
            'personnel_type' => $validated['personnel_type'] ?? null,
            'user_status' => $validated['user_status'] ?? null,
            'leave_type' => $validated['leave_type'] ?? null,
        ];

        $data = $dashboard->build($filters);
        $data['leaveTypes'] = LeaveType::query()->where('is_active', true)->orderBy('name')->get(['code', 'name']);

        return view('admin.index', $data);
    }

    public function home()
    {
        return view('home.index');
    }
    // public function contact()
    // {
    //     return view ('home.contactus');
    // }
}
