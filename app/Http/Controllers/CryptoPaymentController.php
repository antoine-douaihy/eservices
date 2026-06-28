<?php

namespace App\Http\Controllers;

use App\Models\CitizenRequest;
use App\Models\CryptoTransaction;
use App\Models\Setting;
use App\Support\LaravelRequest as Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CryptoPaymentController extends Controller
{
    public function show(CitizenRequest $citizenRequest)
    {
        if ($citizenRequest->user_id !== Auth::id()) {
            return Redirect::route('login');
        }

        $transaction = $citizenRequest->cryptoTransactions()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        $qrSvg = null;
        if ($transaction) {
            try {
                $qrSvg = QrCode::format('svg')->size(180)->generate($transaction->wallet_address);
            } catch (\Exception $e) {}
        }

        return View::make('crypto.payment', compact('citizenRequest', 'transaction', 'qrSvg'));
    }

    public function initiate(Request $request, CitizenRequest $citizenRequest)
    {
        if ($citizenRequest->user_id !== Auth::id()) {
            return Redirect::route('login');
        }

        $service   = $citizenRequest->service;
        $amountUsd = $service->currency === 'LBP'
            ? round($service->price / (float) Setting::get('lbp_usd_rate', 89500), 2)
            : round((float) $service->price, 2);

        $wallet = Setting::get('usdt_wallet', '');

        // Expire any existing pending transactions
        $citizenRequest->cryptoTransactions()
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        CryptoTransaction::create([
            'citizen_request_id' => $citizenRequest->id,
            'currency'           => 'USDT',
            'amount_usd'         => $amountUsd,
            'amount_crypto'      => $amountUsd,   // USDT is 1:1 with USD
            'crypto_price_usd'   => 1.00,
            'wallet_address'     => $wallet,
            'status'             => 'pending',
            'expires_at'         => now()->addHours(24),
        ]);

        return Redirect::route('crypto.payment', $citizenRequest);
    }

    public function submitTxHash(Request $request, CryptoTransaction $transaction)
    {
        if ($transaction->citizenRequest->user_id !== Auth::id()) {
            return Redirect::route('login');
        }

        if ($transaction->status !== 'pending') {
            $message = match ($transaction->status) {
                'confirmed' => 'This transaction has already been submitted.',
                'expired'   => 'This payment request has expired. Please generate a new one.',
                default     => 'This transaction is no longer valid.',
            };
            return Redirect::back()->withInput()->with('error', $message);
        }

        $request->validate([
            'tx_hash' => 'required|string|min:10|max:150',
        ]);

        $transaction->update([
            'tx_hash' => $request->input('tx_hash'),
            'status'  => 'confirmed',
        ]);

        $transaction->citizenRequest->update([
            'payment_method' => 'crypto',
            'payment_status' => 'paid',
            'status'         => 'in_review',
        ]);

        if ($transaction->citizenRequest->serviceApplication) {
            $transaction->citizenRequest->serviceApplication->update(['status' => 'reviewing']);
        }

        try {
            $transaction->citizenRequest->user->notify(
                new \App\Notifications\PaymentConfirmed($transaction->citizenRequest->fresh(), 'crypto')
            );
        } catch (\Exception $e) {}

        return Redirect::route('citizen.my-requests')
            ->with('success', 'Payment submitted. Your TXID has been recorded and is under review.');
    }
}
