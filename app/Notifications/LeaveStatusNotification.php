<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveStatusNotification extends Notification
{
    use Queueable;

    protected LeaveRequest $leaveRequest;
    protected string $status;

    public function __construct(LeaveRequest $leaveRequest, string $status)
    {
        $this->leaveRequest = $leaveRequest;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'status' => $this->status,
            'leave_request_id' => $this->leaveRequest->id,
            'request_number' => $this->leaveRequest->request_number,
            'leave_type' => $this->leaveRequest->leaveType?->name ?? 'Cuti',
            'rejection_reason' => $this->leaveRequest->rejection_reason,
            'approved_days' => $this->leaveRequest->approved_days,
            'url' => route('user.leave.show', $this->leaveRequest->id, false),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getTitle() . ' - Pers Bengpuskomlekad')
            ->view('emails.leave.status', [
                'user' => $notifiable,
                'leaveRequest' => $this->leaveRequest,
                'status' => $this->status,
                'title' => $this->getTitle(),
                'statusMessage' => $this->getMessage(),
                'detailUrl' => route('user.leave.show', $this->leaveRequest->id),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    protected function getTitle(): string
    {
        return match ($this->status) {
            'approved' => 'Pengajuan Cuti Disetujui',
            'rejected' => 'Pengajuan Cuti Ditolak',
            'cancelled' => 'Pengajuan Cuti Dibatalkan',
            default => 'Perubahan Status Pengajuan Cuti',
        };
    }

    protected function getMessage(): string
    {
        return match ($this->status) {
            'approved' => 'Pengajuan cuti Anda telah disetujui.',
            'rejected' => 'Pengajuan cuti Anda telah ditolak.',
            'cancelled' => 'Pengajuan cuti Anda telah dibatalkan.',
            default => 'Status pengajuan cuti Anda telah diperbarui.',
        };
    }
}