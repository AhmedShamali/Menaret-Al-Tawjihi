<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Support\CurrentActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * عرض صفحة سجل الإشعارات والتنبيهات
     */
    public function index()
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        $studentId = $student?->id;

        // جلب الإشعارات من جدول notifications إن وجد، أو من التنبيهات والرسائل غير المقروءة
        $notifications = collect();

        if ($student) {
            // 1. فحص إشعارات Laravel المدمجة
            $systemNotifications = $student->notifications()->latest()->paginate(15);
            if ($systemNotifications->isNotEmpty()) {
                return view('student.notifications.index', ['notifications' => $systemNotifications]);
            }

            // 2. إذا لم تكن هناك إشعارات نظام، جلب تنبيهات الرسائل والأنشطة الأكاديمية
            $messages = Message::where('student_id', $studentId)
                ->where('sender_type', '!=', 'student')
                ->latest()
                ->paginate(15);

            return view('student.notifications.index', ['notifications' => $messages]);
        }

        return view('student.notifications.index', ['notifications' => $notifications]);
    }

    /**
     * تعيين إشعار كمقروء
     */
    public function markAsRead($id)
    {
        $student = CurrentActor::student();
        if ($student) {
            $notification = $student->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['status' => 'success']);
            }
        }

        // إذا كان رسالة
        Message::where('id', $id)->update(['is_read' => true]);
        return response()->json(['status' => 'success']);
    }

    /**
     * تعيين كافة الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        $student = CurrentActor::student();
        if ($student) {
            $student->unreadNotifications->markAsRead();
            Message::where('student_id', $student->id)
                ->where('sender_type', '!=', 'student')
                ->update(['is_read' => true]);
        }

        return response()->json(['status' => 'success', 'message' => 'تم تعيين جميع التنبيهات كمقروءة']);
    }

    /**
     * جلب أحدث التنبيهات مع العداد للأيقونة العلوية (Live Bell)
     */
    public function getUnread()
    {
        $student = CurrentActor::student();
        if (!$student) {
            return response()->json(['count' => 0, 'items' => []]);
        }

        $unreadMessages = Message::where('student_id', $student->id)
            ->where('sender_type', '!=', 'student')
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'title' => $msg->sender_type === 'teacher' ? 'رسالة جديدة من المعلم' : 'رد من الدعم الفني',
                    'message' => \Illuminate\Support\Str::limit($msg->message, 60),
                    'created_at' => $msg->created_at ? $msg->created_at->diffForHumans() : 'الآن',
                    'url' => $msg->sender_type === 'teacher' ? route('student.chat.teacher', $msg->teacher_id ?? 1) : route('student.support'),
                    'type' => 'message'
                ];
            });

        $count = Message::where('student_id', $student->id)
            ->where('sender_type', '!=', 'student')
            ->where('is_read', false)
            ->count();

        return response()->json([
            'count' => $count,
            'items' => $unreadMessages
        ]);
    }
}
