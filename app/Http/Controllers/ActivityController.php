<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // ضروري جداً للرسم البياني

class ActivityController extends Controller
{
    /**
     * واجهة "نبض النظام" للمدير (الإحصائيات المتقدمة)
     */
    public function adminPulse()
    {
        // 1. جلب آخر 50 نشاطاً مع بيانات الطلاب المرتبطين
        $activities = Activity::with('student')->latest()->take(50)->get();

        // 2. تجهيز بيانات الرسم البياني (نشاط المنصة في كل ساعة لآخر 24 ساعة)
        try {
            $driver = DB::getDriverName();
            $hourExpr = match($driver) {
                'pgsql'  => "date_part('hour', created_at)::int as hour",
                'sqlite' => "cast(strftime('%H', created_at) as integer) as hour",
                default  => "HOUR(created_at) as hour",
            };

            $chartData = Activity::select(
                    DB::raw($hourExpr),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>', now()->subDay())
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
        } catch (\Throwable $e) {
            $chartData = collect();
        }

        // 3. عرض الواجهة (تأكد أن الملف موجود في admin/activities/pulse.blade.php)
        return view('admin.activities.pulse', compact('activities', 'chartData'));
    }

    /**
     * عرض سجل النشاطات العادي (اختياري)
     */
    public function index()
    {
        $activities = Activity::with('student')->latest()->paginate(20);
        return view('admin.activities.index', compact('activities'));
    }
}
