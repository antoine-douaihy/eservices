<?php

namespace App\Http\Controllers;

use App\Models\CitizenRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TrackingController extends Controller
{
    public function index($uuid)
    {
        $citizenRequest = CitizenRequest::with(['service', 'office'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $qrCode = QrCode::size(200)
            ->color(0, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->generate(route('track.show', $uuid));

        return view('tracking.status', compact('citizenRequest', 'qrCode'));
    }
}
