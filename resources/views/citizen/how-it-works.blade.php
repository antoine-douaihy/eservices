@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'كيف تعمل العملية' : 'How It Works')
@section('page-title', app()->getLocale() === 'ar' ? 'كيف تعمل العملية' : 'How It Works')

@push('styles')
<style>
    .wf-header { text-align:center; max-width:680px; margin:0 auto 2.5rem; }
    .wf-badge {
        display:inline-flex; align-items:center; gap:0.5rem; background:#ede9fe;
        border:1px solid #c4b5fd; border-radius:20px; padding:0.35rem 1rem;
        font-size:0.78rem; color:#5b21b6; font-weight:600; letter-spacing:0.05em; margin-bottom:1rem;
    }
    .wf-title { font-family:'Syne',sans-serif; font-weight:800; font-size:2rem; color:var(--navy); margin-bottom:0.75rem; }
    .wf-subtitle { color:var(--muted); font-size:0.95rem; }

    .wf-tree { max-width:760px; margin:0 auto 3rem; position:relative; }
    .wf-tree::before {
        content:''; position:absolute; left:27px; top:28px; bottom:28px; width:2px;
        background:repeating-linear-gradient(to bottom, var(--border) 0, var(--border) 6px, transparent 6px, transparent 12px);
    }
    html[dir="rtl"] .wf-tree::before { left:auto; right:27px; }

    .wf-step {
        position:relative; display:flex; gap:1.25rem; margin-bottom:0.5rem;
        padding:1rem 0; cursor:pointer;
    }
    .wf-step-icon {
        width:56px; height:56px; border-radius:50%; flex-shrink:0;
        display:flex; align-items:center; justify-content:center; font-size:1.3rem;
        border:3px solid #fff; box-shadow:0 0 0 1px var(--border); transition:transform 0.2s, box-shadow 0.2s;
        background:#fff; cursor:pointer; user-select:none;
    }
    .wf-step:hover .wf-step-icon { box-shadow:0 0 0 3px rgba(214,158,46,0.45); transform:scale(1.06); }
    .wf-step.active .wf-step-icon { transform:scale(1.08); box-shadow:0 0 0 3px var(--gold); }
    .wf-step-body { flex:1; padding-top:0.4rem; }
    .wf-step-title { font-family:'Syne',sans-serif; font-weight:700; font-size:1.05rem; color:var(--navy); margin-bottom:0.25rem; display:flex; align-items:center; gap:0.5rem; }
    .wf-step-sub { color:var(--muted); font-size:0.85rem; }
    .wf-step-detail {
        display:none; margin-top:0.875rem; background:#f8fafc; border:1px solid var(--border);
        border-radius:10px; padding:1rem 1.25rem; font-size:0.85rem; color:var(--text); line-height:1.7;
    }
    .wf-step.active .wf-step-detail { display:block; }
    .wf-chevron { margin-left:auto; color:var(--muted); transition:transform 0.2s; }
    .wf-step.active .wf-chevron { transform:rotate(90deg); }

    .wf-services-card { max-width:760px; margin:0 auto; background:#fff; border:1px solid var(--border); border-radius:16px; padding:1.75rem; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
    .wf-doc-row { display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; color:var(--text); margin-bottom:0.4rem; }
</style>
@endpush

@section('content')

@php $isAr = app()->getLocale() === 'ar'; @endphp

<div style="padding:2rem 0 4rem;">

    <div class="wf-header">
        <div class="wf-badge"><i class="bi bi-diagram-3-fill"></i> {{ $isAr ? 'دليل العملية' : 'PROCESS GUIDE' }}</div>
        <h1 class="wf-title">{{ $isAr ? 'كيف تعمل العملية' : 'How It Works' }}</h1>
        <p class="wf-subtitle">{{ $isAr ? 'تعرّف على كل خطوة من تقديم الطلب إلى استلام الشهادة — قبل أن تبدأ.' : 'See every step from submitting your application to receiving your certificate — before you even start. Click any step below for details.' }}</p>
    </div>

    <div class="wf-tree" id="wfTree">

        <div class="wf-step active" data-step="1">
            <div class="wf-step-icon" style="background:#dbeafe;color:#1d4ed8;" title="{{ $isAr ? 'انقر للتفاصيل' : 'Click for details' }}"><i class="bi bi-search"></i></div>
            <div class="wf-step-body">
                <div class="wf-step-title">{{ $isAr ? '1. تصفح واختر خدمة' : '1. Browse & Select a Service' }} <i class="bi bi-chevron-right wf-chevron"></i></div>
                <div class="wf-step-sub">{{ $isAr ? 'ابحث في دليل الخدمات' : 'Search the service catalog' }}</div>
                <div class="wf-step-detail">{{ $isAr ? 'تصفح جميع الخدمات الحكومية المتوفرة، وابحث بالاسم أو الدائرة. كل خدمة تعرض رسومها (إن وجدت) والمستندات المطلوبة قبل أن تبدأ.' : "Browse all available government services, search by name or office. Each listing shows its fee (if any) and required documents upfront, so there are no surprises before you start." }}</div>
            </div>
        </div>

        <div class="wf-step" data-step="2">
            <div class="wf-step-icon" style="background:#fef3c7;color:var(--gold);" title="{{ $isAr ? 'انقر للتفاصيل' : 'Click for details' }}"><i class="bi bi-pencil-square"></i></div>
            <div class="wf-step-body">
                <div class="wf-step-title">{{ $isAr ? '2. التقديم وتحميل المستندات' : '2. Apply & Upload Documents' }} <i class="bi bi-chevron-right wf-chevron"></i></div>
                <div class="wf-step-sub">{{ $isAr ? 'أدخل بياناتك وحمّل ما هو مطلوب' : 'Fill in your details, upload what\'s required' }}</div>
                <div class="wf-step-detail">{{ $isAr ? 'أدخل معلومات الاتصال الخاصة بك، ودع النظام يحدد أقرب دائرة تلقائياً (أو اخترها يدوياً)، ثم حمّل المستندات المطلوبة (PDF أو JPG أو PNG). تُشفَّر مستنداتك وتُخزَّن بأمان.' : "Enter your contact details, let the platform auto-detect the nearest office (or pick one yourself), and upload the required documents (PDF, JPG, or PNG). Your documents are encrypted and stored securely." }}</div>
            </div>
        </div>

        <div class="wf-step" data-step="3">
            <div class="wf-step-icon" style="background:#d1fae5;color:#047857;" title="{{ $isAr ? 'انقر للتفاصيل' : 'Click for details' }}"><i class="bi bi-credit-card-fill"></i></div>
            <div class="wf-step-body">
                <div class="wf-step-title">{{ $isAr ? '3. الدفع (إذا كانت الخدمة مدفوعة)' : '3. Payment (if the service has a fee)' }} <i class="bi bi-chevron-right wf-chevron"></i></div>
                <div class="wf-step-sub">{{ $isAr ? 'بطاقة ائتمان أو عملة مشفرة' : 'Card or cryptocurrency' }}</div>
                <div class="wf-step-detail">{{ $isAr ? 'إذا كانت الخدمة تتطلب رسوماً، تنتقل إلى صفحة الدفع لاختيار طريقة الدفع: بطاقة ائتمان عبر Stripe، أو عملة مشفرة (Bitcoin/Ethereum). تُحدَّث حالة طلبك تلقائياً بعد تأكيد الدفع.' : "If the service has a fee, you'll be taken to a payment page to choose Stripe (card) or cryptocurrency (Bitcoin/Ethereum). Your request status updates automatically the moment payment is confirmed — free services skip this step entirely." }}</div>
            </div>
        </div>

        <div class="wf-step" data-step="4">
            <div class="wf-step-icon" style="background:#ede9fe;color:#5b21b6;" title="{{ $isAr ? 'انقر للتفاصيل' : 'Click for details' }}"><i class="bi bi-people-fill"></i></div>
            <div class="wf-step-body">
                <div class="wf-step-title">{{ $isAr ? '4. مراجعة الدائرة' : '4. Office Review' }} <i class="bi bi-chevron-right wf-chevron"></i></div>
                <div class="wf-step-sub">{{ $isAr ? 'يتحقق الموظفون من طلبك' : 'Staff verify your request' }}</div>
                <div class="wf-step-detail">{{ $isAr ? 'يراجع موظفو الدائرة المخصصة طلبك ومستنداتك. يمكنك التواصل معهم مباشرة عبر المحادثة المباشرة داخل صفحة طلبك إذا كان لديك أي سؤال.' : "Staff at your assigned office review your request and documents. You can message them directly through the live chat on your request page at any point if you have questions." }}</div>
            </div>
        </div>

        <div class="wf-step" data-step="5">
            <div class="wf-step-icon" style="background:#fee2e2;color:#991b1b;" title="{{ $isAr ? 'انقر للتفاصيل' : 'Click for details' }}"><i class="bi bi-signpost-split-fill"></i></div>
            <div class="wf-step-body">
                <div class="wf-step-title">{{ $isAr ? '5. القرار' : '5. The Decision' }} <i class="bi bi-chevron-right wf-chevron"></i></div>
                <div class="wf-step-sub">{{ $isAr ? 'مقبول، مستندات ناقصة، أو مرفوض' : 'Approved, missing documents, or declined' }}</div>
                <div class="wf-step-detail">
                    {{ $isAr ? 'هناك ثلاث نتائج ممكنة:' : 'There are three possible outcomes:' }}
                    <br>• <strong>{{ $isAr ? 'مقبول' : 'Approved' }}</strong> — {{ $isAr ? 'تنتقل مباشرة إلى الخطوة 6.' : 'you move straight to step 6.' }}
                    <br>• <strong>{{ $isAr ? 'مستندات ناقصة' : 'Missing Documents' }}</strong> — {{ $isAr ? 'تحصل على إشعار يطلب رفع مستندات إضافية، ثم تعاد المراجعة.' : "you'll be notified exactly what's missing and can re-upload it, then review continues." }}
                    <br>• <strong>{{ $isAr ? 'مرفوض' : 'Declined' }}</strong> — {{ $isAr ? 'تحصل على سبب الرفض ويمكنك إعادة التقديم بمعلومات مصححة.' : "you'll see the reason and can resubmit with corrected information." }}
                </div>
            </div>
        </div>

        <div class="wf-step" data-step="6">
            <div class="wf-step-icon" style="background:#fef3c7;color:var(--gold);" title="{{ $isAr ? 'انقر للتفاصيل' : 'Click for details' }}"><i class="bi bi-patch-check-fill"></i></div>
            <div class="wf-step-body">
                <div class="wf-step-title">{{ $isAr ? '6. الشهادة والتتبع' : '6. Certificate & Tracking' }} <i class="bi bi-chevron-right wf-chevron"></i></div>
                <div class="wf-step-sub">{{ $isAr ? 'تحميل الشهادة وتتبعها برمز QR' : 'Download your certificate, track anytime with a QR code' }}</div>
                <div class="wf-step-detail">{{ $isAr ? 'بعد القبول، يتم إصدار شهادتك الرسمية فوراً ويمكن تحميلها كملف PDF. يحصل كل طلب أيضاً على رمز QR فريد يتيح لك أو لأي شخص تتبع حالته دون الحاجة لتسجيل الدخول.' : "Once approved, your official certificate is generated instantly as a downloadable PDF. Every request also gets a unique QR code so you (or anyone) can check its status anytime — no login required." }}</div>
            </div>
        </div>

    </div>

    @if($services->isNotEmpty())
    <div class="wf-services-card">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;color:var(--navy);margin-bottom:1rem;">
            <i class="bi bi-clipboard-check me-2" style="color:var(--gold);"></i>
            {{ $isAr ? 'تحقق من متطلبات خدمة معينة' : "Check a specific service's requirements" }}
        </div>
        <select id="wfServiceSelect" class="form-select-custom" style="margin-bottom:1.25rem;">
            <option value="">{{ $isAr ? '— اختر خدمة —' : '— Choose a service —' }}</option>
            @foreach($services as $service)
                <option value="svc-{{ $service->id }}">{{ $service->display_name }}</option>
            @endforeach
        </select>

        @foreach($services as $service)
        <div id="svc-{{ $service->id }}" style="display:none;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
                <strong style="color:var(--navy);">{{ $service->display_name }}</strong>
                <span class="price-display" data-currency="{{ $service->currency }}" data-lbp-raw="{{ $service->price }}" style="font-weight:700;color:var(--gold);">
                    @if($service->price > 0)
                        @if($service->currency === 'LBP')ل.ل {{ number_format($service->price, 0) }}@else{{ $service->currency }} {{ number_format($service->price, 2) }}@endif
                    @else
                        {{ $isAr ? 'مجاني' : 'Free' }}
                    @endif
                </span>
            </div>
            @if($service->office)
                <div style="font-size:0.8rem;color:var(--muted);margin-bottom:0.75rem;"><i class="bi bi-building me-1"></i>{{ $service->office->name }}</div>
            @endif
            @if($service->requiredDocuments->isNotEmpty())
                <div style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">{{ $isAr ? 'المستندات المطلوبة' : 'Required Documents' }}</div>
                @foreach($service->requiredDocuments as $doc)
                    <div class="wf-doc-row">
                        <i class="bi bi-{{ $doc->is_mandatory ? 'check-circle-fill' : 'circle' }}" style="color:{{ $doc->is_mandatory ? '#047857' : 'var(--muted)' }};font-size:0.7rem;"></i>
                        {{ $doc->display_name }}
                        @if(!$doc->is_mandatory)<span style="color:var(--muted);font-size:0.72rem;">({{ $isAr ? 'اختياري' : 'optional' }})</span>@endif
                    </div>
                @endforeach
            @else
                <div style="font-size:0.82rem;color:var(--muted);">{{ $isAr ? 'لا توجد مستندات مطلوبة لهذه الخدمة.' : 'No documents required for this service.' }}</div>
            @endif
            <a href="{{ route('citizen.services.apply', $service) }}" class="btn-gold" style="margin-top:1rem;display:inline-flex;">
                <i class="bi bi-send-fill"></i> {{ $isAr ? 'تقديم الآن' : 'Apply Now' }}
            </a>
        </div>
        @endforeach
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Step accordion
    document.querySelectorAll('#wfTree .wf-step').forEach(function (step) {
        step.addEventListener('click', function () {
            step.classList.toggle('active');
        });
    });

    // Service requirements lookup
    var wfSelect = document.getElementById('wfServiceSelect');
    if (wfSelect) {
        wfSelect.addEventListener('change', function () {
            document.querySelectorAll('[id^="svc-"]').forEach(function (el) {
                el.style.display = 'none';
            });
            if (this.value) {
                var el = document.getElementById(this.value);
                if (el) el.style.display = 'block';
            }
        });
    }
});
</script>
@endpush