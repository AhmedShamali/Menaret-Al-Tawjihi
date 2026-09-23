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
        $query = Exam::with(['subject', 'stage'])->withCount(['questions', 'submissions']);

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

        return redirect()->route('admin.exams.edit', $exam->id);
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
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'show_result_immediately' => ['nullable'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.type' => ['required', 'in:mcq,essay,text'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.points' => ['required', 'integer', 'min:1'],
            'questions.*.image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'questions.*.a' => ['nullable', 'string'],
            'questions.*.b' => ['nullable', 'string'],
            'questions.*.c' => ['nullable', 'string'],
            'questions.*.d' => ['nullable', 'string'],
            'questions.*.a_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'questions.*.b_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'questions.*.c_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'questions.*.d_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'questions.*.correct_answer' => ['nullable'],
            'questions.*.require_file' => ['nullable'],
            'questions.*.is_multiple' => ['nullable'],
        ]);

        $showResultImmediately = filter_var($request->input('show_result_immediately', false), FILTER_VALIDATE_BOOLEAN);
        $sub = Subject::find($validated['subject_id']);
        $effectiveStageId = $validated['stage_id'] ?? $sub?->stage_id;

        DB::transaction(function () use ($request, $validated, $showResultImmediately, $effectiveStageId) {
            $exam = Exam::create([
                'teacher_id' => auth()->id(),
                'title' => $validated['title'],
                'subject_id' => $validated['subject_id'],
                'stage_id' => $effectiveStageId,
                'duration_minutes' => $validated['duration_minutes'],
                'starts_at' => !empty($validated['starts_at']) ? \Carbon\Carbon::parse($validated['starts_at']) : null,
                'ends_at' => !empty($validated['ends_at']) ? \Carbon\Carbon::parse($validated['ends_at']) : null,
                'show_result_immediately' => $showResultImmediately,
            ]);

            foreach ($request->questions as $index => $q) {
                $imagePath = null;
                $imageFile = $request->file("questions.{$index}.image") ?? ($q['image'] ?? null);

                if ($imageFile instanceof \Illuminate\Http\UploadedFile && $imageFile->isValid()) {
                    if (!app()->environment('testing') && !empty(config('filesystems.disks.supabase.key'))) {
                        try {
                            $path = $imageFile->store('questions', 'supabase');
                            $imagePath = Storage::disk('supabase')->url($path);
                        } catch (\Throwable $e) {
                            $imagePath = $imageFile->store('questions', 'public');
                        }
                    } else {
                        $imagePath = $imageFile->store('questions', 'public');
                    }
                }

                // معالجة صور خيارات الاختيار من متعدد (A, B, C, D)
                $optImages = [];
                foreach (['a', 'b', 'c', 'd'] as $opt) {
                    $optFile = $request->file("questions.{$index}.{$opt}_image") ?? ($q["{$opt}_image"] ?? null);
                    $optPath = null;
                    if ($optFile instanceof \Illuminate\Http\UploadedFile && $optFile->isValid()) {
                        if (!app()->environment('testing') && !empty(config('filesystems.disks.supabase.key'))) {
                            try {
                                $p = $optFile->store('question_options', 'supabase');
                                $optPath = Storage::disk('supabase')->url($p);
                            } catch (\Throwable $e) {
                                $optPath = $optFile->store('question_options', 'public');
                            }
                        } else {
                            $optPath = $optFile->store('question_options', 'public');
                        }
                    }
                    $optImages[$opt] = $optPath;
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
                    'a_image' => $optImages['a'],
                    'b' => $q['b'] ?? null,
                    'b_image' => $optImages['b'],
                    'c' => $q['c'] ?? null,
                    'c_image' => $optImages['c'],
                    'd' => $q['d'] ?? null,
                    'd_image' => $optImages['d'],
                    'correct_answer' => $correctAnswer,
                    'points' => $q['points'],
                    'require_file' => $requireFileValue ? 1 : 0,
                    'is_multiple' => $isMultipleValue,
                ]);
            }
        });

        try {
            $stageId = $effectiveStageId;
            if ($stageId && $sub) {
                \App\Services\NotificationService::notifyStageStudents(
                    $stageId,
                    'اختبار تقييمي جديد 📝',
                    "تم نشر اختبار جديد: \"{$validated['title']}\" في مبحث {$sub->name_ar}. اختبر معلوماتك الآن!",
                    'exam',
                    route('student.exams.index')
                );
            }
        } catch (\Throwable $e) {}

        return response()->json(['message' => 'تم نشر الاختبار بنجاح 🚀'], 201);
    }

    /**
     * التحقق من صلاحية المعلم أو المدير على الاختبار
     */
    protected function isUserAuthorizedForExam($user, Exam $exam): bool
    {
        if (!$user) {
            return false;
        }
        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'teacher') {
            $examTeacherId = $exam->teacher_id ?? $exam->subject?->user_id;
            return ((int) $examTeacherId === (int) $user->id);
        }
        return false;
    }

    /**
     * التحقق من صلاحية المعلم أو المدير على تسليم الاختبار
     */
    protected function isUserAuthorizedForExamSubmission($user, ExamSubmission $submission): bool
    {
        if (!$user) {
            return false;
        }
        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'teacher') {
            $exam = $submission->exam;
            if (!$exam) {
                return false;
            }
            return $this->isUserAuthorizedForExam($user, $exam);
        }
        return false;
    }

    public function edit(Exam $exam)
    {
        $user = auth()->user();
        if (!$this->isUserAuthorizedForExam($user, $exam)) {
            abort(403, 'غير مصرح لك بتعديل هذا الاختبار');
        }

        $stages = Stage::with('subjects')->get();
        $subjects = Subject::orderBy('name_ar')->get();
        return view('admin.exams.edit', compact('exam', 'stages', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $user = auth()->user();
        if (!$this->isUserAuthorizedForExam($user, $exam)) {
            abort(403, 'غير مصرح لك بتحديث هذا الاختبار');
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks'      => 'nullable|integer|min:1',
            'pass_marks'       => 'nullable|integer|min:1',
            'starts_at'        => 'nullable|date',
            'ends_at'          => 'nullable|date',
            'is_active'        => 'nullable|boolean',
            'show_result_immediately' => 'nullable',
            'subject_id'       => 'nullable|exists:subjects,id',
            'stage_id'         => 'nullable|exists:stages,id',
        ]);

        $showResultImmediately = $request->has('show_result_immediately') 
            ? filter_var($request->show_result_immediately, FILTER_VALIDATE_BOOLEAN) 
            : $exam->show_result_immediately;

        $updateData = [
            'title'            => $validated['title'],
            'duration_minutes' => $validated['duration_minutes'],
            'starts_at'        => $request->filled('starts_at') ? \Carbon\Carbon::parse($request->starts_at) : null,
            'ends_at'          => $request->filled('ends_at') ? \Carbon\Carbon::parse($request->ends_at) : null,
            'show_result_immediately' => $showResultImmediately,
            'subject_id'       => $request->filled('subject_id') ? $request->subject_id : $exam->subject_id,
            'stage_id'         => $request->filled('stage_id') ? $request->stage_id : $exam->stage_id,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('exams', 'is_active')) {
            if ($request->has('is_active')) {
                $updateData['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
            }
        }

        $exam->update($updateData);

        // تحديث ومزامنة أسئلة الاختبار وصورها وصور الخيارات إن تم إرسالها
        if ($request->has('questions') && is_array($request->questions)) {
            $submittedQuestionIds = [];
            foreach ($request->questions as $index => $qData) {
                if (empty($qData['question_text'])) continue;

                $qId = $qData['id'] ?? null;
                $existingQ = $qId ? Question::where('exam_id', $exam->id)->find($qId) : null;

                $imagePath = $existingQ ? $existingQ->image : ($qData['existing_image'] ?? null);
                if ($request->hasFile("questions.{$index}.image")) {
                    $imageFile = $request->file("questions.{$index}.image");
                    if ($imageFile->isValid()) {
                        if (!app()->environment('testing') && !empty(config('filesystems.disks.supabase.key'))) {
                            try {
                                $path = $imageFile->store('questions', 'supabase');
                                $imagePath = Storage::disk('supabase')->url($path);
                            } catch (\Throwable $e) {
                                $imagePath = $imageFile->store('questions', 'public');
                            }
                        } else {
                            $imagePath = $imageFile->store('questions', 'public');
                        }
                    }
                } elseif (!empty($qData['remove_image']) && $qData['remove_image'] == '1') {
                    $imagePath = null;
                }

                // معالجة صور خيارات الاختيار من متعدد (A, B, C, D)
                $optImages = [];
                foreach (['a', 'b', 'c', 'd'] as $opt) {
                    $field = $opt . '_image';
                    $existingOptImg = $existingQ ? $existingQ->$field : ($qData["existing_{$opt}_image"] ?? null);
                    if ($request->hasFile("questions.{$index}.{$opt}_image")) {
                        $optFile = $request->file("questions.{$index}.{$opt}_image");
                        if ($optFile->isValid()) {
                            if (!app()->environment('testing') && !empty(config('filesystems.disks.supabase.key'))) {
                                try {
                                    $p = $optFile->store('question_options', 'supabase');
                                    $existingOptImg = Storage::disk('supabase')->url($p);
                                } catch (\Throwable $e) {
                                    $existingOptImg = $optFile->store('question_options', 'public');
                                }
                            } else {
                                $existingOptImg = $optFile->store('question_options', 'public');
                            }
                        }
                    } elseif (!empty($qData["remove_{$opt}_image"]) && $qData["remove_{$opt}_image"] == '1') {
                        $existingOptImg = null;
                    }
                    $optImages[$field] = $existingOptImg;
                }

                $requireFileValue = (bool) filter_var($qData['require_file'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $isMultipleValue = filter_var($qData['is_multiple'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                $correctAnswer = $qData['correct_answer'] ?? null;
                if (is_array($correctAnswer)) {
                    $correctAnswer = json_encode($correctAnswer);
                }

                $dataToSave = [
                    'exam_id'        => $exam->id,
                    'type'           => $qData['type'] ?? 'mcq',
                    'question_text'  => $qData['question_text'],
                    'image'          => $imagePath,
                    'a'              => $qData['a'] ?? null,
                    'a_image'        => $optImages['a_image'],
                    'b'              => $qData['b'] ?? null,
                    'b_image'        => $optImages['b_image'],
                    'c'              => $qData['c'] ?? null,
                    'c_image'        => $optImages['c_image'],
                    'd'              => $qData['d'] ?? null,
                    'd_image'        => $optImages['d_image'],
                    'correct_answer' => $correctAnswer,
                    'points'         => !empty($qData['points']) ? (int)$qData['points'] : ($existingQ->points ?? 5),
                    'require_file'   => $requireFileValue ? 1 : 0,
                    'is_multiple'    => $isMultipleValue,
                ];

                if ($existingQ) {
                    $existingQ->update($dataToSave);
                    $submittedQuestionIds[] = $existingQ->id;
                } else {
                    $newQ = Question::create($dataToSave);
                    $submittedQuestionIds[] = $newQ->id;
                }
            }

            if (!empty($submittedQuestionIds)) {
                Question::where('exam_id', $exam->id)->whereNotIn('id', $submittedQuestionIds)->delete();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تحديث بيانات الاختبار بنجاح!']);
        }

        $redirectRoute = ($user->role === 'admin') ? 'admin.exams.index' : 'teacher.exams.index';
        return redirect()->route($redirectRoute)->with('success', 'تم تحديث بيانات الاختبار بنجاح!');
    }

    // دالة حذف الاختبار المضافة حديثاً
    public function destroy($id)
    {
        $user = auth()->user();
        $exam = Exam::with('subject')->findOrFail($id);

        if (!$this->isUserAuthorizedForExam($user, $exam)) {
            return redirect()->back()->with('error', 'غير مصرح لك بحذف هذا الاختبار');
        }

        try {
            $exam->questions()->delete();
            $exam->delete();

            $redirectRoute = ($user->role === 'admin') ? 'admin.exams.index' : 'teacher.exams.index';
            return redirect()->route($redirectRoute)->with('success', 'تم حذف الاختبار بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    public function stats($id)
    {
        $exam = Exam::with(['subject', 'questions', 'submissions.student'])->findOrFail($id);
        $user = auth()->user();
        if (!$this->isUserAuthorizedForExam($user, $exam)) {
            abort(403, 'غير مصرح لك بعرض إحصائيات هذا الاختبار');
        }

        $stats = [
            'avg'   => $exam->submissions->avg('total_earned_grade') ?? 0,
            'max'   => $exam->submissions->max('total_earned_grade') ?? 0,
            'count' => $exam->submissions->count(),
        ];

        return view('admin.exams.stats', compact('exam', 'stats'));
    }

    public function submissions(Request $request, $exam = null)
    {
        $user = auth()->user();
        $query = ExamSubmission::with(['student', 'exam.subject']);

        if ($user->role !== 'admin') {
            $query->whereHas('exam', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        }

        $examId = $exam ?? $request->route('exam') ?? $request->exam_id;
        if ($examId instanceof Exam) {
            $examId = $examId->id;
        }

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        $submissions = $query->latest()->get();
        return view('admin.exams.submissions', compact('submissions'));
    }

    public function grade($id)
    {
        $submission = ExamSubmission::with(['answers.question', 'student', 'exam.subject'])->findOrFail($id);
        $user = auth()->user();
        if (!$this->isUserAuthorizedForExamSubmission($user, $submission)) {
            abort(403, 'غير مصرح لك بتصحيح هذا التسليم');
        }

        return view('admin.exams.grading', compact('submission'));
    }

    public function saveGrade(Request $request, $id)
    {
        try {
            $submission = ExamSubmission::with(['exam.subject', 'answers'])->findOrFail($id);
            $user = auth()->user();
            if (!$this->isUserAuthorizedForExamSubmission($user, $submission)) {
                return response()->json(['success' => false, 'error' => 'غير مصرح لك برصد الدرجات لهذا التسليم'], 403);
            }

            if ($request->has('grades')) {
                foreach ($request->grades as $answerId => $points) {
                    SubmissionAnswer::where('id', $answerId)->update([
                        'points_awarded' => (float) $points
                    ]);
                }
            }

            $deductionAmount = max(0, (float) $request->input('deduction_amount', 0));
            $deductionReason = $request->input('deduction_reason');
            $teacherNotes = $request->input('teacher_notes');

            // إذا كان هناك تعديل يدوي مباشر للدرجة الأولية أو استخدام مجموع الأسئلة
            $subtotalGrade = (float) $submission->answers()->sum('points_awarded');
            if ($request->filled('override_total_grade')) {
                $subtotalGrade = (float) $request->input('override_total_grade');
            }

            $finalEarnedGrade = max(0, $subtotalGrade - $deductionAmount);

            $submission->update([
                'total_earned_grade' => $finalEarnedGrade,
                'deduction_amount'   => $deductionAmount,
                'deduction_reason'   => $deductionReason,
                'teacher_notes'      => $teacherNotes,
                'status'             => 'graded',
                'is_published'       => true,
            ]);

            try {
                $msg = "قام أستاذ المادة بتصحيح ورصد درجتك في اختبار: \"{$submission->exam->title}\". الدرجة المحتسبة: {$finalEarnedGrade}/{$submission->exam->total_grade}.";
                if ($deductionAmount > 0) {
                    $msg .= " (تم تطبيق خصم أكاديمي بقيمة {$deductionAmount} علامة. السبب: {$deductionReason}).";
                }

                \App\Services\NotificationService::notifyStudent(
                    $submission->student_id,
                    'تم تصحيح اختبارك ورصد الدرجة! 🌟',
                    $msg,
                    'grade',
                    route('student.exams.results', $submission->id),
                    'fa-award'
                );
            } catch (\Throwable $e) {}

            return response()->json([
                'success' => true, 
                'title' => 'تم رصد الدرجات بنجاح ✅',
                'final_grade' => $finalEarnedGrade,
                'deduction_amount' => $deductionAmount,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // --- وظائف الطالب ---

    public function studentIndex()
    {
        $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        if ($student instanceof \App\Models\User) {
            $student = $student->student ?? Student::where('id', $student->id)->orWhere('email', $student->email)->first();
        }
        $studentStageId = $student ? $student->stage_id : null;
        $currentStageName = $student?->stage?->name_ar ?? 'الثانوية العامة (التوجيهي)';

        $enrollments = $student 
            ? \App\Models\Enrollment::where('student_id', $student->id)->where('status', 'active')->get()->keyBy('subject_id') 
            : collect();

        $enrolledSubjectIds = $enrollments->keys()->toArray();

        // حصر قطعي: إذا كان الحساب طالباً وليس لديه أي اشتراك نشط في أي مادة، لا تظهر له أي اختبارات
        if ($student && empty($enrolledSubjectIds)) {
            $exams = collect();
            return view('student.exams.index', compact('exams', 'student', 'currentStageName'));
        }

        $allExamsQuery = Exam::with(['subject', 'stage', 'submissions' => function($query) use ($student) {
            if ($student) {
                $query->where('student_id', $student->id);
            }
        }])
        ->withCount('questions');

        if ($student && !empty($enrolledSubjectIds)) {
            $allExamsQuery->whereIn('subject_id', $enrolledSubjectIds);
        } elseif ($studentStageId) {
            $allExamsQuery->where(function ($q) use ($studentStageId) {
                $q->where('stage_id', $studentStageId)
                  ->orWhere(function ($subQ) use ($studentStageId) {
                      $subQ->whereNull('stage_id')
                           ->whereHas('subject', function ($sQ) use ($studentStageId) {
                               $sQ->where('stage_id', $studentStageId);
                           });
                  });
            });
        }

        $allExams = $allExamsQuery->latest()->get();

        // تصفية الاختبارات بناءً على صلاحيات الوصول وخانات الاختيار [✓] التي حددها المعلم
        $exams = $allExams->filter(function ($exam) use ($student, $enrollments) {
            if (!$student) return true;

            $enr = $enrollments->get($exam->subject_id);
            if (!$enr) {
                // الطالب غير مسجل في هذه المادة نهائياً -> حجب قطعي
                return false;
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

        $submission = ExamSubmission::where('exam_id', $examId)->where('student_id', $student->id)->latest()->first();
        $hasCompletedAnswers = $submission && $submission->answers()->exists();
        if ($hasCompletedAnswers && !$submission->allow_retake) {
            return redirect()->route('student.exams.results', $submission->id)->with('info', 'عذراً، لقد قمت بتقديم هذا الاختبار مسبقاً. وفقاً للأنظمة الأكاديمية لا يمكن إعادة المحاولة إلا بعد الحصول على إذن من أستاذ المادة.');
        }

        $exam = Exam::with(['questions', 'subject', 'stage'])->findOrFail($examId);

        // التحقق الصارم: هل الطالب مسجل ومشترك في مادة هذا الاختبار؟
        $enr = \App\Models\Enrollment::where('student_id', $student->id)
            ->where('subject_id', $exam->subject_id)
            ->where('status', 'active')
            ->first();

        if (!$enr) {
            return redirect()->route('student.exams.index')->with('error', 'عذراً، هذا الاختبار متاح فقط للطلبة المسجلين والمشتركين في هذه المادة.');
        }

        if ($enr->access_mode !== 'all') {
            $hasAccess = \App\Models\ExamAssignment::where('enrollment_id', $enr->id)
                ->where('exam_id', $exam->id)
                ->where('is_visible', true)
                ->exists();
            if (!$hasAccess) {
                return redirect()->route('student.exams.index')->with('error', 'عذراً، هذا الاختبار غير مفعل في خطتك الدراسية المخصصة.');
            }
        }

        // التحقق من توقيت وجدولة الاختبار
        if ($exam->isUpcoming()) {
            $formattedStart = $exam->starts_at->timezone(config('app.timezone', 'Asia/Gaza'))->format('Y/m/d - h:i A');
            return redirect()->route('student.exams.index')->with('error', "هذا الاختبار لم يبدأ موعده بعد. موعد البدء الرسمي: {$formattedStart}");
        }

        if ($exam->isExpired()) {
            $formattedEnd = $exam->ends_at->timezone(config('app.timezone', 'Asia/Gaza'))->format('Y/m/d - h:i A');
            return redirect()->route('student.exams.index')->with('error', "عذراً، انتهت الفترة الزمنية المتاحة لتقديم هذا الاختبار في: {$formattedEnd}");
        }

        return view('student.exams.take', compact('exam', 'submission'));
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
                    return response()->json([
                        'success' => false, 
                        'message' => 'لا يوجد بيانات طالب مسجلة لهذا الحساب!',
                        'error'   => 'لا يوجد بيانات طالب مسجلة لهذا الحساب!'
                    ], 422);
                }

                $exam = Exam::with('questions')->findOrFail($id);

                // التحقق الصارم من اشتراك الطالب في المادة عند الإرسال
                $enr = \App\Models\Enrollment::where('student_id', $student->id)
                    ->where('subject_id', $exam->subject_id)
                    ->where('status', 'active')
                    ->first();

                if (!$enr) {
                    return response()->json([
                        'success' => false,
                        'message' => 'عذراً، لا يمكنك تسليم اختبار لمادة غير مسجل بها أو غير مشترك فيها!',
                        'error'   => 'عذراً، لا يمكنك تسليم اختبار لمادة غير مسجل بها أو غير مشترك فيها!'
                    ], 403);
                }

                if ($enr->access_mode !== 'all') {
                    $hasAccess = \App\Models\ExamAssignment::where('enrollment_id', $enr->id)
                        ->where('exam_id', $exam->id)
                        ->where('is_visible', true)
                        ->exists();
                    if (!$hasAccess) {
                        return response()->json([
                            'success' => false,
                            'message' => 'عذراً، هذا الاختبار غير مفعل في خطتك الدراسية المخصصة!',
                            'error'   => 'عذراً، هذا الاختبار غير مفعل في خطتك الدراسية المخصصة!'
                        ], 403);
                    }
                }

                if ($exam->isUpcoming()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'عذراً، هذا الاختبار لم يبدأ موعده الرسمي بعد!',
                        'error'   => 'عذراً، هذا الاختبار لم يبدأ موعده الرسمي بعد!'
                    ], 422);
                }

                if ($exam->isExpired()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'عذراً، انتهت الفترة الزمنية المحددة لتقديم هذا الاختبار!',
                        'error'   => 'عذراً، انتهت الفترة الزمنية المحددة لتقديم هذا الاختبار!'
                    ], 422);
                }

                $submission = ExamSubmission::where('student_id', $student->id)->where('exam_id', $id)->latest()->first();
                $hasCompletedAnswers = $submission && $submission->answers()->exists();

                if ($hasCompletedAnswers && !$submission->allow_retake) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'لقد قمت بتقديم هذا الاختبار مسبقاً ولا يمكن إعادته إلا بعد موافقة المعلم.',
                        'error'   => 'لقد قمت بتقديم هذا الاختبار مسبقاً ولا يمكن إعادته إلا بعد موافقة المعلم.'
                    ], 422);
                }

                $exam = Exam::with('questions')->findOrFail($id);

                if ($submission) {
                    $submission->answers()->delete();
                    $submission->update([
                        'status' => 'pending',
                        'total_earned_grade' => 0,
                        'allow_retake' => false,
                        'retake_requested' => false,
                    ]);
                } else {
                    $submission = ExamSubmission::create([
                        'exam_id' => $id,
                        'student_id' => $student->id,
                        'status' => 'pending',
                        'total_earned_grade' => 0,
                        'allow_retake' => false,
                        'retake_requested' => false,
                    ]);
                }

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
                                : [strtolower(trim((string)($q->correct_answer ?? '')))];

                            sort($userAnswers);
                            sort($correctAnswers);

                            if ($userAnswers === $correctAnswers && !empty($userAnswers)) {
                                $points = $q->points;
                            }
                        } else {
                            $userAnswer = strtolower(trim((string)(is_array($studentAns) ? ($studentAns[0] ?? '') : ($studentAns ?? ''))));
                            $correctAnswer = strtolower(trim((string)($q->correct_answer ?? '')));

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

                $tabSwitches = (int) $request->input('tab_switches_count', 0);
                $screenshots = (int) $request->input('screenshots_count', 0);
                $rawCheatingFlags = $request->input('cheating_flags');
                $incomingFlags = [];
                if (is_string($rawCheatingFlags)) {
                    $incomingFlags = json_decode($rawCheatingFlags, true) ?? [];
                } elseif (is_array($rawCheatingFlags)) {
                    $incomingFlags = $rawCheatingFlags;
                }

                $totalTabSwitches = max($tabSwitches, (int) ($submission->tab_switches_count ?? 0));
                $totalScreenshots = max($screenshots, (int) ($submission->screenshots_count ?? 0));
                $existingFlags = is_array($submission->cheating_flags) ? $submission->cheating_flags : (json_decode((string)$submission->cheating_flags, true) ?? []);
                
                $mergedFlags = [];
                $seen = [];
                foreach (array_merge($existingFlags, $incomingFlags) as $flag) {
                    $k = is_array($flag) ? json_encode($flag) : (string)$flag;
                    if (!isset($seen[$k])) {
                        $seen[$k] = true;
                        $mergedFlags[] = $flag;
                    }
                }

                $hasCheatingRisk = ($totalTabSwitches > 0 || $totalScreenshots > 0 || !empty($mergedFlags));

                $allQuestionsMcq = $exam->questions->isNotEmpty() && $exam->questions->every(fn($q) => $q->type === 'mcq');
                $isPublished = (bool) ($exam->show_result_immediately && $allQuestionsMcq);

                $submission->update([
                    'total_earned_grade' => $autoGrade,
                    'status' => $isPublished ? 'graded' : 'pending',
                    'tab_switches_count' => $totalTabSwitches,
                    'screenshots_count' => $totalScreenshots,
                    'cheating_flags' => $mergedFlags,
                    'has_cheating_risk' => $hasCheatingRisk,
                    'is_published' => $isPublished,
                ]);

                // إشعار المعلم وإدارة المنصة بتسليم الاختبار
                try {
                    $teacherId = $exam->teacher_id ?? $exam->subject?->user_id;
                    $studentName = $student->name_ar ?? $student->name ?? 'طالب';

                    if ($hasCheatingRisk && $teacherId) {
                        \App\Services\NotificationService::notifyTeacher(
                            $teacherId,
                            '⚠️ تنبيه اشتباه غش في الاختبار!',
                            "قام الطالب ({$studentName}) بتسليم اختبار \"{$exam->title}\" مع رصد مخالفات أكاديمية ({$totalTabSwitches} مغادرة صفحة، {$totalScreenshots} لقطة شاشة).",
                            'cheating_alert',
                            route('teacher.submissions.grade', $submission->id),
                            'fa-triangle-exclamation'
                        );
                    }

                    if ($teacherId) {
                        \App\Services\NotificationService::notifyTeacher(
                            $teacherId,
                            'تسليم اختبار جديد ✍️',
                            "قام الطالب ({$studentName}) بتسليم إجاباته في اختبار: \"{$exam->title}\". الدرجة المحتسبة: {$autoGrade}/{$exam->total_grade}.",
                            'exam',
                            route('teacher.submissions.index')
                        );
                    } else {
                        \App\Services\NotificationService::notifyAdmin(
                            'تسليم اختبار جديد ✍️',
                            "قام الطالب ({$studentName}) بتسليم إجاباته في اختبار: \"{$exam->title}\". الدرجة المحتسبة: {$autoGrade}/{$exam->total_grade}.",
                            'exam',
                            route('admin.submissions.index')
                        );
                    }

                    // إشعار الطالب بالتسليم الناجح
                    $studentMsg = $isPublished
                        ? "تم استلام إجاباتك في اختبار: \"{$exam->title}\" بنجاح. نتيجتك المحتسبة: {$autoGrade} علامة."
                        : "تم استلام وتوثيق إجاباتك في اختبار: \"{$exam->title}\" بنجاح. النتيجة قيد المراجعة والتدقيق من قبل معلّم المساق.";

                    \App\Services\NotificationService::notifyStudent(
                        $student->id,
                        'تم تسليم الاختبار بنجاح ✅',
                        $studentMsg,
                        'exam',
                        route('student.exams.results', $submission->id)
                    );
                } catch (\Throwable $e) {}

                return response()->json([
                    'success' => true,
                    'message' => $isPublished 
                        ? 'تم تسليم الاختبار بنجاح واحتساب نتيجتك!' 
                        : 'تم تسليم الاختبار بنجاح! الإجابات قيد التدقيق والمراجعة من قبل معلّم المساق.',
                    'submission_id' => $submission->id,
                    'is_published' => $isPublished,
                    'redirect' => route('student.exams.results', $submission->id)
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Exam Submission Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'تعذر حفظ وتسليم الاختبار: ' . $e->getMessage(),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function showResult($id)
    {
        $submission = ExamSubmission::with(['exam.subject', 'exam.questions', 'answers.question'])->findOrFail($id);

        $actor = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        if ($actor instanceof \App\Models\User && !in_array($actor->role, ['admin', 'teacher'])) {
            $actor = $actor->student ?? Student::where('id', $actor->id)->orWhere('email', $actor->email)->first();
        }

        // حماية الخصوصية: منع أي طالب من استعراض نتائج طالب آخر
        if ($actor instanceof \App\Models\Student && $submission->student_id && (int)$submission->student_id !== (int)$actor->id) {
            return redirect()->route('student.exams.index')->with('error', 'عذراً، لا تملك صلاحية الاطلاع على نتيجة هذا الاختبار.');
        }

        $canViewResult = $submission->canStudentViewResult();
        return view('student.exams.results', compact('submission', 'canViewResult'));
    }

    /**
     * رصد وتسجيل حركة اشتباه غش لحظياً من متصفح الطالب
     */
    public function reportCheatingIncident(Request $request, $id)
    {
        $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        if ($student instanceof \App\Models\User) {
            $student = $student->student ?? Student::where('id', $student->id)->orWhere('email', $student->email)->first();
        }

        if (!$student) {
            return response()->json(['success' => false, 'error' => 'حساب الطالب غير مصرح'], 403);
        }

        $exam = Exam::with(['subject'])->findOrFail($id);
        $type = $request->input('violation_type', 'unknown');
        $details = $request->input('details', 'حركة مريبة أثناء الامتحان');
        $time = now()->toDateTimeString();

        $submission = ExamSubmission::where('exam_id', $id)->where('student_id', $student->id)->latest()->first();
        if (!$submission) {
            $submission = ExamSubmission::create([
                'exam_id' => $id,
                'student_id' => $student->id,
                'status' => 'pending',
                'total_earned_grade' => 0,
                'allow_retake' => true, // يضمن عدم حظر تسليم الاختبار الفعلي للطالب
                'has_cheating_risk' => true,
                'tab_switches_count' => ($type === 'tab_switch' ? 1 : 0),
                'screenshots_count' => ($type === 'screenshot' ? 1 : 0),
                'cheating_flags' => [
                    ['type' => $type, 'details' => $details, 'time' => $time]
                ],
            ]);
        } else {
            $currentFlags = is_array($submission->cheating_flags) ? $submission->cheating_flags : (json_decode((string)$submission->cheating_flags, true) ?? []);
            $currentFlags[] = ['type' => $type, 'details' => $details, 'time' => $time];
            $submission->update([
                'has_cheating_risk' => true,
                'tab_switches_count' => (int)$submission->tab_switches_count + ($type === 'tab_switch' ? 1 : 0),
                'screenshots_count' => (int)$submission->screenshots_count + ($type === 'screenshot' ? 1 : 0),
                'cheating_flags' => $currentFlags,
            ]);
        }

        try {
            $teacherId = $exam->teacher_id ?? $exam->subject?->user_id;
            $studentName = $student->name_ar ?? $student->name ?? 'طالب';
            $actionText = $type === 'screenshot' ? 'محاولة أخذ لقطة شاشة (Screenshot)' : 'مغادرة نافذة/تبويب الاختبار';

            if ($teacherId) {
                \App\Services\NotificationService::notifyTeacher(
                    $teacherId,
                    '⚠️ رصد حركة مريبة في الامتحان!',
                    "تم رصد الطالب ({$studentName}) أثناء تأدية اختبار \"{$exam->title}\": {$actionText}.",
                    'cheating_alert',
                    route('teacher.exams.submissions', $exam->id),
                    'fa-triangle-exclamation'
                );
            }
        } catch (\Throwable $e) {}

        return response()->json(['success' => true, 'logged' => true, 'status' => 'logged']);
    }

    /**
     * إعلان ونشر النتيجة للطالب أو حجبها من قِبل المعلم/المدير
     */
    public function togglePublishResult($id)
    {
        $user = auth()->user();
        $submission = ExamSubmission::with(['exam.subject', 'student'])->findOrFail($id);

        if (!$this->isUserAuthorizedForExamSubmission($user, $submission)) {
            return response()->json(['success' => false, 'error' => 'غير مصرح لك بنشر نتائج هذا الاختبار'], 403);
        }

        $newStatus = !$submission->is_published;
        $submission->update(['is_published' => $newStatus]);

        if ($newStatus) {
            try {
                \App\Services\NotificationService::notifyStudent(
                    $submission->student_id,
                    'صدرت نتيجتك في الاختبار! 🎓',
                    "أعلن معلّم المساق نتائج اختبار: \"{$submission->exam->title}\". يمكنك الآن الاطلاع على درجتك وإجاباتك.",
                    'exam',
                    route('student.exams.results', $submission->id),
                    'fa-award'
                );
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'is_published' => $newStatus,
            'message' => $newStatus ? 'تم إعلان النتيجة للطالب بنجاح 🎉' : 'تم حجب النتيجة عن الطالب 🔒'
        ]);
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

    /**
     * طلب إذن إعادة الاختبار من المعلم (للطالب)
     */
    public function requestRetake(Request $request, $id)
    {
        $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        if ($student instanceof \App\Models\User) {
            $student = $student->student ?? Student::where('id', $student->id)->orWhere('email', $student->email)->first();
        }

        if (!$student) {
            return response()->json(['success' => false, 'error' => 'حساب الطالب غير مصرح'], 403);
        }

        $submission = ExamSubmission::with('exam.subject')->where('exam_id', $id)->where('student_id', $student->id)->latest()->firstOrFail();

        $submission->update([
            'retake_requested' => true,
            'retake_request_notes' => $request->notes ?? 'يرغب الطالب في إعادة الاختبار لتحسين تحصيله أو معالجة عذر تقني.'
        ]);

        try {
            $exam = $submission->exam;
            $teacherId = $exam->teacher_id ?? $exam->subject?->user_id;
            $studentName = $student->name_ar ?? $student->name ?? 'طالب';

            if ($teacherId) {
                \App\Services\NotificationService::notifyTeacher(
                    $teacherId,
                    'طلب إعادة اختبار من طالب 🔄',
                    "طلب الطالب ({$studentName}) إذناً لإعادة اختبار: \"{$exam->title}\".",
                    'exam',
                    route('teacher.submissions.index', ['exam_id' => $exam->id]),
                    'fa-rotate-right'
                );
            } else {
                \App\Services\NotificationService::notifyAdmin(
                    'طلب إعادة اختبار من طالب 🔄',
                    "طلب الطالب ({$studentName}) إذناً لإعادة اختبار: \"{$exam->title}\".",
                    'exam',
                    route('admin.submissions.index')
                );
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'title' => 'تم إرسال طلب إعادة الاختبار لأستاذ المادة بنجاح! سيصلك تنبيه فور اعتماده.'
        ]);
    }

    /**
     * موافقة المعلم أو الإدارة على إعادة الاختبار للطالب
     */
    public function allowRetake($id)
    {
        $user = auth()->user();
        $submission = ExamSubmission::with(['exam.subject', 'student'])->findOrFail($id);

        if (!$this->isUserAuthorizedForExamSubmission($user, $submission)) {
            return response()->json(['success' => false, 'error' => 'غير مصرح لك بمنح صلاحية الإعادة لهذا الاختبار'], 403);
        }

        $submission->update([
            'allow_retake' => true,
            'retake_requested' => false,
            'retake_granted_by' => $user->id,
        ]);

        try {
            \App\Services\NotificationService::notifyStudent(
                $submission->student_id,
                'تمت الموافقة على إعادة الاختبار! 🚀',
                "وافق أستاذ المادة على إعادة اختبار: \"{$submission->exam->title}\". يمكنك الدخول للاختبار الآن وتقديم محاولة جديدة.",
                'exam',
                route('student.exams.take', $submission->exam_id),
                'fa-unlock'
            );
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'title' => 'تمت الموافقة على إعادة الاختبار للطالب بنجاح 🎉'
        ]);
    }

    /**
     * رفض طلب إعادة الاختبار
     */
    public function denyRetake($id)
    {
        $user = auth()->user();
        $submission = ExamSubmission::with(['exam.subject', 'student'])->findOrFail($id);

        if (!$this->isUserAuthorizedForExamSubmission($user, $submission)) {
            return response()->json(['success' => false, 'error' => 'غير مصرح'], 403);
        }

        $submission->update([
            'retake_requested' => false,
            'allow_retake' => false,
        ]);

        try {
            \App\Services\NotificationService::notifyStudent(
                $submission->student_id,
                'بخصوص طلب إعادة الاختبار ⚠️',
                "تم رفض طلب إعادة اختبار \"{$submission->exam->title}\" من قِبل معلّم المادة.",
                'exam',
                route('student.exams.results', $submission->id),
                'fa-circle-xmark'
            );
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'title' => 'تم رفض طلب الإعادة'
        ]);
    }

    /**
     * خدمة وعرض صورة السؤال بشكل مباشر ومضمون
     */
    public function questionImage($id)
    {
        $question = Question::findOrFail($id);

        if (empty($question->image)) {
            abort(404, 'لا توجد صورة لهذا السؤال');
        }

        // إذا كان الرابط خارجياً ومباشراً (Supabase / S3 / External)
        if (str_starts_with($question->image, 'http://') || str_starts_with($question->image, 'https://')) {
            return redirect()->away($question->image);
        }

        $cleanPath = ltrim(str_replace(['storage/', 'public/'], '', $question->image), '/');

        // البحث في storage/app/public
        $fullPath = storage_path('app/public/' . $cleanPath);
        if (!file_exists($fullPath)) {
            // البحث في public_path مباشرة
            $fullPath = public_path('storage/' . $cleanPath);
        }
        if (!file_exists($fullPath)) {
            $fullPath = public_path($cleanPath);
        }

        if (file_exists($fullPath) && is_file($fullPath)) {
            $mime = mime_content_type($fullPath) ?: 'image/jpeg';
            return response()->file($fullPath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // إذا كان مخزناً في قرص Supabase
        if (!empty(config('filesystems.disks.supabase.key'))) {
            try {
                if (Storage::disk('supabase')->exists($question->image)) {
                    return redirect()->away(Storage::disk('supabase')->url($question->image));
                }
            } catch (\Throwable $e) {}
        }

        abort(404, 'ملف الصورة غير موجود');
    }

    /**
     * خدمة وعرض صورة خيار الاختيار من متعدد (A, B, C, D)
     */
    public function questionOptionImage($id, $option)
    {
        $question = Question::findOrFail($id);
        $field = strtolower($option) . '_image';
        $imgPath = $question->$field ?? null;

        if (empty($imgPath)) {
            abort(404, 'لا توجد صورة لهذا الخيار');
        }

        if (str_starts_with($imgPath, 'http://') || str_starts_with($imgPath, 'https://')) {
            return redirect()->away($imgPath);
        }

        $cleanPath = ltrim(str_replace(['storage/', 'public/'], '', $imgPath), '/');

        $fullPath = storage_path('app/public/' . $cleanPath);
        if (!file_exists($fullPath)) {
            $fullPath = public_path('storage/' . $cleanPath);
        }
        if (!file_exists($fullPath)) {
            $fullPath = public_path($cleanPath);
        }

        if (file_exists($fullPath) && is_file($fullPath)) {
            $mime = mime_content_type($fullPath) ?: 'image/jpeg';
            return response()->file($fullPath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        if (!empty(config('filesystems.disks.supabase.key'))) {
            try {
                if (Storage::disk('supabase')->exists($imgPath)) {
                    return redirect()->away(Storage::disk('supabase')->url($imgPath));
                }
            } catch (\Throwable $e) {}
        }

        abort(404, 'ملف صورة الخيار غير موجود');
    }
}