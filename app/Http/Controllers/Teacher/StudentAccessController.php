<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ContentAssignment;
use App\Models\EducationalContent;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAssignment;
use App\Models\Student;
use App\Models\Subject;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentAccessController extends Controller
{
    /**
     * استعراض طلاب المعلم وصلاحيات وصولهم لمحتوى المواد والاختبارات
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
        $contents = collect();
        $exams = collect();

        if ($selectedSubject) {
            $enrollments = Enrollment::with(['student.stage', 'contentAssignments', 'examAssignments'])
                ->where('subject_id', $selectedSubject->id)
                ->latest()
                ->paginate(20);

            $contents = EducationalContent::where('subject_id', $selectedSubject->id)
                ->orderBy('order', 'asc')
                ->get();

            $exams = Exam::where('subject_id', $selectedSubject->id)
                ->latest()
                ->get();
        }

        $totalContentsCount = $contents->count();
        $totalExamsCount = $exams->count();

        return view('teacher.access.index', compact(
            'teacher',
            'subjects',
            'selectedSubject',
            'enrollments',
            'contents',
            'exams',
            'totalContentsCount',
            'totalExamsCount'
        ));
    }

    /**
     * جلب قائمة دروس واختبارات المادة وحالة إتاحتها لهذا الطالب (للنافذة المنبثقة)
     */
    public function getStudentContents($enrollment_id)
    {
        $enrollment = Enrollment::with(['subject', 'student'])->findOrFail($enrollment_id);
        
        // 1. جميع دروس وفيديوهات وملفات المادة
        $contents = EducationalContent::where('subject_id', $enrollment->subject_id)
            ->orderBy('order', 'asc')
            ->get(['id', 'title', 'type', 'channel_name', 'order', 'url_path', 'pdf_path']);

        $assignedContentIds = ContentAssignment::where('enrollment_id', $enrollment->id)
            ->where('is_visible', true)
            ->pluck('educational_content_id')
            ->toArray();

        // 2. جميع اختبارات المعلم في هذه المادة
        $exams = Exam::where('subject_id', $enrollment->subject_id)
            ->get(['id', 'title', 'duration_minutes']);

        $assignedExamIds = ExamAssignment::where('enrollment_id', $enrollment->id)
            ->where('is_visible', true)
            ->pluck('exam_id')
            ->toArray();

        $isFullAccess = ($enrollment->access_mode === 'all');

        $contentsResult = $contents->map(function ($item) use ($assignedContentIds, $isFullAccess) {
            return [
                'id'           => $item->id,
                'title'        => $item->title,
                'type'         => $item->type,
                'channel_name' => $item->channel_name ?? 'عام',
                'order'        => $item->order,
                'has_video'    => !empty($item->url_path),
                'has_pdf'      => !empty($item->pdf_path),
                'is_unlocked'  => $isFullAccess || in_array($item->id, $assignedContentIds),
            ];
        });

        $examsResult = $exams->map(function ($item) use ($assignedExamIds, $isFullAccess) {
            return [
                'id'               => $item->id,
                'title'            => $item->title,
                'duration_minutes' => $item->duration_minutes,
                'is_unlocked'      => $isFullAccess || in_array($item->id, $assignedExamIds),
            ];
        });

        return response()->json([
            'status' => 'success',
            'enrollment' => [
                'id'           => $enrollment->id,
                'student_name' => $enrollment->student->name_ar ?? $enrollment->student->name ?? 'طالب',
                'subject_name' => $enrollment->subject->name_ar ?? $enrollment->subject->name,
                'access_mode'  => $enrollment->access_mode,
            ],
            'contents' => $contentsResult,
            'exams'    => $examsResult
        ]);
    }

    /**
     * حفظ وتحديث صلاحيات الطالب (كامل المنهج أو جزئية محددة من الدروس والاختبارات)
     */
    public function updateAccess(Request $request, $enrollment_id)
    {
        $request->validate([
            'access_mode'      => 'required|in:all,custom',
            'allowed_contents' => 'nullable|array',
            'allowed_contents.*' => 'integer',
            'allowed_exams'    => 'nullable|array',
            'allowed_exams.*'  => 'integer',
        ]);

        $enrollment = Enrollment::with(['subject', 'student'])->findOrFail($enrollment_id);
        $enrollment->access_mode = $request->access_mode;
        $enrollment->status = 'active';
        $enrollment->assigned_by = Auth::id();
        $enrollment->save();

        if ($request->access_mode === 'custom') {
            $allowedContents = $request->input('allowed_contents', []);
            $allContentIds = EducationalContent::where('subject_id', $enrollment->subject_id)->pluck('id');

            foreach ($allContentIds as $cId) {
                ContentAssignment::updateOrCreate(
                    [
                        'enrollment_id'          => $enrollment->id,
                        'educational_content_id' => $cId,
                    ],
                    [
                        'is_visible' => in_array($cId, $allowedContents),
                    ]
                );
            }

            $allowedExams = $request->input('allowed_exams', []);
            $allExamIds = Exam::where('subject_id', $enrollment->subject_id)->pluck('id');

            foreach ($allExamIds as $eId) {
                ExamAssignment::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'exam_id'       => $eId,
                    ],
                    [
                        'is_visible' => in_array($eId, $allowedExams),
                    ]
                );
            }
        }

        // إشعار الطالب بالتحديث
        try {
            if ($enrollment->student) {
                $modeText = $enrollment->access_mode === 'all' ? 'كامل المنهج والاختبارات' : 'باقة مخصصة من الدروس والاختبارات';
                NotificationService::notifyStudent(
                    $enrollment->student_id,
                    'تحديث صلاحيات المادة الأكاديمية 🎓',
                    "قام أستاذ مادة {$enrollment->subject->name_ar} بتحديث صلاحيات وصولك إلى ({$modeText}).",
                    'academic',
                    route('student.subjects.show', $enrollment->subject_id)
                );
            }
        } catch (\Exception $e) {
            // ignore notification failure
        }

        return response()->json([
            'status'      => 'success',
            'message'     => 'تم تحديث صلاحيات الطالب بنجاح 🎉',
            'access_mode' => $enrollment->access_mode
        ]);
    }

    /**
     * جلب قائمة الطلاب وعلامات الصح [✓] لعنصر محدد (فيديو أو ملف أو اختبار)
     */
    public function getItemStudents(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:content,exam',
            'item_id'   => 'required|integer',
        ]);

        $itemType = $request->item_type;
        $itemId = $request->item_id;

        $item = ($itemType === 'content')
            ? EducationalContent::with('subject')->findOrFail($itemId)
            : Exam::with('subject')->findOrFail($itemId);

        $enrollments = Enrollment::with('student.stage')
            ->where('subject_id', $item->subject_id)
            ->get();

        $students = $enrollments->map(function ($enr) use ($itemType, $itemId) {
            $isAllowed = false;

            if ($enr->access_mode === 'all') {
                $isAllowed = true;
            } else {
                if ($itemType === 'content') {
                    $assign = ContentAssignment::where('enrollment_id', $enr->id)
                        ->where('educational_content_id', $itemId)
                        ->first();
                    $isAllowed = $assign ? (bool)$assign->is_visible : false;
                } else {
                    $assign = ExamAssignment::where('enrollment_id', $enr->id)
                        ->where('exam_id', $itemId)
                        ->first();
                    $isAllowed = $assign ? (bool)$assign->is_visible : false;
                }
            }

            return [
                'enrollment_id' => $enr->id,
                'student_id'    => $enr->student_id,
                'student_name'  => $enr->student->name_ar ?? $enr->student->name ?? 'طالب',
                'student_email' => $enr->student->email ?? '-',
                'stage_name'    => $enr->student->stage->name_ar ?? 'توجيهي',
                'is_allowed'    => $isAllowed,
                'access_mode'   => $enr->access_mode
            ];
        });

        return response()->json([
            'status'     => 'success',
            'item_title' => $item->title,
            'item_type'  => $itemType,
            'students'   => $students
        ]);
    }

    /**
     * تبديل إتاحة عنصر محدد لطالب معين فورياً عبر خانة الاختيار [✓]
     */
    public function toggleItemStudent(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'item_type'     => 'required|in:content,exam',
            'item_id'       => 'required|integer',
            'is_allowed'    => 'required|boolean',
        ]);

        $enrollment = Enrollment::findOrFail($request->enrollment_id);
        $isAllowed = (bool)$request->is_allowed;

        // إذا كان الاشتراك مفتوحاً للكل وأردنا حظر عنصر، نحوله إلى مخصص
        if ($enrollment->access_mode === 'all' && !$isAllowed) {
            $enrollment->access_mode = 'custom';
            $enrollment->save();
            
            // تهيئة باقي المحتويات كمتاحة افتراضياً حتى لا يُحرم الطالب من الباقي
            $allContents = EducationalContent::where('subject_id', $enrollment->subject_id)->pluck('id');
            foreach ($allContents as $cid) {
                ContentAssignment::firstOrCreate(
                    ['enrollment_id' => $enrollment->id, 'educational_content_id' => $cid],
                    ['is_visible' => ($cid != $request->item_id || $request->item_type !== 'content')]
                );
            }
            $allExams = Exam::where('subject_id', $enrollment->subject_id)->pluck('id');
            foreach ($allExams as $eid) {
                ExamAssignment::firstOrCreate(
                    ['enrollment_id' => $enrollment->id, 'exam_id' => $eid],
                    ['is_visible' => ($eid != $request->item_id || $request->item_type !== 'exam')]
                );
            }
        }

        if ($request->item_type === 'content') {
            ContentAssignment::updateOrCreate(
                [
                    'enrollment_id'          => $enrollment->id,
                    'educational_content_id' => $request->item_id
                ],
                [
                    'is_visible' => $isAllowed
                ]
            );
        } else {
            ExamAssignment::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'exam_id'       => $request->item_id
                ],
                [
                    'is_visible' => $isAllowed
                ]
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => $isAllowed ? 'تمت إتاحة العنصر للطالب بنجاح ✅' : 'تم حجب العنصر عن الطالب 🔒'
        ]);
    }

    /**
     * تحديد الكل أو إلغاء تحديد الكل لعنصر محدد
     */
    public function bulkToggleItemStudents(Request $request)
    {
        $request->validate([
            'item_type'  => 'required|in:content,exam',
            'item_id'    => 'required|integer',
            'is_allowed' => 'required|boolean',
        ]);

        $itemType = $request->item_type;
        $itemId = $request->item_id;
        $isAllowed = (bool)$request->is_allowed;

        $item = ($itemType === 'content')
            ? EducationalContent::findOrFail($itemId)
            : Exam::findOrFail($itemId);

        $enrollments = Enrollment::where('subject_id', $item->subject_id)->get();

        foreach ($enrollments as $enr) {
            if (!$isAllowed && $enr->access_mode === 'all') {
                $enr->access_mode = 'custom';
                $enr->save();
            }

            if ($itemType === 'content') {
                ContentAssignment::updateOrCreate(
                    ['enrollment_id' => $enr->id, 'educational_content_id' => $itemId],
                    ['is_visible' => $isAllowed]
                );
            } else {
                ExamAssignment::updateOrCreate(
                    ['enrollment_id' => $enr->id, 'exam_id' => $itemId],
                    ['is_visible' => $isAllowed]
                );
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => $isAllowed ? 'تمت إتاحة العنصر لجميع الطلاب المختارين ✅' : 'تم حجب العنصر عن جميع الطلاب 🔒'
        ]);
    }

    /**
     * إضافة اشتراك سريع لطالب بموجب رقم الهوية أو البريد
     */
    public function quickEnroll(Request $request)
    {
        $request->validate([
            'subject_id'         => 'required|exists:subjects,id',
            'student_identifier' => 'required|string',
            'access_mode'        => 'required|in:all,custom',
        ]);

        $identifier = trim($request->student_identifier);
        $student = Student::where('email', $identifier)
            ->orWhere('nid', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لم يتم العثور على طالب برقم الهوية أو البريد المدخل.'
            ], 404);
        }

        $enrollment = Enrollment::updateOrCreate(
            [
                'student_id' => $student->id,
                'subject_id' => $request->subject_id,
            ],
            [
                'status'         => 'active',
                'access_mode'    => $request->access_mode,
                'payment_status' => 'paid',
                'assigned_by'    => Auth::id(),
                'activated_at'   => now(),
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => "تم تفعيل اشتراك الطالب ({$student->name_ar}) بنجاح!"
        ]);
    }
}
