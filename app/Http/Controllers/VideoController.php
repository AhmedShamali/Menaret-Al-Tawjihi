<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function stream($filename)
{
    // تنظيف المسار من أي زوائد
    $filename = str_replace(['public/', 'storage/'], '', $filename);

    // فحص المسار في storage/app/public
    $path = storage_path('app/public/' . $filename);

    if (!file_exists($path)) {
        // محاولة ثانية للفحص في حال كان الملف بالمجلد الرئيسي storage/app
        $path = storage_path('app/' . $filename);
    }

    if (!file_exists($path)) {
        abort(404, 'Video file not found at: ' . $path);
    }

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
