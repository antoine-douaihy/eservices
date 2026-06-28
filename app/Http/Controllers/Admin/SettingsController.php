<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $lbpRate    = Setting::get('lbp_usd_rate', '89500');
        $usdtWallet = Setting::get('usdt_wallet', '');
        return view('admin.settings', compact('lbpRate', 'usdtWallet'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'lbp_usd_rate' => ['required', 'numeric', 'min:1'],
            'usdt_wallet'  => ['nullable', 'string', 'max:150'],
        ]);

        Setting::set('lbp_usd_rate', $request->lbp_usd_rate);
        Setting::set('usdt_wallet',  trim($request->usdt_wallet ?? ''));

        return redirect()->route('admin.settings')->with('success', 'Settings saved successfully.');
    }
}
