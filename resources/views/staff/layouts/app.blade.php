<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff Portal') — E-Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --navy:          #0d1f3c;
            --navy-2:        #162947;
            --emerald:       #047857;
            --emerald-light: #059669;
            --gold:          #d69e2e;
            --gold-light:    #f6d860;
            --surface:       #111c30;
            --card:          rgba(255,255,255,0.04);
            --border:        rgba(255,255,255,0.08);
            --text:          #e2e8f0;
            --muted:         #94a3b8;
            --sidebar-w:     260px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        * { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent; }

        body {
            background-color: var(--navy);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 0;
        }

        /* SIDEBAR */
        .staff-sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh; height: 100dvh;
            background: rgba(13,31,60,0.97);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            z-index: 200; overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            touch-action: pan-y;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 0.75rem;
            text-decoration: none;
        }

        .sidebar-brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--emerald), var(--gold));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        .sidebar-brand-text {
            font-family: 'Syne', sans-serif;
            font-weight: 800; font-size: 1.1rem;
            color: #fff; letter-spacing: -0.02em;
        }
        .sidebar-brand-text span { color: var(--gold); }

        .sidebar-office-badge {
            margin: 1rem 1rem 0;
            background: rgba(4,120,87,0.12);
            border: 1px solid rgba(4,120,87,0.25);
            border-radius: 10px;
            padding: 0.65rem 0.875rem;
            font-size: 0.78rem;
        }

        .sidebar-section {
            padding: 1.25rem 1rem 0.4rem;
            font-size: 0.68rem; font-weight: 600;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--muted);
        }

        .sidebar-nav { padding: 0 0.75rem; }

        .sidebar-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.6rem 0.75rem; border-radius: 8px;
            color: var(--muted); font-size: 0.875rem; font-weight: 500;
            text-decoration: none; transition: all 0.2s; margin-bottom: 2px;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,255,255,0.06); color: #fff;
        }
        .sidebar-link.active { color: var(--gold); }
        .sidebar-link i { font-size: 1rem; width: 18px; text-align: center; }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 0.75rem calc(1.5rem + env(safe-area-inset-bottom, 0px));
            border-top: 1px solid var(--border);
        }
        .sidebar-footer .sidebar-link {
            color: #f87171;
            font-weight: 600;
        }
        .sidebar-footer .sidebar-link:hover {
            background: rgba(248, 113, 113, 0.12);
            color: #fca5a5;
        }
        .sidebar-footer .sidebar-link i {
            color: #f87171;
        }

        /* TOP NAV */
        .staff-topnav {
            position: fixed; top: 0;
            left: var(--sidebar-w); right: 0; height: 64px;
            background: rgba(13,31,60,0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 2rem; z-index: 100;
        }

        .page-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700; font-size: 1.1rem; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            min-width: 0; flex: 1 1 auto;
        }

        .btn-menu-toggle {
            display: none;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border);
            border-radius: 9px; width: 42px; height: 42px;
            align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem; flex-shrink: 0;
            margin-right: 0.75rem;
        }
        .btn-menu-toggle:hover { background: rgba(255,255,255,0.1); }

        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.55); z-index: 190;
            opacity: 0; transition: opacity 0.2s;
        }
        .sidebar-overlay.show { display: block; opacity: 1; }

        /* MAIN */
        .staff-main {
            margin-left: var(--sidebar-w);
            padding-top: 64px; min-height: 100vh;
            position: relative;
        }

        .content-area { padding: 2rem; }

        @media (max-width: 768px) {
            .staff-sidebar { transform: translateX(-100%); transition: transform 0.25s ease; }
            .staff-sidebar.open { transform: translateX(0); box-shadow: 6px 0 28px rgba(0,0,0,0.3); }
            .staff-main, .staff-topnav { margin-left: 0; left: 0; }
            .staff-topnav { padding: 0 1rem; }
            .btn-menu-toggle { display: flex; }
            .content-area { padding: 1.1rem; }
            .staff-topnav .d-flex.align-items-center.gap-3 > span:not(.badge-active) { display: none; }
            .staff-topnav > div:last-child { flex-shrink: 0; }
            .page-title { font-size: 0.95rem; }
        }

        /* CARD */
        .s-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            backdrop-filter: blur(6px);
        }

        /* ALERTS */
        .alert-success-custom {
            background: rgba(4,120,87,0.15); border: 1px solid rgba(4,120,87,0.35);
            color: #6ee7b7; border-radius: 10px;
            padding: 0.875rem 1.25rem; font-size: 0.875rem;
            display: flex; align-items: center; gap: 0.6rem;
        }
        .alert-error-custom {
            background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5; border-radius: 10px;
            padding: 0.875rem 1.25rem; font-size: 0.875rem;
        }

        /* FORM */
        .form-control-custom, .form-select-custom {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            color: var(--text); border-radius: 9px;
            padding: 0.65rem 1rem; font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s; width: 100%;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            background: rgba(255,255,255,0.07);
            border-color: var(--emerald-light);
            box-shadow: 0 0 0 3px rgba(5,150,105,0.15);
            outline: none; color: var(--text);
        }
        .form-control-custom::placeholder { color: var(--muted); }
        .form-select-custom option { background: var(--navy-2); color: var(--text); }
        textarea.form-control-custom { resize: vertical; min-height: 90px; }
        .form-label-custom {
            font-size: 0.78rem; font-weight: 600; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.05em;
            margin-bottom: 0.4rem; display: block;
        }

        /* BUTTONS */
        .btn-gold {
            background: var(--gold); border: none; color: var(--navy);
            font-weight: 600; font-size: 0.875rem;
            padding: 0.6rem 1.4rem; border-radius: 9px;
            transition: all 0.2s; cursor: pointer;
            display: inline-flex; align-items: center; gap: 0.5rem;
            text-decoration: none;
        }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-1px); color: var(--navy); }

        .btn-ghost {
            background: var(--card); border: 1px solid var(--border);
            color: var(--text); font-size: 0.875rem;
            padding: 0.6rem 1.4rem; border-radius: 9px;
            transition: all 0.2s; cursor: pointer;
            display: inline-flex; align-items: center; gap: 0.5rem;
            text-decoration: none;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); color: #fff; }

        .btn-emerald {
            background: rgba(4,120,87,0.2); border: 1px solid rgba(4,120,87,0.35);
            color: #6ee7b7; font-size: 0.8rem;
            padding: 0.4rem 0.9rem; border-radius: 7px;
            transition: all 0.2s; cursor: pointer;
            display: inline-flex; align-items: center; gap: 0.4rem;
            text-decoration: none;
        }
        .btn-emerald:hover { background: rgba(4,120,87,0.35); color: #a7f3d0; }

        .btn-edit-soft {
            background: rgba(214,158,46,0.1); border: 1px solid rgba(214,158,46,0.25);
            color: var(--gold); font-size: 0.8rem;
            padding: 0.4rem 0.9rem; border-radius: 7px;
            transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.4rem;
            text-decoration: none;
        }
        .btn-edit-soft:hover { background: rgba(214,158,46,0.2); color: var(--gold-light); }

        .btn-danger-soft {
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
            color: #f87171; font-size: 0.8rem;
            padding: 0.4rem 0.9rem; border-radius: 7px;
            transition: all 0.2s; cursor: pointer;
            display: inline-flex; align-items: center; gap: 0.4rem;
        }
        .btn-danger-soft:hover { background: rgba(239,68,68,0.2); color: #fca5a5; }

        /* BADGES */
        .badge-active {
            background: rgba(4,120,87,0.2); color: #6ee7b7;
            border: 1px solid rgba(4,120,87,0.3); font-size: 0.72rem;
            padding: 0.2rem 0.65rem; border-radius: 20px; font-weight: 600;
        }
        .badge-inactive {
            background: rgba(100,116,139,0.2); color: var(--muted);
            border: 1px solid rgba(100,116,139,0.25); font-size: 0.72rem;
            padding: 0.2rem 0.65rem; border-radius: 20px; font-weight: 600;
        }
        .badge-free {
            background: rgba(99,102,241,0.15); color: #a5b4fc;
            border: 1px solid rgba(99,102,241,0.25); font-size: 0.72rem;
            padding: 0.2rem 0.65rem; border-radius: 20px; font-weight: 600;
        }

        /* TABLE */
        .s-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .s-table th {
            font-size: 0.72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.07em;
            color: var(--muted); padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
        }
        .s-table td {
            padding: 0.9rem 1rem; font-size: 0.875rem; color: var(--text);
            border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle;
        }
        .s-table tr:last-child td { border-bottom: none; }
        .s-table tbody tr:hover td { background: rgba(255,255,255,0.02); }

        /* TOGGLE */
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; inset: 0;
            background: rgba(255,255,255,0.1); border-radius: 24px;
            border: 1px solid var(--border); cursor: pointer; transition: 0.3s;
        }
        .toggle-slider::before {
            content: ''; position: absolute;
            left: 3px; top: 3px; width: 16px; height: 16px;
            background: var(--muted); border-radius: 50%; transition: 0.3s;
        }
        input:checked + .toggle-slider { background: rgba(4,120,87,0.35); border-color: var(--emerald-light); }
        input:checked + .toggle-slider::before { transform: translateX(20px); background: #6ee7b7; }

        /* DOCUMENT ROW */
        .doc-row {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 10px; padding: 0.875rem 1rem;
            display: flex; gap: 0.75rem; align-items: flex-start;
            margin-bottom: 0.625rem; transition: border-color 0.2s;
        }
        .doc-row:hover { border-color: rgba(255,255,255,0.14); }
        .doc-drag-handle {
            color: var(--muted); cursor: grab; padding-top: 0.5rem;
            font-size: 1rem; flex-shrink: 0;
        }
        .doc-remove {
            background: none; border: none; color: var(--muted);
            cursor: pointer; padding: 0.4rem; border-radius: 6px;
            transition: all 0.2s; flex-shrink: 0; margin-top: 2px;
        }
        .doc-remove:hover { background: rgba(239,68,68,0.15); color: #f87171; }

        /* MODAL */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
        }
        .modal-box {
            background: #162947; border: 1px solid var(--border);
            border-radius: 16px; padding: 2rem;
            max-width: 420px; width: 90%; margin: auto;
        }
    </style>
    <style>
        .lang-switch { display: flex; align-items: center; gap: 0.3rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-radius: 9px; padding: 0.3rem 0.5rem; }
        .lang-switch a { font-size: 0.78rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 6px; text-decoration: none; color: var(--muted); }
        .lang-switch a.active { background: var(--gold); color: var(--navy); }
        html[dir="rtl"] .staff-sidebar { left: auto; right: 0; border-right: none; border-left: 1px solid var(--border); }
        html[dir="rtl"] .staff-topnav { left: 0; right: var(--sidebar-w); }
        html[dir="rtl"] .staff-main { margin-left: 0; margin-right: var(--sidebar-w); }
        @media (max-width: 768px) {
            html[dir="rtl"] .staff-sidebar { transform: translateX(100%); }
            html[dir="rtl"] .staff-sidebar.open { transform: translateX(0); }
            html[dir="rtl"] .staff-main, html[dir="rtl"] .staff-topnav { margin-right: 0; right: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="staff-sidebar">
    <a href="{{ route('staff.services.index') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-building" style="color:#fff;font-size:1rem;"></i>
        </div>
        <span class="sidebar-brand-text">E-<span>Services</span></span>
    </a>

    {{-- Office badge --}}
    <div class="sidebar-office-badge">
        <div style="font-size:0.68rem;color:var(--muted);margin-bottom:2px;">{{ app()->getLocale() === 'ar' ? 'دائرتك' : 'YOUR OFFICE' }}</div>
        <div style="color:#6ee7b7;font-weight:600;font-size:0.82rem;">
            <i class="bi bi-geo-alt-fill me-1"></i>
            {{ Auth::user()->office->name ?? (app()->getLocale() === 'ar' ? 'غير معيّن' : 'Not Assigned') }}
        </div>
    </div>

    <div class="sidebar-section">{{ __('app.nav_services') }}</div>
    <nav class="sidebar-nav">
        <a href="{{ route('staff.services.index') }}"
           class="sidebar-link {{ request()->routeIs('staff.services.*') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> {{ app()->getLocale() === 'ar' ? 'دليل الخدمات' : 'Service Catalog' }}
        </a>
        <a href="{{ route('staff.services.create') }}" class="sidebar-link">
            <i class="bi bi-plus-circle-fill"></i> {{ app()->getLocale() === 'ar' ? 'إضافة خدمة جديدة' : 'Add New Service' }}
        </a>
    </nav>

    <div class="sidebar-section">{{ app()->getLocale() === 'ar' ? 'أدوات' : 'Tools' }}</div>
    <nav class="sidebar-nav">
        <a href="{{ route('office.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('office.dashboard') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text-fill"></i> {{ __('app.nav_service_requests') }}
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="sidebar-link">
            <i class="bi bi-box-arrow-left"></i> {{ __('app.nav_sign_out') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- TOP NAV -->
<header class="staff-topnav">
    <div class="d-flex align-items-center" style="min-width:0;">
        <button class="btn-menu-toggle" id="menu-toggle-btn" onclick="toggleSidebar()" aria-label="Toggle menu">
            <i class="bi bi-list"></i>
        </button>
        <span class="page-title">@yield('page-title', 'Staff Portal')</span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="lang-switch">
            <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">AR</a>
        </div>
        <span style="font-size:0.82rem;color:var(--muted);">
            <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
        </span>
        <span class="badge-active">{{ app()->getLocale() === 'ar' ? 'موظف' : 'Staff' }}</span>
    </div>
</header>

<!-- MAIN -->
<main class="staff-main">
    <div class="content-area">
        @if(session('success'))
            <div class="alert-success-custom mb-4">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error-custom mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.toggleSidebar = function () {
        const sidebar = document.querySelector('.staff-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        const opening = !sidebar.classList.contains('open');
        sidebar.classList.toggle('open', opening);
        overlay.classList.toggle('show', opening);
        document.body.style.overflow = opening ? 'hidden' : '';
    };

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            const sidebar = document.querySelector('.staff-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar?.classList.remove('open');
            overlay?.classList.remove('show');
            document.body.style.overflow = '';
        }
    });
</script>
@stack('scripts')
</body>
</html>
