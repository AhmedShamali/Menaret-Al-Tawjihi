<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\MediaHelper;

class VideoController extends Controller
{
    /**
     * بث وتدفق الفيديو عالي الكفاءة للمنصة مع الدعم الكامل لتقسيم البايتات (Byte-Ranges / HTTP 206)
     * يدعم التشغيل السلس، معرفة مدة الفيديو، التقديم والتأخير دون أي تجميد عند 0:00
     */
    public function stream(Request $request, $filename)
    {
        // دعم استعلامات CORS المسبقة
        if ($request->isMethod('OPTIONS')) {
            return response('', 204)->withHeaders([
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Range, Content-Type, Accept, Authorization',
                'Access-Control-Max-Age' => '86400',
            ]);
        }

        // إذا كان الرابط بالأساس خارجياً (مثل يوتيوب أو تخزين سحابي مباشر)
        if (filter_var($filename, FILTER_VALIDATE_URL) || str_starts_with($filename, 'http://') || str_starts_with($filename, 'https://')) {
            return redirect()->away($filename, 302);
        }

        // التحقق من صحة اسم الملف ومنع أي محاولات لتخطي المسار (Path Traversal) أو القيم غير الصالحة
        $cleanRawName = strtolower(trim((string)$filename));
        if (empty($cleanRawName) || in_array($cleanRawName, ['undefined', 'null', 'none', 'false', '0', 'undefined.mp4', 'null.mp4'])) {
            abort(404, 'ملف الفيديو غير موجود أو قيد التجهيز.');
        }

        if (str_contains($filename, '..') || str_contains($filename, "\0")) {
            abort(403, 'مسار غير مصرح به.');
        }

        if ($request->isMethod('OPTIONS')) {
            return response('', 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Range, Content-Type, Accept',
            ]);
        }

        // تنظيف المسار من أي زوائد وبوادئ مكررة
        $filename = ltrim(str_replace(['public/', 'storage/'], '', $filename), '/\\');
        $baseName = basename($filename);

        $resolvedPath = null;
        $candidates = [
            Storage::disk('public')->path($filename),
            storage_path('app/public/' . $filename),
            public_path('storage/' . $filename),
            Storage::disk('public')->path('educational/videos/' . $filename),
            storage_path('app/public/educational/videos/' . $filename),
            public_path('storage/educational/videos/' . $filename),
            Storage::disk('public')->path('educational/videos/' . $baseName),
            storage_path('app/public/educational/videos/' . $baseName),
            public_path('storage/educational/videos/' . $baseName),
            Storage::disk('public')->path($baseName),
            storage_path('app/public/' . $baseName),
            public_path('storage/' . $baseName),
            storage_path('app/' . $filename),
        ];

        foreach ($candidates as $cand) {
            // التأكد من أن الملف موجود وقابل للقراءة وحجمه أكبر من 0 بايت
            if (file_exists($cand) && is_file($cand) && filesize($cand) > 0) {
                $resolvedPath = $cand;
                break;
            }
        }

