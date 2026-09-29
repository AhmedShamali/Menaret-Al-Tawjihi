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

        // 2. إذا كنا في بيئة الإنتاج السحابية (Render) أو كان القرص الافتراضي supabase
        $isCloud = (config('filesystems.default') === 'supabase' || app()->environment('production') || !empty(env('RENDER')));

        if ($isCloud) {
            return $supabaseFileUrl;
        }

        // 3. في البيئة المحلية: إذا كان الملف متوفراً محلياً في storage
        if (file_exists(public_path('storage/' . $cleanPath)) || file_exists(storage_path('app/public/' . $cleanPath))) {
            return asset('storage/' . $cleanPath);
        }

        // 4. إذا لم يكن متوفراً محلياً، نستخدم رابط السحابة Supabase مباشرة
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
}
