<?php

namespace App\Http\Controllers;

use App\Models\CitizenRequest;
use App\Models\Office;
use App\Models\Service;
use App\Notifications\RequestStatusChanged;
use App\Rules\ValidFileSignature;
use App\Services\EncryptedFileStorage;
use App\Services\PdfGenerator;
use App\Support\LaravelRequest as Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class CitizenRequestController extends Controller
{
    // Citizen's home dashboard — shows their own requests
    public function index()
    {
        $requests = CitizenRequest::with(['service', 'office'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $services = Service::where('is_active', true)->with('office')->get();

        return View::make('home', compact('requests', 'services'));
    }

    // Citizen: apply form
    public function create()
    {
        $offices  = Office::all();
        $services = Service::with('office')->get();
        return View::make('requests.create', compact('offices', 'services'));
    }

    // Citizen: submit application with file upload
    public function store(Request $request)
    {
        $request->validate([
            'office_id'   => ['required', 'exists:offices,id'],
            'service_id'  => ['required', 'exists:services,id'],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'documents'   => ['nullable', 'array', 'max:10'],
            'documents.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120', new ValidFileSignature],
        ]);

        $uploadedPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                // Encrypted at rest (AES-256) — decrypted on demand via the
                // documents.show route, never served as a raw public file.
                $uploadedPaths[] = EncryptedFileStorage::store($file, 'documents', 'private');
            }
        }

        CitizenRequest::create([
            'user_id'           => Auth::id(),
            'office_id'         => $request->input('office_id'),
            'service_id'        => $request->input('service_id'),
            'notes'             => $request->input('notes'),
            'uploaded_document' => !empty($uploadedPaths) ? json_encode($uploadedPaths) : null,
            'status'            => 'pending',
        ]);

        return Redirect::route('citizen.my-requests')
            ->with('success', 'Your request has been submitted successfully.');
    }

    // Citizen: view their own request list (with search + status filter)
    public function myRequests(Request $request)
    {
        $query = CitizenRequest::with(['service', 'office', 'rating'])
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('service', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('office',  fn($o) => $o->where('name', 'like', "%{$search}%"));
            });
        }

        $requests = $query->latest()->get();
        return View::make('requests.my-requests', compact('requests'));
    }

    // Citizen: show resubmit form (pre-filled from rejected request)
    public function resubmit(CitizenRequest $citizenRequest)
    {
        if ($citizenRequest->user_id !== Auth::id()) {
            abort(403);
        }
        if ($citizenRequest->status !== 'rejected') {
            return Redirect::route('citizen.my-requests')->with('error', 'Only rejected requests can be resubmitted.');
        }

        $offices  = Office::all();
        $services = Service::with('office')->get();
        return View::make('requests.resubmit', compact('citizenRequest', 'offices', 'services'));
    }

    // Citizen: submit resubmission
    public function resubmitStore(Request $request, CitizenRequest $citizenRequest)
    {
        if ($citizenRequest->user_id !== Auth::id()) {
            abort(403);
        }
        if ($citizenRequest->status !== 'rejected') {
            return Redirect::route('citizen.my-requests')->with('error', 'Only rejected requests can be resubmitted.');
        }

        $request->validate([
            'office_id'   => ['required', 'exists:offices,id'],
            'service_id'  => ['required', 'exists:services,id'],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'documents'   => ['nullable', 'array', 'max:10'],
            'documents.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120', new ValidFileSignature],
        ]);

        $uploadedPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $uploadedPaths[] = EncryptedFileStorage::store($file, 'documents', 'private');
            }
        }

        $newRequest = CitizenRequest::create([
            'user_id'           => Auth::id(),
            'office_id'         => $request->input('office_id'),
            'service_id'        => $request->input('service_id'),
            'notes'             => $request->input('notes'),
            'uploaded_document' => !empty($uploadedPaths) ? json_encode($uploadedPaths) : null,
            'status'            => 'pending',
        ]);

        $newRequest->logHistory('pending', Auth::id(), 'Resubmitted from rejected request #' . $citizenRequest->id);

        return Redirect::route('citizen.my-requests')
            ->with('success', 'Your request has been resubmitted successfully.');
    }

    // Decrypt and stream an uploaded document — only the owning citizen,
    // the assigned office's staff, or an admin may view it.
    public function serveDocument(CitizenRequest $citizenRequest, int $index)
    {
        $user = Auth::user();
        $isOwner = $citizenRequest->user_id === $user->id;
        $isAssignedStaff = $user->role === 'office' && $user->office_id === $citizenRequest->office_id;
        $isAdmin = $user->role === 'admin';

        if (!$isOwner && !$isAssignedStaff && !$isAdmin) {
            abort(403);
        }

        $paths = json_decode($citizenRequest->uploaded_document ?? '[]', true) ?? [];
        if (!isset($paths[$index])) {
            abort(404);
        }

        $contents = EncryptedFileStorage::retrieve($paths[$index], 'private');

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($contents) ?: 'application/octet-stream';

        return response($contents, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="document-' . ($index + 1) . '"');
    }

    // Office staff: update status of a CitizenRequest assigned to their office
    public function officeUpdateStatus(CitizenRequest $citizenRequest, Request $request)
    {
        $user = Auth::user();

        // Office staff can only touch requests belonging to their office
        if ($user->role === 'office' && $user->office_id && $citizenRequest->office_id !== $user->office_id) {
            abort(403);
        }

        $allowed = ['in_review', 'missing_documents', 'rejected'];
        $newStatus = $request->input('status');

        if (!in_array($newStatus, $allowed)) {
            return Redirect::back()->with('error', 'Invalid status transition.');
        }

        $note = $request->input('note');
        $citizenRequest->update(['status' => $newStatus]);
        $citizenRequest->logHistory($newStatus, $user->id, $note);

        try {
            $citizenRequest->user->notify(new RequestStatusChanged($citizenRequest->fresh(), $newStatus, $note));
        } catch (\Exception $e) {}

        return Redirect::back()->with('success', 'Request status updated.');
    }

    // Office staff: upload an official response document
    public function uploadResponse(CitizenRequest $citizenRequest, Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'office' && $user->office_id && $citizenRequest->office_id !== $user->office_id) {
            abort(403);
        }

        $request->validate([
            'response_document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120', new ValidFileSignature],
            'response_note'     => ['nullable', 'string', 'max:1000'],
        ]);

        $path = $request->file('response_document')->store('response_documents', 'public');

        $citizenRequest->update([
            'response_document' => $path,
            'response_note'     => $request->input('response_note'),
        ]);

        $citizenRequest->logHistory($citizenRequest->status, $user->id, 'Response document uploaded');

        return Redirect::back()->with('success', 'Response document uploaded successfully.');
    }

    // Admin: view all requests grouped by date (Person C interface)
    public function adminIndex(Request $request)
    {
        $query = CitizenRequest::with(['user', 'service', 'office', 'localPayments', 'cryptoTransactions', 'histories.user'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $requests = $query->get()
            ->groupBy(fn($req) => $req->created_at
                ? $req->created_at->format('d M Y')
                : 'Unknown Date');

        return View::make('requests.index', compact('requests'));
    }

    // Admin: approve and generate certificate
    public function approve(CitizenRequest $citizenRequest, PdfGenerator $pdfGenerator)
    {
        $citizenRequest->update([
            'status'      => 'approved',
            'approved_at' => now(),
        ]);

        $citizenRequest->logHistory('approved', Auth::id());

        $pdf      = $pdfGenerator->loadView('pdf.certificate', ['request' => $citizenRequest]);
        $filename = 'certificate_' . $citizenRequest->id . '_' . time() . '.pdf';

        // Write via the Storage facade (not a raw filesystem path) so this
        // works whether the "public" disk is local or S3/R2-backed.
        Storage::disk('public')->put('certificates/' . $filename, $pdf->output());
        $citizenRequest->update(['certificate_path' => 'certificates/' . $filename]);

        if ($citizenRequest->serviceApplication) {
            $citizenRequest->serviceApplication->update([
                'status'           => 'completed',
                'approved_at'      => now(),
                'certificate_path' => 'certificates/' . $filename,
            ]);
        }

        // Notify the citizen
        try {
            $citizenRequest->user->notify(new RequestStatusChanged($citizenRequest->fresh(), 'approved'));
        } catch (\Exception $e) {
            // Mail failures must not block the approval flow
        }

        return Redirect::back()->with('success', 'Request approved and certificate generated.');
    }

    // Admin: reject
    public function reject(CitizenRequest $citizenRequest, Request $request)
    {
        $note = $request->input('rejection_note');

        $citizenRequest->update(['status' => 'rejected']);
        $citizenRequest->logHistory('rejected', Auth::id(), $note);

        try {
            $citizenRequest->user->notify(new RequestStatusChanged($citizenRequest->fresh(), 'rejected', $note));
        } catch (\Exception $e) {
            // Mail failures must not block the rejection flow
        }

        return Redirect::back()->with('success', 'Request rejected.');
    }

    // Admin: bulk approve or reject selected requests
    public function bulkAction(Request $request, PdfGenerator $pdfGenerator)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['integer', 'exists:citizen_requests,id'],
        ]);

        $ids    = $request->input('ids');
        $action = $request->input('action');

        $toProcess = CitizenRequest::with(['user', 'service', 'serviceApplication'])
            ->whereIn('id', $ids)
            ->whereIn('status', ['pending', 'in_review'])
            ->get();

        foreach ($toProcess as $cr) {
            if ($action === 'approve') {
                $cr->update(['status' => 'approved', 'approved_at' => now()]);
                $cr->logHistory('approved', Auth::id(), 'Bulk approval');

                try {
                    $pdf      = $pdfGenerator->loadView('pdf.certificate', ['request' => $cr]);
                    $filename = 'certificate_' . $cr->id . '_' . time() . '.pdf';
                    Storage::disk('public')->put('certificates/' . $filename, $pdf->output());
                    $cr->update(['certificate_path' => 'certificates/' . $filename]);
                    if ($cr->serviceApplication) {
                        $cr->serviceApplication->update(['status' => 'completed', 'approved_at' => now(), 'certificate_path' => 'certificates/' . $filename]);
                    }
                } catch (\Exception $e) {}

                try { $cr->user->notify(new RequestStatusChanged($cr->fresh(), 'approved')); } catch (\Exception $e) {}

            } else {
                $cr->update(['status' => 'rejected']);
                $cr->logHistory('rejected', Auth::id(), 'Bulk rejection');
                try { $cr->user->notify(new RequestStatusChanged($cr->fresh(), 'rejected')); } catch (\Exception $e) {}
            }
        }

        $count = $toProcess->count();
        return Redirect::back()->with('success', "{$count} request(s) {$action}d successfully.");
    }

    // Admin or citizen owner: download certificate
    public function downloadCertificate(CitizenRequest $citizenRequest, PdfGenerator $pdfGenerator)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $citizenRequest->user_id !== $user->id) {
            return Redirect::route('login');
        }

        $pdf      = $pdfGenerator->loadView('pdf.certificate', ['request' => $citizenRequest]);
        $filename = 'Certificate_' . $citizenRequest->user->first_name . '_' . $citizenRequest->id . '.pdf';
        return $pdf->download($filename);
    }

    // Citizen or admin: download payment receipt PDF
    public function paymentReceipt(CitizenRequest $citizenRequest, PdfGenerator $pdfGenerator)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $citizenRequest->user_id !== $user->id) {
            abort(403);
        }

        $citizenRequest->load(['user', 'service', 'office', 'localPayments', 'cryptoTransactions']);

        $pdf      = $pdfGenerator->loadView('pdf.payment-receipt', ['cr' => $citizenRequest]);
        $filename = 'Receipt_' . $citizenRequest->id . '.pdf';
        return $pdf->download($filename);
    }
}
