<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Throwable;

class Turnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! config('services.turnstile.enabled')) {
            return;
        }

        $secret = config('services.turnstile.secret_key');

        if (blank($secret)) {
            $fail('Registration verification is not configured.');

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => (string) $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (Throwable) {
            $fail('Registration verification could not be completed. Please try again.');

            return;
        }

        if (! $response->successful() || ! $response->json('success')) {
            $fail('Please complete the registration verification and try again.');
        }
    }
}
