<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PasswordResetCodeController extends Controller
{
    public function showEmailForm()
    {
        return view('auth.forgot-password-code');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $user->update([
            'reset_code'            => $code,
            'reset_code_expires_at' => now()->addMinutes(10),
        ]);

        Mail::send('emails.reset-code', [
            'code' => $code,
            'user' => $user,
        ], function ($mail) use ($user) {
            $mail->to($user->email)
                 ->subject('Your Password Reset Code — E-Services Platform');
        });

        session(['reset_email' => $request->email]);

        return redirect()->route('password.code.verify.form');
    }

    public function showVerifyForm()
    {
        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'digits:4'],
        ]);

        $user = User::where('email', $request->email)
                    ->where('reset_code', $request->code)
                    ->where('reset_code_expires_at', '>=', now())
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'code' => 'Invalid or expired code. Please try again.',
            ])->withInput();
        }

        session(['reset_email' => $request->email]);

        return redirect()->route('password.code.reset.form');
    }

    public function showResetForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please try again.']);
        }

        return view('auth.reset-password-code');
    }

    public function resetPassword(Request $request)
    {
        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please try again.']);
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
            'password.min'       => 'Password must be at least 8 characters.',
            'password.regex'     => 'Password must contain uppercase, lowercase, number and special character (@$!%*#?&).',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        $user->update([
            'password'              => $request->password,
            'reset_code'            => null,
            'reset_code_expires_at' => null,
        ]);

        session()->forget('reset_email');

        return redirect()->route('login')
            ->with('status', 'Password reset successfully! Please login.');
    }
}