@extends('layouts.app')

@section('title', 'Home')
@section('page-title', 'Home')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.9rem;color:var(--navy);margin:0;">
            {{ str_replace(':name', Auth::user()->first_name, __('pages.welcome_back_name')) }}
        </h1>
        <p style="color:var(--muted);font-size:1.05rem;margin-top:6px;">
            {{ __('pages.what_to_do_today') }}
        </p>
    </div>
    <a href="{{ route('citizen.services.browse') }}" class="btn-gold">
        <i class="bi bi-plus-lg"></i> {{ __('pages.new_application') }}
    </a>
</div>

{{-- Status Stats --}}
@php
    $statCounts = [
        'total'           => $requests->count(),
        'pending'         => $requests->whereIn('status', ['pending', 'pending_payment'])->count(),
        'in_review'       => $requests->where('status', 'in_review')->count(),
        'approved'        => $requests->where('status', 'approved')->count(),
        'rejected'        => $requests->where('status', 'rejected')->count(),
    ];
@endphp
<div class="row g-3 mb-4">
    @foreach([
        ['total',     __('pages.total'),      'rgba(100,116,139,0.15)', 'rgba(100,116,139,0.25)', 'var(--muted)', 'bi-layers-fill'],
        ['pending',   __('pages.pending'),    'rgba(245,158,11,0.15)',  'rgba(245,158,11,0.3)',   '#b45309',     'bi-hourglass-split'],
        ['in_review', __('pages.in_review'),  'rgba(139,92,246,0.15)',  'rgba(139,92,246,0.3)',   '#6d28d9',     'bi-clock-history'],
        ['approved',  __('pages.approved'),   'rgba(4,120,87,0.15)',    'rgba(4,120,87,0.3)',     '#047857',     'bi-check-circle-fill'],
        ['rejected',  __('pages.declined'),   'rgba(239,68,68,0.12)',   'rgba(239,68,68,0.3)',    '#dc2626',     'bi-x-circle-fill'],
    ] as [$key, $label, $bg, $border, $color, $icon])
    <div class="col-6 col-xl">
        <a href="{{ route('citizen.my-requests', $key !== 'total' ? ['status' => $key] : []) }}"
           style="text-decoration:none;display:block;">
            <div style="background:{{ $bg }};border:1px solid {{ $border }};border-radius:12px;padding:1rem 1.25rem;transition:all 0.2s;"
                 onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                    <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:1.05rem;"></i>
                    <span style="font-size:0.9rem;color:var(--muted);font-weight:500;">{{ $label }}</span>
                </div>
                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.8rem;color:var(--navy);">
                    {{ $statCounts[$key] }}
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- Quick Apply --}}
<div class="app-card mb-4">
    <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:1.25rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
        <i class="bi bi-send-fill me-2" style="color:var(--gold);"></i> {{ __('pages.quick_application') }}
    </div>

    @if($services->isEmpty())
        <p style="color:var(--muted);font-size:1rem;">{{ __('pages.no_services_at_moment') }}</p>
    @else
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label-custom">{{ __('pages.select_service') }}</label>
                <select id="quick-service-select" class="form-select-custom">
                    <option value="" disabled selected>{{ __('pages.choose_service') }}</option>
                    @foreach($services as $service)
                        <option value="{{ route('citizen.services.apply', $service) }}">
                            {{ $service->display_name }}
                            @if($service->price > 0)
                                — @if($service->currency === 'LBP')ل.ل {{ number_format($service->price, 0) }}@else{{ $service->currency }} {{ number_format($service->price, 2) }}@endif
                            @else
                                — {{ __('pages.free') }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button id="quick-apply-btn" onclick="quickApply()" class="btn-gold w-100" style="justify-content:center;">
                    <i class="bi bi-send-fill"></i> {{ __('pages.apply_now') }}
                </button>
            </div>
        </div>
    @endif
</div>

{{-- My Requests Table --}}
<div class="app-card" style="padding:0;overflow:hidden;">
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.1rem;color:var(--navy);">{{ __('app.nav_my_requests') }}</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="app-table">
            <thead>
                <tr>
                    <th>{{ __('pages.col_number') }}</th>
                    <th>{{ __('pages.col_service') }}</th>
                    <th>{{ __('pages.col_date_applied') }}</th>
                    <th>{{ __('pages.col_status_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td style="color:var(--muted);font-size:0.92rem;">#{{ $request->id }}</td>
                    <td style="font-weight:600;color:var(--text);">{{ $request->service->display_name ?? '—' }}</td>
                    <td style="color:var(--muted);font-size:0.92rem;">{{ $request->created_at->format('d M Y') }}</td>
                    <td>
                        @if($request->status === 'pending')
                            <span style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:#92400e;font-size:0.88rem;padding:0.3rem 0.85rem;border-radius:20px;font-weight:600;">
                                {{ __('pages.pending_review') }}
                            </span>

                        @elseif($request->status === 'pending_payment')
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span style="background:rgba(96,165,250,0.15);border:1px solid rgba(96,165,250,0.3);color:#1e40af;font-size:0.88rem;padding:0.3rem 0.85rem;border-radius:20px;font-weight:600;">
                                    {{ __('pages.awaiting_payment') }}
                                </span>
                                <a href="{{ route('citizen.payment.select', $request) }}"
                                   class="btn-gold" style="padding:0.5rem 1.1rem;font-size:0.92rem;text-decoration:none;">
                                    <i class="bi bi-credit-card-fill"></i> {{ __('pages.pay_now') }}
                                </a>
                            </div>

                        @elseif($request->status === 'in_review')
                            <span style="background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.3);color:#5b21b6;font-size:0.88rem;padding:0.3rem 0.85rem;border-radius:20px;font-weight:600;">
                                {{ __('pages.in_review') }}
                            </span>

                        @elseif($request->status === 'approved')
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);color:#065f46;font-size:0.88rem;padding:0.3rem 0.85rem;border-radius:20px;font-weight:600;">
                                    {{ __('pages.approved') }}
                                </span>
                                @if($request->certificate_path)
                                    <a href="{{ route('requests.certificate', $request) }}"
                                       class="btn-gold" style="padding:0.5rem 1.1rem;font-size:0.92rem;text-decoration:none;">
                                        <i class="bi bi-download"></i> {{ __('pages.certificate') }}
                                    </a>
                                @endif
                            </div>

                        @elseif($request->status === 'rejected')
                            <span style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#991b1b;font-size:0.88rem;padding:0.3rem 0.85rem;border-radius:20px;font-weight:600;">
                                {{ __('pages.declined') }}
                            </span>

                        @else
                            <span style="background:rgba(100,116,139,0.15);border:1px solid rgba(100,116,139,0.25);color:var(--muted);font-size:0.88rem;padding:0.3rem 0.85rem;border-radius:20px;font-weight:600;">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:3rem;color:var(--muted);">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
                        {{ __('pages.no_applications_yet') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function quickApply() {
    const select = document.getElementById('quick-service-select');
    const url = select.value;
    if (!url) {
        select.style.borderColor = 'rgba(239,68,68,0.6)';
        setTimeout(() => select.style.borderColor = '', 2000);
        return;
    }
    window.location.href = url;
}
</script>
@endpush