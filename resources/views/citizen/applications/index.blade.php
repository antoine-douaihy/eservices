@extends('layouts.app')

@section('title', 'My Applications')

@section('content')

<style>
.status-badge{font-size:0.72rem;padding:0.2rem 0.75rem;border-radius:20px;font-weight:600;display:inline-block;}
.status-badge.status-pending   {background:#fef3c7;border:1px solid #fde68a;color:#92400e;}
.status-badge.status-reviewing {background:#ede9fe;border:1px solid #c4b5fd;color:#5b21b6;}
.status-badge.status-approved  {background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;}
.status-badge.status-rejected  {background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;}
.status-badge.status-completed {background:#d1fae5;border:1px solid #6ee7b7;color:#047857;}
</style>

<div style="position:relative;z-index:1;padding:2rem 0 4rem;">
<div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.8rem;color:var(--navy);margin:0;">
                {{ __('pages.my_applications') }}
            </h1>
            <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
                {{ app()->getLocale() === 'ar' ? 'تابع حالة جميع طلبات الخدمة التي قدمتها.' : 'Track the status of all your submitted service requests.' }}
            </p>
        </div>
        <a href="{{ route('citizen.services.browse') }}"
           style="background:var(--gold);border:none;color:#fff;font-weight:700;font-size:0.875rem;padding:0.65rem 1.5rem;border-radius:9px;text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;transition:background 0.2s;"
           onmouseover="this.style.background='var(--gold-light)'"
           onmouseout="this.style.background='var(--gold)'">
            <i class="bi bi-plus-lg"></i> {{ __('pages.new_application') }}
        </a>
    </div>

    @if($applications->isEmpty())
        <div style="text-align:center;padding:5rem 2rem;background:#ffffff;border:1px solid var(--border);border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <i class="bi bi-inbox" style="font-size:3rem;color:var(--muted);opacity:0.4;"></i>
            <p style="color:var(--muted);margin-top:1rem;">{{ app()->getLocale() === 'ar' ? 'لم تقدّم أي طلبات بعد.' : "You haven't submitted any applications yet." }}</p>
            <a href="{{ route('citizen.services.browse') }}"
               style="background:var(--gold);border:none;color:#fff;font-weight:700;font-size:0.875rem;padding:0.65rem 1.5rem;border-radius:9px;text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;margin-top:1rem;">
                {{ __('pages.browse_services') }}
            </a>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($applications as $app)
            <div style="background:#ffffff;border:1px solid var(--border);border-radius:14px;padding:1.5rem;transition:border-color 0.2s,box-shadow 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.06);"
                 onmouseover="this.style.borderColor='#93c5fd';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
                 onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)'">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div style="flex:1;">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span style="font-family:'Syne',sans-serif;font-weight:700;color:var(--navy);font-size:1rem;">
                                {{ $app->service->display_name }}
                            </span>
                            @php
                                $statusLabels = app()->getLocale() === 'ar' ? [
                                    'pending'   => 'قيد الانتظار',
                                    'reviewing' => 'قيد المراجعة',
                                    'approved'  => 'مقبول',
                                    'rejected'  => 'مرفوض',
                                    'completed' => 'مكتمل',
                                ] : [
                                    'pending'   => 'Pending',
                                    'reviewing' => 'Reviewing',
                                    'approved'  => 'Approved',
                                    'rejected'  => 'Rejected',
                                    'completed' => 'Completed',
                                ];
                            @endphp
                            <span class="status-badge status-{{ $app->status }}">
                                {{ $statusLabels[$app->status] ?? ucfirst($app->status) }}
                            </span>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:1.25rem;font-size:0.78rem;color:var(--muted);margin-top:0.4rem;">
                            <span><i class="bi bi-hash me-1"></i>{{ $app->reference_number }}</span>
                            <span><i class="bi bi-building me-1"></i>{{ $app->office->name }}</span>
                            <span><i class="bi bi-calendar3 me-1"></i>{{ $app->submitted_at?->format('d M Y') ?? $app->created_at->format('d M Y') }}</span>
                        </div>
                        @if($app->rejection_reason)
                            <div style="margin-top:0.75rem;background:#fee2e2;border:1px solid #fca5a5;border-radius:7px;padding:0.5rem 0.875rem;font-size:0.8rem;color:#991b1b;">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $app->rejection_reason }}
                            </div>
                        @endif
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        @if($app->service->price > 0)
                            <div style="font-family:'Syne',sans-serif;font-weight:700;color:var(--gold);font-size:1.1rem;">
                                {{ $app->service->currency }} {{ number_format($app->service->price, 2) }}
                            </div>
                        @else
                            <span style="background:#ede9fe;border:1px solid #c4b5fd;color:#5b21b6;font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">{{ __('pages.free') }}</span>
                        @endif
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">
                            {{ $app->documents->count() }} {{ app()->getLocale() === 'ar' ? 'مستند مرفوع' : 'doc' . ($app->documents->count() !== 1 ? 's' : '') . ' uploaded' }}
                        </div>
                        @if($app->status === 'pending' && $app->service->price > 0 && $app->citizen_request_id)
                            <a href="{{ route('citizen.payment.select', $app->citizenRequest) }}"
                               style="background:var(--gold);border:none;color:#fff;font-weight:700;font-size:0.75rem;padding:0.4rem 0.8rem;border-radius:6px;text-decoration:none;display:inline-block;margin-top:0.5rem;">
                                {{ __('pages.pay_now') }}
                            </a>
                        @elseif($app->status === 'completed' && $app->certificate_path && $app->citizenRequest)
                            <a href="{{ route('requests.certificate', $app->citizenRequest) }}" target="_blank"
                               style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;font-weight:700;font-size:0.75rem;padding:0.4rem 0.8rem;border-radius:6px;text-decoration:none;display:inline-block;margin-top:0.5rem;">
                                <i class="bi bi-download me-1"></i>{{ app()->getLocale() === 'ar' ? 'تحميل' : 'Download' }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($applications->hasPages())
            <div class="d-flex justify-content-center gap-2 mt-4">
                @if(!$applications->onFirstPage())
                    <a href="{{ $applications->previousPageUrl() }}"
                       style="background:#ffffff;border:1px solid var(--border);color:var(--text);padding:0.5rem 1rem;border-radius:8px;text-decoration:none;font-size:0.875rem;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                @endif
                <span style="background:var(--navy);color:#fff;padding:0.5rem 1.25rem;border-radius:8px;font-size:0.875rem;font-weight:600;">
                    {{ $applications->currentPage() }} / {{ $applications->lastPage() }}
                </span>
                @if($applications->hasMorePages())
                    <a href="{{ $applications->nextPageUrl() }}"
                       style="background:#ffffff;border:1px solid var(--border);color:var(--text);padding:0.5rem 1rem;border-radius:8px;text-decoration:none;font-size:0.875rem;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif
            </div>
        @endi