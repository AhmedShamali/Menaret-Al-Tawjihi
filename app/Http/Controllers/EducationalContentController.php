<?php

namespace App\Http\Controllers;

use App\Models\EducationalContent;
use App\Models\Stage;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class EducationalContentController extends Controller
{
    public function index()
    {
        $contents = EducationalContent::with('subject.stage')->latest()->get();
        return view('educational_contents.index', compact('contents'));
    }

    public function create($subject_id = null)
    {
        $mySubject = $subject_id ? Subject::find($subject_id) : (auth()->user()->subject ?? null);
        $stages = Stage::with('subjects')->get();

        return view('educational_contents.create', compact('mySubject', 'stages'));
    }

    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $validator = validator($request->all(), [
            'subject_id'        => 'required',
            'title'             => 'required|string|min:3',
            'order'             => 'required|numeric',
            'file_upload_video' => 'nullable|file|mimes:mp4,mov,ogg,qt,webm|max:204800',
            'file_upload_pdf'   => 'nullable|file|mimes:pdf,doc,docx|max:50120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'icon'  => 'error',
                'title' => $validator->errors()->first(),
            ], 400);
        }

        // --- جلب المعلم الحقيقي المسجل حالياً وتحديث المادة به ---
        if (auth()->check()) {
            $subject = Subject::find($request->subject_id);
            if ($subject) {
                $subject->user_id = auth()->id();
                $subject->save();
            }
        }

        $content = new EducationalContent();
        $content->subject_id   = $request->subject_id;
        $content->title        = $request->title;
        $content->type         = $request->type ?? 'video';
        $content->channel_name = $request->channel_name ?? 'عام';
        $content->file_size    = $request->file_size ?? 'غير محدد';
        $content->order        = $request->order;

        // 2. معالجة الفيديو (الرفع السحابي عبر Cloudinary بالطريقة المضمونة)
        if ($request->hasFile('file_upload_video') && $request->file('file_upload_video')->isValid()) {
            $uploadedFile = Cloudinary::upload($request->file('file_upload_video')->getRealPath(), [
                'resource_type' => 'video'
            ]);
            $content->url_path = $uploadedFile->getSecurePath();
        } elseif ($request->filled('video_url')) {
            $content->url_path = $request->video_url;
        }

        // 3. معالجة الـ PDF (يمكنك إبقاؤها محلياً أو رفعها أيضاً، سنتركها محلياً كما هي لتجنب التعقيد)
        if ($request->hasFile('file_upload_pdf') && $request->file('file_upload_pdf')->isValid()) {
            $content->pdf_path = $request->file('file_upload_pdf')->store('educational/pdfs', 'public');
        } elseif ($request->filled('pdf_url')) {
            $content->pdf_path = $request->pdf_url;
        }

        if (empty($content->url_path) && empty($content->pdf_path)) {
            return response()->json([
                'icon'  => 'error',
                'title' => 'فشلت عملية رفع الملف! تحقق من اختيار ملف وأن حجمه لا يتجاوز الحد المسموح.'
            ], 422);
        }

        $content->save();

        return response()->json([
            'icon'  => 'success',
            'title' => 'تم حفظ الدرس والمرفقات بنجاح على السحابة 🎉'
        ], 200);
    }

    public function show($id)
    {
        $content = EducationalContent::with('subject.stage')->findOrFail($id);
        return view('educational_contents.show', compact('content'));
    }

    public function edit($id)
    {
        $content = EducationalContent::findOrFail($id);
        $stages = Stage::with('subjects')->get();
        return view('educational_contents.edit', compact('content', 'stages'));
    }

    public function update(Request $request, $id)
    {
        $attributes = [
            'subject_id'   => 'المادة',
            'title'        => 'العنوان',
            'type'         => 'النوع',
            'channel_name' => 'اسم القناة',
            'file_size'    => 'حجم الملف',
            'order'        => 'الترتيب',
        ];

        $messages = [
            'required' => 'حقل :attribute مطلوب.',
            'numeric'  => 'يجب أن يكون حقل :attribute رقماً.',
            'min'      => 'حقل :attribute يجب أن يكون على الأقل :min حروف.',
        ];

        $validator = validator($request->all(), [
            'subject_id'        => 'required',
            'title'             => 'required|string|min:3',
            'order'             => 'required|numeric',
            'file_upload_video' => 'nullable|file|mimes:mp4,mov,ogg,qt,webm|max:204800',
            'file_upload_pdf'   => 'nullable|file|mimes:pdf,doc,docx|max:50120',
        ], $messages, $attributes);

        if ($validator->fails()) {
            return response()->json([
                'icon'  => 'error',
                'title' => $validator->errors()->first(),
            ], 400);
        }

        if (auth()->check() && auth()->user()->role === 'teacher') {
            $subject = Subject::find($request->subject_id);
            if ($subject && is_null($subject->user_id)) {
                $subject->user_id = auth()->id();
                $subject->save();
            }
        }

        $content = EducationalContent::findOrFail($id);

        $content->subject_id   = $request->subject_id;
        $content->title        = $request->title;
        $content->type         = $request->type ?? $content->type;
        $content->channel_name = $request->channel_name ?? $content->channel_name;
        $content->file_size    = $request->file_size ?? $content->file_size;
        $content->order        = $request->order;

        // --- تحديث مرفق الفيديو (عبر Cloudinary) بالطريقة المضمونة ---
        if ($request->hasFile('file_upload_video') && $request->file('file_upload_video')->isValid()) {
            $uploadedFile = Cloudinary::upload($request->file('file_upload_video')->getRealPath(), [
                'resource_type' => 'video'
            ]);
            $content->url_path = $uploadedFile->getSecurePath();
        } elseif ($request->filled('video_url')) {
            $content->url_path = $request->video_url;
        }

        // --- تحديث مرفق الـ PDF ---
        if ($request->hasFile('file_upload_pdf') && $request->file('file_upload_pdf')->isValid()) {
            if ($content->pdf_path && Storage::disk('public')->exists($content->pdf_path)) {
                Storage::disk('public')->delete($content->pdf_path);
            }
            $content->pdf_path = $request->file('file_upload_pdf')->store('educational/pdfs', 'public');
        } elseif ($request->filled('pdf_url')) {
            $content->pdf_path = $request->pdf_url;
        }

        $isSaved = $content->save();

        if ($isSaved) {
            return response()->json([
                'icon'  => 'success',
                'title' => 'تم تحديث البيانات بنجاح 🚀'
            ], 200);
        }

        return response()->json([
            'icon'  => 'error',
            'title' => 'فشلت عملية الحفظ!'
        ], 500);
    }

    public function destroy($id)
    {
        $content = EducationalContent::find($id);

        if ($content) {
            // حذف ملف الـ PDF القديم إن وجد محلياً
            if ($content->pdf_path && Storage::disk('public')->exists($content->pdf_path)) {
                Storage::disk('public')->delete($content->pdf_path);
            }

            $deleted = $content->delete();
            return response()->json(['success' => $deleted, 'message' => 'تم الحذف بنجاح']);
        }

        return response()->json(['success' => false, 'message' => 'العنصر غير موجود'], 404);
    }
}
