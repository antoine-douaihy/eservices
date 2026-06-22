@extends(Auth::user()->role === 'admin' ? 'admin.layouts.app' : 'staff.layouts.app')

@section('title', 'Edit Service')
@section('page-title', 'Edit Service')

@section('content')

{{-- BREADCRUMB --}}
<div class="d-flex align-items-center gap-2 mb-4" style="font-size:0.82rem;color:var(--muted);">
    <a href="{{ route('staff.services.index') }}" style="color:var(--muted);text-decoration:none;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--muted)'">
        <i class="bi bi-grid-fill me-1"></i>Service Catalog
    </a>
    <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
    <span style="color:var(--gold);">Edit — {{ $service->name }}</span>
</div>

<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">

        <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
                    Edit Service
                </h1>
                <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
                    Editing: <strong style="color:var(--gold);">{{ $service->name }}</strong>
                </p>
            </div>
            <button type="button" class="btn-danger-soft"
                    onclick="confirmDelete({{ $service->id }}, '{{ addslashes($service->name) }}')">
                <i class="bi bi-trash3-fill"></i> Delete Service
            </button>
        </div>

        @if($errors->any())
            <div class="alert-error-custom mb-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Please fix the following errors:</strong>
                </div>
                <ul style="margin:0;padding-left:1.25rem;font-size:0.82rem;">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('staff.services.update', $service) }}" method="POST" id="serviceForm">
            @csrf @method('PUT')

            {{-- ── BASIC INFO ── --}}
            <div class="s-card p-4 mb-4">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div style="width:36px;height:36px;background:rgba(214,158,46,0.12);border:1px solid rgba(214,158,46,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-info-circle-fill" style="color:var(--gold);font-size:0.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Service Details</div>
                        <div style="font-size:0.78rem;color:var(--muted);">Basic information about this service</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-custom">Service Name (English) <span style="color:#f87171;">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $service->name) }}"
                               class="form-control-custom @error('name') border-danger @enderror"
                               placeholder="e.g. Marriage Certificate">
                        @error('name') <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Service Name (Arabic) <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', $service->name_ar) }}" dir="rtl"
                               class="form-control-custom @error('name_ar') border-danger @enderror"
                               placeholder="مثال: شهادة زواج">
                        @error('name_ar') <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Office (this copy) <span style="color:#f87171;">*</span></label>
                        <select name="office_id" class="form-select-custom @error('office_id') border-danger @enderror" disabled>
                            @foreach($offices as $o)
                                <option value="{{ $o->id }}" {{ $service->office_id == $o->id ? 'selected' : '' }}>
                                    {{ $o->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="office_id" value="{{ $service->office_id }}">
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">
                            <i class="bi bi-info-circle me-1"></i>
                            @if($service->group_uuid)
                                Saving will update this service at <strong>all {{ \App\Models\Service::where('group_uuid', $service->group_uuid)->count() }} offices</strong> that offer it — name, price, and documents stay in sync everywhere.
                            @else
                                This service exists only at this office.
                            @endif
                        </div>
                        @error('office_id') <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Category</label>
                        <select name="category_id" class="form-select-custom">
                            <option value="">— No Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Description (English)</label>
                        <textarea name="description" class="form-control-custom"
                                  placeholder="Describe what this service covers…">{{ old('description', $service->description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Description (Arabic) <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                        <textarea name="description_ar" dir="rtl" class="form-control-custom"
                                  placeholder="وصف الخدمة…">{{ old('description_ar', $service->description_ar) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── PRICING & TIMING ── --}}
            <div class="s-card p-4 mb-4">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div style="width:36px;height:36px;background:rgba(4,120,87,0.12);border:1px solid rgba(4,120,87,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-cash-coin" style="color:var(--emerald-light);font-size:0.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Pricing & Timing</div>
                        <div style="font-size:0.78rem;color:var(--muted);">Fee and estimated processing time</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label-custom">Currency <span style="color:#f87171;">*</span></label>
                        <select name="currency" class="form-select-custom">
                            <option value="USD" {{ old('currency', $service->currency) == 'USD' ? 'selected' : '' }}>USD — US Dollar</option>
                            <option value="LBP" {{ old('currency', $service->currency) == 'LBP' ? 'selected' : '' }}>LBP — Lebanese Pound</option>
                            <option value="EUR" {{ old('currency', $service->currency) == 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Price <span style="color:#f87171;">*</span></label>
                        <div style="position:relative;">
                            <i class="bi bi-currency-dollar" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                            <input type="number" name="price" value="{{ old('price', $service->price) }}"
                                   step="0.01" min="0"
                                   class="form-control-custom @error('price') border-danger @enderror"
                                   style="padding-left:2.2rem;">
                        </div>
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">Set to 0 for free services</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Processing Time (days) <span style="color:#f87171;">*</span></label>
                        <div style="position:relative;">
                            <i class="bi bi-clock" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                            <input type="number" name="processing_days"
                                   value="{{ old('processing_days', $service->processing_days) }}"
                                   min="1" class="form-control-custom" style="padding-left:2.2rem;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── REQUIRED DOCUMENTS ── --}}
            <div class="s-card p-4 mb-4">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div style="width:36px;height:36px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-file-earmark-check-fill" style="color:#a5b4fc;font-size:0.9rem;"></i>
                        </div>
                        <div>
                            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Required Documents</div>
                            <div style="font-size:0.78rem;color:var(--muted);">What citizens must bring when applying</div>
                        </div>
                    </div>
                    <button type="button" onclick="addDocumentRow()" class="btn-emerald">
                        <i class="bi bi-plus-lg"></i> Add Document
                    </button>
                </div>

                <div id="documentsContainer">
                    @php
                        $existingDocs = old('documents')
                            ? collect(old('documents'))->values()
                            : $service->requiredDocuments;
                        $startIndex = count($existingDocs);
                    @endphp

                    @foreach($existingDocs as $i => $doc)
                        @php
                            $isOld      = is_array($doc);
                            $docName    = $isOld ? ($doc['name'] ?? '') : $doc->name;
                            $docNameAr  = $isOld ? ($doc['name_ar'] ?? '') : $doc->name_ar;
                            $docNotes   = $isOld ? ($doc['notes'] ?? '') : $doc->notes;
                            $docMandatory = $isOld ? isset($doc['is_mandatory']) : $doc->is_mandatory;
                        @endphp
                        <div class="doc-row" id="doc-row-{{ $i }}">
                            <i class="bi bi-grip-vertical doc-drag-handle"></i>
                            <div style="flex:1;">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Document Name (English)</label>
                                        <input type="text" name="documents[{{ $i }}][name]"
                                               value="{{ $docName }}"
                                               class="form-control-custom"
                                               placeholder="e.g. National ID">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Document Name (Arabic)</label>
                                        <input type="text" name="documents[{{ $i }}][name_ar]" dir="rtl"
                                               value="{{ $docNameAr }}"
                                               class="form-control-custom"
                                               placeholder="مثال: هوية شخصية">
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label-custom">Notes / Instructions</label>
                                        <input type="text" name="documents[{{ $i }}][notes]"
                                               value="{{ $docNotes }}"
                                               class="form-control-custom"
                                               placeholder="e.g. Original + 2 copies">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-custom">Required?</label>
                                        <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="documents[{{ $i }}][is_mandatory]"
                                                       {{ $docMandatory ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span style="font-size:0.75rem;color:var(--muted);">Mandatory</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="doc-remove" onclick="removeDoc({{ $i }})">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    @endforeach
                </div>

                <div id="noDocsMsg" style="{{ count($existingDocs) === 0 ? '' : 'display:none;' }}text-align:center;padding:1.5rem;color:var(--muted);font-size:0.875rem;">
                    <i class="bi bi-file-earmark-x" style="font-size:1.5rem;opacity:0.4;display:block;margin-bottom:0.5rem;"></i>
                    No documents added. Click "Add Document" to add required documents.
                </div>
            </div>

            {{-- STATUS --}}
            <div class="s-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Active Status</div>
                        <div style="font-size:0.8rem;color:var(--muted);margin-top:2px;">Inactive services won't be visible to citizens.</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('staff.services.index') }}" class="btn-ghost">
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
<div id="deleteModal" class="modal-overlay">
    <div class="modal-box">
        <div style="text-align:center;margin-bottom:1.5rem;">
            <div style="width:56px;height:56px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-trash3-fill" style="color:#f87171;font-size:1.4rem;"></i>
            </div>
            <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin-bottom:0.5rem;">Delete Service?</h5>
            <p style="color:var(--muted);font-size:0.875rem;margin:0;">
                Permanently deleting <strong id="delName" style="color:#fff;"></strong>. All required documents will also be removed.
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
<form id="delete-form-{{ $service->id }}" action="{{ route('staff.services.destroy', $service) }}" method="POST" class="d-none">
    @csrf @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
let docIndex = {{ $startIndex }};

function addDocumentRow() {
    const container = document.getElementById('documentsContainer');
    document.getElementById('noDocsMsg').style.display = 'none';

    const row = document.createElement('div');
    row.className = 'doc-row';
    row.id = 'doc-row-' + docIndex;
    row.innerHTML = `
        <i class="bi bi-grip-vertical doc-drag-handle"></i>
        <div style="flex:1;">
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label-custom">Document Name (English)</label>
                    <input type="text" name="documents[${docIndex}][name]" class="form-control-custom" placeholder="e.g. National ID">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Document Name (Arabic)</label>
                    <input type="text" name="documents[${docIndex}][name_ar]" dir="rtl" class="form-control-custom" placeholder="مثال: هوية شخصية">
                </div>
                <div class="col-md-9">
                    <label class="form-label-custom">Notes / Instructions</label>
                    <input type="text" name="documents[${docIndex}][notes]" class="form-control-custom" placeholder="e.g. Original + 2 copies">
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Required?</label>
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;">
                        <label class="toggle-switch">
                            <input type="checkbox" name="documents[${docIndex}][is_mandatory]" checked>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:0.75rem;color:var(--muted);">Mandatory</span>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="doc-remove" onclick="removeDoc(${docIndex})">
            <i class="bi bi-x-lg"></i>
        </button>
    `;
    container.appendChild(row);
    docIndex++;
}

function removeDoc(id) {
    const row = document.getElementById('doc-row-' + id);
    if (row) row.remove();
    const container = document.getElementById('documentsContainer');
    if (container.children.length === 0) {
        document.getElementById('noDocsMsg').style.display = 'block';
    }
}

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
    if (pendingId) document.getElementById('delete-form-' + pendingId).submit();
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
