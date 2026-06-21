<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth_pages.create_account_title') }} — E-Services Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --navy:   #0d1f3c;
            --navy-2: #162947;
            --emerald:#047857;
            --emerald-light: #059669;
            --gold:   #d69e2e;
            --gold-light: #f6d860;
            --surface: #111c30;
            --card:   rgba(255,255,255,0.04);
            --border: rgba(255,255,255,0.08);
            --text:   #e2e8f0;
            --muted:  #94a3b8;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--navy);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        .bg-blob-1 {
            position: fixed; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(4,120,87,0.12) 0%, transparent 70%);
            top: -200px; right: -150px; pointer-events: none;
        }

        .bg-blob-2 {
            position: fixed; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(214,158,46,0.07) 0%, transparent 70%);
            bottom: -200px; left: -100px; pointer-events: none;
        }

        /* ── TOP NAV ── */
        .top-bar {
            position: fixed; top: 0; left: 0; right: 0;
            background: rgba(13,31,60,0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
            z-index: 100;
        }

        .brand-link {
            text-decoration: none;
            display: flex; align-items: center; gap: 0.6rem;
        }

        .brand-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--emerald), var(--gold));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .brand-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800; font-size: 1.1rem;
            color: #fff; letter-spacing: -0.02em;
        }

        .brand-name span { color: var(--gold); }

        .signin-link {
            font-size: 0.85rem; color: var(--muted);
            text-decoration: none; transition: color 0.2s;
        }

        .signin-link:hover { color: #fff; }
        .signin-link span { color: var(--emerald-light); font-weight: 500; }

        /* ── PAGE LAYOUT ── */
        .page-wrapper {
            display: flex;
            min-height: 100vh;
            padding-top: 65px;
            position: relative; z-index: 1;
        }

        /* ── LEFT SIDEBAR ── */
        .left-sidebar {
            width: 380px;
            flex-shrink: 0;
            padding: 3rem 2.5rem;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
            position: sticky;
            top: 65px;
            height: calc(100vh - 65px);
            overflow-y: auto;
        }

        /* Step tracker */
        .step-tracker { display: flex; flex-direction: column; gap: 0; }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            position: relative;
            padding-bottom: 1.5rem;
        }

        .step-item:last-child { padding-bottom: 0; }

        .step-item::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 36px;
            bottom: 0;
            width: 2px;
            background: var(--border);
        }

        .step-item:last-child::before { display: none; }

        .step-item.active .step-circle { 
            background: linear-gradient(135deg, var(--emerald), #065f46);
            border-color: var(--emerald);
            color: #fff;
        }

        .step-item.active::before { background: linear-gradient(to bottom, var(--emerald), var(--border)); }

        .step-item.done .step-circle {
            background: rgba(4,120,87,0.2);
            border-color: var(--emerald);
            color: var(--emerald-light);
        }

        .step-circle {
            width: 36px; height: 36px; flex-shrink: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 700; font-size: 0.85rem;
            color: var(--muted);
            transition: all 0.3s;
        }

        .step-info .step-name {
            font-weight: 600; font-size: 0.9rem;
            color: #fff; line-height: 1;
            margin-bottom: 0.25rem;
        }

        .step-item:not(.active) .step-info .step-name { color: var(--muted); }

        .step-info .step-desc {
            font-size: 0.78rem;
            color: var(--muted);
            line-height: 1.5;
        }

        /* Trust box */
        .trust-box {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.25rem;
        }

        .trust-box-title {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 1rem;
        }

        .trust-row {
            display: flex; align-items: center; gap: 0.6rem;
            color: var(--muted); font-size: 0.82rem;
            padding: 0.4rem 0;
        }

        .trust-row i { color: var(--emerald-light); font-size: 0.85rem; }

        /* ── FORM AREA ── */
        .form-area {
            flex: 1;
            padding: 3rem 3rem 3rem 3rem;
            max-width: 700px;
        }

        .form-section-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: 0.3rem;
        }

        .form-section-sub {
            color: var(--muted);
            font-size: 0.88rem;
            margin-bottom: 2rem;
        }

        /* Input styles */
        .field-group { margin-bottom: 1.25rem; }

        .field-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 0.45rem;
            letter-spacing: 0.02em;
        }

        .field-label .req { color: var(--gold); margin-left: 2px; }

        .input-wrap { position: relative; }

        .input-wrap .iico {
            position: absolute;
            left: 1rem; top: 50%;
            transform: translateY(-50%);
            color: var(--muted); font-size: 0.85rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .input-wrap:focus-within .iico { color: var(--emerald-light); }

        .field-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            transition: all 0.2s;
            outline: none;
        }

        .field-input.no-icon { padding-left: 1rem; }

        .field-input::placeholder { color: rgba(148,163,184,0.45); }

        .field-input:focus {
            border-color: var(--emerald-light);
            background: rgba(255,255,255,0.07);
            box-shadow: 0 0 0 3px rgba(4,120,87,0.15);
        }

        .field-input.is-invalid {
            border-color: rgba(239,68,68,0.5);
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }

        .field-error {
            display: none;
            font-size: 0.76rem;
            color: #fca5a5;
            margin-top: 0.3rem;
        }

        .field-input.is-invalid ~ .field-error { display: block; }

        .field-input.is-valid {
            border-color: rgba(4,120,87,0.5);
        }

        /* Password toggle */
        .pw-toggle {
            position: absolute;
            right: 1rem; top: 50%; transform: translateY(-50%);
            background: none; border: none;
            color: var(--muted); cursor: pointer;
            font-size: 0.9rem; transition: color 0.2s; padding: 0;
        }

        .pw-toggle:hover { color: #fff; }

        /* Password strength */
        .pw-strength { margin-top: 0.5rem; }

        .pw-strength-bar {
            height: 4px;
            background: rgba(255,255,255,0.08);
            border-radius: 100px;
            overflow: hidden;
            margin-bottom: 0.35rem;
        }

        .pw-strength-fill {
            height: 100%;
            border-radius: 100px;
            width: 0;
            transition: width 0.3s, background 0.3s;
        }

        .pw-strength-label {
            font-size: 0.73rem;
            color: var(--muted);
        }

        /* Select */
        .field-select {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            transition: all 0.2s;
            outline: none;
            cursor: pointer;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        .field-select:focus {
            border-color: var(--emerald-light);
            box-shadow: 0 0 0 3px rgba(4,120,87,0.15);
        }

        .field-select option { background: var(--navy-2); color: #fff; }

        /* Hide select and show cards on larger screens */
        .field-select.hidden-select { display: none; }
        
        .doc-type-cards {
            display: none;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }
        
        @media (min-width: 576px) {
            .doc-type-cards { display: grid; }
            .field-select.doc-select { display: none; }
        }
        
        @media (max-width: 575px) {
            .doc-type-cards { display: none !important; }
            .field-select.doc-select { display: block !important; }
        }
        
        .doc-type-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.25s;
            text-align: center;
        }
        
        .doc-type-card:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.15);
        }
        
        .doc-type-card.selected {
            background: rgba(4,120,87,0.15);
            border-color: var(--emerald-light);
        }
        
        .doc-type-card i {
            font-size: 1.5rem;
            color: var(--muted);
            transition: color 0.25s;
        }
        
        .doc-type-card.selected i { color: var(--emerald-light); }
        
        .doc-type-card span {
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 500;
            transition: color 0.25s;
        }
        
        .doc-type-card.selected span { color: #fff; }

        /* Upload zone */
        .upload-zone {
            border: 2px dashed var(--border);
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
            position: relative;
            background: rgba(255,255,255,0.02);
        }

        .upload-zone:hover,
        .upload-zone.dragover {
            border-color: var(--emerald-light);
            background: rgba(4,120,87,0.07);
        }

        .upload-zone.has-file {
            border-style: solid;
            border-color: rgba(4,120,87,0.4);
            background: rgba(4,120,87,0.06);
        }

        .upload-zone input[type="file"] {
            position: absolute; inset: 0;
            opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }

        .upload-icon {
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.06);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.4rem;
            color: var(--muted);
            transition: all 0.25s;
        }

        .upload-zone.has-file .upload-icon {
            background: rgba(4,120,87,0.15);
            color: var(--emerald-light);
        }

        .upload-title {
            font-weight: 600; color: #fff;
            font-size: 0.92rem; margin-bottom: 0.25rem;
        }

        .upload-sub { font-size: 0.8rem; color: var(--muted); }

        .file-preview {
            display: none;
            align-items: center;
            gap: 0.75rem;
            background: rgba(4,120,87,0.1);
            border: 1px solid rgba(4,120,87,0.25);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-top: 1rem;
        }

        .file-preview.show { display: flex; }

        .file-preview-name {
            font-size: 0.85rem; color: #fff;
            flex: 1; overflow: hidden;
            text-overflow: ellipsis; white-space: nowrap;
        }

        .file-preview-size {
            font-size: 0.75rem; color: var(--muted);
        }

        /* Section divider */
        .section-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 2rem 0;
        }

        /* Terms */
        .terms-row {
            display: flex; align-items: flex-start; gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .terms-row input[type="checkbox"] {
            width: 18px; height: 18px; flex-shrink: 0;
            margin-top: 2px;
            accent-color: var(--emerald-light);
            cursor: pointer;
        }

        .terms-row span {
            font-size: 0.85rem; color: var(--muted); line-height: 1.6;
        }

        .terms-row a { color: var(--gold); text-decoration: none; }
        .terms-row a:hover { color: #fff; }

        /* Submit */
        .btn-submit-main {
            width: 100%;
            background: linear-gradient(135deg, var(--emerald), #065f46);
            border: none; border-radius: 14px;
            padding: 1rem; color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600; font-size: 1rem;
            cursor: pointer; transition: all 0.25s;
            box-shadow: 0 8px 30px rgba(4,120,87,0.3);
            display: flex; align-items: center;
            justify-content: center; gap: 0.5rem;
            position: relative; overflow: hidden;
        }

        .btn-submit-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(4,120,87,0.45);
        }

        .btn-submit-main:disabled {
            opacity: 0.6; cursor: not-allowed; transform: none;
        }

        .btn-submit-main .spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .btn-submit-main.loading .spinner { display: block; }
        .btn-submit-main.loading .btn-label { display: none; }

        /* Alert */
        .alert-err {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 12px;
            padding: 0.9rem 1rem;
            color: #fca5a5;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex; align-items: flex-start; gap: 0.5rem;
        }

        /* Security note */
        .security-note {
            display: flex; align-items: center; gap: 0.5rem;
            justify-content: center; margin-top: 1.25rem;
            font-size: 0.75rem; color: var(--muted);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .left-sidebar { display: none; }
            .form-area { max-width: 100%; padding: 2rem 1.5rem; }
        }

        @media (max-width: 576px) {
            .form-area { padding: 1.5rem 1rem; }
        }

        .lang-switch-auth { display:flex; align-items:center; gap:0.3rem; }
        .lang-switch-auth a { font-size:0.75rem; font-weight:600; padding:0.25rem 0.6rem; border-radius:6px; text-decoration:none; color:var(--muted); border:1px solid var(--border); }
        .lang-switch-auth a.active { background:var(--navy); color:#fff; }
        html[dir="rtl"] body { font-family: 'Tahoma', 'DM Sans', sans-serif; }

        .field-feedback { display:block; margin-top:4px; font-size:0.76rem; }
        .field-feedback.is-error { color:#dc2626; }
        .field-feedback.is-ok { color:#059669; }
        .field-input.field-valid { border-color:#059669 !important; }
        .field-input.field-invalid { border-color:#dc2626 !important; }
    </style>
</head>
<body>

<div class="bg-blob-1"></div>
<div class="bg-blob-2"></div>

<!-- ── TOP BAR ── -->
<header class="top-bar">
    <a href="/" class="brand-link">
        <div class="brand-icon">
            <i class="bi bi-building" style="color:#fff;font-size:0.9rem;"></i>
        </div>
        <span class="brand-name">E-<span>Services</span></span>
    </a>
    <div class="d-flex align-items-center gap-3">
        <div class="lang-switch-auth">
            <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">AR</a>
        </div>
        <a href="{{ route('login') }}" class="signin-link">
            {{ __('auth_pages.already_registered') }} <span>{{ __('auth_pages.sign_in_arrow') }}</span>
        </a>
    </div>
</header>

<div class="page-wrapper">

    <!-- ── LEFT SIDEBAR ── -->
    <aside class="left-sidebar d-none d-lg-flex flex-column">
        <!-- Step tracker -->
        <div>
            <p style="font-size:0.75rem;font-weight:600;letter-spacing:0.07em;text-transform:uppercase;color:var(--muted);margin-bottom:1.25rem;">{{ __('auth_pages.registration_steps') }}</p>
            <div class="step-tracker">
                <div class="step-item active" id="step-indicator-1">
                    <div class="step-circle" id="sc1">1</div>
                    <div class="step-info">
                        <div class="step-name">{{ __('auth_pages.step1_name') }}</div>
                        <div class="step-desc">{{ __('auth_pages.step1_desc') }}</div>
                    </div>
                </div>
                <div class="step-item" id="step-indicator-2">
                    <div class="step-circle" id="sc2">2</div>
                    <div class="step-info">
                        <div class="step-name">{{ __('auth_pages.step2_name') }}</div>
                        <div class="step-desc">{{ __('auth_pages.step2_desc') }}</div>
                    </div>
                </div>
                <div class="step-item" id="step-indicator-3">
                    <div class="step-circle" id="sc3">3</div>
                    <div class="step-info">
                        <div class="step-name">{{ __('auth_pages.step3_name') }}</div>
                        <div class="step-desc">{{ __('auth_pages.step3_desc') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trust box -->
        <div class="trust-box">
            <p class="trust-box-title">{{ __('auth_pages.privacy_security') }}</p>
            <div class="trust-row">
                <i class="bi bi-shield-lock-fill"></i>
                <span>{{ __('auth_pages.trust_encrypted') }}</span>
            </div>
            <div class="trust-row">
                <i class="bi bi-eye-slash-fill"></i>
                <span>{{ __('auth_pages.trust_admins_only') }}</span>
            </div>
            <div class="trust-row">
                <i class="bi bi-trash3-fill"></i>
                <span>{{ __('auth_pages.trust_deletion') }}</span>
            </div>
            <div class="trust-row">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ __('auth_pages.trust_gdpr') }}</span>
            </div>
        </div>

        <!-- Help -->
        <div style="margin-top:auto;padding-top:1rem;">
            <p style="font-size:0.78rem;color:var(--muted);">{{ __('auth_pages.need_help') }} <a href="#" style="color:var(--emerald-light);text-decoration:none;">{{ __('auth_pages.contact_support') }}</a></p>
        </div>
    </aside>

    <!-- ── FORM AREA ── -->
    <main class="form-area">

        {{-- Error messages --}}
        @if ($errors->any())
        <div class="alert-err">
            <i class="bi bi-exclamation-circle-fill mt-1 flex-shrink-0"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="regForm">
            @csrf

            <!-- ════ STEP 1 ════ -->
            <div id="step1">
                <div class="form-section-title">{{ __('auth_pages.step1_name') }}</div>
                <p class="form-section-sub">{{ __('auth_pages.step1_of_2') }}</p>

                <!-- Name row -->
                <div class="row g-3 mb-1">
                    <div class="col-sm-6">
                        <div class="field-group">
                            <label class="field-label">{{ __('auth_pages.first_name') }} <span class="req">*</span></label>
                            <div class="input-wrap">
                                <i class="bi bi-person-fill iico"></i>
                                <input type="text" name="first_name" id="firstNameField" class="field-input"
                                    placeholder="Ahmad" value="{{ old('first_name') }}"
                                    required maxlength="100">
                            </div>
                            <small class="field-feedback" id="firstNameFeedback" style="display:none;"></small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="field-group">
                            <label class="field-label">{{ __('auth_pages.last_name') }} <span class="req">*</span></label>
                            <div class="input-wrap">
                                <i class="bi bi-person-fill iico"></i>
                                <input type="text" name="last_name" id="lastNameField" class="field-input"
                                    placeholder="Khalil" value="{{ old('last_name') }}"
                                    required maxlength="100">
                            </div>
                            <small class="field-feedback" id="lastNameFeedback" style="display:none;"></small>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="field-group">
                    <label class="field-label">{{ __('auth_pages.email_address') }} <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope-fill iico"></i>
                        <input type="email" name="email" class="field-input"
                            placeholder="ahmad@example.com" value="{{ old('email') }}"
                            required id="emailField">
                    </div>
                    <small class="field-feedback" id="emailFeedback" style="display:none;"></small>
                </div>

                <!-- Phone -->
                <div class="field-group">
                    <label class="field-label">{{ __('auth_pages.phone_number') }} <span style="color:var(--muted);font-weight:400;">({{ __('auth_pages.optional') }})</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-telephone-fill iico"></i>
                        <input type="tel" name="phone" id="phoneField" class="field-input"
                            placeholder="+961 70 000 000" value="{{ old('phone') }}"
                            maxlength="20">
                    </div>
                    <small class="field-feedback" id="phoneFeedback" style="display:none;"></small>
                </div>

                <!-- Password -->
                <div class="field-group">
                    <label class="field-label">{{ __('auth_pages.password') }} <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill iico"></i>
                        <input type="password" name="password" class="field-input"
                            placeholder="Create a strong password"
                            id="pw" required autocomplete="new-password">
                        <button type="button" class="pw-toggle" onclick="togglePw('pw','pwIcon')">
                            <i class="bi bi-eye-fill" id="pwIcon"></i>
                        </button>
                    </div>
                    <div class="pw-strength">
                        <div class="pw-strength-bar">
                            <div class="pw-strength-fill" id="pwBar"></div>
                        </div>
                        <span class="pw-strength-label" id="pwLabel">Password strength</span>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="field-group">
                    <label class="field-label">{{ __('auth_pages.confirm_password') }} <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill iico"></i>
                        <input type="password" name="password_confirmation" class="field-input"
                            placeholder="Repeat your password"
                            id="pw2" required autocomplete="new-password">
                        <button type="button" class="pw-toggle" onclick="togglePw('pw2','pw2Icon')">
                            <i class="bi bi-eye-fill" id="pw2Icon"></i>
                        </button>
                    </div>
                    <div class="field-error" id="pwMatchErr">Passwords do not match</div>
                </div>

                <!-- Next button -->
                <button type="button" class="btn-submit-main mt-2" onclick="goToStep2()">
                    <span class="btn-label d-flex align-items-center gap-2">
                        Continue to Identity Verification
                        <i class="bi bi-arrow-right"></i>
                    </span>
                    <div class="spinner"></div>
                </button>
            </div>


            <!-- ════ STEP 2 ════ -->
            <div id="step2" style="display:none;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <button type="button" onclick="goToStep1()"
                        style="background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:8px;color:var(--muted);padding:0.4rem 0.75rem;cursor:pointer;font-size:0.82rem;transition:all 0.2s;"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--muted)'">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                    <div>
                        <div class="form-section-title" style="font-size:1.3rem;">Identity Verification</div>
                        <p class="form-section-sub" style="margin:0;">Step 2 of 2 — Upload your ID document</p>
                    </div>
                </div>

                <!-- Document type -->
                <div class="field-group">
                    <label class="field-label">{{ __('auth_pages.document_type') }} <span class="req">*</span></label>
                    <select name="id_document_type" class="field-select doc-select" required id="docType">
                        <option value="" disabled selected>Select your ID type</option>
                        <option value="national_id" {{ old('id_document_type') == 'national_id' ? 'selected' : '' }}>National ID Card</option>
                        <option value="passport" {{ old('id_document_type') == 'passport' ? 'selected' : '' }}>Passport</option>
                        <option value="drivers_license" {{ old('id_document_type') == 'drivers_license' ? 'selected' : '' }}>Driver&apos;s License</option>
                    </select>
                    
                    <!-- Custom styled document type cards (alternative to dropdown) -->
                    <div class="doc-type-cards mt-3" id="docTypeCards">
                        <div class="doc-type-card" data-value="national_id" onclick="selectDocType('national_id')">
                            <i class="bi bi-person-vcard-fill"></i>
                            <span>National ID Card</span>
                        </div>
                        <div class="doc-type-card" data-value="passport" onclick="selectDocType('passport')">
                            <i class="bi bi-journal-bookmark-fill"></i>
                            <span>Passport</span>
                        </div>
                        <div class="doc-type-card" data-value="drivers_license" onclick="selectDocType('drivers_license')">
                            <i class="bi bi-car-front-fill"></i>
                            <span>Driver&apos;s License</span>
                        </div>
                    </div>
                </div>

                <!-- Upload zone -->
                <div class="field-group">
                    <label class="field-label">{{ __('auth_pages.upload_document') }} <span class="req">*</span></label>
                    <div class="upload-zone" id="dropZone">
                        <input type="file" name="id_document" id="fileInput"
                            accept=".pdf,.jpg,.jpeg,.png"
                            onchange="handleFile(this)" required>
                        <div class="upload-icon" id="uploadIcon">
                            <i class="bi bi-cloud-upload-fill"></i>
                        </div>
                        <div class="upload-title" id="uploadTitle">Drag &amp; drop your document here</div>
                        <div class="upload-sub" id="uploadSub">or click to browse &bull; PDF, JPG, PNG &bull; Max 5MB</div>
                    </div>

                    <!-- File preview -->
                    <div class="file-preview" id="filePreview">
                        <i class="bi bi-file-earmark-check-fill" style="color:var(--emerald-light);font-size:1.2rem;flex-shrink:0;"></i>
                        <span class="file-preview-name" id="previewName">document.pdf</span>
                        <span class="file-preview-size" id="previewSize">—</span>
                        <button type="button" onclick="removeFile()"
                            style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:0.9rem;padding:0;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>

                <!-- Privacy note -->
                <div style="background:rgba(214,158,46,0.07);border:1px solid rgba(214,158,46,0.2);border-radius:12px;padding:1rem 1.25rem;display:flex;gap:0.75rem;margin-bottom:1.5rem;">
                    <i class="bi bi-lock-fill flex-shrink-0 mt-1" style="color:var(--gold);font-size:0.9rem;"></i>
                    <p style="font-size:0.82rem;color:var(--muted);line-height:1.6;margin:0;">
                        Your document is <strong style="color:var(--navy);">AES-256 encrypted</strong> and stored in our secure vault. It will only be accessed by authorised government officers for identity verification. You can request deletion at any time.
                    </p>
                </div>

                <hr class="section-divider">

                <!-- Terms -->
                <div class="terms-row">
                    <input type="checkbox" name="terms_accepted" id="terms" required>
                    <span>
                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>, and consent to the storage of my identity document for verification purposes.
                    </span>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit-main" id="submitBtn">
                    <span class="btn-label d-flex align-items-center gap-2">
                        <i class="bi bi-person-check-fill"></i>
                        Create My Account
                    </span>
                    <div class="spinner"></div>
                </button>

                <div class="security-note">
                    <i class="bi bi-shield-lock-fill"></i>
                    256-bit SSL &middot; GDPR Compliant &middot; Secure Storage
                </div>
            </div>

        </form>

        <p style="text-align:center;font-size:0.85rem;color:var(--muted);margin-top:2rem;">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--gold);text-decoration:none;">Sign In &rarr;</a>
        </p>
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── Step navigation ── */
function goToStep2() {
    const s1Inputs = document.querySelectorAll('#step1 [required]');
    let valid = true;
    s1Inputs.forEach(i => { if (!i.value.trim()) { i.classList.add('is-invalid'); valid = false; } else i.classList.remove('is-invalid'); });

    const pw  = document.getElementById('pw').value;
    const pw2 = document.getElementById('pw2').value;
    if (pw !== pw2) {
        document.getElementById('pw2').classList.add('is-invalid');
        document.getElementById('pwMatchErr').style.display = 'block';
        valid = false;
    }

    if (!valid) return;

    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
    window.scrollTo(0,0);

    // Update sidebar
    updateStep(2);
}

function goToStep1() {
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
    updateStep(1);
}

function updateStep(n) {
    for (let i = 1; i <= 3; i++) {
        const el = document.getElementById('step-indicator-' + i);
        const sc = document.getElementById('sc' + i);
        if (!el) return;
        el.classList.remove('active', 'done');
        if (i < n) { el.classList.add('done'); sc.innerHTML = '<i class="bi bi-check-lg"></i>'; }
        else if (i === n) { el.classList.add('active'); sc.textContent = i; }
        else { sc.textContent = i; }
    }
}

/* ── Password strength ── */
document.getElementById('pw').addEventListener('input', function () {
    const v = this.value;
    let score = 0;
    if (v.length >= 8)  score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[@$!%*?&]/.test(v)) score++;

    const bar = document.getElementById('pwBar');
    const lbl = document.getElementById('pwLabel');
    const map = [
        { w:'0%',   bg:'',                       t:'' },
        { w:'25%',  bg:'#ef4444',                 t:'Weak' },
        { w:'50%',  bg:'#f59e0b',                 t:'Fair' },
        { w:'75%',  bg:'#3b82f6',                 t:'Good' },
        { w:'100%', bg:'var(--emerald-light)',     t:'Strong' },
    ];
    bar.style.width      = map[score].w;
    bar.style.background = map[score].bg;
    lbl.textContent      = map[score].t;
    lbl.style.color      = map[score].bg || 'var(--muted)';
});

/* ── Real-time field-level validation ── */
function setFieldState(inputEl, feedbackEl, ok, message) {
    if (message === '') {
        inputEl.classList.remove('field-valid', 'field-invalid');
        feedbackEl.style.display = 'none';
        return;
    }
    inputEl.classList.toggle('field-valid', ok);
    inputEl.classList.toggle('field-invalid', !ok);
    feedbackEl.textContent = message;
    feedbackEl.className = 'field-feedback ' + (ok ? 'is-ok' : 'is-error');
    feedbackEl.style.display = 'block';
}

// Names — letters, spaces, hyphens, apostrophes only (Latin or Arabic)
const namePattern = /^[A-Za-z؀-ۿ\s'\-]+$/;
['firstName', 'lastName'].forEach(key => {
    const input = document.getElementById(key + 'Field');
    const feedback = document.getElementById(key + 'Feedback');
    if (!input) return;
    input.addEventListener('input', () => {
        const v = input.value.trim();
        if (v === '') { setFieldState(input, feedback, true, ''); return; }
        if (!namePattern.test(v)) {
            setFieldState(input, feedback, false, 'Letters, spaces, and hyphens only.');
        } else {
            setFieldState(input, feedback, true, '');
        }
    });
});

// Email — basic RFC-shaped check
const emailField = document.getElementById('emailField');
const emailFeedback = document.getElementById('emailFeedback');
if (emailField) {
    emailField.addEventListener('input', () => {
        const v = emailField.value.trim();
        if (v === '') { setFieldState(emailField, emailFeedback, true, ''); return; }
        const ok = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v);
        setFieldState(emailField, emailFeedback, ok, ok ? 'Looks good.' : 'Enter a valid email address.');
    });
}

// Phone — Lebanese mobile/landline format (mirrors server-side LebanesePhoneNumber rule)
const phoneField = document.getElementById('phoneField');
const phoneFeedback = document.getElementById('phoneFeedback');
if (phoneField) {
    phoneField.addEventListener('input', () => {
        const v = phoneField.value.trim();
        if (v === '') { setFieldState(phoneField, phoneFeedback, true, ''); return; }
        const normalized = v.replace(/[\s\-\(\)]+/g, '');
        const ok = /^(?:\+?961|00961)?0?(3\d{6}|7[01689]\d{6}|81\d{6}|[1456789]\d{6,7})$/.test(normalized);
        setFieldState(phoneField, phoneFeedback, ok, ok ? 'Looks good.' : 'Enter a valid Lebanese number, e.g. +961 70 123 456.');
    });
}

/* ── Password visibility toggle ── */
function togglePw(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
}

/* ── Drag & Drop ── */
const zone = document.getElementById('dropZone');
zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('dragover'); });
zone.addEventListener('dragleave', ()  => zone.classList.remove('dragover'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
        document.getElementById('fileInput').files = e.dataTransfer.files;
        handleFile(document.getElementById('fileInput'));
    }
});

function handleFile(input) {
    if (!input.files.length) return;
    const file = input.files[0];
    const maxMB = 5;

    if (!['application/pdf','image/jpeg','image/jpg','image/png'].includes(file.type)) {
        alert('Invalid file type. Please upload PDF, JPG, or PNG.');
        input.value = '';
        return;
    }
    if (file.size > maxMB * 1024 * 1024) {
        alert('File is too large. Maximum size is 5MB.');
        input.value = '';
        return;
    }

    zone.classList.add('has-file');
    document.getElementById('uploadIcon').innerHTML = '<i class="bi bi-file-earmark-check-fill"></i>';
    document.getElementById('uploadTitle').textContent = 'Document selected';
    document.getElementById('uploadSub').textContent   = 'Click to change file';

    document.getElementById('previewName').textContent = file.name;
    document.getElementById('previewSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    document.getElementById('filePreview').classList.add('show');
}

function removeFile() {
    const input = document.getElementById('fileInput');
    input.value = '';
    zone.classList.remove('has-file');
    document.getElementById('uploadIcon').innerHTML = '<i class="bi bi-cloud-upload-fill"></i>';
    document.getElementById('uploadTitle').textContent = 'Drag & drop your document here';
    document.getElementById('uploadSub').textContent   = 'or click to browse • PDF, JPG, PNG • Max 5MB';
    document.getElementById('filePreview').classList.remove('show');
}

/* ── Submit loading state ── */
function handleSubmit(btn) {
    // Check if form is valid first
    const form = document.getElementById('regForm');
    const docType = document.getElementById('docType').value;
    const fileInput = document.getElementById('fileInput');
    const terms = document.getElementById('terms').checked;
    
    if (!docType || !fileInput.files.length || !terms) {
        return true; // Let browser handle validation
    }
    
    btn.classList.add('loading');
    btn.disabled = true;
    
    // Submit the form after a short delay to show loading state
    setTimeout(() => {
        form.submit();
    }, 100);
    
    return false; // Prevent double submission
}

/* ── Document type card selection ── */
function selectDocType(value) {
    // Update hidden select
    document.getElementById('docType').value = value;
    
    // Update card styles
    document.querySelectorAll('.doc-type-card').forEach(card => {
        card.classList.remove('selected');
        if (card.dataset.value === value) {
            card.classList.add('selected');
        }
    });
}

/* ── Form submit handler ── */
document.getElementById('regForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
});

/* ── Pre-fill step state and doc type from old() ── */
@if(old('id_document_type'))
    goToStep2();
    selectDocType('{{ old('id_document_type') }}');
@endif
</script>
</body>
</html>
