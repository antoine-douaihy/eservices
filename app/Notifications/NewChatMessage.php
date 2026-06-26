<?php

namespace App\Notifications;

use App\Models\CitizenRequest;
use App\Models\CitizenRequestMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessage extends Notification
{
    use Queueable;

    public function __construct(
        public CitizenRequestMessage $message,
        public CitizenRequest $citizenRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $serviceName = $this->citizenRequest->service?->name ?? 'Service Request';
        $senderName  = $this->message->user?->first_name ?? ($this->message->is_office ? 'Office Staff' : 'Citizen');
        $preview     = \Str::limit($this->message->content, 120);

        $chatUrl = $this->message->is_office
            ? route('citizen.requests.chat', $this->citizenRequest)
            : route('office.requests.chat',  $this->citizenRequest);

        return (new MailMessage)
            ->subject("New message on your request — {$serviceName}")
            ->greeting("Hello, {$notifiable->first_name}!")
            ->line("You have a new message on your **{$serviceName}** request (#{$this->citizenRequest->id}).")
            ->line("**{$senderName}:** {$preview}")
            ->action('Open Chat', $chatUrl)
            ->line('Reply directly from the platform to keep your conversation going.');
    }

    public function toArray(object $notifiable): array
    {
        $serviceName = $this->citizenRequest->service?->name ?? 'Service Request';
        $senderName  = $this->message->user?->first_name ?? ($this->message->is_office ? 'Office Staff' : 'Citizen');

        $chatUrl = $this->message->is_office
            ? route('citizen.requests.chat', $this->citizenRequest)
            : route('office.requests.chat',  $this->citizenRequest);

        return [
            'title' => "New message — {$serviceName}",
            'body'  => "{$senderName}: " . \Str::limit($this->message->content, 80),
            'url'   => $chatUrl,
            'icon'  => 'bi-chat-text-fill',
        ];
    }
}
