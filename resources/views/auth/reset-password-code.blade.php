<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password — {{ config('app.name', 'E-Services') }}</title>
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
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.3);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }

        h1 { font-size: 1.4rem; font-weight: 700; color: #fff; text-align: center; margin-bottom: 0.4rem; }
        .subtitle { color: #94A3B8; font-size: 0.875rem; text-align: center; margin-bottom: 1.75rem; }

        .alert-danger-custom {
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
            color: #fca5a5; border-radius: 10px; padding: 0.75rem 1rem;
            font-size: 0.85rem; margin-bottom: 1.25rem;
        }

        .alert-danger-custom ul { margin: 0; padding-left: 1.25rem; }

        .field-label { display: block; font-size: 0.82rem; font-weight: 600; color: #94A3B8; margin-bottom: 0.5rem; }

        .input-wrap { position: relative; margin-bottom: 1.25rem; }

        .field-input {
            width: 100%; background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 10px; padding: 0.75rem 2.75rem 0.75rem 2.5rem;
            color: #fff; font-size: 0.95rem; transition: all 0.2s; outline: none;
        }

        .field-input::placeholder { color: rgba(148,163,184,0.45); }

        .field-input:focus {
            border-color: #818CF8;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
            background: rgba(255,255,255,0.07);
        }

        .input-icon {
            position: absolute; left: 0.875rem; top: 50%;
            transform: translateY(-50%); color: #64748B; font-size: 0.9rem;
        }

        .pw-toggle {
            position: absolute; right: 0.875rem; top: 50%;
            transform: translateY(-50%); background: none; border: none;
            color: #64748B; cursor: pointer; font-size: 0.9rem; padding: 0; transition: color 0.2s;
        }

        .pw-toggle:hover { color: #fff; }

        .pw-strength { margin-top: -0.75rem; margin-bottom: 1.25rem; }

        .pw-bar-wrap { height: 4px; background: rgba(255,255,255,0.08); border-radius: 100px; overflow: hidden; margin-bottom: 0.3rem; }
        .pw-bar-fill { height: 100%; width: 0; border-radius: 100px; transition: width 0.3s, background 0.3s; }

        .pw-bar-label { font-size: 0.73rem; color: #64748B; }

        .requirements {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 10px; padding: 0.875rem 1rem;
            margin-bottom: 1.25rem;
        }

        .requirements p { font-size: 0.78rem; color: #64748B; margin-bottom: 0.5rem; }
        .requirements ul { margin: 0; padding-left: 1.1rem; }
        .requirements li { font-size: 0.78rem; color: #64748B; line-height: 1.8; }

        .btn-submit {
            width: 100%; padding: 0.8rem;
            background: #4F46E5; border: 1px solid rgba(99,102,241,0.4);
            border-radius: 10px; color: #fff; font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }

        .btn-submit:hover { background: #4338CA; }
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
        <i class="bi bi-lock-fill" style="color:#A5B4FC;font-size:1.4rem;"></i>
    </div>

    <h1>Set New Password</h1>
    <p class="subtitle">Choose a strong password for your account.</p>

    @if($errors->any())
        <div class="alert-danger-custom">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.code.reset') }}" method="POST">
        @csrf

        <label class="field-label">New Password</label>
        <div class="input-wrap">
            <i class="bi bi-lock-fill input-icon"></i>
            <input type="password" name="password" id="pw"
                   class="field-input"
                   placeholder="Create a strong password"
                   required autocomplete="new-password">
            <button type="button" class="pw-toggle" onclick="togglePw('pw','pwIcon')">
                <i class="bi bi-eye-fill" id="pwIcon"></i>
            </button>
        </div>

        <div class="pw-strength">
            <div class="pw-bar-wrap">
                <div class="pw-bar-fill" id="pwBar"></div>
            </div>
            <span class="pw-bar-label" id="pwLabel">Password strength</span>
        </div>

        <label class="field-label">Confirm Password</label>
        <div class="input-wrap">
            <i class="bi bi-lock-fill input-icon"></i>
            <input type="password" name="password_confirmation" id="pw2"
                   class="field-input"
                   placeholder="Repeat your password"
                   required autocomplete="new-password">
            <button type="button" class="pw-toggle" onclick="togglePw('pw2','pw2Icon')">
                <i class="bi bi-eye-fill" id="pw2Icon"></i>
            </button>
        </div>

        <div class="requirements">
            <p>Password must contain:</p>
            <ul>
                <li>Minimum 8 characters</li>
                <li>At least 1 uppercase letter (A–Z)</li>
                <li>At least 1 lowercase letter (a–z)</li>
                <li>At least 1 number (0–9)</li>
                <li>At least 1 special character (@$!%*#?&amp;)</li>
            </ul>
        </div>

        <button type="submit" class="btn-submit">
            <i class="bi bi-floppy-fill"></i>
            Save New Password
        </button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePw(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
}

document.getElementById('pw').addEventListener('input', function () {
    const v = this.value;
    let score = 0;
    if (v.length >= 8)        score++;
    if (/[A-Z]/.test(v))     score++;
    if (/[0-9]/.test(v))     score++;
    if (/[@$!%*?&#]/.test(v)) score++;

    const map = [
        { w: '0%',   bg: '',                   t: '' },
        { w: '25%',  bg: '#ef4444',             t: 'Weak' },
        { w: '50%',  bg: '#f59e0b',             t: 'Fair' },
        { w: '75%',  bg: '#3b82f6',             t: 'Good' },
        { w: '100%', bg: '#10b981',             t: 'Strong' },
    ];

    document.getElementById('pwBar').style.width      = map[score].w;
    document.getElementById('pwBar').style.background = map[score].bg;
    document.getElementById('pwLabel').textContent    = map[score].t;
    document.getElementById('pwLabel').style.color    = map[score].bg || '#64748B';
});
</script>
</body>
</html>
