<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCertificateController extends Controller
{
    /**
     * لوحة التحكم في نتائج وشهادات نهاية العام الدراسي
     */
    public function index()
    {
        $yearEndPublished = (bool) Setting::get('year_end_certificates_published', 0);
        $allowStudentGpa  = (bool) Setting::get('allow_student_calculate_gpa', 0);

        // جلب جميع الطلاب مع موادهم وشهاداتهم ومراحلهم
        $students = Student::with(['stage', 'enrolledSubjects', 'certificates.subject'])
            ->orderBy('id', 'desc')
            ->paginate(25);

        $subjects = Subject::orderBy('name_ar')->get();

        $stats = [
            'total_students'      => Student::count(),
            'total_certificates'  => Certificate::count(),
            'year_end_published'  => $yearEndPublished,
            'allow_student_gpa'   => $allowStudentGpa,
        ];

        return view('admin.certificates.index', compact('students', 'subjects', 'yearEndPublished', 'allowStudentGpa', 'stats'));
    }

    /**
     * تبديل إعلان أو حجب شهادات ونتائج نهاية العام للطلاب
     */
    public function togglePublish(Request $request)
    {
        $current = (bool) Setting::get('year_end_certificates_published', 0);
        $newVal = $current ? 0 : 1;

        Setting::set('year_end_certificates_published', $newVal);

        $title = $newVal 
            ? 'تم إعلان ونشر شهادات ونتائج نهاية العام للطلاب رسميّاً! 🎓' 
            : 'تم حجب الشهادات، ولن تظهر لأي طالب حتى تعتمدها بنهاية العام 🔒';

        return response()->json([
            'success'   => true,
            'published' => (bool)$newVal,
            'icon'      => $newVal ? 'success' : 'info',
            'title'     => $title,
            'message'   => $newVal 
                ? 'أصبح بإمكان الطلاب الآن استعراض وطباعة شهاداتهم المعتمدة.' 
                : 'الشهادات محجوبة عن الطلاب حالياً وتظهر لهم رسالة الانتظار والاعتماد الأكاديمي.'
        ]);
    }

    /**
     * تبديل السماح للطلاب بحساب واستخراج المعدل
     */
    public function toggleGpa(Request $request)
    {
        $current = (bool) Setting::get('allow_student_calculate_gpa', 0);
        $newVal = $current ? 0 : 1;

        Setting::set('allow_student_calculate_gpa', $newVal);

        return response()->json([
            'success'   => true,
            'allowed'   => (bool)$newVal,
            'icon'      => $newVal ? 'success' : 'info',
            'title'     => $newVal ? 'تم تفعيل حاسبة المعدل للطلاب ✅' : 'تم قفل حاسبة المعدل بانتظار إعلان الإدارة 🔒',
            'message'   => $newVal 
                ? 'يمكن للطلاب الآن استخدام حاسبة المعدل واستخراج درجاتهم.' 
                : 'تم تقييد حساب المعدل للطلاب حتى نهاية العام.'
        ]);
    }

    /**
     * اعتماد وإصدار شهادة رسمية لطالب بمعدل محدد من قبل المدير
     */
    public function issue(Request $request)
    {
        $request->validate([
            'student_id'  => 'required|exists:students,id',
            'final_grade' => 'required|numeric|min:50|max:100',
            'subject_id'  => 'nullable|exists:subjects,id',
        ], [
            'student_id.required'  => 'يرجى اختيار الطالب.',
            'final_grade.required' => 'يرجى إدخال المعدل أو النسبة المعتمدة.',
            'final_grade.min'      => 'المعدل يجب أن لا يقل عن 50%.',
            'final_grade.max'      => 'المعدل لا يتجاوز 100%.',
        ]);

        $student = Student::findOrFail($request->student_id);

        // إذا لم يتم تحديد مادة، نأخذ المادة الأولى المقيد بها أو مادة الفرع
        $subjectId = $request->subject_id;
        if (!$subjectId) {
            $firstSub = $student->enrolledSubjects()->first();
            $subjectId = $firstSub ? $firstSub->id : Subject::where('stage_id', $student->stage_id)->value('id') ?? Subject::first()->id;
        }

        $code = 'TAWJIHI-' . date('Y') . '-' . strtoupper(Str::random(6));

        // إنشاء أو تحديث الشهادة
        $certificate = Certificate::updateOrCreate(
            [
                'student_id' => $student->id,
                'subject_id' => $subjectId,
            ],
            [
                'certificate_code' => $code,
                'final_grade'      => (int) round($request->final_grade),
                'updated_at'       => now(),
            ]
        );

        return response()->json([
            'success'          => true,
            'icon'             => 'success',
            'title'            => 'تم اعتماد وإصدار الشهادة الأكاديمية بنجاح! 🏆',
            'message'          => "تم تسجيل معدل ({$certificate->final_grade}%) وإصدار الشهادة الرسمية للطالب ({$student->name_ar}).",
            'certificate_code' => $certificate->certificate_code,
            'view_url'         => route('certificates.show', $certificate->id)
        ]);
    }

    /**
     * حذف أو سحب شهادة صادرة
     */
    public function destroy($id)
    {
        $cert = Certificate::findOrFail($id);
        $studentName = $cert->student->name_ar ?? 'الطالب';
        $cert->delete();

        return response()->json([
            'success' => true,
            'icon'    => 'success',
            'title'   => 'تم إلغاء وسحب الشهادة بنجاح 🗑️',
            'message' => "تم حذف الشهادة الأكاديمية للطالب ({$studentName})."
        ]);
    }
}
