<?php

namespace App\Http\Controllers;

use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;
use App\Models\CardInfo;
use App\Models\EmployeeProfile;
use App\Models\PersonnelType;
use App\Models\User;
use App\Services\AuditService;
use App\Services\AutomationService;
use App\Services\LeaveCardNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function __construct(
        private readonly LeaveCardNormalizer $normalizer,
        private readonly AutomationService $automation,
        private readonly AuditService $audit,
    ) {}

    public function all_users()
    {
        $users = User::with('employeeProfile.personnelType')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.sidebar.all_users')->with('users', $users);
    }

    public function pending_users()
    {
        $users = User::with('employeeProfile.personnelType')->get();

        return view('admin.sidebar.pending_users')->with('users', $users);
    }

    public function approved_users()
    {
        $users = User::with('employeeProfile.personnelType')
            ->where('usertype', '!=', 'admin')
            ->where('status', 'active')
            ->whereNotNull('email_verified_at')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.sidebar.approved_users')->with('users', $users);
    }

    public function rejected_users()
    {
        $users = User::with('employeeProfile.personnelType')->get();

        return view('admin.sidebar.rejected_users')->with('users', $users);
    }

    public function getUserDetails(Request $request)
    {
        $userId = $request->input('id');
        $user = User::with('employeeProfile.personnelType')->find($userId);

        if ($user) {
            $profile = $user->employeeProfile;

            return response()->json([
                'name' => $user->name,
                'email' => $user->email,
                'position' => $profile?->position,
                'date_employed' => $profile?->date_employed?->toDateString(),
                'sex' => $profile?->sex,
                'date_of_birth' => $profile?->date_of_birth?->toDateString(),
                'place_of_birth' => $profile?->place_of_birth,
                'employee_number' => $profile?->employee_number,
                'station' => $profile?->station,
                'civil_status' => $profile?->civil_status,
                'personnel_type' => $profile?->personnelType?->name ?? 'N/A',
                'status' => $user->status,
            ]);
        }

        return response()->json(['error' => 'User not found'], 404);
    }

    public function approveUser(Request $request, $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $reason = $request->validate(['decision_reason' => 'nullable|string|max:500'])['decision_reason'] ?? null;

        $notificationQueued = DB::transaction(function () use ($user, $reason) {
            $updated = User::query()
                ->whereKey($user->getKey())
                ->where('status', 'pending')
                ->update([
                    'status' => 'active',
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                    'decision_reason' => $reason,
                ]);

            if (! $updated) {
                return false;
            }

            $user->refresh();
            $this->audit->record(
                'user.approved',
                'user',
                $user->getKey(),
                $user->name,
                ['status' => 'pending'],
                [
                    'status' => $user->status,
                    'processed_by' => $user->processed_by,
                    'processed_at' => $user->processed_at?->toDateTimeString(),
                ],
                $reason,
                employeeNumber: $user->employeeProfile?->employee_number,
            );
            Mail::to($user->email)->queue(new UserApprovedMail($user));

            return true;
        });

        $user->refresh();

        return response()->json([
            'message' => $notificationQueued
                ? 'User approved successfully!'
                : 'User status was already decided.',
            'notification_queued' => $notificationQueued,
            'status' => $user->status,
        ]);
    }

    public function rejectUser(Request $request, $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $reason = $request->validate(['decision_reason' => 'required|string|max:500'])['decision_reason'];

        $notificationQueued = DB::transaction(function () use ($user, $reason) {
            $updated = User::query()
                ->whereKey($user->getKey())
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                    'decision_reason' => $reason,
                ]);

            if (! $updated) {
                return false;
            }

            $user->refresh();
            $this->audit->record(
                'user.rejected',
                'user',
                $user->getKey(),
                $user->name,
                ['status' => 'pending'],
                [
                    'status' => $user->status,
                    'processed_by' => $user->processed_by,
                    'processed_at' => $user->processed_at?->toDateTimeString(),
                ],
                $reason,
                employeeNumber: $user->employeeProfile?->employee_number,
            );
            Mail::to($user->email)->queue(new UserRejectedMail($user));

            return true;
        });

        $user->refresh();

        return response()->json([
            'message' => $notificationQueued
                ? 'User rejected successfully!'
                : 'User status was already decided.',
            'notification_queued' => $notificationQueued,
            'status' => $user->status,
        ]);
    }

    public function leave_cards()
    {
        $users = User::with('employeeProfile.personnelType')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.sidebar.leave_cards')->with('users', $users);
    }

    public function show($employee_number)
    {
        $profile = EmployeeProfile::with(['user', 'personnelType'])
            ->where('employee_number', $employee_number)
            ->firstOrFail();
        $user = $profile->user;
        $cardInfoss = $profile->leaveCardQuery()
            ->orderBy('id')
            ->get();

        return view('admin.sidebar.individual_lc', compact('cardInfoss', 'profile', 'user'));
    }

    public function update(Request $request, string $cardType, int $id)
    {
        $reason = $request->validate(['change_reason' => ['required', 'string', 'max:500']])['change_reason'];
        $modelClass = PersonnelType::leaveCardModelClass($cardType);
        $leaveCard = $modelClass::query()->findOrFail($id);
        $changes = $this->validatedLeaveCardAttributes($request, $cardType);
        DB::transaction(function () use ($leaveCard, $changes, $cardType, $reason) {
            $before = $leaveCard->getAttributes();
            $source = array_merge($before, $changes);
            $attributes = match ($cardType) {
                PersonnelType::CODE_TEACHING => $this->normalizer->teaching($source),
                PersonnelType::CODE_NON_TEACHING => $this->normalizer->nonTeaching($source),
            };
            $leaveCard->update($attributes);
            $diff = $this->audit->changedValues($before, $leaveCard->fresh()->getAttributes());
            $this->audit->record(
                'leave_card.updated',
                class_basename($leaveCard),
                $leaveCard->getKey(),
                'Leave-card row '.$leaveCard->getKey(),
                $diff['before'],
                $diff['after'],
                $reason,
                employeeNumber: $leaveCard->employeeProfile?->employee_number,
            );
        });
        $this->automation->notifyEmployeeChange(
            $leaveCard->employeeProfile()->with('user')->firstOrFail(),
            'An administrator updated a row on your leave card.',
        );

        return response()->json(['success' => true, 'message' => 'Row updated successfully.']);
    }

    public function destroy(Request $request, string $cardType, int $id)
    {
        $reason = $request->validate(['audit_reason' => ['required', 'string', 'max:500']])['audit_reason'];
        $modelClass = PersonnelType::leaveCardModelClass($cardType);
        $leaveCard = $modelClass::query()->with('employeeProfile.user')->findOrFail($id);
        $profile = $leaveCard->employeeProfile;
        DB::transaction(function () use ($leaveCard, $profile, $reason) {
            $snapshot = $leaveCard->getAttributes();
            $leaveCard->delete();
            $this->audit->record(
                'leave_card.deleted',
                class_basename($leaveCard),
                $leaveCard->getKey(),
                'Leave-card row '.$leaveCard->getKey(),
                $snapshot,
                [],
                $reason,
                employeeNumber: $profile->employee_number,
            );
        });
        $this->automation->notifyEmployeeChange(
            $profile,
            'An administrator removed a row from your leave card.',
        );

        return response()->json(['success' => true, 'message' => 'Row deleted successfully.']);
    }

    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'inclusive_period' => 'nullable|string|max:255',
            'nature_of_activity' => 'nullable|string|max:255',
            'no_of_days_credited' => 'nullable|integer|min:0',
            'dso_no_vsr' => 'nullable|string|max:255',
            'inclusive_dates' => 'nullable|string|max:255',
            'no_days_leave' => 'nullable|integer|min:0',
            'service_cred_bal' => 'nullable|integer|min:0',
            'leave_without_pay' => 'nullable|integer|min:0',
            'nature_of_leave' => 'nullable|string|max:255',
            'dso_no_rol' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'employee_number' => 'required|exists:employee_profiles,employee_number',
            'change_reason' => 'nullable|string|max:500',
        ]);

        try {
            // Create a new entry in the card_info table, including the user_id
            $profile = EmployeeProfile::query()
                ->with('user')
                ->where('employee_number', $request->employee_number)
                ->firstOrFail();
            $cardInfo = DB::transaction(function () use ($request, $profile) {
                $cardInfo = CardInfo::create([
                    'inclusive_period' => $request->inclusive_period,
                    'nature_of_activity' => $request->nature_of_activity,
                    'no_of_days_credited' => $request->no_of_days_credited,
                    'dso_no_vsr' => $request->dso_no_vsr,
                    'inclusive_dates' => $request->inclusive_dates,
                    'no_days_leave' => $request->no_days_leave,
                    'service_cred_bal' => $request->service_cred_bal,
                    'leave_without_pay' => $request->leave_without_pay,
                    'nature_of_leave' => $request->nature_of_leave,
                    'dso_no_rol' => $request->dso_no_rol,
                    'remarks' => $request->remarks,
                    'emp_num' => $request->employee_number,
                ]);
                $this->audit->record(
                    'leave_card.created',
                    'CardInfo',
                    $cardInfo->getKey(),
                    'Leave-card row '.$cardInfo->getKey(),
                    after: $cardInfo->getAttributes(),
                    reason: $request->change_reason,
                    employeeNumber: $profile->employee_number,
                );

                return $cardInfo;
            });
            $this->automation->notifyEmployeeChange(
                $profile,
                'An administrator added a row to your leave card.',
            );

            return response()->json([
                'success' => true,
                'message' => 'Vacation service rendered added successfully!',
                'data' => $cardInfo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add vacation service rendered. Please try again.',
            ], 500);
        }
    }

    public function getRemarks(Request $request)
    {
        $cardInfoId = $request->id;

        // Fetch the record from the card_info table
        $cardInfo = CardInfo::find($cardInfoId);

        if ($cardInfo) {
            return response()->json([
                'remarks' => $cardInfo->remarks, // Replace 'remarks' with the actual field name
            ]);
        }

        return response()->json([
            'error' => 'Record not found.',
        ], 404);
    }

    private function validatedLeaveCardAttributes(Request $request, string $cardType): array
    {
        $validated = $request->validate([
            'inclusive_period' => 'nullable|string|max:255',
            'nature_of_activity' => 'nullable|string|max:255',
            'no_of_days_credited' => 'nullable|numeric|min:0',
            'dso_no_vsr' => 'nullable|string|max:255',
            'inclusive_dates' => 'nullable|string|max:255',
            'no_days_leave' => 'nullable|numeric|min:0',
            'service_cred_bal' => 'nullable|numeric|min:0',
            'leave_without_pay' => 'nullable|numeric|min:0',
            'nature_of_leave' => 'nullable|string|max:255',
            'dso_no_rol' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ]);

        $attributes = [];

        foreach ($this->leaveCardFieldMap($cardType) as $requestField => $column) {
            if (array_key_exists($requestField, $validated)) {
                $attributes[$column] = $validated[$requestField];
            }
        }

        return $attributes;
    }

    private function leaveCardFieldMap(string $cardType): array
    {
        return match ($cardType) {
            PersonnelType::CODE_TEACHING => [
                'inclusive_period' => 'inclusive_period',
                'nature_of_activity' => 'nature_of_activity',
                'no_of_days_credited' => 'days_credited',
                'dso_no_vsr' => 'vacation_service_dso_number',
                'inclusive_dates' => 'inclusive_leave_dates',
                'no_days_leave' => 'days_with_pay',
                'service_cred_bal' => 'service_credit_balance',
                'leave_without_pay' => 'days_without_pay',
                'nature_of_leave' => 'nature_of_leave',
                'dso_no_rol' => 'record_of_leave_dso_number',
                'remarks' => 'remarks',
            ],
            PersonnelType::CODE_NON_TEACHING => [
                'inclusive_period' => 'period',
                'nature_of_activity' => 'particulars',
                'no_of_days_credited' => 'vacation_leave_earned',
                'dso_no_vsr' => 'vacation_leave_with_pay',
                'inclusive_dates' => 'vacation_leave_balance',
                'no_days_leave' => 'vacation_leave_without_pay',
                'leave_without_pay' => 'sick_leave_earned',
                'service_cred_bal' => 'sick_leave_with_pay',
                'nature_of_leave' => 'sick_leave_balance',
                'dso_no_rol' => 'sick_leave_without_pay',
                'remarks' => 'leave_application_action',
            ],
        };
    }
}
