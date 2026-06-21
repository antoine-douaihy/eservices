<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Covers every appointment status transition the citizen should hear
 * about: confirmed, completed, or cancelled. (A brand-new appointment
 * being scheduled is a separate event — see AppointmentScheduled.)
 */
class AppointmentConfirmed extends Notification
{
    use Queueable;

    public function __construct(
        public Appointment $appointment,
        public string $status = 'confirmed'
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];
        if (!empty($notifiable->phone)) {
            $channels[] = WhatsAppChannel::class;
        }
        return $channels;
    }

    private function statusLabel(): string
    {
        return match ($this->status) {
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default     => 'Confirmed',
        };
    }

    public function toWhatsApp(object $notifiable): string
    {
        $emoji = match ($this->status) {
            'completed' => '✅',
            'cancelled' => '❌',
            default     => '✅',
        };

        $message = "E-Services Platform\n\n"
            . "Appointment {$this->statusLabel()} {$emoji}\n"
            . "{$this->appointment->title}\n"
            . "Date: {$this->appointment->scheduled_at->format('d M Y \a\t H:i')}\n"
            . "Office: {$this->appointment->office->name}";

        if ($this->status === 'confirmed') {
            $message .= "\n\nPlease arrive on time.";
        } el