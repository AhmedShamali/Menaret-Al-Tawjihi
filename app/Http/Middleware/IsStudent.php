<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, $next) {
    if (auth()->guard('student')->check()) {
        return $next($request);
    }
    return redirect('/login')->withErrors(['error' => 'يرجى تسجيل دخولك كطالب أولاً']);
}
}
