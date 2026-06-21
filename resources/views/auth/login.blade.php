<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth_pages.sign_in') }} — {{ config('app.name', 'E-Services') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; overflow: hidden; }

        .left-panel {
            width: 45%;
            background: linear-gradient(150deg, #0f2544 0%, #1E3A5F 60%, #163059 100%);
            display: flex;
            flex-direction: column;
            padding: 2.5rem 3rem;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 320px; height: 320px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 240px; height: 240px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        .panel-logo { display: flex; align-items: center; gap: 0.6rem; text-decoration: none; }
        .logo-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.12);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .logo-text { color: #fff; font-size: 1.1rem; font-weight: 700; letter-spacing: 0.5px; }
        .logo-text span { color: #60A5FA; }

        .panel-content { flex: 1; display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 1; }
        .panel-headline { color: #fff; font-size: 2.2rem; font-weight: 700; line-height: 1.2; margin-bottom: 1rem; }
        .panel-sub { color: rgba(255,255,255,0.6); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem; }

        .feature-list { list-style: none; display: flex; flex-direction: column; gap: 1.1rem; }
        .feature-list li { display: flex; align-items: flex-start; gap: 0.9rem; }
        .fi-icon {
            width: 36px; height: 36px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 1rem;
        }
        .fi-text-title { color: #fff; font-weight: 500; font-size: 0.9rem; margin-bottom: 2px; }
        .fi-text-sub { color: rgba(255,255,255,0.5); font-size: 0.8rem; }

        .stat-cards { display: flex; gap: 1rem; position: relative; z-index: 1; }
        .stat-card {
            flex: 1; background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; padding: 1rem;
        }
        .stat-card .num { color: #fff; font-size: 1.4rem; font-weight: 700; }
        .stat-card .lbl { color: rgba(255,255,255,0.5); font-size: 0.75rem; margin-top: 2px; }

        .right-panel {
            flex: 1;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow-y: auto;
        }
        .form-card { width: 100%; max-width: 420px; }
        .form-card h1 { font-size: 1.7rem; font-weight: 700; color: #1E3A5F; margin-bottom: 0.3rem; }
        .form-card .subtitle { color: #6B7280; font-size: 0.9rem; margin-bottom: 2rem; }

        .form-label { font-weight: 600; font-size: 0.85rem; color: #374151; margin-bottom: 0.4rem; }
        .form-control {
            border: 1.5px solid #E5E7EB; border-radius: 8px;
            padding: 0.65rem 0.9rem; font-size: 0.95rem;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-control:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); outline: none; }

        .btn-submit {
            width: 100%; padding: 0.75rem; border: none;
            background: #1E3A5F; color: #fff;
            border-radius: 8px; font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .btn-submit:hover { background: #163059; }

        .btn-google {
            width: 100%; padding: 0.7rem; border: 1.5px solid #E5E7EB;
            background: #fff; color: #374151;
            border-radius: 8px; font-size: 0.9rem; font-weight: 500;
            cursor: pointer; transition: border-color 0.15s, box-shadow 0.15s;
            display: flex; align-items: center; justify-content: center; gap: 0.6rem;
            text-decoration: none;
        }
        .btn-google:hover { border-color: #D1D5DB; box-shadow: 0 1px 4px rgba(0,0,0,0.08); color: #374151; }

        .divider {
            text-align: center; color: #9CA3AF; font-size: 0.8rem;
            margin: 1.25rem 0; position: relative;
        }
        .divider::before, .divider::after {
            content: ''; position: absolute; top: 50%; width: 45%; height: 1px; background: #E5E7EB;
        }
        .divider::before { left: 0; }
        .divider::after { right: 0; }

        .security-note { display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 1.5rem; }
        .security-note span { font-size: 0.72rem; color: #9CA3AF; }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            body { overflow-y: auto; }
            .right-panel { min-height: 100vh; }
        }

        .lang-switch-auth { display:flex; align-items:center; gap:0.3rem; }
        .lang-switch-auth a { font-size:0.75rem; font-weight:600; padding:0.25rem 0.6rem; border-radius:6px; text-decoration:none; color:rgba(255,255,255,0.6); border:1px solid rgba(255,255,255,0.2); }
        .lang-switch-auth a.active { background:rgba(255,255,255,0.15); color:#fff; }

        html[dir="rtl"] body { font-family: 'Tahoma', 'Segoe UI', sans-serif; }
    </style>
</head>
<body>

    <!-- LEFT PANEL -->
    <aside class="left-panel">
        <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1;">
            <a href="{{ route('welcome') }}" class="panel-logo">
                <div class="logo-icon">
                    <i class="bi bi-building" style="color:#fff;font-size:1rem;"></i>
                </div>
                <span class="logo-text">E-<span>Services</span></span>
            </a>
            <div class="lang-switch-auth">
                <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">AR</a>
            </div>
        </div>

        <div class="panel-content">
            <h2 class="panel-headline">{{ __('auth_pages.welcome_back') }}<br>{{ __('auth_pages.welcome_back_2') }}</h2>
            <p class="panel-sub">{{ __('auth_pages.panel_sub') }}</p>

            <ul class="feature-list">
                <li>
                    <div class="fi-icon" style="background:rgba(4,120,87,0.18);">
                        <i class="bi bi-shield-check-fill" style="color:#34D399;"></i>
                    </div>
                    <div>
                        <div class="fi-text-title">{{ __('auth_pages.feature_security_title') }}</div>
                        <div class="fi-text-sub">{{ __('auth_pages.feature_security_sub') }}</div>
                    </div>
                </li>
                <li>
                    <div class="fi-icon" style="background:rgba(234,179,8,0.15);">
                        <i class="bi bi-lightning-fill" style="color:#FBBF24;"></i>
                    </div>
                    <div>
                        <div class="fi-text-title">{{ __('auth_pages.feature_dashboard_title') }}</div>
                        <div class="fi-text-sub">{{ __('auth_pages.feature_dashboard_sub') }}</div>
                    </div>
                </li>
                <li>
                    <div class="fi-icon" style="background:rgba(99,102,241,0.15);">
                        <i class="bi bi-bell-fill" style="color:#818CF8;"></i>
                    </div>
                    <div>
                        <div class="fi-text-title">{{ __('auth_pages.feature_notif_title') }}</div>
                        <div class="fi-text-sub">{{ __('auth_pages.feature_notif_sub') }}</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="num">50K+</div>
                <div class="lbl">{{ __('auth_pages.stat_citizens') }}</div>
            </div>
            <div class="stat-card">
                <div class="num">40+</div>
                <div class="lbl">{{ __('auth_pages.stat_services') }}</div>
            </div>
        </div>
    </aside>

    <!-- RIGHT PANEL -->
    <main class="right-panel">
        <div class="form-card">
            <h1>{{ __('auth_pages.sign_in') }}</h1>
            <p class="subtitle">{{ __('auth_pages.sign_in_subtitle') }}</p>

            @if(session('status'))
                <div class="alert alert-success alert-sm py-2 mb-3" style="font-size:.85rem">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('auth_pages.email_address') }}</label>
                    <input id="email" type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="you@example.com"
                           required autofocus autocomplete="email">
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label mb-0">{{ __('auth_pages.password') }}</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               style="font-size:.8rem;color:#2563EB;text-decoration:none;">
                                {{ __('auth_pages.forgot_password') }}
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                </div>

                <div class="form-check mb-3 mt-2">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:.85rem;color:#6B7280;">
                        {{ __('auth_pages.remember_me') }}
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bi bi-box-arrow-in-right"></i>
                    {{ __('auth_pages.sign_in_button') }}
                </button>
            </form>

            <div class="divider">{{ __('auth_pages.or_continue_with') }}</div>

            <a href="{{ route('auth.google') }}" class="btn-google">
                <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                {{ __('auth_pages.sign_in_with_google') }}
            </a>

            <p style="text-align:center;margin-top:1.5rem;font-size:.875rem;color:#6B7280;">
                {{ __('auth_pages.no_account') }}
                <a href="{{ route('register') }}" style="color:#2563EB;font-weight:600;text-decoration:none;">
                    {{ __('auth_pages.create_one_free') }}
                </a>
            </p>

            <div class="security-note">
                <i class="bi bi-shield-lock-fill" style="color:#9CA3AF;font-size:.8rem;"></i>
                <span>{{ __('auth_pages.security_note') }}</span>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
