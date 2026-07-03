<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            return view('auth.verify-email', [
                'cooldownSeconds' => RateLimiter::availableIn($user->verificationEmailCooldownKey()),
            ]);
        }

        $request->session()->forget('url.intended');

        return redirect()->to(route($user->dashboardRouteName(), absolute: false));
    }
}
