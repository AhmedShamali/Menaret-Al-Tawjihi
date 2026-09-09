<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\User;

class AuthController extends Controller
{
    // عرض صفحة الدخول
    public function showLogin()
    {
        return view('auth.login');
    }

    // تنفيذ عملية الدخول الذكي
    public function handleLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'required|in:student,teacher,admin',
        ]);

        $credentials = $request->only('email', 'password');
        $role = $request->role; // طالب، مدرس، أو مدير

        // 1. محاولة الدخول كطالب
        if ($role === 'student') {
            if (Auth::guard('student')->attempt($credentials)) {
                $student = Auth::guard('student')->user();

                // تنظيف كامل للجلسة وإعادتها لتجنب التداخل
                $request->session()->regenerate();

                // فحص موافقة المدير على تفعيل حساب الطالب واشتراكه
                if ($student->status !== 'active') {
                    return redirect()->route('student.pending-approval');
                }

                // استخدام redirect() مباشر بدلاً من intended لتجنب التوجيه القديم
                return redirect()->route('student.dashboard');
            }
        }
        // 2. محاولة الدخول لموظفي النظام (مدرس/مدير)
        else {
            if (Auth::guard('web')->attempt($credentials)) {
                $user = Auth::user();

                // التحقق: هل الدور الذي اختاره المستخدم يطابق دوره في قاعدة البيانات؟
                if ($role !== $user->role) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    $roleName = $user->role === 'admin' ? 'مدير' : ($user->role === 'teacher' ? 'مدرس' : $user->role);
                    $requestedRoleName = $role === 'admin' ? 'مدير' : ($role === 'teacher' ? 'مدرس' : $role);

                    return back()->withErrors([
                        'error' => "عذراً، هذا الحساب مسجل كـ ({$roleName}) وليس كـ ({$requestedRoleName})."
                    ]);
                }

                // تنظيف الـ Session وإعادة توليد المعرّف لمنع التوجيهات القديمة (Intended Cache)
                $request->session()->regenerate();
                $request->session()->forget('url.intended');

                // التوجيه الصريح والصارم بناءً على دور المستخدم المخزن في قاعدة البيانات
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->role === 'teacher') {
                    return redirect()->route('teacher.dashboard');
                }
            }
        }

        return back()->withErrors(['error' => 'بيانات الدخول غير صحيحة أو الحساب غير موجود.']);
    }

    public function handleForgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = trim($request->email);
        $user = User::where('email', $email)->first();
        $student = Student::where('email', $email)->first();

        if (!$user && !$student) {
            return back()->withErrors(['error' => 'البريد الإلكتروني المدخل غير مسجل في النظام.']);
        }

        return back()->with('status', 'إذا كان الحساب مسجلاً، فقد تم إرسال تعليمات إعادة تعيين كلمة المرور إلى بريدك.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
