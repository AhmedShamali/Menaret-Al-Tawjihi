<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\{Student, Subject, Exam, ExamSubmission, EducationalContent};
use Illuminate\Http\Request;

class DashboardController extends Controller {

    public function index()
    {
        return $this->adminIndex();
    }

    public function adminIndex()
    {
        $data = [
            'total_teachers'     => \App\Models\User::where('role', 'teacher')->count(),
            'total_students'     => \App\Models\Student::count(),
            'active_exams'       => \App\Models\Exam::count(),
            'total_files'        => \App\Models\EducationalContent::count(),
            'avg_success'        => 85,
            'recent_submissions' => \App\Models\ExamSubmission::with(['student', 'exam'])->latest()->take(5)->get(),
            'top_subjects'       => \App\Models\Subject::withCount('contents')->get(),
        ];

        return view('admin.dashboard', compact('data'));
    }

    public function showSubject($id)
    {
        $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        if ($student instanceof \App\Models\User && $student->role === 'student') {
            $student = $student->student ?? \App\Models\Student::where('id', $student->id)->orWhere('email', $student->email)->first();
        }
        $student_id = $student?->id ?? auth()->id();

        // جلب المادة مع كامل علاقاتها
        $subject = Subject::with(['stage', 'teacher', 'contents', 'educationalContents', 'exams'])->findOrFail($id);

        $isAdminOrTeacher = auth()->check() && in_array(auth()->user()->role, ['admin', 'teacher']);

        // التحقق الصارم للطالب: حظر الوصول تماماً إن لم تكن المادة تابعة لفرعه الأصلي أو لم يكن مسجلاً بها باشتراك نشط
        $enrollment = null;
        if (!$isAdminOrTeacher) {
            if (!$student) {
                return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً للوصول إلى هذا المقرر.');
            }

            // 1. التحقق الصارم من الفرع الأكاديمي الأصلي للطالب
            if ($student->stage_id && $subject->stage_id && (int)$subject->stage_id !== (int)$student->stage_id) {
                return redirect()->route('student.subjects.index')->with('error', 'عذراً، هذه المادة لا تنتمي إلى فرعك الدراسي الأصلي ولا يمكن الوصول إليها.');
            }

            // 2. التحقق الصارم من وجود تسجيل واشتراك نشط معتمد
            $enrollment = \App\Models\Enrollment::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->where('status', 'active')
                ->first();

            if (!$enrollment) {
                return redirect()->route('student.subjects.index')->with('error', 'عذراً، لا يمكنك عرض محتوى هذه المادة لأنك غير مسجل بها باشتراك نشط معتمد.');
            }
        }

        // الاعتماد على المحتوى المتاح في المادة
        $allContents = $subject->contents->isNotEmpty() ? $subject->contents : $subject->educationalContents;

        // للطلاب: إظهار المحتوى المعتمد والمرئي فقط (حيث is_visible != 0)
        // أما المعلم أو المدير فيمكنهما رؤية كافة المحتويات عند المعاينة
        $contents = $isAdminOrTeacher 
            ? $allContents 
            : $allContents->filter(fn($item) => $item->is_visible !== false && $item->is_visible !== 0 && $item->is_visible !== '0');

        // فحص صلاحيات الوصول لدروس المادة
        $isFullAccess = $isAdminOrTeacher || (bool) $subject->is_free || ($subject->effective_price <= 0);
        $allowedIds = [];

        if (!$isFullAccess && $student && $enrollment) {
            $isFullAccess = ($enrollment->access_mode === 'all');
            if (!$isFullAccess) {
                $allowedIds = \App\Models\ContentAssignment::where('enrollment_id', $enrollment->id)
                    ->where('is_visible', true)
                    ->pluck('educational_content_id')
                    ->toArray();
            }
        }

        // تحديد حالة القفل لكل درس
        $contents->each(function ($item) use ($isFullAccess, $allowedIds) {
            $item->is_unlocked = $isFullAccess || in_array($item->id, $allowedIds);
        });

        // 1. جلب الفيديوهات والشروحات المرئية الحقيقية فقط التي رفعها المعلم
        $videos = $contents->filter(function ($item) {
            if ($item->type === 'file') {
                return false;
            }
            $url = trim($item->url_path ?? '');
            if (empty($url)) {
                return false;
            }
            $isYt = !empty($item->youtube_id) || str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be');
            $isDirectVideo = (bool) preg_match('/\.(mp4|webm|ogg|mov|m4v)($|\?)/i', $url);
            return $isYt || $isDirectVideo || $item->type === 'video' || $item->type === 'both';
        })->sortBy('order');

        // 2. جلب الملفات والملازم والدوسيات الحقيقية فقط
        $files = $contents->filter(function ($item) {
            $pdf = trim($item->pdf_path ?? '');
            return !empty($pdf);
        })->sortBy('order');

        // 3. جلب بنك الاختبارات المعتمدة للمادة التابعة لفرع الطالب الأصلي
        $examsQuery = Exam::where('subject_id', $subject->id)
            ->withCount('questions')
            ->latest();

        if (!$isAdminOrTeacher && $student && $student->stage_id) {
            $examsQuery->where(function ($q) use ($student) {
                $q->where('stage_id', $student->stage_id)
                  ->orWhereNull('stage_id');
            });
        }

        $exams = $examsQuery->get();

        // حصر الاختبارات حصرياً بالطلبة المسجلين والمشتركين في المادة
        if (!$isAdminOrTeacher) {
            if (!$enrollment || $enrollment->status !== 'active') {
                $exams = collect();
            } elseif ($enrollment->access_mode !== 'all') {
                $allowedExamIds = \App\Models\ExamAssignment::where('enrollment_id', $enrollment->id)
                    ->where('is_visible', true)
                    ->pluck('exam_id')
                    ->toArray();
                $exams = $exams->whereIn('id', $allowedExamIds)->values();
            }
        }

        // 4. جلب الاختبارات التي حلها الطالب مسبقاً
        $submissions = $student_id 
            ? ExamSubmission::where('student_id', $student_id)->whereIn('exam_id', $exams->pluck('id'))->get()->keyBy('exam_id') 
            : collect();
        $solvedExamIds = $submissions->keys();

        return view('student.subjects.show', compact('subject', 'videos', 'files', 'exams', 'submissions', 'solvedExamIds', 'enrollment', 'isFullAccess'));
    }

