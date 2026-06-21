@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')

@push('styles')
<style>
    .profile-tabs { display:flex; gap:0; border-bottom:1px solid var(--border); margin-bottom:2rem; overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .profile-tab {
        padding:0.75rem 1.5rem; font-size:0.875rem; font-weight:500;
        color:var(--muted); border:none; background:none; cursor:pointer;
        border-bottom:2px solid transparent; transition:all 0.2s; display:inline-flex;
        align-items:center; gap:0.4rem; white-space:nowrap; flex-shrink:0;
    }
    .profile-tab:hover { color:var(--navy); }
    .profile-tab.active { color:var(--gold); border-bottom-color:var(--gold); font-weight:600; }
    .profile-panel { display:none; }
    .profile-panel.active { display:block; }
</style>
@endpush

<div style="max-width:720px;margin:0 auto;">

    <div style="margin-bottom:2rem;">
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:var(--navy);margin:0 0 0.25rem;">
            {{ __('app.nav_profile') }}
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">{{ app()->getLocale() === 'ar' ? 'إدارة معلومات حسابك وإعدادات الأمان' : 'Manage your account information and security settings' }}</p>
    </div>

    @php $isAr = app()->getLocale() === 'ar'; @endphp

    {{-- Tabs --}}
    <div class="profile-tabs">
        <button class="profile-tab {{ session('status') === 'password-updated' ? '' : 'active' }}"
                onclick="switchTab('contact', this)">
            <i class="bi bi-person"></i> {{ $isAr ? 'معلومات التواصل' : 'Contact Information' }}
        </button>
        <button class="profile-tab {{ session('status') === 'password-updated' ? 'active' : '' }}"
                onclick="switchTab('password', this)">
            <i class="bi bi-shield-lock"></i> {{ $isAr ? 'تغيير كلمة المرور' : 'Change Password' }}
        </button>
        @if(in_array(auth()->user()->role, ['admin', 'office']))
        <button class="profile-tab" onclick="switchTab('twofa', this)">
            <i class="bi bi-phone"></i> {{ $isAr ? 'تطبيق المصادقة' : 'Authenticator App' }}
        </button>
        @endif
    </div>

    {{-- Contact Tab --}}
    <div class="profile-panel {{ session('status') === 'password-updated' ? '' : 'active' }}" id="panel-contact">

        @if(session('status') === 'profile-updated')
            <div style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.35);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#065f46;display:flex;align-items:center;gap:0.75rem;">
                <i class="bi bi-check-circle-fill"></i> {{ $isAr ? 'تم تحديث معلومات التواصل!' : 'Contact information updated!' }}
            </div>
        @endif

        <div class="app-card">
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:1.5rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
                {{ $isAr ? 'تحديث معلومات التواصل' : 'Update Contact Information' }}
            </div>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label-custom">{{ __('pages.first_name') }} <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="first_name"
                               class="form-control-custom @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name')<div style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">{{ __('pages.last_name') }} <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="last_name"
                               class="form-control-custom @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')<div style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">{{ __('pages.email_address') }} <span style="color:#dc2626;">*</span></label>
                    <input type="email" name="email"
                           class="form-control-custom @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">{{ $isAr ? 'رقم الهاتف' : 'Phone Number' }}</label>
                    <input type="tel" name="phone"
                           class="form-control-custom @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $user->phone ?? '') }}"
                           placeholder="+961 XX XXX XXX">
                    @error('phone')<div style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">{{ $isAr ? 'العنوان' : 'Address' }}</label>
                    <textarea name="address" rows="2"
                              class="form-control-custom @error('address') is-invalid @enderror"
                              placeholder="{{ $isAr ? 'الشارع، المدينة، البلد' : 'Street, City, Country' }}">{{ old('address', $user->address ?? '') }}</textarea>
                    @error('address')<div style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn-gold">
                    <i class="bi bi-floppy"></i> {{ $isAr ? 'حفظ التغييرات' : 'Save Changes' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Password Tab --}}
    <div class="profile-panel {{ session('status') === 'password-updated' ? 'active' : '' }}" id="panel-password">

        @if(session('status') === 'password-updated')
            <div style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.35);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#065f46;display:flex;align-items:center;gap:0.75rem;">
                <i class="bi bi-check-circle-fill"></i> {{ $isAr ? 'تم تحديث كلمة المرور بنجاح!' : 'Password updated successfully!' }}
            </div>
        @endif

        <div class="app-card">
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:1.5rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
                {{ $isAr ? 'تغيير كلمة المرور' : 'Change Password' }}
            </div>
            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label-custom">{{ $isAr ? 'كلمة المرور الحالية' : 'Current Password' }}</label>
                    <input type="password" name="current_password"
                           class="form-control-custom @error('current_password') is-invalid @enderror"
                           required>
                    @error('current_password')<div style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">{{ $isAr ? 'كلمة المرور الجديدة' : 'New Password' }}</label>
                    <input type="password" name="password"
                           class="form-control-custom @error('password') is-invalid @enderror"
                           required>
                    @error('password')<div style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>@enderror
                    <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">{{ $isAr ? 'الحد الأدنى 8 أحرف.' : 'Minimum 8 characters.' }}</div>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">{{ __('pages.confirm_password') }}</label>
                    <input type="password" name="password_confirmation"
                           class="form-control-custom" required>
                </div>

                <button type="submit" class="btn-gold">
                    <i class="bi bi-shield-check"></i> {{ $isAr ? 'تحديث كلمة المرور' : 'Update Password' }}
                </button>
            </form>
        </div>
    </div>

    {{-- 2FA Tab --}}
    @if(in_array(auth()->user()->role, ['admin', 'office']))
    <div class="profile-panel" id="panel-twofa">
        <div class="app-card">
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:0.4rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
                {{ $isAr ? 'تطبيق المصادقة (2FA)' : 'Authenticator App (2FA)' }}
            </div>
            <p style="color:var(--muted);font-size:0.85rem;margin:1rem 0 1.5rem;">
                {{ $isAr ? 'اسحب رمز QR باستخدام Google Authenticator أو Authy لتفعيل المصادقة الثنائية على حسابك.' : 'Scan the QR code with Google Authenticator or Authy to enable two-factor authentication on your account.' }}
            </p>
            @if(auth()->user()->two_factor_secret)
                <div style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.35);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#065f46;display:flex;align-items:center;gap:0.75rem;">
                    <i class="bi bi-shield-check-fill" style="font-size:1.1rem;"></i>
                    <div><strong>{{ $isAr ? 'المصادقة الثنائية مفعّلة.' : '2FA is active.' }}</strong> {{ $isAr ? 'سيُطلب منك رمز من تطبيقك في كل تسجيل دخول.' : 'You will be asked for a code from your app on every login.' }}</div>
                </div>
                <a href="{{ route('2fa.setup') }}" class="btn-ghost">
                    <i class="bi bi-arrow-repeat"></i> {{ $isAr ? 'إعادة تعيين / إعادة مسح رمز QR' : 'Reset / Re-scan QR Code' }}
                </a>
            @else
                <div style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.3);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#92400e;display:flex;align-items:center;gap:0.75rem;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size:1.1rem;"></i>
                    <div><strong>{{ $isAr ? 'المصادقة الثنائية غير مُفعّلة.' : '2FA is not set up.' }}</strong> {{ $isAr ? 'حسابات الموظفين محمية برمز بريد إلكتروني حتى تقوم بإعداد هذا.' : 'Staff accounts are protected by email code until you set this up.' }}</div>
                </div>
                <a href="{{ route('2fa.setup') }}" class="btn-gold">
                    <i class="bi bi-qr-code"></i> {{ $isAr ? 'إعداد تطبيق المصادقة' : 'Set Up Authenticator App' }}
                </a>
            @endif
        </div>
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
function switchTab(id, btn) {
    document.querySelectorAll('.profile-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
 