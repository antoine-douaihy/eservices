@extends('layouts.app')

@push('styles')
<style>
    #chat-window { height: 380px; overflow-y: auto; }
    .msg-bubble { max-width: 75%; word-break: break-word; }
    .msg-mine   { background: #DBEAFE; border-radius: 12px 12px 2px 12px; }
    .msg-theirs { background: #F1F5F9; border-radius: 12px 12px 12px 2px; }
    .star-btn { background: none; border: none; font-size: 1.6rem; color: #D1D5DB; cursor: pointer; transition: color .15s; }
    .star-btn:hover, .star-btn.active { color: #F59E0B; }
</style>
@endpush

@section('content')
<div class="container py-4">

    @php
        $isStaff = in_array(auth()->user()->role, ['admin', 'office']);
        $backRoute = $isStaff ? route('office.dashboard') : route('citizen.requests.index');
        $isAr = app()->getLocale() === 'ar';
        $badgeMap = ['pending'=>'warning','in_progress'=>'primary','completed'=>'success'];
        $labelMap = $isAr
            ? ['pending'=>'قيد الانتظار','in_progress'=>'قيد التنفيذ','completed'=>'مكتمل']
            : ['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed'];
        $badge = $badgeMap[$service->status] ?? 'secondary';
        $label = $labelMap[$service->status] ?? ucfirst($service->status);
    @endphp

    {{-- Back + Request Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ $backRoute }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="fw-bold mb-0" style="color:#1E3A5F">
                    {{ $service->title ?? __('pages.service_request') }}
                </h5>
                <span class="badge bg-{{ $badge }}" data-status-badge>{{ $label }}</span>
                @if($isStaff)
                    <span class="badge bg-dark opacity-75">
                        <i class="bi bi-person-badge me-1"></i>{{ $isAr ? 'العرض كـ' : 'Viewing as' }} {{ ucfirst(Auth::user()->role) }}
                    </span>
                @endif
            </div>
            <small class="text-muted">
                {{ $service->tracking_code }} ·
                {{ $isStaff ? ($isAr ? 'المواطن: ' : 'Citizen: ') . $service->user->first_name . ' ' . $service->user->last_name . ' · ' : '' }}
                {{ $isAr ? 'تاريخ الإرسال' : 'Submitted' }} {{ $service->created_at->format('M d, Y') }}
            </small>
        </div>
        <a href="{{ route('track.show', $service->uuid) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
            <i class="bi bi-qr-code me-1"></i>QR
        </a>
    </div>

    <div class="row g-4">

        {{-- Chat Column --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h6 class="fw-bold mb-0" style="color:#1E3A5F">
                        <i class="bi bi-chat-dots me-2"></i>{{ $isAr ? 'الرسائل' : 'Messages' }}
                    </h6>
                    <small class="text-muted">
                        {{ $isStaff ? ($isAr ? 'تحدث مع المواطن بخصوص طلبه' : 'Chat with the citizen about their request') : ($isAr ? 'تحدث مع موظفي الدائرة بخصوص طلبك' : 'Chat with our office staff about your request') }}
                    </small>
                </div>
                <div class="card-body px-4 pb-2">
                    {{-- Message Window --}}
                    <div id="chat-window" class="border rounded bg-white p-3 mb-3">
                        @forelse($service->messages as $msg)
                            @php $isMine = $msg->user_id === Auth::id(); @endphp
                            <div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                                <div class="msg-bubble p-2 px-3 {{ $isMine ? 'msg-mine' : 'msg-theirs' }}">
                                    @if(!$isMine)
                                        <small class="fw-semibold text-primary d-block mb-1">
                                            {{ $msg->user->first_name }}
                                        </small>
                                    @endif
                                    <span>{{ $msg->content }}</span>
                                    <small class="text-muted d-block mt-1" style="font-size:.7rem">
                                        {{ $msg->created_at->format('g:i A') }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4 mb-0">
                                <i class="bi bi-chat d-block fs-3 mb-2"></i>
                                {{ $isAr ? 'لا توجد رسائل بعد. أرسل رسالة للتواصل مع موظفينا.' : 'No messages yet. Send a message to communicate with our staff.' }}
                            </p>
                        @endforelse
                    </div>

                    {{-- Send Form --}}
                    <form id="chat-form" class="d-flex gap-2 pb-3">
                        @csrf
                        <input type="text" id="chat-input"
                               class="form-control"
                               placeholder="{{ $isAr ? 'اكتب رسالتك...' : 'Type your message...' }}"
                               autocomplete="off" required>
                        <button type="submit" class="btn btn-primary px-3">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar: Info + Rating --}}
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">

            {{-- Request Details --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h6 class="fw-bold mb-0" style="color:#1E3A5F">{{ $isAr ? 'تفاصيل الطلب' : 'Request Details' }}</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <p class="text-muted small mb-3">{{ $service->description }}</p>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">{{ __('app.status') }}</span>
                        <span class="badge bg-{{ $badge }}" data-status-badge>{{ $label }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mt-2">
                        <span class="text-muted">{{ __('pages.submitted') }}</span>
                        <span>{{ $service->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mt-2">
                        <span class="text-muted">{{ $isAr ? 'آخر تحديث' : 'Last Update' }}</span>
                        <span>{{ $service->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            {{-- Rating Section (only for completed requests by the owner) --}}
            @if($service->status === 'completed' && Auth::id() === $service->user_id)
                @if($service->rating)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body px-4 py-4 text-center">
                            <i class="bi bi-patch-check-fill text-success fs-3 mb-2 d-block"></i>
                            <h6 class="fw-bold">{{ $isAr ? 'تقييمك' : 'Your Rating' }}</h6>
                            <div class="my-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $service->rating->stars ? '-fill' : '' }} text-warning fs-5"></i>
                                @endfor
                            </div>
                            @if($service->rating->comment)
                                <p class="text-muted small mb-0">"{{ $service->rating->comment }}"</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                            <h6 class="fw-bold mb-0" style="color:#1E3A5F">
                                <i class="bi bi-star me-2"></i>{{ $isAr ? 'قيّم هذه الخدمة' : 'Rate This Service' }}
                            </h6>
                            <small class="text-muted">{{ $isAr ? 'شارك تجربتك' : 'Share your experience' }}</small>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <form method="POST" action="{{ route('citizen.requests.rate', $service->id) }}">
                                @csrf
                                <div class="text-center mb-3">
                                    <div id="star-container" class="d-flex justify-content-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" class="star-btn" data-value="{{ $i }}">
                                                <i class="bi bi-star-fill"></i>
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="stars" id="stars-input" value="0" required>
                                    <small class="text-muted" id="star-label">{{ $isAr ? 'اختر تقييماً' : 'Select a rating' }}</small>
                                </div>
                                <textarea name="comment" class="form-control form-control-sm mb-3"
                                          rows="3" placeholder="{{ $isAr ? 'تعليق اختياري...' : 'Optional comment...' }}"></textarea>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-warning btn-sm fw-semibold">
                                        <i class="bi bi-send me-1"></i>{{ $isAr ? 'إرسال التقييم' : 'Submit Rating' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const requestId = {{ $service->id }};
    const chatWindow  = document.getElementById('chat-window');
    const chatForm    = document.getElementById('chat-form');
    const chatInput   = document.getElementById('chat-input');

    // Scroll to bottom on load
    chatWindow.scrollTop = chatWindow.scrollHeight;

    // Send message via AJAX
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const content = chatInput.value.trim();
        if (!content) return;

        chatInput.disabled = true;

        axios.post(`/chat/${requestId}/send`, { content })
            .then(res => {
                const msg = res.data.message;
                appendMessage(msg, true);
                chatInput.value   = '';
                chatInput.disabled = false;
                chatInput.focus();
            })
            .catch(() => { chatInput.disabled = false; });
    });

    // Receive messages via Pusher
    // DOMContentLoaded fires after all deferred modules (including echo.js) have run,
    // so window.Echo is guaranteed to exist by the time this callback executes.
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.Echo) return;

        window.Echo.private(`chat.${requestId}`)
            .listen('.message.new', (data) => {
                appendMessage(data.message, false);
            })
            .listen('.status.updated', (data) => {
                const badgeMap = {pending:'warning', in_progress:'primary', completed:'success'};
                const labelMap = {pending:'Pending', in_progress:'In Progress', completed:'Completed'};
                const newClass = badgeMap[data.status] ?? 'secondary';
                const newLabel = labelMap[data.status] ?? data.status;

                document.querySelectorAll('[data-status-badge]').forEach(el => {
                    el.className = el.className.replace(/bg-\w+/, 'bg-' + newClass);
                    el.textContent = newLabel;
                });
                showToast('Status updated to: ' + newLabel);
            });
    });

    function appendMessage(msg, isMine) {
        const div = document.createElement('div');
        div.className = `d-flex ${isMine ? 'justify-content-end' : 'justify-content-start'} mb-2`;
        div.innerHTML = `
            <div class="msg-bubble p-2 px-3 ${isMine ? 'msg-mine' : 'msg-theirs'}">
                ${!isMine ? `<small class="fw-semibold text-primary d-block mb-1">${msg.user ? msg.user.first_name : 'Staff'}</small>` : ''}
                <span>${escapeHtml(msg.content)}</span>
                <small class="text-muted d-block mt-1" style="font-size:.7rem">Just now</small>
            </div>`;
        chatWindow.appendChild(div);
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 m-3 alert alert-info alert-dismissible fade show';
        toast.style.zIndex = 9999;
        toast.innerHTML = `<i class="bi bi-info-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    // Star rating UI
    const stars       = document.querySelectorAll('.star-btn');
    const starsInput  = document.getElementById('stars-input');
    const starLabels  = ['','Poor','Fair','Good','Very Good','Excellent'];
    const starLabel   = document.getElementById('star-label');

    if (stars.length) {
        stars.forEach(btn => {
            btn.addEventListener('click', () => {
                const val = parseInt(btn.dataset.value);
                starsInput.value = val;
                if (starLabel) starLabel.textContent = starLabels[val] || '';
                stars.forEach((s, i) => s.classList.toggle('active', i < val));
            });
        });
    }
})();
</script>
@endpush
