<?php

namespace App\Http\Controllers;

use App\Models\CardInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function userDashboard()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Fetch related card_info records using the employee_number
        $cardInfoss = CardInfo::where('emp_num', operator: $user->employee_number)->get();

        // Return the view with the user and their card information
        return view('user.dashboard', compact('user', 'cardInfoss'));
    }
    public function warning()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Fetch related card_info records using the employee_number
        $cardInfoss = CardInfo::where('emp_num', operator: $user->employee_number)->get();

        // Return the view with the user and their card information
        return view('user.warning', compact('user', 'cardInfoss'));
    }
}
