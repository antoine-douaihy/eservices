<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Blocks every authenticated request — except the set-password screen
 * itself and logout — until a staff member who logged in with their
 * emailed temporary password has chosen a permanent one.
 */
class ForcePasswordChange
{
    private const ALLOWED_ROUTES = [
        'first-login.set-password',
        'first-login.set-password.store',
        'logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->requires_first_login_otp && !in_array($request->route()?->getName(), self::ALLOWED_ROUTES, true)) {
            return redirect()->route('first-login.set-password');
        }

        return $next($request);
    }
}
