<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    /** @use HasFactory<\Database\Factories\StageFactory> */
    use HasFactory;

    protected $fillable =
    [
        'grade_level',
        'label_ar',
        'icon'
    ];

    public function subjects() {
        return $this->hasMany(Subject::class);
    }

    public function students() {
        return $this->hasMany(Student::class, 'stage_id');
    }

}
