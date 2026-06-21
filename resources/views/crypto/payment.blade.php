@extends('layouts.app')

@section('title', 'Crypto Payment')
@section('page-title', 'Crypto Payment')

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
            {{ app()->getLocale() === 'ar' ? 'الدفع بالعملات المشفرة' : 'Pay with Cryptocurrency' }}
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">
            {{ __('pages.service_label') }}: <strong style="color:var(--text);">{{ $citizenRequest->service->display_name }}</strong>
            &mdash; {{ $citizenRequest->office->name }}
        </p>
    </div>

    @php $isAr = app()->getLocale() === 'ar'; @endphp

    @if($citizenRequest->payment_status === 'paid')
        <div class="app-card" style="text-align:center;padding:3rem 2rem;">
            <div style="width:64px;height:64px;background:rgba(4,120,87,0.2);border:1px solid rgba(4,120,87,0.35);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-check-lg" style="color:#047857;font-size:1.8rem;"></i>
            </div>
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.1rem;color:var(--navy);margin-bottom:0.5rem;">{{ $isAr ? 'تم تأكيد الدفع' : 'Payment Confirmed' }}</div>
            <p style="color:var(--muted);margin:0;">{{ $isAr ? 'تم دفع هذا الطلب مسبقاً.' : 'This request has already been paid.' }}</p>
        </div>
    @else

    {{-- Step 1: Select currency --}}
    @if(!$transaction)
    <div class="app-card mb-4">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:1.25rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
            {{ $isAr ? 'الخطوة 1 — اختر العملة' : 'Step 1 — Choose Currency' }}
        </div>

        <div style="background:rgba(214,158,46,0.08);border:1px solid rgba(214,158,46,0.2);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;">
            {{ $isAr ? 'المبلغ المستحق:' : 'Amount due:' }} <span style="font-family:'Syne',sans-serif;font-weight:800;color:var(--gold);font-size:1.1rem;">${{ number_format($citizenRequest->service->price, 2) }} USD</span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6">
                <div style="background:rgba(247,147,26,0.08);border:1px solid rgba(247,147,26,0.2);border-radius:12px;padding:1.25rem;text-align:center;">
                    <div style="font-size:1.75rem;color:#f7931a;margin-bottom:0.25rem;">₿</div>
                    <div style="font-weight:700;color:var(--navy);margin-bottom:0.25rem;">Bitcoin (BTC)</div>
                    <div style="font-size:0.78rem;color:var(--muted);">${{ number_format($prices['BTC'], 2) }} / BTC</div>
                    <div style="font-size:0.78rem;color:#047857;margin-top:0.35rem;">
                        ≈ {{ number_format($citizenRequest->service->price / $prices['BTC'], 8) }} BTC
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div style="background:rgba(98,126,234,0.08);border:1px solid rgba(98,126,234,0.2);border-radius:12px;padding:1.25rem;text-align:center;">
                    <div style="font-size:1.75rem;color:#627eea;margin-bottom:0.25rem;">⬡</div>
                    <div style="font-weight:700;color:var(--navy);margin-bottom:0.25rem;">Ethereum (ETH)</div>
                    <div style="font-size:0.78rem;color:var(--muted);">${{ number_format($prices['ETH'], 2) }} / ETH</div>
                    <div style="font-size:0.78rem;color:#047857;margin-top:0.35rem;">
                        ≈ {{ number_format($citizenRequest->service->price / $prices['ETH'], 8) }} ETH
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('crypto.initiate', $citizenRequest) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label-custom">{{ $isAr ? 'اختر العملة للدفع بها' : 'Select currency to pay with' }}</label>
                <select name="currency" class="form-select-custom @error('currency') is-invalid @enderror" required>
                    <option value="">{{ $isAr ? '— اختر —' : '— Choose —' }}</option>
                    <option value="BTC">Bitcoin (BTC)</option>
                    <option value="ETH">Ethereum (ETH)</option>
                </select>
                @error('currency')
                    <div style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
            <div style="font-size:0.78rem;color:var(--muted);margin-bottom:1rem;">
                <i class="bi bi-info-circle me-1"></i> {{ $isAr ? 'أسعار مباشرة من CoinGecko. السعر مُثبّت لمدة 30 دقيقة بعد المتابعة.' : 'Live prices from CoinGecko. Rate locked for 30 minutes once you proceed.' }}
            </div>
            <button type="submit" class="btn-gold w-100" style="justify-content:center;">
                <i class="bi bi-lock-fill"></i> {{ $isAr ? 'إنشاء عنوان الدفع' : 'Generate Payment Address' }}
            </button>
        </form>
    </div>
    @endif

    {{-- Step 2: Active payment --}}
    @if($transaction)
    <div class="app-card mb-4">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:1.25rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
            {{ $isAr ? 'الخطوة 2 — إرسال الدفعة' : 'Step 2 — Send Payment' }}
        </div>

        <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.5rem;font-size:0.85rem;color:#92400e;display:flex;align-items:center;gap:0.5rem;">
            <i class="bi bi-clock-fill"></i>
            {{ $isAr ? 'السعر مُثبّت — ينتهي عند' : 'Rate locked — expires at' }} {{ $transaction->expires_at->format('H:i') }}
            &nbsp;<strong id="countdown"></strong>
        </div>

        <div class="row g-4 align-items-center mb-4">
            <div class="col-md-5 text-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($transaction->wallet_address) }}&bgcolor=ffffff&color=1e3a5f&margin=6"
                     alt="QR Code"
                     style="border-radius:12px;border:1px solid var(--border);padding:6px;background:#f8fafc;max-width:180px;">
                <div style="font-size:0.75rem;color:var(--muted);margin-top:0.5rem;">{{ $isAr ? 'اسحب للدفع' : 'Scan to pay' }}</div>
            </div>
            <div class="col-md-7">
                <div style="margin-bottom:1rem;">
                    <div style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ $isAr ? 'العملة' : 'Currency' }}</div>
                    <div style="font-size:1.1rem;font-weight:700;color:var(--navy);">{{ $transaction->currency }}</div>
                </div>
                <div style="margin-bottom:1rem;">
                    <div style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ $isAr ? 'المبلغ المطلوب إرساله' : 'Amount to send' }}</div>
                    <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;color:var(--gold);">
                        {{ rtrim(rtrim(number_format($transaction->amount_crypto, 8), '0'), '.') }} {{ $transaction->currency }}
                    </div>
                    <div style="font-size:0.75rem;color:var(--muted);">
                        ≈ ${{ number_format($transaction->amount_usd, 2) }} {{ $isAr ? 'بسعر' : 'at' }} ${{ number_format($transaction->crypto_price_usd, 2) }}/{{ $transaction->currency }}
                    </div>
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
                </div>
            </div>
        </div>

        <div style="border-top:1px solid var(--border);padding-top:1.25rem;">
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.9rem;color:var(--navy);margin-bottom:0.5rem;">
                {{ $isAr ? 'الخطوة 3 — إرسال رقم المعاملة' : 'Step 3 — Submit Transaction Hash' }}
            </div>
            <p style="color:var(--muted);font-size:0.82rem;margin-bottom:1rem;">
                {{ $isAr ? 'بعد الإرسال، الصق رقم المعاملة (TXID) أدناه لنتمكن من تأكيد دفعتك.' : 'After sending, paste your transaction hash (TXID) below so we can verify your payment.' }}
            </p>

            <form method="POST" action="{{ route('crypto.submit-tx', $transaction) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label-custom">{{ $isAr ? 'رقم المعاملة (TXID)' : 'Transaction Hash (TXID)' }}</label>
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
                    <input type="hidden" name="currency" value="{{ $transaction->currency }}">
                    <button type="submit"
                            style="background:none;border:none;color:var(--muted);font-size:0.8rem;cursor:pointer;text-decoration:underline;">
                        {{ $isAr ? 'تحديث السعر' : 'Refresh rate' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    @endif
</div>

@endsection

@push('scripts')
<script>
@if($transaction && $transaction->status === 'pending')
(function() {
    const expires = new Date('{{ $transaction->expires_at->toIso8601String() }}').getTime();
    const el = document.getElementById('countdown');
    if (!el) return;
    function tick() {
        const diff = Math.max(0, expires - Date.now());
        const m = Math.floor(diff / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.textContent = '(' + m + ':' + String(s).padStart(2, '0') + ' remaining)';
        if (diff > 0) setTimeout(tick, 1000);
        else el.textContent = '(Expired — please refresh rate)';
    }
    tick();
})();
@endif

function copyWallet() {
    const el = document.getElementById('walletAddr');
    navigator.clipboard.writeText(el.value).then(() => {
        const btn = el.nextElementSibling;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
        setTimeout(() => { btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy'; }, 2000);
    });
}
</script>
@endpush