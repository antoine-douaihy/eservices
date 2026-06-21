@extends('admin.layouts.app')

@section('title', 'Review Requests')
@section('page-title', 'Review Requests')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            Review Requests
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            Approve or reject citizen service applications
        </p>
    </div>
</div>

<div class="admin-card" style="padding:0;overflow:hidden;">
    @if($requests->isEmpty())
        <div style="text-align:center;padding:4rem 2rem;">
            <i class="bi bi-folder-check" style="font-size:3rem;color:var(--muted);opacity:0.4;"></i>
            <p style="color:var(--muted);margin-top:1rem;">No citizen requests found.</p>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Citizen</th>
                        <th>Document</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td style="color:var(--muted);font-size:0.78rem;">#{{ $request->id }}</td>
                        <td style="font-weight:600;color:#fff;">{{ $request->user->first_name }} {{ $request->user->last_name }}</td>
                        <td style="color:var(--text);">{{ $request->document_type }}</td>
                        <td style="color:var(--muted);font-size:0.8rem;">{{ $request->created_at->format('d M Y') }}</td>
                        <td>
                            @if($request->status === 'pending')
                                <span style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:#fcd34d;font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">Pending</span>
                            @elseif($request->status === 'approved')
                                <span style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);color:#6ee7b7;font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">Approved</span>
                            @else
                                <span style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#f87171;font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                @if($request->status === 'pending')
                                    <form action="{{ route('admin.requests.update', $request->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-emerald">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.requests.update', $request->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn-danger-soft">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </form>
                                @else
                                    <span style="color:var(--muted);font-size:0.8rem;font-weight:600;">Reviewed</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
