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
        'color',
        'price_ils',
        'discount_price_ils',
        'is_free',
        'description'
    ];

    /**
     * حساب السعر الفعلي للمادة بعد الخصومات أو المجانية
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->is_free) {
            return 0.00;
        }
        if ($this->discount_price_ils !== null && $this->discount_price_ils > 0) {
            return (float) $this->discount_price_ils;
        }
        return (float) ($this->price_ils ?? 150.00);
    }

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

