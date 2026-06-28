@extends('layouts.app')

@section('title', '2FA Setup')
@section('page-title', '2FA Setup')

@section('content')

<div style="max-width:480px;margin:3rem auto;">

    <div class="app-card">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.1rem;color:#fff;margin-bottom:0.4rem;">
            Set Up Authenticator App
        </div>
        <p style="color:var(--muted);font-size:0.875rem;margin-bottom:1.75rem;">
            Scan this QR code with Google Authenticator or Authy, then enter the 6-digit code to confirm.
        </p>

        <div style="text-align:center;margin-bottom:1.5rem;">
            <div style="display:inline-block;border-radius:12px;border:1px solid var(--border);padding:8px;background:#ffffff;">
                {!! $qrSvg !!}
            </div>
        </div>

        <div style="text-align:center;margin-bottom:1.5rem;font-size:0.82rem;color:var(--muted);">
            Or enter this key manually:
            <code style="background:rgba(255,255,255,0.06);border:1px solid var(--border);border-radius:6px;padding:0.15rem 0.5rem;color:var(--gold);font-size:0.82rem;letter-spacing:0.05em;">{{ $secret }}</code>
        </div>

        @if($errors->any())
            <div style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.25rem;font-size:0.875rem;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.confirm') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label-custom">Enter the 6-digit code from your app</label>
                <input type="text" name="code"
                       class="form-control-custom"
                       style="text-align:center;font-size:1.3rem;font-family:'Syne',sans-serif;font-weight:700;letter-spacing:0.25em;"
                       maxlength="6" placeholder="— — — — — —"
                       autocomplete="one-time-code" autofocus>
            </div>
            <button type="submit" class="btn-gold w-100" style="justify-content:center;">
                <i class="bi bi-shield-check-fill"></i> Confirm & Enable 2FA
            </button>
        </form>

        <div style="text-align:center;margin-top:1.25rem;">
            <a href="{{ route('profile.edit') }}"
               style="color:var(--muted);text-decoration:none;font-size:0.82rem;transition:color 0.2s;"
               onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--muted)'">
                <i class="bi bi-arrow-left me-1"></i> Back to Profile
            </a>
        </div>
    </div>

</div>

@endsection
