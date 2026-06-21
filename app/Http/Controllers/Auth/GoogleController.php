<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Step 1: Redirect user to Google login page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Step 2: Google sends user back here after they log in.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception) {
            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }

        // Find user by google_id OR email. If not found, create new.
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update Google ID if they previously registered with email
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        } else {
            // Parse Google full name into first/last
            $nameParts = explode(' ', trim($googleUser->getName()), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? '';

            // Brand new user — create with role = citizen (ALWAYS)
            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $googleUser->getEmail(),
                'google_id'  => $googleUser->getId(),
                'avatar'     => $googleUser->getAvatar(),
                'role'       => 'citizen',
                'password'   => \Illuminate\Support\Str::random(32),
            ]);
        }

        // Log the user in (true = remember me)
        Auth::login($user, true);

        return redirect()->intended('/dashboard');
    }
}
