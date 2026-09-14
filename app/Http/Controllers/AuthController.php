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
            'email'        => 'required',
            'nid'          => 'required|digits:9',
            'new_password' => 'nullable|min:6',
        ], [
            'email.required'        => 'يرجى إدخال البريد الإلكتروني أو اسم المستخدم.',
            'nid.required'          => 'رقم الهوية الفلسطينية إلزامي للتحقق من هوية الحساب.',
            'nid.digits'            => 'رقم الهوية الفلسطينية يجب أن يتكون من 9 أرقام تماماً.',
            'new_password.min'      => 'كلمة المرور الجديدة يجب ألا تقل عن 6 خانات.',
        ]);

        $email = trim($request->email);
        $nid = trim($request->nid);

        // فحص سجلات الطلاب أولاً بمطابقة الإيميل ورقم الهوية بدقة
        $student = Student::where('nid', $nid)
            ->where(function ($q) use ($email) {
                $q->where('email', $email)
                  ->orWhere('email', strtolower($email) . '@tawjihi-gaza.ps')
                  ->orWhere('name_ar', 'like', "%{$email}%");
            })
            ->first();

        $user = null;
        if (!$student) {
            $user = User::where('email', $email)->first();
        }

        if (!$student && !$user) {
            return back()->withErrors([
                'error' => 'بيانات التحقق غير متطابقة! يرجى التأكد من رقم الهوية الفلسطينية المكون من 9 أرقام والبريد المسجل، أو التواصل مع إدارة المنصة عبر واتساب.'
            ]);
        }

        // إذا تم إرسال كلمة مرور جديدة، يتم تحديثها فورياً بعد تأكيد مطابقة الهوية
        if ($request->filled('new_password')) {
            $newHash = Hash::make($request->new_password);
            if ($student) {
                $student->password = $newHash;
                try {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('students', 'plain_password')) {
                        $student->plain_password = $request->new_password;
                    }
                } catch (\Throwable $e) {}
                $student->save();
            } elseif ($user) {
                $user->password = $newHash;
                try {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'plain_password')) {
                        $user->plain_password = $request->new_password;
                    }
                } catch (\Throwable $e) {}
                $user->save();
            }

            return back()->with('status', 'تم التحقق من مطابقة الهوية الوطنية وتعيين كلمة المرور الجديدة بنجاح! يمكنك الآن تسجيل الدخول.');
        }

        return back()->with('status', 'تمت مطابقة رقم الهوية الفلسطينية بنجاح! يمكنك تعيين كلمة مرور جديدة أو التواصل فورياً مع المشرف العام.');
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
