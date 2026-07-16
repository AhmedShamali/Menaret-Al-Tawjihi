<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionAnswer extends Model
{
    use HasFactory;

    // أضف هذه الحقول للسماح بحفظها
    protected $fillable = [
        'exam_submission_id',
        'question_id',
        'answer_text',
        'file_path',
        'points_awarded'
    ];

    // علاقة مع السؤال
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
