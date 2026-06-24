@extends('admin.layouts.app')

@section('title', 'Citizen Requests')
@section('page-title', 'Citizen Requests')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            Citizen Requests
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            Review, approve, and manage all submitted service requests
        </p>
    </div>
    <div style="display:flex;align-items:center;gap:0.625rem;flex-wrap:wrap;">
        <div style="position:relative;">
            <i class="bi bi-search" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;pointer-events:none;"></i>
            <input type="search" id="citizen-search"
                   style="background:rgba(255,255,255,0.05);border:1px solid var(--border);color:var(--text);border-radius:9px;padding:0.5rem 0.875rem 0.5rem 2.25rem;font-size:0.85rem;width:200px;outline:none;transition:border-color 0.2s;"
                   placeholder="Search by name…" autocomplete="off"
                   onfocus="this.style.borderColor='rgba(214,158,46,0.5)'"
                   onblur="this.style.borderColor='var(--border)'">
        </div>
        <select id="status-filter"
                style="background:rgba(255,255,255,0.05);border:1px solid var(--border);color:var(--text);border-radius:9px;padding:0.5rem 0.875rem;font-size:0.85rem;outline:none;cursor:pointer;"
                onchange="applyFilters()">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="in_review">In Review</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="pending_payment">Awaiting Payment</option>
        </select>
        <input type="date" id="date-filter"
               style="background:rgba(255,255,255,0.05);border:1px solid var(--border);color:var(--text);border-radius:9px;padding:0.5rem 0.875rem;font-size:0.85rem;outline:none;transition:border-color 0.2s;color-scheme:dark;"
               onfocus="this.style.borderColor='rgba(214,158,46,0.5)'"
               onblur="this.style.borderColor='var(--border)'">
        <button id="clear-filters"
                style="background:rgba(255,255,255,0.05);border:1px solid var(--border);color:var(--muted);border-radius:9px;padding:0.5rem 0.875rem;font-size:0.85rem;cursor:pointer;transition:all 0.2s;"
                onmouseover="this.style.color='#fff';this.style.borderColor='rgba(255,255,255,0.2)'"
                onmouseout="this.style.color='var(--muted)';this.style.borderColor='var(--border)'">
            Clear
        </button>
        <div style="background:rgba(214,158,46,0.15);border:1px solid rgba(214,158,46,0.3);color:var(--gold);font-family:'Syne',sans-serif;font-weight:700;font-size:0.875rem;padding:0.4rem 0.875rem;border-radius:9px;">
            {{ $requests->flatten()->count() }} Total
        </div>
    </div>
</div>

@if(session('success'))
    <div style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.35);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#6ee7b7;display:flex;align-items:center;gap:0.75rem;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif

@if($requests->isEmpty())
    <div class="admin-card" style="text-align:center;padding:4rem 2rem;">
        <i class="bi bi-folder-check" style="font-size:3rem;color:var(--muted);opacity:0.4;display:block;margin-bottom:1rem;"></i>
        <p style="color:var(--muted);margin:0;">No citizen requests found.</p>
    </div>
@else

{{-- Hidden bulk submission form (outside the table to avoid nested-form/_method conflicts) --}}
<form id="bulk-form" method="POST" action="{{ route('requests.bulk-action') }}" style="display:none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input" value="">
</form>

{{-- Bulk Action Bar --}}
<div id="bulk-bar"
     style="display:none;background:rgba(214,158,46,0.1);border:1px solid rgba(214,158,46,0.25);border-radius:10px;padding:0.75rem 1.25rem;margin-bottom:1rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
    <span id="bulk-count" style="font-size:0.85rem;color:var(--gold);font-weight:600;">0 selected</span>
    <button type="button" onclick="submitBulk('approve')" class="btn-emerald" style="padding:0.35rem 0.9rem;font-size:0.82rem;">
        <i class="bi bi-check-lg"></i> Approve Selected
    </button>
    <button type="button" onclick="submitBulk('reject')" class="btn-danger-soft" style="padding:0.35rem 0.9rem;font-size:0.82rem;">
        <i class="bi bi-x-lg"></i> Reject Selected
    </button>
    <button type="button" onclick="clearSelection()" class="btn-ghost" style="padding:0.35rem 0.7rem;font-size:0.82rem;">
        <i class="bi bi-x"></i> Clear
    </button>
</div>

