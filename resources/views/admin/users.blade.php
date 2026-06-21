@extends('admin.layouts.app')

@section('title', 'Citizens & Users')
@section('page-title', 'Citizens & Users')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            Citizens &amp; Users
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            Create staff accounts and manage all user roles
        </p>
    </div>
    <button class="btn-gold" onclick="document.getElementById('createUserModal').style.display='flex'">
        <i class="bi bi-person-plus-fill"></i> Add Staff / Admin
    </button>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach([
        ['Total Users',  $counts['total'],   'rgba(37,99,235,0.15)',  'rgba(37,99,235,0.25)',  '#93c5fd',    'bi-people-fill'],
        ['Admins',       $counts['admin'],   'rgba(239,68,68,0.12)',  'rgba(239,68,68,0.25)',  '#f87171',    'bi-shield-fill'],
        ['Office Staff', $counts['office'],  'rgba(214,158,46,0.15)', 'rgba(214,158,46,0.25)', 'var(--gold)','bi-building-fill'],
        ['Citizens',     $counts['citizen'], 'rgba(4,120,87,0.15)',   'rgba(4,120,87,0.3)',    '#6ee7b7',    'bi-person-check-fill'],
    ] as [$label, $count, $bg, $border, $color, $icon])
    <div class="col-6 col-xl-3">
        <div class="admin-card d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:{{ $bg }};border:1px solid {{ $border }};border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">{{ number_format($count) }}</div>
                <div style="font-size:0.78rem;color:var(--muted);">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filter --}}
