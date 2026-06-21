<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    // ── Show the 2FA code entry page ───────────────────
    public function showChallenge()
    {
        if (!session('2fa:user_id')) {
            return redirect('/login');
        }
        $user   = User::findOrFail(session('2fa:user_id'));
        $method = $user->two_factor_secret ? 'totp' : 'email';
        return view('auth.two-factor', compact('method'));
    }

    // ── Verify the submitted code ──────────────────────
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|min:6|max:6']);
        $user = User::findOrFail(session('2fa:user_id'));

        // 1. First, check if the code matches the Email Code (if one was sent)
        $isEmailValid = $user->two_factor_email_code === $request->code
            && $user->two_factor_code_expires_at
            && now()->lessThan($user->two_factor_code_expires_at);

        if ($isEmailValid) {
            $valid = true;
        }
        // 2. If it's not an email code, check if it's a TOTP code (if they have a secret)
        elseif ($user->two_factor_secret) {
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);
        } else {
            $valid = false;
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

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
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
            \Illuminate\Support\Facades\Log::error('2FA resend error: ' . $e->getMessage());
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

        // Send the code via email
        Mail::raw(
            "Your verification code is: {$code}\n\nThis code expires in 10 minutes.\n\nIf you did not request this, please ignore this email.",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Login Verification Code');
            }
        );
    }
}
