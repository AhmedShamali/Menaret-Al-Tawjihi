<?php


namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamSubmission;
use App\Models\SubmissionAnswer;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('subject')
            ->withCount('submissions')
            ->latest()
            ->get();

        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name_ar')->get();
        $stages = \App\Models\Stage::with('subjects')->orderBy('grade_level', 'asc')->get();

        return view('admin.exams.create', compact('subjects', 'stages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'stage_id' => ['nullable', 'exists:stages,id'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:600'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.type' => ['required', 'in:mcq,essay'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.points' => ['required', 'integer', 'min:1'],
            'questions.*.a' => ['required_if:questions.*.type,mcq', 'nullable', 'string'],
            'questions.*.b' => ['required_if:questions.*.type,mcq', 'nullable', 'string'],
            'questions.*.c' => ['required_if:questions.*.type,mcq', 'nullable', 'string'],
            'questions.*.d' => ['required_if:questions.*.type,mcq', 'nullable', 'string'],
            'questions.*.correct_answer' => ['nullable', 'in:a,b,c,d'],
            'questions.*.require_file' => ['nullable'],
        ]);

        DB::transaction(function () use ($validated) {
            $exam = Exam::create([
                'title' => $validated['title'],
                'subject_id' => $validated['subject_id'],
                'stage_id' => $validated['stage_id'] ?? null,
                'duration_minutes' => $validated['duration_minutes'],
                'is_published' => true,
            ]);

            foreach ($validated['questions'] as $q) {
                Question::create([
                    'exam_id' => $exam->id,
                    'type' => $q['type'],
                    'question_text' => $q['question_text'],
                    'a' => $q['a'] ?? null,
                    'b' => $q['b'] ?? null,
                    'c' => $q['c'] ?? null,
                    'd' => $q['d'] ?? null,
                    'correct_answer' => $q['correct_answer'] ?? null,
                    'points' => $q['points'],
                    'require_file' => (bool) ($q['require_file'] ?? false),
                ]);
            }
        });

        return response()->json(['title' => 'تم نشر الاختبار بنجاح 🚀']);
    }

    public function stats($id)
    {
        $exam = Exam::with(['subject', 'questions', 'submissions.student'])->findOrFail($id);

        // 1. تجهيز مصفوفة الإحصائيات بالأسماء الصحيحة التي تطلبها الواجهة
        $stats = [
            'avg'   => $exam->submissions->avg('total_earned_grade') ?? 0, // حساب المتوسط
            'max'   => $exam->submissions->max('total_earned_grade') ?? 0, // أعلى درجة
            'count' => $exam->submissions->count(), // عدد الطلاب
        ];

        // 2. تمرير المصفوفة للواجهة
        return view('admin.exams.stats', compact('exam', 'stats'));
    }

    public function gradebook()
    {
        // جلب كل تسليمات الطالب الحالي مع العلاقات الضرورية
        $submissions = ExamSubmission::with(['exam.subject', 'answers.question'])
            ->where('student_id', 1) // استبدل 1 بـ auth()->id() عند تفعيل الدخول
            ->latest()
            ->get();

        return view('student.exams.gradebook', compact('submissions'));
    }

    // --- وظائف الطالب ---

    /**
     * عرض قائمة الاختبارات المتاحة للطالب
     */
    public function studentIndex() {
        $exams = \App\Models\Exam::with('subject')->latest()->get();
        return view('student.exams.index', compact('exams'));
    }

    /**
     * دخول قاعة الاختبار وعرض الأسئلة
     */
    public function takeExam($id) {
        $exam = \App\Models\Exam::with('questions')->findOrFail($id);
        return view('student.exams.take', compact('exam'));
    }

    /**
     * استقبال حلول الطالب وتصحيح الـ MCQ تلقائياً
     */

    /**
 * عرض قائمة تسليمات الطلاب للمدرس
 */
public function submissions(Request $request)
{
    // جلب التسليمات مع بيانات الطالب والاختبار والمادة
    $query = ExamSubmission::with(['student', 'exam.subject']);

    // إذا كان هناك فلتر لاختبار معين
    if ($request->has('exam_id')) {
        $query->where('exam_id', $request->exam_id);
    }

    $submissions = $query->latest()->get();

    // تأكد أن ملف الواجهة موجود في: resources/views/admin/exams/submissions.blade.php
    return view('admin.exams.submissions', compact('submissions'));
}

