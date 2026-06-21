@extends('admin.layouts.app')

@section('title', 'Edit Staff Member')
@section('page-title', 'Edit Staff Member')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            Edit Staff Member
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            {{ $staff->first_name }} {{ $staff->last_name }}
        </p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn-ghost">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-8">

@if($errors->any())
    <div class="alert-error-custom mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<div class="admin-card">
    <form method="POST" action="{{ route('admin.staff.update', $staff->id) }}">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">First Name <span style="color:#f87171;">*</span></label>
                <input type="text" class="form-control-custom" name="first_name"
                       value="{{ old('first_name', $staff->first_name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Last Name <span style="color:#f87171;">*</span></label>
                <input type="text" class="form-control-custom" name="last_name"
                       value="{{ old('last_name', $staff->last_name) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Email Address <span style="color:#f87171;">*</span></label>
            <input type="email" class="form-control-custom" name="email"
                   value="{{ old('email', $staff->email) }}" required>
        </div>

        <div class="mb-4">
            <label class="form-label-custom">Account Status <span style="color:#f87171;">*</span></label>
            <select class="form-select-custom" name="status" required>
                <option value="active"   {{ (old('status', $staff->status) === 'active')   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ (old('status', $staff->status) === 'inactive') ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div style="border-top:1px solid var(--border);padding-top:1.25rem;margin-bottom:1.25rem;">
            <div style="font-size:0.78rem;color:var(--muted);margin-bottom:1rem;">
                Leave password fields blank to keep the current password.
            </div>
            <div class="mb-3">
                <label class="form-label-custom">New Password</label>
                <input type="password" class="form-control-custom" name="password"
                       placeholder="Min. 8 characters" minlength="8">
            </div>
            <div class="mb-4">
                <label class="form-label-custom">Confirm New Password</label>
                <input type="password" class="form-control-custom" name="password_confirmation"
                       placeholder="Repeat new password">
            </div>
        </div>

        <div class="d-flex gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</a>
            <button type="submit" class="btn-gold" style="flex:1;justify-content:center;">
                <i class="bi bi-check-lg"></i> Save Changes
            </button>
        </div>
    </form>
</div>

</div>
</div>

@endsection
