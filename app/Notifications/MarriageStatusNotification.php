<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarriageStatusNotification extends Notification
{
    use Queueable;

    protected $application;
    protected $title;
    protected $message;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($application, $title, $message, $status = 'info')
    {
        $this->application = $application;
        $this->title = $title;
        $this->message = $message;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->status,
        ];
    }
}
