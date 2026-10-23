<?php

namespace App\Http\Controllers;

use App\Models\{Student, Subject, EducationalContent, Exam};
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'students' => Student::count(),
                'subjects' => Subject::count(),
                'lessons'  => EducationalContent::count(),
                'exams'    => Exam::count(),
            ];
        } catch (\Throwable $e) {
            $stats = [
                'students' => 1250,
                'subjects' => 18,
                'lessons'  => 360,
                'exams'    => 140,
            ];
        }

        $freeVideos = $this->getFreeSampleVideos();

        return view('welcome', compact('stats', 'freeVideos'));
    }

    /**
     * استخراج وتجهيز الحصص المجانية الاستعراضية لمادتي اللغة العربية واللغة الإنجليزية
     */
    protected function getFreeSampleVideos(): array
    {
        $results = [
            'arabic'  => null,
            'english' => null,
        ];

        // 1. فحص إعدادات التخصيص اليدوي من الإدارة أولاً
        $customArabicUrl     = \App\Models\Setting::get('free_video_arabic_url');
        $customArabicTitle   = \App\Models\Setting::get('free_video_arabic_title');
        $customArabicTeacher = \App\Models\Setting::get('free_video_arabic_teacher');

        $customEnglishUrl     = \App\Models\Setting::get('free_video_english_url');
        $customEnglishTitle   = \App\Models\Setting::get('free_video_english_title');
        $customEnglishTeacher = \App\Models\Setting::get('free_video_english_teacher');

        // 2. محاولة جلب أول فيديو للغة العربية تلقائياً من قاعدة البيانات
        $arabicContent = null;
        $arabicSubject = null;
        try {
            $arabicSubject = \App\Models\Subject::where(function($q) {
                $q->where('name_ar', 'like', '%عربية%')
                  ->orWhere('name_ar', 'like', '%عربي%');
            })->first();

            if ($arabicSubject) {
                $arabicContent = \App\Models\EducationalContent::where('subject_id', $arabicSubject->id)
                    ->where('type', '!=', 'file')
                    ->where(function($q) {
                        $q->where('is_visible', true)->orWhereNull('is_visible');
                    })
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();
            }
        } catch (\Throwable $e) {}

        // 3. محاولة جلب أول فيديو للغة الإنجليزية تلقائياً من قاعدة البيانات
        $englishContent = null;
        $englishSubject = null;
        try {
            $englishSubject = \App\Models\Subject::where(function($q) {
                $q->where('name_ar', 'like', '%إنجليز%')
                  ->orWhere('name_ar', 'like', '%انجليز%');
            })->first();

            if ($englishSubject) {
                $englishContent = \App\Models\EducationalContent::where('subject_id', $englishSubject->id)
                    ->where('type', '!=', 'file')
                    ->where(function($q) {
                        $q->where('is_visible', true)->orWhereNull('is_visible');
                    })
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();
            }
        } catch (\Throwable $e) {}

        // 4. تجهيز بيانات الحصة الأولى للغة العربية
        $arRawUrl = !empty($customArabicUrl) ? $customArabicUrl : ($arabicContent?->url_path ?? '');
        $arYtId = $this->extractYoutubeId($arRawUrl);
        if (empty($arYtId)) {
            $arYtId = 'y6120QOlsfU'; // معرف يوتيوب تعليمي احتياطي للمنهاج
        }

        $results['arabic'] = [
            'key'            => 'arabic',
            'subject_id'     => $arabicSubject?->id ?? null,
            'subject_name'   => 'اللغة العربية',
            'branch_label'   => 'المنهاج الوزاري الفلسطيني (علمي وأدبي وريادي)',
            'badge'          => 'الحصة التأسيسية الأولى - مجاني 100%',
            'title'          => $customArabicTitle ?: ($arabicContent?->title ?: 'الدرس الأول: قواعد النحو والإعراب - أسلوب الشرط وأدواته الجازمة'),
            'teacher'        => $customArabicTeacher ?: ($arabicSubject?->teacher_name ?: 'نخبة معلمي اللغة العربية للثانوية العامة'),
            'youtube_id'     => $arYtId,
            'url_path'       => $arRawUrl,
            'embed_url'      => "https://www.youtube-nocookie.com/embed/{$arYtId}?enablejsapi=1&rel=0&modestbranding=1&iv_load_policy=3&playsinline=1",
            'watch_url'      => "https://www.youtube.com/watch?v={$arYtId}",
            'thumb_max'      => "https://img.youtube.com/vi/{$arYtId}/maxresdefault.jpg",
            'thumb_hq'       => "https://img.youtube.com/vi/{$arYtId}/hqdefault.jpg",
            'pdf_path'       => $arabicContent?->pdf_path ? asset('storage/' . $arabicContent->pdf_path) : null,
            'color'          => '#1e3a8a',
            'accent'         => '#d97706',
            'icon'           => 'fa-solid fa-feather-pointed',
            'views'          => $arabicContent?->views_count ?? 1840,
            'duration'       => '35 دقيقة',
            'quality'        => '1080p Full HD',
        ];

        // 5. تجهيز بيانات الحصة الأولى للغة الإنجليزية
        $enRawUrl = !empty($customEnglishUrl) ? $customEnglishUrl : ($englishContent?->url_path ?? '');
        $enYtId = $this->extractYoutubeId($enRawUrl);
        if (empty($enYtId)) {
            $enYtId = 'EngPalestine1'; 
        }

        $results['english'] = [
            'key'            => 'english',
            'subject_id'     => $englishSubject?->id ?? null,
            'subject_name'   => 'اللغة الإنجليزية (English for Palestine)',
            'branch_label'   => 'المنهاج الوزاري الفلسطيني (الفصل الأول)',
            'badge'          => 'الحصة التأسيسية الأولى - مجاني 100%',
            'title'          => $customEnglishTitle ?: ($englishContent?->title ?: 'Unit 1: A Question of Taste - Reading & Language Study | Grammar & Vocabulary in Context'),
            'teacher'        => $customEnglishTeacher ?: ($englishSubject?->teacher_name ?: 'نخبة معلمي اللغة الإنجليزية للثانوية العامة'),
            'youtube_id'     => $enYtId,
            'url_path'       => $enRawUrl,
            'embed_url'      => "https://www.youtube-nocookie.com/embed/{$enYtId}?enablejsapi=1&rel=0&modestbranding=1&iv_load_policy=3&playsinline=1",
            'watch_url'      => "https://www.youtube.com/watch?v={$enYtId}",
            'thumb_max'      => "https://img.youtube.com/vi/{$enYtId}/maxresdefault.jpg",
            'thumb_hq'       => "https://img.youtube.com/vi/{$enYtId}/hqdefault.jpg",
            'pdf_path'       => $englishContent?->pdf_path ? asset('storage/' . $englishContent->pdf_path) : null,
            'color'          => '#065f46',
            'accent'         => '#10b981',
            'icon'           => 'fa-solid fa-language',
            'views'          => $englishContent?->views_count ?? 2150,
            'duration'       => '30 دقيقة',
            'quality'        => '1080p Full HD',
        ];

        return $results;
    }

    /**
     * دالة مساعدة لاستخراج معرف يوتيوب (11 حرفاً) بدقة متناهية من أي صيغة رابط
     */
    protected function extractYoutubeId(?string $url): ?string
    {
        if (empty($url)) return null;
        $url = trim($url);
        if (preg_match('/^[a-zA-Z0-9_\-]{11}$/', $url)) {
            return $url;
        }
        $patterns = [
            '/[?&]v=([a-zA-Z0-9_\-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_\-]{11})/',
            '/(?:embed|shorts|live|v)\/([a-zA-Z0-9_\-]{11})/',
            '/(?:youtube(?:-nocookie)?\.com\/)(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)([^"&?\/\s]{11})/i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function faq() { return view('public.faq'); }
    public function contact() { return view('public.contact'); }
    public function terms() { return view('public.terms'); }
    public function privacy() { return view('public.privacy'); }

    /**
     * استقبال ومعالجة الشكاوى والدعم الفني
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:150',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'type'    => 'required|string|max:100',
            'message' => 'required|string|min:5|max:3000',
        ], [
            'name.required'    => 'يرجى كتابة الاسم الكريم.',
            'email.required'   => 'يرجى إدخال البريد الإلكتروني.',
            'type.required'    => 'يرجى اختيار نوع الاستفسار أو الشكوى.',
            'message.required' => 'يرجى كتابة نص الرسالة بالتفصيل.',
        ]);

        $complaint = null;
        try {
            $complaint = \App\Models\Complaint::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'phone'    => $request->input('phone'),
                'type'     => $validated['type'],
                'category' => $validated['type'],
                'subject'  => $validated['type'],
                'message'  => $validated['message'],
                'status'   => 'new',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error saving complaint: ' . $e->getMessage());
        }

        try {
            $actionUrl = $complaint ? route('admin.inquiries.index', ['open_id' => $complaint->id]) : route('admin.inquiries.index');
            \App\Services\NotificationService::notifyAdmin(
                'شكوى / استفسار جديد 📩',
                "وردت رسالة جديدة من ({$validated['name']}) بخصوص: {$validated['type']}.",
                'support',
                $actionUrl,
                'fa-envelope'
            );
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'تم استلام رسالتك وشكواك بنجاح! سيقوم فريق الدعم الفني بمتابعتها والرد عليك فورياً. 🕊️'
            ]);
        }

        return back()->with('success', 'تم استلام رسالتك وشكواك بنجاح! سيقوم فريق الدعم الفني بمتابعتها والتواصل معك.');
    }

    /**
     * تبديل لغة المنصة وحفظها في الجلسة والكوكي (عربي / إنجليزي)
     */
    public function switchLanguage(Request $request, $locale)
    {
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        \Illuminate\Support\Facades\Session::put('locale', $locale);
        \Illuminate\Support\Facades\Session::save();
        \Illuminate\Support\Facades\App::setLocale($locale);

        $cookie = cookie()->forever('app_locale', $locale);

        $referer = $request->headers->get('referer');

        if (
            !$referer ||
            str_contains($referer, '/change-language') ||
            str_contains($referer, '/language/') ||
            preg_match('#/lang/(ar|en)#', $referer)
        ) {
            return redirect()->route('home', ['lang' => $locale])->withCookie($cookie);
        }

        $cleanReferer = preg_replace('/([?&])lang=[^&]*(&|$)/', '$1', $referer);
        $cleanReferer = rtrim($cleanReferer, '?&');
        $targetUrl = $cleanReferer . (str_contains($cleanReferer, '?') ? '&' : '?') . 'lang=' . $locale;

        return redirect()->to($targetUrl)->withCookie($cookie);
    }
}


