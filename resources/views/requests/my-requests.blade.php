@extends('layouts.app')

@section('title', 'My Requests')
@section('page-title', 'My Requests')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:var(--navy);margin:0;">
            {{ __('app.nav_my_requests') }}
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            {{ app()->getLocale() === 'ar' ? 'تابع وأدِر طلبات الخدمة التي قدمتها.' : 'Track and manage your submitted service requests.' }}
        </p>
    </div>
    <a href="{{ route('citizen.services.browse') }}" class="btn-gold">
        <i class="bi bi-plus-lg"></i> {{ __('pages.new_application') }}
    </a>
</div>

@if(session('success'))
    <div class="alert-success-custom mb-4">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif

{{-- Status Stats --}}
@php
    $allRequests = Auth::user()->citizenRequests ?? collect();
    $statCounts = [
        'all'             => $allRequests->count(),
        'pending'         => $allRequests->where('status', 'pending')->count(),
        'pending_payment' => $allRequests->where('status', 'pending_payment')->count(),
        'in_review'       => $allRequests->where('status', 'in_review')->count(),
        'approved'        => $allRequests->where('status', 'approved')->count(),
        'rejected'        => $allRequests->where('status', 'rejected')->count(),
    ];
@endphp
<div class="row g-3 mb-4">
    @foreach([
        ['all',             __('pages.total'),      'rgba(100,116,139,0.15)', 'rgba(100,116,139,0.25)', 'var(--muted)',  'bi-layers-fill'],
        ['pending',         __('pages.pending'),    'rgba(245,158,11,0.15)', 'rgba(245,158,11,0.3)',   '#b45309',      'bi-hourglass-split'],
        ['in_review',       __('pages.in_review'),  'rgba(139,92,246,0.15)', 'rgba(139,92,246,0.3)',   '#6d28d9',      'bi-clock-history'],
        ['approved',        __('pages.approved'),   'rgba(4,120,87,0.15)',   'rgba(4,120,87,0.3)',     '#047857',      'bi-check-circle-fill'],
        ['rejected',        __('pages.declined'),   'rgba(239,68,68,0.12)', 'rgba(239,68,68,0.3)',    '#dc2626',      'bi-x-circle-fill'],
    ] as [$key, $label, $bg, $border, $color, $icon])
    <div class="col-6 col-xl">
        <a href="{{ route('citizen.my-requests', ['status' => $key === 'all' ? null : $key]) }}"
           style="text-decoration:none;display:block;">
            <div style="background:{{ request('status', 'all') === $key ? $bg : '#f8fafc' }};border:1px solid {{ request('status', 'all') === $key ? $border : 'var(--border)' }};border-radius:12px;padding:1rem 1.25rem;transition:all 0.2s;"
                 onmouseover="this.style.borderColor='{{ $border }}'" onmouseout="this.style.borderColor='{{ request('status', 'all') === $key ? $border : 'var(--border)' }}'">
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.4rem;">
                    <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:0.9rem;"></i>
                    <span style="font-size:0.75rem;color:var(--muted);font-weight:500;">{{ $label }}</span>
                </div>
                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.4rem;color:var(--navy);">
                    {{ $statCounts[$key] }}
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- Search + Filter Bar --}}
<div class="app-card mb-4" style="padding:1rem 1.25rem;">
    <form method="GET" action="{{ route('citizen.my-requests') }}" class="d-flex gap-2 flex-wrap align-items-center">
        <div style="position:relative;flex:1;min-width:180px;">
            <i class="bi bi-search" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;"></i>
            <input type="text" name="search" class="form-control-custom" style="padding-left:2.4rem;"
                   placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالخدمة أو الدائرة…' : 'Search by service or office…' }}" value="{{ request('search') }}">
        </div>
        @php $ar = app()->getLocale() === 'ar'; @endphp
        <select name="status" class="form-select-custom" style="width:auto;min-width:150px;" onchange="this.form.submit()">
            <option value="">{{ $ar ? 'جميع الحالات' : 'All Statuses' }}</option>
            <option value="pending"           {{ request('status') === 'pending'           ? 'selected' : '' }}>{{ $ar ? 'قيد المراجعة' : 'Pending Review' }}</option>
            <option value="pending_payment"   {{ request('status') === 'pending_payment'   ? 'selected' : '' }}>{{ $ar ? 'بانتظار الدفع' : 'Awaiting Payment' }}</option>
            <option value="in_review"         {{ request('status') === 'in_review'         ? 'selected' : '' }}>{{ __('pages.in_review') }}</option>
            <option value="missing_documents" {{ request('status') === 'missing_documents' ? 'selected' : '' }}>{{ $ar ? 'مستندات ناقصة' : 'Missing Documents' }}</option>
            <option value="approved"          {{ request('status') === 'approved'          ? 'selected' : '' }}>{{ __('pages.approved') }}</option>
            <option value="rejected"          {{ request('status') === 'rejected'          ? 'selected' : '' }}>{{ __('pages.declined') }}</option>
        </select>
        <button type="submit" class="btn-gold" style="padding:0.6rem 1.1rem;">
            <i class="bi bi-funnel-fill"></i>
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('citizen.my-requests') }}" class="btn-ghost" style="padding:0.6rem 0.9rem;">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </form>
</div>

