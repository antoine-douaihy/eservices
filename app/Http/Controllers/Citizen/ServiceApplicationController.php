<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\CitizenRequest;
use App\Models\Office;
use App\Models\Service;
use App\Models\ServiceApplication;
use App\Rules\LebanesePhoneNumber;
use App\Rules\ValidFileSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceApplicationController extends Controller
{
    // ────────────────────────────────────────────────
    //  STEP 0 — Browse all available services
    // ────────────────────────────────────────────────
    public function browse(Request $request)
    {
        $query = Service::with(['office.municipality', 'requiredDocuments'])
                        ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('office_id')) {
            // Citizen explicitly wants a specific office's listing —
            // each office already has at most one row per service, so
            // no deduplication is needed here.
            $query->where('office_id', $request->office_id);
        } else {
            // Every service now exists once per active office (every
            // municipality), but the catalog should show one card per
            // distinct service, not one per office copy — citizens pick
            // or auto-detect their office later, inside the apply flow.
            $representativeIds = Service::where('is_active', true)
                ->selectRaw('MIN(id) as id')
                ->groupBy(DB::raw("COALESCE(group_uuid, CONCAT('solo-', id))"))
                ->pluck('id');

            $query->whereIn('id', $representativeIds);
        }

        $services = $query->latest()->paginate(12)->withQueryString();
        $offices  = Office::where('is_active', true)->orderBy('name')->get();

        return view('citizen.services.browse', compact('services', 'offices'));
    }

    // ────────────────────────────────────────────────
    //  STEP 1 — Show application form for a service
    // ────────────────────────────────────────────────
    public function apply(Service $service)
    {
        if (!$service->is_active) {
            return redirect()->route('citizen.services.browse')
                             ->with('error', 'This service is currently unavailable.');
        }

        $service->load(['requiredDocuments', 'office.municipality']);

        $officesWithService = Office::where('is_active', true)
            ->whereHas('services', fn($q) => $q->where('name', $service->name))
            ->get(['id', 'name', 'address', 'latitude', 'longitude']);

        $officesJson = $officesWithService->map(function($o) {
            return [
                'id'   => $o->id,
                'name' => $o->name,
                'lat'  => $o->latitude,
                'lng'  => $o->longitude,
            ];
        })->values();

        return view('citizen.services.apply', compact('service', 'officesWithService', 'officesJson'));
    }

    // ────────────────────────────────────────────────
    //  STEP 2 — Store the application
    // ────────────────────────────────────────────────
    public function store(Request $request, Service $service)
    {
        $rules = [
            'full_name'   => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-]+$/u'],
            'phone'       => ['required', new LebanesePhoneNumber],
            'email'       => ['required', 'email:rfc', 'max:255'],
            'address'     => ['required', 'string', 'max:500'],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'citizen_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'citizen_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'office_id'   => ['required', 'exists:offices,id'],
        ];

        // Add file validation for each required document — extension/MIME check
        // plus a magic-byte signature check so a disguised file can't slip through.
        foreach ($service->requiredDocuments as $doc) {
            $fieldKey = 'doc_' . $doc->id;
            if ($doc->is_mandatory) {
                $rules[$fieldKey] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120', new ValidFileSignature];
            } else {
                $rules[$fieldKey] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120', new ValidFileSignature];
            }
        }

        $validated = $request->validate($rules, [
            'full_name.required' => 'Please enter your full name.',
            'full_name.regex'    => 'Full name may only contain letters, spaces, and hyphens.',
            'phone.required'     => 'Please enter your phone number.',
            'email.required'     => 'Please enter your email address.',
            'address.required'   => 'Please enter your address.',
            'office_id.required' => 'Please select an office.',
        ]);

        DB::beginTransaction();
        try {
            // If coordinates were provided, find nearest office
            $officeId = $validated['office_id'];
            if (!empty($validated['citizen_lat']) && !empty($validated['citizen_lng'])) {
                $nearest = $this->findNearestOffice(
                    $validated['citizen_lat'],
                    $validated['citizen_lng'],
                    $service->name
                );
                if ($nearest) {
                    $officeId = $nearest->id;
                }
            }

            // Create the application
            $application = ServiceApplication::create([
                'reference_number' => ServiceApplication::generateReference(),
                'user_id'          => Auth::id(),
                'service_id'       => $service->id,
                'office_id'        => $officeId,
                'full_name'        => $validated['full_name'],
                'phone'            => $validated['phone'],
                'email'            => $validated['email'],
                'address'          => $validated['address'],
                'notes'            => $validated['notes'] ?? null,
                'citizen_lat'      => $validated['citizen_lat'] ?? null,
                'citizen_lng'      => $validated['citizen_lng'] ?? null,
                'status'           => 'pending',
                'submitted_at'     => now(),
            ]);

            // Store uploaded documents — encrypted at rest (AES-256) and
            // kept off the public disk since these are sensitive citizen
            // supporting documents (IDs, proofs of address, etc.).
            foreach ($service->requiredDocuments as $doc) {
                $fieldKey = 'doc_' . $doc->id;
                if ($request->hasFile($fieldKey)) {
                    $file = $request->file($fieldKey);
                    $path = \App\Services\EncryptedFileStorage::store(
                        $file,
                        'application_documents/' . $application->id,
                        'private'
                    );

                    ApplicationDocument::create([
                        'application_id'       => $application->id,
                        'required_document_id' => $doc->id,
                        'document_name'        => $doc->name,
                        'file_path'            => $path,
                        'file_original_name'   => $file->getClientOriginalName(),
                        'mime_type'            => $file->getMimeType(),
                        'file_size'            => $file->getSize(),
                    ]);
                }
            }

            // Create CitizenRequest inside the transaction so everything rolls back together
            $citizenRequest = CitizenRequest::create([
                'user_id'      => Auth::id(),
                'service_id'   => $service->id,
                'office_id'    => $officeId,
                'full_name'    => $validated['full_name'],
                'phone'        => $validated['phone'],
                'email'        => $validated['email'],
                'address'      => $validated['address'],
                'notes'        => $validated['notes'] ?? null,
                'status'       => $service->price > 0 ? 'pending_payment' : 'pending',
                'submitted_at' => now(),
            ]);

            // Link to application
            $application->update(['citizen_request_id' => $citizenRequest->id]);

            DB::commit();

            try {
                Auth::user()->notify(new \App\Notifications\RequestSubmitted($citizenRequest->fresh()));
            } catch (\Exception $e) {}

            if ($service->price > 0) {
                return redirect()->route('citizen.payment.select', $citizenRequest)
                                 ->with('success', 'Application submitted! Please complete payment.');
            } else {
                return redirect()->route('citizen.applications.success', $application)
                                 ->with('success', 'Application submitted successfully!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                         ->with('error', 'Something went wrong. Please try again.');
        }
    }

    // ────────────────────────────────────────────────
    //  Success page
    // ────────────────────────────────────────────────
    public function success(ServiceApplication $application)
    {
        // Make sure the citizen can only see their own applications
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $application->load(['service', 'office.municipality', 'documents']);
        return view('citizen.services.success', compact('application'));
    }

    // ────────────────────────────────────────────────
    //  Citizen's application history
    // ────────────────────────────────────────────────
    public function myApplications()
    {
        $applications = ServiceApplication::with(['service', 'office', 'citizenRequest'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('citizen.applications.index', compact('applications'));
    }

    // ────────────────────────────────────────────────
    //  Payment selection
    // ────────────────────────────────────────────────
    public function selectPayment(CitizenRequest $citizenRequest)
    {
        if ($citizenRequest->user_id !== Auth::id()) {
            abort(403);
        }

        $citizenRequest->load(['service', 'office']);
        return view('citizen.payment.select', compact('citizenRequest'));
    }

    // ────────────────────────────────────────────────
    //  NEAREST OFFICE LOGIC (Haversine formula)
    // ────────────────────────────────────────────────
    private function findNearestOffice(float $lat, float $lng, string $serviceName): ?Office
    {
        // Get all active offices that offer this service and have coordinates
        $offices = Office::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereHas('services', fn($q) => $q->where('name', $serviceName)->where('is_active', true))
            ->get();

 