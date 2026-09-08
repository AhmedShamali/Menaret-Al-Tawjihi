<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StreakController extends Controller
{
    /**
     * عرض لوحة الشرف وأوائل طلبة فلسطين على المنصة
     */
    public function leaderboard()
    {
        $currentStudent = Auth::guard('student')->user();
        if ($currentStudent) {
            $currentStudent->recordDailyStreak();
        }

        // جلب أفضل 20 طالباً حسب النقاط وأيام الالتزام
        $topStudents = Student::with('stage')
            ->orderBy('total_points', 'desc')
            ->orderBy('streak_count', 'desc')
            ->take(20)
            ->get();

        return view('student.leaderboard', compact('topStudents', 'currentStudent'));
    }

    /**
     * تسجيل نشاط اليوم للطالب
     */
    public function recordActivity()
    {
        $student = Auth::guard('student')->user();
        if ($student) {
            $newStreak = $student->recordDailyStreak();
            return response()->json([
                'success' => true,
                'streak' => $newStreak,
                'points' => $student->total_points
            ]);
        }

        return response()->json(['success' => false], 401);
    }
}
