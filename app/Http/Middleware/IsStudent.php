<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();

            // فحص دوري لاستحقاق رسوم الشهر الجديد والتجميد التلقائي
            if ($student->status === 'active') {
                try {
                    // مزامنة الاشتراكات الشهرية
                    \App\Models\StudentMonthlySubscription::syncWithStudentPayments($student);

                    if ($student->approved_at && $student->isMonthlyFeeDue()) {
                        $dueMonthName = $student->currentDueMonthName();
                        $dueFee = $student->monthlyAmountDue();
                        $reason = "استحقاق رسوم {$dueMonthName} بمبلغ (" . number_format($dueFee, 0) . " ₪). يرجى سداد قسط الشهر الجديد لاستئناف الدراسة.";

                        $student->status = 'suspended';
                        $student->freeze_reason = $reason;
                        $student->save();

                        try {
                            \App\Services\NotificationService::notifyStudent(
                                $student->id,
                                'تجميد مؤقت: استحقاق رسوم شهر جديد 🔒',
                                $reason,
                                'payment',
                                route('student.pending-approval'),
                                'fa-receipt'
                            );
                        } catch (\Throwable $e) {}

                        return redirect()->route('student.pending-approval');
                    }
                } catch (\Throwable $e) {
                    \Log::error('IsStudent auto-freeze check error: ' . $e->getMessage());
                }
            }

            // فحص حالة اعتماد الطالب من قبل إدارة المنصة
            if ($student->status !== 'active') {
                // السماح لصفحة انتظار الاعتماد، ونموذج إرسال إشعار السداد، وتسجيل الخروج
                if ($request->routeIs('student.pending-approval') || 
                    $request->routeIs('student.pendingPayment.submit') || 
                    $request->routeIs('student.pendingPayment.show') || 
                    $request->is('student/pending-payment*') || 
                    $request->is('logout') || 
                    $request->is('student/logout')) {
                    return $next($request);
                }

                return redirect()->route('student.pending-approval');
            }

            // إذا كان حساب الطالب مفعلاً بالفعل وحاول زيارة صفحة الانتظار، يتم تحويله للوحة التحكم
            if ($request->routeIs('student.pending-approval')) {
                return redirect()->route('student.dashboard');
            }

            return $next($request);
        }

        return redirect('/login')->withErrors(['error' => 'يرجى تسجيل دخولك كطالب أولاً']);
    }
}
