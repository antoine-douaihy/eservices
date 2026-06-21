@extends('admin.layouts.app')

@section('title', 'Government Offices')
@section('page-title', 'Government Offices')

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            Government Offices
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            Manage all offices and their assigned municipalities.
        </p>
    </div>
    <a href="{{ route('admin.offices.create') }}" class="btn-gold">
        <i class="bi bi-plus-lg"></i> Add Office
    </a>
</div>

{{-- ── STATS ROW ── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:rgba(214,158,46,0.15);border:1px solid rgba(214,158,46,0.25);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-building-fill" style="color:var(--gold);font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">
                    {{ $offices->total() }}
                </div>
                <div style="font-size:0.78rem;color:var(--muted);">Total Offices</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-geo-alt-fill" style="color:#6ee7b7;font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">
                    {{ $municipalities->count() }}
                </div>
                <div style="font-size:0.78rem;color:var(--muted);">Municipalities</div>
            </div>
        </div>
    </div>
</div>

{{-- ── FILTERS ── --}}
<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.offices.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label-custom">Search</label>
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;"></i>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control-custom"
                           style="padding-left:2.4rem;"
                           placeholder="Search by name or code…">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label-custom">Municipality</label>
                <select name="municipality_id" class="form-select-custom">
                    <option value="">All Municipalities</option>
                    @foreach($municipalities as $m)
                        <option value="{{ $m->id }}" {{ request('municipality_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn-gold" style="flex:1;justify-content:center;">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ route('admin.offices.index') }}" class="btn-ghost" style="padding:0.6rem 0.9rem;">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ── TABLE ── --}}
<div class="admin-card" style="padding:0;overflow:hidden;">
    @if($offices->isEmpty())
        <div style="text-align:center;padding:4rem 2rem;">
            <i class="bi bi-building" style="font-size:3rem;color:var(--muted);opacity:0.4;"></i>
            <p style="color:var(--muted);margin-top:1rem;font-size:0.9rem;">No offices found. Start by adding one.</p>
            <a href="{{ route('admin.offices.create') }}" class="btn-gold mt-3">
                <i class="bi bi-plus-lg"></i> Add First Office
            </a>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Office Name</th>
                        <th>Code</th>
                        <th>Municipality</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offices as $office)
                    <tr>
                        <td style="color:var(--muted);font-size:0.78rem;">{{ $office->id }}</td>
                        <td>
                            <div style="font-weight:600;color:#fff;">{{ $office->name }}</div>
                            @if($office->address)
                                <div style="font-size:0.78rem;color:var(--muted);margin-top:2px;">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $office->address }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($office->code)
                                <span style="background:rgba(214,158,46,0.1);border:1px solid rgba(214,158,46,0.2);color:var(--gold);font-size:0.75rem;padding:0.15rem 0.6rem;border-radius:6px;font-family:monospace;">
                                    {{ $office->code }}
                                </span>
                            @else
                                <span style="color:var(--muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.5rem;">
                                <i class="bi bi-geo-alt-fill" style="color:var(--emerald-light);font-size:0.8rem;"></i>
                                {{ $office->municipality->name ?? '—' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size:0.8rem;">
                                @if($office->phone)
                                    <div style="color:var(--muted);"><i class="bi bi-telephone me-1"></i>{{ $office->phone }}</div>
                                @endif
                                @if($office->email)
                                    <div style="color:var(--muted);"><i class="bi bi-envelope me-1"></i>{{ $office->email }}</div>
                                @endif
                                @if(!$office->phone && !$office->email)
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($office->is_active)
                                <span class="badge-active"><i class="bi bi-circle-fill me-1" style="font-size:0.45rem;vertical-align:middle;"></i>Active</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.offices.edit', $office) }}" class="btn-edit-soft">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                                <button type="button" class="btn-danger-soft"
                                        data-office-id="{{ $office->id }}"
                                        data-office-name="{{ $office->name }}"
                                        onclick="confirmDelete(this.dataset.officeId, this.dataset.officeName)">
                                    <i class="bi bi-trash3-fill"></i> Delete
                                </button>
                                <form id="delete-form-{{ $office->id }}"
                                      action="{{ route('admin.offices.destroy', $office) }}"
                                      method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($offices->hasPages())
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
                <span style="font-size:0.8rem;color:var(--muted);">
                    Showing {{ $offices->firstItem() }}–{{ $offices->lastItem() }} of {{ $offices->total() }} offices
                </span>
                <div class="d-flex gap-2">
                    @if($offices->onFirstPage())
                        <span class="btn-ghost" style="opacity:0.4;cursor:not-allowed;padding:0.4rem 0.85rem;font-size:0.8rem;">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $offices->previousPageUrl() }}" class="btn-ghost" style="padding:0.4rem 0.85rem;font-size:0.8rem;">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    @endif

                    @if($offices->hasMorePages())
                        <a href="{{ $offices->nextPageUrl() }}" class="btn-ghost" style="padding:0.4rem 0.85rem;font-size:0.8rem;">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @else
                        <span class="btn-ghost" style="opacity:0.4;cursor:not-allowed;padding:0.4rem 0.85rem;font-size:0.8rem;">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

{{-- ── DELETE MODAL ── --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#162947;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:420px;width:90%;margin:auto;">
        <div style="text-align:center;margin-bottom:1.5rem;">
            <div style="width:56px;height:56px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-trash3-fill" style="color:#f87171;font-size:1.4rem;"></i>
            </div>
            <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin-bottom:0.5rem;">Delete Office?</h5>
            <p style="color:var(--muted);font-size:0.875rem;margin:0;">
                You are about to delete <strong id="deleteOfficeName" style="color:#fff;"></strong>.
                This action cannot be undone.
            </p>
        </div>
        <div class="d-flex gap-3">
            <button onclick="closeDeleteModal()" class="btn-ghost" style="flex:1;justify-content:center;">
                Cancel
            </button>
            <button onclick="submitDelete()" class="btn-danger-soft" style="flex:1;justify-content:center;font-size:0.875rem;padding:0.6rem;">
                <i class="bi bi-trash3-fill"></i> Yes, Delete
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let pendingDeleteId = null;

function confirmDelete(id, name) {
    pendingDeleteId = id;
    document.getElementById('deleteOfficeName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    pendingDeleteId = null;
    document.getElementById('deleteModal').style.display = 'none';
}

function submitDelete() {
    if (pendingDeleteId) {
        document.getElementById('delete-form-' + pendingDeleteId).submit();
    }
}

// Close on backdrop click
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush
