<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $fillable =
    [
        'stage_id',
        'name_ar',
        'subject_key',
        'icon',
        'color'
    ];

    public function stage() {
        return $this->belongsTo(Stage::class);
    }

    public function contents() {
        return $this->hasMany(EducationalContent::class);
    }
}
