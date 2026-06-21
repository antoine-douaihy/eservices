@extends('layouts.tracking')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-6">

        {{-- Status Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-5">
                @php $isAr = app()->getLocale() === 'ar'; @endphp
                <p class="text-muted small mb-1">{{ $isAr ? 'المرجع' : 'Reference' }}</p>
                <h3 class="fw-bold mb-3" style="color:#1E3A5F;letter-spacing:2px">
                    #{{ $citizenRequest->id }}
                </h3>

                @php
                    $statusConfig = $isAr ? [
                        'pending'           => ['warning', 'قيد المراجعة'],
                        'pending_payment'   => ['primary', 'بانتظار الدفع'],
                        'in_review'         => ['info',    'قيد المراجعة'],
                        'missing_documents' => ['warning', 'مستندات ناقصة'],
                        'approved'          => ['success', 'مقبول'],
                        'rejected'          => ['danger',  'مرفوض'],
                    ] : [
                        'pending'           => ['warning', 'Pending Review'],
                        'pending_payment'   => ['primary', 'Awaiting Payment'],
                        'in_review'         => ['info',    'In Review'],
                        'missing_documents' => ['warning', 'Missing Documents'],
                        'approved'          => ['success', 'Approved'],
                        'rejected'          => ['danger',  'Declined'],
                    ];
                    [$badgeClass, $statusLabel] = $statusConfig[$citizenRequest->status]
                        ?? ['secondary', ucfirst(str_replace('_', ' ', $citizenRequest->status))];
                @endphp

                <span class="badge bg-{{ $badgeClass }} fs-6 px-4 py-2 mb-4">
                    {{ $statusLabel }}
                </span>

                <h5 class="fw-semibold mb-2">{{ $citizenRequest->service->name ?? '—' }}</h5>

                @if($citizenRequest->office)
                    <p class="text-muted mb-0 small">
                        {{ $citizenRequest->office->name }}{{ $citizenRequest->office->city ? ', '.$citizenRequest->office->city : '' }}
                    </p>
                @endif

                @if($citizenRequest->notes)
                    <p class="text-muted small mt-2 mb-0 fst-italic">"{{ $citizenRequest->notes }}"</p>
                @endif
            </div>
        </div>

        {{-- QR Code Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <p class="text-muted small mb-3">{{ $isAr ? 'اسحب لمشاركة صفحة التتبع هذه' : 'Scan to share this tracking page' }}</p>
                <div class="d-inline-block p-3 bg-white border rounded shadow-sm">
                    {!! $qrCode !!}
                </div>
                <p class="text-muted small mt-3 mb-0">
                    <i class="bi bi-phone me-1"></i>
                    {{ $isAr ? 'شارك رمز QR هذا للسماح للآخرين بمعرفة حالة هذا الطلب' : "Share this QR code to let others check this request's status" }}
                </p>
            </div>
        </div>

        {{-- Status Timeline --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                <h6 class="fw-bold mb-0" style="color:#1E3A5F">{{ $isAr ? 'مسار الحالة' : 'Status Timeline' }}</h6>
            </div>
            <div class="card-body px-4 pb-4">
                @php
                    $statusOrder = ['pending', 'pending_payment', 'in_review', 'approved'];
                    $steps = $isAr ? [
                        'pending'         => ['تم الإرسال',        'تم استلام الطلب وهو بانتظار المراجعة'],
                        'pending_payment' => ['بانتظار الدفع', 'الدفع مطلوب للمتابعة'],
                        'in_review'       => ['قيد المراجعة',        'طلبك قيد المعالجة من قبل الموظفين'],
                        'approved'        => ['مقبول',         'تم قبول طلبك'],
                    ] : [
                        'pending'         => ['Submitted',        'Request received and awaiting review'],
                        'pending_payment' => ['Awaiting Payment', 'Payment required to proceed'],
                        'in_review'       => ['In Review',        'Your request is being processed by staff'],
                        'approved'        => ['Approved',         'Your request has been approved'],
                    ];
                    $currentIndex = array_search($citizenRequest->status, $statusOrder);
                    $isRejected   = $citizenRequest->status === 'rejected';
                @endphp

                @foreach($steps as $step => [$label, $desc])
                    @php
                        $stepIndex = array_search($step, $statusOrder);
                        $isDone    = !$isRejected && $currentIndex !== false && $stepIndex <= $currentIndex;
                    @endphp
                    <div class="d-flex gap-3 mb-3">
                        <div class="flex-shrink-0 mt-1">
                            @if($isDone)
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:28px;height:28px;background:#DCFCE7">
                                    <svg width="14" height="14" fill="#16A34A" viewBox="0 0 16 16">
                                        <path d="M13.485 1.431a1.473 1.473 0 0 1 2.104 2.062l-7.84 9.801a1.473 1.473 0 0 1-2.12.04L.431 8.138a1.473 1.473 0 0 1 2.084-2.083l4.111 4.112 6.82-8.69a.486.486 0 0 1 .04-.045z"/>
                                    </svg>
                                </div>
                            @else
                                <div class="rounded-circle border"
                                     style="width:28px;height:28px;background:#F1F5F9"></div>
                            @endif
                        </div>
                        <div>
                            <p class="mb-0 fw-semibold {{ $isDone ? '' : 'text-muted' }}" style="font-size:.9rem">{{ $label }}</p>
                            <small class="text-muted">{{ $desc }}</small>
                        </div>
                    </div>
                @endforeach

                @if($isRejected)
                    <div class="d-flex gap-3 mb-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:28px;height:28px;background:#FEE2E2">
                                <svg width="14" height="14" fill="#DC2626" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="mb-0 fw-semibold text-danger" style="font-size:.9rem">{{ $isAr ? 'مرفوض' : 'Declined' }}</p>
                            <small class="text-muted">{{ $isAr ? 'لم يتم قبول هذا الطلب' : 'This request was not approved' }}</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <p class="text-center text-muted small mt-4">
            {{ $isAr ? 'آخر تحديث:' : 'Last updated:' }} {{ $citizenRequest->updated_at->diffForHumans() }}
        </p>

    </div>
</div>
@endsection
