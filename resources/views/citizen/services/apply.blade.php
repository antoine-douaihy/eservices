@extends('layouts.app')

@section('title', 'Apply — ' . $service->name)

@section('content')

<div style="position:relative;z-index:1;padding:2.5rem 0 5rem;">
<div class="container">
<div class="row justify-content-center">
<div class="col-xl-8 col-lg-10">

    {{-- BACK --}}
    <a href="{{ route('citizen.services.browse') }}"
       style="display:inline-flex;align-items:center;gap:0.5rem;color:var(--muted);text-decoration:none;font-size:0.98rem;margin-bottom:1.5rem;transition:color 0.2s;"
       onmouseover="this.style.color='var(--navy)'" onmouseout="this.style.color='var(--muted)'">
        <i class="bi bi-arrow-left"></i> {{ __('pages.back_to_services') }}
    </a>

    {{-- SERVICE SUMMARY CARD --}}
    <div style="background:#ffffff;border:1px solid #fde68a;border-radius:16px;padding:1.5rem;margin-bottom:2rem;display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="width:52px;height:52px;background:linear-gradient(135deg,#d1fae5,#fef3c7);border:1px solid #fde68a;border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-file-earmark-text-fill" style="color:var(--gold);font-size:1.3rem;"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <h2 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;color:var(--navy);margin:0 0 0.25rem;">
                {{ $service->display_name }}
            </h2>
            <div style="display:flex;flex-wrap:wrap;gap:0.75rem 1.25rem;font-size:0.92rem;color:var(--muted);min-width:0;">
                <span style="min-width:0;"><i class="bi bi-geo-alt me-1"></i>All Municipalities</span>
                <span style="min-width:0;"><i class="bi bi-geo-alt me-1"></i>{{ $service->office->municipality->name ?? '' }}</span>
                <span style="min-width:0;"><i class="bi bi-clock me-1"></i>~{{ $service->processing_days }} business day{{ $service->processing_days > 1 ? 's' : '' }}</span>
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            @if($service->price == 0)
                <span style="background:#ede9fe;border:1px solid #c4b5fd;color:#5b21b6;font-size:0.92rem;padding:0.35rem 0.95rem;border-radius:20px;font-weight:600;">{{ __('pages.free') }}</span>
            @else
                <div class="price-display"
                     data-currency="{{ $service->currency }}"
                     data-lbp-raw="{{ $service->price }}"
                     style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.55rem;color:var(--gold);">
                    @if($service->currency === 'LBP')
                        ل.ل {{ number_format($service->price, 0) }}
                    @else
                        {{ $service->currency }} {{ number_format($service->price, 2) }}
                    @endif
                </div>
                <div style="font-size:0.88rem;color:var(--muted);">{{ __('pages.service_fee') }}</div>
            @endif
        </div>
    </div>

    {{-- STEP INDICATOR --}}
    <style>
        .step-indicator.step-curr { background:var(--gold); color:#fff; border:none; }
        .step-indicator.step-done { background:var(--emerald); color:#fff; border:none; }
        .step-indicator.step-todo { background:#f1f5f9; color:var(--muted); border:1px solid var(--border); }
        .step-label.step-curr { color:var(--navy); font-weight:600; }
        .step-label.step-todo { color:var(--muted); font-weight:400; }
        @media (max-width: 480px) {
            .step-label { display: none; }
        }
    </style>
    <div style="display:flex;align-items:center;gap:0;margin-bottom:2rem;">
        @foreach([__('pages.step_your_details'), __('pages.step_documents'), __('pages.step_review')] as $i => $stepLabel)
            <div style="display:flex;align-items:center;flex:1;">
                <div class="step-indicator {{ $i === 0 ? 'step-curr' : 'step-todo' }}"
                     data-step="{{ $i + 1 }}"
                     style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.95rem;font-weight:700;flex-shrink:0;">
                    {{ $i + 1 }}
                </div>
                <div class="step-label {{ $i === 0 ? 'step-curr' : 'step-todo' }}"
                     data-step-label="{{ $i + 1 }}"
                     style="margin-left:0.5rem;font-size:0.95rem;">
                    {{ $stepLabel }}
                </div>
                @if($i < 2)
                    <div style="flex:1;height:1px;background:var(--border);margin:0 0.75rem;"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ERRORS --}}
    @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>{{ __('pages.please_fix_following') }}</strong>
            </div>
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('citizen.services.store', $service) }}" method="POST"
          enctype="multipart/form-data" id="applyForm"
          data-error-keys="{{ implode(',', array_keys($errors->toArray())) }}">
        @csrf

        {{-- Hidden coords filled by JS --}}
        <input type="hidden" name="citizen_lat" id="citizen_lat">
        <input type="hidden" name="citizen_lng" id="citizen_lng">
        <input type="hidden" name="office_id"   id="selected_office_id" value="{{ $officesWithService->first()?->id ?? $service->office_id }}">

        {{-- ═══════════ STEP 1 — PERSONAL DETAILS ═══════════ --}}
        <div id="step-1">
            <div style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.75rem;margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div style="width:36px;height:36px;background:#fef3c7;border:1px solid #fde68a;border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-person-fill" style="color:var(--gold);font-size:0.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);">{{ __('pages.personal_details') }}</div>
                        <div style="font-size:0.92rem;color:var(--muted);">{{ __('pages.personal_details_sub') }}</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label style="font-size:0.92rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:0.5rem;">
                            {{ __('pages.full_name') }} <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="full_name" required value="{{ old('full_name', Auth::user()->first_name . ' ' . Auth::user()->last_name) }}"
                               style="background:#ffffff;border:1.5px solid var(--border);color:var(--text);border-radius:9px;padding:0.85rem 1.1rem;font-size:1.05rem;width:100%;outline:none;transition:border-color 0.2s;min-height:48px;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='var(--border)'"
                               placeholder="{{ __('pages.as_on_id') }}">
                        @error('full_name') <div style="font-size:0.88rem;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label style="font-size:0.92rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:0.5rem;">
                            {{ __('pages.phone') }} <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="phone" id="applyPhoneField" required value="{{ old('phone') }}"
                               style="background:#ffffff;border:1.5px solid var(--border);color:var(--text);border-radius:9px;padding:0.85rem 1.1rem;font-size:1.05rem;width:100%;outline:none;transition:border-color 0.2s;min-height:48px;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='var(--border)'"
                               placeholder="+961 XX XXX XXX">
                        <div id="applyPhoneFeedback" style="font-size:0.75rem;margin-top:4px;display:none;"></div>
                        @error('phone') <div style="font-size:0.88rem;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label style="font-size:0.92rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:0.5rem;">
                            {{ __('pages.email') }} <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="email" name="email" required value="{{ old('email', Auth::user()->email ?? '') }}"
                               style="background:#ffffff;border:1.5px solid var(--border);color:var(--text);border-radius:9px;padding:0.85rem 1.1rem;font-size:1.05rem;width:100%;outline:none;transition:border-color 0.2s;min-height:48px;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='var(--border)'"
                               placeholder="your@email.com">
                        @error('email') <div style="font-size:0.88rem;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label style="font-size:0.92rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:0.5rem;">
                            {{ __('pages.home_address') }} <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="address" required value="{{ old('address') }}"
                               style="background:#ffffff;border:1.5px solid var(--border);color:var(--text);border-radius:9px;padding:0.85rem 1.1rem;font-size:1.05rem;width:100%;outline:none;transition:border-color 0.2s;min-height:48px;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='var(--border)'"
                               placeholder="{{ __('pages.street_city') }}">
                        @error('address') <div style="font-size:0.88rem;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label style="font-size:0.92rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:0.5rem;">
                            {{ __('pages.additional_notes') }}
                        </label>
                        <textarea name="notes" rows="3"
                                  style="background:#ffffff;border:1.5px solid var(--border);color:var(--text);border-radius:9px;padding:0.85rem 1.1rem;font-size:1.05rem;width:100%;resize:vertical;outline:none;transition:border-color 0.2s;"
                                  onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='var(--border)'"
                                  placeholder="{{ __('pages.special_instructions') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- MUNICIPALITY SELECTOR --}}
            <div id="locationBox" data-offices='@json($officesJson ?? [])' style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.75rem;margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
                    <div style="width:36px;height:36px;background:#d1fae5;border:1px solid #6ee7b7;border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-geo-alt-fill" style="color:var(--emerald);font-size:0.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);">Select Your Municipality</div>
                        <div style="font-size:0.92rem;color:var(--muted);">Choose the municipality closest to you — it will appear on your certificate.</div>
                    </div>
                </div>

                {{-- Municipality dropdown --}}
                <select id="manual_office_select" required
                        onchange="document.getElementById('selected_office_id').value = this.value"
                        style="background:#ffffff;border:1.5px solid var(--border);color:var(--text);border-radius:9px;padding:0.85rem 1.1rem;font-size:1.05rem;width:100%;outline:none;min-height:48px;margin-bottom:0.75rem;">
                    @foreach($officesWithService as $o)
                        @php $cityLabel = $o->city ?? ($o->municipality->name ?? $o->name); @endphp
                        <option value="{{ $o->id }}"
                                data-lat="{{ $o->latitude }}"
                                data-lng="{{ $o->longitude }}"
                                {{ $loop->first ? 'selected' : '' }}>
                            {{ $cityLabel }}
                        </option>
                    @endforeach
                </select>

                {{-- Optional: auto-detect nearest --}}
                <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                    <button type="button" onclick="detectLocation()"
                            style="background:#f0fdf4;border:1px solid #bbf7d0;color:#047857;font-size:0.88rem;padding:0.45rem 0.95rem;border-radius:7px;cursor:pointer;display:flex;align-items:center;gap:0.4rem;transition:background 0.2s;"
                            onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#f0fdf4'">
                        <i class="bi bi-crosshair2"></i> Detect nearest municipality
                    </button>
                    <div id="nearestOfficeResult" style="display:none;font-size:0.88rem;color:#047857;">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Nearest: <strong id="nearestOfficeName"></strong>
                        <span style="color:var(--muted);" id="nearestOfficeDist"></span>
                    </div>
                </div>
            </div
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" onclick="goToStep(2)"
                        style="background:var(--gold);border:none;color:#fff;font-weight:700;font-size:1.05rem;padding:0.9rem 2.2rem;border-radius:9px;cursor:pointer;display:flex;align-items:center;gap:0.5rem;transition:background 0.2s;min-height:50px;"
                        onmouseover="this.style.background='var(--gold-light)'" onmouseout="this.style.background='var(--gold)'">
                    {{ __('pages.next_upload_documents') }} <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- ═══════════ STEP 2 — DOCUMENTS ═══════════ --}}
        <div id="step-2" style="display:none;">
            <div style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.75rem;margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div style="width:36px;height:36px;background:#ede9fe;border:1px solid #c4b5fd;border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-file-earmark-arrow-up-fill" style="color:#7c3aed;font-size:0.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);">{{ __('pages.upload_documents') }}</div>
                        <div style="font-size:0.92rem;color:var(--muted);">{{ __('pages.upload_documents_sub') }}</div>
                    </div>
                </div>

                @if($service->requiredDocuments->isEmpty())
                    <div style="text-align:center;padding:2rem;color:var(--muted);font-size:1rem;">
                        <i class="bi bi-check-circle-fill" style="color:#047857;font-size:1.5rem;display:block;margin-bottom:0.5rem;"></i>
                        {{ __('pages.no_documents_required') }}
                    </div>
                @else
                    @foreach($service->requiredDocuments as $doc)
                    <div style="background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:1.25rem;margin-bottom:0.875rem;">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
                            <div>
                                <div style="font-weight:600;color:var(--navy);font-size:1.05rem;">
                                    {{ $doc->display_name }}
                                    @if($doc->is_mandatory)
                                        <span style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;font-size:0.8rem;padding:0.15rem 0.6rem;border-radius:4px;margin-left:0.4rem;font-weight:600;">{{ __('pages.required_badge') }}</span>
                                    @else
                                        <span style="background:#f1f5f9;border:1px solid var(--border);color:var(--muted);font-size:0.8rem;padding:0.15rem 0.6rem;border-radius:4px;margin-left:0.4rem;">{{ __('pages.optional_badge') }}</span>
                                    @endif
                                </div>
                                @if($doc->notes)
                                    <div style="font-size:0.9rem;color:var(--muted);margin-top:3px;">
                                        <i class="bi bi-info-circle me-1"></i>{{ $doc->notes }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="file-upload-area" id="upload-{{ $doc->id }}"
                             data-doc-id="{{ $doc->id }}"
                             onclick="document.getElementById('file-' + this.dataset.docId).click()"
                             style="border:2px dashed #cbd5e1;border-radius:9px;padding:1.5rem;text-align:center;cursor:pointer;transition:all 0.2s;background:#ffffff;min-height:48px;"
                             ondragover="this.style.borderColor='var(--gold)';this.style.background='#fffbeb';event.preventDefault();"
                             ondragleave="this.style.borderColor='#cbd5e1';this.style.background='#ffffff'"
                             ondrop="handleDrop(event, this.dataset.docId)">
                            <i class="bi bi-cloud-arrow-up" style="font-size:1.7rem;color:var(--muted);display:block;margin-bottom:0.5rem;"></i>
                            <div id="label-{{ $doc->id }}" style="font-size:0.98rem;color:var(--muted);">
                                {{ __('pages.click_or_drag_drop') }}
                            </div>
                        </div>
                        <input type="file" id="file-{{ $doc->id }}" name="doc_{{ $doc->id }}"
                               data-doc-id="{{ $doc->id }}"
                               accept=".pdf,.jpg,.jpeg,.png" style="display:none;"
                               onchange="previewFile(this, this.dataset.docId)">
                        @error('doc_' . $doc->id)
                            <div style="font-size:0.88rem;color:#dc2626;margin-top:5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    @endforeach
                @endif
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" onclick="goToStep(1)"
                        style="background:#ffffff;border:1px solid var(--border);color:var(--text);font-size:1.05rem;padding:0.85rem 1.7rem;border-radius:9px;cursor:pointer;display:flex;align-items:center;gap:0.5rem;transition:background 0.2s;min-height:50px;"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#ffffff'">
                    <i class="bi bi-arrow-left"></i> {{ __('pages.back') }}
                </button>
                <button type="button" onclick="goToStep(3)"
                        style="background:var(--gold);border:none;color:#fff;font-weight:700;font-size:1.05rem;padding:0.9rem 2.2rem;border-radius:9px;cursor:pointer;display:flex;align-items:center;gap:0.5rem;transition:background 0.2s;min-height:50px;"
                        onmouseover="this.style.background='var(--gold-light)'" onmouseout="this.style.background='var(--gold)'">
                    {{ __('pages.next_review') }} <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- ═══════════ STEP 3 — REVIEW ═══════════ --}}
        <div id="step-3" style="display:none;">
            <div style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.75rem;margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;color:var(--navy);margin-bottom:1.25rem;padding-bottom:0.875rem;border-bottom:1px solid var(--border);">
                    <i class="bi bi-clipboard-check-fill me-2" style="color:var(--gold);"></i> {{ __('pages.review_your_application') }}
                </div>

                {{-- Review: service --}}
                <div style="margin-bottom:1rem;">
                    <div style="font-size:0.85rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.35rem;">{{ __('pages.service_label') }}</div>
                    <div style="color:var(--navy);font-weight:600;font-size:1.05rem;">{{ $service->display_name }}</div>
                    <div style="font-size:0.92rem;color:var(--muted);">
                        {{ __('pages.fee_label') }} {{ $service->price == 0 ? __('pages.free') : $service->currency . ' ' . number_format($service->price, 2) }}
                        · {{ __('pages.processing_label') }} {{ $service->processing_days }} {{ $service->processing_days > 1 ? __('pages.days') : __('pages.day') }}
                    </div>
                </div>

                <div style="border-top:1px solid var(--border);padding-top:1rem;margin-bottom:1rem;">
                    <div style="font-size:0.85rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">{{ __('pages.your_details') }}</div>
                    <div class="row g-2" style="font-size:1rem;">
                        <div class="col-sm-6">
                            <span style="color:var(--muted);">{{ __('pages.name_label') }} </span>
                            <span id="review-name" style="color:var(--navy);font-weight:500;"></span>
                        </div>
                        <div class="col-sm-6">
                            <span style="color:var(--muted);">{{ __('pages.phone_label') }} </span>
                            <span id="review-phone" style="color:var(--navy);font-weight:500;"></span>
                        </div>
                        <div class="col-sm-6">
                            <span style="color:var(--muted);">{{ __('pages.email_label') }} </span>
                            <span id="review-email" style="color:var(--navy);font-weight:500;"></span>
                        </div>
                        <div class="col-sm-6">
                            <span style="color:var(--muted);">{{ __('pages.address_label') }} </span>
                            <span id="review-address" style="color:var(--navy);font-weight:500;"></span>
                        </div>
                        <div class="col-sm-6">
                            <span style="color:var(--muted);">{{ __('pages.office_label') }} </span>
                            <span id="review-office" style="color:var(--navy);font-weight:500;"></span>
                        </div>
                    </div>
                </div>

                <div style="border-top:1px solid var(--border);padding-top:1rem;">
                    <div style="font-size:0.85rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">{{ __('pages.uploaded_documents') }}</div>
                    <div id="review-docs" style="font-size:0.95rem;color:var(--muted);">—</div>
                </div>
            </div>

            {{-- Disclaimer --}}
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1.1rem;margin-bottom:1.5rem;font-size:0.95rem;color:var(--muted);">
                <i class="bi bi-shield-check-fill me-2" style="color:var(--emerald);"></i>
                {{ __('pages.submit_disclaimer') }}
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" onclick="goToStep(2)"
                        style="background:#ffffff;border:1px solid var(--border);color:var(--text);font-size:1.05rem;padding:0.85rem 1.7rem;border-radius:9px;cursor:pointer;display:flex;align-items:center;gap:0.5rem;transition:background 0.2s;min-height:50px;"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#ffffff'">
                    <i class="bi bi-arrow-left"></i> {{ __('pages.back') }}
                </button>
                <button type="submit" id="submitBtn"
                        style="background:var(--emerald);border:none;color:#fff;font-weight:700;font-size:1.05rem;padding:0.9rem 2.6rem;border-radius:9px;cursor:pointer;display:flex;align-items:center;gap:0.5rem;min-height:50px;">
                    <i class="bi bi-send-fill"></i> {{ __('pages.submit_application') }}
                </button>
            </div>
        </div>

    </form>
