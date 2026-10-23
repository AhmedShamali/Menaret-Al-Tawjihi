<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;
use App\Services\NotificationService;

class Notification extends DatabaseNotification
{
    /**
     * إرسال تنبيه متوافق مع بنية جدول الإشعارات
     */
    public static function send($userId, $title, $message, $type = 'system', ?string $actionUrl = null) {
        $student = Student::find($userId);
        if ($student) {
            return NotificationService::notifyStudent($userId, $title, $message, $type, $actionUrl);
        }
        return NotificationService::notifyUser($userId, $title, $message, $type, $actionUrl);
    }
}
