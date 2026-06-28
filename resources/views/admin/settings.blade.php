@extends('admin.layouts.app')

@section('title', 'Platform Settings')
@section('page-title', 'Platform Settings')

@section('content')

<div style="max-width:680px;">

    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="width:44px;height:44px;background:rgba(214,158,46,0.12);border:1px solid rgba(214,158,46,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-gear-fill" style="color:var(--gold);font-size:1.1rem;"></i>
        </div>
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.4rem;color:#fff;margin:0;">Platform Settings</h1>
            <p style="color:var(--muted);font-size:0.83rem;margin:0;">Configure exchange rates and platform-wide options</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.35);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#6ee7b7;display:flex;align-items:center;gap:0.75rem;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        {{-- LBP / USD Exchange Rate --}}
        <div class="admin-card mb-4">
            <div style="padding:1.5rem 1.75rem;border-bottom:1px solid var(--border);">
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.25rem;">
                    <i class="bi bi-currency-exchange" style="color:var(--gold);"></i>
                    <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;color:#fff;">Currency Exchange Rate</span>
                </div>
                <p style="color:var(--muted);font-size:0.83rem;margin:0;">
                    Used to convert LBP service fees to USD when processing Stripe card payments.
                </p>
            </div>
            <div style="padding:1.5rem 1.75rem;">
                <label class="form-label-custom">LBP per 1 USD</label>
                <div style="display:flex;align-items:center;gap:0.75rem;max-width:320px;">
                    <div style="position:relative;flex:1;">
                        <span style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;font-weight:600;">ل.ل</span>
                        <input type="number"
                               name="lbp_usd_rate"
                               value="{{ old('lbp_usd_rate', $lbpRate) }}"
                               min="1"
                               step="1"
                               class="form-control-custom"
                               style="padding-left:2.6rem;"
                               required>
                    </div>
                    <span style="color:var(--muted);font-size:0.85rem;white-space:nowrap;">= $1 USD</span>
                </div>
                @error('lbp_usd_rate')
                    <div style="color:#f87171;font-size:0.8rem;margin-top:6px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
                <p style="color:var(--muted);font-size:0.78rem;margin-top:0.6rem;">
                    Current rate: <strong style="color:var(--text);">{{ number_format((float)$lbpRate) }} LBP = $1 USD</strong>
                    &nbsp;·&nbsp;
                    Example: a 500,000 LBP service = ${{ number_format(500000 / (float)$lbpRate, 2) }} USD charged to card
                </p>
            </div>
        </div>

        {{-- USDT Wallet Address --}}
        <div class="admin-card mb-4">
            <div style="padding:1.5rem 1.75rem;border-bottom:1px solid var(--border);">
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.25rem;">
                    <i class="bi bi-wallet2" style="color:var(--gold);"></i>
                    <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;color:#fff;">USDT Wallet Address</span>
                </div>
                <p style="color:var(--muted);font-size:0.83rem;margin:0;">
                    The wallet address citizens send USDT to when paying with cryptocurrency.
                </p>
            </div>
            <div style="padding:1.5rem 1.75rem;">
                <label class="form-label-custom">USDT Wallet Address (TRC-20 / ERC-20)</label>
                <input type="text"
                       name="usdt_wallet"
                       value="{{ old('usdt_wallet', $usdtWallet) }}"
                       class="form-control-custom"
                       style="font-family:monospace;font-size:0.85rem;"
                       placeholder="e.g. TRx7NqTHkMBpvVkALRKhkNXdFUPcmEzDpM">
                @error('usdt_wallet')
                    <div style="color:#f87171;font-size:0.8rem;margin-top:6px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
                <p style="color:var(--muted);font-size:0.78rem;margin-top:0.5rem;">
                    <i class="bi bi-info-circle me-1"></i>Make sure citizens know which network to use (TRC-20 is recommended for low fees).
                </p>
            </div>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-gold">
                <i class="bi bi-floppy-fill"></i> Save Settings
            </button>
        </div>

    </form>

</div>

@endsection
