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

    /**
     * عرض تفاصيل المادة الدراسية الموحدة (للفيديوهات، الملفات، والاختبارات)
     */
    public function showSubject($id)
    {
        $student_id = auth()->id() ?? 1;

        // جلب المادة مع كامل علاقاتها (المرحلة، المعلم، المحتوى، والاختبارات)
        $subject = Subject::with(['stage', 'teacher', 'contents', 'educationalContents', 'exams'])->findOrFail($id);

        // الاعتماد على المحتوى المتاح سواء كان عبر contents أو educationalContents
        $contents = $subject->contents->isNotEmpty() ? $subject->contents : $subject->educationalContents;

        // 1. جلب الفيديوهات
        $videos = $contents->filter(function ($item) {
            return !empty($item->url_path);
        })->sortBy('order');

        // 2. جلب الملفات والكتب
        $files = $contents->filter(function ($item) {
            return !empty($item->pdf_path);
        })->sortBy('order');

        // 3. جلب الاختبارات التي حلها الطالب مسبقاً
        $solvedExamIds = ExamSubmission::where('student_id', $student_id)->pluck('exam_id');

        return view('student.subjects.show', compact('subject', 'videos', 'files', 'solvedExamIds'));
    }

    // جعل الدالة البديلة تحول مباشرة للدالة الموحدة لضمان عدم حدوث تضارب
    public function studentSubjectShow($id)
    {
        return $this->showSubject($id);
    }

    public function studentSubjectsIndex()
    {
        $user = auth()->user();

        // جلب معرف المرحلة الخاص بالطالب
        $stageId = $user->stage_id ?? optional($user->student)->stage_id;

        // جلب المواد التي تنتمي لهذه المرحلة فقط مع عدادات المحتوى والاختبارات
        $subjects = \App\Models\Subject::where('stage_id', $stageId)
                        ->withCount(['educationalContents', 'contents', 'exams'])
                        ->get();

        return view('student.subjects.index', compact('subjects'));
    }

    public function studentIndex() {
        $student_id = auth()->id() ?? 1;

        $my_stats = [
            'completed_exams' => ExamSubmission::where('student_id', $student_id)->count(),
            'avg_grade'       => ExamSubmission::where('student_id', $student_id)->avg('total_earned_grade'),
        ];

        // 1. جلب آي دي الاختبارات التي حلها الطالب مسبقاً
        $solvedExamIds = ExamSubmission::where('student_id', $student_id)->pluck('exam_id');

        // 2. جلب الاختبارات المتاحة مع استثناء التي تم حلها مسبقاً
        $available_exams = Exam::latest()
                            ->whereNotIn('id', $solvedExamIds)
                            ->take(3)
                            ->get();

        // 3. الاختبارات المكتملة
        $completed_exams = ExamSubmission::with('exam')
                            ->where('student_id', $student_id)
                            ->latest()
                            ->get();

        return view('student.dashboard', compact('my_stats', 'available_exams', 'completed_exams'));
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
            'site_name' => 'منصة جسر الرقمية',
            'site_symbol' => 'ج',
            'site_description' => 'بوابتك المتكاملة لمتابعة المسارات التعليمية والاختبارات.'
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