<div class="admin-card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="admin-table" id="requests-table">
                <thead>
                    <tr>
                        <th style="width:36px;">
                            <input type="checkbox" id="select-all"
                                   style="width:16px;height:16px;cursor:pointer;accent-color:var(--gold);"
                                   title="Select all pending/in-review">
                        </th>
                        <th>#</th>
                        <th>Citizen</th>
                        <th>Service</th>
                        <th>Office</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Documents</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $date => $group)
                    <tr class="date-separator" data-separator="1">
                        <td colspan="10"
                            style="background:rgba(214,158,46,0.06);border-bottom:1px solid rgba(214,158,46,0.15);padding:0.5rem 1rem;">
                            <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.8rem;color:var(--gold);">
                                {{ $date }}
                            </span>
                            <span style="color:var(--muted);font-size:0.78rem;margin-left:0.5rem;">
                                ({{ $group->count() }} {{ Str::plural('request', $group->count()) }})
                            </span>
                        </td>
                    </tr>
                    @foreach($group as $req)
                    @php
                        $localPayment  = $req->localPayments->where('status', 'confirmed')->first();
                        $cryptoPayment = $req->cryptoTransactions->where('status', 'confirmed')->first();
                        $isActionable  = in_array($req->status, ['pending', 'in_review']);
                        // SLA: flag pending/in_review requests older than 3 days
                        $slaWarning    = $isActionable && $req->created_at && $req->created_at->diffInDays(now()) >= 3;
                    @endphp
                    <tr data-date="{{ $req->created_at?->format('Y-m-d') ?? '' }}"
                        data-status="{{ $req->status }}"
                        data-name="{{ strtolower(($req->user?->first_name ?? '') . ' ' . ($req->user?->last_name ?? '')) }}"
                        style="{{ $slaWarning ? 'border-left:3px solid rgba(239,68,68,0.6);' : '' }}">
                        <td>
                            @if($isActionable)
                            <input type="checkbox" name="ids[]" value="{{ $req->id }}"
                                   class="row-checkbox"
                                   style="width:16px;height:16px;cursor:pointer;accent-color:var(--gold);"
                                   onchange="updateBulkBar()">
                            @endif
                        </td>
                        <td style="color:var(--muted);font-size:0.78rem;">
                            {{ $req->id }}
                            @if($slaWarning)
                                <span title="Pending {{ $req->created_at->diffInDays(now()) }} days — SLA overdue"
                                      style="color:#f87171;margin-left:4px;font-size:0.7rem;">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;color:#fff;">{{ $req->user?->first_name ?? '—' }} {{ $req->user?->last_name ?? '' }}</div>
                            <div style="font-size:0.75rem;color:var(--muted);">{{ $req->user?->email ?? '—' }}</div>
                        </td>
                        <td style="color:var(--text);font-weight:500;">{{ $req->service?->name ?? '—' }}</td>
                        <td style="color:var(--muted);font-size:0.82rem;">{{ $req->office?->name ?? '—' }}</td>
                        <td>
                            <div style="font-size:0.82rem;color:var(--muted);">{{ $req->created_at?->format('d M Y') ?? '—' }}</div>
                            <div style="font-size:0.75rem;color:var(--muted);">{{ $req->created_at?->format('H:i') ?? '' }}</div>
                            @if($slaWarning)
                                <div style="font-size:0.7rem;color:#f87171;margin-top:2px;">
                                    {{ $req->created_at->diffInDays(now()) }}d overdue
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusStyle = match($req->status) {
                                    'pending'         => 'background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:#fcd34d;',
                                    'pending_payment' => 'background:rgba(96,165,250,0.15);border:1px solid rgba(96,165,250,0.3);color:#93c5fd;',
                                    'in_review'       => 'background:rgba(96,165,250,0.15);border:1px solid rgba(96,165,250,0.3);color:#93c5fd;',
                                    'approved'        => 'background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);color:#6ee7b7;',
                                    'rejected'        => 'background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#f87171;',
                                    default           => 'background:rgba(100,116,139,0.15);border:1px solid rgba(100,116,139,0.25);color:var(--muted);',
                                };
                            @endphp
                            <span style="{{ $statusStyle }}font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">
                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                            </span>
                            @if($req->histories->isNotEmpty())
                                <button type="button"
                                        onclick="toggleHistory('hist-{{ $req->id }}')"
                                        style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:0.72rem;margin-top:4px;display:block;padding:0;"
                                        title="View status history">
                                    <i class="bi bi-clock-history me-1"></i>{{ $req->histories->count() }} events
                                </button>
                            @endif
                        </td>
                        <td>
                            @if($req->payment_status === 'paid')
                                @if($localPayment)
                                    <span style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);color:#6ee7b7;font-size:0.72rem;padding:0.2rem 0.55rem;border-radius:20px;font-weight:700;">
                                        {{ strtoupper($localPayment->method) }}
                                    </span>
                                    <div style="font-size:0.74rem;color:var(--muted);margin-top:0.3rem;">
                                        Ref: <strong style="color:var(--text);">{{ $localPayment->reference_number }}</strong>
                                    </div>
                                    <div style="font-size:0.74rem;color:var(--muted);">${{ number_format($localPayment->amount_usd, 2) }}</div>
                                @elseif($cryptoPayment)
                                    <span style="background:rgba(247,147,26,0.15);border:1px solid rgba(247,147,26,0.3);color:#fcd34d;font-size:0.72rem;padding:0.2rem 0.55rem;border-radius:20px;font-weight:700;">
                                        {{ $cryptoPayment->currency }}
                                    </span>
                                    <div style="font-size:0.74rem;color:var(--muted);margin-top:0.3rem;max-width:140px;word-break:break-all;">
                                        TX: <strong style="color:var(--text);">{{ Str::limit($cryptoPayment->tx_hash, 18) }}</strong>
                                    </div>
                                    <div style="font-size:0.74rem;color:var(--muted);">{{ $cryptoPayment->amount_crypto }} {{ $cryptoPayment->currency }}</div>
                                @else
                                    <span style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);color:#6ee7b7;font-size:0.72rem;padding:0.2rem 0.55rem;border-radius:20px;font-weight:700;">Paid</span>
                                @endif
                            @else
                                <span style="color:var(--muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($req->uploaded_document)
                                @php $docs = json_decode($req->uploaded_document, true) ?? []; @endphp
                                @foreach($docs as $i => $path)
                                    <a href="{{ route('requests.document', [$req, $i]) }}" target="_blank" class="btn-ghost"
                                       style="padding:0.25rem 0.6rem;font-size:0.75rem;margin-bottom:0.25rem;display:inline-flex;">
                                        <i class="bi bi-paperclip"></i> Doc {{ $i + 1 }}
                                    </a>
                                @endforeach
                            @else
                                <span style="color:var(--muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($isActionable)
                                    @if($req->payment_status === 'paid')
                                        <form method="POST" action="{{ route('requests.approve', $req) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn-emerald"
                                                    onclick="return confirm('Approve and generate certificate?')">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                        </form>
                                    @else
                                        <span style="color:var(--muted);font-size:0.78rem;font-style:italic;">Awaiting payment</span>
                                    @endif
                                    <button type="button" class="btn-danger-soft"
                                            onclick="openRejectModal({{ $req->id }})">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                @endif

                                @if($req->status === 'approved')
                                    <a href="{{ route('requests.certificate', $req) }}" class="btn-gold"
                                       style="padding:0.35rem 0.85rem;font-size:0.8rem;">
                                        <i class="bi bi-download"></i> Certificate
                                    </a>
                                @endif

                                @if($req->payment_status === 'paid')
                                    <a href="{{ route('requests.payment-receipt', $req) }}" class="btn-ghost"
                                       style="padding:0.35rem 0.7rem;font-size:0.78rem;" title="Download payment receipt">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- History panel (hidden by default) --}}
                    @if($req->histories->isNotEmpty())
                    <tr id="hist-{{ $req->id }}" style="display:none;">
                        <td colspan="10" style="padding:0;border-bottom:1px solid var(--border);">
                            <div style="background:rgba(0,0,0,0.2);padding:0.875rem 1.5rem;">
                                <div style="font-size:0.78rem;font-weight:600;color:var(--gold);margin-bottom:0.6rem;text-transform:uppercase;letter-spacing:0.05em;">
                                    <i class="bi bi-clock-history me-1"></i>Status History
                                </div>
                                <div style="display:flex;flex-direction:column;gap:0.4rem;">
                                    @foreach($req->histories as $hist)
                                    <div style="display:flex;align-items:center;gap:0.75rem;font-size:0.78rem;">
                                        <span style="color:var(--muted);flex-shrink:0;min-width:130px;">{{ $hist->created_at->format('d M Y H:i') }}</span>
                                        @if($hist->from_status)
                                            <span style="color:var(--muted);">{{ ucfirst(str_replace('_',' ',$hist->from_status)) }}</span>
                                            <i class="bi bi-arrow-right" style="color:var(--muted);font-size:0.7rem;"></i>
                                        @endif
                                        <span style="font-weight:600;color:#fff;">{{ ucfirst(str_replace('_',' ',$hist->to_status)) }}</span>
                                        @if($hist->user)
                                            <span style="color:var(--muted);">by {{ $hist->user->first_name }} {{ $hist->user->last_name }}</span>
                                        @endif
                                        @if($hist->note)
                                            <span style="color:var(--muted);font-style:italic;">"{{ $hist->note }}"</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif

                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endif

