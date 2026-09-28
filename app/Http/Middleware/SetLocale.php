<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $queryLang = $request->query('lang');
        if ($queryLang && in_array($queryLang, ['ar', 'en'])) {
            $locale = $queryLang;
            Session::put('locale', $locale);
            cookie()->queue(cookie()->forever('app_locale', $locale));
        } else {
            $locale = Session::get('locale', $request->cookie('app_locale', 'ar'));
        }

        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
