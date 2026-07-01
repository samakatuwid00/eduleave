<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the incoming request data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'date_employed' => ['required', 'date'],
            'sex' => ['required', 'in:Male,Female'],
            'date_of_birth' => ['required', 'date'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'employee_number' => ['required', 'string', 'unique:users,employee_number', 'max:255'],
            'station' => ['required', 'string', 'max:255'],
            'civil_status' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
    
        // Create the user with the validated data
        $user = User::create([
            'name' => $request->name,
            'position' => $request->position,
            'date_employed' => $request->date_employed,
            'sex' => $request->sex,
            'date_of_birth' => $request->date_of_birth,
            'place_of_birth' => $request->place_of_birth,
            'employee_number' => $request->employee_number,
            'station' => $request->station,
            'civil_status' => $request->civil_status,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
    
        // Trigger the Registered event
        event(new Registered($user));
    
        // Log in the user
        Auth::login($user);
    
        // Redirect to the dashboard or a different route as required
        return redirect(route('/user/dashboard/warning'));
    }
    public function registerAgain($userId)
    {
    // Retrieve the user by ID from the session
    $user = User::findOrFail($userId);

    // Perform any logic to update the user's information
    // Example: return to the registration form with prefilled data
    return view('auth.register', compact('user'));
    }

}
