<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\EducationalContent;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إجبار كافة الفيديوهات السابقة في قاعدة البيانات على دقة 1080p Full HD
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
                $sep = str_contains($clean, '?') ? '&' : '?';
                $c->url_path = $clean . $sep . 'quality=1080p';
                $c->saveQuietly();
            }
        } catch (\Throwable $e) {
            // الاستمرار بسلاسة دون تعطيل المايجريشن
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
