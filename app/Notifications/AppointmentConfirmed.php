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
        } elseif ($this->status === 'cancelled') {
            $message .= "\n\nIf this was a mistake, please contact the office to reschedule.";
        }

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Appointment {$this->statusLabel()}",
            'body'  => "{$this->appointment->title} — {$this->appointment->scheduled_at->format('d M Y \a\t H:i')}",
            'url'   => route('citizen.appointments.index'),
            'icon'  => match ($this->status) {
                'completed' => 'bi-check2-all',
                'cancelled' => 'bi-x-circle-fill',
                default     => 'bi-calendar-check-fill',
            },
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Appointment {$this->statusLabel()} — {$this->appointment->title}")
            ->greeting("Hello, {$notifiable->first_name}!")
            ->line("Your appointment has been **{$this->statusLabel()}**.")
            ->line("**Title:** {$this->appointment->title}")
            ->line("**Date & Time:** {$this->appointment->scheduled_at->format('d M Y \a\t H:i')}")
            ->line("**Office:** {$this->appointment->office->name}")
            ->action('View My Appointments', route('citizen.appointments.index'));
    }
}
