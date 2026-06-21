{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background: #F8FAFC;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: #1E3A5F;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .75);
            padding: .6rem 1.2rem;
            border-radius: 6px;
            margin: 2px 8px;
            transition: background .15s, color .15s;
            font-size: .9rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, .12);
            color: #fff;
        }

        .sidebar .nav-link svg {
            margin-right: 8px;
            flex-shrink: 0;
        }

        .main-content {
            margin-left: 240px;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            height: 56px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .25s;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    {{-- ── Sidebar ── --}}
    <nav class="sidebar d-flex flex-column">
        <div class="px-4 py-4 border-bottom border-white border-opacity-10">
            <h6 class="text-white fw-bold mb-0">{{ config('app.name') }}</h6>
            <small style="color:rgba(255,255,255,.5)">
                {{ auth()->user()->role === 'admin' ? 'Admin Panel' : 'Office Staff' }}
            </small>
        </div>
        <ul class="nav flex-column flex-grow-1 mt-3">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4zM3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10zm9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5zm.754-4.246a.389.389 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.389.389 0 0 0-.029-.518z" />
                        <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A7.988 7.988 0 0 1 0 10zm8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.63A7 7 0 0 0 8 3z" />
                    </svg>
                    Dashboard
                </a>
            </li>
            @if(auth()->user()->role === 'admin')
            <li class="nav-item">
                <a href="{{ route('admin.users') }}"
                   class="nav-link d-flex align-items-center {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z" />
                        <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
                    </svg>
                    Users & Staff
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a href="{{ route('office.dashboard') }}"
                   class="nav-link d-flex align-items-center {{ request()->routeIs('office.*') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                        <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z" />
                    </svg>
                    Service Requests
                </a>
            </li>
        </ul>
        <div class="px-4 py-3 border-top border-white border-opacity-10">
            <small style="color:rgba(255,255,255,.5)">Logged in as</small>
            <p class="text-white mb-0 small fw-semibold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
            <div class="d-flex gap-2 mt-1 flex-wrap">
                <a href="{{ route('profile.edit') }}"
                   class="btn btn-sm btn-link text-white-50 p-0" style="font-size:.8rem">
                    <i class="bi bi-person me-1"></i>Profile
                </a>
                <a href="{{ route('2fa.setup') }}"
                   class="btn btn-sm btn-link text-white-50 p-0" style="font-size:.8rem">
                    <i class="bi bi-shield-lock me-1"></i>2FA Setup
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm btn-link text-white-50 p-0" style="font-size:.8rem">
                        <i class="bi bi-box-arrow-right me-1"></i>Sign out
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- ── Main Content ── --}}
    <div class="main-content">
        <header class="topbar d-flex align-items-center px-4 sticky-top">
            <button class="btn btn-sm d-md-none me-3" onclick="document.querySelector('.sidebar').classList.toggle('show')">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                </svg>
            </button>
            <span class="fw-semibold" style="color:#1E3A5F">@yield('title', 'Dashboard')</span>
            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border">{{ auth()->user()->role }}</span>
            </div>
        </header>
        <main>
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>