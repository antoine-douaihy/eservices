@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#1E3A5F">
                <i class="bi bi-list-task me-2"></i>{{ __('pages.my_service_requests') }}
            </h4>
            <p class="text-muted mb-0 small">{{ __('pages.track_manage_requests') }}</p>
        </div>
        <a href="{{ route('citizen.requests.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>{{ __('pages.new_request') }}
        </a>
    </div>

    {{-- Stats Row --}}
    @php
        $pending    = $requests->where('status', 'pending')->count();
        $inProgress = $requests->where('status', 'in_progress')->count();
        $completed  = $requests->where('status', 'completed')->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-3 fw-bold text-warning" data-stat="pending">{{ $pending }}</div>
                <small class="text-muted">{{ __('pages.pending') }}</small>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-3 fw-bold text-primary" data-stat="in_progress">{{ $inProgress }}</div>
                <small class="text-muted">{{ __('pages.in_progress') }}</small>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-3 fw-bold text-success" data-stat="completed">{{ $completed }}</div>
                <small class="text-muted">{{ __('pages.completed') }}</small>
            </div>
        </div>
    </div>

    {{-- Requests List --}}
    @forelse($requests as $request)
        @php
            $badgeMap = [
                'pending'     => 'warning',
                'in_progress' => 'primary',
                'completed'   => 'success',
            ];
            $labelMap = [
                'pending'     => __('pages.pending'),
                'in_progress' => __('pages.in_progress'),
                'completed'   => __('pages.completed'),
            ];
            $badge = $badgeMap[$request->status] ?? 'secondary';
            $label = $labelMap[$request->status] ?? ucfirst($request->status);
        @endphp
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-{{ $badge }}"
                                  data-status-badge
                                  data-request-id="{{ $request->id }}">{{ $label }}</span>
                            <small class="text-muted">{{ $request->tracking_code }}</small>
                        </div>
                        <h6 class="fw-semibold mb-1">
                            {{ $request->title ?? __('pages.service_request') }}
                        </h6>
                        <p class="text-muted small mb-0">
                            {{ Str::limit($request->description, 120) }}
                        </p>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <small class="text-muted">{{ $request->created_at->format('M d, Y') }}</small>
                        <div class="d-flex gap-2 mt-1">
                            <a href="{{ route('citizen.requests.show', $request->uuid) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots me-1"></i>{{ __('pages.view_and_chat') }}
                            </a>
                            <a href="{{ route('track.show', $request->uuid) }}"
                               class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="bi bi-qr-code me-1"></i>QR
                            </a>
                        </div>
                        @if($request->status === 'completed' && !$request->rating)
                            <a href="{{ route('citizen.requests.show', $request->uuid) }}"
                               class="badge bg-info text-decoration-none small mt-1">
                                <i class="bi bi-star me-1"></i>{{ __('pages.rate_this_service') }}
                            </a>
                        @elseif($request->rating)
                            <span class="text-warning small mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $request->rating->stars ? '-fill' : '' }}"></i>
                                @endfor
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">{{ __('pages.no_requests_yet') }}</h5>
                <p class="text-muted small">{{ __('pages.submit_first_request') }}</p>
                <a href="{{ route('citizen.requests.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('pages.submit_a_request') }}
                </a>
            </div>
        </div>
    @endforelse

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Echo) return;

    const badgeMap = {pending:'warning', in_progress:'primary', completed:'success'};
    const labelMap = {pending:'Pending', in_progress:'In Progress', completed:'Completed'};

    // Subscribe to each request's chat channel (same channel used on the show page)
    document.querySelectorAll('[data-status-badge][data-request-id]').forEach(badge => {
        const requestId = badge.dataset.requestId;

        window.Echo.private(`chat.${requestId}`)
            .listen('.status.updated', (data) => {
                const newClass = badgeMap[data.status] ?? 'secondary';
                const newLabel = labelMap[data.status] ?? data.status;

                badge.className = badge.className.replace(/bg-\w+/, 'bg-' + newClass);
                badge.textContent = newLabel;

                updateStatCounters();
                showToast('A request status changed to: ' + newLabel);
            });
    });

    function updateStatCounters() {
        const counts = {pending: 0, in_progress: 0, completed: 0};
        document.querySelectorAll('[data-status-badge]').forEach(b => {
            const status = Object.keys(badgeMap).find(s => b.classList.contains('bg-' + badgeMap[s]));
            if (status) counts[status]++;
        });
        const el = {
            pending:     document.querySelector('[data-stat="pending"]'),
            in_progress: document.querySelector('[data-stat="in_progress"]'),
            completed:   document.querySelector('[data-stat="completed"]'),
        };
        if (el.pending)     el.pending.textContent     = counts.pending;
        if (el.in_progress) el.in_progress.textContent = counts.in_progress;
        if (el.completed)   el.completed.textContent   = counts.completed;
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 m-3 alert alert-info alert-dismissible fade show';
        toast.style.zIndex = 9999;
        toast.innerHTML = '<i class="bi bi-info-circle me-2"></i>' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
});
</script>
@endpush
