<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function stream($filename)
    {
        // منع أي محاولات لتخطي المسار (Path Traversal) أو إدخال حروف غير صالحة
        if (str_contains($filename, '..') || str_contains($filename, "\0")) {
            abort(403, 'مسار غير مصرح به.');
        }

        // تنظيف المسار من أي زوائد
        $filename = ltrim(str_replace(['public/', 'storage/'], '', $filename), '/\\');

        $allowedBaseDirs = array_filter([
            realpath(storage_path('app/public')),
            realpath(storage_path('app')),
            realpath(public_path('storage')),
        ]);

        $resolvedPath = null;
        $candidates = [
            storage_path('app/public/' . $filename),
            storage_path('app/' . $filename),
            public_path('storage/' . $filename),
        ];

        foreach ($candidates as $cand) {
            $real = realpath($cand);
            if ($real && is_file($real)) {
                foreach ($allowedBaseDirs as $base) {
                    if (str_starts_with($real, $base)) {
                        $resolvedPath = $real;
                        break 2;
                    }
                }
            }
        }

        if (!$resolvedPath) {
            abort(404, 'ملف الفيديو غير موجود.');
        }

        $allowedExtensions = ['mp4', 'webm', 'ogg', 'mov', 'mkv', 'm4v'];
        $ext = strtolower(pathinfo($resolvedPath, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            abort(403, 'نوع الملف غير مدعوم للبث.');
        }

        $path = $resolvedPath;

    $size = filesize($path);
    $file = fopen($path, 'rb');

    $headers = [
        'Content-Type' => 'video/mp4',
        'Accept-Ranges' => 'bytes',
    ];

    if (request()->server('HTTP_RANGE')) {
        list($param, $range) = explode('=', request()->server('HTTP_RANGE'), 2);
        if ($param === 'bytes') {
            list($from, $to) = explode('-', $range, 2);
            $from = intval($from);
            $to = $to === '' ? $size - 1 : intval($to);

            $headers['Content-Range'] = "bytes $from-$to/$size";
            $headers['Content-Length'] = ($to - $from) + 1;

            return response()->stream(function () use ($file, $from, $to) {
                fseek($file, $from);
                $chunkSize = 1024 * 1024;
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
    }

    $headers['Content-Length'] = $size;
    return response()->stream(function () use ($file) {
        fpassthru($file);
        fclose($file);
    }, 200, $headers);
}
}
