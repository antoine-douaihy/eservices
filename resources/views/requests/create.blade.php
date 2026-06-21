@extends('layouts.app')

@section('title', 'Submit Request')
@section('page-title', 'Submit Request')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:var(--navy);margin:0;">
            {{ app()->getLocale() === 'ar' ? 'تقديم طلب خدمة' : 'Submit a Service Request' }}
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            {{ app()->getLocale() === 'ar' ? 'اختر الدائرة والخدمة لبدء طلبك.' : 'Select an office and service to begin your application.' }}
        </p>
    </div>
    <a href="{{ route('citizen.my-requests') }}" class="btn-ghost">
        <i class="bi bi-arrow-left"></i> {{ __('app.nav_my_requests') }}
    </a>
</div>

@if($errors->any())
    <div class="alert-error-custom mb-4">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
        <div>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

@if($offices->isEmpty())
    <div class="app-card" style="text-align:center;padding:3rem 2rem;">
        <i class="bi bi-building-slash" style="font-size:2.5rem;color:var(--muted);opacity:0.4;display:block;margin-bottom:1rem;"></i>
        <p style="color:var(--muted);">{{ app()->getLocale() === 'ar' ? 'لا توجد دوائر متوفرة بعد. يرجى المحاولة لاحقاً.' : 'No offices are available yet. Please check back later.' }}</p>
    </div>
@else

<div class="row justify-content-center">
<div class="col-lg-7">
    <div class="app-card">
        <form method="POST" action="{{ route('citizen.apply.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="form-label-custom">{{ app()->getLocale() === 'ar' ? 'اختر الدائرة' : 'Select Office' }} <span style="color:#dc2626;">*</span></label>
                <select name="office_id" id="office_id" class="form-select-custom" required>
                    <option value="" disabled selected>{{ app()->getLocale() === 'ar' ? '— اختر دائرة —' : '— Choose an office —' }}</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}"
                            {{ old('office_id') == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}@if($office->city) — {{ $office->city }}@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label-custom">{{ app()->getLocale() === 'ar' ? 'اختر الخدمة' : 'Select Service' }} <span style="color:#dc2626;">*</span></label>
                <select name="service_id" id="service_id" class="form-select-custom" required>
                    <option value="" disabled selected>{{ app()->getLocale() === 'ar' ? '— اختر خدمة —' : '— Choose a service —' }}</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}"
                                data-office="{{ $service->office_id }}"
                                data-price="{{ $service->price }}"
                                data-docs="{{ $service->required_documents }}"
                                {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->display_name }} — {{ $service->price > 0 ? '$'.number_format($service->price,2) : __('pages.free') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Service info panel --}}
            <div id="service-info"
                 style="display:none;background:rgba(4,120,87,0.08);border:1px solid rgba(4,120,87,0.2);border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.25rem;font-size:0.875rem;">
                <strong style="color:#047857;">{{ __('pages.required_documents') }}:</strong>
                <p id="service-docs" style="color:var(--text);margin:0.35rem 0 0.5rem;"></p>
                <strong style="color:#047857;">{{ app()->getLocale() === 'ar' ? 'السعر:' : 'Price:' }}</strong>
                <span style="color:var(--gold);font-weight:700;">$<span id="service-price"></span></span>
            </div>

            {{-- Document upload --}}
            <div id="docs-upload-section" style="display:none;margin-bottom:1.25rem;">
                <label class="form-label-custom">
                    {{ app()->getLocale() === 'ar' ? 'تحميل المستندات المطلوبة' : 'Upload Required Documents' }} <span style="color:#dc2626;">*</span>
                    <span id="docs-upload-hint" style="text-transform:none;letter-spacing:0;font-weight:400;color:var(--muted);"></span>
                </label>
                <input type="file" name="documents[]" id="documents"
                       multiple accept=".jpg,.jpeg,.png,.pdf"
                       class="form-control-custom">
                <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">
                    {{ app()->getLocale() === 'ar' ? 'المقبول: JPG، PNG، PDF • حمّل ملفاً واحداً لكل مستند' : 'Accepted: JPG, PNG, PDF • Upload one file per document' }}
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label-custom">{{ __('pages.additional_notes') }}</label>
                <textarea name="notes" rows="3"
                          class="form-control-custom"
                          placeholder="{{ app()->getLocale() === 'ar' ? 'أي تفاصيل ذات صلة بطلبك…' : 'Any relevant details about your request…' }}">{{ old('notes') }}</textarea>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn-gold" style="flex:1;justify-content:center;">
                    <i class="bi bi-send-fill"></i> {{ __('pages.submit_application') }}
                </button>
                <a href="{{ route('citizen.my-requests') }}" class="btn-ghost" style="flex:1;justify-content:center;">
                    {{ __('app.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
</div>

@endif

@endsection

@push('scripts')
<script>
const officeSelect  = document.getElementById('office_id');
const serviceSelect = document.getElementById('service_id');
const serviceInfo   = document.getElementById('service-info');
const allOptions    = Array.from(serviceSelect.querySelectorAll('option[data-office]'));

function filterServices() {
    const selectedOffice = officeSelect.value;
    allOptions.forEach(opt => {
        if (!selectedOffice || opt.dataset.office === selectedOffice) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
            if (opt.selected) {
                opt.selected = false;
                serviceInfo.style.display = 'none';
            }
        }
    });
}

function showServiceInfo() {
    const selected = serviceSelect.options[serviceSelect.selectedIndex];
    const uploadSection = document.getElementById('docs-upload-section');
    const uploadHint    = document.getElementById('docs-upload-hint');

    if (selected && selected.dataset.price !== undefined) {
        document.getElementById('service-price').textContent = parseFloat(selected.dataset.price).toFixed(2);
        const docs = selected.dataset.docs || '';
        document.getElementById('service-docs').textContent = docs || 'None specified';
        serviceInfo.style.display = 'block';

        if (docs && docs !== 'null' && docs !== '[]') {
            uploadHint.textContent = '— ' + docs;
            uploadSection.style.display = 'block';
        } else {
            uploadSection.style.display = 'none';
        }
    } else {
        serviceInfo.style.display = 'none';
        uploadSection.style.display = 'none';
    }
}

officeSelect.addEventListener('change', filterServices);
serviceSelect.addEventListener('change', showServiceInfo);

filterServices();
showServiceInfo();
</script>
@endpush