<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivationCode extends Model
{
    protected $fillable = [
        'code',
        'subject_id',
        'access_mode',
        'price_ils',
        'duration_days',
        'is_used',
        'used_by_student_id',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'used_at' => 'datetime',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'used_by_student_id');
    }

    public static function generateCode($prefix = 'TAW'): string
    {
        return $prefix . '-' . strtoupper(\Illuminate\Support\Str::random(4)) . '-' . strtoupper(\Illuminate\Support\Str::random(4));
    }
}
