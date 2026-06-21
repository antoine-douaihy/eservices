<?php

use App\Models\CitizenRequest;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Broadcast;

// Legacy channel
Broadcast::channel('chat.{serviceRequestId}', function ($user, $serviceRequestId) {
    $request = ServiceRequest::find($serviceRequestId);
    if (!$request) return false;
    return (int) $user->id === (int) $request->user_id || in_array($user->role, ['admin', 'office']);
});

// CitizenRequest real-time chat channel
Broadcast::channel('citizen-request.{requestId}', function ($user, $requestId) {
    $cr = CitizenRequest::find($requestId);
    if (!$cr) return false;
    return (int) $user->id === (int) $cr->user_id || in_array($user->role, ['admin', 'office']);
});
