@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('citizen.requests.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0" style="color:#1E3A5F">{{ app()->getLocale() === 'ar' ? 'إرسال طلب جديد' : 'Submit New Request' }}</h4>
                    <p class="text-muted mb-0 small">{{ app()->getLocale() === 'ar' ? 'املأ التفاصيل أدناه لإرسال طلبك' : 'Fill in the details below to submit your request' }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('citizen.requests.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">{{ app()->getLocale() === 'ar' ? 'عنوان الطلب' : 'Request Title' }}</label>
                            <input type="text" id="title" name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}"
                                   placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: شهادة ميلاد، رخصة تجارية' : 'e.g. Birth Certificate, Business Permit' }}"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label>
                            <textarea id="description" name="description" rows="5"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="{{ app()->getLocale() === 'ar' ? 'يرجى وصف ما تحتاجه بالتفصيل...' : 'Please describe what you need in detail...' }}"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ app()->getLocale() === 'ar' ? 'قدّم أكبر قدر ممكن من التفاصيل لمساعدة موظفينا على معالجة طلبك بسرعة.' : 'Provide as much detail as possible to help our staff process your request quickly.' }}</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>{{ app()->getLocale() === 'ar' ? 'إرسال الطلب' : 'Submit Request' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
