<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PersonnelType;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'date_employed' => ['required', 'date'],
            'sex' => ['required', 'in:Male,Female'],
            'date_of_birth' => ['required', 'date'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'employee_number' => ['required', 'string', 'unique:employee_profiles,employee_number', 'max:255'],
            'personnel' => ['required', 'in:Teaching,Non-Teaching'],
            'station' => ['required', 'string', 'max:255'],
            'civil_status' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $personnelType = PersonnelType::query()
            ->where('name', $validated['personnel'])
            ->firstOrFail();

        $user = DB::transaction(function () use ($validated, $personnelType) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->employeeProfile()->create([
                'employee_number' => $validated['employee_number'],
                'personnel_type_id' => $personnelType->getKey(),
                'position' => $validated['position'],
                'date_employed' => $validated['date_employed'],
                'sex' => $validated['sex'],
                'date_of_birth' => $validated['date_of_birth'],
                'place_of_birth' => $validated['place_of_birth'],
                'station' => $validated['station'],
                'civil_status' => $validated['civil_status'],
            ]);

            return $user;
        });

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
