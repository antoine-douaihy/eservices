<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentScheduled extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

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
            'title' => 'You Have a New Appointment',
            'body'  => "{$this->appointment->title} — {$this->appointment->scheduled_at->format('d M Y \a\t H:i')} at {$this->appointment->office->name}",
            'url'   => route('citizen.appointments.index'),
            'icon'  => 'bi-calendar-plus-fill',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You Have a New Appointment — ' . $this->appointment->title)
            ->greeting("Hello, {$notifiable->first_name}!")
            ->line("Our office has scheduled a new appointment for you.")
            ->line("**Title:** {$this->appointment->title}")
            ->line("**Date & Time:** {$this->appointment->scheduled_at->format('d M Y \a\t H:i')}")
            ->line("**Office:** {$this->appointment->office->name}")
            ->when($this->appointment->notes, fn($m) => $m->line("**Notes:** {$this->appointment->notes}"))
            ->action('View My Appointments', route('citizen.appointments.index'))
            ->line('Please confirm or let us know if you need to reschedule.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "E-Services Platform\n\n"
            . "You Have a New Appointment 📅\n"
            . "{$this->appointment->title}\n"
            . "Date: {$this->appointment->scheduled_at->format('d M Y \a\t H:i')}\n"
            . "Office: {$this->appointment->office->name}\n\n"
            . "Please confirm it in the app.";
    }
}
