<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->take(15)->get()->map(fn($n) => [
            'id'   => $n->id,
            'data' => $n->data,
            'read' => !is_null($n->read_at),
            'time' => $n->created_at->diffForHumans(),
        ]);

        return response()->json([
            'notifications' => $notifications,
            'unread'        => $user->unreadNotifications()->count(),
        ]);
    }

    public function markRead($id)
    {
        Auth::user()->notifications()->findOrFail($id)->markAsRead();
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }
}
