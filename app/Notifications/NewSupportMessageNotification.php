<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSupportMessageNotification extends Notification
{
    use Queueable;

    public $messageData;

    public function __permissions($notifiable)
    {
        //
    }

    public function __construct($messageData)
    {
        $this->messageData = $messageData;
    }

    public function via($notifiable)
    {
        return ['database']; // أو يمكنك إضافة 'broadcast' إذا كنت تستخدم Pusher
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->messageData->message ?? 'رسالة جديدة',
            'sender_id' => $this->messageData->sender_id ?? null,
        ];
    }
}
