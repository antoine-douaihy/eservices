<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — {{ config('app.name', 'E-Services') }}</title>
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
        }

        .logo-wrap {
            display: flex; align-items: center; gap: 0.6rem;
            text-decoration: none; margin-bottom: 1.75rem; justify-content: center;
        }

        .logo-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.08);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .logo-text { color: #fff; font-size: 1.05rem; font-weight: 700; }
        .logo-text span { color: #60A5FA; }

        h1 { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 0.3rem; }
        .subtitle { color: #94A3B8; font-size: 0.875rem; margin-bottom: 1.75rem; }

        .alert-danger-custom {
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
            color: #fca5a5; border-radius: 10px; padding: 0.75rem 1rem;
            font-size: 0.85rem; margin-bottom: 1.25rem;
        }

        .alert-danger-custom ul { margin: 0; padding-left: 1.25rem; }

        .field-label { display: block; font-size: 0.82rem; font-weight: 600; color: #94A3B8; margin-bottom: 0.45rem; }

        .field-input {
            width: 100%; background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 10px; padding: 0.7rem 0.9rem;
            color: #fff; font-size: 0.95rem; transition: all 0.2s; outline: none;
        }

        .field-input::placeholder { color: rgba(148,163,184,0.45); }

        .field-input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
            background: rgba(255,255,255,0.07);
        }

        .field-input.is-invalid { border-color: rgba(239,68,68,0.5); }

        .form-hint { font-size: 0.76rem; color: #64748B; margin-top: 0.3rem; }

        .btn-submit {
            width: 100%; padding: 0.8rem;
            background: #1E3A5F; border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px; color: #fff; font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background 0.2s; margin-top: 0.5rem;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }

        .btn-submit:hover { background: #163059; }

        .bottom-note { text-align: center; margin-top: 1.25rem; font-size: 0.875rem; color: #64748B; }
        .bottom-note a { color: #2563EB; font-weight: 600; text-decoration: none; }
        .bottom-note a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="auth-card">

    <a href="{{ route('welcome') }}" class="logo-wrap">
        <div class="logo-icon">
            <i class="bi bi-building" style="color:#fff;font-size:0.9rem;"></i>
        </div>
        <span class="logo-text">E-<span>Services</span></span>
    </a>

    <h1>Create an Account</h1>
    <p class="subtitle">Register to access government services online</p>

    @if($errors->any())
        <div class="alert-danger-custom">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="field-label">First Name <span style="color:#f87171;">*</span></label>
                <input type="text" name="first_name"
                       class="field-input @error('first_name') is-invalid @enderror"
                       value="{{ old('first_name') }}" required autofocus placeholder="Ahmad">
            </div>
            <div class="col-6">
                <label class="field-label">Last Name <span style="color:#f87171;">*</span></label>
                <input type="text" name="last_name"
                       class="field-input @error('last_name') is-invalid @enderror"
                       value="{{ old('last_name') }}" required placeholder="Khalil">
            </div>
        </div>

        <div class="mb-3">
            <label class="field-label">Email Address <span style="color:#f87171;">*</span></label>
            <input type="email" name="email"
                   class="field-input @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required placeholder="you@example.com">
        </div>

        <div class="mb-3">
            <label class="field-label">Password <span style="color:#f87171;">*</span></label>
            <input type="password" name="password"
                   class="field-input @error('password') is-invalid @enderror"
                   required placeholder="Min. 8 characters">
            <span class="form-hint">Minimum 8 characters</span>
        </div>

        <div class="mb-3">
            <label class="field-label">Confirm Password <span style="color:#f87171;">*</span></label>
            <input type="password" name="password_confirmation"
                   class="field-input" required placeholder="Repeat your password">
        </div>

        <button type="submit" class="btn-submit">
            <i class="bi bi-person-check-fill"></i>
            Create Account
        </button>
    </form>

    <p class="bottom-note">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </p>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
