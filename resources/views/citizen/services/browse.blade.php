@extends('layouts.app')

@section('title', 'Available Services')

@section('content')

<div style="position:relative;z-index:1;padding:2rem 0 4rem;">
<div class="container">

    {{-- HEADER --}}
    <div class="text-center mb-5">
        <div style="display:inline-flex;align-items:center;gap:0.5rem;background:#d1fae5;border:1px solid #6ee7b7;border-radius:20px;padding:0.35rem 1rem;font-size:0.78rem;color:#047857;font-weight:600;letter-spacing:0.05em;margin-bottom:1rem;">
            <i class="bi bi-grid-fill"></i> {{ __('pages.service_catalog') }}
        </div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:2.5rem;color:var(--navy);margin-bottom:0.75rem;">
            {{ __('pages.what_can_we_help') }}
        </h1>
        <p style="color:var(--muted);font-size:1.15rem;max-width:560px;margin:0 auto;">
            {{ __('pages.browse_all_govt_services') }}
        </p>
    </div>

    {{-- SEARCH & FILTER --}}
    <div style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.25rem;margin-bottom:2.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <form method="GET" action="{{ route('citizen.services.browse') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <div style="position:relative;">
                        <i class="bi bi-search" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               style="background:#ffffff;border:1.5px solid var(--border);color:var(--text);border-radius:9px;padding:0.85rem 1rem 0.85rem 2.85rem;font-size:1.05rem;width:100%;outline:none;transition:border-color 0.2s;min-height:48px;"
                               onfocus="this.style.borderColor='#2563eb'"
                               onblur="this.style.borderColor='var(--border)'"
                               placeholder="{{ __('pages.search_services_placeholder') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="office_id"
                            style="background:#ffffff;border:1.5px solid var(--border);color:var(--text);border-radius:9px;padding:0.85rem 1rem;font-size:1.05rem;width:100%;outline:none;min-height:48px;">
                        <option value="">{{ __('pages.all_offices') }}</option>
                        @foreach($offices as $o)
                            <option value="{{ $o->id }}" {{ request('office_id') == $o->id ? 'selected' : '' }}>
                                {{ $o->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit"
                            style="background:var(--gold);border:none;color:#fff;font-weight:600;font-size:1.05rem;padding:0.85rem 1.4rem;border-radius:9px;cursor:pointer;flex:1;transition:background 0.2s;min-height:48px;"
                            onmouseover="this.style.background='var(--gold-light)'"
                            onmouseout="this.style.background='var(--gold)'">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                    @if(request()->hasAny(['search','office_id']))
                        <a href="{{ route('citizen.services.browse') }}"
                           style="background:#ffffff;border:1px solid var(--border);color:var(--muted);font-size:1.05rem;padding:0.85rem 1.1rem;border-radius:9px;display:flex;align-items:center;text-decoration:none;transition:all 0.2s;min-height:48px;"
                           onmouseover="this.style.background='#f1f5f9'"
                           onmouseout="this.style.background='#ffffff'">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- RESULTS --}}
    @if($services->isEmpty())
        <div style="text-align:center;padding:5rem 2rem;background:#ffffff;border:1px solid var(--border);border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <i class="bi bi-search" style="font-size:3rem;color:var(--muted);opacity:0.4;"></i>
            <p style="color:var(--muted);margin-top:1rem;">{{ __('pages.no_services_found') }}</p>
            <a href="{{ route('citizen.services.browse') }}"
               style="background:var(--gold);border:none;color:#fff;font-weight:600;font-size:0.875rem;padding:0.6rem 1.5rem;border-radius:9px;text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;margin-top:1rem;">
                {{ __('pages.clear_filters') }}
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($services as $service)
            <div class="col-lg-4 col-md-6">
                <div style="background:#ffffff;border:1px solid var(--border);border-radius:16px;padding:1.5rem;height:100%;display:flex;flex-direction:column;transition:border-color 0.2s,transform 0.2s,box-shadow 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.06);"
                     onmouseover="this.style.borderColor='#93c5fd';this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'"
                     onmouseout="this.style.borderColor='var(--border)';this.style.transform='none';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)'">

                    {{-- Service icon + status --}}
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#d1fae5,#fef3c7);border:1px solid #fde68a;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-file-earmark-text-fill" style="color:var(--gold);font-size:1.2rem;"></i>
                        </div>
                        @if($service->price == 0)
                            <span style="background:#ede9fe;border:1px solid #c4b5fd;color:#5b21b6;font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">
                                {{ __('pages.free') }}
                            </span>
                        @else
                            <span class="price-display"
                                  data-currency="{{ $service->currency }}"
                                  data-lbp-raw="{{ $service->price }}"
                                  style="background:#fef3c7;border:1px solid #fde68a;color:#92400e;font-size:0.78rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:700;">
                                @if($service->currency === 'LBP')
                                    ل.ل {{ number_format($service->price, 0) }}
                                @else
                                    {{ $service->currency }} {{ number_format($service->price, 2) }}
                                @endif
                            </span>
                        @endif
                    </div>

                    {{-- Name & description --}}
                    <h3 style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.05rem;color:var(--navy);margin-bottom:0.5rem;">
                        {{ $service->display_name }}
                    </h3>
                    @if($service->display_description)
                        <p style="color:var(--muted);font-size:0.95rem;line-height:1.6;flex:1;margin-bottom:1rem;">
                            {{ Str::limit($service->display_description, 90) }}
                        </p>
                    @else
                        <div style="flex:1;"></div>
                    @endif

                    {{-- Meta --}}
                    <div style="display:flex;gap:1rem;margin-bottom:1.25rem;font-size:0.9rem;color:var(--muted);">
                        <span><i class="bi bi-geo-alt me-1"></i>{{ __('pages.all_municipalities') }}</span>
                        <span><i class="bi bi-clock me-1"></i>{{ $service->processing_days }} {{ $service->processing_days > 1 ? __('pages.days') : __('pages.day') }}</span>
                    </div>

                    {{-- Documents needed --}}
                    @if($service->requiredDocuments->count() > 0)
                        <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:0.8rem 1rem;margin-bottom:1.25rem;">
                            <div style="font-size:0.85rem;color:var(--muted);margin-bottom:0.5rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">
                                {{ __('pages.documents_required') }}
                            </div>
                            @foreach($service->requiredDocuments->take(3) as $doc)
                                <div style="font-size:0.9rem;color:var(--text);display:flex;align-items:center;gap:0.4rem;margin-bottom:4px;">
                                    <i class="bi bi-{{ $doc->is_mandatory ? 'check-circle-fill' : 'circle' }}"
                                       style="color:{{ $doc->is_mandatory ? '#047857' : 'var(--muted)' }};font-size:0.78rem;"></i>
                                    {{ $doc->display_name }}
                                    @if(!$doc->is_mandatory)
                                        <span style="color:var(--muted);font-size:0.82rem;">({{ __('pages.optional') }})</span>
                                    @endif
                                </div>
                            @endforeach
                            @if($service->requiredDocuments->count() > 3)
                                <div style="font-size:0.85rem;color:var(--muted);margin-top:2px;">
                                    +{{ $service->requiredDocuments->count() - 3 }} {{ __('pages.more') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- CTA --}}
                    @auth
                        <a href="{{ route('citizen.services.apply', $service) }}"
                           style="background:var(--gold);border:none;color:#fff;font-weight:700;font-size:1rem;padding:0.85rem 1rem;border-radius:9px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;min-height:48px;"
                           onmouseover="this.style.background='var(--gold-light)'"
                           onmouseout="this.style.background='var(--gold)'">
                            <i class="bi bi-send-fill"></i> {{ __('pages.apply_now') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           style="background:#f8fafc;border:1px solid var(--border);color:var(--muted);font-weight:600;font-size:1rem;padding:0.85rem 1rem;border-radius:9px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:0.5rem;min-height:48px;">
                            <i class="bi bi-lock-fill"></i> {{ __('pages.login_to_apply') }}
                        </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($services->hasPages())
            <div class="d-flex justify-content-center gap-2 mt-5">
                @if(!$services->onFirstPage())
                    <a href="{{ $services->previousPageUrl() }}"
                       style="background:#ffffff;border:1px solid var(--border);color:var(--text);padding:0.5rem 1rem;border-radius:8px;text-decoration:none;font-size:0.875rem;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                        <i class="bi bi-chevron-left"></i> {{ __('pages.prev') }}
                    </a>
                @endif
                <span style="background:var(--navy);color:#fff;padding:0.5rem 1.25rem;border-radius:8px;font-size:0.875rem;font-weight:600;">
                    {{ $services->currentPage() }} / {{ $services->lastPage() }}
                </span>
                @if($services->hasMorePages())
                    <a href="{{ $services->nextPageUrl() }}"
                       style="background:#ffffff;border:1px solid var(--border);color:var(--text);padding:0.5rem 1rem;border-radius:8px;text-decoration:none;font-size:0.875rem;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                        {{ __('pages.next_word') }} <i class="bi bi-chevron-right"></i>
                    </a>
                @endif
            </div>
        @endif
    @endif

</div>
</div>

@endsection