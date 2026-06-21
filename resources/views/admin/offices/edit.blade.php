@extends('admin.layouts.app')

@section('title', 'Edit Office')
@section('page-title', 'Edit Office')

@section('content')

{{-- ── BREADCRUMB ── --}}
<div class="d-flex align-items-center gap-2 mb-4" style="font-size:0.82rem;color:var(--muted);">
    <a href="{{ route('admin.offices.index') }}" style="color:var(--muted);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--muted)'">
        <i class="bi bi-building-fill me-1"></i>Offices
    </a>
    <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
    <span style="color:var(--gold);">Edit — {{ $office->name }}</span>
</div>

<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">

        {{-- HEADER --}}
        <div class="d-flex align-items-start justify-content-between mb-4 gap-3 flex-wrap">
            <div>
                <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
                    Edit Office
                </h1>
                <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
                    Editing: <strong style="color:var(--gold);">{{ $office->name }}</strong>
                    @if($office->code)
                        <span style="background:rgba(214,158,46,0.1);border:1px solid rgba(214,158,46,0.2);color:var(--gold);font-size:0.72rem;padding:0.1rem 0.5rem;border-radius:5px;font-family:monospace;margin-left:6px;">{{ $office->code }}</span>
                    @endif
                </p>
            </div>
            {{-- Quick delete from edit page --}}
            <button type="button" class="btn-danger-soft"
                    onclick="confirmDelete({{ $office->id }}, '{{ addslashes($office->name) }}')">
                <i class="bi bi-trash3-fill"></i> Delete Office
            </button>
        </div>

        {{-- VALIDATION ERRORS --}}
        @if($errors->any())
            <div class="alert-error-custom mb-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Please fix the following errors:</strong>
                </div>
                <ul style="margin:0;padding-left:1.25rem;font-size:0.82rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.offices.update', $office) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- ── BASIC INFO ── --}}
            <div class="admin-card mb-4">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div style="width:36px;height:36px;background:rgba(214,158,46,0.12);border:1px solid rgba(214,158,46,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-info-circle-fill" style="color:var(--gold);font-size:0.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Basic Information</div>
                        <div style="font-size:0.78rem;color:var(--muted);">Core details about the office</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label-custom">Office Name <span style="color:#f87171;">*</span></label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $office->name) }}"
                               class="form-control-custom @error('name') border-danger @enderror"
                               placeholder="e.g. Ministry of Finance — North Branch">
                        @error('name')
                            <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-5">
                        <label class="form-label-custom">Office Code</label>
                        <input type="text"
                               name="code"
                               value="{{ old('code', $office->code) }}"
                               class="form-control-custom @error('code') border-danger @enderror"
                               placeholder="e.g. MOF-001"
                               style="font-family:monospace;">
                        @error('code')
                            <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Description</label>
                        <textarea name="description"
                                  class="form-control-custom"
                                  placeholder="Brief description…">{{ old('description', $office->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── LOCATION ── --}}
            <div class="admin-card mb-4">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div style="width:36px;height:36px;background:rgba(4,120,87,0.12);border:1px solid rgba(4,120,87,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-geo-alt-fill" style="color:var(--emerald-light);font-size:0.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Location & Municipality</div>
                        <div style="font-size:0.78rem;color:var(--muted);">Assign this office to a municipality and pin its location</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-custom">Municipality <span style="color:#f87171;">*</span></label>
                        <select name="municipality_id"
                                class="form-select-custom @error('municipality_id') border-danger @enderror">
                            <option value="">— Select Municipality —</option>
                            @foreach($municipalities as $m)
                                <option value="{{ $m->id }}"
                                    {{ old('municipality_id', $office->municipality_id) == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('municipality_id')
                            <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">City</label>
                        <input type="text"
                               name="city"
                               id="city"
                               value="{{ old('city', $office->city) }}"
                               class="form-control-custom"
                               placeholder="e.g. Beirut">
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Street Address</label>
                        <input type="text"
                               name="address"
                               id="address"
                               value="{{ old('address', $office->address) }}"
                               class="form-control-custom"
                               placeholder="e.g. 12 Main Street, Floor 3">
                    </div>

                    {{-- Hidden coordinate fields --}}
                    <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude',  $office->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $office->longitude) }}">

                    {{-- Leaflet Map Picker --}}
                    <div class="col-12">
                        <label class="form-label-custom">
                            Pin Office on Map
                            <span style="font-weight:400;color:var(--muted);margin-left:4px;">(click to move marker)</span>
                        </label>

                        {{-- Search box --}}
                        <div style="position:relative;margin-bottom:0.6rem;">
                            <i class="bi bi-search" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;pointer-events:none;z-index:1;"></i>
                            <input id="map-search"
                                   type="text"
                                   placeholder="Search for an address…"
                                   class="form-control-custom"
                                   style="padding-left:2.4rem;">
                            <div id="search-results" style="position:absolute;top:100%;left:0;right:0;background:#162947;border:1px solid var(--border);border-radius:8px;z-index:1000;display:none;max-height:200px;overflow-y:auto;margin-top:4px;"></div>
                        </div>

                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                        <div id="office-map"
                             style="width:100%;height:320px;border-radius:12px;border:1px solid var(--border);overflow:hidden;">
                        </div>

                        <div id="coords-display"
                             style="font-size:0.75rem;color:var(--muted);margin-top:6px;{{ ($office->latitude && $office->longitude) ? '' : 'display:none;' }}">
                            <i class="bi bi-geo-alt-fill" style="color:var(--emerald-light);"></i>
                            Pinned: <span id="coords-text">{{ $office->latitude ? number_format($office->latitude,5).', '.number_format($office->longitude,5) : '' }}</span>
                            &nbsp;&mdash;&nbsp;
                            <button type="button" onclick="clearPin()"
                                    style="background:none;border:none;color:#f87171;font-size:0.75rem;cursor:pointer;padding:0;">
                                Remove pin
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── CONTACT ── --}}
            <div class="admin-card mb-4">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div style="width:36px;height:36px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-telephone-fill" style="color:#a5b4fc;font-size:0.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Contact Information</div>
                        <div style="font-size:0.78rem;color:var(--muted);">How citizens can reach this office</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-custom">Phone Number</label>
                        <div style="position:relative;">
                            <i class="bi bi-telephone" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;"></i>
                            <input type="text" name="phone" value="{{ old('phone', $office->phone) }}"
                                   class="form-control-custom" style="padding-left:2.4rem;"
                                   placeholder="+961 1 000 000">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Email Address</label>
                        <div style="position:relative;">
                            <i class="bi bi-envelope" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem;"></i>
                            <input type="email" name="email" value="{{ old('email', $office->email) }}"
                                   class="form-control-custom @error('email') border-danger @enderror"
                                   style="padding-left:2.4rem;" placeholder="office@gov.lb">
                        </div>
                        @error('email')
                            <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ── OFFICE HOURS ── --}}
            <div class="admin-card mb-4">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div style="width:36px;height:36px;background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-clock-fill" style="color:#fcd34d;font-size:0.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Office Hours</div>
                        <div style="font-size:0.78rem;color:var(--muted);">When citizens can visit or contact this office</div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Opening Time</label>
                        <input type="time" name="opening_time" value="{{ old('opening_time', $office->opening_time) }}"
                               class="form-control-custom" style="color-scheme:dark;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Closing Time</label>
                        <input type="time" name="closing_time" value="{{ old('closing_time', $office->closing_time) }}"
                               class="form-control-custom" style="color-scheme:dark;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Working Days</label>
                        <input type="text" name="working_days" value="{{ old('working_days', $office->working_days) }}"
                               class="form-control-custom" placeholder="e.g. Mon – Fri">
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">Free-text, e.g. "Mon – Fri" or "Mon, Wed, Fri"</div>
                    </div>
                </div>
            </div>

            {{-- ── STATUS ── --}}
            <div class="admin-card mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Active Status</div>
                        <div style="font-size:0.8rem;color:var(--muted);margin-top:2px;">
                            Inactive offices are hidden from citizens.
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $office->is_active) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            {{-- ── ACTIONS ── --}}
            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('admin.offices.index') }}" class="btn-ghost">
                    <i class="bi bi-x-lg"></i> Cancel
                </a>
                <button type="submit" class="btn-gold">
                    <i class="bi bi-floppy-fill"></i> Save Changes
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
            <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin-bottom:0.5rem;">Delete Office?</h5>
            <p style="color:var(--muted);font-size:0.875rem;margin:0;">
                You are about to permanently delete <strong id="deleteOfficeName" style="color:#fff;"></strong>.
            </p>
        </div>
        <div class="d-flex gap-3">
            <button onclick="closeDeleteModal()" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
            <button onclick="submitDelete()" class="btn-danger-soft" style="flex:1;justify-content:center;font-size:0.875rem;padding:0.6rem;">
                <i class="bi bi-trash3-fill"></i> Yes, Delete
            </button>
        </div>
    </div>
