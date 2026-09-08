<?php

namespace App\Http\Controllers;

use App\Models\{Student, Subject, EducationalContent, Certificate, Recommendation, ExamSubmission, Setting};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SmartLearningController extends Controller
{
    /**
     * لوحة إنجازات الطالب، الشهادات الأكاديمية، والأوسمة
     */
    public function myAchievements()
    {
        $student = Auth::guard('student')->user() ?? Auth::user();
        if (!$student) {
            return redirect()->route('login');
        }

        $studentId = $student->id;

        // إذا لم يكن لدى الطالب شهادة، نولد له شهادة فخرية لمادته الأولى تشجيعاً له
        $firstSubject = $student->enrolledSubjects()->first() ?? Subject::first();
        if ($firstSubject && Certificate::where('student_id', $studentId)->count() === 0) {
            Certificate::create([
                'student_id' => $studentId,
                'subject_id' => $firstSubject->id,
                'certificate_code' => 'TAWJIHI-' . date('Y') . '-' . strtoupper(Str::random(6)),
                'final_grade' => 96
            ]);
        }

        $certificates = Certificate::with(['subject', 'student'])->where('student_id', $studentId)->latest()->get();
        $recommendations = Recommendation::with('content.subject')->where('student_id', $studentId)->get();
        $completedExamsCount = ExamSubmission::where('student_id', $studentId)->count();

        return view('student.achievements.index', compact('certificates', 'recommendations', 'student', 'completedExamsCount'));
    }

    /**
     * إصدار شهادة تفوق تلقائية لمساق
     */
    public function generateCertificate($student_id, $subject_id, $grade = 95)
    {
        $exists = Certificate::where('student_id', $student_id)->where('subject_id', $subject_id)->first();
        if (!$exists) {
            Certificate::create([
                'student_id' => $student_id,
                'subject_id' => $subject_id,
                'certificate_code' => 'TAWJIHI-' . date('Y') . '-' . strtoupper(Str::random(6)),
                'final_grade' => $grade
            ]);
        }
        return back()->with('success', 'مبروك! تم إصدار شهادة التميز المعتمدة لك 🏆');
    }

    /**
     * عرض الشهادة الملكية الفاخرة للطباعة والتحميل
     */
    public function showCertificate($id)
    {
        $certificate = Certificate::with(['student', 'subject'])->where('id', $id)
            ->orWhere('certificate_code', $id)
            ->firstOrFail();

        $siteName = Setting::get('site_name', 'منارة التوجيهي');
        $siteSlogan = Setting::get('site_slogan', 'المنصة التعليمية الأولى لطلبة الثانوية العامة في فلسطين');
        $verificationUrl = route('certificates.verify', $certificate->certificate_code);

        return view('student.achievements.certificate_royal', compact('certificate', 'siteName', 'siteSlogan', 'verificationUrl'));
    }

    /**
     * صفحة التحقق العامة من صحة الشهادة عند مسح رمز QR
     */
    public function verifyCertificate($code)
    {
        $certificate = Certificate::with(['student', 'subject'])->where('certificate_code', $code)->first();
        $siteName = Setting::get('site_name', 'منارة التوجيهي');
        $siteSlogan = Setting::get('site_slogan', 'المنصة التعليمية الأولى لطلبة الثانوية العامة في فلسطين');

        return view('public.certificate_verify', compact('certificate', 'code', 'siteName', 'siteSlogan'));
    }

    /**
     * حفظ جلسة تركيز بومودورو للثانوية العامة وزيادة رصيد الالتزام
     */
    public function savePomodoroSession(Request $request)
    {
        $minutes = (int) $request->input('minutes', 25);
        $student = Auth::guard('student')->user();

        if ($student) {
            if (isset($student->streak_count)) {
                $student->streak_count = max(1, $student->streak_count + 1);
                $student->last_active_at = now();
                $student->save();
            }

            return response()->json([
                'success' => true,
                'message' => "أحسنت يا بطل! تم تسجيل جلسة تركيز مدتها {$minutes} دقيقة بنجاح 🎯",
                'streak' => $student->streak_count ?? 1
            ]);
        }

        return response()->json(['success' => true, 'message' => 'جلسة تركيز ممتازة!']);
    }

    /**
     * إحصائيات المحتوى للمدير
     */
    public function adminInsights()
    {
        $top_contents = EducationalContent::with('subject')->orderBy('views_count', 'desc')->take(10)->get();
        return view('admin.management.insights', compact('top_contents'));
    }
}
