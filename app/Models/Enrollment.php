<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'status',
        'access_mode',
        'semester',
        'region_applied',
        'fee_amount',
        'paid_amount',
        'payment_status',
        'assigned_by',
        'activated_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'expires_at'   => 'datetime',
            'fee_amount'   => 'decimal:2',
            'paid_amount'  => 'decimal:2',
        ];
    }

    public function getSemesterLabelAttribute(): string
    {
        return match($this->semester) {
            'term_1' => 'الفصل الأول',
            'term_2' => 'الفصل الثاني',
            default  => 'الفصلين معاً (العام كامل)',
        };
    }

    public function getRegionLabelAttribute(): string
    {
        return ($this->region_applied === 'gaza') ? 'غزة' : 'الضفة';
    }

    public function getRemainingAmountAttribute(): float
    {
        if ($this->payment_status === 'scholarship' || $this->payment_status === 'free') {
            return 0.00;
        }
        $fee = (float) ($this->fee_amount ?? 0);
        $paid = (float) ($this->paid_amount ?? 0);
        return max(0.00, round($fee - $paid, 2));
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function contentAssignments(): HasMany
    {
        return $this->hasMany(ContentAssignment::class);
    }

    public function examAssignments(): HasMany
    {
        return $this->hasMany(ExamAssignment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
