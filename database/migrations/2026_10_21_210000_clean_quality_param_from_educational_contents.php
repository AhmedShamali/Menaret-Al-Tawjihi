<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\EducationalContent;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تنظيف معلمات الجودة الملحقة بمسار الرابط في قاعدة البيانات لمنع أخطاء البحث عن الملفات
     */
    public function up(): void
    {
        try {
            $contents = EducationalContent::whereNotNull('url_path')
                ->where('url_path', '!=', '')
                ->get();

            foreach ($contents as $c) {
                $raw = trim((string)$c->url_path);
                if (empty($raw) || in_array(strtolower($raw), ['undefined', 'null', 'none'])) continue;
                $clean = preg_replace('/([?&])quality=[^&]+(&|$)/i', '$1', $raw);
                $clean = rtrim($clean, '?&');
                if ($clean !== $raw) {
                    $c->url_path = $clean;
                    $c->saveQuietly();
                }
            }
        } catch (\Throwable $e) {
            // الاستمرار بسلاسة
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا حاجة للتراجع
    }
};
