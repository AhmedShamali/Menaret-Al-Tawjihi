<?php

namespace App\Support;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CurrentActor
{
    public static function student(): ?Student
    {
        return Auth::guard('student')->user();
    }

    public static function staff(): ?User
    {
        return Auth::guard('web')->user();
    }

    public static function examsIndexRoute(): string
    {
        $user = self::staff();

        if ($user && $user->role === 'teacher') {
            return 'teacher.exams.index';
        }

        return 'admin.exams.index';
    }

    public static function isAdmin(): bool
    {
        return self::staff()?->role === 'admin';
    }

    public static function isTeacher(): bool
    {
        return self::staff()?->role === 'teacher';
    }
}
