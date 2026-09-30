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
            'email'    => 'required|string',
            'password' => 'required|string',
            'role'     => 'required|in:student,teacher,admin',
        ], [
            'email.required'    => 'يرجى إدخال اسم المستخدم، البريد الأكاديمي، أو رقم الهوية.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'role.required'     => 'يرجى تحديد نوع الحساب.',
        ]);

        $input = trim($request->input('email'));
        $password = $request->input('password');
        $role = $request->role; // طالب، مدرس، أو مدير

        // 1. محاولة الدخول كطالب
        if ($role === 'student') {
            // البحث عن الطالب عبر البريد أو رقم الهوية أو الهاتف أو اسم المستخدم
            $student = Student::where('email', $input)
                ->orWhere('nid', $input)
                ->orWhere('phone', $input)
                ->orWhere('email', strtolower($input) . '@tawjihi.ps')
                ->orWhere('email', strtolower($input) . '@tawjihi-gaza.ps')
                ->first();

            if ($student && Hash::check($password, $student->password)) {
                Auth::guard('student')->login($student, $request->filled('remember'));

                // تنظيف كامل للجلسة وإعادتها لتجنب التداخل
                $request->session()->regenerate();

                // فحص موافقة المدير على تفعيل حساب الطالب واشتراكه
                if ($student->status !== 'active') {
                    return redirect()->route('student.pending-approval');
                }

                return redirect()->route('student.dashboard');
            }

            // محاولة بديلة عبر attempt القياسي
            if (Auth::guard('student')->attempt(['email' => $input, 'password' => $password], $request->filled('remember'))) {
                $student = Auth::guard('student')->user();
                $request->session()->regenerate();

                if ($student->status !== 'active') {
                    return redirect()->route('student.pending-approval');
                }

                return redirect()->route('student.dashboard');
            }
        }
        // 2. محاولة الدخول لموظفي النظام (مدرس/مدير)
        else {
            // البحث عن المستخدم عبر البريد أو الاسم أو النطاق الرسمي
            $user = User::where('email', $input)
                ->orWhere('email', strtolower($input) . '@tawjihi.ps')
                ->orWhere('name', $input)
                ->first();

            if ($user && Hash::check($password, $user->password)) {
                // التحقق: هل الدور الذي اختاره المستخدم يطابق دوره في قاعدة البيانات؟
                if ($role !== $user->role) {
                    $roleName = $user->role === 'admin' ? 'مدير' : ($user->role === 'teacher' ? 'مدرس' : $user->role);
                    $requestedRoleName = $role === 'admin' ? 'مدير' : ($role === 'teacher' ? 'مدرس' : $role);

                    return back()->withErrors([
                        'error' => "عذراً، هذا الحساب مسجل كـ ({$roleName}) وليس كـ ({$requestedRoleName})."
                    ])->withInput();
                }

                Auth::guard('web')->login($user, $request->filled('remember'));

                $request->session()->regenerate();
                $request->session()->forget('url.intended');

                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->role === 'teacher') {
                    return redirect()->route('teacher.dashboard');
                }
            }

            // محاولة بديلة عبر attempt القياسي
            if (Auth::guard('web')->attempt(['email' => $input, 'password' => $password], $request->filled('remember'))) {
                $user = Auth::user();

                if ($role !== $user->role) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    $roleName = $user->role === 'admin' ? 'مدير' : ($user->role === 'teacher' ? 'مدرس' : $user->role);
                    $requestedRoleName = $role === 'admin' ? 'مدير' : ($role === 'teacher' ? 'مدرس' : $role);

                    return back()->withErrors([
                        'error' => "عذراً، هذا الحساب مسجل كـ ({$roleName}) وليس كـ ({$requestedRoleName})."
                    ])->withInput();
                }

                $request->session()->regenerate();
                $request->session()->forget('url.intended');

                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->role === 'teacher') {
                    return redirect()->route('teacher.dashboard');
                }
            }
        }

        return back()->withErrors(['error' => 'بيانات الدخول غير صحيحة أو الحساب غير موجود.'])->withInput();
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

        // فحص سجلات الطلاب أولاً بمطابقة الإيميل أو اسم المستخدم أو الهاتف ورقم الهوية بدقة
        $student = Student::where('nid', $nid)
            ->where(function ($q) use ($email) {
                $q->where('email', $email)
                  ->orWhere('email', strtolower($email) . '@tawjihi.ps')
                  ->orWhere('email', strtolower($email) . '@tawjihi-gaza.ps')
                  ->orWhere('phone', $email)
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
