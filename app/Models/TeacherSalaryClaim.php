<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSalaryClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'year',
        'month',
        'message',
        'admin_reply',
        'replied_by',
        'replied_at',
        'status',
    ];

    protected $casts = [
        'year'       => 'integer',
        'month'      => 'integer',
        'replied_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function getMonthNameArAttribute(): string
    {
        return TeacherSalary::monthNamesAr()[$this->month] ?? "شهر {$this->month}";
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'replied' => ['label' => 'تم الرد والاعتماد ✅', 'class' => 'badge-replied', 'color' => '#059669', 'bg' => '#ecfdf5'],
            default   => ['label' => 'بانتظار مراجعة الإدارة ⏳', 'class' => 'badge-pending', 'color' => '#d97706', 'bg' => '#fffbeb'],
        };
    }
}
