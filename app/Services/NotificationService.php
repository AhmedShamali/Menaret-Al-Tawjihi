<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * إرسال تنبيه أكاديمي أو مالي لطالب محدد
     */
    public static function notifyStudent(
        int $studentId,
        string $title,
        string $message,
        string $type = 'system',
        ?string $actionUrl = null,
        ?string $icon = null
    ): bool {
        try {
            $student = Student::find($studentId);
            if (!$student) return false;

            $defaultIcons = [
                'message' => 'fa-comment-dots',
                'exam'    => 'fa-file-signature',
                'grade'   => 'fa-award',
                'content' => 'fa-video',
                'streak'  => 'fa-fire',
                'payment' => 'fa-receipt',
                'system'  => 'fa-bell',
            ];

            $iconClass = $icon ?? ($defaultIcons[$type] ?? 'fa-bell');

            DB::table('notifications')->insert([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\\Notifications\\AcademicAlert',
                'notifiable_type' => 'App\\Models\\Student',
                'notifiable_id'   => $studentId,
                'data'            => json_encode([
                    'title'      => $title,
                    'message'    => $message,
                    'type'       => $type,
                    'action_url' => $actionUrl,
                    'icon'       => $iconClass,
                    'created_at' => now()->toIso8601String(),
                ], JSON_UNESCAPED_UNICODE),
                'read_at'         => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            \Log::error('فشل إرسال الإشعار للطالب: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * إرسال تنبيه لكافة طلاب مرحلة أو فرع دراسي محدد
     */
    public static function notifyStageStudents(
        int $stageId,
        string $title,
        string $message,
        string $type = 'content',
        ?string $actionUrl = null
    ): int {
        $studentIds = Student::where('stage_id', $stageId)->where('status', 'active')->pluck('id');
        $count = 0;
        foreach ($studentIds as $sId) {
            if (self::notifyStudent($sId, $title, $message, $type, $actionUrl)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * إرسال تنبيه لإدارة المنصة
     */
    public static function notifyAdmin(
        string $title,
        string $message,
        string $type = 'payment',
        ?string $actionUrl = null
    ): void {
        try {
            $admin = User::where('role', 'admin')->first();
            if (!$admin) return;

            DB::table('notifications')->insert([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\\Notifications\\AdminAlert',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id'   => $admin->id,
                'data'            => json_encode([
                    'title'      => $title,
                    'message'    => $message,
                    'type'       => $type,
                    'action_url' => $actionUrl,
                    'icon'       => 'fa-shield-halved',
                    'created_at' => now()->toIso8601String(),
                ], JSON_UNESCAPED_UNICODE),
                'read_at'         => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('فشل إرسال الإشعار للإدارة: ' . $e->getMessage());
        }
    }
}
