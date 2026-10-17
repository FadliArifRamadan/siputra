<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountRejectedMail;
use App\Mail\BookingApprovedMail;
use App\Mail\TaskAssignedMail;
use App\Mail\TicketCompletedMail;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function create(User $user, string $title, string $message, string $type, ?string $link = null, $reference = null): Notification
    {
        $data = [
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
        ];

        if ($reference) {
            $data['reference_type'] = get_class($reference);
            $data['reference_id'] = $reference->id;
        }

        return Notification::create($data);
    }

    public function getUnreadCount(User $user): int
    {
        return Notification::forUser($user->id)->unread()->count();
    }

    public function getUnread(User $user, int $limit = 10)
    {
        return Notification::forUser($user->id)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getLatest(User $user, int $limit = 20)
    {
        return Notification::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function markAllAsRead(User $user): int
    {
        return Notification::forUser($user->id)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public function markAsRead(User $user, int $notificationId): ?Notification
    {
        $notification = Notification::forUser($user->id)->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
        return $notification;
    }
    /**
     * Send email notification for specific event types.
     */
    public function sendEmail($user, string $type, $data): void
    {
        if (!$user->email) {
            return;
        }

        $mailClass = match ($type) {
            'account_approved' => AccountApprovedMail::class,
            'account_rejected' => AccountRejectedMail::class,
            'task_assigned' => TaskAssignedMail::class,
            'ticket_completed' => TicketCompletedMail::class,
            'booking_approved' => BookingApprovedMail::class,
            // ✅ Tambahkan untuk reminder
            'reminder_booking', 'reminder_task' => null, // Tidak pakai email untuk reminder
            default => null,
        };

        if ($mailClass) {
            Mail::to($user->email)->send(new $mailClass($data));
        }
    }

    /**
     * Create notification and send email (combined).
     */
    public function notify(User $user, string $title, string $message, string $type, ?string $link = null, $reference = null, bool $sendEmail = true): Notification
    {
        // 1. Create in-app notification
        $notification = $this->create($user, $title, $message, $type, $link, $reference);

        // 2. Send email if requested
        if ($sendEmail && $reference) {
            $this->sendEmail($user, $type, $reference);
        }

        return $notification;
    }
}
