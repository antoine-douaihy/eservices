@extends('admin.layouts.app')

@section('title', 'Citizen Ratings')
@section('page-title', 'Citizen Feedback & Ratings')

@section('content')

{{-- Header + average --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0">
            Citizen Feedback
        </h1>
        <p style="color:var(--muted);font-size:.875rem;margin:.25rem 0 0">
            Ratings and comments left by citizens on completed requests
        </p>
    </div>
    @if($ratings->count())
        <div class="admin-card px-4 py-3 text-center">
            <div style="font-size:1.8rem;font-weight:800;color:var(--gold);line-height:1">
                {{ number_format($avgStars, 1) }}
                <span style="font-size:1.1rem">/ 5</span>
            </div>
            <div style="color:var(--muted);font-size:.8rem;margin-top:.25rem">
                Average · {{ $ratings->count() }} {{ Str::plural('review', $ratings->count()) }}
            </div>
            <div style="color:var(--gold);font-size:1rem;margin-top:.25rem">
                @for($i=1;$i<=5;$i++)
                    <i class="bi bi-star{{ $i <= round($avgStars) ? '-fill' : '' }}"></i>
                @endfor
            </div>
        </div>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="font-size:.875rem">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($ratings->isEmpty())
    <div class="admin-card p-5 text-center">
        <i class="bi bi-star" style="font-size:3rem;color:var(--muted);opacity:.4"></i>
        <p class="mt-3 mb-0" style="color:var(--muted)">No ratings yet.</p>
    </div>
@else
    <div class="d-flex flex-column gap-3">
        @foreach($ratings as $rating)
            <div class="admin-card p-4">
                <div class="row g-3 align-items-start">
                    {{-- Left: rating info --}}
                    <div class="col-md-8">
                        {{-- Stars + meta --}}
                        <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">
                            <div style="color:var(--gold);font-size:1.1rem">
                                @for($i=1;$i<=5;$i++)
                                    <i class="bi bi-star{{ $i <= $rating->stars ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <span style="color:var(--text);font-weight:600;font-size:.95rem">
                                {{ $rating->user->first_name }} {{ $rating->user->last_name }}
                            </span>
                            <span style="color:var(--muted);font-size:.8rem">
                                {{ $rating->created_at->format('d M Y · H:i') }}
                            </span>
                        </div>

                        {{-- Comment --}}
                        @if($rating->comment)
                            <p style="color:var(--text);font-size:.9rem;margin-bottom:.75rem">
                                "{{ $rating->comment }}"
                            </p>
                        @endif

                        {{-- Linked request --}}
                        <div style="font-size:.8rem;color:var(--muted)">
                            Request:
                            <span style="color:var(--gold);font-weight:600">
                                {{ $rating->citizenRequest->reference_number ?? '—' }}
                            </span>
                            @if($rating->citizenRequest->service)
                                · {{ $rating->citizenRequest->service->name }}
                            @endif
                        </div>

                        {{-- Existing office response --}}
                        @if($rating->office_response)
                            <div class="mt-3 p-3 rounded-3" style="background:rgba(4,120,87,.12);border-left:3px solid var(--emerald)">
                                <div style="font-size:.75rem;color:var(--emerald);font-weight:700;margin-bottom:.4rem">
                                    <i class="bi bi-building me-1"></i>OFFICE RESPONSE
                                    <span style="color:var(--muted);font-weight:400"> · {{ $rating->responded_at?->format('d M Y') }}</span>
                                </div>
                                <p style="color:var(--text);font-size:.875rem;margin:0">{{ $rating->office_response }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Right: respond button --}}
                    <div class="col-md-4 text-md-end">
                        @if(!$rating->office_response)
                            <button
                                class="btn btn-emerald btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#respond-modal-{{ $rating->id }}"
                            >
                                <i class="bi bi-reply me-1"></i> Respond
                            </button>
                        @else
                            <button
                                class="btn btn-ghost btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#respond-modal-{{ $rating->id }}"
                            >
                                <i class="bi bi-pencil me-1"></i> Edit Response
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Respond modal --}}
            <div class="modal fade" id="respond-modal-{{ $rating->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background:#1a2942;border:1px solid var(--border);color:var(--text)">
                        <div class="modal-header" style="border-color:var(--border)">
                            <h6 class="modal-title fw-bold" style="color:var(--gold)">
                                <i class="bi bi-reply me-1"></i> Respond to Feedback
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('office.ratings.respond', $rating) }}">
                            @csrf
                            @method('POST')
                            <div class="modal-body">
                                {{-- Original comment summary --}}
                                <div class="p-3 rounded-3 mb-3" style="background:rgba(255,255,255,.05)">
                                    <div style="color:var(--gold);font-size:.8rem;margin-bottom:.4rem">
                                        @for($i=1;$i<=5;$i++)
                                            <i class="bi bi-star{{ $i <= $rating->stars ? '-fill' : '' }}"></i>
                                        @endfor
                                        <span style="color:var(--muted);margin-left:.5rem">{{ $rating->user->first_name }}</span>
                                    </div>
                                    @if($rating->comment)
                                        <p style="font-size:.875rem;color:var(--text);margin:0">"{{ $rating->comment }}"</p>
                                    @else
                                        <p style="font-size:.875rem;color:var(--muted);margin:0;font-style:italic">No comment left.</p>
                                    @endif
                                </div>

                                <label class="form-label-custom">Your Public Response</label>
                                <textarea
                                    name="office_response"
                                    class="form-control-custom"
                                    rows="4"
                                    maxlength="1000"
                                    placeholder="Write a professional, helpful response that will be visible to the citizen…"
                                    required
                                >{{ $rating->office_response }}</textarea>
                                <div style="font-size:.75rem;color:var(--muted);margin-top:.4rem">
                                    Max 1000 characters. This response is public.
                                </div>
                            </div>
                            <div class="modal-footer" style="border-color:var(--border)">
                                <button type="button" class="btn btn-ghost btn-sm" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-emerald btn-sm">
                                    <i class="bi bi-send me-1"></i> Publish Response
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
