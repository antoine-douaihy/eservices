<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'E-Services') }} — Government Online Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; }

        /* Navbar */
        .navbar-brand { font-weight: 800; font-size: 1.2rem; color: #1E3A5F !important; }
        .navbar-brand span { color: #2563EB; }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, #0f2544 0%, #1E3A5F 50%, #1a4a7a 100%);
            min-height: 90vh;
            display: flex; align-items: center;
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -100px; right: -100px;
            width: 500px; height: 500px;
            background: rgba(37,99,235,0.15); border-radius: 50%;
        }
        .hero::after {
            content: ''; position: absolute; bottom: -80px; left: -80px;
            width: 350px; height: 350px;
            background: rgba(255,255,255,0.04); border-radius: 50%;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50px; padding: 0.35rem 1rem;
            color: rgba(255,255,255,0.85); font-size: 0.8rem; margin-bottom: 1.5rem;
        }
        .hero h1 { color: #fff; font-size: 3.2rem; font-weight: 800; line-height: 1.15; }
        .hero h1 span { color: #60A5FA; }
        .hero p { color: rgba(255,255,255,0.7); font-size: 1.1rem; line-height: 1.7; }

        .btn-hero-primary {
            background: #2563EB; color: #fff; border: none;
            padding: 0.85rem 2rem; border-radius: 8px;
            font-weight: 600; font-size: 1rem;
            text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-hero-primary:hover { background: #1d4ed8; color: #fff; transform: translateY(-1px); }

        .btn-hero-outline {
            background: transparent; color: #fff;
            border: 1.5px solid rgba(255,255,255,0.35);
            padding: 0.85rem 2rem; border-radius: 8px;
            font-weight: 600; font-size: 1rem;
            text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;
            transition: border-color 0.2s, background 0.2s;
        }
        .btn-hero-outline:hover { border-color: rgba(255,255,255,0.7); background: rgba(255,255,255,0.06); color: #fff; }

        /* Stats */
        .stat-row { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2.5rem; margin-top: 2.5rem; }
        .stat-item .num { color: #fff; font-size: 2rem; font-weight: 700; }
        .stat-item .lbl { color: rgba(255,255,255,0.55); font-size: 0.85rem; }

        /* Hero visual card */
        .hero-card {
            background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px; padding: 1.5rem; position: relative; z-index: 1;
        }
        .status-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 50px; font-weight: 500;
        }
        .req-row {
            background: rgba(255,255,255,0.06); border-radius: 10px;
            padding: 0.85rem 1rem; display: flex; align-items: center;
            gap: 0.8rem; margin-bottom: 0.6rem;
        }
        .req-icon {
            width: 36px; height: 36px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 1rem;
        }

        /* Features */
        .features { padding: 6rem 0; background: #F8FAFC; }
        .feature-card {
            background: #fff; border-radius: 14px;
            padding: 2rem; border: 1px solid #E5E7EB;
            transition: box-shadow 0.2s, transform 0.2s;
            height: 100%;
        }
        .feature-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); transform: translateY(-3px); }
        .feature-icon {
            width: 52px; height: 52px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; margin-bottom: 1.2rem;
        }
        .feature-card h5 { font-weight: 700; color: #1E3A5F; margin-bottom: 0.6rem; }
        .feature-card p { color: #6B7280; font-size: 0.9rem; line-height: 1.6; margin: 0; }

        /* Services */
        .services { padding: 5rem 0; }
        .service-item {
            text-align: center; padding: 1.5rem;
            border-radius: 12px; transition: background 0.2s;
        }
        .service-item:hover { background: #F1F5F9; }
        .service-item .si-icon {
            width: 56px; height: 56px; border-radius: 14px;
            background: #DBEAFE; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #2563EB; margin: 0 auto 1rem;
        }
        .service-item h6 { font-weight: 600; color: #1E3A5F; margin-bottom: 0.3rem; font-size: 0.9rem; }
        .service-item p { color: #9CA3AF; font-size: 0.8rem; margin: 0; }

        /* CTA */
        .cta-section {
            background: linear-gradient(135deg, #1E3A5F 0%, #2563EB 100%);
            padding: 5rem 0; text-align: center;
        }

        /* Footer */
        footer { background: #0f1d2e; color: rgba(255,255,255,0.5); padding: 2rem 0; font-size: 0.85rem; }

        /* Lang switch */
        .lang-switch-welcome { display:flex; align-items:center; gap:0.3rem; background:#f1f5f9; border-radius:8px; padding:0.25rem 0.4rem; }
        .lang-switch-welcome a { font-size:0.75rem; font-weight:600; padding:0.2rem 0.5rem; border-radius:6px; text-decoration:none; color:#64748b; }
        .lang-switch-welcome a.active { background:#1E3A5F; color:#fff; }

        html[dir="rtl"] body { font-family: 'Tahoma', 'Segoe UI', sans-serif; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top" style="box-shadow:0 1px 8px rgba(0,0,0,0.06)">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-building me-1"></i>E-<span>Services</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#features">{{ __('welcome.nav_features') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">{{ __('welcome.nav_services') }}</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center mt-2 mt-lg-0">
                    <div class="lang-switch-welcome">
                        <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                        <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">AR</a>
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3">{{ __('welcome.sign_in') }}</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-3">{{ __('welcome.register') }}</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container position-relative" style="z-index:1">
            <div class="row align-items-center g-5">
                <div class="col-12 col-lg-6">
                    <div class="hero-badge">
                        <i class="bi bi-patch-check-fill" style="color:#34D399;"></i>
                        {{ __('welcome.hero_badge') }}
                    </div>
                    <h1>{{ __('welcome.hero_title_1') }}<br>{{ __('welcome.hero_title_2') }}</h1>
                    <p class="mt-3 mb-4">
                        {{ __('welcome.hero_subtitle') }}
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('register') }}" class="btn-hero-primary">
                            <i class="bi bi-person-plus"></i> {{ __('welcome.get_started_free') }}
                        </a>
                        <a href="{{ route('login') }}" class="btn-hero-outline">
                            <i class="bi bi-box-arrow-in-right"></i> {{ __('welcome.sign_in') }}
                        </a>
                    </div>
                    <div class="stat-row row g-3">
                        <div class="col-4 stat-item">
                            <div class="num">50K+</div>
                            <div class="lbl">{{ __('welcome.stat_citizens') }}</div>
                        </div>
                        <div class="col-4 stat-item">
                            <div class="num">40+</div>
                            <div class="lbl">{{ __('welcome.stat_services') }}</div>
                        </div>
                        <div class="col-4 stat-item">
                            <div class="num">99.9%</div>
                            <div class="lbl">{{ __('welcome.stat_uptime') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 d-none d-lg-block">
                    <div class="hero-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span style="color:#fff;font-weight:600;font-size:.9rem;">
                                <i class="bi bi-list-task me-2" style="color:#60A5FA;"></i>{{ __('welcome.my_requests') }}
                            </span>
                            <span class="status-badge" style="background:rgba(52,211,153,0.15);color:#34D399;">
                                <i class="bi bi-circle-fill" style="font-size:.45rem;"></i> {{ __('welcome.live') }}
                            </span>
                        </div>
                        <div class="req-row">
                            <div class="req-icon" style="background:rgba(250,204,21,0.15);">
                                <i class="bi bi-file-earmark-text" style="color:#FBBF24;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="color:#fff;font-size:.85rem;font-weight:500;">Birth Certificate</div>
                                <div style="color:rgba(255,255,255,0.45);font-size:.75rem;">REQ-AB12CD34</div>
                            </div>
                            <span class="status-badge" style="background:rgba(250,204,21,0.15);color:#FBBF24;">{{ __('welcome.pending') }}</span>
                        </div>
                        <div class="req-row">
                            <div class="req-icon" style="background:rgba(96,165,250,0.15);">
                                <i class="bi bi-building" style="color:#60A5FA;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="color:#fff;font-size:.85rem;font-weight:500;">Business Permit</div>
                                <div style="color:rgba(255,255,255,0.45);font-size:.75rem;">REQ-EF56GH78</div>
                            </div>
                            <span class="status-badge" style="background:rgba(96,165,250,0.15);color:#60A5FA;">{{ __('welcome.in_progress') }}</span>
                        </div>
                        <div class="req-row" style="margin-bottom:0">
                            <div class="req-icon" style="background:rgba(52,211,153,0.15);">
                                <i class="bi bi-house" style="color:#34D399;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="color:#fff;font-size:.85rem;font-weight:500;">Barangay Clearance</div>
                                <div style="color:rgba(255,255,255,0.45);font-size:.75rem;">REQ-IJ90KL12</div>
                            </div>
                            <span class="status-badge" style="background:rgba(52,211,153,0.15);color:#34D399;">{{ __('welcome.completed') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3">{{ __('welcome.why_choose_us') }}</span>
                <h2 class="fw-bold" style="color:#1E3A5F">{{ __('welcome.features_title') }}</h2>
                <p class="text-muted mx-auto" style="max-width:500px">
                    {{ __('welcome.features_subtitle') }}
                </p>
            </div>
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background:#DBEAFE;">
                            <i class="bi bi-qr-code" style="color:#2563EB;"></i>
                        </div>
                        <h5>{{ __('welcome.feature_qr_title') }}</h5>
                        <p>{{ __('welcome.feature_qr_body') }}</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background:#DCFCE7;">
                            <i class="bi bi-chat-dots" style="color:#16A34A;"></i>
                        </div>
                        <h5>{{ __('welcome.feature_chat_title') }}</h5>
                        <p>{{ __('welcome.feature_chat_body') }}</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background:#EDE9FE;">
                            <i class="bi bi-shield-lock" style="color:#7C3AED;"></i>
                        </div>
                        <h5>{{ __('welcome.feature_2fa_title') }}</h5>
                        <p>{{ __('welcome.feature_2fa_body') }}</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background:#FEF3C7;">
                            <i class="bi bi-star" style="color:#D97706;"></i>
                        </div>
                        <h5>{{ __('welcome.feature_rating_title') }}</h5>
                        <p>{{ __('welcome.feature_rating_body') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="services" id="services">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 mb-3">{{ __('welcome.available_services_badge') }}</span>
                <h2 class="fw-bold" style="color:#1E3A5F">{{ __('welcome.services_title') }}</h2>
                <p class="text-muted">{{ __('welcome.services_subtitle') }}</p>
            </div>
            <div class="row g-2">
                @foreach([
                    ['bi-file-earmark-person','Barangay Clearance','Residency verification'],
                    ['bi-building','Business Permit','License registration'],
                    ['bi-heart-pulse','Birth Certificate','Civil registry records'],
                    ['bi-people','Marriage Certificate','Civil registry records'],
                    ['bi-file-earmark-text','Community Tax','Cedula issuance'],
                    ['bi-house-check','Building Permit','Construction approval'],
                    ['bi-car-front','Driver\'s License','LTO documentation'],
                    ['bi-passport','Passport Assistance','DFA appointment help'],
                ] as [$icon, $title, $sub])
                <div class="col-6 col-md-3">
                    <div class="service-item">
                        <div class="si-icon"><i class="bi {{ $icon }}"></i></div>
                        <h6>{{ $title }}</h6>
                        <p>{{ $sub }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <h2 class="text-white fw-bold mb-3">{{ __('welcome.cta_title') }}</h2>
            <p class="text-white mb-4" style="opacity:.75;max-width:480px;margin:0 auto 1.5rem;">
                {{ __('welcome.cta_subtitle') }}
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-light fw-semibold px-4 py-2">
                    <i class="bi bi-person-plus me-2"></i>{{ __('welcome.create_free_account') }}
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light fw-semibold px-4 py-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('welcome.sign_in') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container d-flex flex-wrap justify-content-between align-items-center">
            <span><i class="bi bi-building me-1"></i>E-Services &copy; {{ date('Y') }}. {{ __('welcome.footer_text') }}</span>
            <span>
                <i class="bi bi-shield-lock me-1"></i>{{ __('welcome.footer_security') }}
            </span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
