@extends('admin.layouts.app')

@section('title', 'Municipalities')
@section('page-title', 'Municipalities')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            Municipalities
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            Manage municipalities linked to offices and citizens.
        </p>
    </div>
    <button class="btn-gold" onclick="document.getElementById('createModal').style.display='flex'">
        <i class="bi bi-plus-lg"></i> Add Municipality
    </button>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:rgba(214,158,46,0.15);border:1px solid rgba(214,158,46,0.25);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-geo-alt-fill" style="color:var(--gold);font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">
                    {{ $municipalities->count() }}
                </div>
                <div style="font-size:0.78rem;color:var(--muted);">Total</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-check-circle-fill" style="color:#6ee7b7;font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">
                    {{ $municipalities->where('is_active', true)->count() }}
                </div>
                <div style="font-size:0.78rem;color:var(--muted);">Active</div>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="admin-card" style="padding:0;overflow:hidden;">
    @if($municipalities->isEmpty())
        <div style="text-align:center;padding:4rem 2rem;">
            <i class="bi bi-geo-alt" style="font-size:3rem;color:var(--muted);opacity:0.4;"></i>
            <p style="color:var(--muted);margin-top:1rem;font-size:0.9rem;">No municipalities yet.</p>
            <button class="btn-gold mt-3" onclick="document.getElementById('createModal').style.display='flex'">
                <i class="bi bi-plus-lg"></i> Add First Municipality
            </button>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Region</th>
                        <th>Postal Code</th>
                        <th>Offices</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($municipalities as $m)
                    <tr>
                        <td style="color:var(--muted);font-size:0.78rem;">{{ $m->id }}</td>
                        <td style="font-weight:600;color:#fff;">{{ $m->name }}</td>
                        <td style="color:var(--muted);font-size:0.85rem;">{{ $m->region ?? '—' }}</td>
                        <td style="color:var(--muted);font-size:0.85rem;">{{ $m->postal_code ?? '—' }}</td>
                        <td>
                            <span style="background:rgba(214,158,46,0.1);border:1px solid rgba(214,158,46,0.2);color:var(--gold);font-size:0.75rem;padding:0.15rem 0.6rem;border-radius:6px;">
                                {{ $m->offices_count }}
                            </span>
                        </td>
                        <td>
                            @if($m->is_active)
                                <span class="badge-active"><i class="bi bi-circle-fill me-1" style="font-size:0.45rem;vertical-align:middle;"></i>Active</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn-edit-soft"
                                        data-id="{{ $m->id }}"
                                        data-name="{{ $m->name }}"
                                        data-region="{{ $m->region }}"
                                        data-postal="{{ $m->postal_code }}"
                                        data-active="{{ $m->is_active ? '1' : '0' }}"
                                        onclick="openEdit(this)">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </button>
                                <button type="button" class="btn-danger-soft"
                                        data-id="{{ $m->id }}"
                                        data-name="{{ $m->name }}"
                                        onclick="confirmDelete(this.dataset.id, this.dataset.name)">
                                    <i class="bi bi-trash3-fill"></i> Delete
                                </button>
                                <form id="delete-form-{{ $m->id }}"
                                      action="{{ route('admin.municipalities.destroy', $m) }}"
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
    @endif
</div>

{{-- CREATE MODAL --}}
<div id="createModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#162947;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:480px;width:90%;margin:auto;">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin:0;">Add Municipality</h5>
            <button onclick="document.getElementById('createModal').style.display='none'" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.2rem;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.municipalities.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label-custom">Name <span style="color:#f87171;">*</span></label>
                <input type="text" name="name" class="form-control-custom" placeholder="e.g. Quezon City" required>
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Region</label>
                <input type="text" name="region" class="form-control-custom" placeholder="e.g. NCR">
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Postal Code</label>
                <input type="text" name="postal_code" class="form-control-custom" placeholder="e.g. 1100">
            </div>
            <div class="mb-4 d-flex align-items-center gap-2">
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="toggle-slider"></span>
                </label>
                <span style="font-size:0.85rem;color:var(--muted);">Active</span>
            </div>
            <div class="d-flex gap-3">
                <button type="button" onclick="document.getElementById('createModal').style.display='none'" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                <button type="submit" class="btn-gold" style="flex:1;justify-content:center;">
                    <i class="bi bi-plus-lg"></i> Create
                </button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div id="editModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#162947;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:480px;width:90%;margin:auto;">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin:0;">Edit Municipality</h5>
            <button onclick="document.getElementById('editModal').style.display='none'" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.2rem;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label-custom">Name <span style="color:#f87171;">*</span></label>
                <input type="text" id="editName" name="name" class="form-control-custom" required>
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Region</label>
                <input type="text" id="editRegion" name="region" class="form-control-custom">
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Postal Code</label>
                <input type="text" id="editPostal" name="postal_code" class="form-control-custom">
            </div>
            <div class="mb-4 d-flex align-items-center gap-2">
                <label class="toggle-switch">
                    <input type="checkbox" id="editActive" name="is_active" value="1">
                    <span class="toggle-slider"></span>
                </label>
                <span style="font-size:0.85rem;color:var(--muted);">Active</span>
            </div>
            <div class="d-flex gap-3">
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                <button type="submit" class="btn-gold" style="flex:1;justify-content:center;">
                    <i class="bi bi-check-lg"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- DELETE MODAL --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#162947;border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:420px;width:90%;margin:auto;">
        <div style="text-align:center;margin-bottom:1.5rem;">
            <div style="width:56px;height:56px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-trash3-fill" style="color:#f87171;font-size:1.4rem;"></i>
            </div>
            <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin-bottom:0.5rem;">Delete Municipality?</h5>
            <p style="color:var(--muted);font-size:0.875rem;margin:0;">
                You are about to delete <strong id="deleteName" style="color:#fff;"></strong>.
                Municipalities with offices cannot be deleted.
            </p>
        </div>
        <div class="d-flex gap-3">
            <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
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

function openEdit(btn) {
    const d = btn.dataset;
    document.getElementById('editForm').action = '/admin/municipalities/' + d.id;
    document.getElementById('editName').value   = d.name;
    document.getElementById('editRegion').value = d.region || '';
    document.getElementById('editPostal').value = d.postal || '';
    document.getElementById('editActive').checked = d.active === '1';
    document.getElementById('editModal').style.display = 'flex';
}

function confirmDelete(id, name) {
    pendingDeleteId = id;
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
}

function submitDelete() {
    if (pendingDeleteId) {
        document.getElementById('delete-form-' + pendingDeleteId).submit();
    }
}

[['createModal'], ['editModal'], ['deleteModal']].forEach(([id]) => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
</script>
@endpush
