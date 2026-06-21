<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — {{ config('app.name', 'E-Services') }}</title>
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
            max-width: 460px;
            position: relative;
            z-index: 1;
            text-align: center;
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
            width: 72px; height: 72px; border-radius: 50%;
            background: rgba(37,99,235,0.12);
            border: 1px solid rgba(37,99,235,0.25);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
        }

        h1 { font-size: 1.4rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem; }

        .subtitle { color: #94A3B8; font-size: 0.875rem; margin-bottom: 1.75rem; line-height: 1.6; }

        .alert-success-custom {
            background: rgba(4,120,87,0.12);
            border: 1px solid rgba(4,120,87,0.3);
            color: #6ee7b7; border-radius: 10px;
            padding: 0.75rem 1rem; font-size: 0.85rem;
            margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: 0.5rem;
        }

        .btn-submit {
            width: 100%; padding: 0.8rem;
            background: #1E3A5F; border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px; color: #fff; font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }

        .btn-submit:hover { background: #163059; }

        .logout-link {
            display: block; color: #64748B; font-size: 0.82rem;
            text-decoration: none; margin-top: 1.25rem; transition: color 0.2s;
        }

        .logout-link:hover { color: #94A3B8; }
    </style>
</head>
<body>

<div class="auth-card">

    <a href="/" class="logo-wrap">
        <div class="logo-icon">
            <i class="bi bi-building" style="color:#fff;font-size:0.9rem;"></i>
        </div>
        <span class="logo-text">E-<span>Services</span></span>
    </a>

    <div class="icon-ring">
        <i class="bi bi-envelope-fill" style="color:#93C5FD;font-size:1.8rem;"></i>
    </div>

    <h1>Verify Your Email</h1>
    <p class="subtitle">
        Before you can access your account, please verify your email address by clicking the link we sent you.
        <br>
        If you didn't receive the email, you can request another one below.
    </p>

    @if(session('resent'))
        <div class="alert-success-custom">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            A fresh verification link has been sent to your email address.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn-submit">
            <i class="bi bi-send-fill"></i>
            Resend Verification Email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="margin-top:1.25rem;">
        @csrf
        <button type="submit" class="logout-link" style="background:none;border:none;cursor:pointer;width:100%;font-family:inherit;">
            <i class="bi bi-box-arrow-right me-1"></i> Sign out
        </button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
