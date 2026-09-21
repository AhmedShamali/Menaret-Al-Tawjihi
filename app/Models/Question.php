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
     * الحصول على رابط صورة الخيار (A, B, C, D)
     */
    public function getOptionImageUrl(string $opt): ?string
    {
        $field = strtolower($opt) . '_image';
        $val = $this->$field ?? null;
        if (empty($val)) {
            return null;
        }

        $img = trim($val);
        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
            return $img;
        }

        $cleanPath = ltrim(str_replace(['storage/', 'public/'], '', $img), '/');
        return asset('storage/' . $cleanPath);
    }

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

        $img = trim($this->image);
        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
            return $img;
        }

        $cleanPath = ltrim(str_replace(['storage/', 'public/'], '', $img), '/');
        return asset('storage/' . $cleanPath);
    }

    /**
     * رابط محلي بديل في حال تعذر الاتصال السحابي
     */
    public function getLocalImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }
        $cleanPath = ltrim(str_replace('storage/', '', trim($this->image)), '/');
        if (str_starts_with($cleanPath, 'http')) {
            $parts = explode('/', $cleanPath);
            $cleanPath = 'questions/' . end($parts);
        }
        return asset('storage/' . $cleanPath);
    }
}
