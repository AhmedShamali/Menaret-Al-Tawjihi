<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlacementResult;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;

class PlacementController extends Controller
{
    /**
     * عرض واجهة الاختبار التشخيصي (الفاقد التعليمي)
     */
    public function index()
    {
        // عرض الصفحة التي تحتوي على الـ 250 سؤالاً مدمجة بالجافاسكربت
        return view('student.exams.placement');
    }

    /**
     * حفظ نتيجة الاختبار التشخيصي وإعطاء توصية أكاديمية
     */
    public function store(Request $request)
    {
        try {
            $student = \App\Models\Student::first(); // تجريبي

            \App\Models\PlacementResult::create([
                'student_id'   => $student->id,
                'subject_name' => $request->subject,
                'score'        => $request->score,
                'percentage'   => $request->percentage, // الآن سيتم الحفظ بنجاح
                'level'        => $request->level ?? 'مكتمل'
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }
}
