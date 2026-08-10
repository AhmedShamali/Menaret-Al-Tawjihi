<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if ($role === 'admin' && (!auth()->check() || auth()->user()->role !== 'admin')) {
            abort(403, 'غير مصرح للمدراء فقط');
        }

        if ($role === 'student' && !auth()->guard('student')->check()) {
            abort(403, 'للطلاب فقط');
        }

        return $next($request);
    }
}
