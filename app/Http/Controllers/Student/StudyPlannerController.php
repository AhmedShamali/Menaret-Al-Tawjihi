<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyPlannerController extends Controller
{
    /**
     * عرض صفحة مولّد جدول المراجعة الذكي للامتحانات
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        return view('student.planner.index', compact('student'));
    }

    /**
     * توليد جدول المراجعة الذكي بناءً على معطيات الطالب
     */
    public function generate(Request $request)
    {
        $request->validate([
            'exam_start_date' => 'required|date|after:today',
            'daily_hours'     => 'required|numeric|min:2|max:14',
            'subjects'        => 'required|array|min:1',
        ]);

        $startDate = now();
        $examDate = \Carbon\Carbon::parse($request->exam_start_date);
        $daysRemaining = max(1, $startDate->diffInDays($examDate));

        $dailyHours = floatval($request->daily_hours);
        $totalHoursAvailable = $daysRemaining * $dailyHours;

        $subjects = $request->subjects; // ['name' => ..., 'difficulty' => 'hard|medium|easy']
        $totalWeight = 0;

        foreach ($subjects as &$sub) {
            $diff = $sub['difficulty'] ?? 'medium';
            $weight = match ($diff) {
                'hard' => 3,
                'medium' => 2,
                'easy' => 1,
                default => 2,
            };
            $sub['weight'] = $weight;
            $totalWeight += $weight;
        }

        // توزيع الساعات على المواد
        $scheduleSummary = [];
        foreach ($subjects as $sub) {
            $allocatedHours = round(($sub['weight'] / $totalWeight) * $totalHoursAvailable, 1);
            $dailyMins = round(($allocatedHours / $daysRemaining) * 60);

            $scheduleSummary[] = [
                'name' => $sub['name'],
                'difficulty' => $sub['difficulty'] ?? 'medium',
                'total_hours' => $allocatedHours,
                'daily_minutes' => $dailyMins,
                'pomodoro_sessions' => ceil($allocatedHours / 0.8), // جلسات 50 دقيقة
            ];
        }

        return response()->json([
            'success' => true,
            'days_remaining' => $daysRemaining,
            'daily_hours' => $dailyHours,
            'total_hours' => $totalHoursAvailable,
            'schedule' => $scheduleSummary,
        ]);
    }
}