{{-- Reject Modal --}}
<div id="reject-modal"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#162947;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:440px;width:90%;">
        <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin:0 0 1.25rem;">Reject Request</h5>
        <form id="reject-form" method="POST" action="">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="form-label-custom">Rejection Note (optional)</label>
                <textarea name="rejection_note" rows="3" class="form-control-custom"
                          placeholder="Reason for rejection shown to the citizen…"></textarea>
            </div>
            <div class="d-flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                <button type="submit" class="btn-danger-soft" style="flex:1;justify-content:center;">
                    <i class="bi bi-x-lg"></i> Confirm Reject
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Filters ────────────────────────────────────────────────────────────────
const searchInput  = document.getElementById('citizen-search');
const dateInput    = document.getElementById('date-filter');
const statusFilter = document.getElementById('status-filter');
const clearBtn     = document.getElementById('clear-filters');

function applyFilters() {
    const query     = searchInput.value.toLowerCase().trim();
    const dateVal   = dateInput.value;
    const statusVal = statusFilter.value;
    const rows      = document.querySelectorAll('#requests-table tbody tr');

    rows.forEach(row => {
        if (row.dataset.separator) return;
        if (row.id && row.id.startsWith('hist-')) return;
        const name     = row.dataset.name    ?? '';
        const rowDate  = row.dataset.date    ?? '';
        const rowStat  = row.dataset.status  ?? '';
        const nameOk   = !query     || name.includes(query);
        const dateOk   = !dateVal   || rowDate === dateVal;
        const statOk   = !statusVal || rowStat === statusVal;
        const visible  = nameOk && dateOk && statOk;
        row.style.display = visible ? '' : 'none';
        // also hide related history row
        const histRow = document.getElementById('hist-' + (row.dataset.id ?? ''));
        if (histRow && !visible) histRow.style.display = 'none';
    });

    rows.forEach(row => {
        if (!row.dataset.separator) return;
        let next = row.nextElementSibling;
        let anyVisible = false;
        while (next && !next.dataset.separator) {
            if (!next.id?.startsWith('hist-') && next.style.display !== 'none') anyVisible = true;
            next = next.nextElementSibling;
        }
        row.style.display = anyVisible ? '' : 'none';
    });
}

