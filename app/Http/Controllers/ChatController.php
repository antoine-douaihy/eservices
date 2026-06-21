<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\ServiceRequest;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Store a new message and broadcast it.
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // 1. Save to Database
        $message = Message::create([
            'service_request_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->content
        ]);

        // 2. Broadcast to Pusher
        // toOthers() prevents the sender from receiving their own message twice
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status'  => 'Message Sent!',
            'message' => $message->load('user'),
        ]);
    }

    public function getMessages($id)
    {
        $service = ServiceRequest::findOrFail($id);

        if (Auth::user()->role === 'citizen' && $service->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json(
            $service->messages()->with('user')->get()
        );
    }
}