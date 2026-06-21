<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Your Account — {{ config('app.name', 'E-Services') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0d1f3c;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(4,120,87,0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(214,158,46,0.06) 0%, transparent 50%);
            pointer-events: none;
        }

        .auth-card {
            background: #162947;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }

        .logo-wrap {
            display: flex; align-items: center; gap: 0.6rem;
            text-decoration: none; margin-bottom: 2rem; justify-content: center;
        }
        .logo-icon {
            width: 36px; height: 36px; background: rgba(255,255,255,0.08);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
        }
        .logo-text { color: #fff; font-size: 1.05rem; font-weight: 700; letter-spacing: 0.5px; }
        .logo-text span { color: #60A5FA; }

        .icon-ring {
            width: 60px; height: 60px; border-radius: 50%;
            background: rgba(214,158,46,0.12); border: 1px solid rgba(214,158,46,0.3);
            display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;
        }

        h1 { font-size: 1.4rem; font-weight: 700; color: #fff; text-align: center; margin-bottom: 0.4rem; }
        .subtitle { color: #94A3B8; font-size: 0.875rem; text-align: center; margin-bottom: 1.75rem; line-height: 1.5; }

        .welcome-box {
            background: rgba(214,158,46,0.08); border: 1px solid rgba(214,158,46,0.25);
            border-radius: 10px; padding: 0.875rem 1.1rem; margin-bottom: 1.5rem;
            font-size: 0.82rem; color: #fcd34d; display: flex; gap: 0.6rem; align-items: flex-start;
        }

        .alert-success-custom {
            background: rgba(4,120,87,0.12); border: 1px solid rgba(4,120,87,0.3); color: #6ee7b7;
            border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.85rem; margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .alert-danger-custom {
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); color: #fca5a5;
            border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.85rem; margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: 0.5rem;
        }

        .field-label { display: block; font-size: 0.82rem; font-weight: 600; color: #94A3B8; margin-bottom: 0.5rem; }

        .code-input {
            width: 100%; background: rgba(255,255,255,0.05); border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 12px; padding: 0.9rem; color: #fff; font-size: 1.8rem; font-weight: 700;
            text-align: center; letter-spacing: 0.4em; font-family: 'Courier New', monospace;
            transition: border-color 0.2s, box-shadow 0.2s; outline: none; margin-bottom: 1.25rem;
        }
        .code-input::placeholder { color: rgba(148,163,184,0.35); letter-spacing: 0.3em; }
        .code-input:focus { border-color: #d69e2e; box-shadow: 0 0 0 3px rgba(214,158,46,0.15); background: rgba(255,255,255,0.07); }

        .btn-primary-custom {
            width: 100%; padding: 0.8rem; background: #d69e2e; border: none; border-radius: 10px;
            color: #0d1f3c; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .btn-primary-custom:hover { background: #f6d860; }

        .btn-outline-custom {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;
            color: #94A3B8; font-size: 0.85rem; cursor: pointer; padding: 0.5rem 1rem; transition: all 0.2s;
        }
        .btn-outline-custom:hover { background: rgba(255,255,255,0.08); color: #fff; }

        .divider { border: none; border-top: 1px solid rgba(255,255,255,0.07); margin: 1.5rem 0; }

        .back-link {
            display: block; text-align: center; color: #64748B; font-size: 0.82rem;
            text-decoration: none; margin-top: 1.25rem; transition: color 0.2s;
        }
        .back-link:hover { color: #94A3B8; }
    </style>
</head>
<body>

<div class="auth-card">

    <a href="{{ route('welcome') }}" class="logo-wrap">
        <div class="logo-icon"><i class="bi bi-building" style="color:#fff;font-size:0.9rem;"></i></div>
        <span class="logo-text">E-<span>Services</span></span>
    </a>

    <div class="icon-ring">
        <i class="bi bi-envelope-check-fill" style="color:#fcd34d;font-size:1.4rem;"></i>
    </div>

    <h1>Verify Your New Account</h1>
    <p class="subtitle">A 6-digit verification code was sent to {{ $user->email }}.</p>

    <div class="welcome-box">
        <i class="bi bi-info-circle-fill flex-shrink-0" style="margin-top:1px;"></i>
        <div>Your administrator created this account for you. Enter the code below to verify it's really you, then you'll choose your own permanent password.</div>
    </div>

    @if(session('status'))
        <div class="alert-success-custom">
            <i class="bi bi-envelope-check-fill flex-shrink-0"></i>
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-danger-custom">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('first-login.verify') }}">
        @csrf
        <label class="field-label">Verification Code</label>
        <input type="text"
               name="code"
               class="code-input"
               maxlength="6"
               placeholder="000000"
               inputmode="numeric"
               autocomplete="one-time-code"
               autofocus>

        <button type="submit" class="btn-primary-custom">
            <i class="bi bi-shield-check-fill"></i>
            Verify Account
        </button>
    </form>

    <hr class="divider">
    <div style="text-align:center;">
        <p style="color:#64748B;font-size:0.82rem;margin-bottom:0.6rem;">Didn't get the email?</p>
        <form method="POST" action="{{ route('first-login.resend') }}">
            @csrf
            <button type="submit" class="btn-outline-custom">
                <i class="bi bi-arrow-clockwise me-1"></i> Resend code
            </button>
        </form>
    </div>

    <a href="{{ route('login') }}" class="back-link">
        <i class="bi bi-arrow-left me-1"></i> Back to login
    </a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
