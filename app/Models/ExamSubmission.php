<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\ExamSubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'exam_id', 
        'student_id', 
        'total_earned_grade', 
        'tab_switches_count',
        'screenshots_count',
        'cheating_flags',
        'has_cheating_risk',
        'is_published',
        'status', 
        'allow_retake', 
        'retake_requested', 
        'retake_request_notes', 
        'retake_granted_by'
    ];

    protected $casts = [
        'allow_retake' => 'boolean',
        'retake_requested' => 'boolean',
        'has_cheating_risk' => 'boolean',
        'is_published' => 'boolean',
        'cheating_flags' => 'array',
        'tab_switches_count' => 'integer',
        'screenshots_count' => 'integer',
    ];

    /**
     * التحقق مما إذا كان مسموحاً للطالب بالاطلاع على درجته ونموذج الإجابة
     */
    public function canStudentViewResult(): bool
    {
        // 1. إذا تم اعتماد ونشر النتيجة صراحة من قبل المعلم
        if ($this->is_published) {
            return true;
        }

        // 2. إذا تم تصحيح الامتحان من قبل المعلم واكتمل التقييم
        if ($this->status === 'graded') {
            return true;
        }

        // 3. إذا كان المعلم قد اختار إظهار النتيجة فورياً وكان الاختبار موضوعياً بالكامل
        if ($this->exam && $this->exam->show_result_immediately) {
            $allMcq = $this->exam->questions->isNotEmpty() && $this->exam->questions->every(fn($q) => $q->type === 'mcq');
            if ($allMcq) {
                return true;
            }
        }

        return false;
    }

    public function answers() {
        return $this->hasMany(SubmissionAnswer::class);
    }
    public function student() {
        return $this->belongsTo(Student::class);
    }
    public function exam() {
        return $this->belongsTo(Exam::class);
    }
    public function grantedBy() {
        return $this->belongsTo(User::class, 'retake_granted_by');
    }
}
