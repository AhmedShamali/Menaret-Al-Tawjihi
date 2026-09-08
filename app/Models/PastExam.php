<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'session',
        'branch',
        'subject_name',
        'exam_paper_url',
        'answer_key_url',
        'total_marks',
        'downloads_count',
        'notes',
    ];

    public function getSessionLabelAttribute()
    {
        return match ($this->session) {
            'first' => 'الدورة الأولى (يونيو)',
            'second' => 'الدورة الثانية (أغسطس)',
            'completion' => 'الدورة الاستكمالية',
            default => $this->session,
        };
    }

    public function getBranchLabelAttribute()
    {
        return match ($this->branch) {
            'scientific' => 'الفرع العلمي',
            'literary' => 'الفرع الأدبي',
            'business' => 'الريادة والأعمال',
            'industrial' => 'الفرع الصناعي',
            default => 'كافة الفروع',
        };
    }
}
