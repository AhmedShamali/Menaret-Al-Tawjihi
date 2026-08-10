<?php
namespace App\Http\Controllers;

use App\Models\{Student, Subject, EducationalContent, Certificate, Recommendation, ExamSubmission};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SmartLearningController extends Controller {

    // 1. توليد شهادة إنجاز (تستدعى عند الحصول على درجة > 90%)
    public function generateCertificate($student_id, $subject_id) {
        $exists = Certificate::where('student_id', $student_id)->where('subject_id', $subject_id)->first();
        if (!$exists) {
            Certificate::create([
                'student_id' => $student_id,
                'subject_id' => $subject_id,
                'certificate_code' => 'JESR-' . strtoupper(Str::random(8)),
                'final_grade' => 95 // مثال
            ]);
        }
        return back()->with('success', 'مبروك! تم إصدار شهادة التميز لك 🏆');
    }

    // 2. لوحة إنجازات الطالب (الشهادات والتوصيات)
    public function myAchievements() {
        $student_id = 1; // Auth::id()
        $certificates = Certificate::with('subject')->where('student_id', $student_id)->get();
        $recommendations = Recommendation::with('content.subject')->where('student_id', $student_id)->get();

        return view('student.achievements.index', compact('certificates', 'recommendations'));
    }

    // 3. إحصائيات المحتوى للمدير
    public function adminInsights() {
        $top_contents = EducationalContent::with('subject')->orderBy('views_count', 'desc')->take(10)->get();
        return view('admin.management.insights', compact('top_contents'));
    }
}
