<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure user is authenticated before accessing Auth::user()
        if (Auth::check() && (Auth::user()->usertype != 'user' || Auth::user()->status != 'active')) {
            // Redirect to 'welcome' if the user is not a 'user' or not active
            return redirect('welcome');
        }

        return $next($request);
    }
}
