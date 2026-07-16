<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\ExamSubmissionFactory> */
    use HasFactory;

    protected $fillable = ['exam_id', 'student_id', 'total_earned_grade', 'status'];
    public function answers() {
        return $this->hasMany(SubmissionAnswer::class);
        }
    public function student() {
        return $this->belongsTo(Student::class);
        }
    public function exam() {
        return $this->belongsTo(Exam::class);
        }
}
