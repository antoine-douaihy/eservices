<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use App\Models\Office;
use App\Rules\LebanesePhoneNumber;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    /**
     * Display a listing of all offices.
     */
    public function index(Request $request)
    {
        $query = Office::with('municipality');

        // Live search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by municipality
        if ($request->filled('municipality_id')) {
            $query->where('municipality_id', $request->municipality_id);
        }

        $offices        = $query->latest()->paginate(12)->withQueryString();
        $municipalities = Municipality::where('is_active', true)->orderBy('name')->get();

        return view('admin.offices.index', compact('offices', 'municipalities'));
    }

    /**
     * Show the form for creating a new office.
     */
    public function create()
    {
        $municipalities = Municipality::where('is_active', true)->orderBy('name')->get();
        return view('admin.offices.create', compact('municipalities'));
    }

    /**
     * Store a newly created office in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:50|unique:offices,code',
            'description'     => 'nullable|string|max:1000',
            'address'         => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:100',
            'phone'           => ['nullable', new LebanesePhoneNumber],
            'email'           => 'nullable|email|max:255',
            'municipality_id' => 'required|exists:municipalities,id',
            'is_active'       => 'boolean',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'opening_time'    => 'nullable|string|max:10',
            'closing_time'    => 'nullable|string|max:10',
            'working_days'    => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Office::create($validated);

        return redirect()->route('admin.offices.index')
                         ->with('success', 'Office created successfully.');
    }

    /**
     * Show the form for editing the specified office.
     */
    public function edit(Office $office)
    {
        $municipalities = Municipality::where('is_active', true)->orderBy('name')->get();
        return view('admin.offices.edit', compact('office', 'municipalities'));
    }

    /**
     * Update the specified office in storage.
     */
    public function update(Request $request, Office $office)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:50|unique:offices,code,' . $office->id,
            'description'     => 'nullable|string|max:1000',
            'address'         => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:100',
            'phone'           => ['nullable', new LebanesePhoneNumber],
            'email'           => 'nullable|email|max:255',
            'municipality_id' => 'required|exists:municipalities,id',
            'is_active'       => 'boolean',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'opening_time'    => 'nullable|string|max:10',
            'closing_time'    => 'nullable|string|max:10',
            'working_days'    => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $office->update($validated);

        return redirect()->route('admin.offices.index')
                         ->with('success', 'Office updated successfully.');
    }

    /**
     * Remove the specified office from storage.
     */
    public function destroy(Office $office)
    {
        $office->delete();

        return redirect()->route('admin.offices.index')
                         ->with('success', 'Office deleted successfully.');
    }
}
