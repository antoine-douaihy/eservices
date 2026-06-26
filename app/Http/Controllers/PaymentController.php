<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CitizenRequest;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Setting;

class PaymentController extends Controller
{
    // 1. Redirect to Stripe Checkout
    public function checkout($id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        // Ensure they only pay for approved requests
        if ($serviceRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'You can only pay for approved documents.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Government Document: ' . $serviceRequest->document_type,
                    ],
                    // Hardcoding $50.00 for the project requirement (5000 cents)
                    'unit_amount' => 5000,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['id' => $serviceRequest->id]),
            'cancel_url' => route('home'),
        ]);

        return redirect($checkout_session->url);
    }

    // 2. Handle successful payment (legacy ServiceRequest flow)
    public function success($id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $serviceRequest->update(['status' => 'completed']);

        return redirect()->route('home')->with('success', 'Payment successful! Your document is now fully processed.');
    }

    // 3. Stripe checkout for CitizenRequest (service application flow)
    public function checkoutCitizenRequest(CitizenRequest $citizenRequest)
    {
        abort_if($citizenRequest->user_id !== Auth::id(), 403);

        $citizenRequest->load('service');

        Stripe::setApiKey(config('services.stripe.secret'));

        $currency    = strtolower($citizenRequest->service->currency ?? 'usd');
        $price       = (float) $citizenRequest->service->price;
        $displayName = $citizenRequest->service->name;

        // LBP is not a Stripe-supported currency — convert to USD using the admin-set rate
        if ($currency === 'lbp') {
            $lbpRate    = (float) Setting::get('lbp_usd_rate', 89500);
            $usdAmount  = $price / $lbpRate;
            $currency   = 'usd';
            $displayName .= ' (' . number_format($price, 0) . ' LBP)';
        } else {
            $usdAmount = $price;
        }

        $unitAmount = (int) round($usdAmount * 100); // cents

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => $currency,
                    'product_data' => ['name' => $displayName],
                    'unit_amount'  => $unitAmount,
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('stripe.citizen.success', $citizenRequest),
            'cancel_url'  => route('citizen.payment.select', $citizenRequest),
        ]);

        return redirect($session->url);
    }

    // 4. Handle successful Stripe payment for CitizenRequest
    public function successCitizenRequest(CitizenRequest $citizenRequest)
    {
        abort_if($citizenRequest->user_id !== Auth::id(), 403);

        $citizenRequest->update([
            'payment_method' => 'stripe',
            'payment_status' => 'paid',
            'status'         => 'in_review',
        ]);

        if ($citizenRequest->serviceApplication) {
            $citizenRequest->serviceApplication->update(['status' => 'reviewing']);
        }

        try {
            $citizenRequest->user->notify(new \App\Notifications\PaymentConfirmed($citizenRequest->fresh(), 'stripe'));
        } catch (\Exception $e) {}

        return redirect()->route('citizen.my-requests')
            ->with('success', 'Payment successful! Your request is now under review.');
    }
}
