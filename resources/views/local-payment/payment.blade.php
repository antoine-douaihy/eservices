@extends('layouts.app')

@section('title', 'Local Payment')
@section('page-title', 'Local Payment')

@section('content')

<div style="max-width:680px;margin:0 auto;">

    {{-- Back + Header --}}
    <div style="margin-bottom:1.75rem;">
        <a href="{{ route('citizen.payment.select', $citizenRequest) }}"
           style="color:var(--muted);text-decoration:none;font-size:0.82rem;display:inline-flex;align-items:center;gap:0.4rem;margin-bottom:0.75rem;transition:color 0.2s;"
           onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--muted)'">
            <i class="bi bi-arrow-left"></i> Back to Payment Methods
        </a>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0 0 0.25rem;">
            Pay via Wish / OMT
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">
            Service: <strong style="color:var(--text);">{{ $citizenRequest->service->name }}</strong>
            &mdash; {{ $citizenRequest->office->name }}
        </p>
    </div>

    @if(session('success'))
        <div style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.35);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#6ee7b7;display:flex;align-items:center;gap:0.75rem;">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($citizenRequest->payment_status === 'paid')
        <div class="app-card" style="text-align:center;padding:3rem 2rem;">
            <div style="width:64px;height:64px;background:rgba(4,120,87,0.2);border:1px solid rgba(4,120,87,0.35);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-check-lg" style="color:#6ee7b7;font-size:1.8rem;"></i>
            </div>
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.1rem;color:#fff;margin-bottom:0.5rem;">Payment Confirmed</div>
            <p style="color:var(--muted);margin:0;">This request has already been paid.</p>
        </div>
    @else

    {{-- Step 1: Choose method --}}
    @if(!$pending)
    <div class="app-card mb-4">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;margin-bottom:1.25rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
            Step 1 — Choose Payment Method
        </div>

        <div style="background:rgba(214,158,46,0.08);border:1px solid rgba(214,158,46,0.2);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;">
            Amount due: <span style="font-family:'Syne',sans-serif;font-weight:800;color:var(--gold);font-size:1.1rem;">${{ number_format($citizenRequest->service->price, 2) }} USD</span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6">
                <div style="background:rgba(111,66,193,0.08);border:1px solid rgba(111,66,193,0.2);border-radius:12px;padding:1.25rem;text-align:center;height:100%;display:flex;flex-direction:column;justify-content:center;">
                    <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;color:#c084fc;margin-bottom:0.4rem;">Wish</div>
                    <div style="font-size:0.78rem;color:var(--muted);">Send via the Wish Money app to our registered account</div>
                </div>
            </div>
            <div class="col-6">
                <div style="background:rgba(37,99,235,0.08);border:1px solid rgba(37,99,235,0.2);border-radius:12px;padding:1.25rem;text-align:center;height:100%;display:flex;flex-direction:column;justify-content:center;">
                    <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;color:#93c5fd;margin-bottom:0.4rem;">OMT</div>
                    <div style="font-size:0.78rem;color:var(--muted);">Transfer via any OMT agent using our account number</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('local-payment.initiate', $citizenRequest) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label-custom">Select method</label>
                <select name="method" class="form-select-custom @error('method') is-invalid @enderror" required>
                    <option value="">— Choose —</option>
                    <option value="wish">Wish Money</option>
                    <option value="omt">OMT</option>
                </select>
                @error('method')
                    <div style="font-size:0.75rem;color:#f87171;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn-gold w-100" style="justify-content:center;">
                <i class="bi bi-arrow-right-circle-fill"></i> Show Account Details
            </button>
        </form>
    </div>
    @endif

    {{-- Step 2: Account details --}}
    @if($pending)
    <div class="app-card mb-4">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;margin-bottom:1.25rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
            Step 2 — Transfer to our
            <span style="color:{{ $pending->method === 'wish' ? '#c084fc' : '#93c5fd' }};">
                {{ strtoupper($pending->method) }}
            </span>
            account
        </div>

        <div style="background:rgba(96,165,250,0.08);border:1px solid rgba(96,165,250,0.2);border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.5rem;font-size:0.85rem;color:#93c5fd;display:flex;align-items:center;gap:0.5rem;">
            <i class="bi bi-info-circle-fill"></i>
            Transfer exactly <strong style="margin:0 0.25rem;">${{ number_format($pending->amount_usd, 2) }} USD</strong> to the account below, then submit your reference number.
        </div>

        {{-- Account box --}}
        <div style="background:rgba(255,255,255,0.04);border:1px solid var(--border);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;text-align:center;">
            <div style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">
                @if($pending->method === 'wish') Wish Money Account @else OMT Account Number @endif
            </div>
            <div id="accountDetails" style="font-family:monospace;font-size:1.4rem;font-weight:700;color:#fff;letter-spacing:0.05em;margin-bottom:0.75rem;">
                {{ $pending->account_details }}
            </div>
            <button onclick="copyAccount(event)" class="btn-ghost" style="padding:0.35rem 1rem;font-size:0.8rem;">
                <i class="bi bi-clipboard"></i> Copy
            </button>
        </div>

        {{-- How to pay --}}
        <div style="margin-bottom:1.5rem;">
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.9rem;color:#fff;margin-bottom:0.75rem;">How to pay</div>
            @if($pending->method === 'wish')
            <ol style="color:var(--muted);font-size:0.83rem;margin:0;padding-left:1.25rem;display:flex;flex-direction:column;gap:0.4rem;">
                <li>Open the <strong style="color:var(--text);">Wish Money</strong> app on your phone</li>
                <li>Tap <strong style="color:var(--text);">Send Money</strong> and enter the account number above</li>
                <li>Enter the exact amount: <strong style="color:var(--gold);">${{ number_format($pending->amount_usd, 2) }}</strong></li>
                <li>Complete the transfer and copy the <strong style="color:var(--text);">Transaction Reference</strong> shown</li>
                <li>Paste it in the field below</li>
            </ol>
            @else
            <ol style="color:var(--muted);font-size:0.83rem;margin:0;padding-left:1.25rem;display:flex;flex-direction:column;gap:0.4rem;">
                <li>Visit any <strong style="color:var(--text);">OMT agent</strong> near you</li>
                <li>Tell the agent to send <strong style="color:var(--gold);">${{ number_format($pending->amount_usd, 2) }}</strong> to account <strong style="color:var(--text);">{{ $pending->account_details }}</strong></li>
                <li>The agent will give you a <strong style="color:var(--text);">Reference / PIN</strong> — keep it</li>
                <li>Enter that reference in the field below</li>
            </ol>
            @endif
        </div>

        {{-- Step 3: Submit reference --}}
        <div style="border-top:1px solid var(--border);padding-top:1.25rem;">
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.9rem;color:#fff;margin-bottom:0.5rem;">
                Step 3 — Submit Your Reference Number
            </div>
            <p style="color:var(--muted);font-size:0.82rem;margin-bottom:1rem;">
                After completing the transfer, paste your reference or PIN below to confirm.
            </p>

            <form method="POST" action="{{ route('local-payment.submit', $pending) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label-custom">
                        @if($pending->method === 'wish') Transaction Reference @else OMT Reference / PIN @endif
                    </label>
                    <input type="text" name="reference_number"
                           class="form-control-custom @error('reference_number') is-invalid @enderror"
                           style="font-family:monospace;"
                           placeholder="e.g. 12345678"
                           value="{{ old('reference_number') }}" required>
                    @error('reference_number')
                        <div style="font-size:0.75rem;color:#f87171;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn-emerald w-100" style="justify-content:center;padding:0.65rem;">
                    <i class="bi bi-check-circle-fill"></i> Confirm Payment
                </button>
            </form>

            <div style="text-align:center;margin-top:1rem;">
                <form method="POST" action="{{ route('local-payment.initiate', $citizenRequest) }}">
                    @csrf
                    <input type="hidden" name="method" value="{{ $pending->method }}">
                    <button type="submit"
                            style="background:none;border:none;color:var(--muted);font-size:0.8rem;cursor:pointer;text-decoration:underline;">
                        Switch payment method
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
function copyAccount(e) {
    const text = document.getElementById('accountDetails').textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const btn = e.currentTarget;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
        setTimeout(() => { btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy'; }, 2000);
    });
}
</script>
@endpush
