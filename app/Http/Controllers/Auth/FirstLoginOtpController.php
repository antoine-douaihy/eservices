<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Handles the mandatory first-login flow for newly created office staff
 * accounts. When an admin creates a staff account, a random temporary
 * password is generated and emailed directly to the staff member (the
 * admin never sees or sets it), and the account is flagged with
 * requires_first_login_otp = true. The staff member logs in normally
 * using that emailed password, but is then forced — before doing
 * anything else — to choose their own permanent password.
 */
class FirstLoginOtpController extends Controller
{
    // Show the "choose your password" page (user is already authenticated
    // at this point — they just logged in with their emailed temp password).
    public function showSetPassword()
    {
        if (!Auth::check() || !Auth::user()->requires_first_login_otp) {
            return redirect()->route('login');
        }

        return view('auth.first-login-set-password');
    }

    // Save the new permanent password and lift the gate.
    public function setPassword(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->requires_first_login_otp) {
            return redirect()->route('login');
        }

        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.min'   => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain an uppercase letter, lowercase letter, number, and special character (@$!%*#?&).',
        ]);

        $user->update([
            'password'                 => $request->password,
            'requires_first_login_otp' => false,
        ]);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Password set! Welcome to your account.');
        }
        return redirect()->route('office.dashboard')->with('success', 'Password set! Welcome to your account.');
    }

    // ── Static helper: email the freshly generated temporary password ──
    public static function sendTemporaryPasswordEmail(User $user, string $temporaryPassword): void
    {
        Mail::raw(
            "Welcome to E-Services Platform!\n\n" .
            "An administrator has created a staff account for you.\n\n" .
            "Email: {$user->email}\n" .
            "Temporary password: {$temporaryPassword}\n\n" .
            "Log in with this password at your earliest convenience. You will be " .
            "required to choose your own permanent password immediately after your " .
            "first login — this temporary password will stop working once you do.\n\n" .
            "If you were not expecting this email, please contact your administrator.",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your New Staff Account — E-Services Platform');
            }
        );
    }
}
