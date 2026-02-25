<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
    
        $loginStatus = $this->attemptLogin($request);
    
        if ($loginStatus !== true) {
            $errorMessages = [
                'email_not_found' => 'The email address does not exist.',
                'account_rejected' => 'Your account was rejected. Contact support for assistance.',
                'invalid_credentials' => 'The provided credentials are incorrect.',
            ];
    
            return back()->withErrors([
                'email' => $errorMessages[$loginStatus] ?? 'Login failed. Please try again.',
            ]);
        }
    
        $request->session()->regenerate();
    
        // Set the session variable after successful login
        session(['login_success' => true]);
    
        if ($request->user()->usertype === 'admin') {
            return redirect('admin/dashboard');
        }
    
        if ($request->user()->status === 'pending') {
            return redirect('/user/dashboard/warning');
        }
    
        return redirect()->intended(route('user/dashboard'));
    }
    
        
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
    
        // Set the logout_success session variable
        session(['logout_success' => true]);
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('welcome');  // or your homepage route
    }
    
    protected function attemptLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        // Check if the user exists
        $user = \App\Models\User::where('email', $credentials['email'])->first();
    
        if (!$user) {
            // If the email doesn't exist, return an error message
            return 'email_not_found';
        }
    
        if ($user->status === 'rejected') {
            // If the user is not approved, return a specific error message
            return 'account_rejected';
        }
    
        // Attempt login
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // If the password is incorrect
            return 'invalid_credentials';
        }
    
        return true;
    }
    



}
