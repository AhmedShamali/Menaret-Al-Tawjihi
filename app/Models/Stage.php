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