</div>
</div>
</div>
</div>

@endsection

@push('scripts')
<script>
// ── REAL-TIME PHONE VALIDATION (mirrors server-side LebanesePhoneNumber rule) ──
const applyPhoneField = document.getElementById('applyPhoneField');
const applyPhoneFeedback = document.getElementById('applyPhoneFeedback');
if (applyPhoneField) {
    applyPhoneField.addEventListener('input', () => {
        const v = applyPhoneField.value.trim();
        if (v === '') { applyPhoneFeedback.style.display = 'none'; applyPhoneField.style.borderColor = 'var(--border)'; return; }
        const normalized = v.replace(/[\s\-\(\)]+/g, '');
        const ok = /^(?:\+?961|00961)?0?(3\d{6}|7[01689]\d{6}|81\d{6}|[1456789]\d{6,7})$/.test(normalized);
        applyPhoneFeedback.style.display = 'block';
        applyPhoneFeedback.style.color = ok ? '#059669' : '#dc2626';
        applyPhoneFeedback.textContent = ok ? 'Looks good.' : 'Enter a valid Lebanese number, e.g. +961 70 123 456.';
        applyPhoneField.style.borderColor = ok ? '#059669' : '#dc2626';
    });
}

// ── STEP NAVIGATION ──
function goToStep(n) {
    [1,2,3].forEach(i => {
        document.getElementById('step-' + i).style.display = i === n ? 'block' : 'none';
        const ind = document.querySelector(`.step-indicator[data-step="${i}"]`);
        const lbl = document.querySelector(`.step-label[data-step-label="${i}"]`);
        if (ind) {
            ind.classList.remove('step-curr', 'step-done', 'step-todo');
            ind.classList.add(i === n ? 'step-curr' : (i < n ? 'step-done' : 'step-todo'));
        }
        if (lbl) {
            lbl.classList.remove('step-curr', 'step-todo');
            lbl.classList.add(i <= n ? 'step-curr' : 'step-todo');
        }
    });

    if (n === 3) populateReview();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── REVIEW POPULATION ──
function populateReview() {
    document.getElementById('review-name').textContent    = document.querySelector('[name=full_name]').value || '—';
    document.getElementById('review-phone').textContent   = document.querySelector('[name=phone]').value    || '—';
    document.getElementById('review-email').textContent   = document.querySelector('[name=email]').value    || '—';
    document.getElementById('review-address').textContent = document.querySelector('[name=address]').value  || '—';
    const sel = document.getElementById('manual_office_select');
    document.getElementById('review-office').textContent  = sel ? sel.options[sel.selectedIndex]?.text.split('—')[0].trim() : '—';

    // Uploaded docs
    const docContainer = document.getElementById('review-docs');
    const fileInputs    = document.querySelectorAll('input[type=file][name^=doc_]');
    const docItems      = [];
    fileInputs.forEach(inp => {
        if (inp.files && inp.files[0]) {
            docItems.push(`<span style="display:inline-flex;align-items:center;gap:0.35rem;background:#d1fae5;border:1px solid #6ee7b7;border-radius:6px;padding:0.2rem 0.6rem;font-size:0.75rem;color:#047857;margin:2px;"><i class="bi bi-file-check-fill"></i>${inp.files[0].name}</span>`);
        }
    });
    docContainer.innerHTML = docItems.length ? docItems.join('') : '<span style="color:var(--muted);">No documents uploaded</span>';
}

// ── FILE PREVIEW ──
function previewFile(input, docId) {
    const label = document.getElementById('label-' + docId);
    const area   = document.getElementById('upload-' + docId);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        label.innerHTML = `<i class="bi bi-file-check-fill" style="color:#047857;"></i> <strong style="color:#047857;">${file.name}</strong> <span style="color:var(--muted);">(${(file.size/1024).toFixed(1)} KB)</span>`;
        area.style.borderColor = '#6ee7b7';
        area.style.background  = '#f0fdf4';
    }
}

