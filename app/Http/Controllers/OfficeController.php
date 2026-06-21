<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\ServiceRequest;
use App\Rules\LebanesePhoneNumber;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,office']);
    }

    public function dashboard(Request $request)
    {
        $query = ServiceRequest::with(['user', 'messages', 'rating'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('tracking_code', 'like', $term)
                  ->orWhere('title', 'like', $term)
                  ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', $term)
                      ->orWhere('last_name', 'like', $term)
                      ->orWhere('email', 'like', $term));
            });
        }

        $requests = $query->get();

        $stats = [
            'pending'     => ServiceRequest::where('status', 'pending')->count(),
            'in_progress' => ServiceRequest::where('status', 'in_progress')->count(),
            'completed'   => ServiceRequest::where('status', 'completed')->count(),
        ];

        return view('office.dashboard', compact('requests', 'stats'));
    }

    public function index()
    {
        $offices = Office::latest()->get();
        return view('offices.index', compact('offices'));
    }

    public function create()
    {
        return view('offices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'address'         => ['required', 'string', 'max:500'],
            'city'            => ['nullable', 'string', 'max:100'],
            'phone'           => ['nullable', new LebanesePhoneNumber],
            'email'           => ['nullable', 'email', 'max:255'],
            'latitude'        => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'       => ['nullable', 'numeric', 'between:-180,180'],
            'municipality_id' => ['nullable', 'exists:municipalities,id'],
        ]);

        Office::create($validated);

        return redirect()->route('offices.index')
            ->with('success', 'Office created successfully.');
    }

    public function show(Office $office)
    {
        return view('offices.show', compact('office'));
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return redirect()->route('offices.index')
            ->with('success', 'Office deleted.');
    }
}
