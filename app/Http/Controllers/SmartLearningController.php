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

        // التحقق من قرار الإدارة بإعلان ونشر شهادات ونتائج نهاية العام
        $isYearEndPublished = (bool) Setting::get('year_end_certificates_published', 0);
        $allowGpaCalculation = (bool) Setting::get('allow_student_calculate_gpa', 0);

        // حجب الشهادات تماماً عن الطالب حتى نهاية العام وعندما يأذن المدير
        if ($isYearEndPublished) {
            $certificates = Certificate::with(['subject', 'student'])->where('student_id', $studentId)->latest()->get();
        } else {
            $certificates = collect();
        }

        $recommendations = Recommendation::with('content.subject')->where('student_id', $studentId)->get();
        $completedExamsCount = ExamSubmission::where('student_id', $studentId)->count();

        // جلب المواد الخاصة بالمرحلة الدراسية للطالب لخيارات تنظيم الدراسة
        $subjects = collect();
        if (!empty($student->stage_id)) {
            $subjects = Subject::where('stage_id', $student->stage_id)->orderBy('name_ar')->get();
        }
        if ($subjects->isEmpty()) {
            $subjects = Subject::orderBy('name_ar')->get();
        }

        return view('student.achievements.index', compact(
            'certificates',
            'recommendations',
            'student',
            'completedExamsCount',
            'subjects',
            'isYearEndPublished',
            'allowGpaCalculation'
        ));
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
     * عرض شهادة التفوق الملكية المعتمدة بتصميم رسمي فاخر
     */
    public function showCertificate($id)
    {
        $certificate = Certificate::with(['student.stage', 'subject.stage'])
            ->when(is_numeric($id), function ($q) use ($id) {
                $q->where('id', $id)->orWhere('certificate_code', $id);
            }, function ($q) use ($id) {
                $q->where('certificate_code', $id);
            })
            ->firstOrFail();

        $isAdmin = (Auth::guard('web')->check() && Auth::user() && Auth::user()->role === 'admin');
        $isYearEndPublished = (bool) Setting::get('year_end_certificates_published', 0);

        if (!$isAdmin && !$isYearEndPublished) {
            return redirect()->route('student.achievements')
                ->with('warning', 'عذراً، الشهادات الأكاديمية محجوبة وتُعلن رسمياً في نهاية العام الدراسي بقرار الإدارة 🔒');
        }

        $student = $certificate->student ?? Auth::guard('student')->user() ?? Auth::user();
        $siteName = Setting::get('site_name', 'منارة التوجيهي');
        $siteSlogan = Setting::get('site_slogan', 'المنصة التعليمية الأولى لطلبة الثانوية العامة في فلسطين');
        $verificationUrl = route('certificates.verify', $certificate->certificate_code);

        return view('student.achievements.certificate_royal', compact('certificate', 'student', 'siteName', 'siteSlogan', 'verificationUrl'));
    }

    /**
     * التحقق العام من صحة الشهادة عبر الـ QR Code
     */
    public function verifyCertificate($code)
    {
        $certificate = Certificate::with(['student', 'subject'])->where('certificate_code', $code)->first();
        $siteName = Setting::get('site_name', 'منارة التوجيهي');
        $siteSlogan = Setting::get('site_slogan', 'المنصة التعليمية الأولى لطلبة الثانوية العامة في فلسطين');

        return view('public.certificate_verify', compact('certificate', 'code', 'siteName', 'siteSlogan'));
    }

    /**
     * حفظ جلسة تركيز بومودورو للثانوية العامة وزيادة رصيد الالتزام وتنظيم الدراسة
     */
    public function savePomodoroSession(Request $request)
    {
        $minutes = (int) $request->input('minutes', 25);
        $subjectName = trim($request->input('subject_name', ''));
        $taskGoal = trim($request->input('task_goal', ''));
        $tasksCompleted = (int) $request->input('tasks_completed', 0);
        $student = Auth::guard('student')->user();

        if ($student) {
            if (isset($student->streak_count)) {
                $student->streak_count = max(1, $student->streak_count + 1);
                $student->last_active_at = now();
                $student->save();
            }

            $extraText = '';
            if ($subjectName) {
                $extraText .= " في مادة [{$subjectName}]";
            }
            if ($taskGoal) {
                $extraText .= " | الهدف: {$taskGoal}";
            }
            if ($tasksCompleted > 0) {
                $extraText .= " (تم إنجاز {$tasksCompleted} مهام)";
            }

            return response()->json([
                'success' => true,
                'message' => "أحسنت يا بطل التوجيهي! تم تسجيل جلسة دراسة مدتها {$minutes} دقيقة{$extraText} بنجاح 🎯",
                'streak' => $student->streak_count ?? 1
            ]);
        }

        return response()->json(['success' => true, 'message' => 'جلسة تركيز ودراسة ممتازة!']);
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
