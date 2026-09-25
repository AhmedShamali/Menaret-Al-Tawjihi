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
        $validator = validator($request->all(), [
            'subject_id'      => 'required',
            'title'           => 'required|string|min:3',
            'order'           => 'required|numeric',
            'video_url'       => 'nullable|url',
            'video_file'      => 'nullable|file|mimes:mp4,webm,ogg,mov,m4v,mkv|max:512000',
            'file_upload_pdf' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,webp,zip,rar,txt|max:102400',
            'pdf_url'         => 'nullable|url',
        ], [
            'title.required'      => 'يرجى إدخال عنوان الدرس أو المحتوى التعليمي.',
            'subject_id.required' => 'يرجى تحديد المادة الدراسية.',
            'video_file.mimes'    => 'صيغ ملفات الفيديو المعتمدة هي: MP4, WebM, MOV, OGG, M4V.',
            'video_file.max'      => 'الحد الأقصى لحجم الفيديو هو 500 ميغابايت.',
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
        $content->channel_name = $request->channel_name ?? 'منارة التوجيهي';
        $content->file_size    = $request->file_size ?? 'غير محدد';
        $content->order        = $request->order;
        $content->is_visible   = true;

        // معالجة ملف الفيديو المباشر المرفوع على المنصة
        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $uploadedVideo = $request->file('video_file');
            $sizeMb = round($uploadedVideo->getSize() / (1024 * 1024), 1);
            $content->file_size = $sizeMb > 0 ? $sizeMb . ' MB' : round($uploadedVideo->getSize() / 1024) . ' KB';
            $path = $uploadedVideo->store('educational/videos', 'public');
            $content->url_path = $path;
        } elseif ($request->filled('video_url')) {
            $dummy = new EducationalContent(['url_path' => $request->video_url]);
            $content->url_path = $request->video_url;
        }

        // --- التخزين مع دعم كافة الامتدادات مع الاحتياطي المحلي والتوافقية مع بيئة الاختبار ---
        if ($request->hasFile('file_upload_pdf') && $request->file('file_upload_pdf')->isValid()) {
            $uploaded = $request->file('file_upload_pdf');
            $sizeKb = round($uploaded->getSize() / 1024);
            $content->file_size = $sizeKb > 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';

            if (app()->environment('testing') || empty(config('filesystems.disks.supabase.key'))) {
                $path = $uploaded->store('educational/files', 'public');
                $content->pdf_path = asset('storage/' . $path);
            } else {
                try {
                    $path = $uploaded->store('educational/files', 'supabase');
                    $content->pdf_path = Storage::disk('supabase')->url($path);
                } catch (\Throwable $e) {
                    $path = $uploaded->store('educational/files', 'public');
                    $content->pdf_path = asset('storage/' . $path);
                }
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

        // تحديد نوع المحتوى تلقائياً وبدقة
        if ($request->filled('type')) {
            $content->type = $request->type;
        } else {
            if (!empty($content->url_path) && !empty($content->pdf_path)) {
                $content->type = 'both';
            } elseif (!empty($content->pdf_path)) {
                $content->type = 'file';
            } else {
                $content->type = 'video';
            }
        }

        if (auth()->check()) {
            $subject = Subject::find($request->subject_id);
            if ($subject && is_null($subject->user_id)) {
                $subject->user_id = auth()->id();
                $subject->save();
            }
        }

        // حفظ المحتوى مع حماية تلقائية وشاملة ضد قيود قواعد البيانات القديمة
        try {
            $content->save();
        } catch (\Illuminate\Database\QueryException $e) {
            $err = strtolower($e->getMessage());
            if (str_contains($err, 'url_path') && (str_contains($err, 'not null') || str_contains($err, 'violates not-null'))) {
                $content->url_path = '';
                $content->save();
            } elseif (str_contains($err, 'check constraint') || $e->getCode() == '23514') {
                $content->type = !empty($content->url_path) ? 'video' : 'file';
                $content->save();
            } else {
                \Log::error('EducationalContent save query exception: ' . $e->getMessage());
                return response()->json([
                    'icon'  => 'error',
                    'title' => 'تعذر حفظ المحتوى التعليمي، يرجى التحقق من صحة البيانات والمحاولة مجدداً.'
                ], 500);
            }
        } catch (\Throwable $e) {
            \Log::error('EducationalContent save exception: ' . $e->getMessage());
            return response()->json([
                'icon'  => 'error',
                'title' => 'حدث خطأ أثناء معالجة المحتوى التعليمي.'
            ], 500);
        }

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
            'video_file'      => 'nullable|file|mimes:mp4,webm,ogg,mov,m4v,mkv|max:512000',
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

        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $uploadedVideo = $request->file('video_file');
            $sizeMb = round($uploadedVideo->getSize() / (1024 * 1024), 1);
            $content->file_size = $sizeMb > 0 ? $sizeMb . ' MB' : round($uploadedVideo->getSize() / 1024) . ' KB';
            $path = $uploadedVideo->store('educational/videos', 'public');
            $content->url_path = $path;
        } elseif ($request->filled('video_url')) {
            $content->url_path = $request->video_url;
        }

        // --- تحديث الملف وحذف القديم بأمان مع دعم السحابة والتخزين المحلي ---
        if ($request->hasFile('file_upload_pdf') && $request->file('file_upload_pdf')->isValid()) {
            if ($content->pdf_path) {
                try {
                    if (!empty(config('filesystems.disks.supabase.key')) && !empty(config('filesystems.disks.supabase.url'))) {
                        $parsedPath = str_replace(rtrim(config('filesystems.disks.supabase.url'), '/') . '/', '', $content->pdf_path);
                        if (Storage::disk('supabase')->exists($parsedPath)) {
                            Storage::disk('supabase')->delete($parsedPath);
                        }
                    }
                } catch (\Throwable $e) {}
            }
            $uploaded = $request->file('file_upload_pdf');
            $sizeKb = round($uploaded->getSize() / 1024);
            $content->file_size = $sizeKb > 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';

            if (app()->environment('testing') || empty(config('filesystems.disks.supabase.key'))) {
                $path = $uploaded->store('educational/files', 'public');
                $content->pdf_path = asset('storage/' . $path);
            } else {
                try {
                    $path = $uploaded->store('educational/files', 'supabase');
                    $content->pdf_path = Storage::disk('supabase')->url($path);
                } catch (\Throwable $e) {
                    $path = $uploaded->store('educational/files', 'public');
                    $content->pdf_path = asset('storage/' . $path);
                }
            }
        } elseif ($request->filled('pdf_url')) {
            $content->pdf_path = $request->pdf_url;
        }

        // تحديد نوع المحتوى تلقائياً
        if ($request->filled('type')) {
            $content->type = $request->type;
        } else {
            if (!empty($content->url_path) && !empty($content->pdf_path)) {
                $content->type = 'both';
            } elseif (!empty($content->pdf_path)) {
                $content->type = 'file';
            } else {
                $content->type = 'video';
            }
        }

        try {
            $content->save();
        } catch (\Illuminate\Database\QueryException $e) {
            $err = strtolower($e->getMessage());
            if (str_contains($err, 'url_path') && (str_contains($err, 'not null') || str_contains($err, 'violates not-null'))) {
                $content->url_path = '';
                $content->save();
            } elseif (str_contains($err, 'check constraint') || $e->getCode() == '23514') {
                $content->type = !empty($content->url_path) ? 'video' : 'file';
                $content->save();
            } else {
                \Log::error('EducationalContent update query error: ' . $e->getMessage());
                return response()->json([
                    'icon'  => 'error',
                    'title' => 'تعذر تحديث المحتوى التعليمي. يرجى مراجعة البيانات والمحاولة مجدداً.'
                ], 500);
            }
        } catch (\Throwable $e) {
            \Log::error('EducationalContent update error: ' . $e->getMessage());
            return response()->json([
                'icon'  => 'error',
                'title' => 'حدث خطأ أثناء حفظ التعديلات.'
            ], 500);
        }

        return response()->json([
            'icon'  => 'success',
            'title' => 'تم تحديث البيانات بنجاح 🚀'
        ], 200);
    }

    public function destroy($id)
    {
        $content = EducationalContent::find($id);

        if ($content) {
            $user = auth()->user();
            if ($user && $user->role !== 'admin' && !empty($user->subject_id)) {
                if ((int)$content->subject_id !== (int)$user->subject_id) {
                    return response()->json(['success' => false, 'message' => 'غير مصرح لك بحذف هذا المحتوى.'], 403);
                }
            }
            if ($content->pdf_path) {
                try {
                    if (!empty(config('filesystems.disks.supabase.key')) && !empty(config('filesystems.disks.supabase.url'))) {
                        $parsedPath = str_replace(rtrim(config('filesystems.disks.supabase.url'), '/') . '/', '', $content->pdf_path);
                        if (Storage::disk('supabase')->exists($parsedPath)) {
                            Storage::disk('supabase')->delete($parsedPath);
                        }
                    }
                } catch (\Throwable $e) {}

                if (str_contains($content->pdf_path, 'storage/educational/files/')) {
                    $localRel = 'educational/files/' . basename($content->pdf_path);
                    try {
                        if (Storage::disk('public')->exists($localRel)) {
                            Storage::disk('public')->delete($localRel);
                        }
                    } catch (\Throwable $e) {}
                }
            }

            if ($content->url_path && str_starts_with($content->url_path, 'educational/videos/')) {
                try {
                    if (Storage::disk('public')->exists($content->url_path)) {
                        Storage::disk('public')->delete($content->url_path);
                    }
                } catch (\Throwable $e) {}
            }

            $deleted = $content->delete();
            return response()->json(['success' => $deleted, 'message' => 'تم الحذف بنجاح']);
        }

        return response()->json(['success' => false, 'message' => 'العنصر غير موجود'], 404);
    }

    /**
     * تنزيل ملف الفيديو المرفوع على المنصة مباشرة
     */
    public function downloadVideo($id)
    {
        $content = EducationalContent::with('subject')->findOrFail($id);

        if (empty($content->url_path)) {
            return back()->with('error', 'لا يوجد ملف فيديو مخصص لهذا الدرس.');
        }

        $rawUrl = $content->url_path;
        $isDirect = (bool) preg_match('/\.(mp4|webm|ogg|mov|m4v)($|\?)/i', $rawUrl) || str_contains($rawUrl, 'educational/videos');

        if ($isDirect) {
            $relativePath = null;
            if (preg_match('~educational/videos/[^\s?#]+~', $rawUrl, $m)) {
                $relativePath = $m[0];
            } elseif (!filter_var($rawUrl, FILTER_VALIDATE_URL)) {
                $relativePath = ltrim($rawUrl, '/');
            }

            $cleanTitle = preg_replace('/[^\p{Arabic}\p{L}\p{N}\-_]/u', '_', $content->title ?? 'درس_فيديو');
            $fileName = ($cleanTitle ?: 'درس_فيديو') . '.mp4';

            if ($relativePath && Storage::disk('public')->exists($relativePath)) {
                return Storage::disk('public')->download($relativePath, $fileName);
            }

            if (filter_var($rawUrl, FILTER_VALIDATE_URL)) {
                return redirect()->away($rawUrl);
            }
        }

        // إذا كان رابط يوتيوب يتم إبقاؤه داخل المنصة وتنبيه الطالب بأن المشاهدة والتفاعل متاحين حصرياً على المنصة
        return redirect()->back()->with('info', 'هذا الشرح المرئي متاح للمشاهدة الآمنة وتدوين الملاحظات التفاعلية وحفظها حصرياً داخل المنصة.');
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

        // استخراج المسار النسبي للملف داخل نظام التخزين (مثل educational/files/xxxx.pdf)
        $relativePath = null;
        if (preg_match('~educational/(?:files|pdfs)/[^\s?#]+~', $content->pdf_path, $m)) {
            $relativePath = $m[0];
        } elseif (!filter_var($content->pdf_path, FILTER_VALIDATE_URL)) {
            $relativePath = ltrim($content->pdf_path, '/');
        }

        // 1. التنزيل من قرص Supabase السحابي المعتمد
        if (!empty(config('filesystems.disks.supabase.key')) && $relativePath) {
            try {
                if (Storage::disk('supabase')->exists($relativePath)) {
                    return Storage::disk('supabase')->download($relativePath, $fileName, [
                        'Content-Type' => $contentType,
                        'Content-Disposition' => 'attachment; filename="' . rawurlencode($fileName) . '"',
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning("Supabase storage download check failed: " . $e->getMessage());
            }
        }

        // 2. التنزيل من القرص المحلي العام (public)
        if ($relativePath && Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->download($relativePath, $fileName, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . rawurlencode($fileName) . '"',
            ]);
        }

        // 3. التحقق من وجود الملف في مسار التخزين الفعلي على القرص
        if ($relativePath && file_exists(storage_path('app/public/' . $relativePath))) {
            return response()->download(storage_path('app/public/' . $relativePath), $fileName, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . rawurlencode($fileName) . '"',
            ]);
        }

        // 4. إذا كان الملف يحمل رابط Supabase عام، جلب محتواه المباشر
        if (str_contains($content->pdf_path, 'supabase.co') && $relativePath) {
            try {
                $bucket = config('filesystems.disks.supabase.bucket', 'educational');
                $host = parse_url($content->pdf_path, PHP_URL_HOST);
                $publicUrl = "https://{$host}/storage/v1/object/public/{$bucket}/{$relativePath}";
                $resp = \Illuminate\Support\Facades\Http::timeout(20)->withoutVerifying()->get($publicUrl);
                if ($resp->successful() && strlen($resp->body()) > 20) {
                    return response($resp->body(), 200, [
                        'Content-Type' => $contentType,
                        'Content-Disposition' => 'attachment; filename="' . rawurlencode($fileName) . '"',
                    ]);
                }
            } catch (\Throwable $e) {}
        }

        // 5. إذا كان الرابط URL خارجي مباشر، جلب المحتوى الآمن والتأكد من عدم كونه فارغاً
        if (filter_var($content->pdf_path, FILTER_VALIDATE_URL)) {
            try {
                $resp = \Illuminate\Support\Facades\Http::timeout(20)->withoutVerifying()->get($content->pdf_path);
                if ($resp->successful() && strlen($resp->body()) > 20) {
                    return response($resp->body(), 200, [
                        'Content-Type' => $contentType,
                        'Content-Disposition' => 'attachment; filename="' . rawurlencode($fileName) . '"',
                    ]);
                }
            } catch (\Throwable $e) {}

            // تحويل مباشر للمتصفح إذا تعذر الجلب الخادمي
            return redirect()->away($content->pdf_path);
        }

        // 6. في حال لم يتوفر الملف نهائياً: تنبيه واضح ومنع إرجاع ملف تالف بحجم 0 بايت
        return back()->with('error', 'عذراً، لم يتم العثور على الملف المطلوب على الخادم، يرجى مراجعة المعلم أو إدارة المنصة.');
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
                  ->orWhereIn('type', ['video', 'both']);
            });

        if ($user->role === 'teacher' && $subjectId) {
            $query->where('subject_id', $subjectId);
        } elseif ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $stats = [
            'total'   => (clone $query)->count(),
            'visible' => (clone $query)->where('is_visible', 1)->count(),
            'hidden'  => (clone $query)->where('is_visible', 0)->count(),
        ];

        $videos = $query->orderBy('order')->latest()->paginate(20);

        return view('teacher.videos.index', compact('videos', 'subjects', 'subjectId', 'stats'));
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
                  ->orWhereIn('type', ['file', 'pdf', 'both']);
            });

        if ($user->role === 'teacher' && $subjectId) {
            $query->where('subject_id', $subjectId);
        } elseif ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $stats = [
            'total'   => (clone $query)->count(),
            'visible' => (clone $query)->where('is_visible', 1)->count(),
            'hidden'  => (clone $query)->where('is_visible', 0)->count(),
        ];

        $files = $query->orderBy('order')->latest()->paginate(20);

        return view('teacher.files.index', compact('files', 'subjects', 'subjectId', 'stats'));
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
