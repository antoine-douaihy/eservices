<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, $id)
    {
        $service = ServiceRequest::findOrFail($id);

        // Security Guard: Prevent ratings on unfinished services or by wrong users
        if ($service->status !== 'completed') {
            return back()->with('error', 'You can only rate a completed service.');
        }

        if ($service->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        // Using the relationship (Make sure you define 'rating()' in the ServiceRequest model)
        $service->rating()->create([
            'user_id' => Auth::id(),
            'stars' => $request->stars,
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }
}
