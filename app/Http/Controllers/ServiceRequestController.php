<?php

namespace App\Http\Controllers;

use App\Events\StatusUpdated;
use App\Models\ServiceRequest;
use App\Notifications\StatusChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceRequest::where('user_id', Auth::id())
            ->with('rating')
            ->latest()
            ->get();

        return view('citizen.dashboard', compact('requests'));
    }

    public function create()
    {
        return view('citizen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:2000',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'status'      => 'pending',
        ]);

        return redirect()
            ->route('citizen.requests.show', $serviceRequest->uuid)
            ->with('success', 'Request submitted! Tracking code: ' . $serviceRequest->tracking_code);
    }

    public function show($uuid)
    {
        $service = ServiceRequest::where('uuid', $uuid)
            ->with(['messages.user', 'rating', 'user'])
            ->firstOrFail();

        // Citizens can only view their own requests
        if (Auth::user()->role === 'citizen' && $service->user_id !== Auth::id()) {
            abort(403);
        }

        return view('citizen.show', compact('service'));
    }

    public function updateStatus(Request $request, $id)
    {
        $service = ServiceRequest::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $oldStatus = $service->status;
        $service->update(['status' => $request->status]);

        if ($oldStatus !== $request->status) {
            try {
                broadcast(new StatusUpdated($service));
            } catch (\Exception $e) {
                \Log::error('StatusUpdated broadcast failed: ' . $e->getMessage());
            }

            try {
                $service->user->notify(new StatusChangedNotification($service));
            } catch (\Exception $e) {
                \Log::warning('Status change email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Status updated to ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }
}
