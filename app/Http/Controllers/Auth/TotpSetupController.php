<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TotpSetupController extends Controller
{
    public function setup()
    {
        $user      = Auth::user();
        $google2fa = new Google2FA();

        // If they already have a pending (disabled) secret saved, reuse it
        // so refreshing the page does not generate a new QR.
        // If 2FA is already fully enabled, generate a fresh secret for re-enrollment.
        $existingSecret = DB::table('users')
            ->where('id', $user->id)
            ->value('two_factor_secret');

        $alreadyEnabled = DB::table('users')
            ->where('id', $user->id)
            ->value('two_factor_enabled');

        if ($existingSecret && !$alreadyEnabled) {
            // Reuse the pending secret
            $secret = $existingSecret;
        } else {
            // Generate fresh secret and save it immediately (disabled until confirmed)
            $secret = $google2fa->generateSecretKey();
            DB::table('users')->where('id', $user->id)->update([
                'two_factor_secret'  => $secret,
                'two_factor_enabled' => 0,
                'updated_at'         => now(),
            ]);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $qrSvg = QrCode::format('svg')->size(200)->errorCorrection('M')->generate($qrCodeUrl);

        return view('auth.totp-setup', compact('qrCodeUrl', 'secret', 'qrSvg'));
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $google2fa = new Google2FA();

        // Read secret directly from DB — no session dependency
        $secret = DB::table('users')->where('id', Auth::id())->value('two_factor_secret');

        if (!$secret) {
            return redirect()->route('2fa.setup')
                ->withErrors(['code' => 'No secret found. Please scan the QR code again.']);
        }

        if (!$google2fa->verifyKey($secret, $request->code, 4)) {
            return back()->withErrors(['code' => 'Code did not match. Please try again.']);
        }

        // Secret verified — activate 2FA
        DB::table('users')->where('id', Auth::id())->update([
            'two_factor_enabled' => 1,
            'updated_at'         => now(),
        ]);

        // Cycle the remember token so any existing "remember me" cookie is invalidated.
        // Next login via that cookie will require going through the 2FA challenge.
        DB::table('users')->where('id', Auth::id())->update([
            'remember_token' => \Illuminate\Support\Str::random(60),
        ]);

        // Mark this session as already 2FA-verified so the middleware does not bounce
        // the user out of the very page they just completed setup on.
        session(['2fa:verified' => true]);

        return redirect()->route('home')
            ->with('status', 'Two-factor authentication has been enabled on your account.');
    }
}
