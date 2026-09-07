<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ContentAssignment;
use App\Models\EducationalContent;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Subject;
use App\Notifications\NewSupportMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentAccessController extends Controller
{
    /**
     * استعراض طلاب المعلم وصلاحيات وصولهم لمحتوى المواد
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        
        // جلب المواد التي يدرسها هذا المعلم
        $subjects = Subject::where('user_id', $teacher->id)
            ->orWhere('teacher_id', $teacher->id)
            ->when($teacher->subject_id, function($q) use ($teacher) {
                $q->orWhere('id', $teacher->subject_id);
            })
            ->get();

        if ($subjects->isEmpty()) {
            $subjects = Subject::all();
        }

        $selectedSubjectId = $request->get('subject_id', $subjects->first()?->id);
        $selectedSubject = $subjects->firstWhere('id', $selectedSubjectId) ?? $subjects->first();

        // جلب الاشتراكات الحالية في هذه المادة
        $enrollments = collect();
        if ($selectedSubject) {
            $enrollments = Enrollment::with(['student.stage', 'contentAssignments'])
                ->where('subject_id', $selectedSubject->id)
                ->latest()
                ->paginate(15);
        }

        // إجمالي محتويات المادة (فيديوهات وملفات)
        $totalContentsCount = $selectedSubject ? EducationalContent::where('subject_id', $selectedSubject->id)->count() : 0;

        return view('teacher.access.index', compact('teacher', 'subjects', 'selectedSubject', 'enrollments', 'totalContentsCount'));
    }

    /**
     * جلب قائمة دروس المادة وحالة إتاحتها لهذا الطالب (للنافذة المنبثقة Modal)
     */
    public function getStudentContents($enrollment_id)
    {
        $enrollment = Enrollment::with(['subject', 'student'])->findOrFail($enrollment_id);
        
        // جميع دروس وفيديوهات المادة مرتبة
        $contents = EducationalContent::where('subject_id', $enrollment->subject_id)
            ->orderBy('order', 'asc')
            ->get(['id', 'title', 'type', 'channel_name', 'order', 'url_path', 'pdf_path']);

        // الدروس المخصصة للطالب حالياً
        $assignedIds = ContentAssignment::where('enrollment_id', $enrollment->id)
            ->where('is_visible', true)
            ->pluck('educational_content_id')
            ->toArray();

        // إذا كان الاشتراك كاملاً، فجميع الدروس مفعلة افتراضياً
        $isFullAccess = ($enrollment->access_mode === 'all');

        $result = $contents->map(function ($item) use ($assignedIds, $isFullAccess) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'type' => $item->type,
                'channel_name' => $item->channel_name ?? 'عام',
                'order' => $item->order,
                'has_video' => !empty($item->url_path),
                'has_pdf' => !empty($item->pdf_path),
                'is_unlocked' => $isFullAccess || in_array($item->id, $assignedIds),
            ];
        });

        return response()->json([
            'status' => 'success',
            'enrollment' => [
                'id' => $enrollment->id,
                'student_name' => $enrollment->student->name_ar ?? $enrollment->student->name_en ?? 'طالب',
                'subject_name' => $enrollment->subject->name_ar ?? $enrollment->subject->name,
                'access_mode' => $enrollment->access_mode,
            ],
            'contents' => $result
        ]);
    }

    /**
     * حفظ وتحديث صلاحيات الطالب (كامل المنهج أو جزئية محددة)
     */
    public function updateAccess(Request $request, $enrollment_id)
    {
        $request->validate([
            'access_mode' => 'required|in:all,custom',
            'allowed_contents' => 'nullable|array',
            'allowed_contents.*' => 'integer',
        ]);

        $enrollment = Enrollment::with(['subject', 'student'])->findOrFail($enrollment_id);
        $enrollment->access_mode = $request->access_mode;
        $enrollment->status = 'active';
        $enrollment->assigned_by = Auth::id();
        $enrollment->save();

        if ($request->access_mode === 'custom') {
            $allowedIds = $request->input('allowed_contents', []);
            
            // جلب كافة المحتويات في المادة
            $allContentIds = EducationalContent::where('subject_id', $enrollment->subject_id)->pluck('id');

            foreach ($allContentIds as $cId) {
                $isVisible = in_array($cId, $allowedIds);
                ContentAssignment::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'educational_content_id' => $cId,
                    ],
                    [
                        'is_visible' => $isVisible,
                    ]
                );
            }
        }

        // إرسال إشعار للطالب بتحديث الصلاحيات
        try {
            if ($enrollment->student) {
                $modeText = $enrollment->access_mode === 'all' ? 'كامل المنهج' : 'باقة مخصصة من الدروس';
                Notification::send(
                    $enrollment->student_id,
                    'تحديث صلاحيات المادة',
                    "قام أستاذ مادة {$enrollment->subject->name_ar} بتحديث صلاحيات وصولك إلى ({$modeText}).",
                    'success'
                );
            }
        } catch (\Exception $e) {
            // ignore notification failure
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث صلاحيات الطالب بنجاح 🎉',
            'access_mode' => $enrollment->access_mode
        ]);
    }

    /**
     * إضافة اشتراك سريع لطالب بموجب رقم الهوية أو البريد
     */
    public function quickEnroll(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'student_identifier' => 'required|string',
            'access_mode' => 'required|in:all,custom',
        ]);

        $identifier = trim($request->student_identifier);
        $student = Student::where('email', $identifier)
            ->orWhere('nid', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'لم يتم العثور على طالب برقم الهوية أو البريد المدخل.'
            ], 404);
        }

        $enrollment = Enrollment::updateOrCreate(
            [
                'student_id' => $student->id,
                'subject_id' => $request->subject_id,
            ],
            [
                'status' => 'active',
                'access_mode' => $request->access_mode,
                'payment_status' => 'paid',
                'assigned_by' => Auth::id(),
                'activated_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => "تم تفعيل اشتراك الطالب ({$student->name_ar}) بنجاح!"
        ]);
    }
}
