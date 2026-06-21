<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — E-Services Platform</title>
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
            font-weight: 400;
            min-height: 100vh;
        }

        /* ── NOISE TEXTURE ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* ── SIDEBAR ── */
        .admin-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: rgba(13,31,60,0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 200;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .sidebar-brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--emerald), var(--gold));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: #fff;
            letter-spacing: -0.02em;
        }
        .sidebar-brand-text span { color: var(--gold); }

        .sidebar-section {
            padding: 1.25rem 1rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .sidebar-nav { padding: 0 0.75rem; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 8px;
            color: var(--muted);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }

        .sidebar-link.active { color: var(--gold); }
        .sidebar-link i { font-size: 1rem; width: 18px; text-align: center; }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 0.75rem 1.5rem;
            border-top: 1px solid var(--border);
        }

        /* ── TOP NAV ── */
        .admin-topnav {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: 64px;
            background: rgba(13,31,60,0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 100;
        }

        .page-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
        }

        /* ── MAIN CONTENT ── */
        .admin-main {
            margin-left: var(--sidebar-w);
            padding-top: 64px;
            min-height: 100vh;
            position: relative;
        }

        .content-area { padding: 2rem; }

        /* ── CARDS ── */
        .admin-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.5rem;
            backdrop-filter: blur(6px);
        }

        /* ── ALERTS ── */
        .alert-success-custom {
            background: rgba(4,120,87,0.15);
            border: 1px solid rgba(4,120,87,0.35);
            color: #6ee7b7;
            border-radius: 10px;
            padding: 0.875rem 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .alert-error-custom {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            border-radius: 10px;
            padding: 0.875rem 1.25rem;
            font-size: 0.875rem;
        }

        /* ── FORM CONTROLS ── */
        .form-control-custom, .form-select-custom {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 9px;
            padding: 0.65rem 1rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            background: rgba(255,255,255,0.07);
            border-color: var(--emerald-light);
            box-shadow: 0 0 0 3px rgba(5,150,105,0.15);
            outline: none;
            color: var(--text);
        }

        .form-control-custom::placeholder { color: var(--muted); }

        .form-select-custom option { background: var(--navy-2); color: var(--text); }

        .form-label-custom {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.4rem;
            display: block;
        }

        textarea.form-control-custom { resize: vertical; min-height: 90px; }

        /* ── BUTTONS ── */
        .btn-gold {
            background: var(--gold);
            border: none;
            color: var(--navy);
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.6rem 1.4rem;
            border-radius: 9px;
            transition: all 0.2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-gold:hover { background: var(--gold-light); transform: translateY(-1px); color: var(--navy); }

        .btn-ghost {
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 0.875rem;
            padding: 0.6rem 1.4rem;
            border-radius: 9px;
            transition: all 0.2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-ghost:hover { background: rgba(255,255,255,0.08); color: #fff; }

        .btn-danger-soft {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            color: #f87171;
            font-size: 0.8rem;
            padding: 0.4rem 0.9rem;
            border-radius: 7px;
            transition: all 0.2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-danger-soft:hover { background: rgba(239,68,68,0.2); color: #fca5a5; }

        .btn-edit-soft {
            background: rgba(214,158,46,0.1);
            border: 1px solid rgba(214,158,46,0.25);
            color: var(--gold);
            font-size: 0.8rem;
            padding: 0.4rem 0.9rem;
            border-radius: 7px;
            transition: all 0.2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
        }

        .btn-edit-soft:hover { background: rgba(214,158,46,0.2); color: var(--gold-light); }

        .btn-emerald {
            background: rgba(4,120,87,0.2);
            border: 1px solid rgba(4,120,87,0.35);
            color: #6ee7b7;
            font-size: 0.8rem;
            padding: 0.4rem 0.9rem;
            border-radius: 7px;
            transition: all 0.2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
        }

        .btn-emerald:hover { background: rgba(4,120,87,0.35); color: #a7f3d0; }

        /* ── Aliases used by service-management views ── */
        .s-card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; backdrop-filter: blur(6px); }

        .s-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .s-table th { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--muted); padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); }
        .s-table td { padding: 0.9rem 1rem; font-size: 0.875rem; color: var(--text); border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
        .s-table tr:last-child td { border-bottom: none; }
        .s-table tbody tr:hover td { background: rgba(255,255,255,0.02); }

        .badge-free { background: rgba(99,102,241,0.15); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.25); font-size: 0.72rem; padding: 0.2rem 0.65rem; border-radius: 20px; font-weight: 600; }

        .doc-row { background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 10px; padding: 0.875rem 1rem; display: flex; gap: 0.75rem; align-items: flex-start; margin-bottom: 0.625rem; transition: border-color 0.2s; }
        .doc-row:hover { border-color: rgba(255,255,255,0.14); }
        .doc-drag-handle { color: var(--muted); cursor: grab; padding-top: 0.5rem; font-size: 1rem; flex-shrink: 0; }
        .doc-remove { background: none; border: none; color: var(--muted); cursor: pointer; padding: 0.4rem; border-radius: 6px; transition: all 0.2s; flex-shrink: 0; margin-top: 2px; }
        .doc-remove:hover { background: rgba(239,68,68,0.15); color: #f87171; }

        .modal-overlay { display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; }
        .modal-box { background: #162947; border: 1px solid var(--border); border-radius: 16px; padding: 2rem; max-width: 420px; width: 90%; margin: auto; }

        /* ── BADGE ── */
        .badge-active {
            background: rgba(4,120,87,0.2);
            color: #6ee7b7;
            border: 1px solid rgba(4,120,87,0.3);
            font-size: 0.72rem;
            padding: 0.2rem 0.65rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-inactive {
            background: rgba(100,116,139,0.2);
            color: var(--muted);
            border: 1px solid rgba(100,116,139,0.25);
            font-size: 0.72rem;
            padding: 0.2rem 0.65rem;
            border-radius: 20px;
            font-weight: 600;
        }

        /* ── TABLE ── */
        .admin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .admin-table th {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--muted);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        .admin-table td {
            padding: 0.85rem 1rem;
            font-size: 0.875rem;
            color: var(--text);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            vertical-align: middle;
        }

        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover td { background: rgba(255,255,255,0.02); }

        /* ── TOGGLE ── */
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; inset: 0;
            background: rgba(255,255,255,0.1);
            border-radius: 24px;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: 0.3s;
        }
        .toggle-slider::before {
            content: '';
            position: absolute;
            left: 3px; top: 3px;
            width: 16px; height: 16px;
            background: var(--muted);
            border-radius: 50%;
            transition: 0.3s;
        }
        input:checked + .toggle-slider { background: rgba(4,120,87,0.35); border-color: var(--emerald-light); }
        input:checked + .toggle-slider::before { transform: translateX(20px); background: #6ee7b7; }

        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-main, .admin-topnav { margin-left: 0; left: 0; }
        }

        html[dir="rtl"] .admin-sidebar { left: auto; right: 0; border-right: none; border-left: 1px solid var(--border); }
        html[dir="rtl"] .admin-topnav { left: 0; right: var(--sidebar-w); }
        html[dir="rtl"] .admin-main { margin-left: 0; margin-right: var(--sidebar-w); }
        .lang-switch { display: flex; align-items: center; gap: 0.3rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-radius: 9px; padding: 0.3rem 0.5rem; }
        .lang-switch a { font-size: 0.78rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 6px; text-decoration: none; color: var(--muted); }
        .lang-switch a.active { background: var(--gold); color: var(--navy); }
    </style>
    @stack('styles')
</head>
<body>

<!-- ═══════════ SIDEBAR ═══════════ -->
<aside class="admin-sidebar">
    <a href="{{ route('admin.offices.index') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-building" style="color:#fff;font-size:1rem;"></i>
        </div>
        <span class="sidebar-brand-text">E-<span>Services</span></span>
    </a>

    @if(Auth::user()->role === 'admin')
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
        @if(Auth::user()->role === 'admin')
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
                    <span style="background:rgba(239,68,68,0.2);border:1px solid rgba(239,68,68,0.3);color:#f87171;font-size:0.65rem;padding:0.1rem 0.5rem;border-radius:20px;font-weight:700;">
                        {{ $pendingApprovalCount }}
                    </span>
                @endif
            </a>
        @endif
    </nav>

    @if(Auth::user()->role === 'admin')
    <div class="sidebar-section">{{ __('app.nav_staff') }}</div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.staff.create') }}"
           class="sidebar-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
            <i class="bi bi-person-plus-fill"></i> {{ __('app.nav_add_staff') }}
        </a>
    </nav>
    @endif

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="sidebar-link">
            <i class="bi bi-box-arrow-left"></i> {{ __('app.nav_sign_out') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</aside>

<!-- ═══════════ TOP NAV ═══════════ -->
<header class="admin-topnav">
    <span class="page-title">@yield('page-title', 'Dashboard')</span>
    <div class="d-flex align-items-center gap-3">
        <div class="lang-switch">
            <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">AR</a>
        </div>
        <span style="font-size:0.82rem;color:var(--muted);">
            <i class="bi bi-person-circle me-1"></i>
            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
        </span>
        <span class="badge-active">{{ Auth::user()->role === 'admin' ? (app()->getLocale() === 'ar' ? 'مسؤول عام' : 'Super Admin') : (app()->getLocale() === 'ar' ? 'موظف الدائرة' : 'Office Staff') }}</span>
    </div>
</header>

<!-- ═══════════ MAIN ═══════════ -->
<main class="admin-main">
    <div class="content-area">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert-success-custom mb-4">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error-custom mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
