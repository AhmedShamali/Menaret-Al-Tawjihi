<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'educational_content_id',
        'timestamp_seconds',
        'note_text',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function content()
    {
        return $this->belongsTo(EducationalContent::class, 'educational_content_id');
    }

    public function getFormattedTimestampAttribute()
    {
        $minutes = floor($this->timestamp_seconds / 60);
        $seconds = $this->timestamp_seconds % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
