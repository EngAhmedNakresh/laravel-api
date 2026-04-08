<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Appointment $appointment, private readonly string $action)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Appointment update')
            ->line("Your appointment has been {$this->action}.")
            ->line('Doctor: '.$this->appointment->doctor->name_en)
            ->line('Date: '.$this->appointment->date?->format('Y-m-d'))
            ->line('Time: '.$this->appointment->time?->format('H:i'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'status' => $this->appointment->status?->value ?? $this->appointment->status,
            'action' => $this->action,
        ];
    }
}
