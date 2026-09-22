<?php

namespace App\Http\Controllers;

use App\Models\{Student, Subject, EducationalContent, Exam};
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => Student::count(),
            'subjects' => Subject::count(),
            'lessons'  => EducationalContent::count(),
            'exams'    => Exam::count(),
        ];
        return view('welcome', compact('stats'));
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
    public function switchLanguage($locale)
    {
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        session(['locale' => $locale]);
        cookie()->queue('app_locale', $locale, 60 * 24 * 365);

        return redirect()->back();
    }
}


