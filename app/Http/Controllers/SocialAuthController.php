<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Stage;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * توجيه المستخدم لصفحة مصادقة Google الرسمية
     */
    public function redirectToGoogle(Request $request)
    {
        $clientId = config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID');
        $redirectUri = url('/auth/google/callback');

        // إذا لم يتم ضبط معرّفات Google بعد في بيئة التشغيل، نتيح الاستيراد الذكي المباشر
        if (!$clientId) {
            return redirect()->route('students.create', ['social_prompt' => 'google_demo'])
                ->with('info', 'يرجى إدخال بيانات Google Client ID في ملف .env لتفعيل المصادقة الحية التامة. تم تفعيل التعبئة الذكية للبريد.');
        }

        $state = Str::random(40);
        $request->session()->put('oauth_state', $state);

        $params = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid profile email',
            'access_type'   => 'offline',
            'state'         => $state,
            'prompt'        => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    /**
     * استقبال استجابة Google OAuth2 ومعالجة الدخول أو التسجيل التلقائي
     */
    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('students.create')->withErrors(['error' => 'تم إلغاء عملية تسجيل الدخول عبر Google.']);
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('students.create')->withErrors(['error' => 'لم يتم استلام كود المصادقة من Google.']);
        }

        $clientId = config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID');
        $clientSecret = config('services.google.client_secret') ?: env('GOOGLE_CLIENT_SECRET');
        $redirectUri = url('/auth/google/callback');

        try {
            // 1. استبدال الكود بـ Access Token
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code'          => $code,
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => $redirectUri,
                'grant_type'    => 'authorization_code',
            ]);

            if (!$tokenResponse->successful()) {
                Log::error('Google OAuth Token Error: ' . $tokenResponse->body());
                return redirect()->route('students.create')->withErrors(['error' => 'فشل التحقق من رمز المصادقة لدى Google.']);
            }

            $accessToken = $tokenResponse->json()['access_token'] ?? null;
            if (!$accessToken) {
                return redirect()->route('students.create')->withErrors(['error' => 'تعذر الحصول على تصريح الوصول من Google.']);
            }

            // 2. جلب بيانات المستخدم من Google UserInfo API
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');
            if (!$userResponse->successful()) {
                return redirect()->route('students.create')->withErrors(['error' => 'فشل جلب بيانات الحساب من Google.']);
            }

            $googleData = $userResponse->json();
            $email = $googleData['email'] ?? null;
            $name = $googleData['name'] ?? ($googleData['given_name'] . ' ' . ($googleData['family_name'] ?? ''));
            $googleId = $googleData['sub'] ?? null;
            $picture = $googleData['picture'] ?? null;

            if (!$email) {
                return redirect()->route('students.create')->withErrors(['error' => 'حساب Google لا يحتوي على بريد إلكتروني معتمد.']);
            }

            // 3. التحقق: هل الطالب مسجل مسبقاً في النظام؟
            $existingStudent = Student::where('email', $email)
                ->orWhere('google_id', $googleId)
                ->first();

            if ($existingStudent) {
                // تحديث بيانات Google للطالب الحالي
                $existingStudent->update([
                    'google_id'  => $googleId,
                    'provider'   => 'google',
                    'avatar_url' => $picture ?: $existingStudent->avatar_url,
                ]);

                Auth::guard('student')->login($existingStudent);
                $request->session()->regenerate();

                if ($existingStudent->status !== 'active') {
                    return redirect()->route('student.pending-approval')->with('info', 'أهلاً بك مجدداً! حسابك قيد مراجعة الإدارة.');
                }

                return redirect()->route('student.dashboard')->with('success', 'تم تسجيل الدخول بحساب Google بنجاح! 🚀');
            }

            // 4. إذا كان طالباً جديداً: حفظ بيانات Google بالجلسة وإعادته لصفحة التسجيل معبأة مسبقاً
            $request->session()->put('google_profile', [
                'email'     => $email,
                'name_ar'   => $name,
                'google_id' => $googleId,
                'picture'   => $picture,
            ]);

            return redirect()->route('students.create', ['social' => 'google'])
                ->with('success', "مرحباً بك ({$name})! 🎉 تم ربط حسابك في Google بنجاح. أكمل فقط الفرع ورقم الهوية للبدء.");

        } catch (\Throwable $e) {
            Log::error('Google Callback Exception: ' . $e->getMessage());
            return redirect()->route('students.create')->withErrors(['error' => 'حدث خطأ غير متوقع أثناء المعالجة: ' . $e->getMessage()]);
        }
    }

    /**
     * معالجة توكن Google One-Tap أو Google Sign-In القادم من واجهة المستخدم (GIS)
     */
    public function handleOneTap(Request $request)
    {
        $credential = $request->input('credential');
        if (!$credential) {
            return response()->json(['status' => 'error', 'message' => 'رمز المصادقة مفقود'], 400);
        }

        try {
            // تفكيك محتوى الـ JWT من Google (الجزء الثاني Payload)
            $parts = explode('.', $credential);
            if (count($parts) < 2) {
                return response()->json(['status' => 'error', 'message' => 'رمز غير صالح'], 400);
            }

            $payloadJson = base64_decode(str_pad(strtr($parts[1], '-_', '+/'), strlen($parts[1]) % 4, '=', STR_PAD_RIGHT));
            $googleUser = json_decode($payloadJson, true);

            if (!$googleUser || empty($googleUser['email'])) {
                return response()->json(['status' => 'error', 'message' => 'بيانات Google غير صالحة'], 400);
            }

            $email = $googleUser['email'];
            $name = $googleUser['name'] ?? 'طالب جديد';
            $picture = $googleUser['picture'] ?? null;
            $googleId = $googleUser['sub'] ?? null;

            // التحقق من وجود الطالب
            $student = Student::where('email', $email)
                ->orWhere('google_id', $googleId)
                ->first();

            if ($student) {
                $student->update([
                    'google_id'  => $googleId,
                    'provider'   => 'google',
                    'avatar_url' => $picture ?: $student->avatar_url,
                ]);

                Auth::guard('student')->login($student);
                $request->session()->regenerate();

                $redirect = ($student->status === 'active')
                    ? route('student.dashboard')
                    : route('student.pending-approval');

                return response()->json([
                    'status'   => 'logged_in',
                    'exists'   => true,
                    'message'  => 'تم تسجيل الدخول بنجاح عبر حساب Google! 🎉',
                    'redirect' => $redirect,
                ]);
            }

            // حفظ بيانات Google لاستخدامها في إكمال الحساب
            $request->session()->put('google_profile', [
                'email'     => $email,
                'name_ar'   => $name,
                'google_id' => $googleId,
                'picture'   => $picture,
            ]);

            return response()->json([
                'status'  => 'prefill',
                'exists'  => false,
                'message' => 'تم استيراد بيانات حسابك من Google بنجاح! 🎓',
                'data'    => [
                    'email'    => $email,
                    'name_ar'  => $name,
                    'picture'  => $picture,
                    'google_id'=> $googleId
                ]
            ]);

        } catch (\Throwable $e) {
            Log::error('Google OneTap Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * استيراد فوري سريع للبيانات للتسهيل المباشر
     */
    public function quickFill(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name'  => 'nullable|string',
        ]);

        $email = trim($request->email);
        $name = trim($request->name) ?: explode('@', $email)[0];

        $request->session()->put('google_profile', [
            'email'   => $email,
            'name_ar' => $name,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'تم استيراد البريد والاسم بنجاح! يرجى إكمال باقي الحقول.',
            'data'    => [
                'email'   => $email,
                'name_ar' => $name,
            ]
        ]);
    }
}
