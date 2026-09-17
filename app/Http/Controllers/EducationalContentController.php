<?php

namespace App\Http\Controllers;

use App\Models\EducationalContent;
use App\Models\Stage;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        if ($request->hasFile('video_file')) {
            return response()->json([
                'icon'  => 'warning',
                'title' => 'تم حظر رفع ملفات الفيديو المباشرة! يرجى إدخال رابط YouTube فقط لضمان سرعة المشاهدة واستقرارها.'
            ], 422);
        }

        $validator = validator($request->all(), [
            'subject_id'      => 'required',
            'title'           => 'required|string|min:3',
            'order'           => 'required|numeric',
            'video_url'       => 'nullable|url',
            'file_upload_pdf' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,webp,zip,rar,txt|max:102400',
            'pdf_url'         => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'icon'  => 'error',
                'title' => $validator->errors()->first(),
            ], 400);
        }

        $content = new EducationalContent();
        $content->subject_id   = $request->subject_id;
        $content->title        = $request->title;
        $content->type         = $request->type ?? 'video';
        $content->channel_name = $request->channel_name ?? 'منارة التوجيهي';
        $content->file_size    = $request->file_size ?? 'غير محدد';
        $content->order        = $request->order;

        // التحقق من صحة رابط اليوتيوب إذا وُجد
        if ($request->filled('video_url')) {
            $dummy = new EducationalContent(['url_path' => $request->video_url]);
            if (!$dummy->youtube_id) {
                return response()->json([
                    'icon'  => 'error',
                    'title' => 'يرجى إدخال رابط YouTube صحيح ومباشر (watch, embed, youtu.be, shorts)'
                ], 422);
            }
            $content->url_path = $request->video_url;
        }

        // --- التخزين مع دعم كافة الامتدادات مع الاحتياطي المحلي ---
        if ($request->hasFile('file_upload_pdf') && $request->file('file_upload_pdf')->isValid()) {
            $uploaded = $request->file('file_upload_pdf');
            $sizeKb = round($uploaded->getSize() / 1024);
            $content->file_size = $sizeKb > 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';

            try {
                $path = $uploaded->store('educational/files', 'supabase');
                $content->pdf_path = Storage::disk('supabase')->url($path);
            } catch (\Throwable $e) {
                $path = $uploaded->store('educational/files', 'public');
                $content->pdf_path = asset('storage/' . $path);
            }
        } elseif ($request->filled('pdf_url')) {
            $content->pdf_path = $request->pdf_url;
        }

        if (empty($content->url_path) && empty($content->pdf_path)) {
            return response()->json([
                'icon'  => 'error',
                'title' => 'يرجى إدخال رابط فيديو YouTube أو إرفاق ملف دراسي واحد على الأقل!'
            ], 422);
        }

        if (auth()->check()) {
            $subject = Subject::find($request->subject_id);
            if ($subject && is_null($subject->user_id)) {
                $subject->user_id = auth()->id();
                $subject->save();
            }
        }

        $content->save();

        try {
            $subject = \App\Models\Subject::find($content->subject_id);
            if ($subject && $subject->stage_id) {
                \App\Services\NotificationService::notifyStageStudents(
                    $subject->stage_id,
                    'درس ومصدر تعليمي جديد 📚',
                    "أُضيف درس جديد: \"{$content->title}\" في مبحث {$subject->name_ar}.",
                    'content',
                    route('student.subjects.show', $subject->id),
                    'fa-video'
                );
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'icon'  => 'success',
            'title' => 'تم حفظ الدرس والمرفقات بنجاح 🎉'
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
        if ($request->hasFile('video_file')) {
            return response()->json([
                'icon'  => 'warning',
                'title' => 'تم حظر رفع ملفات الفيديو المباشرة! يرجى إدخال رابط YouTube فقط.'
            ], 422);
        }

        $attributes = [
            'subject_id'   => 'المادة',
            'title'        => 'العنوان',
            'type'         => 'النوع',
            'channel_name' => 'اسم القناة',
            'file_size'    => 'حجم الملف',
            'order'        => 'الترتيب',
        ];

        $validator = validator($request->all(), [
            'subject_id'      => 'required',
            'title'           => 'required|string|min:3',
            'order'           => 'required|numeric',
            'video_url'       => 'nullable|url',
            'file_upload_pdf' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,webp,zip,rar,txt|max:102400',
            'pdf_url'         => 'nullable|url',
        ], [], $attributes);

        if ($validator->fails()) {
            return response()->json([
                'icon'  => 'error',
                'title' => $validator->errors()->first(),
            ], 400);
        }

        $content = EducationalContent::findOrFail($id);
        $content->subject_id   = $request->subject_id;
        $content->title        = $request->title;
        $content->type         = $request->type ?? $content->type;
        $content->channel_name = $request->channel_name ?? $content->channel_name;
        $content->order        = $request->order;

        if ($request->filled('video_url')) {
            $dummy = new EducationalContent(['url_path' => $request->video_url]);
            if (!$dummy->youtube_id) {
                return response()->json([
                    'icon'  => 'error',
                    'title' => 'يرجى إدخال رابط YouTube صحيح ومباشر (watch, embed, youtu.be, shorts)'
                ], 422);
            }
            $content->url_path = $request->video_url;
        }

        // --- تحديث الملف وحذف القديم من التخزين السحابي ---
        if ($request->hasFile('file_upload_pdf') && $request->file('file_upload_pdf')->isValid()) {
            if ($content->pdf_path) {
                $parsedPath = str_replace(rtrim(config('filesystems.disks.supabase.url'), '/') . '/', '', $content->pdf_path);
                if (Storage::disk('supabase')->exists($parsedPath)) {
                    Storage::disk('supabase')->delete($parsedPath);
                }
            }
            $uploaded = $request->file('file_upload_pdf');
            $sizeKb = round($uploaded->getSize() / 1024);
            $content->file_size = $sizeKb > 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';

            $path = $uploaded->store('educational/files', 'supabase');
            $content->pdf_path = Storage::disk('supabase')->url($path);
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
            if ($content->pdf_path) {
                $parsedPath = str_replace(rtrim(config('filesystems.disks.supabase.url'), '/') . '/', '', $content->pdf_path);
                if (Storage::disk('supabase')->exists($parsedPath)) {
                    Storage::disk('supabase')->delete($parsedPath);
                }
            }

            $deleted = $content->delete();
            return response()->json(['success' => $deleted, 'message' => 'تم الحذف بنجاح']);
        }

        return response()->json(['success' => false, 'message' => 'العنصر غير موجود'], 404);
    }

    public function downloadFile($id)
    {
        $content = EducationalContent::with('subject')->findOrFail($id);

        if (empty($content->pdf_path)) {
            return back()->with('error', 'لا يوجد ملف مرفق لهذا الدرس.');
        }

        $ext = $content->file_extension ?? 'pdf';
        $cleanTitle = preg_replace('/[^\p{Arabic}\p{L}\p{N}\-_]/u', '_', $content->title ?? 'ملف_تعليمي');
        $cleanTitle = trim(preg_replace('/_+/', '_', $cleanTitle), '_');
        $fileName = ($cleanTitle ?: 'ملف_تعليمي') . '.' . $ext;

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip'  => 'application/zip',
            'rar'  => 'application/x-rar-compressed',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'txt'  => 'text/plain',
        ];
        $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

        // 1. إذا كان الملف مخزناً محلياً
        if (Storage::disk('public')->exists($content->pdf_path)) {
            return Storage::disk('public')->download($content->pdf_path, $fileName, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . rawurlencode($fileName) . '"',
            ]);
        }

        // 2. إذا كان الملف على Supabase
        if (str_contains($content->pdf_path, 'storage.supabase.co')) {
            $parsedPath = preg_replace('#^.*?/educational/#', 'educational/', $content->pdf_path);
            if (Storage::disk('supabase')->exists($parsedPath)) {
                return Storage::disk('supabase')->download($parsedPath, $fileName, [
                    'Content-Type' => $contentType,
                    'Content-Disposition' => 'attachment; filename="' . rawurlencode($fileName) . '"',
                ]);
            }
        }

        // 3. مسار رابط عام أو مباشر (تدفق آمن يفرض التحميل)
        return response()->streamDownload(function () use ($content) {
            $opts = [
                'http' => [
                    'method' => 'GET', 
                    'header' => "User-Agent: TawjihiPlatform/1.0\r\n",
                    'follow_location' => 1,
                    'timeout' => 60
                ]
            ];
            $context = stream_context_create($opts);
            $stream = @fopen($content->pdf_path, 'rb', false, $context);
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, $fileName, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . rawurlencode($fileName) . '"',
        ]);
    }

    /**
     * واجهة رفع وإدارة الفيديوهات للمعلم
     */
    public function teacherVideos(Request $request)
    {
        $user = auth()->user();
        $subjectId = $user->subject_id;
        $subjects = Subject::orderBy('name_ar')->get();

        $query = EducationalContent::with('subject.stage')
            ->where(function($q) {
                $q->whereNotNull('url_path')->where('url_path', '!=', '')
                  ->orWhere('type', 'video');
            });

        if ($user->role === 'teacher' && $subjectId) {
            $query->where('subject_id', $subjectId);
        } elseif ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $videos = $query->orderBy('order')->latest()->paginate(20);

        return view('teacher.videos.index', compact('videos', 'subjects', 'subjectId'));
    }

    /**
     * واجهة رفع وإدارة الملفات والملازم للمعلم
     */
    public function teacherFiles(Request $request)
    {
        $user = auth()->user();
        $subjectId = $user->subject_id;
        $subjects = Subject::orderBy('name_ar')->get();

        $query = EducationalContent::with('subject.stage')
            ->where(function($q) {
                $q->whereNotNull('pdf_path')->where('pdf_path', '!=', '')
                  ->orWhere('type', 'pdf');
            });

        if ($user->role === 'teacher' && $subjectId) {
            $query->where('subject_id', $subjectId);
        } elseif ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $files = $query->orderBy('order')->latest()->paginate(20);

        return view('teacher.files.index', compact('files', 'subjects', 'subjectId'));
    }

    /**
     * واجهة التحكم بظهور وإخفاء المحتوى عن الطلبة
     */
    public function teacherVisibility(Request $request)
    {
        $user = auth()->user();
        $subjectId = $user->subject_id;
        $subjects = Subject::orderBy('name_ar')->get();

        $query = EducationalContent::with('subject.stage');

        if ($user->role === 'teacher' && $subjectId) {
            $query->where('subject_id', $subjectId);
        } elseif ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $contents = $query->orderBy('order')->latest()->paginate(25);

        return view('teacher.visibility.index', compact('contents', 'subjects', 'subjectId'));
    }

    /**
     * تبديل حالة ظهور المحتوى بلمسة واحدة
     */
    public function toggleVisibility($id)
    {
        $content = EducationalContent::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'teacher' && $user->subject_id && $content->subject_id != $user->subject_id) {
            return response()->json(['success' => false, 'error' => 'غير مصرح لك بتعديل هذا المحتوى'], 403);
        }

        $newVisible = $content->is_visible ? 0 : 1;
        $content->update(['is_visible' => $newVisible]);

        return response()->json([
            'success'    => true,
            'is_visible' => $newVisible,
            'message'    => $newVisible ? 'تم إظهار المحتوى وإتاحته للطلبة 🟢' : 'تم إخفاء وقفل المحتوى عن الطلبة 🔒'
        ]);
    }
}
