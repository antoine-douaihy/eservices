@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Offices</h4>
        <a href="{{ route('offices.create') }}" class="btn btn-primary">+ Create Office</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($offices->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <p class="mb-2 fs-5">No offices yet.</p>
                <a href="{{ route('offices.create') }}" class="btn btn-outline-primary">Create the first office</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($offices as $office)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-1">{{ $office->name }}</h5>
                            <p class="text-muted small mb-2">{{ $office->city }}</p>
                            <p class="mb-1 small">{{ $office->address }}</p>
                            @if($office->phone)
                                <p class="mb-1 small text-muted">{{ $office->phone }}</p>
                            @endif
                            <div class="mt-2">
                                <span class="badge bg-light text-dark border small">
                                    {{ number_format($office->latitude, 5) }}, {{ number_format($office->longitude, 5) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pb-3 px-4">
                            <a href="{{ route('offices.show', $office) }}" class="btn btn-sm btn-outline-primary">View Map</a>
                            <form method="POST" action="{{ route('offices.destroy', $office) }}"
                                  onsubmit="return confirm('Delete this office?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
