@extends('admin.layouts.app')

@section('title', 'Appointments')
@section('page-title', 'Appointment Management')

@section('content')

@php
    $statusColors = [
        'pending'   => 'warning',
        'confirmed' => 'success',
        'cancelled' => 'danger',
        'completed' => 'info',
    ];
@endphp

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0">
            Appointments
        </h1>
        <p style="color:var(--muted);font-size:.875rem;margin:.25rem 0 0">
            Schedule and manage in-person visits with citizens
        </p>
    </div>
    <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#new-appointment-modal">
        <i class="bi bi-calendar-plus me-1"></i> New Appointment
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="font-size:.875rem">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="font-size:.875rem">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Stats row --}}
@php
    $total     = $appointments->count();
    $pending   = $appointments->where('status','pending')->count();
    $confirmed = $appointments->where('status','confirmed')->count();
    $today     = $appointments->filter(fn($a) => $a->scheduled_at->isToday())->count();
@endphp
<div class="row g-3 mb-4">
    @foreach([
        ['Total',     $total,     'calendar3',          'var(--gold)'],
        ['Pending',   $pending,   'hourglass-split',    '#f59e0b'],
        ['Confirmed', $confirmed, 'calendar2-check',    '#10b981'],
        ['Today',     $today,     'calendar-event',     '#6366f1'],
    ] as [$label, $value, $icon, $color])
    <div class="col-6 col-md-3">
        <div class="admin-card p-3 d-flex align-items-center gap-3">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $color }}22;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-{{ $icon }}" style="color:{{ $color }};font-size:1.1rem"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:800;color:#fff;line-height:1">{{ $value }}</div>
                <div style="font-size:.75rem;color:var(--muted)">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Appointments table --}}
@if($appointments->isEmpty())
    <div class="admin-card p-5 text-center">
        <i class="bi bi-calendar-x" style="font-size:3rem;color:var(--muted);opacity:.4"></i>
        <p class="mt-3 mb-0" style="color:var(--muted)">No appointments scheduled yet.</p>
        <button class="btn btn-gold mt-3" data-bs-toggle="modal" data-bs-target="#new-appointment-modal">
            <i class="bi bi-calendar-plus me-1"></i> Schedule First Appointment
        </button>
    </div>
@else
    <div class="admin-card overflow-hidden">
        <div class="table-responsive">
            <table class="admin-table w-100">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Citizen</th>
                        <th>Title / Purpose</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appt)
                        <tr>
                            <td>
                                <div style="font-weight:600;color:var(--text)">
                                    {{ $appt->scheduled_at->format('d M Y') }}
                                </div>
                                <div style="font-size:.8rem;color:var(--muted)">
                                    {{ $appt->scheduled_at->format('H:i') }}
                                    @if($appt->scheduled_at->isToday())
                                        <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem">Today</span>
                                    @elseif($appt->scheduled_at->isPast())
                                        <span class="badge bg-secondary ms-1" style="font-size:.65rem">Past</span>
                                    @elseif($appt->scheduled_at->isTomorrow())
                                        <span class="badge bg-info text-dark ms-1" style="font-size:.65rem">Tomorrow</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="color:var(--text);font-weight:500">
                                    {{ $appt->user->first_name ?? '' }} {{ $appt->user->last_name ?? '' }}
                                </div>
                                @if($appt->citizenRequest)
                                    <div style="font-size:.78rem;color:var(--muted)">
                                        Ref: {{ $appt->citizenRequest->reference_number }}
                                    </div>
                                @endif
                            </td>
                            <td style="color:var(--text)">
                                {{ $appt->title }}
                                @if($appt->notes)
                                    <div style="font-size:.78rem;color:var(--muted);margin-top:.2rem">
                                        {{ Str::limit($appt->notes, 60) }}
                                    </div>
                                @endif
                            </td>
                            <td style="color:var(--muted);font-size:.875rem">
                                {{ $appt->duration_minutes }} min
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusColors[$appt->status] ?? 'secondary' }}">
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end flex-wrap">
                                    @if($appt->status === 'pending')
                                        <form method="POST" action="{{ route('office.appointments.status', $appt) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-emerald btn-sm">
                                                <i class="bi bi-check-lg me-1"></i>Confirm
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('office.appointments.status', $appt) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-danger-soft btn-sm">Cancel</button>
                                        </form>
                                    @elseif($appt->status === 'confirmed')
                                        <form method="POST" action="{{ route('office.appointments.status', $appt) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-ghost btn-sm">
                                                <i class="bi bi-check2-all me-1"></i>Complete
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('office.appointments.status', $appt) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-danger-soft btn-sm">Cancel</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('office.appointments.destroy', $appt) }}"
                                          onsubmit="return confirm('Delete this appointment?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger-soft btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- New Appointment Modal --}}
<div class="modal fade" id="new-appointment-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:#1a2942;border:1px solid var(--border);color:var(--text)">
            <div class="modal-header" style="border-color:var(--border)">
                <h6 class="modal-title fw-bold" style="color:var(--gold)">
                    <i class="bi bi-calendar-plus me-1"></i> Schedule New Appointment
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('office.appointments.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        @if(auth()->user()->role !== 'office')
                        <div class="col-md-6">
                            <label class="form-label-custom">Office <span style="color:#f87171">*</span></label>
                            <select name="office_id" class="form-select-custom" required>
                                <option value="">— Select office —</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="{{ auth()->user()->role !== 'office' ? 'col-md-6' : 'col-md-6' }}">
                            <label class="form-label-custom">Citizen (User) <span style="color:#f87171">*</span></label>
                            <select name="user_id" class="form-select-custom" required>
                                <option value="">— Select citizen —</option>
                                @foreach(\App\Models\User::where('role','citizen')->orderBy('first_name')->get() as $citizen)
                                    <option value="{{ $citizen->id }}">{{ $citizen->first_name }} {{ $citizen->last_name }} — {{ $citizen->email }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Linked Request (optional)</label>
                            <select name="citizen_request_id" class="form-select-custom">
                                <option value="">— None —</option>
                                @foreach(\App\Models\CitizenRequest::with('user','service')->latest()->take(100)->get() as $cr)
                                    <option value="{{ $cr->id }}">
                                        #{{ $cr->id }} — {{ $cr->user->first_name ?? '' }} {{ $cr->user->last_name ?? '' }} · {{ $cr->service->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Appointment Title <span style="color:#f87171">*</span></label>
                            <input type="text" name="title" class="form-control-custom" placeholder="e.g. Document Verification, ID Collection…" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Date & Time <span style="color:#f87171">*</span></label>
                            <input type="datetime-local" name="scheduled_at" class="form-control-custom" required
                                min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Duration (minutes)</label>
                            <select name="duration_minutes" class="form-select-custom">
                                <option value="15">15 min</option>
                                <option value="30" selected>30 min</option>
                                <option value="45">45 min</option>
                                <option value="60">1 hour</option>
                                <option value="90">1.5 hours</option>
                                <option value="120">2 hours</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Internal Notes (optional)</label>
                            <textarea name="notes" class="form-control-custom" rows="3" maxlength="1000"
                                placeholder="Any notes visible to the citizen about what to bring or prepare…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)">
                    <button type="button" class="btn btn-ghost btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold btn-sm">
                        <i class="bi bi-calendar-plus me-1"></i> Schedule Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
