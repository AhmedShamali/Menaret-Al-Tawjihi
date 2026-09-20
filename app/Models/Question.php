<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'question_text',
        'image', // ⬅️ تمت إضافته للسماح بالحفظ الشامل عبر الـ Eloquent
        'a',
        'b',
        'c',
        'd',
        'correct_answer',
        'require_file',
        'points',
        'is_placement'
    ];

    protected $casts = [
        'require_file' => 'boolean',
    ];

    /**
     * الحصول على الرابط المباشر الصحيح لصورة السؤال
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        // 1. إذا كان المسار يبدأ بـ http:// أو https:// (رابط مباشر)
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        // 2. إذا كان الملف موجوداً على قرص Supabase السحابي المعتمد
        if (!empty(config('filesystems.disks.supabase.key'))) {
            try {
                if (\Illuminate\Support\Facades\Storage::disk('supabase')->exists($this->image)) {
                    return \Illuminate\Support\Facades\Storage::disk('supabase')->url($this->image);
                }
            } catch (\Throwable $e) {}
        }

        // 3. إذا كان المسار يبدأ بالفعل بـ storage/
        if (str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }

        // 4. المسار الافتراضي على القرص المحلي العام
        return asset('storage/' . ltrim($this->image, '/'));
    }
}
