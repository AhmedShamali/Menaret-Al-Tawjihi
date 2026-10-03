<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_level',
        'label_ar',
        'icon'
    ];

    protected $appends = ['short_label'];

    /**
     * اسم الفرع المختصر والأنيق بدون حشو
     */
    public function getShortLabelAttribute(): string
    {
        $label = $this->label_ar ?? '';
        $clean = preg_replace('/\s*\(.*?\)\s*/u', '', $label);
        $clean = preg_replace('/الثانوية\s+العامة\s*[-–—]?\s*/u', '', $clean);
        $clean = preg_replace('/[-–—]?\s*الثانوية\s+العامة/u', '', $clean);
        $clean = trim($clean, " -–—\t\n\r\0\x0B");
        return !empty($clean) ? $clean : $label;
    }

    public function getNameArAttribute(): string
    {
        return $this->label_ar ?? '';
    }

    public function getNameAttribute(): string
    {
        return $this->label_ar ?? '';
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'stage_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'stage_id');
    }

    public function teachers()
    {
        return $this->hasMany(User::class, 'stage_id');
    }
}
