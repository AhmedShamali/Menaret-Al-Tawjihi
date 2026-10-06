<?php

namespace App\Http\Controllers;

use App\Models\EducationalContent;
use App\Models\Stage;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\OfflineVideoManager;

class EducationalContentController extends Controller
{
    /**
     * استخراج كافة معرفات المواد المسندة للمعلم بدقة من كافة العلاقات المعتمدة
     * وتوسيعها لتشمل الفروع الأكاديمية المختلفة لنفس المادة (توزيع متعدد)
     */
    protected function getTeacherSubjectIds($user = null): array
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return [];
        }
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return Subject::pluck('id')->toArray();
        }
        $ids = [];
        if (!empty($user->subject_id)) {
            $ids[] = (int) $user->subject_id;
        }
        $fromUser = Subject::where('user_id', $user->id)->pluck('id')->toArray();
        $fromTeacher = Subject::where('teacher_id', $user->id)->pluck('id')->toArray();
        $assignedIds = array_values(array_unique(array_filter(array_merge($ids, $fromUser, $fromTeacher))));

        // إذا لم يكن مرتبطاً بمعرف مادة مباشر، البحث باسم المعلم
        if (!empty($user->name)) {
            $byName = Subject::where('teacher_name', 'like', "%{$user->name}%")->pluck('id')->toArray();
            $assignedIds = array_merge($assignedIds, $byName);
        }

        // فحص التخصص الأكاديمي للمعلم (major) ومطابقته مع أسماء المواد
        if (!empty($user->major)) {
            $cleanMajor = trim(preg_replace('/\s*\(.*?\)\s*/u', '', $user->major));
            if (!empty($cleanMajor)) {
                $normMajor = preg_replace('/[إأآا]/u', '%', $cleanMajor);
                $normMajor = preg_replace('/[ةه]/u', '%', $normMajor);
                $normMajor = preg_replace('/[ىي]/u', '%', $normMajor);
                $byMajor = Subject::where(function($q) use ($cleanMajor, $normMajor) {
                    $q->where('name_ar', 'like', "%{$cleanMajor}%")
                      ->orWhere('name_ar', 'like', "%{$normMajor}%");
                })->pluck('id')->toArray();
                $assignedIds = array_merge($assignedIds, $byMajor);
            }
        }

        // فحص أي مواد سبق للمعلم رفع محتوى فيها
        $fromUploads = EducationalContent::where('uploaded_by', $user->id)->pluck('subject_id')->toArray();
        $assignedIds = array_values(array_unique(array_filter(array_merge($assignedIds, $fromUploads))));

        // توسيع نطاق المواد ليشمل المواد المشتركة عبر كافة الفروع الأكاديمية لنفس تخصص المعلم
        // مثلاً: إذا كان المعلم يدرس اللغة الإنجليزية في العلمي، يشمل تلقائياً الإنجليزية في الأدبي والصناعي
        if (!empty($assignedIds)) {
            $baseSubjects = Subject::whereIn('id', $assignedIds)->get();
            $sisterSubjectIds = [];
            foreach ($baseSubjects as $baseSub) {
                $cleanName = trim(preg_replace('/\s*\(.*?\)\s*/u', '', $baseSub->name_ar ?? ''));
                if (!empty($cleanName)) {
                    $normClean = preg_replace('/[إأآا]/u', '%', $cleanName);
                    $normClean = preg_replace('/[ةه]/u', '%', $normClean);
                    $normClean = preg_replace('/[ىي]/u', '%', $normClean);
                    $matched = Subject::where(function($q) use ($cleanName, $normClean) {
                        $q->where('name_ar', 'like', "%{$cleanName}%")
                          ->orWhere('name_ar', 'like', "%{$normClean}%");
                    })->pluck('id')->toArray();
                    $sisterSubjectIds = array_merge($sisterSubjectIds, $matched);
                }
                if (!empty($baseSub->subject_key)) {
                    $baseKey = explode('_', $baseSub->subject_key)[0];
                    if (!empty($baseKey)) {
                        $matchedKey = Subject::where('subject_key', 'like', "{$baseKey}_%")->pluck('id')->toArray();
                        $sisterSubjectIds = array_merge($sisterSubjectIds, $matchedKey);
                    }
                }
            }
            $assignedIds = array_values(array_unique(array_filter(array_merge($assignedIds, $sisterSubjectIds))));
        }

        return $assignedIds;
    }

    /**
     * فحص هل المحتوى مصرح للطالب الحالي وفق المنطقة (غزة / الضفة)
     */
    protected function checkStudentRegionAccess(EducationalContent $content): bool
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['admin', 'super_admin', 'teacher'])) {
            return true;
        }

        $student = \App\Support\CurrentActor::student()
            ?? \Illuminate\Support\Facades\Auth::guard('student')->user()
            ?? ($user && $user->role === 'student' ? ($user->student ?? \App\Models\Student::find($user->id)) : null);

        if (!$student) {
            return true;
        }

        $contentRegion = $content->target_region ?? 'all';
        if ($contentRegion === 'all' || empty($contentRegion)) {
            return true;
        }

        $studentRegion = $student->resolved_region ?? 'west_bank';
        return $contentRegion === $studentRegion;
    }

    public function index()
    {
        $user = auth()->user();
        $query = EducationalContent::with('subject.stage')->latest();
        if ($user && $user->role === 'teacher') {
            $teacherSubjectIds = $this->getTeacherSubjectIds($user);
            if (!empty($teacherSubjectIds)) {
                $allTeacherSisterIds = \App\Services\EducationalContentSyncService::resolveAllSisterSubjectIds($teacherSubjectIds);
                $query->where(function($q) use ($allTeacherSisterIds, $user) {
                    $q->whereIn('subject_id', $allTeacherSisterIds)
                      ->orWhere('uploaded_by', $user->id);
                });
            }
        }
        $contents = $query->get();
        return view('educational_contents.index', compact('contents'));
    }

    /**
     * مطابقة معرفات المواد في الفروع المحددة بناءً على المادة الأساسية المختارة
     */
    public static function resolveMatchingSubjectIds(array $stageIds, int $primarySubjectId): array
    {
        $primarySubject = Subject::find($primarySubjectId);
        if (!$primarySubject || empty($stageIds)) {
            return $primarySubject ? [$primarySubject->id] : [];
        }

        $cleanName = trim(preg_replace('/\s*\(.*?\)\s*/u', '', $primarySubject->name_ar ?? $primarySubject->name ?? ''));
        $baseKey = !empty($primarySubject->subject_key) ? explode('_', $primarySubject->subject_key)[0] : '';

        $matchingIds = Subject::whereIn('stage_id', $stageIds)
            ->where(function($q) use ($cleanName, $baseKey, $primarySubject) {
                if (!empty($cleanName)) {
                    $q->where('name_ar', 'like', "%{$cleanName}%");
                }
                if (!empty($baseKey)) {
                    $q->orWhere('subject_key', 'like', "{$baseKey}_%");
                }
                $q->orWhere('id', $primarySubject->id);
            })
            ->pluck('id')
            ->toArray();

        return array_values(array_unique(array_merge([$primarySubject->id], $matchingIds)));
    }

    public function create($subject_id = null)
    {
        $user = auth()->user();
        $isTeacher = ($user && $user->role === 'teacher');
        $teacherSubjectIds = $isTeacher ? $this->getTeacherSubjectIds($user) : [];

        if ($isTeacher) {
            if ($subject_id && !in_array((int)$subject_id, $teacherSubjectIds)) {
                $subject_id = $teacherSubjectIds[0] ?? null;
            } elseif (!$subject_id && !empty($teacherSubjectIds)) {
                $subject_id = $teacherSubjectIds[0];
            }
            $mySubject = $subject_id ? Subject::find($subject_id) : null;
            $stages = Stage::whereHas('subjects', function($q) use ($teacherSubjectIds) {
                $q->whereIn('id', $teacherSubjectIds);
            })->with(['subjects' => function($q) use ($teacherSubjectIds) {
                $q->whereIn('id', $teacherSubjectIds);
            }])->get();
            $subjects = !empty($teacherSubjectIds)
                ? Subject::whereIn('id', $teacherSubjectIds)->orderBy('name_ar')->get()
                : collect();
        } else {
            $mySubject = $subject_id ? Subject::find($subject_id) : ($user->subject ?? null);
            $stages = Stage::with('subjects')->orderBy('grade_level')->get();
            $subjects = Subject::with('stage')->orderBy('name_ar')->get();
        }

        return view('educational_contents.create', compact('mySubject', 'stages', 'subjects'));
    }

    /**
     * رفع ملفات الفيديو الكبيرة والضخمة بالجيجابايت بنظام الأجزاء المتعددة (Chunked Upload)
     * لتجاوز كافة قيود السيرفر (PHP Limit / Cloudflare / HTTP 413) ودعم أي حجم حتى 10GB+ باستقرار وسرعة فائقة
     */
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'file_id'      => 'required|string',
            'chunk_index'  => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
            'file_name'    => 'required|string',
            'chunk'        => 'required|file',
        ]);

        $fileId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->file_id);
        $chunkIndex = (int) $request->chunk_index;
        $totalChunks = (int) $request->total_chunks;
        $ext = strtolower(pathinfo($request->file_name, PATHINFO_EXTENSION));

        $allowedExtensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v', 'mkv'];
        if (!in_array($ext, $allowedExtensions)) {
            return response()->json([
                'success' => false,
                'message' => 'صيغة الفيديو غير مدعومة. الصيغ المسموحة هي: ' . implode(', ', array_map('strtoupper', $allowedExtensions))
            ], 422);
        }

        $chunksFolder = storage_path('app/chunks/' . $fileId);
        if (!file_exists($chunksFolder)) {
            mkdir($chunksFolder, 0777, true);
        }

        $chunk = $request->file('chunk');
        $chunk->move($chunksFolder, 'part_' . $chunkIndex);

        // التحقق من اكتمال استلام كافة الأجزاء
        $allPresent = true;
        for ($i = 0; $i < $totalChunks; $i++) {
            if (!file_exists($chunksFolder . '/part_' . $i)) {
                $allPresent = false;
                break;
            }
        }

        if ($allPresent) {
            // رفع القيود الزمنية واستهلاك الذاكرة لدمج ملفات الجيجابايت بسرعة فائقة
            @ini_set('max_execution_time', '0');
            @set_time_limit(0);
            @ini_set('memory_limit', '1024M');
            if (function_exists('ignore_user_abort')) {
                @ignore_user_abort(true);
            }

            $finalFilename = 'video_' . time() . '_' . Str::random(12) . '.' . $ext;
            $relativeDir = 'educational/videos';
            $targetDir = storage_path('app/public/' . $relativeDir);
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $finalPath = $targetDir . '/' . $finalFilename;

            $output = fopen($finalPath, 'wb');
            if (!$output) {
                return response()->json(['success' => false, 'message' => 'تعذر فتح مسار دمج الفيديو على السيرفر.'], 500);
            }

            // الدمج المباشر عبر تدفقات النظام (stream_copy_to_stream) لكفاءة خارقة في ملفات الجيجابايت
            for ($i = 0; $i < $totalChunks; $i++) {
                $partPath = $chunksFolder . '/part_' . $i;
                if (file_exists($partPath)) {
                    $input = fopen($partPath, 'rb');
                    if ($input) {
                        stream_copy_to_stream($input, $output);
                        fclose($input);
                    }
                    @unlink($partPath);
                }
            }
            fflush($output);
            fclose($output);
            @rmdir($chunksFolder);

            $fileSizeBytes = file_exists($finalPath) ? filesize($finalPath) : 0;
            if ($fileSizeBytes >= 1073741824) {
                $formattedSize = round($fileSizeBytes / 1073741824, 2) . ' GB';
            } elseif ($fileSizeBytes >= 1048576) {
                $formattedSize = round($fileSizeBytes / 1048576, 1) . ' MB';
            } else {
                $formattedSize = round($fileSizeBytes / 1024) . ' KB';
            }

            $relativeStoragePath = $relativeDir . '/' . $finalFilename;

            // بالنسبة للتخزين السحابي: لا نرفع الملفات الضخمة بالجيجابايت في نفس الطلب لتفادي مهلة HTTP أو سعة سوبابيز المجانية (50MB)
            if (!app()->environment('testing') && !empty(config('filesystems.disks.supabase.key')) && $fileSizeBytes < 45 * 1024 * 1024) {
                try {
                    $supabasePath = Storage::disk('supabase')->putFileAs($relativeDir, new \Illuminate\Http\File($finalPath), $finalFilename);
                    if ($supabasePath) {
                        $relativeStoragePath = Storage::disk('supabase')->url($supabasePath);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Supabase sync skipped for video: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success'             => true,
                'done'                => true,
                'completed'           => true,
                'file_path'           => $relativeStoragePath,
                'uploaded_video_path' => $relativeStoragePath,
                'formatted_size'      => $formattedSize,
                'message'             => 'تم اكتمال رفع ودمج الفيديو بنجاح',
            ]);
        }

        return response()->json([
            'success'     => true,
            'done'        => false,
            'chunk_index' => $chunkIndex,
            'percent'     => round((($chunkIndex + 1) / $totalChunks) * 100),
        ]);
    }

    /**
     * فحص أجزاء الفيديو المرفوعة مسبقاً لدعم استئناف الرفع (Resumable Upload)
     * للملفات الضخمة بالجيجابايت في حال انقطاع النت أو إعادة فتح الصفحة
     */
    public function checkChunkStatus(Request $request)
    {
        $request->validate([
            'file_id'      => 'required|string',
            'total_chunks' => 'required|integer|min:1',
        ]);

        $fileId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->file_id);
        $totalChunks = (int) $request->total_chunks;
        $chunksFolder = storage_path('app/chunks/' . $fileId);

        if (!file_exists($chunksFolder)) {
            return response()->json([
                'exists'          => false,
                'uploaded_chunks' => [],
                'uploaded_count'  => 0,
                'total_chunks'    => $totalChunks,
            ]);
        }

        $uploadedChunks = [];
        for ($i = 0; $i < $totalChunks; $i++) {
            $partFile = $chunksFolder . '/part_' . $i;
            if (file_exists($partFile) && filesize($partFile) > 0) {
                $uploadedChunks[] = $i;
            }
        }

        return response()->json([
            'exists'          => count($uploadedChunks) > 0,
            'uploaded_chunks' => $uploadedChunks,
            'uploaded_count'  => count($uploadedChunks),
            'total_chunks'    => $totalChunks,
        ]);
    }

    public function store(Request $request)
    {
        // التحقق الأولي السريع من وجود ملف فيديو وأي خطأ مرتبط برفعه قبل التحقق العام
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            if (!$file->isValid()) {
                $errCode = $file->getError();
                $errMsg = match($errCode) {
                    UPLOAD_ERR_INI_SIZE => 'حجم ملف الفيديو يتجاوز الحد الأقصى المسموح به في إعدادات السيرفر.',
                    UPLOAD_ERR_FORM_SIZE => 'حجم ملف الفيديو يتجاوز الحجم المسموح به في النموذج.',
                    UPLOAD_ERR_PARTIAL => 'تم رفع جزء من ملف الفيديو فقط، يرجى إعادة المحاولة.',
                    UPLOAD_ERR_NO_FILE => 'لم يتم استلام ملف الفيديو، يرجى اختياره مجدداً.',
                    default => 'حدث خطأ أثناء رفع ملف الفيديو (رمز الخطأ: ' . $errCode . ').'
                };
                return response()->json([
                    'icon'  => 'error',
                    'title' => $errMsg
                ], 422);
            }

            $ext = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v', 'mkv'];
            if (!in_array($ext, $allowedExtensions)) {
                return response()->json([
                    'icon'  => 'error',
                    'title' => 'صيغ ملفات الفيديو المعتمدة هي: ' . implode(', ', array_map('strtoupper', $allowedExtensions)) . '.'
                ], 422);
            }
        }

        $validator = validator($request->all(), [
            'subject_id'          => 'nullable',
            'subject_ids'         => 'nullable',
            'stage_ids'           => 'nullable',
            'title'               => 'required|string|min:3',
            'order'               => 'required|numeric',
            'video_url'           => 'nullable|url',
            'video_file'          => 'nullable|file|max:10485760', // حتى 10 جيجابايت
            'uploaded_video_path' => 'nullable|string',
            'formatted_size'      => 'nullable|string',
            'file_upload_pdf'     => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,webp,zip,rar,txt|max:102400',
            'pdf_url'             => 'nullable|url',
            'target_region'       => 'nullable|string|in:all,gaza,west_bank',
        ], [
            'title.required'      => 'يرجى إدخال عنوان الدرس أو المحتوى التعليمي.',
            'video_file.max'      => 'الحد الأقصى لحجم الفيديو هو 10 جيجابايت.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'icon'  => 'error',
                'title' => $validator->errors()->first(),
            ], 400);
        }

        $user = auth()->user();
        $isTeacher = ($user && $user->role === 'teacher');
        $teacherSubjectIds = $isTeacher ? $this->getTeacherSubjectIds($user) : [];

        // استخراج معرفات المواد المستهدفة (دعم تعدد الفروع والمواد في وقت واحد)
        $targetSubjectIds = [];

        // 1. إذا تم إرسال مصفوفة مواد محددة
        if ($request->filled('subject_ids')) {
            $raw = $request->input('subject_ids');
            if (is_array($raw)) {
                $targetSubjectIds = array_map('intval', array_filter($raw));
            } elseif (is_string($raw)) {
                $targetSubjectIds = array_map('intval', array_filter(explode(',', $raw)));
            }
        }

        // 2. إذا تم تحديد فروع ومادة أساسية، نقوم بمطابقة المادة في كافة الفروع المختارة تلقائياً
        if ($request->filled('stage_ids') && $request->filled('subject_id')) {
            $stgIds = array_map('intval', array_filter((array)$request->input('stage_ids')));
            if (!empty($stgIds)) {
                $matched = self::resolveMatchingSubjectIds($stgIds, (int)$request->subject_id);
                $targetSubjectIds = array_values(array_unique(array_merge($targetSubjectIds, $matched)));
            }
        }

        // 3. الحالة الافتراضية التقليدية: مادة واحدة
        if (empty($targetSubjectIds) && $request->filled('subject_id')) {
            $targetSubjectIds = [(int)$request->subject_id];
        }

        if (empty($targetSubjectIds)) {
            return response()->json([
                'icon'  => 'error',
                'title' => 'يرجى تحديد المادة الدراسية أو الفروع المستهدفة لنشر المحتوى.'
            ], 422);
        }

        if ($isTeacher) {
            $targetSubjectIds = array_values(array_intersect($targetSubjectIds, $teacherSubjectIds));
            if (empty($targetSubjectIds)) {
                return response()->json([
                    'icon'  => 'error',
                    'title' => 'غير مصرح لك بنشر محتوى لهذه المادة الدراسية أو الفروع المختارة.'
                ], 403);
            }
        }

        $targetRegion = $request->input('target_region', 'all');
        if (!in_array($targetRegion, ['all', 'gaza', 'west_bank'])) {
            $targetRegion = 'all';
        }

        $urlPath = null;
        $pdfPath = null;
        $fileSize = $request->file_size ?? 'غير محدد';

        // معالجة ملف الفيديو المرفوع مسبقاً بنظام الأجزاء (Chunked Upload)
        if ($request->filled('uploaded_video_path')) {
            $urlPath = $request->uploaded_video_path;
            if ($request->filled('formatted_size')) {
                $fileSize = $request->formatted_size;
            }
        }
        // معالجة ملف الفيديو المباشر المرفوع على المنصة بالصيغة التقليدية
        elseif ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $uploadedVideo = $request->file('video_file');
            $sizeMb = round($uploadedVideo->getSize() / (1024 * 1024), 1);
            $fileSize = $sizeMb > 0 ? $sizeMb . ' MB' : round($uploadedVideo->getSize() / 1024) . ' KB';
            
            if (app()->environment('testing') || empty(config('filesystems.disks.supabase.key'))) {
                $urlPath = $uploadedVideo->store('educational/videos', 'public');
            } else {
                try {
                    $path = $uploadedVideo->store('educational/videos', 'supabase');
                    $urlPath = Storage::disk('supabase')->url($path);
                } catch (\Throwable $e) {
                    $urlPath = $uploadedVideo->store('educational/videos', 'public');
                }
            }
        } elseif ($request->filled('video_url')) {
            $urlPath = $request->video_url;
        }

        // --- التخزين مع دعم كافة الامتدادات مع الاحتياطي المحلي والتوافقية مع بيئة الاختبار ---
        if ($request->hasFile('file_upload_pdf') && $request->file('file_upload_pdf')->isValid()) {
            $uploaded = $request->file('file_upload_pdf');
            $sizeKb = round($uploaded->getSize() / 1024);
            $fileSize = $sizeKb > 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';

            if (app()->environment('testing') || empty(config('filesystems.disks.supabase.key'))) {
                $path = $uploaded->store('educational/files', 'public');
                $pdfPath = asset('storage/' . $path);
            } else {
                try {
                    $path = $uploaded->store('educational/files', 'supabase');
                    $pdfPath = Storage::disk('supabase')->url($path);
                } catch (\Throwable $e) {
                    $path = $uploaded->store('educational/files', 'public');
                    $pdfPath = asset('storage/' . $path);
                }
            }
        } elseif ($request->filled('pdf_url')) {
            $pdfPath = $request->pdf_url;
        }

        if (empty($urlPath) && empty($pdfPath)) {
            return response()->json([
                'icon'  => 'error',
                'title' => 'يرجى رفع ملف الفيديو أو إرفاق ملف دراسي واحد على الأقل!'
            ], 422);
        }

        // تحديد نوع المحتوى تلقائياً وبدقة
        if ($request->filled('type')) {
            $contentType = $request->type;
        } else {
            if (!empty($urlPath) && !empty($pdfPath)) {
                $contentType = 'both';
            } elseif (!empty($pdfPath)) {
                $contentType = 'file';
            } else {
                $contentType = 'video';
            }
        }

        $savedContents = [];

        foreach ($targetSubjectIds as $subId) {
            $subject = Subject::find($subId);
            if (!$subject) continue;

            if (auth()->check() && is_null($subject->user_id)) {
                $subject->user_id = auth()->id();
                $subject->save();
            }

            $content = new EducationalContent();
            $content->subject_id   = $subId;
            $content->title        = $request->title;
            $content->type         = $contentType;
            $content->file_size    = $fileSize;
            $content->order        = $request->order ?? 1;
            $content->is_visible   = true;
            $content->target_region = $targetRegion;
            $content->url_path     = $urlPath;
            $content->pdf_path     = $pdfPath;

            // تحديد اسم مقدم الشرح أو القناة تلقائياً
            if ($request->filled('channel_name')) {
                $content->channel_name = $request->channel_name;
            } elseif (!empty($subject->teacher_name)) {
                $content->channel_name = $subject->teacher_name;
            } elseif ($subject->teacher && !empty($subject->teacher->name)) {
                $content->channel_name = $subject->teacher->name_ar ?? $subject->teacher->name;
            } else {
                $content->channel_name = $user ? ($user->name_ar ?? $user->name) : 'إدارة المنصة';
            }

            // حفظ المحتوى مع حماية التوافقية
            try {
                $content->save();
                $savedContents[] = $content;
            } catch (\Illuminate\Database\QueryException $e) {
                $err = strtolower($e->getMessage());
                if (str_contains($err, 'target_region')) {
                    try {
                        \Illuminate\Support\Facades\DB::statement("ALTER TABLE educational_contents ADD COLUMN IF NOT EXISTS target_region VARCHAR(20) DEFAULT 'all'");
                        $content->target_region = $targetRegion;
                        $content->save();
                        $savedContents[] = $content;
                    } catch (\Throwable $th) {
                        unset($content->target_region);
                        $content->save();
                        $savedContents[] = $content;
                    }
                } elseif (str_contains($err, 'url_path') && (str_contains($err, 'not null') || str_contains($err, 'violates not-null'))) {
                    $content->url_path = '';
                    $content->save();
                    $savedContents[] = $content;
                } else {
                    \Log::error("EducationalContent multi-save query error: " . $e->getMessage());
                }
            } catch (\Throwable $e) {
                \Log::error("EducationalContent multi-save error: " . $e->getMessage());
            }

            // نشر وتوزيع تلقائي لكافة الفروع والمواد المشتركة وفتح الوصول التلقائي للطلبة
            try {
                \App\Services\EducationalContentSyncService::distributeContentToAllBranches($content);
            } catch (\Throwable $th) {}

            // إرسال إشعارات لطلبة المرحلة
            if ($subject->stage_id) {
                try {
                    \App\Services\NotificationService::notifyStageStudents(
                        $subject->stage_id,
                        'درس ومصدر تعليمي جديد 📚',
                        "أُضيف درس جديد: \"{$content->title}\" في مبحث {$subject->name_ar}.",
                        'content',
                        route('student.subjects.show', $subject->id),
                        'fa-video'
                    );
                } catch (\Throwable $e) {}
            }
        }

        if (empty($savedContents)) {
            return response()->json([
                'icon'  => 'error',
                'title' => 'تعذر حفظ المحتوى التعليمي، يرجى المحاولة مرة أخرى.'
            ], 500);
        }

        $count = count($savedContents);
        $successMsg = $count > 1 
            ? "تم حفظ ونشر المحتوى بنجاح في {$count} فروع ومواد بالتوازي 🎉"
            : 'تم حفظ ونشر المحتوى بنجاح 🎉';

        return response()->json([
            'icon'  => 'success',
            'title' => $successMsg,
            'count' => $count
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
        $user = auth()->user();
        if ($user && $user->role === 'teacher') {
            $teacherSubjectIds = $this->getTeacherSubjectIds($user);
            if (!in_array((int)$content->subject_id, $teacherSubjectIds)) {
                abort(403, 'غير مصرح لك بتعديل هذا المحتوى');
            }
            $stages = Stage::whereHas('subjects', function($q) use ($teacherSubjectIds) {
                $q->whereIn('id', $teacherSubjectIds);
            })->with(['subjects' => function($q) use ($teacherSubjectIds) {
                $q->whereIn('id', $teacherSubjectIds);
            }])->get();
        } else {
            $stages = Stage::with('subjects')->get();
        }
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
            'subject_id'          => 'required',
            'title'               => 'required|string|min:3',
            'order'               => 'required|numeric',
            'video_url'           => 'nullable',
            'video_file'          => 'nullable|file|max:10485760',
            'uploaded_video_path' => 'nullable|string',
            'formatted_size'      => 'nullable|string',
            'file_upload_pdf'     => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,webp,zip,rar,txt|max:102400',
            'pdf_url'             => 'nullable|url',
            'target_region'       => 'nullable|string|in:all,gaza,west_bank',
        ], [], $attributes);

        if ($validator->fails()) {
            return response()->json([
                'icon'  => 'error',
                'title' => $validator->errors()->first(),
            ], 400);
        }

        $content = EducationalContent::findOrFail($id);
        $user = auth()->user();
        if ($user && $user->role === 'teacher') {
            $teacherSubjectIds = $this->getTeacherSubjectIds($user);
            if (!in_array((int)$content->subject_id, $teacherSubjectIds) || !in_array((int)$request->subject_id, $teacherSubjectIds)) {
                return response()->json([
                    'icon'  => 'error',
                    'title' => 'غير مصرح لك بتعديل هذا المحتوى أو نقله لمادة أخرى.'
                ], 403);
            }
        }
        $content->subject_id   = $request->subject_id;
        $content->title        = $request->title;
        $content->type         = $request->type ?? $content->type;
        $content->channel_name = $request->channel_name ?? $content->channel_name;
        $content->order        = $request->order;

        if ($request->filled('target_region')) {
            $targetRegion = $request->input('target_region');
            if (in_array($targetRegion, ['all', 'gaza', 'west_bank'])) {
                $content->target_region = $targetRegion;
            }
        }

        if ($request->filled('uploaded_video_path')) {
            $content->url_path = $request->uploaded_video_path;
            if ($request->filled('formatted_size')) {
                $content->file_size = $request->formatted_size;
            }
        } elseif ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
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

    public function destroy(Request $request, $id)
    {
        $content = EducationalContent::find($id);

        if ($content) {
            $user = auth()->user();
            if ($user && $user->role === 'teacher') {
                $teacherSubjectIds = $this->getTeacherSubjectIds($user);
                if (!in_array((int)$content->subject_id, $teacherSubjectIds) && $content->uploaded_by != $user->id) {
                    return response()->json(['success' => false, 'message' => 'غير مصرح لك بحذف هذا المحتوى.'], 403);
                }
            }

            $title = $content->title;
            $urlPath = $content->url_path;
            $pdfPath = $content->pdf_path;

            // حذف هذه المحاضرة وجميع النسخ الموزعة منها في الفروع الشقيقة لضمان عدم بقاء نسخ يتيمة
            $allIdsToDelete = [$content->id];
            if (!empty($urlPath)) {
                $sisterIds = EducationalContent::where('id', '!=', $content->id)
                    ->where('title', $content->title)
                    ->where('url_path', $urlPath)
                    ->pluck('id')
                    ->toArray();
                if (!empty($sisterIds)) {
                    $allIdsToDelete = array_merge($allIdsToDelete, $sisterIds);
                }
            }

            // حذف التعيينات وسجلات تقدم المشاهدة وملاحظات الفيديو
            try {
                \App\Models\ContentAssignment::whereIn('educational_content_id', $allIdsToDelete)->delete();
                \App\Models\VideoProgress::whereIn('educational_content_id', $allIdsToDelete)->delete();
                \App\Models\VideoNote::whereIn('educational_content_id', $allIdsToDelete)->delete();
            } catch (\Throwable $e) {}

            // حذف ملفات PDF المرتبطة إذا لم تكن مستخدمة في محتوى آخر
            if ($pdfPath) {
                $otherPdfUses = EducationalContent::whereNotIn('id', $allIdsToDelete)
                    ->where('pdf_path', $pdfPath)
                    ->exists();

                if (!$otherPdfUses) {
                    try {
                        if (!empty(config('filesystems.disks.supabase.key')) && !empty(config('filesystems.disks.supabase.url'))) {
                            $parsedPath = str_replace(rtrim(config('filesystems.disks.supabase.url'), '/') . '/', '', $pdfPath);
                            if (Storage::disk('supabase')->exists($parsedPath)) {
                                Storage::disk('supabase')->delete($parsedPath);
                            }
                        }
                    } catch (\Throwable $e) {}

                    if (str_contains($pdfPath, 'storage/educational/files/')) {
                        $localRel = 'educational/files/' . basename($pdfPath);
                        try {
                            if (Storage::disk('public')->exists($localRel)) {
                                Storage::disk('public')->delete($localRel);
                            }
                        } catch (\Throwable $e) {}
                    }
                }
            }

            // حذف ملفات الفيديو المحلية إذا لم تكن مستخدمة في محتوى آخر
            if ($urlPath && str_starts_with($urlPath, 'educational/videos/')) {
                $otherVideoUses = EducationalContent::whereNotIn('id', $allIdsToDelete)
                    ->where('url_path', $urlPath)
                    ->exists();

                if (!$otherVideoUses) {
                    try {
                        if (Storage::disk('public')->exists($urlPath)) {
                            Storage::disk('public')->delete($urlPath);
                        }
                    } catch (\Throwable $e) {}
                }
            }

            EducationalContent::whereIn('id', $allIdsToDelete)->delete();

            $deletedCount = count($allIdsToDelete);
            $msg = $deletedCount > 1 
                ? "تم حذف المحاضرة \"{$title}\" بنجاح من كافة الفروع الأكاديمية ({$deletedCount} فروع) ✅"
                : "تم حذف المحاضرة \"{$title}\" بنجاح ✅";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }

            return redirect()->back()->with('success', $msg);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'المحاضرة غير موجودة أو تم حذفها مسبقاً.'], 404);
        }

        return redirect()->back()->with('error', 'المحاضرة غير موجودة أو تم حذفها مسبقاً.');
    }

    /**
     * حذف وتصفير جميع المحتويات والمحاضرات على المنصة دفعة واحدة (مخصص للمدير العام فقط)
     */
    public function purgeAllContents(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'عذراً، هذا الإجراء مخصص للمدير العام فقط.'], 403);
            }
            abort(403, 'عذراً، هذا الإجراء مخصص للمدير العام فقط.');
        }

        try {
            \DB::beginTransaction();

            $totalCount = EducationalContent::count();

            // 1. تصفير تعيينات المحتوى للطلبة
            try {
                \DB::table('content_assignments')->delete();
            } catch (\Throwable $e) {}

            // 2. تصفير تقدم المشاهدة وملاحظات الفيديو
            try {
                \DB::table('video_progress')->delete();
                \DB::table('video_notes')->delete();
            } catch (\Throwable $e) {}

            // 3. حذف جميع سجلات المحتوى
            \DB::table('educational_contents')->delete();

            // 4. تنظيف ملفات الفيديو المؤقتة من التخزين إذا رغب المدير
            if ($request->boolean('delete_physical_files', false)) {
                try {
                    $videoFiles = Storage::disk('public')->files('educational/videos');
                    Storage::disk('public')->delete($videoFiles);
                } catch (\Throwable $e) {}
            }

            \DB::commit();

            $msg = "تم حذف وتصفير كافة المحاضرات والمحتويات بنجاح! تم مسح ({$totalCount}) محتوى تعليمي من المنصة بالكامل لجميع المستخدمين 🗑️";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            \DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء عملية الحذف الشامل: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    /**
     * تنزيل أو بث ملف الفيديو للدرس (حصرياً لحفظه وتشغيله داخل المنصة أوفلاين، وممنوع التنزيل كملف خارجي للطلبة)
     */
    public function downloadVideo($id)
    {
        $content = EducationalContent::with('subject')->findOrFail($id);

        if (!$this->checkStudentRegionAccess($content)) {
            return redirect()->back()->with('error', 'عذراً، هذا الدرس مخصص لمنطقة تعليمية أخرى وغير متاح في خطتك الدراسية.');
        }

        $user = auth()->user() ?? auth('student')->user();
        $isStaff = $user && in_array($user->role, ['admin', 'super_admin', 'teacher']);
        $isInternalXhr = request()->ajax() 
            || request()->wantsJson() 
            || request()->header('X-Requested-With') === 'XMLHttpRequest'
            || request()->header('Sec-Fetch-Dest') === 'empty';

        // منع تنزيل الفيديو كملف خارجي للطلبة أو عبر كتابة الرابط مباشرة بالمتصفح
        if (!$isInternalXhr && !$isStaff && !app()->runningUnitTests()) {
            $redirectRoute = \Illuminate\Support\Facades\Route::has('student.subjects.show') 
                ? route('student.subjects.show', $content->subject_id) 
                : (\Illuminate\Support\Facades\Route::has('subject.show')
                    ? route('subject.show', $content->subject_id)
                    : url('/subjects/' . $content->subject_id));
            return redirect($redirectRoute)
                ->with('info', 'حمايةً للمحتوى الأكاديمي، يتم حفظ الفيديوهات للمشاهدة بدون إنترنت حصرياً من داخل المنصة عبر زر "تحميل أوفلاين".');
        }

        $cleanTitle = preg_replace('/[^\p{Arabic}\p{L}\p{N}\-_]/u', '_', $content->title ?? 'درس_فيديو');
        $fileName = ($cleanTitle ?: 'درس_فيديو') . '.mp4';

        // 1. فحص توفر ملف MP4 محلي على الخادم (سواء كان مرفوعاً أو تم تحويله وتخزينه مسبقاً من يوتيوب)
        $localPath = OfflineVideoManager::resolveLocalMp4Path($content);
        if ($localPath && file_exists($localPath)) {
            // المعلم أو الإدارة فقط يمكنهم تنزيل الملف الأصلي خارج المنصة إذا طلبوا ذلك
            if ($isStaff && (!$isInternalXhr || request()->query('force_download') === '1')) {
                return response()->download($localPath, $fileName, [
                    'Content-Type' => 'video/mp4',
                    'Accept-Ranges' => 'bytes',
                ]);
            }

            // بث المحتوى كـ Stream للـ XHR لتخزينه في الذاكرة المحلية (IndexedDB) بدون حفظه كملف في المتصفح
            return response()->file($localPath, [
                'Content-Type' => 'video/mp4',
                'Accept-Ranges' => 'bytes',
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        // 2. إذا كان الفيديو رابط يوتيوب: محاولة التنزيل والتحويل التلقائي أوفلاين
        if (!empty($content->youtube_id)) {
            if (OfflineVideoManager::isEngineAvailable()) {
                $conversion = OfflineVideoManager::downloadAndCacheYouTube($content);
                if ($conversion['success'] && !empty($conversion['path']) && file_exists($conversion['path'])) {
                    if ($isStaff && (!$isInternalXhr || request()->query('force_download') === '1')) {
                        return response()->download($conversion['path'], $fileName, [
                            'Content-Type' => 'video/mp4',
                            'Accept-Ranges' => 'bytes',
                        ]);
                    }
                    return response()->file($conversion['path'], [
                        'Content-Type' => 'video/mp4',
                        'Accept-Ranges' => 'bytes',
                        'Access-Control-Allow-Origin' => '*',
                    ]);
                }
            }

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'is_youtube' => true,
                    'message' => 'جاري تجهيز نسخة الأوفلاين لفيديو اليوتيوب...',
                ], 422);
            }

            return redirect()->back()->with('info', 'هذا الشرح المرئي من YouTube ومتاح للمشاهدة المباشرة والأوفلاين داخل المنصة.');
        }

        // 3. فحص التخزين السحابي أو الروابط المباشرة
        $rawUrl = $content->url_path;
        $resolvedUrl = (filter_var($rawUrl, FILTER_VALIDATE_URL)) ? $rawUrl : \App\Support\MediaHelper::url($rawUrl);
        if (!empty($resolvedUrl) && filter_var($resolvedUrl, FILTER_VALIDATE_URL)) {
            if ($isStaff || $isInternalXhr) {
                return redirect()->away($resolvedUrl);
            }
            return redirect()->back()->with('info', 'هذا الشرح المرئي متاح للمشاهدة المباشرة وحفظه أوفلاين داخل المنصة.');
        }

        return redirect()->back()->with('info', 'هذا الشرح المرئي متاح للمشاهدة المباشرة داخل المنصة.');
    }

    /**
     * معالجة وتجهيز فيديو اليوتيوب للأوفلاين عبر طلب غير متزامن (AJAX)
     */
    public function prepareOfflineVideo($id)
    {
        $content = EducationalContent::with('subject')->findOrFail($id);

        if (!$this->checkStudentRegionAccess($content)) {
            return response()->json([
                'success' => false,
                'ready'   => false,
                'message' => 'عذراً، هذا الشرح مخصص لمنطقة تعليمية أخرى.'
            ], 403);
        }

        if (OfflineVideoManager::hasLocalMp4($content)) {
            return response()->json([
                'success' => true,
                'ready'   => true,
                'download_url' => route('content.downloadVideo', $content->id),
                'message' => 'الفيديو جاهز للتحميل أوفلاين فوراً ⚡',
            ]);
        }

        if (empty($content->youtube_id)) {
            return response()->json([
                'success' => false,
                'ready'   => false,
                'message' => 'هذا الدرس لا يحتوي على فيديو يوتيوب أو ملف MP4 صالح.',
            ], 400);
        }

        if (!OfflineVideoManager::isEngineAvailable()) {
            return response()->json([
                'success' => false,
                'ready'   => false,
                'engine_pending' => true,
                'message' => 'محرك تحميل اليوتيوب قيد التجهيز على الخادم. يمكنك أيضاً رفع ملف الفيديو بصيغة MP4 مباشرة في لوحة التحكم.',
            ], 503);
        }

        $result = OfflineVideoManager::downloadAndCacheYouTube($content);
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'ready'   => true,
                'download_url' => route('content.downloadVideo', $content->id),
                'message' => 'تم تجهيز وتنزيل الفيديو بنجاح! يبدأ الحفظ أوفلاين الآن ⚡',
            ]);
        }

        return response()->json([
            'success' => false,
            'ready'   => false,
            'message' => $result['message'],
        ], 500);
    }

    public function downloadFile($id)
    {
        $content = EducationalContent::with('subject')->findOrFail($id);

        if (!$this->checkStudentRegionAccess($content)) {
            return redirect()->back()->with('error', 'عذراً، هذا الملف مخصص لمنطقة تعليمية أخرى وغير متاح في خطتك الدراسية.');
        }

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
    /**
     * واجهة رفع وإدارة الفيديوهات للمعلم والمدير
     */
    public function teacherVideos(Request $request)
    {
        // مزامنة ذاتية لكافة الفيديوهات والشروحات وتوزيعها على الفروع
        try {
            \App\Services\EducationalContentSyncService::syncAllExistingVideos();
        } catch (\Throwable $e) {}

        $user = auth()->user();
        $isTeacher = ($user && $user->role === 'teacher');
        $teacherSubjectIds = $isTeacher ? $this->getTeacherSubjectIds($user) : [];

        if ($isTeacher) {
            $subjects = !empty($teacherSubjectIds)
                ? Subject::with('stage')->whereIn('id', $teacherSubjectIds)->orderBy('name_ar')->get()
                : Subject::with('stage')->orderBy('name_ar')->get();
            $stages = Stage::with('subjects')->orderBy('grade_level')->get();
        } else {
            $subjects = Subject::with('stage')->orderBy('name_ar')->get();
            $stages = Stage::with('subjects')->orderBy('grade_level')->get();
        }

        $query = EducationalContent::with('subject.stage')
            ->where(function($q) {
                $q->whereNotNull('url_path')->where('url_path', '!=', '')
                  ->orWhereIn('type', ['video', 'both']);
            });

        if ($request->filled('subject_id')) {
            $targetSubIds = \App\Services\EducationalContentSyncService::resolveAllSisterSubjectIds((int)$request->subject_id);
            $query->whereIn('subject_id', $targetSubIds);
        } elseif ($isTeacher) {
            if (!empty($teacherSubjectIds) && $request->get('show_all') !== '1') {
                $allTeacherSisterIds = \App\Services\EducationalContentSyncService::resolveAllSisterSubjectIds($teacherSubjectIds);
                $query->where(function($q) use ($allTeacherSisterIds, $user) {
                    $q->whereIn('subject_id', $allTeacherSisterIds)
                      ->orWhere('uploaded_by', $user->id)
                      ->orWhere('channel_name', 'like', "%{$user->name}%");
                });
            }
        }

        if ($request->filled('stage_id')) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('stage_id', (int)$request->stage_id);
            });
        }

        if ($request->filled('target_region') && in_array($request->target_region, ['all', 'gaza', 'west_bank'])) {
            $query->where('target_region', $request->target_region);
        }

        $stats = [
            'total'   => (clone $query)->count(),
            'visible' => (clone $query)->where('is_visible', true)->count(),
            'hidden'  => (clone $query)->where('is_visible', false)->count(),
        ];

        // عند تحديد مادة معينة يتم الترتيب حسب ترتيب الدروس، وعند العرض العام يتم إظهار أحدث الفيديوهات المرفوعة أولاً
        if ($request->filled('subject_id')) {
            $videos = $query->orderBy('order')->latest('id')->paginate(20);
        } else {
            $videos = $query->latest('id')->paginate(20);
        }
        $subjectId = $request->get('subject_id') ?: ($isTeacher && count($teacherSubjectIds) === 1 ? $teacherSubjectIds[0] : null);

        // فحص أي ملف فيديو مكتمل تم رفعه مؤخراً على السيرفر ولم يُسجل في قاعدة البيانات بعد
        $recentUnlinkedVideo = null;
        if (!$isTeacher) {
            try {
                $videoDir = storage_path('app/public/educational/videos');
                if (file_exists($videoDir)) {
                    $files = glob($videoDir . '/*.{mp4,webm,mov,mkv}', GLOB_BRACE);
                    if ($files) {
                        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
                        $linkedUrls = EducationalContent::whereNotNull('url_path')->pluck('url_path')->toArray();
                        $linkedBaseNames = array_map('basename', $linkedUrls);
                        foreach ($files as $filePath) {
                            $fBase = basename($filePath);
                            if (!in_array($fBase, $linkedBaseNames) && (time() - filemtime($filePath) < 86400)) {
                                $sizeBytes = filesize($filePath);
                                if ($sizeBytes > 1048576) {
                                    $sizeMb = round($sizeBytes / 1048576, 1);
                                    $recentUnlinkedVideo = [
                                        'path' => 'educational/videos/' . $fBase,
                                        'filename' => $fBase,
                                        'size' => $sizeMb >= 1024 ? round($sizeMb / 1024, 2) . ' GB' : $sizeMb . ' MB',
                                        'time_ago' => \Carbon\Carbon::createFromTimestamp(filemtime($filePath))->diffForHumans(),
                                    ];
                                    break;
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }

        return view('teacher.videos.index', compact('videos', 'subjects', 'subjectId', 'stats', 'stages', 'recentUnlinkedVideo'));
    }

    /**
     * واجهة رفع وإدارة الملفات والملازم للمعلم والمدير
     */
    public function teacherFiles(Request $request)
    {
        // مزامنة ذاتية وضمان ظهور كافة الملفات وتوزيعها على الفروع
        try {
            \App\Services\EducationalContentSyncService::syncAllExistingVideos();
        } catch (\Throwable $e) {}

        $user = auth()->user();
        $isTeacher = ($user && $user->role === 'teacher');
        $teacherSubjectIds = $isTeacher ? $this->getTeacherSubjectIds($user) : [];

        if ($isTeacher) {
            $subjects = !empty($teacherSubjectIds)
                ? Subject::with('stage')->whereIn('id', $teacherSubjectIds)->orderBy('name_ar')->get()
                : Subject::with('stage')->orderBy('name_ar')->get();
            $stages = Stage::with('subjects')->orderBy('grade_level')->get();
        } else {
            $subjects = Subject::with('stage')->orderBy('name_ar')->get();
            $stages = Stage::with('subjects')->orderBy('grade_level')->get();
        }

        $query = EducationalContent::with('subject.stage')
            ->where(function($q) {
                $q->whereNotNull('pdf_path')->where('pdf_path', '!=', '')
                  ->orWhereIn('type', ['file', 'pdf', 'both']);
            });

        if ($request->filled('subject_id')) {
            $targetSubIds = \App\Services\EducationalContentSyncService::resolveAllSisterSubjectIds((int)$request->subject_id);
            $query->whereIn('subject_id', $targetSubIds);
        } elseif ($isTeacher) {
            if (!empty($teacherSubjectIds) && $request->get('show_all') !== '1') {
                $allTeacherSisterIds = \App\Services\EducationalContentSyncService::resolveAllSisterSubjectIds($teacherSubjectIds);
                $query->where(function($q) use ($allTeacherSisterIds, $user) {
                    $q->whereIn('subject_id', $allTeacherSisterIds)
                      ->orWhere('uploaded_by', $user->id)
                      ->orWhere('channel_name', 'like', "%{$user->name}%");
                });
            }
        }

        if ($request->filled('stage_id')) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('stage_id', (int)$request->stage_id);
            });
        }

        if ($request->filled('target_region') && in_array($request->target_region, ['all', 'gaza', 'west_bank'])) {
            $query->where('target_region', $request->target_region);
        }

        $stats = [
            'total'   => (clone $query)->count(),
            'visible' => (clone $query)->where('is_visible', true)->count(),
            'hidden'  => (clone $query)->where('is_visible', false)->count(),
        ];

        if ($request->filled('subject_id')) {
            $files = $query->orderBy('order')->latest('id')->paginate(20);
        } else {
            $files = $query->latest('id')->paginate(20);
        }
        $subjectId = $request->get('subject_id') ?: ($isTeacher && count($teacherSubjectIds) === 1 ? $teacherSubjectIds[0] : null);

        return view('teacher.files.index', compact('files', 'subjects', 'subjectId', 'stats', 'stages'));
    }

    /**
     * واجهة التحكم بظهور وإخفاء المحتوى عن الطلبة
     */
    public function teacherVisibility(Request $request)
    {
        $user = auth()->user();
        $isTeacher = ($user && $user->role === 'teacher');
        $teacherSubjectIds = $isTeacher ? $this->getTeacherSubjectIds($user) : [];

        if ($isTeacher) {
            $subjects = !empty($teacherSubjectIds)
                ? Subject::with('stage')->whereIn('id', $teacherSubjectIds)->orderBy('name_ar')->get()
                : Subject::with('stage')->orderBy('name_ar')->get();
            $stages = Stage::with('subjects')->orderBy('grade_level')->get();
        } else {
            $subjects = Subject::with('stage')->orderBy('name_ar')->get();
            $stages = Stage::with('subjects')->orderBy('grade_level')->get();
        }

        $query = EducationalContent::with('subject.stage');

        if ($request->filled('subject_id')) {
            $targetSubIds = \App\Services\EducationalContentSyncService::resolveAllSisterSubjectIds((int)$request->subject_id);
            $query->whereIn('subject_id', $targetSubIds);
        } elseif ($isTeacher) {
            if (!empty($teacherSubjectIds) && $request->get('show_all') !== '1') {
                $allTeacherSisterIds = \App\Services\EducationalContentSyncService::resolveAllSisterSubjectIds($teacherSubjectIds);
                $query->where(function($q) use ($allTeacherSisterIds, $user) {
                    $q->whereIn('subject_id', $allTeacherSisterIds)
                      ->orWhere('uploaded_by', $user->id)
                      ->orWhere('channel_name', 'like', "%{$user->name}%");
                });
            }
        }

        if ($request->filled('stage_id')) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('stage_id', (int)$request->stage_id);
            });
        }

        if ($request->filled('subject_id')) {
            $contents = $query->orderBy('order')->latest('id')->paginate(25);
        } else {
            $contents = $query->latest('id')->paginate(25);
        }
        $subjectId = $request->get('subject_id') ?: ($isTeacher && count($teacherSubjectIds) === 1 ? $teacherSubjectIds[0] : null);

        return view('teacher.visibility.index', compact('contents', 'subjects', 'subjectId', 'stages'));
    }

    /**
     * تبديل حالة ظهور المحتوى بلمسة واحدة
     */
    public function toggleVisibility($id)
    {
        $content = EducationalContent::findOrFail($id);
        $user = auth()->user();
        if ($user && $user->role === 'teacher') {
            $teacherSubjectIds = $this->getTeacherSubjectIds($user);
            if (!in_array((int)$content->subject_id, $teacherSubjectIds)) {
                return response()->json(['success' => false, 'error' => 'غير مصرح لك بتعديل هذا المحتوى'], 403);
            }
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
