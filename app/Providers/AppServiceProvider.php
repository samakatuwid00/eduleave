<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('registration', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            $emailKey = $email !== '' ? sha1($email) : $request->ip();

            return [
                Limit::perMinutes(10, 3)->by('registration-ip:'.$request->ip()),
                Limit::perHour(2)->by('registration-email:'.$emailKey),
            ];
        });

        RateLimiter::for('password-reset', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            $emailKey = $email !== '' ? sha1($email) : $request->ip();

            return [
                Limit::perMinutes(10, 5)->by('password-reset-ip:'.$request->ip()),
                Limit::perMinutes(10, 2)->by('password-reset-email:'.$emailKey),
            ];
        });

        RateLimiter::for('verification-email', function (Request $request) {
            $userKey = $request->user()?->getAuthIdentifier() ?? $request->ip();

            return [
                Limit::perMinute(1)->by('verification-minute:'.$userKey),
                Limit::perHour(3)->by('verification-hour:'.$userKey),
            ];
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->view('auth.verification', [
                    'user' => $notifiable,
                    'url' => $url,
                ]);
        });
    }
}
