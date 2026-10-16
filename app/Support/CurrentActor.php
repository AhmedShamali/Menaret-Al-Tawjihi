<?php

namespace App\Support;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CurrentActor
{
    public static function student(): ?Student
    {
        $user = Auth::guard('student')->user();
        return ($user instanceof Student) ? $user : null;
    }

    public static function staff(): ?User
    {
        $user = Auth::guard('web')->user();
        return ($user instanceof User) ? $user : null;
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
