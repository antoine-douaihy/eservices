@extends('admin.layouts.app')

@section('title', 'Citizen Requests')
@section('page-title', 'Citizen Requests')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            {{ app()->getLocale() === 'ar' ? 'طلبات المواطنين' : 'Citizen Requests' }}
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            @if(Auth::user()->role === 'office' && Auth::user()->office)
                {{ Auth::user()->office->name }} — {{ app()->getLocale() === 'ar' ? 'الطلبات الواردة' : 'incoming service requests' }}
            @else
                {{ app()->getLocale() === 'ar' ? 'جميع طلبات خدمة المواطنين' : 'All citizen service requests' }}
            @endif
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('office.ratings.index') }}" class="btn-ghost">
            <i class="bi bi-star-fill" style="color:var(--gold);"></i> Ratings
        </a>
        <a href="{{ route('office.appointments.index') }}" class="btn-ghost">
            <i class="bi bi-calendar-check"></i> Appointments
        </a>
        <span style="background:rgba(214,158,46,0.12);border:1px solid rgba(214,158,46,0.2);color:var(--gold);font-size:0.8rem;padding:0.3rem 0.875rem;border-radius:20px;font-weight:600;">
            {{ Auth::user()->role === 'admin' ? 'Admin' : 'Office Staff' }}
        </span>
    </div>
</div>

@if(session('success'))
    <div style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.35);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#6ee7b7;display:flex;align-items:center;gap:0.75rem;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#f87171;display:flex;align-items:center;gap:0.75rem;">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach([
        ['pending',   $stats['pending'],   'rgba(245,158,11,0.15)', 'rgba(245,158,11,0.25)', '#fcd34d', 'bi-hourglass-split', 'Pending'],
        ['in_review', $stats['in_review'], 'rgba(139,92,246,0.15)', 'rgba(139,92,246,0.25)', '#c4b5fd', 'bi-clock-history',   'In Review'],
        ['approved',  $stats['approved'],  'rgba(4,120,87,0.15)',   'rgba(4,120,87,0.3)',    '#6ee7b7', 'bi-check2-circle',   'Approved'],
    ] as [$key, $count, $bg, $border, $color, $icon, $label])
    <div class="col-sm-4">
        <div class="admin-card d-flex align-items-center gap-3" style="background:{{ $bg }};border:1px solid {{ $border }};">
            <div style="width:44px;height:44px;background:rgba(0,0,0,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">{{ $count }}</div>
                <div style="font-size:0.78rem;color:var(--muted);">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filter --}}
<div class="admin-card mb-4" style="padding:1rem 1.25rem;">
    <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
        <div style="position:relative;flex:1;min-width:180px;">
            <i class="bi bi-search" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;"></i>
            <input type="text" name="search" class="form-control-custom" style="padding-left:2.4rem;"
                   placeholder="Search name, email, service…" value="{{ request('search') }}">
        </div>
        <select name="status" class="form-select-custom" style="width:auto;min-width:160px;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="pending"            {{ request('status') === 'pending'            ? 'selected' : '' }}>Pending</option>
            <option value="pending_payment"    {{ request('status') === 'pending_payment'    ? 'selected' : '' }}>Awaiting Payment</option>
            <option value="in_review"          {{ request('status') === 'in_review'          ? 'selected' : '' }}>In Review</option>
            <option value="missing_documents"  {{ request('status') === 'missing_documents'  ? 'selected' : '' }}>Missing Documents</option>
            <option value="approved"           {{ request('status') === 'approved'           ? 'selected' : '' }}>Approved</option>
            <option value="rejected"           {{ request('status') === 'rejected'           ? 'selected' : '' }}>Rejected</option>
        </select>
        <button type="submit" class="btn-gold" style="padding:0.6rem 1.1rem;">
            <i class="bi bi-funnel-fill"></i>
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('office.dashboard') }}" class="btn-ghost" style="padding:0.6rem 0.9rem;"><i class="bi bi-x-lg"></i></a>
        @endif
        <span style="font-size:0.8rem;color:var(--muted);margin-left:auto;">{{ $requests->count() }} request(s)</span>
    </form>
</div>

