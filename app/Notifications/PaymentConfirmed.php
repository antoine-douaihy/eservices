<?php

namespace App\Notifications;

use App\Models\CitizenRequest;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification
{
    use Queueable;

    public function __construct(
        public CitizenRequest $citizenRequest,
        public string $method
    ) {}

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
            'title' => "Payment Confirmed — Request #{$this->citizenRequest->id}",
            'body'  => "Your payment for {$this->citizenRequest->service->name} was received. Your request is now under review.",
            'url'   => route('citizen.my-requests'),
            'icon'  => 'bi-credit-card-fill',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Payment Confirmed — Request #{$this->citizenRequest->id}")
            ->greeting("Hello, {$notifiable->first_name}!")
            ->line("We've received your payment for **{$this->citizenRequest->service->name}** (Request #{$this->citizenRequest->id}).")
            ->line("**Payment method:** " . ucfirst($this->method))
            ->line("Your request has moved to review and our staff will process it shortly.")
            ->action('View My Requests', route('citizen.my-requests'))
            ->line('Thank you for using our E-Services platform.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "E-Services Platform\n\n"
            . "Payment Confirmed ✅\n"
            . "Request #{$this->citizenRequest->id} — {$this->citizenRequest->service->name}\n"
            . "Method: " . ucfirst($this->method) . "\n\n"
            . "Your request is now under review.";
    }
}
