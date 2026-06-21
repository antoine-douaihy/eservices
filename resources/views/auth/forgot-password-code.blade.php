<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — {{ config('app.name', 'E-Services') }}</title>
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
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .logo-wrap {
            display: flex; align-items: center; gap: 0.6rem;
            text-decoration: none; margin-bottom: 2rem; justify-content: center;
        }

        .logo-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.08);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .logo-text { color: #fff; font-size: 1.05rem; font-weight: 700; }
        .logo-text span { color: #60A5FA; }

        .icon-ring {
            width: 60px; height: 60px; border-radius: 50%;
            background: rgba(214,158,46,0.12);
            border: 1px solid rgba(214,158,46,0.25);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }

        h1 { font-size: 1.4rem; font-weight: 700; color: #fff; text-align: center; margin-bottom: 0.4rem; }

        .subtitle { color: #94A3B8; font-size: 0.875rem; text-align: center; margin-bottom: 1.75rem; line-height: 1.5; }

        .alert-danger-custom {
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
            color: #fca5a5; border-radius: 10px; padding: 0.75rem 1rem;
            font-size: 0.85rem; margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: 0.5rem;
        }

        .field-label { display: block; font-size: 0.82rem; font-weight: 600; color: #94A3B8; margin-bottom: 0.5rem; }

        .field-input {
            width: 100%; background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 10px; padding: 0.75rem 0.75rem 0.75rem 2.5rem;
            color: #fff; font-size: 0.95rem; transition: all 0.2s; outline: none;
        }

        .field-input::placeholder { color: rgba(148,163,184,0.45); }

        .field-input:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
            background: rgba(255,255,255,0.07);
        }

        .input-wrap { position: relative; margin-bottom: 1.5rem; }

        .input-wrap i {
            position: absolute; left: 0.875rem; top: 50%;
            transform: translateY(-50%); color: #64748B; font-size: 0.9rem;
        }

        .btn-submit {
            width: 100%; padding: 0.8rem;
            background: #1E3A5F; border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px; color: #fff; font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }

        .btn-submit:hover { background: #163059; }

        .back-link {
            display: block; text-align: center; color: #64748B;
            font-size: 0.82rem; text-decoration: none; margin-top: 1.25rem; transition: color 0.2s;
        }

        .back-link:hover { color: #94A3B8; }
    </style>
</head>
<body>

<div class="auth-card">

    <a href="{{ route('login') }}" class="logo-wrap">
        <div class="logo-icon">
            <i class="bi bi-building" style="color:#fff;font-size:0.9rem;"></i>
        </div>
        <span class="logo-text">E-<span>Services</span></span>
    </a>

    <div class="icon-ring">
        <i class="bi bi-key-fill" style="color:#D69E2E;font-size:1.4rem;"></i>
    </div>

    <h1>Forgot Password</h1>
    <p class="subtitle">Enter your email address and we'll send you a verification code to reset your password.</p>

    @if($errors->any())
        <div class="alert-danger-custom">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.code.send') }}" method="POST">
        @csrf
        <label class="field-label">Email Address</label>
        <div class="input-wrap">
            <i class="bi bi-envelope-fill"></i>
            <input type="email" name="email" class="field-input"
                   placeholder="you@example.com"
                   value="{{ old('email') }}"
                   required autofocus autocomplete="email">
        </div>

        <button type="submit" class="btn-submit">
            <i class="bi bi-send-fill"></i>
            Send Reset Code
        </button>
    </form>

    <a href="{{ route('login') }}" class="back-link">
        <i class="bi bi-arrow-left me-1"></i> Back to login
    </a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
