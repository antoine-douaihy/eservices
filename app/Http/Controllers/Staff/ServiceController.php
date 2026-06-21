<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\RequiredDocument;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display all services for the staff's office(s).
     */
    public function index(Request $request)
    {
        $query = Service::with(['office', 'requiredDocuments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $services = $query->latest()->paginate(10)->withQueryString();
        $offices   = Office::where('is_active', true)->orderBy('name')->get();

        return view('staff.services.index', compact('services', 'offices'));
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        $offices    = Office::where('is_active', true)->orderBy('name')->get();
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        return view('staff.services.create', compact('offices', 'categories'));
    }

    /**
     * Store a new service with its required documents.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'name_ar'         => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'description_ar'  => 'nullable|string|max:2000',
            'price'           => 'required|numeric|min:0',
            'currency'        => 'required|string|max:10',
            'processing_days' => 'required|integer|min:1',
            'office_id'       => 'required|exists:offices,id',
            'category_id'     => 'nullable|exists:service_categories,id',
            'is_active'       => 'boolean',

            // Documents array
            'documents'               => 'nullable|array',
            'documents.*.name'        => 'required_with:documents|string|max:255',
            'documents.*.name_ar'     => 'nullable|string|max:255',
            'documents.*.notes'       => 'nullable|string|max:500',
            'documents.*.is_mandatory' => 'nullable|in:0,1,true,false,on',
        ]);

        // Every service is offered at every active office (every
        // municipality) — the office picked in the form is just the
        // citizen-facing default, not a restriction. One Service row is
        // created per active office, all linked by a shared group_uuid
        // so edits/deletes can stay in sync across all of them.
        $groupUuid = (string) Str::uuid();
        $offices = Office::where('is_active', true)->get();
        if ($offices->isEmpty()) {
            $offices = collect([Office::find($validated['office_id'])]);
        }

        $createdService = null;

        DB::transaction(function () use ($validated, $request, $groupUuid, $offices, &$createdService) {
            foreach ($offices as $office) {
                $service = Service::create([
                    'name'            => $validated['name'],
                    'name_ar'         => $validated['name_ar'] ?? null,
                    'slug'            => Str::slug($validated['name']) . '-' . Str::random(5),
                    'description'     => $validated['description'] ?? null,
                    'description_ar'  => $validated['description_ar'] ?? null,
                    'price'           => $validated['price'],
                    'currency'        => $validated['currency'],
                    'processing_days' => $validated['processing_days'],
                    'office_id'       => $office->id,
                    'category_id'     => $validated['category_id'] ?? null,
                    'group_uuid'      => $groupUuid,
                    'is_active'       => $request->boolean('is_active', true),
                ]);

                if (!empty($validated['documents'])) {
                    foreach ($validated['documents'] as $index => $doc) {
                        if (!empty($doc['name'])) {
                            $service->requiredDocuments()->create([
                                'name'         => $doc['name'],
                                'name_ar'      => $doc['name_ar'] ?? null,
                                'notes'        => $doc['notes'] ?? null,
                                'is_mandatory' => !empty($doc['is_mandatory']),
                                'sort_order'   => $index,
                            ]);
                        }
                    }
                }

                if ($office->id == $validated['office_id']) {
                    $createdService = $service;
                }
            }
        });

        $officeCount = $offices->count();
        return redirect()->route('staff.services.index')
                         ->with('success', "Service \"{$validated['name']}\" created and made available at all {$officeCount} active office(s).");
    }

    /**
     * Show the edit form.
     */
    public function edit(Service $service)
    {
        $service->load('requiredDocuments');
        $offices    = Office::where('is_active', true)->orderBy('name')->get();
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        return view('staff.services.edit', compact('service', 'offices', 'categories'));
    }

    /**
     * Update service and sync required documents. Since this service is
     * offered at every municipality (one row per office, linked by
     * group_uuid), the shared details (name, price, documents, etc.) are
     * propagated to every sibling row so all offices stay consistent.
     * Each sibling keeps its own office_id — editing here never moves or
     * removes a service from any individual office.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'name_ar'         => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'description_ar'  => 'nullable|string|max:2000',
            'price'           => 'required|numeric|min:0',
            'currency'        => 'required|string|max:10',
            'processing_days' => 'required|integer|min:1',
            'office_id'       => 'required|exists:offices,id',
            'category_