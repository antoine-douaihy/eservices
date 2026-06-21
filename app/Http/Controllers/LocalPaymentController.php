<?php

namespace App\Http\Controllers;

use App\Models\CitizenRequest;
use App\Models\LocalPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocalPaymentController extends Controller
{
    public function show(CitizenRequest $citizenRequest)
    {
        abort_if($citizenRequest->user_id !== Auth::id(), 403);

        $pending = $citizenRequest->localPayments()
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('local-payment.payment', compact('citizenRequest', 'pending'));
    }

    public function initiate(Request $request, CitizenRequest $citizenRequest)
    {
        abort_if($citizenRequest->user_id !== Auth::id(), 403);

        $request->validate(['method' => 'required|in:wish,omt']);

        $method  = $request->input('method');
        $account = $method === 'wish'
            ? config('services.local_payments.wish_account')
            : config('services.local_payments.omt_account');

        if (empty($account)) {
            return back()->withErrors(['method' => strtoupper($method) . ' account is not configured yet. Please contact support.']);
        }

        // Remove any stale pending payments before creating a new one
        $citizenRequest->localPayments()
            ->where('status', 'pending')
            ->delete();

        LocalPayment::create([
            'citizen_request_id' => $citizenRequest->id,
            'method'             => $method,
            'amount_usd'         => $citizenRequest->service->price,
            'account_details'    => $account,
            'status'             => 'pending',
        ]);

        return redirect()->route('local-payment.show', $citizenRequest);
    }

    public function submitReference(Request $request, LocalPayment $payment)
    {
        abort_if($payment->citizenRequest->user_id !== Auth::id(), 403);
        abort_if($payment->status !== 'pending', 422);

        $request->validate([
            'reference_number' => 'required|string|min:4|max:100',
        ]);

        $payment->update([
            'reference_number' => $request->input('reference_number'),
            'status'           => 'confirmed',
        ]);

        $payment->citizenRequest->update([
            'payment_method' => $payment->method,
            'payment_status' => 'paid',
            'status'         => 'in_review',
        ]);

        // Update linked ServiceApplication status to reviewing
        if ($payment->citizenRequest->serviceApplication) {
            $payment->citizenRequest->serviceApplication->update(['status' => 'reviewing']);
        }

        return redirect()->route('citizen.my-requests')
            ->with('success', 'Payment reference submitted successfully. It will be verified shortly.');
    }
}
