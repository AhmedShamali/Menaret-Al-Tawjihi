<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Support\CurrentActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * عرض مركز الإشعارات والتنبيهات الشامل مع التصفية والتبويبات
     */
    public function index(Request $request)
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return redirect()->route('login');
        }

        $filter = $request->get('filter', 'all');

        // 1. جلب إشعارات قاعدة البيانات
        $dbNotifications = $student->notifications()->latest()->get()->map(function ($notif) {
            $data = is_array($notif->data) ? $notif->data : json_decode($notif->data, true) ?? [];
            $type = $data['type'] ?? 'system';
            return (object)[
                'id'          => $notif->id,
                'source'      => 'system',
                'type'        => $type,
                'title'       => $data['title'] ?? 'تنبيه أكاديمي',
                'message'     => $data['message'] ?? '',
                'action_url'  => $data['action_url'] ?? $data['url'] ?? route('student.dashboard'),
                'is_read'     => !is_null($notif->read_at),
                'created_at'  => $notif->created_at,
            ];
        });

        // 2. جلب رسائل المعلمين والدعم الفني غير المقروءة أو الحديثة
        $messages = Message::where('student_id', $student->id)
            ->where('sender_type', '!=', 'student')
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($msg) {
                $isTeacher = ($msg->sender_type === 'teacher');
                $title = $isTeacher ? 'رسالة جديدة من أستاذ المادة 💬' : 'تنبيه من إدارة الدعم الفني 🎧';
                $url = $isTeacher ? route('student.teachers.chat', $msg->teacher_id ?? 1) : route('student.support');
                return (object)[
                    'id'          => 'msg_' . $msg->id,
                    'source'      => 'message',
                    'original_id' => $msg->id,
                    'type'        => 'message',
                    'title'       => $title,
                    'message'     => $msg->message,
                    'action_url'  => $url,
                    'is_read'     => (bool)$msg->is_read,
                    'created_at'  => $msg->created_at,
                ];
            });

        // 3. دمج الإشعارات وترتيبها زمنياً
        $all = $dbNotifications->concat($messages)->sortByDesc('created_at')->values();

        // إحصائيات التبويبات (Counts)
        $counts = [
            'all'      => $all->count(),
            'unread'   => $all->where('is_read', false)->count(),
            'message'  => $all->where('type', 'message')->count(),
            'payment'  => $all->where('type', 'payment')->count(),
            'exam'     => $all->where('type', 'exam')->count(),
            'academic' => $all->whereIn('type', ['academic', 'streak', 'system'])->count(),
        ];

        // تطبيق فلترة التبويب
        $filtered = match ($filter) {
            'unread'   => $all->where('is_read', false),
            'message'  => $all->where('type', 'message'),
            'payment'  => $all->where('type', 'payment'),
            'exam'     => $all->where('type', 'exam'),
            'academic' => $all->whereIn('type', ['academic', 'streak', 'system']),
            default    => $all,
        };

        // تقسيم يدوي للصفحات
        $page = (int)$request->get('page', 1);
        $perPage = 15;
        $items = $filtered->slice(($page - 1) * $perPage, $perPage)->values();
        $total = $filtered->count();
        $lastPage = max(1, (int)ceil($total / $perPage));

        return view('student.notifications.index', compact('items', 'filter', 'counts', 'page', 'lastPage', 'total'));
    }

    /**
     * تعيين إشعار مفرد كمقروء
     */
    public function markAsRead($id)
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return response()->json(['status' => 'error'], 401);
        }

        if (str_starts_with($id, 'msg_')) {
            $msgId = (int)str_replace('msg_', '', $id);
            Message::where('id', $msgId)->where('student_id', $student->id)->update(['is_read' => true]);
            return response()->json(['status' => 'success']);
        }

        $notification = $student->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * تعيين كافة الإشعارات كمقروءة دفعة واحدة
     */
    public function markAllAsRead()
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        if ($student) {
            $student->unreadNotifications->markAsRead();
            Message::where('student_id', $student->id)
                ->where('sender_type', '!=', 'student')
                ->update(['is_read' => true]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تعيين جميع الإشعارات كمقروءة بنجاح ✅'
        ]);
    }

    /**
     * حذف إشعار محدد
     */
    public function destroy($id)
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return response()->json(['status' => 'error'], 401);
        }

        if (str_starts_with($id, 'msg_')) {
            // لا نحذف رسالة المحادثة الأصلية لكن نعتبرها مقروءة
            $msgId = (int)str_replace('msg_', '', $id);
            Message::where('id', $msgId)->where('student_id', $student->id)->update(['is_read' => true]);
            return response()->json(['status' => 'success', 'message' => 'تم إخفاء التنبيه بنجاح']);
        }

        $notification = $student->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->delete();
        }

        return response()->json(['status' => 'success', 'message' => 'تم حذف الإشعار بنجاح']);
    }

    /**
     * جلب أحدث التنبيهات غير المقروءة مع العداد للـ Bell والـ Dropdown المباشر
     */
    public function getUnread()
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return response()->json(['count' => 0, 'items' => []]);
        }

        // إشعارات النظام غير المقروءة
        $systemUnread = $student->unreadNotifications()->latest()->take(5)->get()->map(function ($notif) {
            $data = is_array($notif->data) ? $notif->data : json_decode($notif->data, true) ?? [];
            return [
                'id'         => $notif->id,
                'title'      => $data['title'] ?? 'إشعار جديد',
                'message'    => Str::limit($data['message'] ?? '', 55),
                'type'       => $data['type'] ?? 'system',
                'url'        => $data['action_url'] ?? $data['url'] ?? route('student.dashboard'),
                'created_at' => $notif->created_at ? $notif->created_at->diffForHumans() : 'الآن',
            ];
        });

        // رسائل غير مقروءة
        $messagesUnread = Message::where('student_id', $student->id)
            ->where('sender_type', '!=', 'student')
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($msg) {
                return [
                    'id'         => 'msg_' . $msg->id,
                    'title'      => $msg->sender_type === 'teacher' ? 'رسالة من المعلم' : 'رد من الدعم الفني',
                    'message'    => Str::limit($msg->message, 55),
                    'type'       => 'message',
                    'url'        => $msg->sender_type === 'teacher' ? route('student.teachers.chat', $msg->teacher_id ?? 1) : route('student.support'),
                    'created_at' => $msg->created_at ? $msg->created_at->diffForHumans() : 'الآن',
                ];
            });

        $combined = $systemUnread->concat($messagesUnread)->sortByDesc('created_at')->take(5)->values();
        $totalCount = $student->unreadNotifications()->count() + Message::where('student_id', $student->id)->where('sender_type', '!=', 'student')->where('is_read', false)->count();

        return response()->json([
            'count' => $totalCount,
            'items' => $combined
        ]);
    }
}
