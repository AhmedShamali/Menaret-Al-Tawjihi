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
        'a_image',
        'b',
        'b_image',
        'c',
        'c_image',
        'd',
        'd_image',
        'correct_answer',
        'require_file',
        'points',
        'is_placement'
    ];

    /**
     * الحصول على مصفوفة بكافة روابط الصورة الممكنة (محلياً وسحابياً)
     */
    public function getImageCandidates(): array
    {
        if (empty($this->image)) {
            return [];
        }

        $candidates = [];
        $raw = trim($this->image);
        $cleanPath = ltrim(str_replace(['storage/', 'public/', 'app/public/'], '', $raw), '/');
        $filename = basename($raw);

        // 1. فحص وجود الملف محلياً في مجلدات التخزين
        if ($filename) {
            foreach (['questions/' . $filename, 'question_options/' . $filename, 'uploads/' . $filename, $filename] as $tryPath) {
                if (file_exists(public_path('storage/' . $tryPath)) || file_exists(storage_path('app/public/' . $tryPath))) {
                    $candidates[] = asset('storage/' . $tryPath);
                    $candidates[] = '/storage/' . $tryPath;
                    break;
                }
            }
        }

        // 2. فحص الرابط الأصلي المحفوظ
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            $candidates[] = $raw;
        } else {
            $candidates[] = asset('storage/' . $cleanPath);
            $candidates[] = '/storage/' . $cleanPath;
        }

        // 3. بدائل سحابية في حال كان الملف مرفوعاً على Supabase أو Render
        if ($filename) {
            $candidates[] = "https://jdvcftdzwgydtztyszlg.supabase.co/storage/v1/object/public/educational-files/questions/{$filename}";
            $candidates[] = "https://menaret-al-tawjihi.onrender.com/storage/questions/{$filename}";
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    /**
     * الحصول على مصفوفة بكافة روابط صورة الخيار الممكنة
     */
    public function getOptionImageCandidates(string $opt): array
    {
        $field = strtolower($opt) . '_image';
        $val = $this->$field ?? null;
        if (empty($val)) {
            return [];
        }

        $candidates = [];
        $raw = trim($val);
        $cleanPath = ltrim(str_replace(['storage/', 'public/', 'app/public/'], '', $raw), '/');
        $filename = basename($raw);

        // 1. فحص وجود الملف محلياً
        if ($filename) {
            foreach (['question_options/' . $filename, 'questions/' . $filename, 'uploads/' . $filename, $filename] as $tryPath) {
                if (file_exists(public_path('storage/' . $tryPath)) || file_exists(storage_path('app/public/' . $tryPath))) {
                    $candidates[] = asset('storage/' . $tryPath);
                    $candidates[] = '/storage/' . $tryPath;
                    break;
                }
            }
        }

        // 2. الرابط الأصلي
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            $candidates[] = $raw;
        } else {
            $candidates[] = asset('storage/' . $cleanPath);
            $candidates[] = '/storage/' . $cleanPath;
        }

        // 3. بدائل سحابية
        if ($filename) {
            $candidates[] = "https://jdvcftdzwgydtztyszlg.supabase.co/storage/v1/object/public/educational-files/question_options/{$filename}";
            $candidates[] = "https://menaret-al-tawjihi.onrender.com/storage/question_options/{$filename}";
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    /**
     * الحصول على رابط صورة الخيار (A, B, C, D) مع أفضل مسار متوفر
     */
    public function getOptionImageUrl(string $opt): ?string
    {
        $candidates = $this->getOptionImageCandidates($opt);
        return !empty($candidates) ? $candidates[0] : null;
    }

    protected $casts = [
        'require_file' => 'boolean',
    ];

    /**
     * الحصول على الرابط المباشر الصحيح لصورة السؤال
     */
    public function getImageUrlAttribute(): ?string
    {
        $candidates = $this->getImageCandidates();
        return !empty($candidates) ? $candidates[0] : null;
    }

    /**
     * رابط محلي بديل
     */
    public function getLocalImageUrlAttribute(): ?string
    {
        $candidates = $this->getImageCandidates();
        return count($candidates) > 1 ? $candidates[1] : ($candidates[0] ?? null);
    }
}
