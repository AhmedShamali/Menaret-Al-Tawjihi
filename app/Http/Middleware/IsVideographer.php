<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsVideographer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // المصور أو المدير يمتلكان صلاحية الوصول لبوابة المصور
        if ($user->role === 'videographer' || $user->role === 'admin') {
            return $next($request);
        }

        abort(403, 'عذراً، هذا المسار مخصص لطاقم تصوير وإنتاج المحاضرات فقط.');
    }
}
