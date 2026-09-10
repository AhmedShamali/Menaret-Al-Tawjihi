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
     * إرسال تنبيه لإدارة المنصة (لكافة المشرفين والمديرين)
     */
    public static function notifyAdmin(
        string $title,
        string $message,
        string $type = 'system',
        ?string $actionUrl = null,
        ?string $icon = null
    ): void {
        try {
            $admins = User::where('role', 'admin')->get();
            if ($admins->isEmpty()) {
                $firstUser = User::first();
                if ($firstUser) {
                    $admins = collect([$firstUser]);
                } else {
                    return;
                }
            }

            $iconClass = $icon ?? match ($type) {
                'payment'  => 'fa-wallet',
                'student'  => 'fa-user-plus',
                'exam'     => 'fa-file-signature',
                'support'  => 'fa-headset',
                default    => 'fa-shield-halved',
            };

            foreach ($admins as $admin) {
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
                        'icon'       => $iconClass,
                        'created_at' => now()->toIso8601String(),
                    ], JSON_UNESCAPED_UNICODE),
                    'read_at'         => null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('فشل إرسال الإشعار للإدارة: ' . $e->getMessage());
        }
    }

    /**
     * إرسال تنبيه لمعلم محدد
     */
    public static function notifyTeacher(
        int $teacherId,
        string $title,
        string $message,
        string $type = 'exam',
        ?string $actionUrl = null,
        ?string $icon = null
    ): bool {
        try {
            $teacher = User::find($teacherId);
            if (!$teacher) return false;

            $iconClass = $icon ?? match ($type) {
                'exam'    => 'fa-pen-to-square',
                'grade'   => 'fa-marker',
                'message' => 'fa-comments',
                'student' => 'fa-user-graduate',
                default   => 'fa-chalkboard-teacher',
            };

            DB::table('notifications')->insert([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\\Notifications\\TeacherAlert',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id'   => $teacherId,
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
            \Log::error('فشل إرسال الإشعار للمعلم: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * إرسال تنبيه لكافة الطلاب المسجلين في مادة محددة
     */
    public static function notifyEnrolledStudents(
        int $subjectId,
        string $title,
        string $message,
        string $type = 'content',
        ?string $actionUrl = null,
        ?string $icon = null
    ): int {
        try {
            $studentIds = \App\Models\Enrollment::where('subject_id', $subjectId)
                ->where('status', 'active')
                ->pluck('student_id')
                ->unique();

            $count = 0;
            foreach ($studentIds as $sId) {
                if (self::notifyStudent($sId, $title, $message, $type, $actionUrl, $icon)) {
                    $count++;
                }
            }
            return $count;
        } catch (\Throwable $e) {
            \Log::error('فشل إرسال الإشعار للطلاب المسجلين: ' . $e->getMessage());
            return 0;
        }
    }
}
