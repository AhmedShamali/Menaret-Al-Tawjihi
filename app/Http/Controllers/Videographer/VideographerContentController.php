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
        $userId = auth()->id();
        $isAdmin = auth()->user()->role === 'admin';

        $query = EducationalContent::with('subject.stage', 'uploader');

        // إذا لم يكن مديراً، يعرض فقط ما رفعه هذا المصور
        if (!$isAdmin) {
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
                      $sq->where('name_ar', 'like', "%{$s}%")
                        ->orWhere('name', 'like', "%{$s}%");
                  });
            });
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

        return view('videographer.create', compact('stages', 'allSubjects', 'stageSubjectsMap'));
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

        // 1. استخراج معرفات المواد المستهدفة في كافة الفروع المختارة
        if ($request->filled('subject_ids') && is_array($request->subject_ids)) {
            $targetSubjectIds = array_map('intval', $request->subject_ids);
        }

        // إذا تم اختيار اسم مادة عام أو مادة مرجعية، نربطها تلقائياً بالمواد المطابقة في الفروع المختارة
        if ($request->filled('common_name') || $request->filled('primary_subject_id')) {
            $refName = trim($request->common_name);
            if ($request->filled('primary_subject_id')) {
                $refSub = Subject::find($request->primary_subject_id);
                if ($refSub) {
                    $refName = trim(preg_replace('/\s*\(.*?\)\s*/u', '', $refSub->name_ar ?? $refSub->name));
                }
            }

            if (!empty($refName)) {
                $matchedIds = Subject::whereIn('stage_id', $stageIds)
                    ->where(function($q) use ($refName) {
                        $q->where('name_ar', 'like', "%{$refName}%")
                          ->orWhere('name', 'like', "%{$refName}%");
                    })
                    ->pluck('id')
                    ->toArray();

                $targetSubjectIds = array_values(array_unique(array_merge($targetSubjectIds, $matchedIds)));
            }
        }

        if (empty($targetSubjectIds)) {
            return back()->withInput()->withErrors([
                'stage_ids' => 'يرجى تحديد مادة واحدة على الأقل أو اختيار فروع تحتوي على المادة المطلوبة.'
            ]);
        }

        // 2. معالجة الفيديو الأساسي
        $videoPath = null;
        $fileSize = null;

        // أ) إذا تم رفعه مسبقاً عبر Chunked Upload
        if ($request->filled('uploaded_video_path')) {
            $videoPath = $request->uploaded_video_path;
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

        // 3. معالجة ملف الدوسية أو الملخص (PDF)
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

        // 4. إنشاء وتوزيع المحتوى تلقائياً لكل مادة وفرع تم اختياره
        foreach ($targetSubjectIds as $subId) {
            EducationalContent::create([
                'subject_id'    => $subId,
                'uploaded_by'   => $userId,
                'title'         => $request->title,
                'type'          => 'video',
                'url_path'      => $videoPath,
                'pdf_path'      => $pdfPath,
                'channel_name'  => $request->channel_name ?? (auth()->user()->name ?? 'المصور الأكاديمي'),
                'file_size'     => $fileSize,
                'order'         => (int) ($request->order ?? 0),
                'is_visible'    => true,
                'target_region' => $request->target_region ?? 'all',
            ]);
            $createdRecordsCount++;
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
     * حذف فيديو من مكتبة المصور
     */
    public function destroy($id)
    {
        $userId = auth()->id();
        $isAdmin = auth()->user()->role === 'admin';

        $content = EducationalContent::findOrFail($id);

        if (!$isAdmin && $content->uploaded_by !== $userId) {
            abort(403, 'غير مصرح لك بحذف هذا المحتوى.');
        }

        // حذف الملفات المرتبطة إن وجدت
        if (!empty($content->url_path) && !str_starts_with($content->url_path, 'http')) {
            Storage::disk('public')->delete($content->url_path);
        }
        if (!empty($content->pdf_path)) {
            Storage::disk('public')->delete($content->pdf_path);
        }

        $content->delete();

        return redirect()->back()->with('success', 'تم حذف المحاضرة بنجاح.');
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
                'done'                => true,
                'uploaded_video_path' => $relativeDir . '/' . $finalFilename,
                'formatted_size'      => $formattedSize,
                'message'             => 'تم اكتمال رفع ودمج الفيديو بنجاح',
            ]);
        }

        return response()->json([
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
