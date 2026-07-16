<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Submission_Answer;


class Exam extends Model
{
    /** @use HasFactory<\Database\Factories\ExamFactory> */
    use HasFactory;

    protected $fillable = ['subject_id', 'title', 'duration_minutes', 'status'];
    public function questions() { return $this->hasMany(Question::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function submissions() { return $this->hasMany(ExamSubmission::class); }
}
