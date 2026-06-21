<?php

namespace App\Events;

use App\Models\CitizenRequestMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(public CitizenRequestMessage $message)
    {
        $this->payload = [
            'id'        => $message->id,
            'content'   => $message->content,
            'sender'    => $message->user->first_name ?? 'User',
            'time'      => $message->created_at->format('H:i'),
            'date'      => $message->created_at->format('d M Y'),
            'is_office' => $message->is_office,
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('citizen-request.' . $this->message->citizen_request_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
