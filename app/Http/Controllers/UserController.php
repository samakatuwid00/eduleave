<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function userDashboard()
    {
        $user = Auth::user()->loadMissing('employeeProfile.personnelType');
        $profile = $user->employeeProfile;

        abort_if($profile === null, 404, 'Employee profile not found.');

        $cardInfoss = $profile->leaveCardQuery()
            ->orderBy('id')
            ->get();

        return view('user.dashboard', compact('user', 'profile', 'cardInfoss'));
    }

    public function warning()
    {
        $user = Auth::user()->loadMissing('employeeProfile.personnelType');
        $profile = $user->employeeProfile;

        abort_if($profile === null, 404, 'Employee profile not found.');

        return view('user.warning', compact('user', 'profile'));
    }
}
