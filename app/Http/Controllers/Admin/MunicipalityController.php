<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $municipalities = Municipality::withCount('offices')->orderBy('name')->get();
        return view('admin.municipalities.index', compact('municipalities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:municipalities,name',
            'region'      => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'is_active'   => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        Municipality::create($validated);
        return back()->with('success', 'Municipality created successfully.');
    }

    public function update(Request $request, Municipality $municipality)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:municipalities,name,' . $municipality->id,
            'region'      => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'is_active'   => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $municipality->update($validated);
        return back()->with('success', 'Municipality updated successfully.');
    }

    public function destroy(Municipality $municipality)
    {
        if ($municipality->offices()->exists()) {
            return back()->with('error', 'Cannot delete "' . $municipality->name . '" — it still has offices assigned.');
        }
        $municipality->delete();
        return back()->with('success', 'Municipality deleted successfully.');
    }
}
