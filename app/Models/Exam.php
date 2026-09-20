<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'stage_id',
        'title',
        'duration_minutes',
        'show_result_immediately',
        'is_published',
        'is_active',
        'status'
    ];

    protected $casts = [
        'show_result_immediately' => 'boolean',
        'is_published' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function submissions()
    {
        return $this->hasMany(ExamSubmission::class);
    }
}