    // جعل الدالة البديلة تحول مباشرة للدالة الأساسية لضمان عدم حدوث تضارب
    public function studentSubjectShow($id)
    {
        return $this->showSubject($id);
    }

    public function studentSubjectsIndex()
    {
        $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        if ($student instanceof \App\Models\User && $student->role === 'student') {
            $student = $student->student ?? \App\Models\Student::where('id', $student->id)->orWhere('email', $student->email)->first();
        }

        if (!$student) {
            return redirect()->route('login');
        }

        // جلب معرف المرحلة الخاص بالطالب
        $stageId = $student->stage_id ?? optional($student->student)->stage_id;

        // حصر العرض قطيعاً بالمواد التي سجّل بها الطالب واعتمدتها الإدارة (الاشتراكات النشطة)
        $enrolledSubjectIds = \App\Models\Enrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->pluck('subject_id')
            ->toArray();

        if (empty($enrolledSubjectIds)) {
            $subjects = collect();
            return view('student.subjects.index', compact('subjects'));
        }

        // جلب المواد مع عدادات المحتوى والاختبارات التابعة لفرعه الأصلي فقط
        $query = \App\Models\Subject::with(['stage', 'teacher'])->withCount(['educationalContents', 'contents', 'exams'])
            ->whereIn('id', $enrolledSubjectIds);

        if ($stageId) {
            $query->where('stage_id', $stageId);
        }

        $subjects = $query->get();

        return view('student.subjects.index', compact('subjects'));
    }

