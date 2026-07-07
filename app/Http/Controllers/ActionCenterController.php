<?php

namespace App\Http\Controllers;

use App\Models\PersonnelType;
use App\Services\ActionCenterService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActionCenterController extends Controller
{
    public function index(Request $request, ActionCenterService $actionCenter)
    {
        $filters = $request->validate([
            'category' => ['nullable', Rule::in([
                'pending_approval',
                'missing_profile',
                'missing_card',
                'low_balance',
                'data_quality',
            ])],
            'severity' => ['nullable', Rule::in(['medium', 'high', 'critical'])],
            'personnel_type' => ['nullable', Rule::in([
                PersonnelType::CODE_TEACHING,
                PersonnelType::CODE_NON_TEACHING,
            ])],
            'age_days' => ['nullable', 'integer', Rule::in([1, 3, 7])],
        ]);

        $filters += [
            'category' => null,
            'severity' => null,
            'personnel_type' => null,
            'age_days' => null,
        ];
        $data = $actionCenter->build($filters);

        return view('admin.action-center', [
            ...$data,
            'filters' => $filters,
            'actionCount' => $data['counts']['total'],
        ]);
    }
}
