<?php

namespace App\Http\Controllers;

use App\Models\ChannelRequest;
use App\Models\Student;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    /**
     * عرض صفحة القنوات للطالب
     */
    public function showChannelPage()
    {
        // تجريبي: نفترض الطالب رقم 1، لاحقاً نستخدم auth()->id()
        $student = Student::with('stage')->find(1);

        // فحص هل أرسل طلب سابقاً
        $request_exists = ChannelRequest::where('student_id', $student->id)->exists();

        return view('student.channels.index', compact('student', 'request_exists'));
    }

    /**
     * إرسال طلب انضمام
     */
    public function joinRequest()
    {
        $student = Student::find(1); // تجريبي

        ChannelRequest::create([
            'student_id' => $student->id,
            'channel_name' => "قناة " . $student->stage->label_ar . " - " . ($student->gender == 'ذكر' ? 'طلاب' : 'طالبات'),
            'status' => 'pending'
        ]);

        return response()->json([
            'icon' => 'success',
            'title' => 'تم إرسال طلب الانضمام بنجاح ✅'
        ]);
    }

}
