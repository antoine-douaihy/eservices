@extends(Auth::user()->role === 'admin' ? 'admin.layouts.app' : 'staff.layouts.app')

@section('title', 'Add Service')
@section('page-title', 'Add New Service')

@section('content')

{{-- BREADCRUMB --}}
<div class="d-flex align-items-center gap-2 mb-4" style="font-size:0.82rem;color:var(--muted);">
    <a href="{{ route('staff.services.index') }}" style="color:var(--muted);text-decoration:none;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--muted)'">
        <i class="bi bi-grid-fill me-1"></i>Service Catalog
    </a>
    <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
    <span style="color:var(--gold);">Add New</span>
</div>

<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">

        <div class="mb-4">
            <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
                Define New Service
            </h1>
            <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
                Create a service that citizens can apply for through your office.
            </p>
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

        <form action="{{ route('staff.services.store') }}" method="POST" id="serviceForm">
            @csrf

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
                    <div class="col-md-8">
                        <label class="form-label-custom">Service Name (English) <span style="color:#f87171;">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control-custom @error('name') border-danger @enderror"
                               placeholder="e.g. Marriage Certificate, Birth Registration…">
                        @error('name') <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label-custom">Service Name (Arabic) <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                        <input type="text" name="name_ar" value="{{ old('name_ar') }}" dir="rtl"
                               class="form-control-custom @error('name_ar') border-danger @enderror"
                               placeholder="مثال: شهادة زواج، تسجيل ولادة…">
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">
                            Shown to citizens automatically when they switch the site to Arabic. Leave blank to show the English name in both languages.
                        </div>
                        @error('name_ar') <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Primary Office <span style="color:#f87171;">*</span></label>
                        <select name="office_id" class="form-select-custom @error('office_id') border-danger @enderror">
                            <option value="">— Select Office —</option>
                            @foreach($offices as $o)
                                <option value="{{ $o->id }}" {{ old('office_id') == $o->id ? 'selected' : '' }}>
                                    {{ $o->name }}
                                </option>
                            @endforeach
                        </select>
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">
                            <i class="bi bi-info-circle me-1"></i>
                            This service will automatically be created at <strong>every active office</strong> (every municipality) — this is just the default shown in admin lists.
                        </div>
                        @error('office_id') <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Category</label>
                        <select name="category_id" class="form-select-custom">
                            <option value="">— No Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Description (English)</label>
                        <textarea name="description" class="form-control-custom"
                                  placeholder="Describe what this service covers and who is eligible…">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Description (Arabic) <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                        <textarea name="description_ar" dir="rtl" class="form-control-custom"
                                  placeholder="وصف الخدمة ومن يحق له الاستفادة منها…">{{ old('description_ar') }}</textarea>
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
                        <div style="font-size:0.78rem;color:var(--muted);">Set the fee and estimated processing time</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label-custom">Currency <span style="color:#f87171;">*</span></label>
                        <select name="currency" class="form-select-custom">
                            <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD — US Dollar</option>
                            <option value="LBP" {{ old('currency') == 'LBP' ? 'selected' : '' }}>LBP — Lebanese Pound</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Price <span style="color:#f87171;">*</span></label>
                        <div style="position:relative;">
                            <i class="bi bi-currency-dollar" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                            <input type="number" name="price" value="{{ old('price', 0) }}"
                                   step="0.01" min="0"
                                   class="form-control-custom @error('price') border-danger @enderror"
                                   style="padding-left:2.2rem;"
                                   placeholder="0.00">
                        </div>
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">Set to 0 for free services</div>
                        @error('price') <div style="font-size:0.78rem;color:#f87171;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Processing Time (days) <span style="color:#f87171;">*</span></label>
                        <div style="position:relative;">
                            <i class="bi bi-clock" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                            <input type="number" name="processing_days" value="{{ old('processing_days', 1) }}"
                                   min="1" class="form-control-custom" style="padding-left:2.2rem;">
                        </div>
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">Estimated business days</div>
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
                    {{-- Restore old documents on validation fail --}}
                    @if(old('documents'))
                        @foreach(old('documents') as $i => $doc)
                            <div class="doc-row" id="doc-row-{{ $i }}">
                                <i class="bi bi-grip-vertical doc-drag-handle"></i>
                                <div style="flex:1;">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Document Name (English)</label>
                                            <input type="text" name="documents[{{ $i }}][name]"
                                                   value="{{ $doc['name'] ?? '' }}"
                                                   class="form-control-custom"
                                                   placeholder="e.g. National ID, Birth Certificate">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Document Name (Arabic)</label>
                                            <input type="text" name="documents[{{ $i }}][name_ar]" dir="rtl"
                                                   value="{{ $doc['name_ar'] ?? '' }}"
                                                   class="form-control-custom"
                                                   placeholder="مثال: هوية شخصية، شهادة ميلاد">
                                        </div>
                                        <div class="col-md-9">
                                            <label class="form-label-custom">Notes / Instructions</label>
                                            <input type="text" name="documents[{{ $i }}][notes]"
                                                   value="{{ $doc['notes'] ?? '' }}"
                                                   class="form-control-custom"
                                                   placeholder="e.g. Original + 2 copies">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-custom">Required?</label>
                                            <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;">
                                                <label class="toggle-switch">
                                                    <input type="checkbox" name="documents[{{ $i }}][is_mandatory]"
                                                           {{ isset($doc['is_mandatory']) ? 'checked' : '' }}>
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
                    @else
                        {{-- Default: 1 empty row --}}
                        <div class="doc-row" id="doc-row-0">
                            <i class="bi bi-grip-vertical doc-drag-handle"></i>
                            <div style="flex:1;">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Document Name (English)</label>
                                        <input type="text" name="documents[0][name]"
                                               class="form-control-custom"
                                               placeholder="e.g. National ID, Birth Certificate">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Document Name (Arabic)</label>
                                        <input type="text" name="documents[0][name_ar]" dir="rtl"
                                               class="form-control-custom"
                                               placeholder="مثال: هوية شخصية، شهادة ميلاد">
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label-custom">Notes / Instructions</label>
                                        <input type="text" name="documents[0][notes]"
                                               class="form-control-custom"
                                               placeholder="e.g. Original + 2 copies required">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-custom">Required?</label>
                                        <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="documents[0][is_mandatory]" checked>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span style="font-size:0.75rem;color:var(--muted);">Mandatory</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="doc-remove" onclick="removeDoc(0)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <div id="noDocsMsg" style="display:none;text-align:center;padding:1.5rem;color:var(--muted);font-size:0.875rem;">
                    <i class="bi bi-file-earmark-x" style="font-size:1.5rem;opacity:0.4;display:block;margin-bottom:0.5rem;"></i>
                    No documents added. Click "Add Document" to add required documents.
                </div>
            </div>

            {{-- ── STATUS ── --}}
            <div class="s-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:#fff;">Active Status</div>
                        <div style="font-size:0.8rem;color:var(--muted);margin-top:2px;">
                            Inactive services won't be visible to citizens.
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('staff.services.index') }}" class="btn-ghost">
           