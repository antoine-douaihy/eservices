<?php

namespace App\Events;

use App\Models\ServiceRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceRequest $service) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('chat.' . $this->service->id)];
    }

    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'uuid'          => $this->service->uuid,
            'tracking_code' => $this->service->tracking_code,
            'status'        => $this->service->status,
            'title'         => $this->service->title,
        ];
    }
}