<div class="admin-card mb-4">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <div style="position:relative;flex:1;min-width:180px;">
            <i class="bi bi-search" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;"></i>
            <input type="text" name="search" class="form-control-custom" style="padding-left:2.4rem;"
                   placeholder="Search name or email…" value="{{ request('search') }}">
        </div>
        <select name="role" class="form-select-custom" style="width:auto;min-width:140px;" onchange="this.form.submit()">
            <option value="">All Roles</option>
            <option value="admin"   {{ request('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
            <option value="office"  {{ request('role') === 'office'  ? 'selected' : '' }}>Office Staff</option>
            <option value="citizen" {{ request('role') === 'citizen' ? 'selected' : '' }}>Citizen</option>
        </select>
        <button type="submit" class="btn-gold" style="padding:0.6rem 1.1rem;">
            <i class="bi bi-funnel-fill"></i> Filter
        </button>
        @if(request('search') || request('role'))
            <a href="{{ route('admin.users') }}" class="btn-ghost" style="padding:0.6rem 0.9rem;"><i class="bi bi-x-lg"></i></a>
        @endif
        <span style="font-size:0.8rem;color:var(--muted);margin-left:auto;">{{ $users->total() }} user(s)</span>
    </form>
</div>

{{-- Table --}}
<div class="admin-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Change Role</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="color:var(--muted);font-size:0.78rem;">{{ $user->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;
                                {{ $user->role === 'admin' ? 'background:rgba(239,68,68,0.2);color:#f87171;border:1px solid rgba(239,68,68,0.25);' : ($user->role === 'office' ? 'background:rgba(214,158,46,0.15);color:var(--gold);border:1px solid rgba(214,158,46,0.2);' : 'background:rgba(37,99,235,0.15);color:#93c5fd;border:1px solid rgba(37,99,235,0.2);') }}">
                                {{ strtoupper(substr($user->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;color:#fff;font-size:0.875rem;">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                    @if($user->id === auth()->id())
                                        <span style="background:rgba(100,116,139,0.2);color:var(--muted);font-size:0.65rem;padding:0.1rem 0.45rem;border-radius:4px;margin-left:0.3rem;">You</span>
                                    @endif
                                </div>
                                <div style="font-size:0.75rem;color:var(--muted);">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($user->role === 'admin')
                            <span style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.25);color:#f87171;font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">Admin</span>
                        @elseif($user->role === 'office')
                            <span style="background:rgba(214,158,46,0.12);border:1px solid rgba(214,158,46,0.2);color:var(--gold);font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">Office</span>
                        @else
                            <span style="background:rgba(37,99,235,0.12);border:1px solid rgba(37,99,235,0.2);color:#93c5fd;font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">Citizen</span>
                        @endif
                    </td>
                    <td style="color:var(--muted);font-size:0.8rem;">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.role', $user->id) }}">
                            @csrf @method('PATCH')
                            <div class="d-flex gap-2 align-items-center">
                                <select name="role" class="form-select-custom" style="width:auto;padding:0.35rem 0.75rem;font-size:0.8rem;">
                                    <option value="admin"   {{ $user->role === 'admin'   ? 'selected' : '' }}>Admin</option>
                                    <option value="office"  {{ $user->role === 'office'  ? 'selected' : '' }}>Office</option>
                                    <option value="citizen" {{ $user->role === 'citizen' ? 'selected' : '' }}>Citizen</option>
                                </select>
                                <button type="submit" class="btn-ghost" style="padding:0.35rem 0.75rem;font-size:0.8rem;">Save</button>
                            </div>
                        </form>
                        @else
                            <span style="color:var(--muted);font-size:0.8rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex justify-content-end">
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                  onsubmit="return confirm('Delete {{ $user->first_name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-soft">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:4rem;color:var(--muted);">
                        <i class="bi bi-people" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.4;"></i>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:0.8rem;color:var(--muted);">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
        </span>
        <div class="d-flex gap-2">
            @if($users->onFirstPage())
                <span class="btn-ghost" style="opacity:0.4;cursor:not-allowed;padding:0.4rem 0.85rem;font-size:0.8rem;"><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="btn-ghost" style="padding:0.4rem 0.85rem;font-size:0.8rem;"><i class="bi bi-chevron-left"></i></a>
            @endif
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="btn-ghost" style="padding:0.4rem 0.85rem;font-size:0.8rem;"><i class="bi bi-chevron-right"></i></a>
            @else
                <span class="btn-ghost" style="opacity:0.4;cursor:not-allowed;padding:0.4rem 0.85rem;font-size:0.8rem;"><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Create User Modal --}}
<div id="createUserModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#162947;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:500px;width:90%;margin:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
            <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin:0;">
                <i class="bi bi-person-plus-fill me-2" style="color:var(--gold);"></i>Create New Account
            </h5>
            <button onclick="document.getElementById('createUserModal').style.display='none'"
                    style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.2rem;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @if($errors->any())
                <div class="alert-error-custom mb-3" style="font-size:0.85rem;">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label-custom">Role <span style="color:#f87171;">*</span></label>
                <select name="role" class="form-select-custom" required>
                    <option value="office"  {{ old('role') === 'office'  ? 'selected' : '' }}>Office Staff</option>
                    <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                    <option value="citizen" {{ old('role') === 'citizen' ? 'selected' : '' }}>Citizen</option>
                </select>
                <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">Select "Office Staff" to create a municipality employee account.</div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col">
                    <label class="form-label-custom">First Name <span style="color:#f87171;">*</span></label>
                    <input type="text" name="first_name" class="form-control-custom"
                           value="{{ old('first_name') }}" required placeholder="Maria">
                </div>
                <div class="col">
                    <label class="form-label-custom">Last Name <span style="color:#f87171;">*</span></label>
                    <input type="text" name="last_name" class="form-control-custom"
                           value="{{ old('last_name') }}" required placeholder="Santos">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label-custom">Email <span style="color:#f87171;">*</span></label>
                <input type="email" name="email" class="form-control-custom"
                       value="{{ old('email') }}" required placeholder="staff@municipality.gov">
            </div>

            <div class="mb-3">
                <label class="form-label-custom">Password <span style="color:#f87171;">*</span></label>
                <input type="password" name="password" class="form-control-custom"
                       required placeholder="Min. 8 characters" minlength="8">
            </div>

            <div class="mb-4">
                <label class="form-label-custom">Confirm Password <span style="color:#f87171;">*</span></label>
                <input type="password" name="password_confirmation" class="form-control-custom"
                       required placeholder="Repeat password" minlength="8">
            </div>

            <div class="d-flex gap-3">
                <button type="button" onclick="document.getElementById('createUserModal').style.display='none'"
                        class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                <button type="submit" class="btn-gold" style="flex:1;justify-content:center;">
                    <i class="bi bi-person-check-fill"></i> Create Account
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('createUserModal').style.display = 'flex';
    });
</script>
@endif
<script>
    document.getElementById('createUserModal').addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
</script>
@endpush
