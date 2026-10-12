<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class NewApplicationNotification extends Notification
{
    use Queueable;

    protected $applicationType; // 'Cuti' or 'Nikah'
    protected $userName;
    protected $url;

    public function __construct(string $applicationType, string $userName, string $url)
    {
        $this->applicationType = $applicationType;
        $this->userName = $userName;
        $this->url = $url;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan ' . $this->applicationType . ' Baru',
            'message' => $this->userName . ' telah mengajukan permohonan ' . strtolower($this->applicationType) . ' baru.',
            'status' => 'pending',
            'url' => $this->url,
        ];
    }
}
