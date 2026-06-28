@extends('layouts.app')

@section('title', 'Application Submitted')

@section('content')

<div style="position:relative;z-index:1;padding:4rem 0 6rem;min-height:70vh;display:flex;align-items:center;">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-7 text-center">

    {{-- SUCCESS ICON --}}
    <div style="width:80px;height:80px;background:#d1fae5;border:2px solid #6ee7b7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
        <i class="bi bi-check-lg" style="font-size:2.2rem;color:#047857;"></i>
    </div>

    <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:2rem;color:var(--navy);margin-bottom:0.75rem;">
        {{ __('pages.application_submitted') }}
    </h1>
    <p style="color:var(--muted);font-size:0.95rem;margin-bottom:2rem;line-height:1.7;">
        {{ __('pages.application_submitted_body') }}<br>
        {{ __('pages.will_contact_you_at') }} <strong style="color:var(--navy);">{{ $application->email }}</strong>.
    </p>

    {{-- REFERENCE CARD --}}
    <div style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.5rem;margin-bottom:2rem;text-align:left;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="text-align:center;margin-bottom:1.25rem;">
            <div style="font-size:0.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.4rem;">{{ __('pages.reference_number') }}</div>
            <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:var(--gold);letter-spacing:0.05em;">
                {{ $application->reference_number }}
            </div>
            <div style="font-size:0.75rem;color:var(--muted);margin-top:0.25rem;">{{ __('pages.keep_number_for_tracking') }}</div>
        </div>

        <div style="border-top:1px solid var(--border);padding-top:1.25rem;">
            <div class="row g-3" style="font-size:0.85rem;">
                <div class="col-sm-6">
                    <div style="color:var(--muted);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ __('pages.service_label') }}</div>
                    <div style="color:var(--navy);font-weight:600;">{{ $application->service->display_name }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:var(--muted);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ __('pages.assigned_office') }}</div>
                    <div style="color:var(--navy);font-weight:600;">{{ $application->office->name }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:var(--muted);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ __('pages.submitted') }}</div>
                    <div style="color:var(--text);">{{ $application->submitted_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:var(--muted);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">{{ __('app.status') }}</div>
                    <span style="background:#fef3c7;border:1px solid #fde68a;color:#92400e;font-size:0.75rem;padding:0.2rem 0.75rem;border-radius:20px;font-weight:600;">
                        {{ __('pages.pending_review') }}
                    </span>
                </div>
                @if($application->documents->count() > 0)
                <div class="col-12">
                    <div style="color:var(--muted);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">{{ __('pages.uploaded_documents') }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:0.4rem;">
                        @foreach($application->documents as $doc)
                            <span style="background:#d1fae5;border:1px solid #6ee7b7;color:#047857;font-size:0.75rem;padding:0.2rem 0.65rem;border-radius:6px;">
                                <i class="bi bi-file-check me-1"></i>{{ $doc->document_name }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- WHAT'S NEXT --}}
    <div style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.5rem;margin-bottom:2rem;text-align:left;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="font-family:'Syne',sans-serif;font-weight:700;color:var(--navy);font-size:0.95rem;margin-bottom:1rem;">{{ __('pages.whats_next') }}</div>
        <div class="d-flex flex-column gap-3">
            @foreach([
                ['bi-1-circle-fill', 'var(--gold)', __('pages.next_review_title'), __('pages.next_review_desc')],
                ['bi-2-circle-fill', '#7c3aed', __('pages.next_processing_title'), str_replace(':days', $application->service->processing_days, __('pages.next_processing_desc'))],
                ['bi-3-circle-fill', 'var(--emerald)', __('pages.next_notification_title'), __('pages.next_notification_desc')],
            ] as [$icon, $color, $title, $desc])
            <div class="d-flex gap-3 align-items-start">
                <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:1.1rem;flex-shrink:0;margin-top:1px;"></i>
                <div>
                    <div style="font-weight:600;color:var(--navy);font-size:0.875rem;">{{ $title }}</div>
                    <div style="color:var(--muted);font-size:0.8rem;">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ route('citizen.my-requests') }}"
           style="background:var(--gold);border:none;color:#fff;font-weight:700;font-size:0.875rem;padding:0.75rem 1.75rem;border-radius:9px;text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;transition:background 0.2s;"
           onmouseover="this.style.background='var(--gold-light)'"
           onmouseout="this.style.background='var(--gold)'">
            <i class="bi bi-folder2-open"></i> {{ __('app.nav_my_requests') }}
        </a>
        <a href="{{ route('citizen.services.browse') }}"
           style="background:#ffffff;border:1px solid var(--border);color:var(--text);font-size:0.875rem;padding:0.75rem 1.75rem;border-radius:9px;text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;transition:all 0.2s;"
           onmouseover="this.style.background='#f1f5f9'"
           onmouseout="this.style.background='#ffffff'">
            <i class="bi bi-grid"></i> {{ __('pages.browse_more_services') }}
        </a>
    </div>

</div>
</div>
</div>
</div>

@end