{{-- Reject Modal --}}
<div id="reject-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#162947;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:440px;width:90%;">
        <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin:0 0 1.25rem;">Reject Request</h5>
        <form id="reject-form" method="POST" action="">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="rejected">
            <div class="mb-4">
                <label class="form-label-custom">Reason (shown to citizen)</label>
                <textarea name="note" rows="3" class="form-control-custom" placeholder="Explain why this request was rejected…"></textarea>
            </div>
            <div class="d-flex gap-3">
                <button type="button" onclick="closeModal('reject-modal')" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                <button type="submit" class="btn-danger-soft" style="flex:1;justify-content:center;"><i class="bi bi-x-lg"></i> Confirm</button>
            </div>
        </form>
    </div>
</div>

{{-- Upload Response Modal --}}
<div id="upload-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#162947;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:460px;width:90%;">
        <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin:0 0 1.25rem;"><i class="bi bi-upload me-2" style="color:var(--gold);"></i>Upload Response Document</h5>
        <form id="upload-form" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label-custom">Document <span style="color:#f87171;">*</span></label>
                <input type="file" name="response_document" accept=".jpg,.jpeg,.png,.pdf" class="form-control-custom" required>
                <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">PDF, JPG or PNG — max 5MB</div>
            </div>
            <div class="mb-4">
                <label class="form-label-custom">Note to citizen (optional)</label>
                <textarea name="response_note" rows="2" class="form-control-custom" placeholder="Explain what this document contains…"></textarea>
            </div>
            <div class="d-flex gap-3">
                <button type="button" onclick="closeModal('upload-modal')" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                <button type="submit" class="btn-gold" style="flex:1;justify-content:center;"><i class="bi bi-upload"></i> Upload</button>
            </div>
        </form>
    </div>
</div>

