@extends('layouts.app')

@section('title', 'USDT Payment')
@section('page-title', 'USDT Payment')

@section('content')

<div style="max-width:680px;margin:0 auto;">

    {{-- Back + Header --}}
    <div style="margin-bottom:1.75rem;">
        <a href="{{ route('citizen.payment.select', $citizenRequest) }}"
           style="color:var(--muted);text-decoration:none;font-size:0.82rem;display:inline-flex;align-items:center;gap:0.4rem;margin-bottom:0.75rem;transition:color 0.2s;"
           onmouseover="this.style.color='var(--navy)'" onmouseout="this.style.color='var(--muted)'">
            <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'العودة إلى طرق الدفع' : 'Back to Payment Methods' }}
        </a>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:var(--navy);margin:0 0 0.25rem;">
            {{ app()->getLocale() === 'ar' ? 'الدفع بـ USDT' : 'Pay with USDT' }}
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">
            {{ __('pages.service_label') }}: <strong style="color:var(--text);">{{ $citizenRequest->service->display_name }}</strong>
            &mdash; {{ $citizenRequest->office->name }}
        </p>
    </div>

    @php $isAr = app()->getLocale() === 'ar'; @endphp

    @if(session('error'))
        <div style="background:rgba(220,38,38,0.1);border:1px solid rgba(220,38,38,0.3);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#dc2626;display:flex;align-items:center;gap:0.75rem;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    @if($citizenRequest->payment_status === 'paid')
        <div class="app-card" style="text-align:center;padding:3rem 2rem;">
            <div style="width:64px;height:64px;background:rgba(4,120,87,0.2);border:1px solid rgba(4,120,87,0.35);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-check-lg" style="color:#047857;font-size:1.8rem;"></i>
            </div>
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.1rem;color:var(--navy);margin-bottom:0.5rem;">
                {{ $isAr ? 'تم تأكيد الدفع' : 'Payment Confirmed' }}
            </div>
            <p style="color:var(--muted);margin:0;">{{ $isAr ? 'تم دفع هذا الطلب مسبقاً.' : 'This request has already been paid.' }}</p>
        </div>

    @elseif(!$transaction)
        {{-- No active transaction — show initiation card --}}
        <div class="app-card mb-4">
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:1.25rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
                {{ $isAr ? 'الدفع بعملة USDT المستقرة' : 'Pay with USDT Stablecoin' }}
            </div>

            <div style="background:rgba(71,195,110,0.08);border:1px solid rgba(71,195,110,0.25);border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.75rem;display:flex;align-items:center;gap:1rem;">
                <div style="font-size:2rem;">💲</div>
                <div>
                    <div style="font-weight:700;color:var(--navy);margin-bottom:0.2rem;">USDT · Tether</div>
                    <div style="font-size:0.82rem;color:var(--muted);">{{ $isAr ? 'عملة مستقرة مربوطة بالدولار الأمريكي — 1 USDT = $1.00' : 'USD-pegged stablecoin — 1 USDT = $1.00 always' }}</div>
                </div>
            </div>

            @php
                $service   = $citizenRequest->service;
                $lbpRate   = (float) \App\Models\Setting::get('lbp_usd_rate', 89500);
                $amountUsd = $service->currency === 'LBP'
                    ? round($service->price / $lbpRate, 2)
                    : round((float) $service->price, 2);
            @endphp

            <div style="background:rgba(214,158,46,0.08);border:1px solid rgba(214,158,46,0.2);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
                <div style="font-size:0.78rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">{{ $isAr ? 'المبلغ المستحق' : 'Amount Due' }}</div>
                <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.75rem;color:var(--gold);">
                    {{ number_format($amountUsd, 2) }} <span style="font-size:1rem;">USDT</span>
                </div>
                @if($service->currency === 'LBP')
                <div style="font-size:0.78rem;color:var(--muted);margin-top:0.25rem;">
                    {{ $isAr ? 'يعادل' : 'equivalent to' }} {{ number_format($service->price, 0) }} LBP
                    {{ $isAr ? 'بسعر' : 'at' }} {{ number_format($lbpRate, 0) }} LBP/USD
                </div>
                @endif
            </div>

            <form method="POST" action="{{ route('crypto.initiate', $citizenRequest) }}">
                @csrf
                <button type="submit" class="btn-gold w-100" style="justify-content:center;">
                    <i class="bi bi-wallet2"></i> {{ $isAr ? 'إنشاء عنوان الدفع' : 'Generate Payment Address' }}
                </button>
            </form>
        </div>

    @else
        {{-- Active transaction --}}
        <div class="app-card mb-4">
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:1.25rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
                {{ $isAr ? 'أرسل الدفعة' : 'Send Payment' }}
            </div>

            <div style="background:rgba(71,195,110,0.08);border:1px solid rgba(71,195,110,0.25);border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.5rem;font-size:0.85rem;color:#065f46;display:flex;align-items:center;gap:0.5rem;">
                <i class="bi bi-clock-fill"></i>
                {{ $isAr ? 'صالح حتى' : 'Valid until' }} {{ $transaction->expires_at->format('d M Y, H:i') }}
            </div>

            <div class="row g-4 align-items-center mb-4">
                <div class="col-md-5 text-center">
                    @if($qrSvg)
                        <div style="display:inline-block;border-radius:12px;border:1px solid var(--border);padding:8px;background:#ffffff;max-width:196px;">
                            {!! $qrSvg !!}
                        </div>
                    @else
                        <div style="width:180px;height:180px;border-radius:12px;border:1px solid var(--border);background:#f8fafc;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                            <span style="color:var(--muted);font-size:0.8rem;">QR unavailable</span>
                        </div>
                    @endif
                    <div style="font-size:0.75rem;color:var(--muted);margin-top:0.5rem;">{{ $isAr ? 'امسح للدفع' : 'Scan to pay' }}</div>
                </div>

                <div class="col-md-7">
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ $isAr ? 'المبلغ المطلوب إرساله' : 'Amount to send' }}</div>
                        <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;color:var(--gold);">
                            {{ number_format($transaction->amount_crypto, 2) }} USDT
                        </div>
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:2px;">≈ ${{ number_format($transaction->amount_usd, 2) }} USD</div>
                    </div>

                    <div>
                        <div style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">{{ $isAr ? 'عنوان المحفظة' : 'Wallet address' }}</div>
                        <div style="display:flex;gap:0.5rem;">
                            <input type="text" id="walletAddr" value="{{ $transaction->wallet_address }}" readonly
                                   style="background:#f8fafc;border:1px solid var(--border);color:var(--text);border-radius:7px;padding:0.45rem 0.75rem;font-size:0.78rem;font-family:monospace;flex:1;min-width:0;">
                            <button onclick="copyWallet()" class="btn-ghost" style="padding:0.45rem 0.75rem;font-size:0.78rem;flex-shrink:0;">
                                <i class="bi bi-clipboard"></i> {{ $isAr ? 'نسخ' : 'Copy' }}
                            </button>
                        </div>
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:0.4rem;">
                            <i class="bi bi-info-circle me-1"></i>{{ $isAr ? 'تأكد من إرسال USDT على الشبكة الصحيحة.' : 'Make sure you send USDT on the correct network (TRC-20 / ERC-20).' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit TXID --}}
            <div style="border-top:1px solid var(--border);padding-top:1.25rem;">
                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.9rem;color:var(--navy);margin-bottom:0.5rem;">
                    {{ $isAr ? 'أدخل رقم المعاملة' : 'Submit Transaction Hash (TXID)' }}
                </div>
                <p style="color:var(--muted);font-size:0.82rem;margin-bottom:1rem;">
                    {{ $isAr ? 'بعد الإرسال، الصق رقم المعاملة أدناه لتأكيد دفعتك.' : 'After sending, paste your transaction hash below so we can verify your payment.' }}
                </p>

                <form method="POST" action="{{ route('crypto.submit-tx', $transaction) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label-custom">{{ $isAr ? 'رقم المعاملة (TXID / Hash)' : 'Transaction Hash (TXID)' }}</label>
                        <input type="text" name="tx_hash"
                               class="form-control-custom"
                               style="font-family:monospace;"
                               placeholder="e.g. 3a1b2c3d4e5f…"
                               value="{{ old('tx_hash') }}" required>
                        @error('tx_hash')
                            <div style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn-emerald w-100" style="justify-content:center;padding:0.65rem;">
                        <i class="bi bi-check-circle-fill"></i> {{ $isAr ? 'تأكيد الدفع' : 'Confirm Payment' }}
                    </button>
                </form>

                <div style="text-align:center;margin-top:1rem;">
                    <form method="POST" action="{{ route('crypto.initiate', $citizenRequest) }}">
                        @csrf
                        <button type="submit" style="background:none;border:none;color:var(--muted);font-size:0.8rem;cursor:pointer;text-decoration:underline;">
                            {{ $isAr ? 'إنشاء عنوان جديد' : 'Generate new address' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
function copyWallet() {
    const el = document.getElementById('walletAddr');
    if (!el) return;
    navigator.clipboard.writeText(el.value).then(() => {
        const btn = el.nextElementSibling;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
        setTimeout(() => { btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy'; }, 2000);
    });
}
</script>
@endpush
