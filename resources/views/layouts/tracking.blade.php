<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? 'تتبع الطلب' : 'Track Request' }} — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #F0F4F8; font-family: 'Segoe UI', sans-serif; }
        html[dir="rtl"] body { font-family: 'Tahoma', 'Segoe UI', sans-serif; }
        .brand-bar { background: #1E3A5F; }
        .status-pending     { background: #FEF3C7; color: #92400E; }
        .status-in_progress { background: #DBEAFE; color: #1E40AF; }
        .status-completed   { background: #DCFCE7; color: #166534; }
        .lang-switch-tracking a { font-size:0.78rem; font-weight:600; padding:0.25rem 0.6rem; border-radius:6px; text-decoration:none; color:rgba(255,255,255,0.7); border:1px solid rgba(255,255,255,0.25); margin-inline-end:0.3rem; }
        .lang-switch-tracking a.active { background:rgba(255,255,255,0.18); color:#fff; }
    </style>
</head>
<body>
    <nav class="brand-bar py-3 mb-5">
        <div class="container d-flex align-items-center justify-content-between">
            <span class="text-white fw-bold fs-5">{{ config('app.name', 'E-Services') }}</span>
            <div class="d-flex align-items-center gap-2">
                <span class="lang-switch-tracking">
                    <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">AR</a>
                </span>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In' }}</a>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
