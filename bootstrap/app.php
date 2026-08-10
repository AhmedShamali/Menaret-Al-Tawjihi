<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 1. تسجيل الـ Aliases للـ Middleware ليتوافق مع ملف routes/web.php
        $middleware->alias([
            'IsAdmin'   => \App\Http\Middleware\IsAdmin::class,
            'IsTeacher' => \App\Http\Middleware\IsTeacher::class,
            'IsStudent' => \App\Http\Middleware\IsStudent::class,
            'admin'     => \App\Http\Middleware\IsAdmin::class,   // احتياطاً
            'teacher'   => \App\Http\Middleware\IsTeacher::class, // احتياطاً
            'student'   => \App\Http\Middleware\IsStudent::class, // احتياطاً
        ]);

        // 2. توجيه المستخدمين المسجلين مسبقاً إذا حاولوا فتح صفحة الدخول
        $middleware->redirectUsersTo(function (Request $request) {
            if (Auth::guard('student')->check()) {
                return route('student.dashboard');
            }

            $user = Auth::user();
            if ($user) {
                if ($user->role === 'admin') {
                    return route('admin.dashboard');
                }
                if ($user->role === 'teacher') {
                    return route('teacher.dashboard');
                }
            }

            return route('login');
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
