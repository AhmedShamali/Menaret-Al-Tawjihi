<?php

namespace App\Services;

use App\Models\EducationalContent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OfflineVideoManager
{
    /**
     * المسار المحلي لأداة تنزيل الفيديوهات yt-dlp
     */
    public static function getYtDlpPath(): ?string
    {
        $candidates = [
            storage_path('app/bin/yt-dlp.exe'),
            base_path('tools/yt-dlp.exe'),
            base_path('bin/yt-dlp.exe'),
            'C:\\xampp\\yt-dlp.exe',
            'yt-dlp.exe',
            'yt-dlp',
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand) && is_file($cand)) {
                return $cand;
            }
        }

        // فحص وجودها في PATH النظام
        $out = [];
        $code = 1;
        @exec('where yt-dlp 2>nul', $out, $code);
        if ($code === 0 && !empty($out[0]) && file_exists(trim($out[0]))) {
            return trim($out[0]);
        }

        return null;
    }

    /**
     * هل محرك تنزيل اليوتيوب مثبت ومتاح؟
     */
    public static function isEngineAvailable(): bool
    {
        return self::getYtDlpPath() !== null;
    }

    /**
     * المسار المتوقع لتخزين ملف MP4 الخاص باليوتيوب محلياً على الخادم
     */
    public static function getCachedYouTubePath(string $youtubeId): string
    {
        return storage_path('app/public/educational/videos/yt_' . $youtubeId . '.mp4');
    }

    /**
     * هل يتوفر ملف MP4 محلي على الخادم لهذا الدرس (سواء كان مرفوعاً أو تم تحويله من يوتيوب)؟
     */
    public static function hasLocalMp4(EducationalContent $content): bool
    {
        return self::resolveLocalMp4Path($content) !== null;
    }

    /**
     * استخراج المسار الفعلي لملف MP4 على الخادم إن وُجد
     */
    public static function resolveLocalMp4Path(EducationalContent $content): ?string
    {
        // 1. فحص كاش اليوتيوب أولاً إذا كان المحتوى يحتوي على معرف يوتيوب
        $ytId = $content->youtube_id;
        if ($ytId) {
            $cachedYt = self::getCachedYouTubePath($ytId);
            if (file_exists($cachedYt) && filesize($cachedYt) > 10240) {
                return $cachedYt;
            }
        }

        // 2. فحص مسار url_path المرفوع مباشرة
        $rawUrl = $content->url_path;
        if (empty($rawUrl)) {
            return null;
        }

        $isDirect = (bool) preg_match('/\.(mp4|webm|ogg|mov|m4v)($|\?)/i', $rawUrl) || str_contains($rawUrl, 'educational/videos');
        if ($isDirect) {
            $relativePath = null;
            if (preg_match('~educational/videos/[^\s?#]+~', $rawUrl, $m)) {
                $relativePath = $m[0];
            } elseif (!filter_var($rawUrl, FILTER_VALIDATE_URL)) {
                $relativePath = ltrim($rawUrl, '/');
            }

            if ($relativePath) {
                $candidates = [
                    Storage::disk('public')->path($relativePath),
                    public_path('storage/' . $relativePath),
                    public_path($relativePath),
                    storage_path('app/public/' . $relativePath),
                ];
                foreach ($candidates as $cand) {
                    if (file_exists($cand) && is_file($cand)) {
                        return $cand;
                    }
                }
            }
        }

        return null;
    }

    /**
     * تنزيل فيديو يوتيوب وتحويله إلى MP4 مخزن على الخادم
     */
    public static function downloadAndCacheYouTube(EducationalContent $content): array
    {
        $ytId = $content->youtube_id;
        if (!$ytId) {
            return ['success' => false, 'message' => 'هذا الدرس لا يحتوي على معرف فيديو يوتيوب صالح.'];
        }

        $targetFile = self::getCachedYouTubePath($ytId);

        // إذا كان الملف موجوداً مسبقاً وبحجم سليم
        if (file_exists($targetFile) && filesize($targetFile) > 102400) {
            return [
                'success' => true,
                'cached'  => true,
                'path'    => $targetFile,
                'size'    => filesize($targetFile),
                'message' => 'ملف الفيديو مجهز مسبقاً وجاهز للتحميل أوفلاين.',
            ];
        }

        $engine = self::getYtDlpPath();
        if (!$engine) {
            return [
                'success' => false,
                'message' => 'محرك تحميل اليوتيوب (yt-dlp) غير مثبت على الخادم حالياً. يرجى تثبيته أو رفع ملف الفيديو بصيغة MP4 مباشرة.',
            ];
        }

        // التأكد من وجود المجلد
        $dir = dirname($targetFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $ytUrl = "https://www.youtube.com/watch?v={$ytId}";
        // تحميل أفضل جودة مدمجة MP4 (720p أو 480p أو 360p) لا تحتاج معالجة ffmpeg معقدة
        $cmd = escapeshellarg($engine) . ' -f "best[ext=mp4]/best" --no-playlist -o ' . escapeshellarg($targetFile) . ' ' . escapeshellarg($ytUrl) . ' 2>&1';

        $output = [];
        $returnCode = 1;
        @exec($cmd, $output, $returnCode);

        if ($returnCode === 0 && file_exists($targetFile) && filesize($targetFile) > 102400) {
            // تحديث حجم الملف في قاعدة البيانات إن وُجد
            try {
                $content->file_size = round(filesize($targetFile) / (1024 * 1024), 1) . ' MB';
                $content->save();
            } catch (\Throwable $e) {}

            return [
                'success' => true,
                'cached'  => false,
                'path'    => $targetFile,
                'size'    => filesize($targetFile),
                'message' => 'تم تنزيل وتجهيز فيديو اليوتيوب أوفلاين بنجاح.',
            ];
        }

        $errorMsg = implode("\n", array_slice($output, -5));
        Log::error("Failed to download YouTube video {$ytId} via yt-dlp: " . $errorMsg);

        return [
            'success' => false,
            'message' => 'تعذر تنزيل الفيديو من يوتيوب: ' . ($errorMsg ?: 'رمز الخطأ ' . $returnCode),
        ];
    }
}
