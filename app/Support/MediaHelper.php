<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    /**
     * الحصول على الرابط العام المباشر لأي ملف أو صورة في النظام
     * يدعم تلقائياً كلاً من:
     * - الروابط الكاملة الخارجية (HTTP/HTTPS)
     * - التخزين السحابي Supabase Storage
     * - التخزين المحلي في public/storage
     * - البدائل التلقائية في حال عدم وجود مسار
     */
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if (empty($path)) {
            return $fallback;
        }

        $path = trim($path);

        // 1. إذا كان المسار بالأساس رابطاً كاملاً
        if (filter_var($path, FILTER_VALIDATE_URL) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // تنظيف البوادئ المكررة مثل storage/ أو public/
        $cleanPath = ltrim(preg_replace('/^(storage\/|public\/|app\/public\/)/', '', $path), '/');

        // رابط Supabase المعتمد
        $supabaseBucket = config('filesystems.disks.supabase.bucket') ?? env('SUPABASE_BUCKET', 'educational-files');
        $supabaseUrl = config('filesystems.disks.supabase.url');
        if (empty($supabaseUrl)) {
            $baseEndpoint = rtrim(env('SUPABASE_URL', 'https://jdvcftdzwgydtztyszlg.supabase.co'), '/');
            $supabaseUrl = "{$baseEndpoint}/storage/v1/object/public/{$supabaseBucket}";
        }
        $supabaseFileUrl = rtrim($supabaseUrl, '/') . '/' . $cleanPath;

        // 2. إذا كان الملف متوفراً محلياً في مسار التخزين (سواء محلياً أو على قرص السيرفر الدائم)
        if (file_exists(public_path('storage/' . $cleanPath)) || file_exists(storage_path('app/public/' . $cleanPath))) {
            return asset('storage/' . $cleanPath);
        }

        // 3. إذا كنا في بيئة الإنتاج السحابية أو كان القرص الافتراضي supabase ولم يتوفر محلياً
        $isCloud = (config('filesystems.default') === 'supabase' || app()->environment('production') || !empty(env('RENDER')));
        if ($isCloud) {
            return $supabaseFileUrl;
        }

        // 4. البديل الافتراضي
        return $supabaseFileUrl;
    }

    /**
     * رابط الصورة الشخصية للطالب أو المعلم أو المستخدم مع بديل افتراضي عالي الجودة
     */
    public static function avatarUrl(?string $path, ?string $name = null, string $role = 'student'): string
    {
        $displayName = !empty($name) ? $name : ($role === 'teacher' ? 'معلم' : 'طالب');
        $fallback = 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=0284c7&color=fff&size=200&bold=true';

        if (empty($path)) {
            return $fallback;
        }

        return self::url($path, $fallback) ?: $fallback;
    }

    /**
     * رفع وحفظ الملف الذكي: يرفع إلى Supabase والتخزين المحلي معاً لضمان عدم ضياع أي ملف
     */
    public static function store(UploadedFile $file, string $directory, ?string $disk = null): string
    {
        // اسم فريد للملف
        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png';
        $filename = \Illuminate\Support\Str::random(40) . '.' . $extension;
        $relativePath = trim($directory, '/') . '/' . $filename;

        // 1. الحفظ على القرص المحلي دائماً كنسخة فورية
        try {
            Storage::disk('public')->putFileAs(trim($directory, '/'), $file, $filename);
        } catch (\Throwable $e) {
            Log::warning("Local storage save failed for {$relativePath}: " . $e->getMessage());
        }

        // 2. الحفظ على Supabase لضمان البقاء الدائم في السحابة
        try {
            $stream = fopen($file->getRealPath(), 'r');
            $mime = $file->getMimeType() ?: 'application/octet-stream';
            Storage::disk('supabase')->put($relativePath, $stream, [
                'ContentType' => $mime,
                'visibility'  => 'public',
            ]);
            if (is_resource($stream)) {
                fclose($stream);
            }
        } catch (\Throwable $e) {
            Log::warning("Supabase storage save failed for {$relativePath}: " . $e->getMessage());
        }

        return $relativePath;
    }

    /**
     * إنشاء استجابة تنزيل أو استعراض لوثيقة أو ملف في النظام
     * يضمن التنزيل السلس المباشر وتجاوز قيود الـ CORS وتعيين الاسم العربي واللاحقة بدقة
     *
     * @param string|null $path المسار في قاعدة البيانات أو التخزين
     * @param string $downloadFilename الاسم المطلوب للملف المنزّل
     * @param bool $inline هل يتم العرض المباشر (inline) أم الإجبار على التنزيل (attachment)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function documentResponse(?string $path, string $downloadFilename, bool $inline = false)
    {
        if (empty($path)) {
            abort(404, __('عذراً، الوثيقة المطلوبة غير متوفرة أو لم تُرفع بعد.'));
        }

        $cleanPath = ltrim(preg_replace('/^(storage\/|public\/|app\/public\/)/', '', trim($path)), '/');
        $extension = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION) ?: pathinfo($downloadFilename, PATHINFO_EXTENSION) ?: 'jpg');

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
        ];
        $mime = $mimeTypes[$extension] ?? 'application/octet-stream';

        // التأكد من لاحقة اسم الملف عند التنزيل
        if (!str_ends_with(strtolower($downloadFilename), '.' . $extension)) {
            $downloadFilename .= '.' . $extension;
        }

        $asciiFallback = 'document_' . time() . '.' . $extension;
        $encodedFilename = rawurlencode($downloadFilename);
        $disposition = $inline ? 'inline' : 'attachment';
        $dispositionHeader = "{$disposition}; filename=\"{$asciiFallback}\"; filename*=UTF-8''{$encodedFilename}";

        // 1. الفحص في القرص المحلي عبر Storage::disk('public') لدعم كافة البيئات والتخزين الوهمي في الفحوصات
        try {
            if (Storage::disk('public')->exists($cleanPath)) {
                $stream = Storage::disk('public')->readStream($cleanPath);
                if ($stream) {
                    return response()->stream(function () use ($stream) {
                        fpassthru($stream);
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }, 200, [
                        'Content-Type' => $mime,
                        'Content-Disposition' => $dispositionHeader,
                        'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Local disk public readStream failed for {$cleanPath}: " . $e->getMessage());
        }

        // فحص مباشر للملفات المحلية بالمسار الفيزيائي إن وجدت
        $localFile = null;
        if (file_exists(storage_path('app/public/' . $cleanPath))) {
            $localFile = storage_path('app/public/' . $cleanPath);
        } elseif (file_exists(public_path('storage/' . $cleanPath))) {
            $localFile = public_path('storage/' . $cleanPath);
        }

        if ($localFile) {
            return response()->file($localFile, [
                'Content-Type' => $mime,
                'Content-Disposition' => $dispositionHeader,
                'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            ]);
        }

        // 2. الفحص في قرص Supabase Storage
        try {
            if (Storage::disk('supabase')->exists($cleanPath)) {
                $stream = Storage::disk('supabase')->readStream($cleanPath);
                if ($stream) {
                    return response()->stream(function () use ($stream) {
                        fpassthru($stream);
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }, 200, [
                        'Content-Type' => $mime,
                        'Content-Disposition' => $dispositionHeader,
                        'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Supabase readStream failed for {$cleanPath}: " . $e->getMessage());
        }

        // 3. المحاولة عبر الرابط العام بواسطة Http Client
        try {
            $url = self::url($cleanPath);
            if ($url) {
                $res = \Illuminate\Support\Facades\Http::timeout(20)->get($url);
                if ($res->successful()) {
                    return response($res->body(), 200, [
                        'Content-Type' => $mime,
                        'Content-Disposition' => $dispositionHeader,
                        'Content-Length' => strlen($res->body()),
                        'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error("Http download failed for {$cleanPath}: " . $e->getMessage());
        }

        // 4. في أسوأ الظروف، إعادة توجيه مباشرة إلى الرابط العام
        $directUrl = self::url($cleanPath);
        if ($directUrl) {
            return redirect()->away($directUrl);
        }

        abort(404, __('عذراً، تعذر العثور على ملف الوثيقة.'));
    }
}
