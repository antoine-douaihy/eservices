<?php

namespace App\Http\Controllers;

use App\Models\CitizenRequest;
use App\Models\CryptoTransaction;
use App\Support\LaravelRequest as Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class CryptoPaymentController extends Controller
{
    public function show(CitizenRequest $citizenRequest)
    {
        if ($citizenRequest->user_id !== Auth::id()) {
            return Redirect::route('login');
        }

        $prices      = $this->fetchCryptoPrices();
        $transaction = $citizenRequest->cryptoTransactions()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return View::make('crypto.payment', compact('citizenRequest', 'prices', 'transaction'));
    }

    public function initiate(Request $request, CitizenRequest $citizenRequest)
    {
        if ($citizenRequest->user_id !== Auth::id()) {
            return Redirect::route('login');
        }

        $request->validate(['currency' => 'required|in:BTC,ETH']);

        $prices     = $this->fetchCryptoPrices();
        $currency   = $request->input('currency');
        $priceUsd   = $prices[$currency];
        $amountUsd  = $citizenRequest->service->price;
        $amountCrypto = round($amountUsd / $priceUsd, 8);

        $wallet = $currency === 'BTC'
            ? Config::get('services.crypto.btc_wallet')
            : Config::get('services.crypto.eth_wallet');

        // Expire any existing pending transactions for this request
        $citizenRequest->cryptoTransactions()
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        CryptoTransaction::create([
            'citizen_request_id' => $citizenRequest->id,
            'currency'           => $currency,
            'amount_usd'         => $amountUsd,
            'amount_crypto'      => $amountCrypto,
            'crypto_price_usd'   => $priceUsd,
            'wallet_address'     => $wallet,
            'status'             => 'pending',
            'expires_at'         => now()->addMinutes(30),
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
                'expired'   => 'This payment request has expired. Please refresh the rate and try again.',
                default     => 'This transaction is no longer valid. Please start a new payment.',
            };

            return Redirect::back()->withInput()->with('error', $message);
        }

        $request->validate([
            'tx_hash' => 'required|string|min:10|max:100',
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

        // Update linked ServiceApplication status to reviewing
        if ($transaction->citizenRequest->serviceApplication) {
            $transaction->citizenRequest->serviceApplication->update(['status' => 'reviewing']);
        }

        try {
            $transaction->citizenRequest->user->notify(
                new \App\Notifications\PaymentConfirmed($transaction->citizenRequest->fresh(), 'crypto')
            );
        } catch (\Exception $e) {}

        return Redirect::route('citizen.my-requests')
            ->with('success', 'Payment submitted. Your transaction hash has been recorded and is under review.');
    }

    private function fetchCryptoPrices(): array
    {
        try {
            $response = Http::timeout(5)->get('https://api.coingecko.com/api/v3/simple/price', [
                'ids'           => 'bitcoin,ethereum',
                'vs_currencies' => 'usd',
            ]);

            $data = $response->json();

            return [
                'BTC' => $data['bitcoin']['usd'] ?? 0,
                'ETH' => $data['ethereum']['usd'] ?? 0,
            ];
        } catch (\Exception $e) {
            // Fallback prices if the API is unreachable, so payment isn't blocked
            return ['BTC' => 60000, 'ETH' => 3000];
        }
    }
}