{{-- Appointment Modal --}}
<div id="appt-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#162947;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:480px;width:90%;">
        <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin:0 0 1.25rem;"><i class="bi bi-calendar-plus me-2" style="color:var(--gold);"></i>Schedule Appointment</h5>
        <form method="POST" action="{{ route('office.appointments.store') }}">
            @csrf
            <input type="hidden" name="citizen_request_id" id="appt-request-id">
            <input type="hidden" name="user_id" id="appt-user-id">
            <div class="mb-3">
                <label class="form-label-custom">Title <span style="color:#f87171;">*</span></label>
                <input type="text" name="title" class="form-control-custom" placeholder="e.g. Document Verification Meeting" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-7">
                    <label class="form-label-custom">Date & Time <span style="color:#f87171;">*</span></label>
                    <input type="datetime-local" name="scheduled_at" class="form-control-custom" style="color-scheme:dark;" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label-custom">Duration (min)</label>
                    <input type="number" name="duration_minutes" class="form-control-custom" value="30" min="15" max="480">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label-custom">Notes</label>
                <textarea name="notes" rows="2" class="form-control-custom" placeholder="Instructions for the citizen…"></textarea>
            </div>
            <div class="d-flex gap-3">
                <button type="button" onclick="closeModal('appt-modal')" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                <button type="submit" class="btn-gold" style="flex:1;justify-content:center;"><i class="bi bi-calendar-check"></i> Schedule</button>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="admin-card" style="padding:0;overflow:hidden;">
    @if($requests->isEmpty())
        <div style="text-align:center;padding:4rem 2rem;">
            <i class="bi bi-inbox" style="font-size:3rem;color:var(--muted);opacity:0.4;display:block;margin-bottom:1rem;"></i>
            <p style="color:var(--muted);margin:0;">No requests found for this office.</p>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Citizen</th>
                        <th>Service</th>
                        <th>Submitted</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Documents</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                    @php
                        $localPayment  = $req->localPayments->where('status', 'confirmed')->first();
                        $cryptoPayment = $req->cryptoTransactions->where('status', 'confirmed')->first();
                        $statusStyle   = match($req->status) {
                            'pending'            => 'background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:#fcd34d;',
                            'pending_payment'    => 'background:rgba(96,165,250,0.15);border:1px solid rgba(96,165,250,0.3);color:#93c5fd;',
                            'in_review'          => 'background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.3);color:#c4b5fd;',
                            'missing_documents'  => 'background:rgba(249,115,22,0.15);border:1px solid rgba(249,115,22,0.3);color:#fdba74;',
                            'approved'           => 'background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);color:#6ee7b7;',
                            'rejected'           => 'background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#f87171;',
                            default              => 'background:rgba(100,116,139,0.15);border:1px solid rgba(100,116,139,0.25);color:var(--muted);',
                        };
                        $statusLabel   = match($req->status) {
                            'pending'            => 'Pending',
                            'pending_payment'    => 'Awaiting Payment',
                            'in_review'          => 'In Review',
                            'missing_documents'  => 'Missing Documents',
                            'approved'           => 'Approved',
                            'rejected'           => 'Rejected',
                            default              => ucfirst(str_replace('_',' ',$req->status)),
                        };
                        $isActionable  = in_array($req->status, ['pending','pending_payment','in_review','missing_documents']);
                    @endphp
                    <tr>
                        <td style="color:var(--muted);font-size:0.8rem;">#{{ $req->id }}</td>
                        <td>
                            <div style="font-weight:600;color:#fff;font-size:0.875rem;">{{ $req->user?->first_name ?? '—' }} {{ $req->user?->last_name ?? '' }}</div>
                            <div style="font-size:0.75rem;color:var(--muted);">{{ $req->user?->email ?? '—' }}</div>
                        </td>
                        <td style="font-weight:500;color:var(--text);font-size:0.875rem;">{{ $req->service?->name ?? '—' }}</td>
                        <td style="color:var(--muted);font-size:0.8rem;">
                            {{ $req->created_at?->format('d M Y') ?? '—' }}<br>
                            <span style="font-size:0.75rem;">{{ $req->created_at?->format('H:i') ?? '' }}</span>
                        </td>
                        <td>
                            @if($req->payment_status === 'paid')
                                @if($localPayment)
                                    <span style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);color:#6ee7b7;font-size:0.72rem;padding:0.2rem 0.55rem;border-radius:20px;font-weight:700;">{{ strtoupper($localPayment->method) }}</span>
                                    <div style="font-size:0.73rem;color:var(--muted);margin-top:2px;">${{ number_format($localPayment->amount_usd, 2) }}</div>
                                @elseif($cryptoPayment)
                                    <span style="background:rgba(247,147,26,0.15);border:1px solid rgba(247,147,26,0.3);color:#fcd34d;font-size:0.72rem;padding:0.2rem 0.55rem;border-radius:20px;font-weight:700;">{{ $cryptoPayment->currency }}</span>
                                @else
                                    <span style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);color:#6ee7b7;font-size:0.72rem;padding:0.2rem 0.55rem;border-radius:20px;font-weight:700;">Paid</span>
                                @endif
                            @else
                                <span style="color:var(--muted);font-size:0.8rem;">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <span style="{{ $statusStyle }}font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            {{-- Citizen uploaded docs --}}
                            @if($req->uploaded_document)
                                @php $docs = json_decode($req->uploaded_document, true) ?? []; @endphp
                                @foreach($docs as $i => $path)
                                    <a href="{{ route('requests.document', [$req, $i]) }}" target="_blank" class="btn-ghost"
                                       style="padding:0.2rem 0.5rem;font-size:0.75rem;display:inline-flex;margin-bottom:2px;">
                                        <i class="bi bi-paperclip"></i> Doc {{ $i + 1 }}
                                    </a>
                                @endforeach
                            @endif
                            {{-- Office response doc --}}
                            @if($req->response_document)
                                <a href="{{ Storage::disk('public')->url($req->response_document) }}" target="_blank"
                                   style="color:#6ee7b7;font-size:0.75rem;display:block;margin-top:2px;">
                                    <i class="bi bi-file-earmark-check"></i> Response
                                </a>
                            @endif
                            @if(!$req->uploaded_document && !$req->response_document)
                                <span style="color:var(--muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end flex-wrap">
                                {{-- Chat --}}
                                <a href="{{ route('office.requests.chat', $req) }}" class="btn-ghost"
                                   style="padding:0.3rem 0.6rem;font-size:0.78rem;" title="Chat with citizen">
                                    <i class="bi bi-chat-text"></i>
                                    @php $msgCount = $req->messages_count ?? ($req->relationLoaded('messages') ? $req->messages->count() : 0); @endphp
                                    @if($msgCount)
                                        <span style="background:rgba(214,158,46,0.3);border-radius:20px;padding:0 5px;font-size:0.68rem;color:var(--gold);">
                                            {{ $msgCount }}
                                        </span>
                                    @endif
                                </a>

                                {{-- Status updates --}}
                                @if($req->status === 'pending' || $req->status === 'pending_payment')
                                    <form method="POST" action="{{ route('office.requests.status', $req) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="in_review">
                                        <button type="submit" class="btn-ghost" style="padding:0.3rem 0.65rem;font-size:0.78rem;" title="Mark In Review">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($isActionable)
                                    <form method="POST" action="{{ route('office.requests.status', $req) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="missing_documents">
                                        <input type="hidden" name="note" value="Please upload the required documents.">
                                        <button type="submit" class="btn-ghost" style="padding:0.3rem 0.65rem;font-size:0.78rem;color:#fdba74;" title="Request Missing Documents"
                                                onclick="return confirm('Mark as Missing Documents?')">
                                            <i class="bi bi-folder-x"></i>
                                        </button>
                                    </form>

                                    <button type="button" class="btn-danger-soft" style="padding:0.3rem 0.65rem;font-size:0.78rem;"
                                            onclick="openRejectModal({{ $req->id }})" title="Reject">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @endif

                                {{-- Upload response document --}}
                                <button type="button" class="btn-ghost" style="padding:0.3rem 0.65rem;font-size:0.78rem;color:var(--gold);"
                                        onclick="openUploadModal({{ $req->id }})" title="Upload response document">
                                    <i class="bi bi-upload"></i>
                                </button>

                                {{-- Schedule appointment --}}
                                <button type="button" class="btn-ghost" style="padding:0.3rem 0.65rem;font-size:0.78rem;"
                                        onclick="openApptModal({{ $req->id }}, {{ $req->user_id }})" title="Schedule appointment">
                                    <i class="bi bi-calendar-plus"></i>
                                </button>

                                {{-- Approve (office staff + admin) --}}
                                @if($isActionable && ($req->payment_status === 'paid' || $req->service?->price == 0))
                                    <form method="POST" action="{{ route('office.requests.status', $req) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn-emerald" style="padding:0.3rem 0.65rem;font-size:0.78rem;"
                                                onclick="return confirm('Approve this request and generate certificate?')" title="Approve">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function openRejectModal(id) {
    document.getElementById('reject-form').action = '/office/requests/' + id + '/status';
    document.getElementById('reject-modal').style.display = 'flex';
}
function openUploadModal(id) {
    document.getElementById('upload-form').action = '/office/requests/' + id + '/upload-response';
    document.getElementById('upload-modal').style.display = 'flex';
}
function openApptModal(requestId, userId) {
    document.getElementById('appt-request-id').value = requestId;
    document.getElementById('appt-user-id').value = userId;
    document.getElementById('appt-modal').style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
['reject-modal','upload-modal','appt-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', e => {
        if (e.target === document.getElementById(id)) closeModal(id);
    });
});

