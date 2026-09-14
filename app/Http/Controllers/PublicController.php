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

        try {
            \App\Models\Complaint::create([
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'phone'   => $request->input('phone'),
                'type'    => $validated['type'],
                'message' => $validated['message'],
                'status'  => 'pending',
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error saving complaint: ' . $e->getMessage());
        }

        try {
            \App\Services\NotificationService::notifyAdmin(
                'شكوى / استفسار جديد 📩',
                "وردت رسالة جديدة من ({$validated['name']}) بخصوص: {$validated['type']}.",
                'support',
                route('admin.messages.index'),
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
}


