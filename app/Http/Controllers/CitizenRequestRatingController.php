<?php

namespace App\Http\Controllers;

use App\Models\CitizenRequest;
use App\Models\CitizenRequestRating;
use App\Support\LaravelRequest as Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class CitizenRequestRatingController extends Controller
{
    // Citizen: submit a rating for an approved request
    public function store(Request $request, CitizenRequest $citizenRequest)
    {
        if ($citizenRequest->user_id !== Auth::id()) abort(403);

        if ($citizenRequest->status !== 'approved') {
            return Redirect::back()->with('error', 'Only approved requests can be rated.');
        }

        if ($citizenRequest->rating) {
            return Redirect::back()->with('error', 'You have already rated this request.');
        }

        $request->validate([
            'stars'   => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        CitizenRequestRating::create([
            'citizen_request_id' => $citizenRequest->id,
            'user_id'            => Auth::id(),
            'stars'              => $request->input('stars'),
            'comment'            => $request->input('comment'),
        ]);

        return Redirect::back()->with('success', 'Thank you for your feedback!');
    }

    // Office staff: list all ratings for their office
    public function officeIndex()
    {
        $user  = Auth::user();
        $query = CitizenRequestRating::with(['citizenRequest.service', 'citizenRequest.office', 'user'])
            ->latest();

        if ($user->role === 'office' && $user->office_id) {
            $query->whereHas('citizenRequest', fn($q) => $q->where('office_id', $user->office_id));
        }

        $ratings = $query->get();
        $avgStars = $ratings->avg('stars');

        return View::make('office.ratings.index', compact('ratings', 'avgStars'));
    }

    // Office staff: respond to a rating
    public function respond(CitizenRequestRating $rating, Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'office' && $user->office_id) {
            $officeId = $rating->citizenRequest->office_id ?? null;
            if ($officeId !== $user->office_id) abort(403);
        }

        $request->validate(['office_response' => ['required', 'string', 'max:1000']]);

        $rating->update([
            'office_response' => $request->input('office_response'),
            'responded_at'    => now(),
        ]);

        return Redirect::back()->with('success', 'Response saved.');
    }
}
