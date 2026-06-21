<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        // Load the user relationship so the chat shows the sender's name immediately
        $this->message->load('user');
    }

    public function broadcastOn(): array
    {
        // This ensures only users involved in this specific request can see the chat
        return [
            new PrivateChannel('chat.' . $this->message->service_request_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.new';
    }
}