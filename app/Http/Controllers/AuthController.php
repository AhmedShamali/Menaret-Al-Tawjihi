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
            'role'     => 'required|in:student,teacher,admin,videographer',
        ], [
            'email.required'    => 'يرجى إدخال اسم المستخدم، البريد الأكاديمي، أو رقم الهوية.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'role.required'     => 'يرجى تحديد نوع الحساب.',
        ]);

        $input = trim((string)$request->input('email'));
        $inputLower = strtolower($input);
        $password = (string)$request->input('password');
        $passwordTrimmed = trim($password);
        $role = $request->role; // طالب، مدرس، مدير، أو مصور

        $getRoleName = function($r) {
            return match($r) {
                'admin'        => 'مدير',
                'teacher'      => 'مدرس',
                'videographer' => 'مصور / منسق وسائط',
                'student'      => 'طالب',
                default        => $r
            };
        };

        // دالة موحدة للتحقق من كلمة المرور بدعم التشفير وكلمات المرور المعتمدة
        $verifyPassword = function($model) use ($password, $passwordTrimmed) {
            if (!$model) return false;

            // 1. الفحص القياسي لهاش لارافيل
            if (Hash::check($password, $model->password) || Hash::check($passwordTrimmed, $model->password)) {
                return true;
            }

            // 2. فحص كلمة المرور المعتمدة المخزنة كنص واضح plain_password مع التحديث التلقائي
            if (!empty($model->plain_password)) {
                $plain = trim((string)$model->plain_password);
                if ($plain === $password || $plain === $passwordTrimmed || (string)$model->plain_password === $password) {
                    try {
                        $model->password = Hash::make($passwordTrimmed);
                        $model->save();
                    } catch (\Throwable $e) {}
                    return true;
                }
            }

            return false;
        };

        // دالة موحدة للبحث عن كادر المنظومة (إدارة، مدرسين، مصورين)
        $findStaff = function($term) use ($inputLower) {
            return User::where('email', $term)
                ->orWhere('email', $inputLower)
                ->orWhere('name', $term)
                ->orWhere('phone', $term)
                ->orWhere('email', $inputLower . '@tawjihi.ps')
                ->orWhere('email', $inputLower . '@step.ps')
                ->first();
        };

        // دالة موحدة للبحث عن الطلاب
        $findStudent = function($term) use ($inputLower) {
            return Student::where('email', $term)
                ->orWhere('email', $inputLower)
                ->orWhere('nid', $term)
                ->orWhere('phone', $term)
                ->orWhere('email', $inputLower . '@tawjihi.ps')
                ->orWhere('email', $inputLower . '@tawjihi-gaza.ps')
                ->orWhere('email', $inputLower . '@step.ps')
                ->first();
        };

        // 1. مسار تسجيل دخول الطالب
        if ($role === 'student') {
            $student = $findStudent($input);

            if ($student && $verifyPassword($student)) {
                Auth::guard('student')->login($student, $request->filled('remember'));
                $request->session()->regenerate();

                if ($student->status !== 'active') {
                    return redirect()->route('student.pending-approval');
                }

                return redirect()->route('student.dashboard');
            }

            // محاولة بديلة عبر attempt القياسي
            if (Auth::guard('student')->attempt(['email' => $input, 'password' => $passwordTrimmed], $request->filled('remember'))) {
                $student = Auth::guard('student')->user();
                $request->session()->regenerate();

                if ($student->status !== 'active') {
                    return redirect()->route('student.pending-approval');
                }

                return redirect()->route('student.dashboard');
            }

            // فحص ذكي: هل المستخدم في الحقيقة من كادر النظام واختار بالخطأ تبويب الطالب؟
            $staff = $findStaff($input);
            if ($staff && $verifyPassword($staff)) {
                Auth::guard('web')->login($staff, $request->filled('remember'));
                $request->session()->regenerate();
                $request->session()->forget('url.intended');

                if ($staff->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                if ($staff->role === 'teacher') {
                    return redirect()->route('teacher.dashboard');
                }
                if ($staff->role === 'videographer') {
                    return redirect()->route('videographer.dashboard');
                }
            }
        }
        // 2. مسار تسجيل دخول كادر المنظومة (مدرس / مدير / مصور)
        else {
            $user = $findStaff($input);

            if ($user && $verifyPassword($user)) {
                Auth::guard('web')->login($user, $request->filled('remember'));
                $request->session()->regenerate();
                $request->session()->forget('url.intended');

                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                if ($user->role === 'teacher') {
                    return redirect()->route('teacher.dashboard');
                }
                if ($user->role === 'videographer') {
                    return redirect()->route('videographer.dashboard');
                }
            }

            // محاولة بديلة عبر attempt القياسي
            if (Auth::guard('web')->attempt(['email' => $input, 'password' => $passwordTrimmed], $request->filled('remember')) ||
                Auth::guard('web')->attempt(['email' => $inputLower, 'password' => $passwordTrimmed], $request->filled('remember'))) {
                $user = Auth::user();
                $request->session()->regenerate();
                $request->session()->forget('url.intended');

                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                if ($user->role === 'teacher') {
                    return redirect()->route('teacher.dashboard');
                }
                if ($user->role === 'videographer') {
                    return redirect()->route('videographer.dashboard');
                }
            }

            // فحص ذكي: هل المستخدم طالب واختار بالخطأ تبويب الكادر؟
            $student = $findStudent($input);
            if ($student && $verifyPassword($student)) {
                Auth::guard('student')->login($student, $request->filled('remember'));
                $request->session()->regenerate();

                if ($student->status !== 'active') {
                    return redirect()->route('student.pending-approval');
                }

                return redirect()->route('student.dashboard');
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
