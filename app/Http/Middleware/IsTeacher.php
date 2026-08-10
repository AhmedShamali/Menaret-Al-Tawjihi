<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsTeacher
{
    public function handle(Request $request, Closure $next): Response
    {
        // إذا كان مسجل دخول وهو مدرس -> اعطه السماح بالمرور
        if (Auth::check() && Auth::user()->role === 'teacher') {
            return $next($request);
        }

        // إذا كان أدمن محول بالخطأ هنا -> ارفعه للوحة الأدمن
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // إذا لم يكن مسجلاً أصلاً
        return redirect()->route('login');
    }
}
