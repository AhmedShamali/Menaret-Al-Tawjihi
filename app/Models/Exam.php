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
        'starts_at',
        'ends_at',
        'show_result_immediately',
        'is_published',
        'is_active',
        'status'
    ];

    protected $casts = [
        'show_result_immediately' => 'boolean',
        'is_published' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * هل الاختبار مفتوح ومتاح حالياً للطلاب
     */
    public function isOpen(): bool
    {
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }
        return true;
    }

    /**
     * هل الاختبار قادم ولم يبدأ موعده بعد
     */
    public function isUpcoming(): bool
    {
        return $this->starts_at ? now()->lt($this->starts_at) : false;
    }

    /**
     * هل انتهت الفترة الزمنية المحددة للاختبار
     */
    public function isExpired(): bool
    {
        return $this->ends_at ? now()->gt($this->ends_at) : false;
    }

    /**
     * حالة توقيت الاختبار
     */
    public function getTimeStatusAttribute(): string
    {
        if ($this->isUpcoming()) {
            return 'upcoming';
        }
        if ($this->isExpired()) {
            return 'expired';
        }
        if ($this->ends_at) {
            return 'active_limited';
        }
        return 'always_open';
    }

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