</div>

<form id="delete-form-{{ $office->id }}" action="{{ route('admin.offices.destroy', $office) }}" method="POST" class="d-none">
    @csrf @method('DELETE')
</form>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map, marker;

document.addEventListener('DOMContentLoaded', function () {
    const defaultCenter = [33.8886, 35.4955];
    const savedLat = parseFloat(document.getElementById('latitude').value);
    const savedLng = parseFloat(document.getElementById('longitude').value);
    const hasPin   = !isNaN(savedLat) && !isNaN(savedLng);

    map = L.map('office-map').setView(hasPin ? [savedLat, savedLng] : defaultCenter, hasPin ? 15 : 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    if (hasPin) placeMarker(savedLat, savedLng);

    map.on('click', function (e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
    });

    let searchTimeout;
    const searchInput = document.getElementById('map-search');
    const resultsDiv  = document.getElementById('search-results');

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 3) { resultsDiv.style.display = 'none'; return; }
        searchTimeout = setTimeout(function () {
            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5')
                .then(function(r) { return r.json(); })
                .then(function (data) {
                    resultsDiv.innerHTML = '';
                    if (!data.length) { resultsDiv.style.display = 'none'; return; }
                    data.forEach(function (item) {
                        const div = document.createElement('div');
                        div.style.cssText = 'padding:0.6rem 1rem;cursor:pointer;font-size:0.82rem;color:#ccc;border-bottom:1px solid rgba(255,255,255,0.06);';
                        div.textContent = item.display_name;
                        div.addEventListener('mouseenter', function() { this.style.background = 'rgba(255,255,255,0.05)'; });
                        div.addEventListener('mouseleave', function() { this.style.background = ''; });
                        div.addEventListener('click', function () {
                            const lat = parseFloat(item.lat);
                            const lng = parseFloat(item.lon);
                            map.setView([lat, lng], 16);
                            placeMarker(lat, lng);
                            const addrField = document.getElementById('address');
                            if (addrField && !addrField.value) addrField.value = item.display_name;
                            searchInput.value = item.display_name;
                            resultsDiv.style.display = 'none';
                        });
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.style.display = 'block';
                });
        }, 500);
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });
});

function placeMarker(lat, lng) {
    if (marker) {
        marker.setLatLng([lat, lng]);
    } else {
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.on('dragend', function () {
            const pos = marker.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });
    }
    updateCoords(lat, lng);
}

function updateCoords(lat, lng) {
    document.getElementById('latitude').value  = lat.toFixed(7);
    document.getElementById('longitude').value = lng.toFixed(7);
    document.getElementById('coords-text').textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
    document.getElementById('coords-display').style.display = 'block';
}

function clearPin() {
    if (marker) { marker.remove(); marker = null; }
    document.getElementById('latitude').value  = '';
    document.getElementById('longitude').value = '';
    document.getElementById('coords-display').style.display = 'none';
}
</script>
@endpush

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
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush
