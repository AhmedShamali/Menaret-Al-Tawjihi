<?php

namespace App\Services;

use App\Models\EducationalContent;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\ContentAssignment;
use Illuminate\Support\Facades\Log;

class EducationalContentSyncService
{
    /**
     * استخراج كافة معرفات المواد التابعة لكافة الفروع التي تطابق المادة المحددة (مواد المنهاج المشتركة)
     */
    public static function resolveAllSisterSubjectIds(int|array $subjectIds): array
    {
        $inputIds = is_array($subjectIds) ? array_map('intval', array_filter($subjectIds)) : [(int)$subjectIds];
        if (empty($inputIds)) {
            return [];
        }

        $baseSubjects = Subject::whereIn('id', $inputIds)->get();
        $allResolvedIds = $inputIds;

        foreach ($baseSubjects as $baseSub) {
            $rawName = $baseSub->name_ar ?? $baseSub->name ?? '';
            $cleanName = trim(preg_replace('/\s*\(.*?\)\s*/u', '', $rawName));
            $baseKey = !empty($baseSub->subject_key) ? explode('_', $baseSub->subject_key)[0] : '';

            if (empty($cleanName) && empty($baseKey)) {
                continue;
            }

            $normName = preg_replace('/[إأآا]/u', '%', $cleanName);
            $normName = preg_replace('/[ةه]/u', '%', $normName);
            $normName = preg_replace('/[ىي]/u', '%', $normName);

            $matchedIds = Subject::where(function($q) use ($cleanName, $normName, $baseKey) {
                if (!empty($cleanName)) {
                    $q->where('name_ar', 'like', "%{$cleanName}%");
                }
                if (!empty($normName) && $normName !== $cleanName) {
                    $q->orWhere('name_ar', 'like', "%{$normName}%");
                }
                if (!empty($baseKey)) {
                    $q->orWhere('subject_key', 'like', "{$baseKey}_%");
                }
            })->pluck('id')->toArray();

            $allResolvedIds = array_merge($allResolvedIds, $matchedIds);
        }

        return array_values(array_unique(array_filter($allResolvedIds)));
    }

    /**
     * نشر محتوى تعليمي وتوزيعه تلقائياً على كافة الفروع والمواد المشتركة
     */
    public static function distributeContentToAllBranches(EducationalContent $content): int
    {
        if (empty($content->subject_id)) {
            return 0;
        }

        $allSisterIds = self::resolveAllSisterSubjectIds($content->subject_id);
        $replicatedCount = 0;

        foreach ($allSisterIds as $sisterSubId) {
            if ($sisterSubId === (int)$content->subject_id) {
                continue;
            }

            // فحص هل المحتوى منشور مسبقاً في هذه المادة الشقيقة
            $exists = EducationalContent::where('subject_id', $sisterSubId)
                ->where(function($q) use ($content) {
                    if (!empty($content->url_path)) {
                        $q->where('url_path', $content->url_path);
                    }
                    if (!empty($content->pdf_path)) {
                        $q->orWhere('pdf_path', $content->pdf_path);
                    }
                    if (!empty($content->title)) {
                        $q->orWhere('title', $content->title);
                    }
                })->exists();

            if (!$exists) {
                $sisterSub = Subject::with(['teacher'])->find($sisterSubId);
                $channelName = $content->channel_name;
                if (!empty($sisterSub?->teacher_name)) {
                    $channelName = $sisterSub->teacher_name;
                } elseif ($sisterSub?->teacher && !empty($sisterSub->teacher->name)) {
                    $channelName = $sisterSub->teacher->name_ar ?? $sisterSub->teacher->name;
                }

                $replica = EducationalContent::create([
                    'subject_id'    => $sisterSubId,
                    'uploaded_by'   => $content->uploaded_by,
                    'title'         => $content->title,
                    'type'          => $content->type ?? 'video',
                    'url_path'      => $content->url_path,
                    'pdf_path'      => $content->pdf_path,
                    'channel_name'  => $channelName,
                    'file_size'     => $content->file_size,
                    'order'         => $content->order ?? 1,
                    'is_visible'    => true,
                    'target_region' => $content->target_region ?? 'all',
                ]);
                $replicatedCount++;

                // فتح الوصول التلقائي للطلبة المسجلين باشتراك نشط في المادة الشقيقة
                self::unlockForActiveEnrollments($replica);
            }
        }

        // فتح الوصول للمادة الأساسية أيضاً
        self::unlockForActiveEnrollments($content);

        return $replicatedCount;
    }

    /**
     * فتح وإتاحة الوصول الفوري التلقائي للطلبة المسجلين باشتراك نشط
     */
    public static function unlockForActiveEnrollments(EducationalContent $content): void
    {
        try {
            $enrollmentIds = Enrollment::where('subject_id', $content->subject_id)
                ->where('status', 'active')
                ->pluck('id');

            if ($enrollmentIds->isEmpty()) {
                return;
            }

            $alreadyAssigned = ContentAssignment::whereIn('enrollment_id', $enrollmentIds)
                ->where('educational_content_id', $content->id)
                ->pluck('enrollment_id')
                ->flip();

            $now = now();
            $records = [];
            foreach ($enrollmentIds as $enrId) {
                if (!isset($alreadyAssigned[$enrId])) {
                    $records[] = [
                        'enrollment_id'          => $enrId,
                        'educational_content_id' => $content->id,
                        'is_visible'             => true,
                        'created_at'             => $now,
                        'updated_at'             => $now,
                    ];
                }
            }

            if (!empty($records)) {
                foreach (array_chunk($records, 100) as $chunk) {
                    ContentAssignment::insert($chunk);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Unlock active enrollments error: " . $e->getMessage());
        }
    }

    /**
     * مزامنة ذاتية لكافة الفيديوهات الموجودة مسبقاً وتوزيعها على جميع الفروع وتفعيل ظهورها للجميع
     */
    public static function syncAllExistingVideos(bool $force = false): int
    {
        if (!$force && \Illuminate\Support\Facades\Cache::has('educational_contents_last_synced_at')) {
            return 0;
        }
        \Illuminate\Support\Facades\Cache::put('educational_contents_last_synced_at', now()->timestamp, 45);

        // 1. جعل كل المحتويات المرئية مرئية ومفعلة (is_visible = true)
        try {
            EducationalContent::where('is_visible', false)->orWhereNull('is_visible')->update(['is_visible' => true]);
        } catch (\Throwable $e) {}

        // 2. فحص الفيديوهات والمحتويات وتوزيعها على المواد الشقيقة
        $contents = EducationalContent::where(function($q) {
            $q->whereNotNull('url_path')->where('url_path', '!=', '')
              ->orWhereNotNull('pdf_path')->where('pdf_path', '!=', '')
              ->orWhereIn('type', ['video', 'both', 'file', 'pdf']);
        })->get();

        $syncedTotal = 0;
        foreach ($contents as $item) {
            $syncedTotal += self::distributeContentToAllBranches($item);
        }

        return $syncedTotal;
    }
}
