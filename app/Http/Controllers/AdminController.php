<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\CardInfo;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function all_users()
    {
        $users = User::all();
        return view('admin.sidebar.all_users')->with('users',$users);
    }
    public function pending_users()
    {
        $users = User::all();
        return view('admin.sidebar.pending_users')->with('users',$users);
    }
    public function approved_users()
    {
        $users = User::all();
        return view('admin.sidebar.approved_users')->with('users',$users);
    }
    public function rejected_users()
    {
        $users = User::all();
        return view('admin.sidebar.rejected_users')->with('users',$users);
    }
    public function getUserDetails(Request $request)
    {
        $userId = $request->input('id');
        $user = User::find($userId);

        if ($user) {
            return response()->json([
                'name' => $user->name,
                'email' => $user->email,
                'position' => $user->position,
                'date_employed' => Carbon::parse($user->date_employed)->toDateString(),
                'sex' => $user->sex,
                'date_of_birth' => Carbon::parse($user->date_of_birth)->toDateString(),
                'place_of_birth' => $user->place_of_birth,
                'employee_number' => $user->employee_number,
                'station' => $user->station,
                'civil_status' => $user->civil_status,
                'status' => $user->status,
            ]);
        }

        return response()->json(['error' => 'User not found'], 404);
    }

    public function approveUser($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 'active'; // Set the status to 'active'
            $user->save();

            return response()->json(['message' => 'User approved successfully!']);
        }

        return response()->json(['message' => 'User not found!'], 404);
    }

    public function rejectUser($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 'rejected'; // Set the status to 'rejected'
            $user->save();

            return response()->json(['message' => 'User rejected successfully!']);
        }

        return response()->json(['message' => 'User not found!'], 404);
    }
    
    public function leave_cards()
    {
        $users = User::all();
        return view('admin.sidebar.leave_cards')->with('users',$users);
    }

    public function show($employee_number)
    {
        // Fetch the user details
        $user = User::where('employee_number', $employee_number)->first();
        
        // Fetch all cardInfo for the specific user, ordered by 'id'
        $cardInfoss = CardInfo::where('emp_num', $employee_number)
                              ->orderBy('id') // Orders by 'id' ascending
                              ->get();
        
        return view('admin.sidebar.individual_lc', compact('cardInfoss', 'user'));
    }    
        
    public function update(Request $request, $id)
    {
        $cardInfo = CardInfo::findOrFail($id);
        $cardInfo->update($request->all());
        return response()->json(['success' => true, 'message' => 'Row updated successfully.']);
    }

    public function destroy($id)
    {
        $cardInfo = CardInfo::findOrFail($id);
        $cardInfo->delete();
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
            'employee_number' => 'required|exists:users,employee_number', 
        ]);
    
        try {
            // Create a new entry in the card_info table, including the user_id
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
}

