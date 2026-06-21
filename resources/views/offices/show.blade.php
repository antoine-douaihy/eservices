@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('offices.index') }}" class="btn btn-outline-secondary btn-sm me-3">← Back</a>
                <h4 class="fw-bold mb-0">{{ $office->name }}</h4>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <small class="text-muted">City</small>
                            <div class="fw-semibold">{{ $office->city }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <small class="text-muted">Address</small>
                            <div class="fw-semibold">{{ $office->address }}</div>
                        </div>
                        @if($office->phone)
                        <div class="col-md-6 mb-2">
                            <small class="text-muted">Phone</small>
                            <div class="fw-semibold">{{ $office->phone }}</div>
                        </div>
                        @endif
                        @if($office->email)
                        <div class="col-md-6 mb-2">
                            <small class="text-muted">Email</small>
                            <div class="fw-semibold">{{ $office->email }}</div>
                        </div>
                        @endif
                        <div class="col-md-6 mb-2">
                            <small class="text-muted">Coordinates</small>
                            <div class="fw-semibold">{{ $office->latitude }}, {{ $office->longitude }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Map showing the pinned location --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Office Location</h5>
                    <div id="map" style="height: 400px; border-radius: 10px;"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lat = {{ $office->latitude ?? 33.8886 }};
    const lng = {{ $office->longitude ?? 35.4955 }};
    const map = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    L.marker([lat, lng])
        .addTo(map)
        .bindPopup('<strong>{{ addslashes($office->name) }}</strong><br>{{ addslashes($office->address) }}')
        .openPopup();
});
</script>
@endsection
