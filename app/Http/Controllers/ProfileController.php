<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        // جلب بيانات الطالب (تجريبي ID=1 لعدم وجود Auth حالياً)
        $student = Student::with('stage')->findOrFail(1);

        // جلب آخر النشاطات باستخدام المودل الجديد Activity
        $activities = Activity::where('student_id', $student->id)
                                ->latest()
                                ->take(6)
                                ->get();

        return view('student.profile', compact('student', 'activities'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);

        $student = Student::findOrFail(1);

        if (!Hash::check($request->old_password, $student->password)) {
            return response()->json(['icon' => 'error', 'title' => 'كلمة المرور القديمة غير صحيحة']);
        }

        $student->update(['password' => Hash::make($request->new_password)]);

        // تسجيل نشاط "تغيير كلمة المرور"
        Activity::create([
            'student_id' => $student->id,
            'type' => 'security',
            'description' => 'قام بتحديث كلمة مرور الحساب'
        ]);

        return response()->json(['icon' => 'success', 'title' => 'تم تحديث كلمة المرور بنجاح ✅']);
    }
}