searchInput.addEventListener('input', applyFilters);
dateInput.addEventListener('change', applyFilters);
clearBtn.addEventListener('click', () => {
    searchInput.value = '';
    dateInput.value   = '';
    statusFilter.value = '';
    applyFilters();
    clearSelection();
});

// ── History Toggle ─────────────────────────────────────────────────────────
function toggleHistory(id) {
    const row = document.getElementById(id);
    if (!row) return;
    row.style.display = row.style.display === 'none' ? '' : 'none';
}

// ── Bulk Actions ───────────────────────────────────────────────────────────
const bulkBar   = document.getElementById('bulk-bar');
const bulkCount = document.getElementById('bulk-count');

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    if (checked > 0) {
        bulkBar.style.display = 'flex';
        bulkCount.textContent = checked + ' selected';
    } else {
        bulkBar.style.display = 'none';
    }
}

document.getElementById('select-all').addEventListener('change', function () {
    document.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = this.checked; });
    updateBulkBar();
});

function clearSelection() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    bulkBar.style.display = 'none';
}

function submitBulk(action) {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    if (!checkboxes.length) return;
    if (!confirm('Are you sure you want to ' + action + ' ' + checkboxes.length + ' request(s)?')) return;
    const form = document.getElementById('bulk-form');
    form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
    checkboxes.forEach(cb => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ids[]';
        inp.value = cb.value;
        form.appendChild(inp);
    });
    document.getElementById('bulk-action-input').value = action;
    form.submit();
}

// ── Reject Modal ───────────────────────────────────────────────────────────
function openRejectModal(id) {
    const base = '{{ rtrim(url('/'), '/') }}';
    document.getElementById('reject-form').action = base + '/requests/' + id;
    const modal = document.getElementById('reject-modal');
    modal.style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('reject-modal').style.display = 'none';
}
document.getElementById('reject-modal').addEventListener('click', e => {
    if (e.target === document.getElementById('reject-modal')) closeRejectModal();
});
</script>
@endpush
