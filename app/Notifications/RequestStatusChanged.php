<?php

namespace App\Notifications;

use App\Models\CitizenRequest;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public CitizenRequest $citizenRequest,
        public string $newStatus,
        public ?string $note = null
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];
        if (!empty($notifiable->phone)) {
            $channels[] = WhatsAppChannel::class;
        }
        return $channels;
    }

    public function toWhatsApp(object $notifiable): string
    {
        // "Approved" gets a dedicated, friendlier headline since this is
        // the moment the citizen has been waiting for.
        if ($this->newStatus === 'approved') {
            $message = "E-Services Platform\n\n"
                . "Your Service Is Ready! ✅\n"
                . "Request #{$this->citizenRequest->id} — {$this->citizenRequest->service->name} has been approved.";

            if ($this->citizenRequest->certificate_path) {
                $message .= "\n\nDownload your certificate: " . route('requests.certificate', $this->citizenRequest);
            }
            return $message;
        }

        $statusLabel = match($this->newStatus) {
            'rejected'           => 'Declined ❌',
            'in_review'          => 'Under Review 🔎',
            'pending_payment'    => 'Awaiting Payment 💳',
            'missing_documents'  => 'Missing Information ⚠️',
            default              => ucfirst(str_replace('_', ' ', $this->newStatus)),
        };

        $message = "E-Services Platform\n\n"
            . "Your request #{$this->citizenRequest->id} ({$this->citizenRequest->service->name}) is now: {$statusLabel}";

        if ($this->newStatus === 'missing_documents') {
            $message .= "\nPlease upload the missing documents so we can continue processing your request.";
        }

        if ($this->note) {
            $message .= "\nNote: {$this->note}";
        }

        if ($this->newStatus === 'pending_payment') {
            $message .= "\n\nComplete your payment: " . route('citizen.payment.select', $this->citizenRequest);
        }

        return $message;
    }

    private function statusLabel(): string
    {
        return match($this->newStatus) {
            'approved'           => 'Your Service Is Ready',
            'rejected'       