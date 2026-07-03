<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            $request->session()->forget('url.intended');

            return redirect()->to(route($user->dashboardRouteName(), absolute: false));
        }

        $cooldownKey = $user->verificationEmailCooldownKey();

        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            abort(429, 'Please wait before requesting another verification email.', [
                'Retry-After' => RateLimiter::availableIn($cooldownKey),
            ]);
        }

        $user->sendEmailVerificationNotification();
        RateLimiter::hit($cooldownKey, 60);

        return back()->with('status', 'verification-link-sent');
    }
}
