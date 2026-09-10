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
        $student = \Illuminate\Support\Facades\Auth::guard('student')->user() 
            ?? \App\Support\CurrentActor::student() 
            ?? Student::with('stage')->first();

        if (!$student) {
            return redirect()->route('login');
        }

        if (!$student->relationLoaded('stage')) {
            $student->load('stage');
        }

        // فحص هل أرسل طلب سابقاً
        $request_exists = ChannelRequest::where('student_id', $student->id)->exists();

        return view('student.channels.index', compact('student', 'request_exists'));
    }

    /**
     * إرسال طلب انضمام
     */
    public function joinRequest()
    {
        $student = \Illuminate\Support\Facades\Auth::guard('student')->user() 
            ?? \App\Support\CurrentActor::student() 
            ?? Student::first();

        if (!$student) {
            return response()->json([
                'icon' => 'error',
                'title' => 'يرجى تسجيل الدخول أولاً'
            ], 401);
        }

        $stageName = $student->stage->label_ar ?? $student->stage->name_ar ?? 'المرحلة';
        $genderLabel = ($student->gender == 'ذكر' || $student->gender == 'male') ? 'طلاب' : 'طالبات';

        ChannelRequest::firstOrCreate([
            'student_id' => $student->id,
        ], [
            'channel_name' => "قناة " . $stageName . " - " . $genderLabel,
            'status' => 'pending'
        ]);

        return response()->json([
            'icon' => 'success',
            'title' => 'تم إرسال طلب الانضمام بنجاح ✅'
        ]);
    }
}
