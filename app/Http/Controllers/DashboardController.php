<?php
namespace App\Http\Controllers;

use App\Models\{Student, Subject, Exam, ExamSubmission, EducationalContent};
use Illuminate\Http\Request;

class DashboardController extends Controller {

    public function adminIndex() {
        $stats = [
            'students_count' => Student::count(),
            'subjects_count' => Subject::count(),
            'exams_count'    => Exam::count(),
            'pending_grades' => ExamSubmission::where('status', 'pending')->count(),
        ];
        $recent_students = Student::latest()->take(5)->get();
        $recent_submissions = ExamSubmission::with(['student', 'exam'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_students', 'recent_submissions'));
    }

    public function studentIndex() {
        $student_id = 1; // سيستبدل بـ Auth::id()
        $my_stats = [
            'completed_exams' => ExamSubmission::where('student_id', $student_id)->count(),
            'avg_grade'       => ExamSubmission::where('student_id', $student_id)->avg('total_earned_grade'),
        ];
        $available_exams = Exam::where('status', 'published')->latest()->take(3)->get();

        return view('student.dashboard', compact('my_stats', 'available_exams'));
    }
}
