<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        // Staff logging in for the first time with their emailed temporary
        // password must choose a permanent password before anything else.
        if ($user->requires_first_login_otp) {
            return redirect()->route('first-login.set-password');
        }

        if ($user->two_factor_enabled && $user->two_factor_secret) {
            Auth::logout();
            session(['2fa:user_id' => $user->id]);
            return redirect()->route('2fa.challenge');
        }

        if ($user->two_factor_enabled) {
            Auth::logout();
            session(['2fa:user_id' => $user->id]);
            TwoFactorController::sendEmailCode($user);
            return redirect()->route('2fa.challenge');
        }

        return redirect()->intended($this->redirectTo);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
          