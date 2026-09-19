<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Message;
use App\Models\Subject;
use App\Notifications\NewSupportMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class CommunicationController extends Controller
{
    // ==========================================
    // 1. الجزء الخاص بالأدمن والدعم الفني (Support)
    // ==========================================

    public function index(Request $request = null)
    {
        $selectedStudentId = $request ? $request->query('student_id') : null;

        $students = Student::with('stage')
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->whereNull('teacher_id')
                      ->where('sender_type', 'student')
                      ->where(function($sub) {
                          $sub->where('is_read', false)->orWhereNull('is_read');
                      });
                }
            ])
            ->get();

        $students->each(function ($st) {
            $lastMsg = Message::where('student_id', $st->id)
                ->whereNull('teacher_id')
                ->latest()
                ->first();
            $st->last_message = $lastMsg ? $lastMsg->message : null;
            $st->last_message_time = $lastMsg && $lastMsg->created_at ? $lastMsg->created_at->diffForHumans() : null;
            $st->last_message_at = $lastMsg ? $lastMsg->created_at : null;
            $st->last_sender_type = $lastMsg ? $lastMsg->sender_type : null;
        });

        $chats = $students->sort(function ($a, $b) use ($selectedStudentId) {
            if ($selectedStudentId) {
                if ($a->id == $selectedStudentId) return -1;
                if ($b->id == $selectedStudentId) return 1;
            }
            if ($a->unread_count !== $b->unread_count) {
                return $b->unread_count <=> $a->unread_count;
            }
            if ($a->last_message_at && $b->last_message_at) {
                return $b->last_message_at <=> $a->last_message_at;
            }
            if ($a->last_message_at && !$b->last_message_at) return -1;
            if (!$a->last_message_at && $b->last_message_at) return 1;
            return $b->id <=> $a->id;
        })->values();

        $selectedStudent = null;
        if ($selectedStudentId) {
            $selectedStudent = $chats->firstWhere('id', (int)$selectedStudentId);
            if (!$selectedStudent) {
                $selectedStudent = Student::with('stage')->find($selectedStudentId);
            }
        }

        return view('admin.management.inbox', compact('chats', 'selectedStudentId', 'selectedStudent'));
    }

    public function adminInbox(Request $request = null)
    {
        return $this->index($request);
    }

    public function studentChat()
    {
        $student = Auth::guard('student')->user() ?? Auth::user();
        $studentId = $student?->id ?? null;

        $studentStageObj = $student?->stage ?? null;
        $studentStage = is_object($studentStageObj)
            ? ($studentStageObj->label_ar ?? $studentStageObj->name_ar ?? $studentStageObj->name ?? 'غير محددة')
            : ($studentStageObj ?? 'غير محددة');

        $support = User::where('role', 'admin')->get();

        if ($support->isEmpty()) {
            $support = collect([
                (object)[
                    'id' => 1,
                    'name' => 'الدعم الفني والمدراء'
                ]
            ]);
        }

        $firstAdminId = $support->first()->id;

        $messages = Message::where('student_id', $studentId)
            ->where('admin_id', $firstAdminId)
            ->whereNull('teacher_id')
            ->orderBy('created_at', 'asc')
            ->get();

        $studentStageId = $student?->stage_id ?? $student?->stage?->id ?? null;
        $teacherIds = [];

        if (Schema::hasTable('subjects')) {
            $subjectQuery = DB::table('subjects');
            if ($studentStageId) {
                $subjectQuery->where('stage_id', $studentStageId);
            }
            if (Schema::hasColumn('subjects', 'user_id')) {
                $teacherIds = array_merge($teacherIds, (clone $subjectQuery)->pluck('user_id')->filter()->toArray());
            }
            if (Schema::hasColumn('subjects', 'teacher_id')) {
                $teacherIds = array_merge($teacherIds, (clone $subjectQuery)->pluck('teacher_id')->filter()->toArray());
            }
        }

        if ($studentStageId && Schema::hasColumn('users', 'stage_id')) {
            $directStageTeachers = User::where('role', 'teacher')->where('stage_id', $studentStageId)->pluck('id')->toArray();
            $teacherIds = array_merge($teacherIds, $directStageTeachers);
        }

        $teacherIds = array_unique(array_filter($teacherIds));

        $teachers = User::where('role', 'teacher')
            ->whereIn('id', $teacherIds)
            ->get();

        if ($teachers->isEmpty()) {
            $teachers = User::where('role', 'teacher')->get();
        }

        return view('student.support.index', compact('messages', 'studentStage', 'support', 'teachers', 'firstAdminId'));
    }


    public function sendFromStudent(Request $request)
    {
        $studentId = Auth::guard('student')->id() ?? Auth::id();

        if (!$studentId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'message'  => 'required|string',
            'admin_id' => 'required',
        ]);

        try {
            $message = Message::create([
                'student_id'  => $studentId,
                'admin_id'    => $request->admin_id,
                'sender_type' => 'student',
                'message'     => trim($request->message),
            ]);

            try {
                $student = Student::find($studentId);
                NotificationService::notifyAdmin(
                    'رسالة جديدة من طالب في الدعم',
                    "أرسل الطالب (" . ($student?->name_ar ?? $student?->name ?? 'طالب') . "): " . Str::limit($request->message, 70),
                    'support',
                    route('admin.messages.index', ['student_id' => $studentId]),
                    'fa-comment-dots'
                );
            } catch (\Throwable $e) {}

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'id'                   => $message->id,
                    'message'              => $message->message,
                    'sender_type'          => $message->sender_type,
                    'created_at_formatted' => $message->created_at ? $message->created_at->timezone('Asia/Gaza')->format('h:i A') : 'الآن'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Send Message Student Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function submitTicket(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $studentId = Auth::guard('student')->id() ?? Auth::id();
            $admin = \App\Models\User::where('role', 'admin')->first();
            $adminId = $admin ? $admin->id : 1;

            $subjectPrefix = $request->filled('subject') ? "📌 [تذكرة دعم: {$request->subject}]\n" : "";
            $fullMessage = $subjectPrefix . trim($request->message);

            $message = Message::create([
                'student_id'  => $studentId,
                'admin_id'    => $adminId,
                'sender_type' => 'student',
                'message'     => $fullMessage,
            ]);

            try {
                $student = Student::find($studentId);
                NotificationService::notifyAdmin(
                    'تذكرة دعم فني جديدة',
                    "أرسل الطالب (" . ($student?->name_ar ?? $student?->name ?? 'طالب') . ") تذكرة دعم فني: " . Str::limit($request->message, 70),
                    'support',
                    route('admin.messages.index', ['student_id' => $studentId]),
                    'fa-headset'
                );
            } catch (\Throwable $e) {}

            return response()->json([
                'status'  => 'success',
                'success' => true,
                'icon'    => 'success',
                'title'   => 'تم إرسال تذكرتك بنجاح ✅',
                'message' => 'تم استلام استفسارك وسيقوم فريق الدعم بالرد عليك قريباً.'
            ]);
        } catch (\Exception $e) {
            Log::error('Submit Ticket Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'تعذر إرسال التذكرة: ' . $e->getMessage()], 500);
        }
    }

    public function fetchMessages($id)
    {
        try {
            $isStudent = Auth::guard('student')->check();

            if ($isStudent) {
                // عندما يكون المتصل طالباً: $id يمثل admin_id، بينما الطالب هو صاحب الجلسة الحالي
                $studentId = Auth::guard('student')->id() ?? Auth::id();
                $adminId = $id;

                Message::where('student_id', $studentId)
                    ->where('admin_id', $adminId)
                    ->whereNull('teacher_id')
                    ->where('sender_type', '!=', 'student')
                    ->where(function($q) {
                        $q->where('is_read', false)->orWhereNull('is_read');
                    })
                    ->update(['is_read' => true]);

                $query = Message::where('student_id', $studentId)
                    ->where('admin_id', $adminId)
                    ->whereNull('teacher_id');
            } else {
                // عندما يكون المتصل مديراً: $id يمثل student_id
                $studentId = $id;

                Message::where('student_id', $studentId)
                    ->whereNull('teacher_id')
                    ->where('sender_type', 'student')
                    ->where(function($q) {
                        $q->where('is_read', false)->orWhereNull('is_read');
                    })
                    ->update(['is_read' => true]);

                $query = Message::where('student_id', $studentId)
                    ->whereNull('teacher_id');
            }

            $messages = $query->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($msg) {
                    return [
                        'id'                    => $msg->id,
                        'message'               => $msg->message,
                        'sender_type'           => strtolower(trim($msg->sender_type ?? 'student')),
                        'created_at_formatted'  => $msg->created_at ? $msg->created_at->timezone('Asia/Gaza')->format('h:i A') : '',
                        'created_at_human'      => $msg->created_at ? $msg->created_at->diffForHumans() : '',
                    ];
                });

            $studentInfo = null;
            if (!$isStudent) {
                $st = Student::with('stage')->find($studentId);
                if ($st) {
                    $studentInfo = [
                        'id'          => $st->id,
                        'name'        => $st->name_ar ?? $st->name ?? trim(($st->first_name ?? '') . ' ' . ($st->last_name ?? '')) ?: 'طالب',
                        'email'       => $st->email,
                        'phone'       => $st->phone ?? $st->whatsapp ?? '',
                        'whatsapp'    => $st->whatsapp ?? $st->phone ?? '',
                        'stage'       => $st->stage ? ($st->stage->name_ar ?? $st->stage->name ?? 'توجيهي') : 'توجيهي',
                        'school_name' => $st->school_name ?? '',
                        'city'        => $st->city ?? '',
                        'status'      => $st->status ?? 'active',
                    ];
                }
            }

            return response()->json([
                'status'   => 'success',
                'messages' => $messages,
                'student'  => $studentInfo
            ]);
        } catch (\Exception $e) {
            Log::error('Fetch Messages Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getMessages($student_id)
    {
        return $this->fetchMessages($student_id);
    }

    public function send(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'message'    => 'required|string',
        ]);

        try {
            $adminUser = Auth::guard('web')->user() ?? Auth::user();
            $adminId = $adminUser ? $adminUser->id : 1;

            $message = Message::create([
                'student_id'  => $request->student_id,
                'admin_id'    => $adminId,
                'sender_type' => 'admin',
                'message'     => trim($request->message),
                'is_read'     => false,
            ]);

            try {
                NotificationService::notifyStudent(
                    (int)$request->student_id,
                    'رد جديد من إدارة المنصة 💬',
                    Str::limit($request->message, 70),
                    'message',
                    route('student.support'),
                    'fa-comment-dots'
                );
            } catch (\Throwable $ne) {
                Log::warning('Send notification to student failed: ' . $ne->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'id'                   => $message->id,
                    'message'              => $message->message,
                    'sender_type'          => 'admin',
                    'created_at_formatted' => $message->created_at ? $message->created_at->timezone('Asia/Gaza')->format('h:i A') : 'الآن'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Send Message Admin Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        return $this->send($request);
    }


    public function teacherInbox()
    {
        $teacher = Auth::user();
        $subjectId = $teacher->subject_id ?? null;
        $students = collect();

        if ($subjectId) {
            $subject = DB::table('subjects')->where('id', $subjectId)->first();
            if ($subject && !empty($subject->stage_id)) {
                $students = DB::table('students')
                    ->where('stage_id', $subject->stage_id)
                    ->get();
            }
        }

        if ($students->isEmpty()) {
            $students = DB::table('students')->get();
        }

        return view('teacher.inbox', compact('students'));
    }


    public function fetchTeacherStudentMessages($student_id)
    {
        try {
            $teacherId = Auth::id();
            Message::where('student_id', $student_id)
                ->where('teacher_id', $teacherId)
                ->where('sender_type', 'student')
                ->where('is_read', \Illuminate\Support\Facades\DB::raw('false'))
                ->update(['is_read' => \Illuminate\Support\Facades\DB::raw('true')]);

            $messages = Message::where('student_id', $student_id)
                ->where('teacher_id', $teacherId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($msg) {
                    return [
                        'id'                    => $msg->id,
                        'message'               => $msg->message,
                        'sender_type'           => strtolower(trim($msg->sender_type)),
                        'created_at_formatted'  => $msg->created_at ? $msg->created_at->timezone('Asia/Gaza')->format('h:i A') : ''
                    ];
                });

            return response()->json([
                'status'   => 'success',
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Fetch Teacher Student Messages Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function sendFromTeacher(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'message'    => 'required|string',
        ]);

        try {
            $teacherId = Auth::id();
            $message = Message::create([
                'student_id'  => $request->student_id,
                'teacher_id'  => $teacherId,
                'sender_type' => 'teacher',
                'message'     => trim($request->message),
            ]);

            $teacherUser = Auth::user();
            $teacherName = $teacherUser ? $teacherUser->name : 'معلم المادة';
            NotificationService::notifyStudent(
                $request->student_id,
                "رد جديد من {$teacherName} 💬",
                "أستاذ المادة قام بالرد على رسالتك: " . Str::limit($request->message, 70),
                'message',
                route('student.teachers.chat', $teacherId)
            );

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data'   => [
                        'id'                   => $message->id,
                        'message'              => $message->message,
                        'sender_type'          => 'teacher',
                        'created_at_formatted' => $message->created_at ? $message->created_at->timezone('Asia/Gaza')->format('h:i A') : 'الآن'
                    ]
                ]);
            }

            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('Send From Teacher Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function teachersIndex()
    {
        $student = Auth::guard('student')->user() ?? Auth::user();

        if (!$student) {
            return redirect()->route('login');
        }

        $studentStageId = $student->stage_id ?? ($student->stage->id ?? null);
        $studentStageObj = $student->stage ?? null;
        $studentStageLabel = is_object($studentStageObj)
            ? ($studentStageObj->label_ar ?? $studentStageObj->name_ar ?? $studentStageObj->name ?? null)
            : ($student->stage_name ?? null);

        $subjectIds = [];
        if (Schema::hasTable('subjects')) {
            $subjectQuery = DB::table('subjects');

            if ($studentStageId && Schema::hasColumn('subjects', 'stage_id')) {
                $subjectQuery->where('stage_id', $studentStageId);
            } elseif ($studentStageLabel) {
                $subjectQuery->where(function($q) use ($studentStageLabel) {
                    if (Schema::hasColumn('subjects', 'label_ar')) {
                        $q->where('label_ar', 'LIKE', "%{$studentStageLabel}%");
                    }
                    if (Schema::hasColumn('subjects', 'name_ar')) {
                        $q->orWhere('name_ar', 'LIKE', "%{$studentStageLabel}%");
                    }
                });
            }
            $subjectIds = $subjectQuery->pluck('id')->toArray();
        }

        $teachers = User::where('role', 'teacher')
            ->whereIn('subject_id', $subjectIds)
            ->get();

        $studentStage = $studentStageLabel ?? ($student->stage->label_ar ?? 'غير محددة');

        return view('student.teachers-list', compact('teachers', 'studentStage'));
    }

    public function showTeacherChat($teacher_id)
    {
        $student = Auth::guard('student')->user() ?? Auth::user();
        $teacher = User::where('role', 'teacher')->findOrFail($teacher_id);

        return view('student.teacher_chat', compact('teacher', 'student'));
    }

    // ==========================================
    // 2. الجزء الخاص بمراسلة المعلمين للأدمن
    // ==========================================
    public function teachersChat(Request $request)
    {
        $teachers = User::where('role', 'teacher')->get();

        $selectedTeacher = null;
        $messages = collect();

        if ($request->has('teacher_id')) {
            $selectedTeacher = User::where('role', 'teacher')->find($request->teacher_id);
            if ($selectedTeacher) {
                $messages = Message::where('teacher_id', $selectedTeacher->id)
                    ->where('admin_id', Auth::id())
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('admin.teachers.chat', compact('teachers', 'selectedTeacher', 'messages'));
    }

    public function inbox()
    {
        $count = DB::table('students')->count();
        // تم إزالة الـ dd() لكي لا تتسبب بتوقف التنفيذ
        return view('admin.management.inbox', compact('count'));
    }

    public function fetchStudentMessages($student_id)
    {
        $teacher = Auth::user();

        $messages = DB::table('messages')
            ->where(function($q) use ($teacher, $student_id) {
                $q->where('sender_id', $teacher->id)->where('receiver_id', $student_id);
            })
            ->orWhere(function($q) use ($teacher, $student_id) {
                $q->where('sender_id', $student_id)->where('receiver_id', $teacher->id);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) use ($teacher) {
                $msg->sender_type = ($msg->sender_id == $teacher->id) ? 'teacher' : 'student';
                $msg->created_at_formatted = \Carbon\Carbon::parse($msg->created_at)->format('H:i');
                return $msg;
            });

        return response()->json(['messages' => $messages]);
    }

    public function sendToStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $teacher = Auth::user();

        DB::table('messages')->insert([
            'sender_id' => $teacher->id,
            'receiver_id' => $request->student_id,
            'message' => $request->message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            NotificationService::notifyStudent(
                $request->student_id,
                'رسالة جديدة من معلمك 💬',
                "أرسل الأستاذ ({$teacher->name}): " . Str::limit($request->message, 60),
                'message',
                route('student.teachers.chat', $teacher->id),
                'fa-comments'
            );
        } catch (\Throwable $e) {}

        return response()->json(['status' => 'success']);
    }

    public function fetchTeacherMessages($teacher_id)
    {
        $student = Auth::guard('student')->user() ?? Auth::user();
        if (!$student) {
            return response()->json(['messages' => []]);
        }

        Message::where('student_id', $student->id)
            ->where('teacher_id', $teacher_id)
            ->where('sender_type', 'teacher')
            ->where('is_read', \Illuminate\Support\Facades\DB::raw('false'))
            ->update(['is_read' => \Illuminate\Support\Facades\DB::raw('true')]);

        $messages = Message::where('student_id', $student->id)
            ->where('teacher_id', $teacher_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) {
                return [
                    'id'                    => $msg->id,
                    'message'               => $msg->message,
                    'sender_type'           => strtolower(trim($msg->sender_type ?? 'student')),
                    'created_at_formatted'  => $msg->created_at ? $msg->created_at->timezone('Asia/Gaza')->format('h:i A') : ''
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    public function sendToTeacher(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'message'    => 'required|string|max:1000',
        ]);

        $student = Auth::guard('student')->user() ?? Auth::user();
        if (!$student) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        try {
            $message = Message::create([
                'student_id'  => $student->id,
                'teacher_id'  => $request->teacher_id,
                'sender_type' => 'student',
                'message'     => trim($request->message),
            ]);

            NotificationService::notifyTeacher(
                $request->teacher_id,
                'استفسار وسؤال جديد من طالب',
                "أرسل الطالب ({$student->name_ar}): " . Str::limit($request->message, 70),
                'message',
                route('teacher.messages.index', ['student_id' => $student->id]),
                'fa-comments'
            );

            NotificationService::notifyAdmin(
                'استفسار دراسي جديد من طالب',
                "قام الطالب {$student->name_ar} بإرسال استفسار دراسي لمعلم المادة: " . Str::limit($request->message, 70),
                'message',
                route('admin.messages.index', ['student_id' => $student->id]),
                'fa-comments'
            );

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'id'                    => $message->id,
                    'message'               => $message->message,
                    'sender_type'           => $message->sender_type,
                    'created_at_formatted'  => $message->created_at ? $message->created_at->timezone('Asia/Gaza')->format('h:i A') : 'الآن'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Send To Teacher Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function adminTeacherChat($teacher_id)
    {
        $admin = Auth::user();
        $teacher = User::where('role', 'teacher')->findOrFail($teacher_id);
        return view('admin.management.teacher_chat', compact('teacher', 'admin'));
    }

    public function fetchAdminTeacherMessages($teacher_id)
    {
        $adminId = Auth::id();
        $messages = Message::where(function($q) use ($adminId, $teacher_id) {
                $q->where('admin_id', $adminId)->where('teacher_id', $teacher_id);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) use ($adminId) {
                return [
                    'id'                    => $msg->id,
                    'message'               => $msg->message,
                    'sender_type'           => ($msg->sender_type === 'admin') ? 'admin' : 'teacher',
                    'created_at_formatted'  => $msg->created_at ? $msg->created_at->timezone('Asia/Gaza')->format('h:i A') : ''
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    public function sendFromAdminToTeacher(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'message'    => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'admin_id'    => Auth::id(),
            'teacher_id'  => $request->teacher_id,
            'sender_type' => 'admin',
            'message'     => trim($request->message),
        ]);

        return response()->json(['status' => 'success', 'data' => $message]);
    }


    public function teacherAdminChat()
    {
        $teacher = Auth::user();
        $admin = User::where('role', 'admin')->first();
        return view('teacher.admin_chat', compact('teacher', 'admin'));
    }

    public function fetchTeacherAdminMessages()
    {
        $teacher = Auth::user();
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return response()->json(['messages' => []]);
        }

        $messages = Message::where('teacher_id', $teacher->id)
            ->where('admin_id', $admin->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) {
                return [
                    'id'                    => $msg->id,
                    'message'               => $msg->message,
                    'sender_type'           => strtolower(trim($msg->sender_type ?? 'teacher')),
                    'created_at_formatted'  => $msg->created_at ? $msg->created_at->timezone('Asia/Gaza')->format('h:i A') : 'الآن'
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    public function sendFromTeacherToAdmin(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $teacher = Auth::user();
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return response()->json(['status' => 'error', 'message' => 'Admin not found'], 404);
        }

        try {
            $message = Message::create([
                'admin_id'    => $admin->id,
                'teacher_id'  => $teacher->id,
                'sender_type' => 'teacher',
                'message'     => trim($request->message),
            ]);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'id'                   => $message->id,
                    'message'              => $message->message,
                    'sender_type'          => $message->sender_type,
                    'created_at_formatted' => $message->created_at ? $message->created_at->timezone('Asia/Gaza')->format('h:i A') : 'الآن'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Send Teacher to Admin Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}