    public function studentIndex() {
        $student = \App\Support\CurrentActor::student() ?? \Illuminate\Support\Facades\Auth::guard('student')->user() ?? auth()->user();
        if ($student instanceof \App\Models\User) {
            $student = $student->student ?? \App\Models\Student::where('id', $student->id)->orWhere('email', $student->email)->first();
        }
        $student_id = $student?->id ?? 1;

        // 1. جلب آي دي الاختبارات التي حلها الطالب مسبقاً
        $solvedExamIds = ExamSubmission::where('student_id', $student_id)->pluck('exam_id');

        // 2. حصر الاختبارات قطيعاً في المواد المسجل بها الطالب ومشترك فيها باشتراك نشط حصراً ولفرعه الأصلي
        $available_exams = collect();
        $availableExamsCount = 0;

        if ($student) {
            $enrollments = \App\Models\Enrollment::where('student_id', $student->id)
                ->where('status', 'active')
                ->get()
                ->keyBy('subject_id');

            $enrolledSubjectIds = $enrollments->keys()->toArray();

            if (!empty($enrolledSubjectIds)) {
                // جلب الاختبارات التابعة للمواد المسجل بها فقط ولفرع الطالب الأصلي
                $candidateExamsQuery = Exam::whereIn('subject_id', $enrolledSubjectIds)
                    ->whereNotIn('id', $solvedExamIds)
                    ->with(['subject', 'stage'])
                    ->withCount('questions')
                    ->latest();

                if ($student->stage_id) {
                    $candidateExamsQuery->where(function ($q) use ($student) {
                        $q->where('stage_id', $student->stage_id)
                          ->orWhere(function ($subQ) use ($student) {
                              $subQ->whereNull('stage_id')
                                   ->whereHas('subject', function ($sQ) use ($student) {
                                       $sQ->where('stage_id', $student->stage_id);
                                   });
                          });
                    });
                }

                $candidateExams = $candidateExamsQuery->get();

                // تصفية صلاحيات الوصول وخطة المادة المعتمدة
                $filteredExams = $candidateExams->filter(function ($exam) use ($enrollments) {
                    $enr = $enrollments->get($exam->subject_id);
                    if (!$enr) {
                        return false;
                    }

                    // إن كان اشتراكاً كاملاً في المادة
                    if ($enr->access_mode === 'all') {
                        return true;
                    }

                    // إن كان اشتراكاً مخصصاً، يجب أن يكون المعلم قد حدد هذا الاختبار للطالب
                    return \App\Models\ExamAssignment::where('enrollment_id', $enr->id)
                        ->where('exam_id', $exam->id)
                        ->where('is_visible', true)
                        ->exists();
                });

                $availableExamsCount = $filteredExams->count();
                $available_exams = $filteredExams->take(6)->values();
            }
        }

        $my_stats = [
            'completed_exams'       => ExamSubmission::where('student_id', $student_id)->count(),
            'available_exams_count' => $availableExamsCount,
            'avg_grade'             => ExamSubmission::where('student_id', $student_id)->avg('total_earned_grade'),
        ];

        // 3. الاختبارات المكتملة
        $completed_exams = ExamSubmission::with('exam')
                            ->where('student_id', $student_id)
                            ->latest()
                            ->get();

        // 4. العمليات المالية قيد المراجعة والتدقيق
        $pendingPayments = \App\Models\Payment::where('student_id', $student_id)
                            ->where('status', 'pending')
                            ->latest()
                            ->get();

        return view('student.dashboard', compact('my_stats', 'available_exams', 'completed_exams', 'student', 'pendingPayments'));
    }

    public function teachersIndex()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.teachers.index', compact('teachers'));
    }

    public function showTeacher($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        return view('admin.teachers.show', compact('teacher'));
    }

    public function studentsIndex()
    {
        $students = \App\Models\Student::all();
        return view('admin.students.index', compact('students'));
    }

    public function showStudent($id)
    {
        $student = \App\Models\Student::findOrFail($id);
        return view('admin.students.show', compact('student'));
    }

    public function profileStudent($id)
    {
        $student = \App\Models\Student::findOrFail($id);
        return view('admin.students.profile', compact('student'));
    }

    public function visitorIndex()
    {
        $settings = (object) [
            'site_name' => \App\Models\Setting::get('site_name', 'منارة التوجيهي'),
            'site_symbol' => 'م',
            'site_description' => \App\Models\Setting::get('site_description', 'المنصة التعليمية المتكاملة لطلبة الثانوية العامة في فلسطين (التوجيهي).')
        ];

        return view('visitor', compact('settings'));
    }

    public function toggleStatus($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $teacher->status = ($teacher->status == 'active') ? 'inactive' : 'active';
        $teacher->save();

        return redirect()->back()->with('success', 'تم تغيير حالة المعلم بنجاح.');
    }

    public function destroyTeacher($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $teacher->delete();

        return redirect()->back()->with('success', 'تم حذف المعلم بنجاح.');
    }
}