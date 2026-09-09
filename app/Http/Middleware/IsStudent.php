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

            // فحص حالة اعتماد الطالب من قبل إدارة المنصة
            if ($student->status !== 'active') {
                // السماح فقط لصفحة انتظار الاعتماد وتسجيل الخروج
                if ($request->routeIs('student.pending-approval') || $request->is('logout') || $request->is('student/logout')) {
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