        // في حال عدم توفر الملف محلياً، التحقق من التخزين السحابي Supabase وبثه مباشرة
        if (!$resolvedPath) {
            $supabaseKey = 'educational/videos/' . $baseName;
            try {
                if (!empty(config('filesystems.disks.supabase.key')) && Storage::disk('supabase')->exists($supabaseKey)) {
                    $s3Size = Storage::disk('supabase')->size($supabaseKey);
                    if ($s3Size <= 100) {
                        return response()->json([
                            'error' => true,
                            'corrupted' => true,
                            'message' => 'ملف هذا الدرس غير مكتمل في التخزين السحابي (حجمه 0 بايت). يرجى من المعلم إعادة رفع الفيديو.'
                        ], 422, [
                            'Content-Type' => 'application/json',
                            'Access-Control-Allow-Origin' => '*',
                            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                        ]);
                    }

                    $s3Stream = Storage::disk('supabase')->readStream($supabaseKey);
                    if ($s3Stream && is_resource($s3Stream)) {
                        $rangeHeader = $request->server('HTTP_RANGE') ?: $request->header('Range');
                        if ($rangeHeader && str_starts_with($rangeHeader, 'bytes=')) {
                            $rangeSpec = substr($rangeHeader, 6);
                            $ranges = explode(',', $rangeSpec);
                            $range = trim($ranges[0]);

                            if (str_starts_with($range, '-')) {
                                $suffix = intval(substr($range, 1));
                                $from = max(0, $s3Size - $suffix);
                                $to = $s3Size - 1;
                            } elseif (str_ends_with($range, '-')) {
                                $from = intval(rtrim($range, '-'));
                                $to = $s3Size - 1;
                            } else {
                                $parts = explode('-', $range, 2);
                                $from = intval($parts[0]);
                                $to = !empty($parts[1]) ? intval($parts[1]) : $s3Size - 1;
                            }

                            $from = max(0, min($from, $s3Size - 1));
                            $to = max($from, min($to, $s3Size - 1));
                            $length = ($to - $from) + 1;

                            $headers = [
                                'Content-Type' => 'video/mp4',
                                'Content-Range' => "bytes {$from}-{$to}/{$s3Size}",
                                'Content-Length' => $length,
                                'Accept-Ranges' => 'bytes',
                                'Access-Control-Allow-Origin' => '*',
                                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                                'Access-Control-Allow-Headers' => 'Range, Content-Type, Accept',
                                'Cache-Control' => 'public, max-age=86400, must-revalidate',
                            ];

                            if ($request->isMethod('HEAD')) {
                                fclose($s3Stream);
                                return response('', 206, $headers);
                            }

                            return response()->stream(function () use ($s3Stream, $from, $to) {
                                while (ob_get_level()) {
                                    ob_end_clean();
                                }
                                fseek($s3Stream, $from);
                                $chunkSize = 256 * 1024;
                                while (!feof($s3Stream) && ($pointer = ftell($s3Stream)) <= $to) {
                                    if ($pointer + $chunkSize > $to) {
                                        $chunkSize = $to - $pointer + 1;
                                    }
                                    echo fread($s3Stream, $chunkSize);
                                    flush();
                                }
                                fclose($s3Stream);
                            }, 206, $headers);
                        }

                        $headers = [
                            'Content-Type' => 'video/mp4',
                            'Content-Length' => $s3Size,
                            'Accept-Ranges' => 'bytes',
                            'Access-Control-Allow-Origin' => '*',
                            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                            'Access-Control-Allow-Headers' => 'Range, Content-Type, Accept',
                            'Cache-Control' => 'public, max-age=86400, must-revalidate',
                        ];

                        if ($request->isMethod('HEAD')) {
                            fclose($s3Stream);
                            return response('', 200, $headers);
                        }

                        return response()->stream(function () use ($s3Stream) {
                            while (ob_get_level()) {
                                ob_end_clean();
                            }
                            fpassthru($s3Stream);
                            fclose($s3Stream);
                        }, 200, $headers);
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('VideoController stream Supabase exception: ' . $e->getMessage());
            }

            $cloudUrl = MediaHelper::url($filename) 
                ?: MediaHelper::url('educational/videos/' . $baseName);

            if ($cloudUrl && filter_var($cloudUrl, FILTER_VALIDATE_URL)) {
                return redirect()->away($cloudUrl, 302, [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Range, Content-Type, Accept',
                    'Accept-Ranges' => 'bytes',
                ]);
            }
            abort(404, 'ملف الفيديو غير موجود.');
        }

        $allowedExtensions = ['mp4', 'webm', 'ogg', 'mov', 'mkv', 'm4v', 'ogv'];
        $ext = strtolower(pathinfo($resolvedPath, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            abort(403, 'نوع الملف غير مدعوم للبث.');
        }

        $mimeTypes = [
            'mp4'  => 'video/mp4',
            'm4v'  => 'video/mp4',
            'webm' => 'video/webm',
            'ogg'  => 'video/ogg',
            'ogv'  => 'video/ogg',
            'mov'  => 'video/quicktime',
            'mkv'  => 'video/x-matroska',
        ];
        $contentType = $mimeTypes[$ext] ?? 'video/mp4';

        $size = filesize($resolvedPath);
        $file = fopen($resolvedPath, 'rb');
        if (!$file) {
            abort(500, 'تعذر فتح ملف الفيديو للقراءة.');
        }

        $headers = [
            'Content-Type' => $contentType,
            'Accept-Ranges' => 'bytes',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'Range, Content-Type, Accept',
            'Cache-Control' => 'public, max-age=86400, must-revalidate',
        ];

        // دعم طلبات الأجزاء (HTTP Range Requests 206)
        $rangeHeader = $request->server('HTTP_RANGE') ?: $request->header('Range');
        if ($rangeHeader && str_starts_with($rangeHeader, 'bytes=')) {
            $rangeSpec = substr($rangeHeader, 6);
            $ranges = explode(',', $rangeSpec);
            // معالجة النطاق الأول
            $range = trim($ranges[0]);

            if (str_starts_with($range, '-')) {
                // صيغة اللاحقة: bytes=-500
                $suffix = intval(substr($range, 1));
                $from = max(0, $size - $suffix);
                $to = $size - 1;
            } elseif (str_ends_with($range, '-')) {
                // صيغة البداية للنهاية: bytes=500-
                $from = intval(rtrim($range, '-'));
                $to = $size - 1;
            } else {
                // صيغة محددة: bytes=100-200
                $parts = explode('-', $range, 2);
                $from = intval($parts[0]);
                $to = !empty($parts[1]) ? intval($parts[1]) : $size - 1;
            }

            // التحقق من صحة الحدود
            $from = max(0, min($from, $size - 1));
            $to = max($from, min($to, $size - 1));
            $length = ($to - $from) + 1;

            $headers['Content-Range'] = "bytes {$from}-{$to}/{$size}";
            $headers['Content-Length'] = $length;

            if ($request->isMethod('HEAD')) {
                fclose($file);
                return response('', 206, $headers);
            }

            return response()->stream(function () use ($file, $from, $to) {
                while (ob_get_level()) {
                    ob_end_clean();
                }
                fseek($file, $from);
                $chunkSize = 256 * 1024; // 256KB chunks for smooth buffering
                while (!feof($file) && ($pointer = ftell($file)) <= $to) {
                    if ($pointer + $chunkSize > $to) {
                        $chunkSize = $to - $pointer + 1;
                    }
                    echo fread($file, $chunkSize);
                    flush();
                }
                fclose($file);
            }, 206, $headers);
        }

        // إرجاع الملف كاملاً إذا لم يطلب المتصفح أجزاء محددة
        $headers['Content-Length'] = $size;

        if ($request->isMethod('HEAD')) {
            fclose($file);
            return response('', 200, $headers);
        }

        return response()->stream(function () use ($file) {
            while (ob_get_level()) {
                ob_end_clean();
            }
            fpassthru($file);
            fclose($file);
        }, 200, $headers);
    }
}