function handleDrop(event, docId) {
    event.preventDefault();
    const input = document.getElementById('file-' + docId);
    const dt = new DataTransfer();
    Array.from(event.dataTransfer.files).forEach(f => dt.items.add(f));
    input.files = dt.files;
    previewFile(input, docId);
    document.getElementById('upload-' + docId).style.borderColor = '#cbd5e1';
    document.getElementById('upload-' + docId).style.background  = '#ffffff';
}

// ── NEAREST OFFICE DETECTION ──
const officesData = JSON.parse(document.getElementById('locationBox').dataset.offices || '[]');

function detectLocation() {
    const status = document.getElementById('locationStatus');
    status.innerHTML = '<i class="bi bi-arrow-clockwise spin me-1"></i> Detecting your location…';

    if (!navigator.geolocation) {
        status.innerHTML = '<i class="bi bi-exclamation-triangle me-1" style="color:#dc2626;"></i> Geolocation not supported by your browser.';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            document.getElementById('citizen_lat').value = lat;
            document.getElementById('citizen_lng').value = lng;
            status.innerHTML = `<i class="bi bi-geo-fill me-1" style="color:#047857;"></i> Location detected (${lat.toFixed(4)}, ${lng.toFixed(4)})`;

            // Find nearest among offices with coordinates
            const withCoords = officesData.filter(o => o.lat && o.lng);
            if (withCoords.length > 0) {
                let nearest = null, minDist = Infinity;
                withCoords.forEach(o => {
                    const d = haversine(lat, lng, parseFloat(o.lat), parseFloat(o.lng));
                    if (d < minDist) { minDist = d; nearest = o; }
                });
                if (nearest) {
                    document.getElementById('selected_office_id').value = nearest.id;
                    document.getElementById('manual_office_select').value = nearest.id;
                    document.getElementById('nearestOfficeName').textContent = nearest.name;
                    document.getElementById('nearestOfficeDist').textContent = ` (${minDist.toFixed(1)} km away)`;
                    document.getElementById('nearestOfficeResult').style.display = 'block';
                }
            }
        },
        err => {
            status.innerHTML = '<i class="bi bi-exclamation-triangle me-1" style="color:#d97706;"></i> Could not get location. Please select manually.';
        }
    );
}

function haversine(lat1, lng1, lat2, lng2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLng/2)**2;
    return R * 2 * Math.asin(Math.sqrt(a));
}

// Spinner animation
const style = document.createElement('style');
style.textContent = '.spin { animation: spin 1s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(style);

// On validation error reload: jump to the step that has errors
const __errorKeys = (document.getElementById('applyForm').dataset.errorKeys || '').split(',').filter(Boolean);
if (__errorKeys.some(k => k.startsWith('doc_'))) {
    goToStep(2);
}

document.getElementById('applyForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting…';
    setTimeout(() => { btn.disabled = true; }, 0);
});
</script>
@endpush