/**
 * 1. عرض واجهة مراجعة وتصحيح إجابات الطالب
 */
public function grade($id)
{
    // جلب التسليم مع كافة البيانات المرتبطة
    $submission = ExamSubmission::with(['answers.question', 'student', 'exam.subject'])->findOrFail($id);
    return view('admin.exams.grading', compact('submission'));
}

public function saveGrade(Request $request, $id)
{
    try {
        $submission = ExamSubmission::findOrFail($id);

        // تحديث درجات الأسئلة المقالية التي أرسلها المدرس
        if ($request->has('grades')) {
            foreach ($request->grades as $answerId => $points) {
                SubmissionAnswer::where('id', $answerId)->update([
                    'points_awarded' => $points
                ]);
            }
        }

        // إعادة حساب المجموع الكلي (تلقائي MCQ + يدوي Essay)
        $newTotalGrade = $submission->answers()->sum('points_awarded');

        $submission->update([
            'total_earned_grade' => $newTotalGrade,
            'status' => 'graded'
        ]);

        return response()->json([
            'success' => true,
            'title' => 'تم رصد الدرجات بنجاح ✅'
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
public function submitExam(Request $request, $id)
{
    try {
        return DB::transaction(function () use ($request, $id) {
            $exam = Exam::with('questions')->findOrFail($id);

            // 1. التأكد من وجود طالب واحد على الأقل في القاعدة (بما أننا في مرحلة التجربة)
            $student = \App\Models\Student::first();
            if (!$student) {
                return response()->json(['success' => false, 'error' => 'لا يوجد طلاب في القاعدة! سجل طالب أولاً.'], 422);
            }

            // 2. إنشاء رأس التسليم
            $submission = ExamSubmission::create([
                'exam_id' => $id,
                'student_id' => $student->id,
                'status' => 'pending',
                'total_earned_grade' => 0 // قيمة مبدئية
            ]);

            $autoGrade = 0;
            $answers = $request->input('answers', []); // مصفوفة الإجابات

            foreach ($exam->questions as $q) {
                $studentAns = isset($answers[$q->id]) ? $answers[$q->id] : null;
                $points = 0;
                $filePath = null;

                // تصحيح تلقائي للموضوعي
                if ($q->type == 'mcq') {
                    if ($studentAns == $q->correct_answer) {
                        $points = $q->points;
                    }
                }
                // رفع الملف للمقالي (إذا وجد)
                elseif ($q->type == 'essay') {
                    if ($request->hasFile("files.{$q->id}")) {
                        $filePath = $request->file("files.{$q->id}")->store('exams', 'public');
                    }
                }

                // حفظ تفاصيل الإجابة
                SubmissionAnswer::create([
                    'exam_submission_id' => $submission->id,
                    'question_id' => $q->id,
                    'answer_text' => is_array($studentAns) ? null : $studentAns,
                    'file_path' => $filePath,
                    'points_awarded' => ($q->type == 'mcq') ? $points : 0,
                ]);

                $autoGrade += $points;
            }

            // 3. تحديث الدرجة النهائية للتسليم
            $submission->update(['total_earned_grade' => $autoGrade]);

            return response()->json([
                'success' => true,
                'submission_id' => $submission->id
            ]);
        });
    } catch (\Exception $e) {
        // إرجاع الخطأ الحقيقي لنعرفه من الـ Console
        return response()->json([
            'success' => false,
            'error' => 'خطأ داخلي: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * عرض نتيجة الاختبار للطالب بعد التسليم
 */
public function showResult($id)
{
    // جلب التسليم مع تفاصيل الأسئلة والإجابات والمادة
    $submission = ExamSubmission::with(['exam.subject', 'answers.question'])
        ->findOrFail($id);

    // تأكد من أن ملف الواجهة موجود في: resources/views/student/exams/results.blade.php
    return view('student.exams.results', compact('submission'));
}


}
