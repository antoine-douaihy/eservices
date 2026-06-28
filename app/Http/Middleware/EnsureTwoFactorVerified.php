<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTwoFactorVerified
{
    private const ALLOWED = [
        '2fa.challenge', '2fa.verify', '2fa.resend',
        '2fa.setup', '2fa.confirm',
        'logout',
        'first-login.set-password', 'first-login.set-password.store',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Not logged in, or 2FA not enabled — let through
        if (!$user || !$user->two_factor_enabled) {
            return $next($request);
        }

        // Always allow 2FA-related routes
        if (in_array($request->route()?->getName(), self::ALLOWED, true)) {
            return $next($request);
        }

        // Already verified this session
        if (session('2fa:verified')) {
            return $next($request);
        }

        // User is authenticated (e.g. via remember cookie) but hasn't done 2FA this session
        $userId = $user->id;
        Auth::logout();
        session(['2fa:user_id' => $userId]);
        return redirect()->route('2fa.challenge');
    }
}
