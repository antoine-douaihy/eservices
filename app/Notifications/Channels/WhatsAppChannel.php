<?php

namespace App\Notifications\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $phone = $notifiable->phone ?? null;
        if (empty($phone)) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        if (empty($message)) {
            return;
        }

        WhatsAppService::send($phone, $message);
    }
}
