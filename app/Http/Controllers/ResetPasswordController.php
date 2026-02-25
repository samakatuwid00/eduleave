<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomResetPasswordMail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class ResetPasswordController extends Controller
{
    public function passwordEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    
        $status = Password::sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                $url = url('reset-password/' . $token); // Create the reset URL
                Mail::to($user->email)->send(new CustomResetPasswordMail($user, $url)); // Send the custom email
            }
        );
    
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }
    
    public function passwordReset(string $token) {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function passwordUpdate(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );
    
        if ($status === Password::PASSWORD_RESET) {
            // Redirect to login page with a success message in the URL
            return redirect()->route('login') ->with('status', 'Password Reset Successfully! Try Logging In!');
        } else {
            return back()->withErrors(['email' => [__($status)]]);
        }
    }    

    public function verifyNotice() {
        return view('auth.verify-email')->with(session()->flash('successfully_sent', 'Verification Successfully Sent'));
    }    

    public function verifyEmail(EmailVerificationRequest $request) {
        $request->fulfill();
    
        return redirect('/user/dashboard/warning')->with('welcome_message', 'Welcome To Your Dashboard!');
    }
     
    public function verifySend(Request $request) {
        $request->user()->sendEmailVerificationNotification();
     
        return back()->with('message', 'Verification link sent!');
    }
    
    public function sendApprovalEmail($userId)
    {
        $user = User::findOrFail($userId);

        // Send email notification
        Mail::to($user->email)->send(new UserApprovedMail($user));

        return response()->json(['message' => 'Approval email sent successfully!']);
    }

    public function sendRejectionEmail($userId)
    {
        $user = User::findOrFail($userId);

        // Send email notification
        Mail::to($user->email)->send(new UserRejectedMail($user));

        return response()->json(['message' => 'Rejection email sent successfully!']);
    }
}
