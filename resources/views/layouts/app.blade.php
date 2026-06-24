<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-Services') }}@hasSection('title') — @yield('title')@endif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --navy:          #1e3a5f;
            --navy-2:        #2d5a8e;
            --emerald:       #047857;
            --emerald-light: #059669;
            --gold:          #d97706;
            --gold-light:    #f59e0b;
            --surface:       #f8fafc;
            --card:          #ffffff;
            --border:        #e2e8f0;
            --text:          #1e293b;
            --muted:         #64748b;
            --sidebar-w:     280px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        /* Larger base size + line-height — every rem-based size in this layout
           and every citizen page scales up from here for easier reading. */
        html { scroll-behavior: smooth; font-size: 18px; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.22); }
        * { scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.12) transparent; }

        body {
            background-color: #f1f5f9;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-weight: 400;
            font-size: 1rem;
            line-height: 1.65;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .app-sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh; height: 100dvh;
            background: #ffffff;
            border-right: 1px solid var(--border);
            box-shadow: 2px 0 8px rgba(0,0,0,0.04);
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
            background: linear-gradient(135deg, var(--navy), var(--navy-2));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            font-family: 'Syne', sans-serif;
            font-weight: 800; font-size: 1.25rem;
            color: var(--navy); letter-spacing: -0.02em;
        }
        .sidebar-brand-text span { color: var(--gold); }

        .sidebar-section {
            padding: 1.25rem 1rem 0.4rem;
            font-size: 0.8rem; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--muted);
        }

        .sidebar-nav { padding: 0 0.75rem; }

        .sidebar-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.85rem 0.9rem; border-radius: 9px;
            color: #475569; font-size: 1rem; font-weight: 500;
            text-decoration: none; transition: all 0.2s; margin-bottom: 3px;
            width: 100%; background: none; border: none; cursor: pointer; text-align: left;
            min-height: 44px;
        }

        .sidebar-link:hover { background: #f1f5f9; color: var(--navy); }
        .sidebar-link.active { background: #eff6ff; color: #2563eb; }
        .sidebar-link i { font-size: 1.15rem; width: 22px; text-align: center; flex-shrink: 0; }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 0.75rem calc(1.5rem + env(safe-area-inset-bottom, 0px));
            border-top: 1px solid var(--border);
        }
        .sidebar-footer .sidebar-link {
            color: #dc2626;
            font-weight: 600;
        }
        .sidebar-footer .sidebar-link:hover {
            background: rgba(220, 38, 38, 0.08);
            color: #b91c1c;
        }
        .sidebar-footer .sidebar-link i {
            color: #dc2626;
        }

        /* ── TOP NAV (authenticated) ── */
        .app-topnav {
            position: fixed; top: 0;
            left: var(--sidebar-w); right: 0; height: 76px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 2rem; z-index: 100; gap: 0.75rem;
        }

        /* ── MOBILE MENU TOGGLE + OVERLAY ── */
        .btn-menu-toggle {
            display: none;
            background: #f8fafc; border: 1px solid var(--border);
            border-radius: 9px; width: 46px; height: 46px;
            align-items: center; justify-content: center;
            color: var(--navy); cursor: pointer; flex-shrink: 0;
            font-size: 1.3rem; transition: background 0.2s;
        }
        .btn-menu-toggle:hover { background: #f1f5f9; }

        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,0.45); z-index: 190;
            opacity: 0; transition: opacity 0.2s;
        }
        .sidebar-overlay.show { display: block; opacity: 1; }

        .topnav-title-group { display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1 1 auto; }
        .page-title { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; flex: 1 1 auto; }

        .page-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700; font-size: 1.35rem; color: var(--navy);
        }

        /* ── MAIN (authenticated) ── */
        .app-main {
            margin-left: var(--sidebar-w);
            padding-top: 76px; min-height: 100vh;
            position: relative;
        }

        .content-area { padding: 2rem; }

        /* ── GUEST TOP NAV ── */
        .guest-topnav {
            position: sticky; top: 0;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 2rem; height: 76px;
            z-index: 100;
        }

        .guest-main {
            min-height: 100vh;
            position: relative;
        }

        .content-area-guest { padding: 2rem; }

        .nav-link-guest {
            color: var(--muted); font-size: 1rem;
            text-decoration: none; transition: color 0.2s;
        }
        .nav-link-guest:hover { color: var(--navy); }

        /* ── ALERTS ── */
        .alert-success-custom {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46; border-radius: 10px;
            padding: 1rem 1.4rem; font-size: 1rem;
            display: flex; align-items: center; gap: 0.6rem;
        }
        .alert-error-custom {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b; border-radius: 10px;
            padding: 1rem 1.4rem; font-size: 1rem;
            display: flex; align-items: center; gap: 0.6rem;
        }

        /* ── BUTTONS ── */
        .btn-gold {
            background: var(--gold); border: none; color: #ffffff;
            font-weight: 600; font-size: 1rem;
            padding: 0.75rem 1.6rem; border-radius: 9px;
            transition: all 0.2s; cursor: pointer;
            display: inline-flex; align-items: center; gap: 0.5rem;
            text-decoration: none; min-height: 46px;
        }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-1px); color: #ffffff; }

        .btn-ghost {
            background: #ffffff; border: 1px solid var(--border);
            color: var(--text); font-size: 1rem;
            padding: 0.75rem 1.6rem; border-radius: 9px;
            transition: all 0.2s; cursor: pointer;
            display: inline-flex; align-items: center; gap: 0.5rem;
            text-decoration: none; min-height: 46px;
        }
        .btn-ghost:hover { background: #f1f5f9; color: var(--navy); border-color: #cbd5e1; }

        .btn-danger-soft {
            background: #fee2e2; border: 1px solid #fca5a5;
            color: #991b1b; font-size: 0.92rem;
            padding: 0.55rem 1.1rem; border-radius: 7px;
            transition: all 0.2s; cursor: pointer;
            display: inline-flex; align-items: center; gap: 0.4rem;
        }
        .btn-danger-soft:hover { background: #fecaca; color: #7f1d1d; }

        .btn-edit-soft {
            background: #fef3c7; border: 1px solid #fde68a;
            color: #92400e; font-size: 0.92rem;
            padding: 0.55rem 1.1rem; border-radius: 7px;
            transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 0.4rem;
            text-decoration: none;
        }
        .btn-edit-soft:hover { background: #fde68a; color: #78350f; }

        .btn-emerald {
            background: #d1fae5; border: 1px solid #6ee7b7;
            color: #065f46; font-size: 0.92rem;
            padding: 0.55rem 1.1rem; border-radius: 7px;
            transition: all 0.2s; cursor: pointer;
            display: inline-flex; align-items: center; gap: 0.4rem;
            text-decoration: none;
        }
        .btn-emerald:hover { background: #a7f3d0; color: #064e3b; }

        /* ── CARDS ── */
        .app-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px; padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .app-card-hover:hover {
            box-shadow: 0 10px 28px rgba(15,23,42,0.09);
            transform: translateY(-3px);
        }

        /* ── BADGES ── */
        .badge-active {
            background: #d1fae5; color: #065f46;
            border: 1px solid #6ee7b7; font-size: 0.85rem;
            padding: 0.3rem 0.85rem; border-radius: 20px; font-weight: 600;
        }
        .badge-inactive {
            background: #f1f5f9; color: var(--muted);
            border: 1px solid var(--border); font-size: 0.85rem;
            padding: 0.3rem 0.85rem; border-radius: 20px; font-weight: 600;
        }
        .badge-role {
            background: #fef3c7; color: #92400e;
            border: 1px solid #fde68a; font-size: 0.85rem;
            padding: 0.3rem 0.85rem; border-radius: 20px; font-weight: 600;
        }

        /* ── FORMS ── */
        .form-control-custom, .form-select-custom {
            background: #ffffff;
            border: 1.5px solid var(--border);
            color: var(--text); border-radius: 9px;
            padding: 0.85rem 1.1rem; font-size: 1.05rem;
            transition: border-color 0.2s, box-shadow 0.2s; width: 100%;
            min-height: 46px;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            background: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
            outline: none; color: var(--text);
        }
        .form-control-custom::placeholder { color: var(--muted); }
        .form-select-custom option { background: #ffffff; color: var(--text); }
        textarea.form-control-custom { resize: vertical; min-height: 90px; }
        .form-label-custom {
            font-size: 0.92rem; font-weight: 600; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.05em;
            margin-bottom: 0.5rem; display: block;
        }

        /* ── TABLE ── */
        .app-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .app-table th {
            font-size: 0.85rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.07em;
            color: var(--muted); padding: 0.9rem 1.1rem;
            border-bottom: 1px solid var(--border);
        }
        .app-table td {
            padding: 1rem 1.1rem; font-size: 1rem;
            color: var(--text); border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .app-table tr:last-child td { border-bottom: none; }
        .app-table tbody tr:hover td { background: #f8fafc; }

        /* ── STATUS BADGES ── */
        .status-badge { font-size:0.85rem; padding:0.32rem 0.9rem; border-radius:20px; font-weight:600; display:inline-block; }
        .status-pending         { background:#fef3c7; border:1px solid #fde68a; color:#92400e; }
        .status-pending_payment { background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; }
        .status-reviewing       { background:#ede9fe; border:1px solid #c4b5fd; color:#5b21b6; }
        .status-in_review       { background:#ede9fe; border:1px solid #c4b5fd; color:#5b21b6; }
        .status-approved        { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; }
        .status-rejected        { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; }
        .status-completed       { background:#d1fae5; border:1px solid #6ee7b7; color:#047857; }
        .status-in_progress     { background:#dbeafe; border:1px solid #93c5fd; color:#1e40af; }

        /* ── MODAL ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.35); backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
        }
        .modal-box {
            background: #ffffff; border: 1px solid var(--border);
            border-radius: 16px; padding: 2rem;
            max-width: 480px; width: 90%; margin: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        /* ── NOTIFICATION BELL ── */
        .btn-notif {
            background: #f8fafc; border: 1px solid var(--border);
            border-radius: 9px; width: 46px; height: 46px;
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); cursor: pointer; position: relative;
            transition: all 0.2s; padding: 0; font-size: 1.05rem;
        }
        .btn-notif:hover { background: #f1f5f9; color: var(--navy); }
        .notif-badge {
            position: absolute; top: -5px; right: -5px;
            background: #ef4444; color: #fff; font-size: 0.6rem; font-weight: 700;
            min-width: 16px; height: 16px; border-radius: 8px; padding: 0 3px;
            display: flex; align-items: center; justify-content: center;
            pointer-events: none;
        }
        .notif-panel {
            position: absolute; top: calc(100% + 10px); right: 0; width: 340px;
            background: #ffffff; border: 1px solid var(--border);
            border-radius: 14px; z-index: 9999;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12); overflow: hidden;
        }
        .notif-panel-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.9rem 1.1rem; border-bottom: 1px solid var(--border);
        }
        .notif-mark-all {
            background: none; border: none; color: var(--muted);
            font-size: 0.85rem; cursor: pointer; transition: color 0.2s; padding: 0;
        }
        .notif-mark-all:hover { color: var(--navy); }
        .notif-item {
            display: flex; align-items: flex-start; gap: 0.75rem;
            padding: 0.95rem 1.1rem;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer; transition: background 0.15s;
        }
        .notif-item:hover { background: #f8fafc; }
        .notif-item.unread { background: #eff6ff; border-left: 3px solid #3b82f6; }
        .notif-icon {
            width: 38px; height: 38px; border-radius: 9px;
            background: #fef3c7;
            display: flex; align-items: center; justify-content: center;
            color: var(--gold); font-size: 1rem; flex-shrink: 0;
        }
        .notif-title { font-size: 0.92rem; font-weight: 600; color: var(--navy); margin-bottom: 2px; }
        .notif-body  { font-size: 0.86rem; color: var(--muted); line-height: 1.45; }
        .notif-time  { font-size: 0.78rem; color: #94a3b8; margin-top: 3px; }
        .notif-empty { padding: 1.5rem; text-align: center; color: var(--muted); font-size: 0.92rem; }

        /* ── RESPONSIVE TABLES ── */
        .app-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        @media (max-width: 768px) {
            .app-sidebar { transform: translateX(-100%); transition: transform 0.25s ease; box-shadow: none; }
            .app-sidebar.open { transform: translateX(0); box-shadow: 6px 0 28px rgba(0,0,0,0.18); }
            .app-main, .app-topnav { margin-left: 0; left: 0; }
            .app-topnav { padding: 0 1rem; }
            .content-area, .content-area-guest { padding: 1.1rem; }
            .btn-menu-toggle { display: flex; }
            .notif-panel,
            html[dir="rtl"] .notif-panel {
                position: fixed !important;
                top: 70px !important;
                left: 1rem !important;
                right: 1rem !important;
                width: auto !important;
            }
            .app-topnav > div:last-child { flex-shrink: 0; }
            .page-title { display: none; }
            .guest-topnav { padding: 0 1rem; flex-wrap: wrap; height: auto; gap: 0.6rem; padding-top: 0.6rem; padding-bottom: 0.6rem; }
            .guest-topnav nav { flex-wrap: wrap; gap: 0.5rem !important; }
        }

        @media (max-width: 480px) {
            .topnav-username { display: none; }
            .app-topnav { padding: 0 0.75rem; gap: 0.5rem; }
            .lang-switch { padding: 0.25rem 0.4rem; }
        }

        /* ── RTL (Arabic) OVERRIDES ── */
        html[dir="rtl"] body { font-family: 'DM Sans', 'Tahoma', sans-serif; }
        html[dir="rtl"] .app-sidebar { left: auto; right: 0; border-right: none; border-left: 1px solid var(--border); box-shadow: -2px 0 8px rgba(0,0,0,0.04); }
        html[dir="rtl"] .app-topnav { left: 0; right: var(--sidebar-w); }
        html[dir="rtl"] .app-main { margin-left: 0; margin-right: var(--sidebar-w); }
        html[dir="rtl"] .sidebar-link { text-align: right; }
        html[dir="rtl"] .notif-panel { right: auto; left: 0; }
        html[dir="rtl"] .notif-badge { right: auto; left: -5px; }
        html[dir="rtl"] .notif-item.unread { border-left: none; border-right: 3px solid #3b82f6; }
        @media (max-width: 768px) {
            html[dir="rtl"] .app-sidebar { transform: translateX(100%); }
            html[dir="rtl"] .app-sidebar.open { transform: translateX(0); }
            html[dir="rtl"] .app-main, html[dir="rtl"] .app-topnav { margin-right: 0; right: 0; }
        }

        /* ── LANGUAGE SWITCH ── */
        .lang-switch {
            display: flex; align-items: center; gap: 0.35rem;
            background: #f8fafc; border: 1px solid var(--border);
            border-radius: 9px; padding: 0.35rem 0.55rem;
        }
        .lang-switch a {
            font-size: 0.92rem; font-weight: 600; padding: 0.3rem 0.6rem;
            border-radius: 6px; text-decoration: none; color: var(--muted);
        }
        .lang-switch a.active { background: var(--navy); color: #fff; }
    </style>
    @stack('styles')
</head>
<body>

@auth
{{-- ═══════════ AUTHENTICATED LAYOUT ═══════════ --}}

<aside class="app-sidebar">
    <a href="{{ route('home') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-building" style="color:#fff;font-size:1rem;"></i>
        </div>
        <span class="sidebar-brand-text">E-<span>Services</span></span>
    </a>

    @if(auth()->user()->role === 'citizen')
        <div class="sidebar-section">{{ __('app.nav_services') }}</div>
        <nav class="sidebar-nav">
            <a href="{{ route('citizen.services.browse') }}"
               class="sidebar-link {{ request()->routeIs('citizen.services.browse') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> {{ __('app.nav_browse_services') }}
            </a>
            <a href="{{ route('citizen.applications.index') }}"
               class="sidebar-link {{ request()->routeIs('citizen.applications.*') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i> {{ __('app.nav_my_applications') }}
            </a>
            <a href="{{ route('citizen.my-requests') }}"
               class="sidebar-link {{ request()->routeIs('citizen.my-requests') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i> {{ __('app.nav_my_requests') }}
            </a>
            <a href="{{ route('citizen.appointments.index') }}"
               class="sidebar-link {{ request()->routeIs('citizen.appointments.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i> {{ __('app.nav_my_appointments') }}
            </a>
            @if(\Illuminate\Support\Facades\Route::has('citizen.how-it-works'))
            <a href="{{ route('citizen.how-it-works') }}"
               class="sidebar-link {{ request()->routeIs('citizen.how-it-works') ? 'active' : '' }}">
                <i class="bi bi-diagram-3-fill"></i> {{ __('app.nav_workflow_guide') }}
            </a>
            @endif
        </nav>

        <div class="sidebar-section">{{ __('app.nav_account') }}</div>
        <nav class="sidebar-nav">
            <a href="{{ route('profile.edit') }}"
               class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> {{ __('app.nav_profile') }}
            </a>
            <a href="{{ route('2fa.setup') }}" class="sidebar-link">
                <i class="bi bi-shield-lock-fill"></i> {{ __('app.nav_2fa_setup') }}
            </a>
        </nav>

    @elseif(in_array(auth()->user()->role, ['admin', 'office']))
        @if(auth()->user()->role === 'admin')
        <div class="sidebar-section">{{ __('app.nav_administration') }}</div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> {{ __('app.nav_dashboard') }}
            </a>
            <a href="{{ route('admin.offices.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.offices.*') ? 'active' : '' }}">
                <i class="bi bi-building-fill"></i> {{ __('app.nav_offices') }}
            </a>
            <a href="{{ route('admin.municipalities.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.municipalities.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i> {{ __('app.nav_municipalities') }}
            </a>
            <a href="{{ route('admin.users') }}"
               class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> {{ __('app.nav_citizens') }}
            </a>
            <a href="{{ route('admin.services.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> {{ __('app.nav_admin_services') }}
            </a>
        </nav>
        @endif

        <div class="sidebar-section">{{ __('app.nav_operations') }}</div>
        <nav class="sidebar-nav">
            <a href="{{ route('office.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('office.dashboard') ? 'active' : '' }}">
                <i class="bi bi-inbox-fill"></i> {{ __('app.nav_service_requests') }}
            </a>
            <a href="{{ route('office.appointments.index') }}"
               class="sidebar-link {{ request()->routeIs('office.appointments.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i> {{ __('app.nav_appointments') }}
            </a>
            <a href="{{ route('office.ratings.index') }}"
               class="sidebar-link {{ request()->routeIs('office.ratings.*') ? 'active' : '' }}">
                <i class="bi bi-star-half"></i> {{ __('app.nav_ratings') }}
            </a>
            @if(auth()->user()->role === 'admin')
                @php
                    $pendingApprovalCount = \App\Models\CitizenRequest::where('payment_status', 'paid')
                        ->whereIn('status', ['pending', 'in_review'])->count();
                @endphp
                <a href="{{ route('requests.index') }}"
                   class="sidebar-link {{ request()->routeIs('requests.*') ? 'active' : '' }}"
                   style="justify-content:space-between;">
                    <span style="display:flex;align-items:center;gap:0.75rem;">
                        <i class="bi bi-folder-check-fill"></i> {{ __('app.nav_review_requests') }}
                    </span>
                    @if($pendingApprovalCount > 0)
                        <span style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;font-size:0.65rem;padding:0.1rem 0.5rem;border-radius:20px;font-weight:700;">
                            {{ $pendingApprovalCount }}
                        </span>
                    @endif
                </a>
            @endif
        </nav>

        @if(auth()->user()->role === 'admin')
        <div class="sidebar-section">{{ __('app.nav_staff') }}</div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.staff.create') }}"
               class="sidebar-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                <i class="bi bi-person-plus-fill"></i> {{ __('app.nav_add_staff') }}
            </a>
        </nav>
        @endif
    @endif

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('app-logout-form').submit();"
           class="sidebar-link">
            <i class="bi bi-box-arrow-left"></i> {{ __('app.nav_sign_out') }}
        </a>
        <form id="app-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

<header class="app-topnav">
    <div class="topnav-title-group">
        <button class="btn-menu-toggle" id="menu-toggle-btn" onclick="toggleSidebar()" aria-label="Toggle menu">
            <i class="bi bi-list"></i>
        </button>
        <span class="page-title">@yield('page-title', config('app.name', 'E-Services'))</span>
    </div>
    <div class="d-flex align-items-center gap-3">

        {{-- Language Switch --}}
        <div class="lang-switch">
            <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">AR</a>
        </div>

        {{-- Notification Bell --}}
        <div class="position-relative" id="notif-wrapper">
            <button class="btn-notif" id="notif-btn" onclick="toggleNotif(event)">
                <i class="bi bi-bell-fill"></i>
                <span id="notif-badge" class="notif-badge d-none">0</span>
            </button>
            <div class="notif-panel" id="notif-panel" style="display:none;">
                <div class="notif-panel-header">
                    <span style="font-weight:600;font-size:1rem;color:var(--navy);">{{ __('app.notifications') }}</span>
                    <button onclick="markAllRead()" class="notif-mark-all">{{ __('app.mark_all_read') }}</button>
                </div>
                <div id="notif-items" style="max-height:360px;overflow-y:auto;">
                    <div class="notif-empty">{{ __('app.loading') }}</div>
                </div>
            </div>
        </div>

        <span class="topnav-username" style="font-size:0.95rem;color:var(--muted);">
            <i class="bi bi-person-circle me-1"></i>
            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
        </span>
        <span class="badge-role">{{ ucfirst(auth()->user()->role) }}</span>
    </div>
</header>

<main class="app-main">
    <div class="content-area">
        @if(session('success'))
            <div class="alert-success-custom mb-4">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error-custom mb-4">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </div>
</main>

@else
{{-- ═══════════ GUEST LAYOUT ═══════════ --}}

<header class="guest-topnav">
    <a href="{{ route('welcome') }}"
       style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;color:var(--navy);text-decoration:none;letter-spacing:-0.02em;">
        E-<span style="color:var(--gold)">Services</span>
    </a>
    <nav class="d-flex align-items-center gap-3">
        <div class="lang-switch">
            <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">AR</a>
        </div>
        <a href="{{ route('citizen.services.browse') }}" class="nav-link-guest">{{ __('app.nav_browse_services') }}</a>
        <a href="{{ route('login') }}" class="btn-ghost" style="padding:0.6rem 1.3rem;">{{ __('app.login') }}</a>
        <a href="{{ route('register') }}" class="btn-gold" style="padding:0.6rem 1.3rem;">{{ __('app.register') }}</a>
    </nav>
</header>

<main class="guest-main">
    <div class="content-area-guest">
        @if(session('success'))
            <div class="alert-success-custom mb-4">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error-custom mb-4">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </div>
</main>

@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

@auth
<script>
(function () {
    const CSRF = () => document.querySelector('meta[name="csrf-token"]').content;
    let panelOpen = false;
    let loaded    = false;

    // ── Mobile sidebar toggle ──
    window.toggleSidebar = function () {
        const sidebar = document.querySelector('.app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        const opening = !sidebar.classList.contains('open');
        sidebar.classList.toggle('open', opening);
        overlay.classList.toggle('show', opening);
        document.body.style.overflow = opening ? 'hidden' : '';
    };

    // Close sidebar automatically if the viewport grows back to desktop size
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            const sidebar = document.querySelector('.app-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar?.classList.remove('open');
            overlay?.classList.remove('show');
            document.body.style.overflow = '';
        }
    });

    window.toggleNotif = function (e) {
        e.stopPropagation();
        panelOpen = !panelOpen;
        document.getElementById('notif-panel').style.display = panelOpen ? 'block' : 'none';
        if (panelOpen && !loaded) fetchNotifications();
    };

    function fetchNotifications() {
        loaded = true;
        fetch('/notifications')
            .then(r => r.json())
            .then(data => {
                updateBadge(data.unread);
                renderItems(data.notifications);
            })
            .catch(() => { loaded = false; });
    }

    function renderItems(items) {
        const el = document.getElementById('notif-items');
        if (!items.length) {
            el.innerHTML = '<div class="notif-empty">No notifications yet</div>';
            return;
        }
        el.innerHTML = items.map(n => `
            <div class="notif-item ${n.read ? '' : 'unread'}"
                 onclick="readOne('${n.id}','${(n.data.url || '').replace(/'/g,"\\'")}')">
                <div class="notif-icon"><i class="bi ${n.data.icon || 'bi-bell-fill'}"></i></div>
                <div style="flex:1;min-width:0;">
                    <div class="notif-title">${n.data.title || ''}</div>
                    <div class="notif-body">${n.data.body || ''}</div>
                    <div class="notif-time">${n.time}</div>
                </div>
            </div>`).join('');
    }

    function updateBadge(count) {
        const b = document.getElementById('notif-badge');
        if (!b) return;
        if (count > 0) { b.textContent = count > 99 ? '99+' : count; b.classList.remove('d-none'); }
        else { b.classList.add('d-none'); }
    }

    window.readOne = function (id, url) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF(), 'Content-Type': 'application/json' }
        }).then(() => { loaded = false; fetchNotifications(); if (url) window.location.href = url; });
    };

    window.markAllRead = function () {
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF(), 'Content-Type': 'application/json' }
        }).then(() => { loaded = false; fetchNotifications(); });
    };

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (!document.getElementById('notif-wrapper')?.contains(e.target)) {
            panelOpen = false;
            const p = document.getElementById('notif-panel');
            if (p) p.style.display = 'none';
        }
    });

    // Load badge count on page load + poll every 60s
    function pollCount() {
        fetch('/notifications')
            .then(r => r.json())
            .then(data => {
                updateBadge(data.unread);
                if (panelOpen) renderItems(data.notifications);
            })
            .catch(() => {});
    }

    document.addEventListener('DOMContentLoaded', function () {
        pollCount();
        setInterval(pollCount, 60000);
    });
})();
</script>
@endauth
<x-chatbot />
</body>
</html>
    