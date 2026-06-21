@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1">{{ app()->getLocale() === 'ar' ? 'أهلاً، ' . Auth::user()->first_name . '!' : 'Welcome, ' . Auth::user()->first_name . '!' }}</h4>
                    <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'أنت مسجل الدخول إلى منصة الخدمات الإلكترونية.' : 'You are logged in to the E-Services Platform.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
