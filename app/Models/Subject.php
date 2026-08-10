<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;


    protected $fillable = [
        'stage_id',
        'user_id',
        'name_ar',
        'subject_key',
        'icon',
        'color'
    ];


    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($subject) {
            if (empty($subject->user_id)) {
                $subject->user_id = \App\Models\User::first()->id ?? null;
            }
        });
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contents()
    {
        return $this->hasMany(EducationalContent::class);
    }

    public function educationalContents()
    {
        return $this->hasMany(EducationalContent::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}

