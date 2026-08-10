<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementResult extends Model
{
    // أضف percentage هنا
    protected $fillable = ['student_id', 'subject_name', 'score', 'percentage', 'level'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
