<?php

namespace App\Notifications;

use App\Models\CitizenRequest;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(public CitizenRequest $citizenRequest) {}

    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];
        if (!empty($notifiable->phone)) {
            $channels[] = WhatsAppChannel::class;
        }
        return $channels;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Request #{$this->citizenRequest->id} Submitted",
            'body'  => "Your application for {$this->citizenRequest->service->name} has been received.",
            'url'   => route('citizen.my-requests'),
            'icon'  => 'bi-send-fill',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Request #{$this->citizenRequest->id} Submitted — E-Services Platform")
            ->greeting("Hello, {$notifiable->first_name}!")
            ->line("Your application for **{$this->citizenRequest->service->name}** has been received.")
            ->line("**Reference:** #{$this->citizenRequest->id}")
            ->line("**Office:** {$this->citizenRequest->office->name}")
            ->action('View My Requests', route('citizen.my-requests'))
            ->line('We will notify you as your request progresses.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "E-Services Platform\n\n"
            . "Request Submitted ✅\n"
            . "Reference: #{$this->citizenRequest->id}\n"
            . "Service: {$this->citizenRequest->service->name}\n"
            . "Office: {$this->citizenRequest->office->name}\n\n"
            . "We'll notify you here as it progresses.";
    }
}