@if($requests->isEmpty())
    <div class="app-card" style="text-align:center;padding:4rem 2rem;">
        <i class="bi bi-inbox" style="font-size:3rem;color:var(--muted);opacity:0.4;display:block;margin-bottom:1rem;"></i>
        <p style="color:var(--muted);font-size:0.9rem;">
            @if(request('search') || request('status'))
                {{ app()->getLocale() === 'ar' ? 'لا توجد طلبات مطابقة لفلاترك.' : 'No requests match your filters.' }}
            @else
                {{ app()->getLocale() === 'ar' ? 'لم تقدّم أي طلبات بعد.' : "You haven't submitted any requests yet." }}
            @endif
        </p>
        @if(request('search') || request('status'))
            <a href="{{ route('citizen.my-requests') }}" class="btn-ghost mt-3">{{ __('pages.clear_filters') }}</a>
        @else
            <a href="{{ route('citizen.services.browse') }}" class="btn-gold mt-3">{{ __('pages.browse_services') }}</a>
        @endif
    </div>
@else
    <div class="d-flex flex-column gap-3">
        @foreach($requests as $req)
        @php
            $statusStyle = match($req->status) {
                'pending'           => 'background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:#92400e;',
                'pending_payment'   => 'background:rgba(96,165,250,0.15);border:1px solid rgba(96,165,250,0.3);color:#1e40af;',
                'in_review'         => 'background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.3);color:#5b21b6;',
                'missing_documents' => 'background:rgba(249,115,22,0.15);border:1px solid rgba(249,115,22,0.3);color:#9a3412;',
                'approved'          => 'background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);color:#065f46;',
                'rejected'          => 'background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#991b1b;',
                default             => 'background:rgba(100,116,139,0.15);border:1px solid rgba(100,116,139,0.25);color:var(--muted);',
            };
            $statusLabel = app()->getLocale() === 'ar' ? match($req->status) {
                'pending'           => 'قيد المراجعة',
                'pending_payment'   => 'بانتظار الدفع',
                'in_review'         => 'قيد المراجعة',
                'missing_documents' => 'مستندات ناقصة',
                'approved'          => 'مقبول',
                'rejected'          => 'مرفوض',
                default             => ucfirst(str_replace('_', ' ', $req->status)),
            } : match($req->status) {
                'pending'           => 'Pending Review',
                'pending_payment'   => 'Awaiting Payment',
                'in_review'         => 'In Review',
                'missing_documents' => 'Missing Documents',
                'approved'          => 'Approved',
                'rejected'          => 'Declined',
                default             => ucfirst(str_replace('_', ' ', $req->status)),
            };
        @endphp

        <div class="app-card" style="padding:1.25rem 1.5rem;">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div style="flex:1;">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <span style="font-family:'Syne',sans-serif;font-weight:700;color:var(--navy);font-size:1rem;">
                            {{ $req->service->display_name ?? '—' }}
                        </span>
                        <span style="{{ $statusStyle }}font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:1.25rem;font-size:0.78rem;color:var(--muted);margin-top:0.4rem;">
                        @if($req->office)
                            <span><i class="bi bi-building me-1"></i>{{ $req->office->name }}{{ $req->office->city ? ', '.$req->office->city : '' }}</span>
                        @endif
                        <span><i class="bi bi-calendar3 me-1"></i>{{ $req->created_at->format('d M Y') }}</span>
                        <span><i class="bi bi-hash me-1"></i>{{ app()->getLocale() === 'ar' ? 'مرجع' : 'Ref' }} #{{ $req->id }}</span>
                    </div>

                    @if($req->notes)
                        <p style="margin-top:0.6rem;font-size:0.82rem;color:var(--muted);font-style:italic;">
                            "{{ Str::limit($req->notes, 120) }}"
                        </p>
                    @endif
                </div>

                <div style="text-align:right;flex-shrink:0;display:flex;flex-direction:column;gap:0.5rem;align-items:flex-end;">
                    {{-- QR tracking button --}}
                    @if($req->uuid)
                    <button onclick="showQR('{{ $req->uuid }}', '{{ addslashes($req->service->name ?? 'Request') }}')"
                            class="btn-ghost" style="padding:0.3rem 0.75rem;font-size:0.78rem;">
                        <i class="bi bi-qr-code me-1"></i> {{ app()->getLocale() === 'ar' ? 'تتبع QR' : 'Track QR' }}
                    </button>
                    @endif
                    {{-- Chat button --}}
                    <a href="{{ route('citizen.requests.chat', $req) }}"
                       class="btn-ghost" style="padding:0.3rem 0.75rem;font-size:0.78rem;text-decoration:none;">
                        <i class="bi bi-chat-dots me-1"></i> {{ app()->getLocale() === 'ar' ? 'محادثة' : 'Chat' }}
                    </a>
                    @if($req->status === 'approved' && $req->certificate_path)
                        <a href="{{ route('requests.certificate', $req) }}" class="btn-emerald"
                           style="padding:0.4rem 0.9rem;font-size:0.8rem;text-decoration:none;">
                            <i class="bi bi-download"></i> {{ __('pages.certificate') }}
                        </a>
                        {{-- Rating --}}
                        @if(!$req->rating)
                        <button onclick="document.getElementById('rating-modal-{{ $req->id }}').style.display='flex'"
                                class="btn-ghost" style="padding:0.3rem 0.75rem;font-size:0.78rem;">
                            <i class="bi bi-star-fill" style="color:var(--gold);"></i> {{ app()->getLocale() === 'ar' ? 'تقييم الخدمة' : 'Rate Service' }}
                        </button>
                        @else
                        <span style="font-size:0.78rem;color:var(--muted);">
                            @for($i=1;$i<=5;$i++)
                                <i class="bi bi-star{{ $i <= $req->rating->stars ? '-fill' : '' }}" style="color:{{ $i <= $req->rating->stars ? 'var(--gold)' : 'var(--muted)' }};font-size:0.7rem;"></i>
                            @endfor
                        </span>
                        @endif

                    @elseif($req->status === 'approved')
                        <span style="font-size:0.8rem;color:#047857;font-style:italic;">
                            <i class="bi bi-hourglass-split me-1"></i>{{ app()->getLocale() === 'ar' ? 'الشهادة قيد التحضير' : 'Certificate being prepared' }}
                        </span>

                    @elseif($req->status === 'pending_payment')
                        <a href="{{ route('citizen.payment.select', $req) }}" class="btn-gold"
                           style="padding:0.4rem 0.9rem;font-size:0.8rem;text-decoration:none;">
                            <i class="bi bi-credit-card-fill"></i> {{ app()->getLocale() === 'ar' ? 'إتمام الدفع' : 'Complete Payment' }}
                        </a>

                    @elseif($req->status === 'in_review')
                        <span style="font-size:0.78rem;color:var(--muted);font-style:italic;">
                            <i class="bi bi-clock-history me-1"></i>{{ app()->getLocale() === 'ar' ? 'قيد المراجعة' : 'Under review' }}
                        </span>

                    @elseif($req->status === 'missing_documents')
                        <div style="font-size:0.8rem;color:#9a3412;font-weight:600;margin-bottom:.25rem;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ app()->getLocale() === 'ar' ? 'إجراء مطلوب' : 'Action Required' }}
                        </div>
                        <span style="font-size:0.78rem;color:var(--muted);">
                            {{ app()->getLocale() === 'ar' ? 'يرجى تحميل المستندات المطلوبة وإعادة الإرسال.' : 'Please upload the requested documents and resubmit.' }}
                        </span>
                        <a href="{{ route('citizen.requests.resubmit', $req) }}"
                           class="btn-gold" style="padding:0.4rem 0.9rem;font-size:0.8rem;text-decoration:none;margin-top:.25rem;">
                            <i class="bi bi-cloud-upload me-1"></i> {{ app()->getLocale() === 'ar' ? 'تحميل المستندات' : 'Upload Docs' }}
                        </a>

                    @elseif($req->status === 'rejected')
                        <a href="{{ route('citizen.requests.resubmit', $req) }}"
                           class="btn-gold" style="padding:0.4rem 0.9rem;font-size:0.8rem;text-decoration:none;">
                            <i class="bi bi-arrow-repeat"></i> {{ app()->getLocale() === 'ar' ? 'إعادة الإرسال' : 'Resubmit' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Rating modal --}}
        @if($req->status === 'approved' && $req->certificate_path && !$req->rating)
        <div id="rating-modal-{{ $req->id }}"
             style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
            <div style="background:#ffffff;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:440px;width:90%;margin:auto;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:var(--navy);margin:0;">
                        {{ app()->getLocale() === 'ar' ? 'تقييم: ' : 'Rate: ' }}{{ $req->service->display_name ?? __('pages.service_label') }}
                    </h5>
                    <button onclick="document.getElementById('rating-modal-{{ $req->id }}').style.display='none'"
                            style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.2rem;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('citizen.requests.rate', $req) }}">
                    @csrf
                    <div style="text-align:center;margin-bottom:1.5rem;">
                        <div style="font-size:0.85rem;color:var(--muted);margin-bottom:0.875rem;">{{ app()->getLocale() === 'ar' ? 'كيف تقيّم هذه الخدمة؟' : 'How would you rate this service?' }}</div>
                        <div class="star-rating" id="stars-{{ $req->id }}" style="display:flex;justify-content:center;gap:0.5rem;">
                            @for($i=1;$i<=5;$i++)
                            <label style="cursor:pointer;font-size:2rem;color:var(--muted);transition:color 0.15s;"
                                   onmouseover="hoverStars({{ $req->id }},{{ $i }})"
                                   onmouseout="resetStars({{ $req->id }})">
                                <input type="radio" name="stars" value="{{ $i }}" style="display:none;"
                                       onclick="selectStars({{ $req->id }},{{ $i }})">
                                <i class="bi bi-star-fill"></i>
                            </label>
                            @endfor
                        </div>
                        <input type="hidden" name="stars" id="stars-val-{{ $req->id }}" value="">
                    </div>
                    <div class="mb-4">
                        <label class="form-label-custom">{{ app()->getLocale() === 'ar' ? 'تعليقات (اختياري)' : 'Comments (optional)' }}</label>
                        <textarea name="comment" rows="3" class="form-control-custom"
                                  placeholder="{{ app()->getLocale() === 'ar' ? 'شارك تجربتك…' : 'Share your experience…' }}"></textarea>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="button"
                                onclick="document.getElementById('rating-modal-{{ $req->id }}').style.display='none'"
                                class="btn-ghost" style="flex:1;justify-content:center;">{{ __('app.cancel') }}</button>
                        <button type="submit" class="btn-gold" style="flex:1;justify-content:center;">
                            <i class="bi bi-star-fill"></i> {{ app()->getLocale() === 'ar' ? 'إرسال التقييم' : 'Submit Rating' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @endforeach
    </div>
@endif

{{-- QR Code Tracking Modal --}}
<div id="qr-modal"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;"
     onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#ffffff;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:360px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 id="qr-title" style="font-family:'Syne',sans-serif;font-weight:700;color:var(--navy);margin:0;font-size:.95rem;">
                {{ app()->getLocale() === 'ar' ? 'تتبع الطلب' : 'Track Request' }}
            </h6>
            <button onclick="document.getElementById('qr-modal').style.display='none'"
                    style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.2rem;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="qr-canvas" style="display:flex;justify-content:center;margin-bottom:1rem;"></div>
        <p style="font-size:.78rem;color:var(--muted);margin:.5rem 0">
            {{ app()->getLocale() === 'ar' ? 'اسحب رمز QR هذا لتتبع حالة طلبك دون الحاجة لتسجيل الدخول.' : 'Scan this QR code to track your request status offline — no login required.' }}
        </p>
        <a id="qr-link" href="#" target="_blank"
           style="font-size:.78rem;color:var(--gold);word-break:break-all;text-decoration:none;">
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
function hoverStars(id, n) {
    document.querySelectorAll('#stars-' + id + ' label').forEach((l, i) => {
        l.style.color = i < n ? 'var(--gold)' : 'var(--muted)';
    });
}
function resetStars(id) {
    const val = parseInt(document.getElementById('stars-val-' + id).value || 0);
    document.querySelectorAll('#stars-' + id + ' label').forEach((l, i) => {
        l.style.color = i < val ? 'var(--gold)' : 'var(--muted)';
    });
}
function selectStars(id, n) {
    document.getElementById('stars-val-' + id).value = n;
    document.querySelectorAll('#stars-' + id + ' input[type=radio]').forEach((r, i) => {
        r.checked = (i + 1) === n;
    });
}
document.querySelectorAll('[id^="rating-modal-"]').forEach(modal => {
    modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
});

function showQR(uuid, serviceName) {
    const url  = '{{ url('/track') }}/' + uuid;
    const modal = document.getElementById('qr-modal');
    const canvas = document.getElementById('qr-canvas');
    document.getElementById('qr-title').textContent = serviceName;
    document.getElementById('qr-link').textContent = url;
    document.getElementById('qr-link').href = url;
    canvas.innerHTML = '';
    QRCode.toCanvas(document.createElement('canvas'), url, {
        width: 200,
        color: { dark: '#1e3a5f', light: '#ffffff' }
    }, function(err, c) {
        if (!err) {
            c.style.borderRadius =