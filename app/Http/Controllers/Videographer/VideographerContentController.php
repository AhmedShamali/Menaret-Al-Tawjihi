<?php

namespace App\Http\Controllers\Videographer;

use App\Http\Controllers\Controller;
use App\Models\EducationalContent;
use App\Models\Stage;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideographerContentController extends Controller
{
    /**
     * لوحة تحكم المصور - إحصائيات الإنجاز والإنتاج
     */
    public function dashboard()
    {
        $userId = auth()->id();

        $uploadedContents = EducationalContent::where('uploaded_by', $userId)->with('subject.stage')->latest()->get();

        $stats = [
            'total_videos'      => $uploadedContents->where('type', 'video')->count(),
            'total_files'       => $uploadedContents->whereNotNull('pdf_path')->count(),
            'subjects_covered'  => $uploadedContents->pluck('subject_id')->unique()->count(),
            'total_views'       => $uploadedContents->sum('views_count'),
        ];

        $recentContents = $uploadedContents->take(10);

        return view('videographer.dashboard', compact('stats', 'recentContents'));
    }

    /**
     * مكتبة وسجل الفيديوهات المرفوعة بواسطة المصور
     */
    public function index(Request $request)
    {
        // مزامنة فورية لكافة الفيديوهات وتوزيعها
        try {
            \App\Services\EducationalContentSyncService::syncAllExistingVideos();
        } catch (\Throwable $e) {}

        $userId = auth()->id();
        $isAdmin = auth()->user()->role === 'admin';

        $query = EducationalContent::with('subject.stage', 'uploader');

        // إذا طلب المصور فقط ما رفعه بنفسه
        if ($request->get('my_only') === '1') {
            $query->where('uploaded_by', $userId);
        }

        if ($request->filled('stage_id')) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('stage_id', $request->stage_id);
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhereHas('subject', function($sq) use ($s) {
                      $sq->where('name_ar', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('visibility')) {
            if ($request->visibility === 'visible') {
                $query->where('is_visible', true);
            } elseif ($request->visibility === 'hidden') {
                $query->where(function($q) {
                    $q->where('is_visible', false)->orWhereNull('is_visible');
                });
            }
        }

        $contents = $query->latest()->paginate(20)->withQueryString();
        $stages = Stage::with('subjects')->orderBy('grade_level')->get();

        return view('videographer.index', compact('contents', 'stages'));
    }

    /**
     * استوديو رفع وتوزيع محاضرة جديدة على الفروع والمواد
     */
    public function create()
    {
        $stages = Stage::with(['subjects' => function($q) {
            $q->orderBy('name_ar');
        }])->orderBy('grade_level', 'desc')->get();

        // تجميع أسماء المواد المشتركة عبر كافة الفروع لسهولة الاختيار
        $allSubjects = Subject::with('stage')->orderBy('name_ar')->get();

        // خريطة المواد المتاحة لكل فرع لدعم التحديد التفاعلي بالجافاسكريبت
        $stageSubjectsMap = [];
        foreach ($stages as $stage) {
            $stageSubjectsMap[$stage->id] = $stage->subjects->map(function($sub) {
                return [
                    'id'       => $sub->id,
                    'name_ar'  => $sub->name_ar ?? $sub->name,
                    'clean_name' => trim(preg_replace('/\s*\(.*?\)\s*/u', '', $sub->name_ar ?? $sub->name)),
                    'subject_key' => $sub->subject_key,
                ];
            })->values();
        }

        // فحص أي ملف فيديو مكتمل تم رفعه مؤخراً ولم يُسجل في قاعدة البيانات بعد (استرداد ذكي في حال انقطاع الجلسة)
        $recentUnlinkedVideo = null;
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

        return view('videographer.create', compact('stages', 'allSubjects', 'stageSubjectsMap', 'recentUnlinkedVideo'));
    }

    /**
     * محرك النشر والتوزيع التلقائي للمحاضرة عبر الفروع والمواد المحددة
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'stage_ids'         => 'required|array|min:1',
            'stage_ids.*'       => 'integer|exists:stages,id',
            'subject_ids'       => 'nullable|array',
            'common_name'       => 'nullable|string',
            'video_url'         => 'nullable|string',
            'uploaded_video_path' => 'nullable|string',
            'video_file'        => 'nullable|file|mimes:mp4,webm,ogg,mov,m4v,mkv',
            'pdf_file'          => 'nullable|file|mimes:pdf,docx,zip|max:61440',
            'order'             => 'nullable|integer|min:0',
            'target_region'     => 'nullable|in:all,west_bank,gaza',
            'channel_name'      => 'nullable|string|max:100',
        ]);

        $stageIds = array_map('intval', $request->stage_ids);
        $targetSubjectIds = [];

        // 1. استخراج معرفات المواد المستهدفة الصريحة
        if ($request->filled('subject_ids') && is_array($request->subject_ids)) {
            $targetSubjectIds = array_map('intval', $request->subject_ids);
        }

        // 2. إذا تم اختيار اسم مادة عام أو مادة مرجعية، نربطها تلقائياً بالمواد المطابقة في الفروع المختارة بأمان تام
        if ($request->filled('common_name') || $request->filled('primary_subject_id')) {
            $refName = trim((string)$request->common_name);
            $refKey = '';
            if ($request->filled('primary_subject_id')) {
                $refSub = Subject::find($request->primary_subject_id);
                if ($refSub) {
                    $refName = trim(preg_replace('/\s*\(.*?\)\s*/u', '', $refSub->name_ar ?? ''));
                    if (!empty($refSub->subject_key)) {
                        $refKey = explode('_', $refSub->subject_key)[0];
                    }
                }
            }

            if (!empty($refName) || !empty($refKey)) {
                $cleanRef = trim(preg_replace('/\s*\(.*?\)\s*/u', '', $refName));
                $normRef = preg_replace('/[إأآا]/u', '%', $cleanRef);
                $normRef = preg_replace('/[ةه]/u', '%', $normRef);
                $normRef = preg_replace('/[ىي]/u', '%', $normRef);

                $matchedIds = Subject::whereIn('stage_id', $stageIds)
                    ->where(function($q) use ($cleanRef, $normRef, $refKey) {
                        if (!empty($cleanRef)) {
                            $q->where('name_ar', 'like', "%{$cleanRef}%");
                        }
                        if (!empty($normRef) && $normRef !== $cleanRef) {
                            $q->orWhere('name_ar', 'like', "%{$normRef}%");
                        }
                        if (!empty($refKey)) {
                            $q->orWhere('subject_key', 'like', "{$refKey}_%");
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                $targetSubjectIds = array_values(array_unique(array_merge($targetSubjectIds, $matchedIds)));
            }
        }

        // 3. توسيع تلقائي إضافي: إذا اختار المستخدم عدة فروع وتحددت مادة لفرع واحد فقط، جلب نظيراتها في باقي الفروع
        if (!empty($targetSubjectIds) && count($stageIds) > 1) {
            $firstSub = Subject::find($targetSubjectIds[0]);
            if ($firstSub) {
                $cName = trim(preg_replace('/\s*\(.*?\)\s*/u', '', $firstSub->name_ar ?? ''));
                $bKey = !empty($firstSub->subject_key) ? explode('_', $firstSub->subject_key)[0] : '';
                $extraIds = Subject::whereIn('stage_id', $stageIds)
                    ->where(function($q) use ($cName, $bKey) {
                        if (!empty($cName)) {
                            $q->where('name_ar', 'like', "%{$cName}%");
                        }
                        if (!empty($bKey)) {
                            $q->orWhere('subject_key', 'like', "{$bKey}_%");
                        }
                    })
                    ->pluck('id')
                    ->toArray();
                $targetSubjectIds = array_values(array_unique(array_merge($targetSubjectIds, $extraIds)));
            }
        }

        if (empty($targetSubjectIds)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى تحديد مادة واحدة على الأقل أو اختيار فروع تحتوي على المادة المطلوبة.'
                ], 422);
            }
            return back()->withInput()->withErrors([
                'stage_ids' => 'يرجى تحديد مادة واحدة على الأقل أو اختيار فروع تحتوي على المادة المطلوبة.'
            ]);
        }

        // 4. معالجة الفيديو الأساسي
        $videoPath = null;
        $fileSize = null;

        // أ) إذا تم رفعه مسبقاً عبر Chunked Upload
        if ($request->filled('uploaded_video_path')) {
            $videoPath = trim($request->uploaded_video_path);
            $fileSize = $request->formatted_size ?? 'فيديو مرفوع';
        }
        // ب) إذا تم رفعه كملف مباشر في الفورم
        elseif ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = 'video_' . time() . '_' . Str::random(10) . '.' . $ext;
            $videoPath = $file->storeAs('educational/videos', $filename, 'public');
            $fileSize = round($file->getSize() / 1048576, 1) . ' MB';
        }
        // ج) إذا كان رابط يوتيوب أو رابط خارجي
        elseif ($request->filled('video_url')) {
            $videoPath = trim($request->video_url);
            $fileSize = 'رابط خارجي';
        }

        if (empty($videoPath)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى تحديد ملف فيديو أو الانتظار حتى اكتمال الرفع أو تزويد رابط للمحاضرة.'
                ], 422);
            }
            return back()->withInput()->withErrors([
                'video_file' => 'يرجى تحديد ملف فيديو أو الانتظار حتى اكتمال الرفع أو تزويد رابط للمحاضرة.'
            ]);
        }

        // 5. معالجة ملف الدوسية أو الملخص (PDF)
        $pdfPath = null;
        if ($request->hasFile('pdf_file')) {
            $pdfFile = $request->file('pdf_file');
            $pdfExt = strtolower($pdfFile->getClientOriginalExtension());
            $pdfFilename = 'sheet_' . time() . '_' . Str::random(10) . '.' . $pdfExt;
            $pdfPath = $pdfFile->storeAs('educational/pdfs', $pdfFilename, 'public');
        }

        $userId = auth()->id();
        $createdRecordsCount = 0;
        $affectedBranchesCount = count($stageIds);

        // 6. إنشاء وتوزيع المحتوى تلقائياً لكل مادة وفرع تم اختياره
        foreach ($targetSubjectIds as $subId) {
            $sub = Subject::with(['teacher', 'stage'])->find($subId);
            if (!$sub) continue;

            // تحديد اسم مقدم الشرح أو القناة تلقائياً (إسناد للمعلم إن وجد)
            $channelName = $request->channel_name;
            if (empty($channelName) || in_array($channelName, [auth()->user()->name, 'المصور الأكاديمي', 'استوديو التصوير المعتمد'])) {
                if (!empty($sub->teacher_name)) {
                    $channelName = $sub->teacher_name;
                } elseif ($sub->teacher && !empty($sub->teacher->name)) {
                    $channelName = $sub->teacher->name_ar ?? $sub->teacher->name;
                } else {
                    $channelName = $request->channel_name ?? (auth()->user()->name ?? 'المصور الأكاديمي');
                }
            }

            $contentRecord = EducationalContent::create([
                'subject_id'    => $subId,
                'uploaded_by'   => $userId,
                'title'         => $request->title,
                'type'          => 'video',
                'url_path'      => $videoPath,
                'pdf_path'      => $pdfPath,
                'channel_name'  => $channelName,
                'file_size'     => $fileSize,
                'order'         => (int) ($request->order ?? 1),
                'is_visible'    => true,
                'target_region' => $request->target_region ?? 'all',
            ]);
            $createdRecordsCount++;

            // إرسال إشعارات فورية لطلبة هذا الفرع والمادة
            if ($sub->stage_id) {
                try {
                    \App\Services\NotificationService::notifyStageStudents(
                        $sub->stage_id,
                        'محاضرة وشرح مرئي جديد 🎬',
                        "أُضيف درس مصور جديد: \"{$request->title}\" في مبحث {$sub->name_ar}.",
                        'content',
                        route('student.subjects.show', $sub->id)
                    );
                } catch (\Throwable $e) {}
            }

            // إتاحة الوصول التلقائي للطلبة المسجلين باشتراك نشط في هذه المادة
            try {
                $activeEnrollmentIds = \App\Models\Enrollment::where('subject_id', $subId)
                    ->where('status', 'active')
                    ->pluck('id');

                if ($activeEnrollmentIds->isNotEmpty()) {
                    $existingAssigned = \App\Models\ContentAssignment::whereIn('enrollment_id', $activeEnrollmentIds)
                        ->where('educational_content_id', $contentRecord->id)
                        ->pluck('enrollment_id')
                        ->flip();

                    $now = now();
                    $newAssignments = [];
                    foreach ($activeEnrollmentIds as $enrId) {
                        if (!isset($existingAssigned[$enrId])) {
                            $newAssignments[] = [
                                'enrollment_id'          => $enrId,
                                'educational_content_id' => $contentRecord->id,
                                'is_visible'             => true,
                                'created_at'             => $now,
                                'updated_at'             => $now,
                            ];
                        }
                    }
                    if (!empty($newAssignments)) {
                        foreach (array_chunk($newAssignments, 100) as $chunk) {
                            \App\Models\ContentAssignment::insert($chunk);
                        }
                    }
                }
            } catch (\Throwable $e) {}

            // توزيع ونشر تلقائي شامل لكافة المواد الشقيقة في جميع الفروع وفتح الوصول التلقائي للطلبة
            try {
                \App\Services\EducationalContentSyncService::distributeContentToAllBranches($contentRecord);
            } catch (\Throwable $e) {}
        }

        $successMsg = "تم رفع المحاضرة ونشرها وتوزيعها تلقائياً على ({$createdRecordsCount}) مواد في ({$affectedBranchesCount}) فروع أكاديمية بنجاح! 🎉";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'redirect' => route('videographer.contents.index'),
            ]);
        }

        return redirect()->route('videographer.contents.index')->with('success', $successMsg);
    }

    /**
     * مزامنة وتوزيع محاضرة موجودة مسبقاً على كافة الفروع الأكاديمية الشقيقة
     */
    public function syncBranches($id, Request $request)
    {
        $content = EducationalContent::with('subject.stage')->findOrFail($id);
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'videographer', 'teacher'])) {
            abort(403, 'غير مصرح لك بمزامنة هذا المحتوى.');
        }

        $baseSub = $content->subject;
        if (!$baseSub) {
            return back()->with('error', 'المادة الأساسية لهذا المحتوى غير مسجلة.');
        }

        $cleanName = trim(preg_replace('/\s*\(.*?\)\s*/u', '', $baseSub->name_ar ?? ''));
        $baseKey = !empty($baseSub->subject_key) ? explode('_', $baseSub->subject_key)[0] : '';

        // البحث عن كافة المواد المشتركة في كافة المراحل والفروع
        $allSisterSubjects = Subject::where(function($q) use ($cleanName, $baseKey) {
            if (!empty($cleanName)) {
                $norm = preg_replace('/[إأآا]/u', '%', $cleanName);
                $norm = preg_replace('/[ةه]/u', '%', $norm);
                $norm = preg_replace('/[ىي]/u', '%', $norm);
                $q->where('name_ar', 'like', "%{$cleanName}%")
                  ->orWhere('name_ar', 'like', "%{$norm}%");
            }
            if (!empty($baseKey)) {
                $q->orWhere('subject_key', 'like', "{$baseKey}_%");
            }
        })->get();

        $syncedCount = 0;
        foreach ($allSisterSubjects as $sisterSub) {
            // التحقق مما إذا كان المحتوى مضافاً مسبقاً في هذه المادة
            $alreadyExists = EducationalContent::where('subject_id', $sisterSub->id)
                ->where(function($q) use ($content) {
                    $q->where('title', $content->title);
                    if (!empty($content->url_path)) {
                        $q->orWhere('url_path', $content->url_path);
                    }
                })->exists();

            if (!$alreadyExists) {
                $channelName = $content->channel_name;
                if (!empty($sisterSub->teacher_name)) {
                    $channelName = $sisterSub->teacher_name;
                } elseif ($sisterSub->teacher && !empty($sisterSub->teacher->name)) {
                    $channelName = $sisterSub->teacher->name_ar ?? $sisterSub->teacher->name;
                }

                $newRecord = EducationalContent::create([
                    'subject_id'    => $sisterSub->id,
                    'uploaded_by'   => $content->uploaded_by,
                    'title'         => $content->title,
                    'type'          => $content->type ?: 'video',
                    'url_path'      => $content->url_path,
                    'pdf_path'      => $content->pdf_path,
                    'channel_name'  => $channelName,
                    'file_size'     => $content->file_size,
                    'order'         => $content->order ?? 1,
                    'is_visible'    => true,
                    'target_region' => $content->target_region ?? 'all',
                ]);
                $syncedCount++;

                // إتاحة الوصول للطلبة المسجلين في هذا الفرع
                try {
                    $activeEnrollments = \App\Models\Enrollment::where('subject_id', $sisterSub->id)
                        ->where('status', 'active')
                        ->get();
                    foreach ($activeEnrollments as $enr) {
                        \App\Models\ContentAssignment::firstOrCreate([
                            'enrollment_id'          => $enr->id,
                            'educational_content_id' => $newRecord->id,
                        ], [
                            'is_visible' => true,
                        ]);
                    }
                } catch (\Throwable $e) {}
            }
        }

        $msg = $syncedCount > 0 
            ? "تم بنجاح توزيع ومزامنة المحاضرة على ({$syncedCount}) فروع أكاديمية إضافية! 🎉" 
            : "المحاضرة موزعة بالفعل على كافة الفروع الأكاديمية المطابقة.";

        return back()->with('success', $msg);
    }

    /**
     * إتاحة أو حجب الفيديو عن الطلاب بلمسة واحدة للمصور
     */
    public function toggleVisibility($id, Request $request)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'videographer'])) {
            return response()->json(['success' => false, 'error' => 'غير مصرح لك بتعديل حالة هذا المحتوى'], 403);
        }

        $content = EducationalContent::findOrFail($id);
        $newVisible = $content->is_visible ? 0 : 1;
        $content->update(['is_visible' => $newVisible]);

        // مزامنة حالة العرض/الحجب مع كافة النسخ الموزعة في الفروع الشقيقة تلقائياً
        try {
            $sisterQuery = EducationalContent::where('id', '!=', $content->id);
            if (!empty($content->url_path)) {
                $sisterQuery->where('url_path', $content->url_path);
            } elseif (!empty($content->pdf_path)) {
                $sisterQuery->where('pdf_path', $content->pdf_path);
            } elseif (!empty($content->title)) {
                $sisterQuery->where('title', $content->title);
            }
            $sisterIds = $sisterQuery->pluck('id')->toArray();
            $sisterQuery->update(['is_visible' => $newVisible]);

            // تحديث تعيينات وصول الطلبة في ContentAssignment لضمان الحجب الفوري أو الإتاحة الفورية
            $allAffectedIds = array_merge([(int)$content->id], $sisterIds);
            \App\Models\ContentAssignment::whereIn('educational_content_id', $allAffectedIds)
                ->update(['is_visible' => (bool)$newVisible]);
        } catch (\Throwable $e) {}

        $msg = $newVisible 
            ? 'تم إتاحة وعرض الفيديو للطلاب بنجاح 🟢' 
            : 'تم حجب الفيديو وقفله عن الطلاب 🔒';

        return response()->json([
            'success'    => true,
            'is_visible' => (bool)$newVisible,
            'message'    => $msg
        ]);
    }

    /**
     * حذف فيديو نهائياً من قاعدة البيانات والسيرفر
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'videographer', 'teacher'])) {
            abort(403, 'غير مصرح لك بحذف هذا المحتوى.');
        }

        $content = EducationalContent::find($id);

        if (!$content) {
            $msg = 'المحاضرة تم مسحها بالفعل مسبقاً من قاعدة البيانات والسيرفر ✅';
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'already_deleted' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        }

        $title = $content->title;
        $urlPath = $content->url_path;
        $pdfPath = $content->pdf_path;

        // العثور على المحاضرة وكافة النسخ الموزعة منها في الفروع الشقيقة بدقة
        $allIds = EducationalContent::where('id', $content->id)
            ->orWhere(function($q) use ($content, $urlPath, $pdfPath) {
                if (!empty($urlPath)) {
                    $q->where('url_path', $urlPath);
                }
                if (!empty($pdfPath)) {
                    $q->orWhere('pdf_path', $pdfPath);
                }
                if (!empty($content->title)) {
                    $q->orWhere(function($subQ) use ($content) {
                        $subQ->where('title', $content->title)
                             ->where('uploaded_by', $content->uploaded_by);
                    });
                }
            })
            ->pluck('id')
            ->toArray();

        if (empty($allIds)) {
            $allIds = [(int)$content->id];
        }

        // مسح كافة التبعيات من الجداول المرتبطة لفك أي قيود
        try {
            \DB::table('recommendations')->whereIn('content_id', $allIds)->delete();
        } catch (\Throwable $e) {}
        try {
            \App\Models\ContentAssignment::whereIn('educational_content_id', $allIds)->delete();
            \DB::table('content_assignments')->whereIn('content_id', $allIds)->orWhereIn('educational_content_id', $allIds)->delete();
        } catch (\Throwable $e) {}
        try {
            \App\Models\VideoProgress::whereIn('educational_content_id', $allIds)->delete();
            \DB::table('video_progress')->whereIn('content_id', $allIds)->orWhereIn('educational_content_id', $allIds)->delete();
            \App\Models\VideoNote::whereIn('educational_content_id', $allIds)->delete();
            \DB::table('video_notes')->whereIn('content_id', $allIds)->orWhereIn('educational_content_id', $allIds)->delete();
        } catch (\Throwable $e) {}

        // حذف الملفات الفعلية من السيرفر إن لم تكن مستخدمة في سجلات أخرى
        if (!empty($urlPath) && !str_starts_with($urlPath, 'http')) {
            $otherUses = EducationalContent::whereNotIn('id', $allIds)->where('url_path', $urlPath)->exists();
            if (!$otherUses) {
                try { Storage::disk('public')->delete($urlPath); } catch (\Throwable $e) {}
            }
        }
        if (!empty($pdfPath)) {
            $otherPdf = EducationalContent::whereNotIn('id', $allIds)->where('pdf_path', $pdfPath)->exists();
            if (!$otherPdf) {
                try { Storage::disk('public')->delete($pdfPath); } catch (\Throwable $e) {}
            }
        }

        // حذف السجلات نهائياً ومباشرة من قاعدة البيانات
        \DB::table('educational_contents')->whereIn('id', $allIds)->delete();
        EducationalContent::whereIn('id', $allIds)->delete();

        // مسح كاش المزامنة التلقائية
        try {
            \Illuminate\Support\Facades\Cache::forget('educational_contents_last_synced_at');
        } catch (\Throwable $e) {}

        $count = count($allIds);
        $msg = $count > 1 
            ? "تم مسح وحذف المحاضرة \"{$title}\" وكافة نسخها في الفروع ({$count} فروع) نهائياً من قاعدة البيانات ✅"
            : "تم مسح وحذف المحاضرة \"{$title}\" نهائياً من قاعدة البيانات بنجاح ✅";

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'deleted_ids' => $allIds]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * تصفير وحذف جميع المحتويات المرفوعة بواسطة المصور نهائياً من قاعدة البيانات
     */
    public function purgeAllContents(Request $request)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'videographer'])) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بتنفيذ هذا الإجراء'], 403);
        }

        try {
            \DB::beginTransaction();

            $query = EducationalContent::query();
            if ($user->role === 'videographer') {
                $query->where('uploaded_by', $user->id);
            }

            $contentIds = $query->pluck('id')->toArray();
            $totalCount = count($contentIds);

            if ($totalCount > 0) {
                // العثور على كافة النسخ الشقيقة المرتبطة بهذه المحتويات
                $urls = EducationalContent::whereIn('id', $contentIds)->whereNotNull('url_path')->pluck('url_path')->toArray();
                $pdfs = EducationalContent::whereIn('id', $contentIds)->whereNotNull('pdf_path')->pluck('pdf_path')->toArray();
                
                $allSisterIds = EducationalContent::where(function($q) use ($contentIds, $urls, $pdfs, $user) {
                    $q->whereIn('id', $contentIds)->orWhere('uploaded_by', $user->id);
                    if (!empty($urls)) $q->orWhereIn('url_path', $urls);
                    if (!empty($pdfs)) $q->orWhereIn('pdf_path', $pdfs);
                })->pluck('id')->toArray();

                try {
                    \DB::table('recommendations')->whereIn('content_id', $allSisterIds)->delete();
                } catch (\Throwable $e) {}

                try {
                    \App\Models\ContentAssignment::whereIn('educational_content_id', $allSisterIds)->delete();
                    \DB::table('content_assignments')->whereIn('content_id', $allSisterIds)->orWhereIn('educational_content_id', $allSisterIds)->delete();
                } catch (\Throwable $e) {}

                try {
                    \App\Models\VideoProgress::whereIn('educational_content_id', $allSisterIds)->delete();
                    \DB::table('video_progress')->whereIn('content_id', $allSisterIds)->orWhereIn('educational_content_id', $allSisterIds)->delete();
                    \App\Models\VideoNote::whereIn('educational_content_id', $allSisterIds)->delete();
                    \DB::table('video_notes')->whereIn('content_id', $allSisterIds)->orWhereIn('educational_content_id', $allSisterIds)->delete();
                } catch (\Throwable $e) {}

                // حذف السجلات نهائياً من قاعدة البيانات
                \DB::table('educational_contents')->whereIn('id', $allSisterIds)->delete();
                EducationalContent::whereIn('id', $allSisterIds)->delete();
            }

            \DB::commit();

            try {
                \Illuminate\Support\Facades\Cache::forget('educational_contents_last_synced_at');
            } catch (\Throwable $e) {}

            $msg = "تم حذف وتصفير ({$totalCount}) محتوى تعليمي نهائياً من قاعدة البيانات بنجاح 🗑️";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء محاولة الحذف من قاعدة البيانات: ' . $e->getMessage()], 500);
        }
    }

    /**
     * محرك الرفع المجزأ للفيديوهات الضخمة (Resumable Chunked Upload) لبوابة المصور
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
            @ini_set('max_execution_time', '0');
            @set_time_limit(0);
            @ini_set('memory_limit', '1024M');
            if (function_exists('ignore_user_abort')) {
                @ignore_user_abort(true);
            }

            $finalFilename = 'video_vg_' . time() . '_' . Str::random(12) . '.' . $ext;
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

            return response()->json([
                'success'             => true,
                'done'                => true,
                'completed'           => true,
                'file_path'           => $relativeDir . '/' . $finalFilename,
                'uploaded_video_path' => $relativeDir . '/' . $finalFilename,
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
     * استئناف الرفع عند انقطاع الاتصال
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
}
