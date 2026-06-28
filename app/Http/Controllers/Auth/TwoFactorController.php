<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    // ── Show the 2FA code entry page ───────────────────
    public function showChallenge()
    {
        if (!session('2fa:user_id')) {
            return redirect('/login')->withErrors(['email' => 'Your session expired. Please sign in again.']);
        }
        $user   = User::findOrFail(session('2fa:user_id'));
        $method = $user->two_factor_secret ? 'totp' : 'email';
        return view('auth.two-factor', compact('method'));
    }

    // ── Verify the submitted code ──────────────────────
    public function verify(Request $request)
    {
        // Guard: session may be lost (stale cookie, mobile browser, etc.)
        if (!session('2fa:user_id')) {
            Log::warning('2FA verify: session 2fa:user_id missing', [
                'ip' => $request->ip(),
                'session_id' => session()->getId(),
            ]);
            return redirect('/login')
                ->withErrors(['email' => 'Your session expired. Please sign in again.']);
        }

        // Sanitize: strip spaces, dashes, and any non-digit character
        // (mobile keyboards sometimes inject spaces or formatting)
        $code = preg_replace('/\D/', '', trim($request->input('code', '')));

        $request->merge(['code' => $code]);
        $request->validate(['code' => 'required|digits:6']);

        $user = User::findOrFail(session('2fa:user_id'));

        // 1. Check email code first (if one was sent)
        $isEmailValid = $user->two_factor_email_code !== null
            && $user->two_factor_email_code === $code
            && $user->two_factor_code_expires_at
            && now()->lessThan($user->two_factor_code_expires_at);

        if ($isEmailValid) {
            $valid = true;
        } elseif ($user->two_factor_secret) {
            // 2. Check TOTP code — window=8 allows ±4 min clock drift
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($user->two_factor_secret, $code, 8);

            if (!$valid) {
                Log::warning('2FA TOTP verify failed', [
                    'user_id' => $user->id,
                    'code_length' => strlen($code),
                    'has_secret' => !empty($user->two_factor_secret),
                ]);
            }
        } else {
            $valid = false;
            Log::warning('2FA verify: no secret and no email code', ['user_id' => $user->id]);
        }

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid or expired code. Please try again.']);
        }

        // Clean up and fully log in
        $user->update([
            'two_factor_email_code'      => null,
            'two_factor_code_expires_at' => null,
        ]);

        session()->forget('2fa:user_id');
        Auth::login($user);
        session(['2fa:verified' => true]);

        Log::info('2FA verified successfully', ['user_id' => $user->id, 'role' => $user->role]);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($user->role === 'office') {
            return redirect()->route('office.dashboard');
        }
        return redirect()->route('home');
    }

    // ── Resend email code ──────────────────────────────
    public function resend()
    {
        try {
            $userId = session('2fa:user_id');
            if (!$userId) {
                return redirect()->route('login');
            }

            $user = User::findOrFail($userId);
            self::sendEmailCode($user);

            return redirect()->route('2fa.challenge')
                ->with('status', 'A verification code has been sent to ' . $user->email);

        } catch (\Exception $e) {
            Log::error('2FA resend error: ' . $e->getMessage());
            return redirect()->route('2fa.challenge')
                ->withErrors(['code' => 'Could not send email: ' . $e->getMessage()]);
        }
    }

    // ── Static helper: generate and email a code ───────
    public static function sendEmailCode(User $user): void
    {
        // 6-digit code, zero-padded
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'two_factor_email_code'      => $code,
            'two_factor_code_expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw(
            "Your verification code is: {$code}\n\nThis code expires in 10 minutes.\n\nIf you did not request this, please ignore this email.",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Login Verification Code');
            }
        );
    }
}
