@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="container py-5" style="max-width:900px">

    <div class="mb-4">
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:var(--navy);margin:0">
            {{ __('app.nav_my_appointments') }}
        </h1>
        <p style="color:var(--muted);font-size:.875rem;margin:.35rem 0 0">
            {{ app()->getLocale() === 'ar' ? 'مواعيد حضورية حددتها الدائرة لطلباتك' : 'In-person appointments scheduled by the office for your requests' }}
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="font-size:.875rem">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($appointments->isEmpty())
        <div class="app-card p-5 text-center">
            <i class="bi bi-calendar-x" style="font-size:3rem;color:var(--muted);opacity:.4"></i>
            <p class="mt-3 mb-0" style="color:var(--muted)">{{ app()->getLocale() === 'ar' ? 'لا توجد مواعيد مجدولة بعد.' : 'No appointments scheduled yet.' }}</p>
            <a href="{{ route('home') }}" class="btn btn-gold mt-3">
                <i class="bi bi-grid me-1"></i> {{ __('pages.browse_services') }}
            </a>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($appointments as $appt)
                @php
                    $isPast   = $appt->scheduled_at->isPast() && $appt->status !== 'completed';
                    $isToday  = $appt->scheduled_at->isToday();
                    $statusColor = [
                        'pending'   => '#f59e0b',
                        'confirmed' => '#10b981',
                        'cancelled' => '#ef4444',
                        'completed' => '#6366f1',
                    ][$appt->status] ?? '#94a3b8';
                    $apptStatusLabels = app()->getLocale() === 'ar' ? [
                        'pending' => 'قيد الانتظار', 'confirmed' => 'مؤكد', 'cancelled' => 'ملغى', 'completed' => 'مكتمل',
                    ] : [
                        'pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled', 'completed' => 'Completed',
                    ];
                @endphp
                <div class="app-card p-4" style="border-left:3px solid {{ $statusColor }}">
                    <div class="row g-3 align-items-start">
                        {{-- Date block --}}
                        <div class="col-auto">
                            <div class="text-center p-3 rounded-3" style="background:#f8fafc;border:1px solid var(--border);min-width:70px">
                                <div style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em">
                                    {{ $appt->scheduled_at->format('M') }}
                                </div>
                                <div style="font-size:1.8rem;font-weight:800;color:var(--navy);line-height:1.1">
                                    {{ $appt->scheduled_at->format('d') }}
                                </div>
                                <div style="font-size:.75rem;color:var(--muted)">
                                    {{ $appt->scheduled_at->format('Y') }}
                                </div>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h6 class="mb-0 fw-bold" style="color:var(--navy);font-size:1rem">{{ $appt->title }}</h6>
                                <span class="badge" style="background:{{ $statusColor }}22;color:{{ $statusColor }};border:1px solid {{ $statusColor }}44">
                                    {{ $apptStatusLabels[$appt->status] ?? ucfirst($appt->status) }}
                                </span>
                                @if($isToday)
                                    <span class="badge bg-warning text-dark">{{ app()->getLocale() === 'ar' ? 'اليوم' : 'Today' }}</span>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap gap-3 mt-2" style="font-size:.85rem;color:var(--muted)">
                                <span><i class="bi bi-clock me-1"></i>{{ $appt->scheduled_at->format('H:i') }}</span>
                                <span><i class="bi bi-hourglass-split me-1"></i>{{ $appt->duration_minutes }} {{ app()->getLocale() === 'ar' ? 'دقيقة' : 'min' }}</span>
                                @if($appt->office)
                                    <span><i class="bi bi-building me-1"></i>{{ $appt->office->name }}</span>
                                @endif
                                @if($appt->citizenRequest)
                                    <span>
                                        <i class="bi bi-file-earmark me-1"></i>
                                        {{ app()->getLocale() === 'ar' ? 'مرجع:' : 'Ref:' }} {{ $appt->citizenRequest->reference_number }}
                                        @if($appt->citizenRequest->service)
                                            · {{ $appt->citizenRequest->service->name }}
                                        @endif
                                    </span>
                                @endif
                            </div>

                            @if($appt->notes)
                                <div class="mt-2 p-2 rounded-2" style="background:#f8fafc;border:1px solid var(--border);font-size:.83rem;color:var(--text)">
                                    <i class="bi bi-info-circle me-1" style="color:var(--gold)"></i>
                                    {{ $appt->notes }}
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="col-auto d-flex flex-column gap-2 align-items-end">
                            @if($appt->status === 'pending')
                                <form method="POST" action="{{ route('citizen.appointments.confirm', $appt) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-emerald btn-sm">
                                        <i class="bi bi-check-lg me-1"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد' : 'Confirm' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('citizen.appointments.cancel', $appt) }}"
                                      onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'إلغاء هذا الموعد؟' : 'Cancel this appointment?' }}')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger-soft btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                                </form>
                            @elseif($appt->status === 'confirmed')
                                <span style="color:#10b981;font-size:.8rem;font-weight:600">
                                    <i class="bi bi-check-circle me-1"></i> {{ app()->getLocale() === 'ar' ? 'مؤكد' : 'Confirmed' }}
                                </span>
                                <form method="POST" action="{{ route('citizen.appointments.cancel', $appt) }}"
                                      onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد أنك تريد الإلغاء؟' : 'Are you sure you want to cancel?' }}')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger-soft btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                                </form>
                            @elseif($appt->status === 'completed')
                                <span style="color:#6366f1;font-size:.8rem;font-weight:600">
                                    <i class="bi bi-check2-all me-1"></i> {{ app()->getLocale() === 'ar' ? 'مكتمل' : 'Completed' }}
                                </span>
                            @elseif($appt->status === 'cancelled')
                                <span style="color:#ef4444;font-size:.8rem;font-weight:600">
                                    <i class="bi bi-x-circle me-1"></i> {{ app()->getLocale() === 'ar' ? 'ملغى' : 'Cancelled' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection