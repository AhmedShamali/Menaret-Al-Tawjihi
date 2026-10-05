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

        // منع أي محاولات لتخطي المسار (Path Traversal) أو إدخال حروف غير صالحة
        if (str_contains($filename, '..') || str_contains($filename, "\0")) {
            abort(403, 'مسار غير مصرح به.');
        }

        // تنظيف المسار من أي زوائد وبوادئ مكررة
        $filename = ltrim(str_replace(['public/', 'storage/'], '', $filename), '/\\');
        $baseName = basename($filename);

        $allowedBaseDirs = array_filter([
            realpath(Storage::disk('public')->path('')),
            realpath(storage_path('app/public')),
            realpath(storage_path('app')),
            realpath(public_path('storage')),
            realpath(public_path('')),
        ]);

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
            storage_path('app/' . $filename),
        ];

        foreach ($candidates as $cand) {
            $real = realpath($cand);
            // التأكد من أن الملف موجود وقابل للقراءة وحجمه أكبر من 0 بايت
            if ($real && is_file($real) && filesize($real) > 0) {
                foreach ($allowedBaseDirs as $base) {
                    if (str_starts_with($real, $base)) {
                        $resolvedPath = $real;
                        break 2;
                    }
                }
            }
        }

        // في حال عدم توفر الملف محلياً، التحويل الفوري إلى رابط التخزين السحابي Supabase الصالح
        if (!$resolvedPath) {
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
