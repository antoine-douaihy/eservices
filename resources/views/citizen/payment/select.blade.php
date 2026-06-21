@extends('layouts.app')

@section('title', 'Select Payment Method')
@section('page-title', 'Select Payment Method')

@section('content')

<div style="max-width:680px;margin:0 auto;">

    {{-- Header --}}
    <div style="text-align:center;margin-bottom:2rem;">
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.7rem;color:var(--navy);margin:0 0 0.5rem;">
            {{ app()->getLocale() === 'ar' ? 'إتمام الدفع' : 'Complete Your Payment' }}
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;">{{ app()->getLocale() === 'ar' ? 'اختر طريقة الدفع المفضلة لإكمال طلبك.' : 'Choose your preferred payment method to proceed with your application.' }}</p>
    </div>

    {{-- Application Details --}}
    <div class="app-card mb-4">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:1.25rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
            <i class="bi bi-receipt me-2" style="color:var(--gold);"></i> {{ __('pages.application_details') }}
        </div>
        <div class="row g-3">
            <div class="col-sm-6">
                <div style="font-size:0.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ __('pages.service_label') }}</div>
                <div style="font-weight:600;color:var(--navy);">{{ $citizenRequest->service->display_name }}</div>
            </div>
            <div class="col-sm-6">
                <div style="font-size:0.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ app()->getLocale() === 'ar' ? 'الدائرة' : 'Office' }}</div>
                <div style="font-weight:600;color:var(--navy);">{{ $citizenRequest->office->name }}</div>
            </div>
            <div class="col-sm-6">
                <div style="font-size:0.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</div>
                <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.2rem;color:var(--gold);">
                    {{ $citizenRequest->service->price }} {{ $citizenRequest->service->currency }}
                </div>
            </div>
            <div class="col-sm-6">
                <div style="font-size:0.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ app()->getLocale() === 'ar' ? 'المرجع' : 'Reference' }}</div>
                <div style="font-weight:600;color:var(--navy);">#{{ $citizenRequest->id }}</div>
            </div>
        </div>
    </div>

    {{-- Payment Methods --}}
    <div style="margin-bottom:1rem;">
        <div style="font-size:0.78rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:1rem;">{{ app()->getLocale() === 'ar' ? 'اختر طريقة الدفع' : 'Select Payment Method' }}</div>

        {{-- Stripe / Card --}}
        <div style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.5rem;margin-bottom:0.875rem;transition:border-color 0.2s,box-shadow 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.06);"
             onmouseover="this.style.borderColor='#93c5fd';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
             onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)'">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:1rem;">
                    <div style="width:48px;height:48px;background:#dbeafe;border:1px solid #93c5fd;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-credit-card-fill" style="color:#1d4ed8;font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;color:var(--navy);font-size:0.95rem;">{{ app()->getLocale() === 'ar' ? 'بطاقة ائتمان / خصم' : 'Credit / Debit Card' }}</div>
                        <div style="color:var(--muted);font-size:0.8rem;">{{ app()->getLocale() === 'ar' ? 'الدفع الآمن عبر Stripe — Visa، Mastercard، Amex' : 'Pay securely via Stripe — Visa, Mastercard, Amex' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('stripe.citizen.checkout', $citizenRequest) }}">
                    @csrf
                    <button type="submit"
                            style="background:#2563eb;border:none;color:#fff;font-weight:600;font-size:0.875rem;padding:0.6rem 1.4rem;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;transition:background 0.2s;"
                            onmouseover="this.style.background='#1d4ed8'"
                            onmouseout="this.style.background='#2563eb'">
                        <i class="bi bi-lock-fill"></i> {{ app()->getLocale() === 'ar' ? 'الدفع بالبطاقة' : 'Pay with Card' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Crypto --}}
        <div style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.5rem;margin-bottom:0.875rem;transition:border-color 0.2s,box-shadow 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.06);"
             onmouseover="this.style.borderColor='#fde68a';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
             onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)'">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:1rem;">
                    <div style="width:48px;height:48px;background:#fef3c7;border:1px solid #fde68a;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-currency-bitcoin" style="color:var(--gold);font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;color:var(--navy);font-size:0.95rem;">{{ app()->getLocale() === 'ar' ? 'العملات المشفرة' : 'Cryptocurrency' }}</div>
                        <div style="color:var(--muted);font-size:0.8rem;">{{ app()->getLocale() === 'ar' ? 'الدفع باستخدام Bitcoin أو Ethereum' : 'Pay with Bitcoin or Ethereum' }}</div>
                    </div>
                </div>
                <a href="{{ route('crypto.payment', $citizenRequest) }}"
                   style="background:#fef3c7;border:1px solid #fde68a;color:#92400e;font-weight:600;font-size:0.875rem;padding:0.6rem 1.4rem;border-radius:9px;text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;transition:all 0.2s;"
                   onmouseover="this.style.background='#fde68a'"
                   onmouseout="this.style.background='#fef3c7'">
                    <i class="bi bi-arrow-right-circle-fill"></i> {{ app()->getLocale() === 'ar' ? 'اختيار العملة المشفرة' : 'Select Crypto' }}
                </a>
            </div>
        </div>

    </div>

    {{-- Back --}}
    <div style="text-align:center;margin-top:1.5rem;">
        <a href="{{ route('citizen.applications.index') }}"
           style="color:var(--muted);text-decoration:none;font-size:0.875rem;transition:color 0.2s;"
           onmouseover="this.style.color='var(--navy)'"
           onmouseout="this.style.color='var(--muted)'">
            <i class="bi bi-arrow-left me-1"></i> {{ app()->getLocale() === 'ar' ? 'العودة إلى طلباتي' : 'Back to My Applications' }}
        </a>
    </div>

</div>

@endsection
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              