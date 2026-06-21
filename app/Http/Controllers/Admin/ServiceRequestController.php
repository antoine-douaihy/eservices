<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    // 1. Load the Dashboard
    public function index()
    {
        // Fetch all requests, grab the citizen's info, and sort by newest first
        $requests = ServiceRequest::with('user')->latest()->get();

        return view('admin.requests', compact('requests'));
    }

    // 2. Approve or Reject the Application
    public function update(Request $request, $id)
    {
        // Ensure they only send 'approved' or 'rejected'
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        // Find the application and update it
        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Application successfully marked as ' . ucfirst($request->status) . '!');
    }
}
