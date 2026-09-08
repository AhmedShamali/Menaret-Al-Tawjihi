<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    use HasFactory;

    protected $table = 'video_progress';

    protected $fillable = [
        'student_id',
        'educational_content_id',
        'last_position_seconds',
        'is_completed',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function content()
    {
        return $this->belongsTo(EducationalContent::class, 'educational_content_id');
    }
}
