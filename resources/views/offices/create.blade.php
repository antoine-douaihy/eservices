@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('offices.index') }}" class="btn btn-outline-secondary btn-sm me-3">← Back</a>
                <h4 class="fw-bold mb-0">Create New Office</h4>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('offices.store') }}" method="POST" id="officeForm">
                @csrf

                {{-- Hidden lat/lng populated by map --}}
                <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Office Details</h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Office Name *</label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Beirut Central Office" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-semibold">Street Address *</label>
                                <input type="text" name="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address') }}" placeholder="e.g. Hamra Street, Building 12" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">City *</label>
                                <input type="text" name="city"
                                       class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city') }}" placeholder="e.g. Beirut" required>
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="tel" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}" placeholder="+961 1 234 567">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" placeholder="office@example.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Google Maps Pin Picker --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-1">Pin Office Location *</h5>
                        <p class="text-muted small mb-3">Click anywhere on the map to drop a pin. Drag the pin to adjust.</p>

                        @error('latitude')
                            <div class="alert alert-danger py-2">Please select a location on the map.</div>
                        @enderror

                        <div id="map" style="height: 420px; border-radius: 10px; border: 1px solid #dee2e6;"></div>

                        <div class="mt-3 d-flex gap-4">
                            <div>
                                <small class="text-muted">Latitude</small>
                                <div id="lat-display" class="fw-semibold text-secondary">—</div>
                            </div>
                            <div>
                                <small class="text-muted">Longitude</small>
                                <div id="lng-display" class="fw-semibold text-secondary">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                        Save Office
                    </button>
                    <a href="{{ route('offices.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
let marker = null;
let map = null;

function initMap() {
    // Default center: Lebanon
    const defaultCenter = { lat: 33.8886, lng: 35.4955 };

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 9,
        center: defaultCenter,
        mapTypeControl: true,
        streetViewControl: false,
    });

    // If old values exist (validation failed), restore the pin
    const oldLat = parseFloat('{{ old('latitude') }}');
    const oldLng = parseFloat('{{ old('longitude') }}');
    if (!isNaN(oldLat) && !isNaN(oldLng)) {
        placeMarker({ lat: oldLat, lng: oldLng });
        map.setCenter({ lat: oldLat, lng: oldLng });
        map.setZoom(14);
    }

    map.addListener('click', function(event) {
        placeMarker(event.latLng);
    });
}

function placeMarker(position) {
    if (marker) {
        marker.setPosition(position);
    } else {
        marker = new google.maps.Marker({
            position: position,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: 'Office Location',
        });

        marker.addListener('dragend', function(event) {
            updateCoords(event.latLng);
        });
    }
    updateCoords(position);
}

function updateCoords(latLng) {
    const lat = latLng.lat();
    const lng = latLng.lng();
    document.getElementById('latitude').value  = lat;
    document.getElementById('longitude').value = lng;
    document.getElementById('lat-display').textContent = lat.toFixed(6);
    document.getElementById('lng-display').textContent = lng.toFixed(6);
}

document.getElementById('officeForm').addEventListener('submit', function(e) {
    if (!document.getElementById('latitude').value) {
        e.preventDefault();
        alert('Please click on the map to select the office location before saving.');
        document.getElementById('map').scrollIntoView({ behavior: 'smooth' });
    }
});
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap"
    async defer>
</script>
@endsection