// Live notification: poll for new pending requests every 30s
@if(Auth::user()->role === 'office' || Auth::user()->role === 'admin')
let knownCount = {{ $requests->whereIn('status',['pending','pending_payment'])->count() }};
setInterval(function() {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc    = parser.parseFromString(html, 'text/html');
            const badge  = doc.querySelector('[data-pending-count]');
            if (!badge) return;
            const newCount = parseInt(badge.dataset.pendingCount || '0');
            if (newCount > knownCount) {
                knownCount = newCount;
                showNotification('New request received! (' + newCount + ' pending)');
            }
        }).catch(() => {});
}, 30000);

function showNotification(msg) {
    const el = document.createElement('div');
    el.style.cssText = 'position:fixed;top:1.5rem;right:1.5rem;z-index:99999;background:rgba(214,158,46,0.2);border:1px solid rgba(214,158,46,0.4);color:var(--gold);border-radius:10px;padding:0.875rem 1.25rem;font-size:0.875rem;display:flex;align-items:center;gap:0.6rem;max-width:320px;box-shadow:0 8px 32px rgba(0,0,0,0.4);';
    el.innerHTML = '<i class="bi bi-bell-fill"></i>' + msg;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 6000);
}
@endif
</script>
<div data-pending-count="{{ $requests->whereIn('status',['pending','pending_payment'])->count() }}" style="display:none;"></div>
@endpush