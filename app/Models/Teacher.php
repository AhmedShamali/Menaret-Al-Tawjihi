<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'stage_id',
        'subject_id',
        'image',
        // أضف بقية الأعمدة الخاصة بالمعلم هنا
    ];

    // علاقة المعلم بالمادة (إن وجدت)
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // علاقة المعلم بالمرحلة (إن وجدت)
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
}
