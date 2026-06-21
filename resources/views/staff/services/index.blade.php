@extends(Auth::user()->role === 'admin' ? 'admin.layouts.app' : 'staff.layouts.app')

@section('title', 'Service Catalog')
@section('page-title', 'Service Catalog')

@section('content')

{{-- HEADER --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            Service Catalog
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            Define and manage the services your office offers to citizens.
        </p>
    </div>
    <a href="{{ route('staff.services.create') }}" class="btn-gold">
        <i class="bi bi-plus-lg"></i> Add Service
    </a>
</div>

{{-- STATS --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="s-card p-3 d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:rgba(214,158,46,0.15);border:1px solid rgba(214,158,46,0.25);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-grid-fill" style="color:var(--gold);font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">{{ $services->total() }}</div>
                <div style="font-size:0.78rem;color:var(--muted);">Total Services</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="s-card p-3 d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-check-circle-fill" style="color:#6ee7b7;font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">
                    {{ $services->getCollection()->where('is_active', true)->count() }}
                </div>
                <div style="font-size:0.78rem;color:var(--muted);">Active</div>
            </div>
        </div>
    </div>
</div>

{{-- FILTERS --}}
<div class="s-card p-3 mb-4">
    <form method="GET" action="{{ route('staff.services.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label-custom">Search</label>
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control-custom" style="padding-left:2.4rem;"
                           placeholder="Search services…">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Office</label>
                <select name="office_id" class="form-select-custom">
                    <option value="">All Offices</option>
                    @foreach($offices as $o)
                        <option value="{{ $o->id }}" {{ request('office_id') == $o->id ? 'selected' : '' }}>
                            {{ $o->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-custom">Status</label>
                <select name="status" class="form-select-custom">
                    <option value="">All</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn-gold" style="flex:1;justify-content:center;">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ route('staff.services.index') }}" class="btn-ghost" style="padding:0.6rem 0.9rem;">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- TABLE --}}
<div class="s-card" style="padding:0;overflow:hidden;">
    @if($services->isEmpty())
        <div style="text-align:center;padding:4rem 2rem;">
            <i class="bi bi-grid" style="font-size:3rem;color:var(--muted);opacity:0.4;"></i>
            <p style="color:var(--muted);margin-top:1rem;font-size:0.9rem;">
                No services defined yet. Start by adding your first service.
            </p>
            <a href="{{ route('staff.services.create') }}" class="btn-gold mt-3">
                <i class="bi bi-plus-lg"></i> Add First Service
            </a>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="s-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Service Name</th>
                        <th>Office</th>
                        <th>Price</th>
                        <th>Documents</th>
                        <th>Processing</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td style="color:var(--muted);font-size:0.78rem;">{{ $service->id }}</td>
                        <td>
                            <div style="font-weight:600;color:#fff;">{{ $service->name }}</div>
                            @if($service->description)
                                <div style="font-size:0.78rem;color:var(--muted);margin-top:2px;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $service->description }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:0.82rem;color:var(--muted);">
                                <i class="bi bi-building me-1"></i>{{ $service->office->name ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @if($service->price == 0)
                                <span class="badge-free">Free</span>
                            @else
                                <span style="font-weight:600;color:var(--gold);">
                                    {{ $service->currency }} {{ number_format($service->price, 2) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @php $docCount = $service->requiredDocuments->count(); @endphp
                            @if($docCount > 0)
                                <div style="display:flex;align-items:center;gap:0.4rem;">
                                    <span style="background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.25);color:#a5b4fc;font-size:0.72rem;padding:0.2rem 0.6rem;border-radius:20px;font-weight:600;">
                                        {{ $docCount }} doc{{ $docCount > 1 ? 's' : '' }}
                                    </span>
                                    <span style="font-size:0.75rem;color:var(--muted);">
                                        ({{ $service->requiredDocuments->where('is_mandatory', true)->count() }} required)
                                    </span>
                                </div>
                            @else
                                <span style="color:var(--muted);font-size:0.8rem;">None</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:0.82rem;color:var(--muted);">
                                <i class="bi bi-clock me-1"></i>
                                {{ $service->processing_days }} day{{ $service->processing_days > 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td>
                            @if($service->is_active)
                                <span class="badge-active"><i class="bi bi-circle-fill me-1" style="font-size:0.45rem;vertical-align:middle;"></i>Active</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('staff.services.edit', $service) }}" class="btn-edit-soft">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                                <button type="button" class="btn-danger-soft"
                                        onclick="confirmDelete({{ $service->id }}, '{{ addslashes($service->name) }}')">
                                    <i class="bi bi-trash3-fill"></i> Delete
                                </button>
                                <form id="del-{{ $service->id }}" action="{{ route('staff.services.destroy', $service) }}" method="POST" class="d-none">
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
        @if($services->hasPages())
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
                <span style="font-size:0.8rem;color:var(--muted);">
                    Showing {{ $services->firstItem() }}–{{ $services->lastItem() }} of {{ $services->total() }}
                </span>
                <div class="d-flex gap-2">
                    @if($services->onFirstPage())
                        <span class="btn-ghost" style="opacity:0.4;cursor:not-allowed;padding:0.4rem 0.85rem;font-size:0.8rem;"><i class="bi bi-chevron-left"></i></span>
                    @else
                        <a href="{{ $services->previousPageUrl() }}" class="btn-ghost" style="padding:0.4rem 0.85rem;font-size:0.8rem;"><i class="bi bi-chevron-left"></i></a>
                    @endif
                    @if($services->hasMorePages())
                        <a href="{{ $services->nextPageUrl() }}" class="btn-ghost" style="padding:0.4rem 0.85rem;font-size:0.8rem;"><i class="bi bi-chevron-right"></i></a>
                    @else
                        <span class="btn-ghost" style="opacity:0.4;cursor:not-allowed;padding:0.4rem 0.85rem;font-size:0.8rem;"><i class="bi bi-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

{{-- DELETE MODAL --}}
<div id="deleteModal" class="modal-overlay">
    <div class="modal-box">
        <div style="text-align:center;margin-bottom:1.5rem;">
            <div style="width:56px;height:56px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-trash3-fill" style="color:#f87171;font-size:1.4rem;"></i>
            </div>
            <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin-bottom:0.5rem;">Delete Service?</h5>
            <p style="color:var(--muted);font-size:0.875rem;margin:0;">
                You are about to delete <strong id="delName" style="color:#fff;"></strong> from <strong>every office that offers it</strong>, not just this one. All required documents will also be deleted.
            </p>
        </div>
        <div class="d-flex gap-3">
            <button onclick="closeModal()" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
            <button onclick="submitDelete()" class="btn-danger-soft" style="flex:1;justify-content:center;font-size:0.875rem;padding:0.6rem;">
                <i class="bi bi-trash3-fill"></i> Yes, Delete
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let pendingId = null;
function confirmDelete(id, name) {
    pendingId = id;
    document.getElementById('delName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeModal() {
    pendingId = null;
    document.getElementById('deleteModal').style.display = 'none';
}
function submitDelete() {
    if (pendingId) document.getElementById('del-' + pendingId).submit();
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.ta