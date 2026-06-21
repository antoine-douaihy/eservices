<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\CitizenRequest;
use App\Models\CitizenRequestMessage;
use App\Support\LaravelRequest as Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class CitizenRequestMessageController extends Controller
{
    // Office staff: view + send messages for a specific request
    public function show(CitizenRequest $citizenRequest)
    {
        $this->authorizeOffice($citizenRequest);
        $citizenRequest->load(['messages.user', 'user', 'service', 'office']);
        return View::make('office.requests.chat', compact('citizenRequest'));
    }

    public function store(CitizenRequest $citizenRequest, Request $request)
    {
        $this->authorizeOffice($citizenRequest);
        $request->validate(['content' => ['required', 'string', 'max:2000']]);

        $msg = CitizenRequestMessage::create([
            'citizen_request_id' => $citizenRequest->id,
            'user_id'            => Auth::id(),
            'content'            => $request->input('content'),
            'is_office'          => true,
        ]);

        $msg->load('user');
        try { broadcast(new ChatMessageSent($msg))->toOthers(); } catch (\Throwable $e) {}

        return response()->json([
            'id'        => $msg->id,
            'content'   => $msg->content,
            'sender'    => Auth::user()->first_name,
            'time'      => $msg->created_at->format('H:i'),
            'date'      => $msg->created_at->format('d M Y'),
            'is_office' => true,
        ]);
    }

    // Office staff: fetch messages as JSON (for polling)
    public function officeMessages(CitizenRequest $citizenRequest, Request $request)
    {
        $this->authorizeOffice($citizenRequest);
        $query = $citizenRequest->messages()->with('user')->orderBy('id');
        if ($request->filled('after')) {
            $query->where('id', '>', $request->integer('after'));
        }
        return response()->json($query->get()->map(fn($m) => [
            'id'        => $m->id,
            'content'   => $m->content,
            'sender'    => $m->user->first_name ?? 'Staff',
            'time'      => $m->created_at->format('H:i'),
            'date'      => $m->created_at->format('d M Y'),
            'is_office' => $m->is_office,
        ]));
    }

    // Citizen: view + send messages for their own request
    public function citizenShow(CitizenRequest $citizenRequest)
    {
        if ($citizenRequest->user_id !== Auth::id()) abort(403);
        $citizenRequest->load(['messages.user', 'service', 'office']);
        return View::make('requests.chat', compact('citizenRequest'));
    }

    public function citizenSend(CitizenRequest $citizenRequest, Request $request)
    {
        if ($citizenRequest->user_id !== Auth::id()) abort(403);
        $request->validate(['content' => ['required', 'string', 'max:2000']]);

        $msg = CitizenRequestMessage::create([
            'citizen_request_id' => $citizenRequest->id,
            'user_id'            => Auth::id(),
            'content'            => $request->input('content'),
            'is_office'          => false,
        ]);

        $msg->load('user');
        try { broadcast(new ChatMessageSent($msg))->toOthers(); } catch (\Throwable $e) {}

        return response()->json([
            'id'        => $msg->id,
            'content'   => $msg->content,
            'sender'    => Auth::user()->first_name,
            'time'      => $msg->created_at->format('H:i'),
            'date'      => $msg->created_at->format('d M Y'),
            'is_office' => false,
        ]);
    }

    // Citizen: fetch messages as JSON (for polling)
    public function citizenMessages(CitizenRequest $citizenRequest, Request $request)
    {
        if ($citizenRequest->user_id !== Auth::id()) abort(403);
        $query = $citizenRequest->messages()->with('user')->orderBy('id');
        if ($request->filled('after')) {
            $query->where('id', '>', $request->integer('after'));
        }
        return response()->json($query->get()->map(fn($m) => [
            'id'        => $m->id,
            'content'   => $m->content,
            'sender'    => $m->user->first_name ?? 'Staff',
            'time'      => $m->created_at->format('H:i'),
            'date'      => $m->created_at->format('d M Y'),
            'is_office' => $m->is_office,
        ]));
    }

    private function authorizeOffice(CitizenRequest $cr): void
    {
        $user = Auth::user();
        if ($user->role === 'office' && $user->office_id && $cr->office_id !== $user->office_id) {
            abort(403);
        }
    }
}
