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

}
