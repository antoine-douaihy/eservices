<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(public ServiceRequest $service) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = match($this->service->status) {
            'pending'     => 'Pending Review',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            default       => ucfirst($this->service->status),
        };

        return [
            'title' => 'Request Updated — ' . $this->service->tracking_code,
            'body'  => "Status changed to: {$statusLabel}",
            'url'   => route('track.show', $this->service->uuid),
            'icon'  => 'bi-arrow-repeat',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $labels = [
            'pending'     => 'Pending Review',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
        ];

        $statusLabel = $labels[$this->service->status] ?? ucfirst($this->service->status);

        return (new MailMessage)
            ->subject('Update on Request ' . $this->service->tracking_code)
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Your service request has been updated.')
            ->line('**Tracking Code:** ' . $this->service->tracking_code)
            ->line('**Title:** ' . ($this->service->title ?? 'N/A'))
            ->line('**New Status:** ' . $statusLabel)
            ->action('Track Your Request', route('track.show', $this->service->uuid))
            ->line('Thank you for using our e-services platform.');
    }
}
