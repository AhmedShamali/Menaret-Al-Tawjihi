<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    // الحقول المسموح بتعبئتها
    protected $fillable = ['student_id', 'educational_content_id', 'reason'];

    // علاقة التوصية بالمحتوى التعليمي
    public function content()
    {
        // نربطها بموديل EducationalContent
        return $this->belongsTo(EducationalContent::class, 'educational_content_id');
    }
}
