<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TotpSetupController extends Controller
{
    public function setup()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        // ONLY generate a secret if one doesn't exist in the session yet
        // This prevents the secret from changing if the page refreshes
        if (!session()->has('2fa:setup_secret')) {
            $secret = $google2fa->generateSecretKey();
            session(['2fa:setup_secret' => $secret]);
        } else {
            $secret = session('2fa:setup_secret');
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $qrSvg = QrCode::format('svg')->size(200)->errorCorrection('M')->generate($qrCodeUrl);

        return view('auth.totp-setup', compact('qrCodeUrl', 'secret', 'qrSvg'));
    }

    // Confirm the user scanned it correctly
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        $google2fa = new Google2FA();
        $secret    = session('2fa:setup_secret');

        if (!$google2fa->verifyKey($secret, $request->code, 4)) {
            return back()->withErrors(['code' => 'Code did not match. Please scan the QR code again.']);
        }

        Auth::user()->update([
            'two_factor_secret'  => $secret,
            'two_factor_enabled' => true,
        ]);

        session()->forget('2fa:setup_secret');

        return redirect()->route('home')->with('status', 'Google Authenticator has been enabled for your account!');
    }
}rget('2fa:setup_secret');

        return redirect('/dashboard')->with('status', 'Google Authenticator has been enabled for your account!');
    }
}
