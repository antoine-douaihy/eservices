@extends('admin.layouts.app')

@section('title', 'Add Staff Member')
@section('page-title', 'Add Staff Member')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            Add Staff Member
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            Create a new office staff account
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
    <form method="POST" action="{{ route('admin.staff.store') }}">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">First Name <span style="color:#f87171;">*</span></label>
                <input type="text" class="form-control-custom" name="first_name"
                       value="{{ old('first_name') }}" required autofocus placeholder="e.g. Maria">
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Last Name <span style="color:#f87171;">*</span></label>
                <input type="text" class="form-control-custom" name="last_name"
                       value="{{ old('last_name') }}" required placeholder="e.g. Santos">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Email Address <span style="color:#f87171;">*</span></label>
            <input type="email" class="form-control-custom" name="email"
                   value="{{ old('email') }}" required placeholder="staff@municipality.gov">
        </div>

        <div class="mb-4" style="background:rgba(214,158,46,0.08);border:1px solid rgba(214,158,46,0.25);border-radius:10px;padding:0.875rem 1.1rem;">
            <div style="font-size:0.82rem;color:#fcd34d;display:flex;gap:0.6rem;align-items:flex-start;">
                <i class="bi bi-envelope-check-fill flex-shrink-0" style="margin-top:1px;"></i>
                <div>No password needed here — a random temporary password will be generated automatically and emailed directly to the staff member. They'll be required to set their own permanent password the first time they log in.</div>
            </div>
        </div>

        <div style="background:rgba(214,158,46,0.06);border:1px solid rgba(214,158,46,0.2);border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;">
            <label class="form-label-custom" style="color:var(--gold);">
                <i class="bi bi-building-fill me-1"></i> Assign to Office <span style="color:#f87171;">*</span>
            </label>
            <select name="office_id" class="form-select-custom" required>
                <option value="" disabled selected>Select an office…</option>
                @if(isset($offices))
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="d-flex gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</a>
            <button type="submit" class="btn-gold" style="flex:1;justify-content:center;">
                <i class="bi bi-person-check-fill"></i> Create Staff Account
            </button>
        </div>
    </form>
</div>

</div>
</di