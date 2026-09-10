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
use Illuminate\Support\Facades\Schema;

class ExamController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Exam::with(['subject', 'stage'])->withCount('questions');

        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
                if (!empty($user->subject_id)) {
                    $q->orWhere('subject_id', $user->subject_id);
                }
            });
        }

        $exams = $query->latest()->get();
        return view('admin.exams.index', compact('exams'));
    }

    public function show($id)
    {
        $exam = Exam::with(['subject', 'stage', 'questions'])->findOrFail($id);
        
        if (view()->exists('admin.exams.show')) {
            return view('admin.exams.show', compact('exam'));
        }

        return redirect()->route('admin.exams.index');
    }

    public function create()
    {
        $subjects = Subject::orderBy('name_ar')->get();
        $stages = Stage::with('subjects')->orderBy('grade_level', 'asc')->get();
        return view('admin.exams.create', compact('subjects', 'stages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'stage_id' => ['nullable', 'exists:stages,id'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.type' => ['required', 'in:mcq,essay'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.points' => ['required', 'integer', 'min:1'],
            'questions.*.image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'questions.*.a' => ['required_if:questions.*.type,mcq', 'nullable', 'string'],
            'questions.*.b' => ['required_if:questions.*.type,mcq', 'nullable', 'string'],
            'questions.*.c' => ['required_if:questions.*.type,mcq', 'nullable', 'string'],
            'questions.*.d' => ['required_if:questions.*.type,mcq', 'nullable', 'string'],
            'questions.*.correct_answer' => ['nullable'],
            'questions.*.require_file' => ['nullable'],
            'questions.*.is_multiple' => ['nullable'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $exam = Exam::create([
                'teacher_id' => auth()->id(),
                'title' => $validated['title'],
                'subject_id' => $validated['subject_id'],
                'stage_id' => $validated['stage_id'] ?? null,
                'duration_minutes' => $validated['duration_minutes'],
            ]);

            foreach ($request->questions as $q) {
                $imagePath = null;
                if (isset($q['image']) && $q['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $q['image']->store('questions', 'public');
                }

                $requireFileValue = (bool) filter_var($q['require_file'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $isMultipleValue = filter_var($q['is_multiple'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

                $correctAnswer = $q['correct_answer'] ?? null;
                if (is_array($correctAnswer)) {
                    $correctAnswer = json_encode($correctAnswer);
                }

                Question::create([
                    'exam_id' => $exam->id,
                    'type' => $q['type'],
                    'question_text' => $q['question_text'],
                    'image' => $imagePath,
                    'a' => $q['a'] ?? null,
                    'b' => $q['b'] ?? null,
                    'c' => $q['c'] ?? null,
                    'd' => $q['d'] ?? null,
                    'correct_answer' => $correctAnswer,
                    'points' => $q['points'],
                    'require_file' => $requireFileValue ? 1 : 0,
                    'is_multiple' => $isMultipleValue,
                ]);
            }
        });

        return response()->json(['message' => 'تم نشر الاختبار بنجاح 🚀'], 201);
    }

    public function edit(Exam $exam)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $exam->teacher_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل هذا الاختبار');
        }

        $stages = Stage::with('subjects')->get();
        $subjects = Subject::orderBy('name_ar')->get();
        return view('admin.exams.edit', compact('exam', 'stages', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $exam->teacher_id !== $user->id) {
            abort(403, 'غير مصرح لك بتحديث هذا الاختبار');
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks'      => 'nullable|integer|min:1',
            'pass_marks'       => 'nullable|integer|min:1',
            'is_active'        => 'nullable|boolean',
            'subject_id'       => 'nullable|exists:subjects,id',
            'stage_id'         => 'nullable|exists:stages,id',
        ]);

        $exam->update([
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? $exam->description,
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks'      => $validated['total_marks'] ?? $exam->total_marks,
            'pass_marks'       => $validated['pass_marks'] ?? $exam->pass_marks,
            'subject_id'       => $request->filled('subject_id') ? $request->subject_id : $exam->subject_id,
            'stage_id'         => $request->filled('stage_id') ? $request->stage_id : $exam->stage_id,
            'is_active'        => $request->has('is_active') ? filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN) : $exam->is_active,
        ]);

        return redirect()->route('admin.exams.index')->with('success', 'تم تحديث بيانات الاختبار بنجاح!');
    }

    // دالة حذف الاختبار المضافة حديثاً
    public function destroy($id)
    {
        $user = auth()->user();
        $exam = Exam::findOrFail($id);

        if ($user->role !== 'admin' && $exam->teacher_id !== $user->id) {
            return redirect()->back()->with('error', 'غير مصرح لك بحذف هذا الاختبار');
        }

        try {
            $exam->questions()->delete();
            $exam->delete();

            return redirect()->route('admin.exams.index')->with('success', 'تم حذف الاختبار بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    public function stats($id)
    {
        $exam = Exam::with(['subject', 'questions', 'submissions.student'])->findOrFail($id);
        $user = auth()->user();
        if ($user->role !== 'admin' && $exam->teacher_id !== $user->id) {
            abort(403, 'غير مصرح لك بعرض إحصائيات هذا الاختبار');
        }

        $stats = [
            'avg'   => $exam->submissions->avg('total_earned_grade') ?? 0,
            'max'   => $exam->submissions->max('total_earned_grade') ?? 0,
            'count' => $exam->submissions->count(),
        ];

        return view('admin.exams.stats', compact('exam', 'stats'));
    }

    public function submissions(Request $request)
    {
        $user = auth()->user();
        $query = ExamSubmission::with(['student', 'exam.subject']);

        if ($user->role !== 'admin') {
            $query->whereHas('exam', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        }

        if ($request->has('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        $submissions = $query->latest()->get();
        return view('admin.exams.submissions', compact('submissions'));
    }

    public function grade($id)
    {
        $submission = ExamSubmission::with(['answers.question', 'student', 'exam.subject'])->findOrFail($id);
        $user = auth()->user();
        if ($user->role !== 'admin' && $submission->exam->teacher_id !== $user->id) {
            abort(403, 'غير مصرح لك بتصحيح هذا التسليم');
        }

        return view('admin.exams.grading', compact('submission'));
    }

    public function saveGrade(Request $request, $id)
    {
        try {
            $submission = ExamSubmission::with('exam')->findOrFail($id);
            $user = auth()->user();
            if ($user->role !== 'admin' && $submission->exam->teacher_id !== $user->id) {
                return response()->json(['success' => false, 'error' => 'غير مصرح لك برصد الدرجات لهذا التسليم'], 403);
            }

            if ($request->has('grades')) {
                foreach ($request->grades as $answerId => $points) {
                    SubmissionAnswer::where('id', $answerId)->update([
                        'points_awarded' => $points
                    ]);
                }
            }

            $newTotalGrade = $submission->answers()->sum('points_awarded');
            $submission->update([
                'total_earned_grade' => $newTotalGrade,
                'status' => 'graded'
            ]);

            return response()->json(['success' => true, 'title' => 'تم رصد الدرجات بنجاح ✅']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // --- وظائف الطالب ---

    public function studentIndex()
    {
        $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        $studentStageId = $student ? $student->stage_id : null;
        $currentStageName = $student?->stage?->name_ar ?? 'الثانوية العامة (التوجيهي)';

        $enrollments = $student 
            ? \App\Models\Enrollment::where('student_id', $student->id)->where('status', 'active')->get()->keyBy('subject_id') 
            : collect();

        $allExams = Exam::with(['subject', 'stage', 'submissions' => function($query) use ($student) {
            if ($student) {
                $query->where('student_id', $student->id);
            }
        }])
        ->withCount('questions')
        ->when($studentStageId, function ($query) use ($studentStageId) {
            $query->where('stage_id', $studentStageId)->orWhereNull('stage_id');
        })
        ->latest()
        ->get();

        // تصفية الاختبارات بناءً على صلاحيات الوصول وخانات الاختيار [✓] التي حددها المعلم
        $exams = $allExams->filter(function ($exam) use ($student, $enrollments) {
            if (!$student) return true;

            $enr = $enrollments->get($exam->subject_id);
            if (!$enr) {
                // إذا لم يكن مسجلاً في المادة، يظهر الاختبار فقط إن كان تجريبياً عاماً
                return $exam->is_free ?? true;
            }

            // إذا كان اشتراكه كاملاً، تظهر جميع اختبارات المادة
            if ($enr->access_mode === 'all') {
                return true;
            }

            // إذا كان اشتراكاً مخصصاً، نفحص هل وضع المعلم علامة صح [✓] لهذا الطالب
            return \App\Models\ExamAssignment::where('enrollment_id', $enr->id)
                ->where('exam_id', $exam->id)
                ->where('is_visible', true)
                ->exists();
        })->values();

        return view('student.exams.index', compact('exams', 'student', 'currentStageName'));
    }

    public function takeExam($examId)
    {
        $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        if ($student instanceof \App\Models\User) {
            $student = $student->student ?? Student::where('id', $student->id)->orWhere('email', $student->email)->first();
        }

        if (!$student) {
            return redirect()->route('student.exams.index')->with('error', 'حساب الطالب غير مرتبط بشكل صحيح.');
        }

        $alreadySubmitted = ExamSubmission::where('exam_id', $examId)->where('student_id', $student->id)->exists();
        if ($alreadySubmitted) {
            return redirect()->route('student.exams.index')->with('error', 'عذراً، لقد قمت بتقديم هذا الاختبار مسبقاً ولا يمكنك الدخول إليه مرة أخرى.');
        }

        $exam = Exam::with(['questions', 'subject', 'stage'])->findOrFail($examId);
        return view('student.exams.take', compact('exam'));
    }

    public function submitExam(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
                if ($student instanceof \App\Models\User) {
                    $student = $student->student ?? Student::where('id', $student->id)->orWhere('email', $student->email)->first();
                }

                if (!$student) {
                    return response()->json(['success' => false, 'error' => 'لا يوجد بيانات طالب مسجلة لهذا الحساب!'], 422);
                }

                $exists = ExamSubmission::where('student_id', $student->id)->where('exam_id', $id)->exists();
                if ($exists) {
                    return response()->json(['success' => false, 'error' => 'لقد قمت بتقديم هذا الاختبار مسبقاً.'], 422);
                }

                $exam = Exam::with('questions')->findOrFail($id);

                $submission = ExamSubmission::create([
                    'exam_id' => $id,
                    'student_id' => $student->id,
                    'status' => 'pending',
                    'total_earned_grade' => 0
                ]);

                $autoGrade = 0;
                $answers = $request->input('answers', []);

                foreach ($exam->questions as $q) {
                    $studentAns = isset($answers[$q->id]) ? $answers[$q->id] : null;
                    $points = 0;
                    $filePath = null;

                    if ($q->type == 'mcq') {
                        if ($q->is_multiple) {
                            $userAnswers = array_map('strtolower', array_map('trim', (array)$studentAns));
                            
                            $decodedCorrect = json_decode($q->correct_answer, true);
                            $correctAnswers = is_array($decodedCorrect) 
                                ? array_map('strtolower', array_map('trim', $decodedCorrect)) 
                                : [strtolower(trim($q->correct_answer ?? ''))];

                            sort($userAnswers);
                            sort($correctAnswers);

                            if ($userAnswers === $correctAnswers && !empty($userAnswers)) {
                                $points = $q->points;
                            }
                        } else {
                            $userAnswer = strtolower(trim(is_array($studentAns) ? ($studentAns[0] ?? '') : ($studentAns ?? '')));
                            $correctAnswer = strtolower(trim($q->correct_answer ?? ''));

                            if (!empty($userAnswer) && $userAnswer === $correctAnswer) {
                                $points = $q->points;
                            }
                        }
                    } elseif ($q->type == 'essay') {
                        if ($request->hasFile("files.{$q->id}")) {
                            $filePath = $request->file("files.{$q->id}")->store('exams', 'public');
                        }
                    }

                    SubmissionAnswer::create([
                        'exam_submission_id' => $submission->id,
                        'question_id' => $q->id,
                        'answer_text' => is_array($studentAns) ? json_encode($studentAns) : $studentAns,
                        'file_path' => $filePath,
                        'points_awarded' => ($q->type == 'mcq') ? $points : 0,
                    ]);

                    $autoGrade += $points;
                }

                $submission->update(['total_earned_grade' => $autoGrade]);

                return response()->json([
                    'success' => true,
                    'message' => 'تم تسليم الاختبار بنجاح!',
                    'submission_id' => $submission->id,
                    'redirect' => route('student.exams.results', $submission->id)
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'خطأ داخلي: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showResult($id)
    {
        $submission = ExamSubmission::with(['exam.subject', 'answers.question'])->findOrFail($id);
        return view('student.exams.results', compact('submission'));
    }

    public function gradebook()
    {
        $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        if ($student instanceof \App\Models\User) {
            $student = $student->student ?? Student::where('id', $student->id)->orWhere('email', $student->email)->first();
        }

        if (!$student) {
            return redirect()->route('student.exams.index')->with('error', 'سجل الدرجات غير متاح لعدم وجود بيانات طالب مرتبطة.');
        }

        $submissions = ExamSubmission::with(['exam.subject', 'answers.question'])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return view('student.exams.gradebook', compact('submissions'));
    }
}