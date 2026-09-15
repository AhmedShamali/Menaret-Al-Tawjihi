<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMonthlySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year',
        'month',
        'amount',
        'status',
        'payment_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'month'   => 'integer',
        'paid_at' => 'datetime',
    ];

    public static function monthNamesAr(): array
    {
        return [
            1  => 'يناير (1)',
            2  => 'فبراير (2)',
            3  => 'مارس (3)',
            4  => 'أبريل (4)',
            5  => 'مايو (5)',
            6  => 'يونيو (6)',
            7  => 'يوليو (7)',
            8  => 'أغسطس (8)',
            9  => 'سبتمبر (9)',
            10 => 'أكتوبر (10)',
            11 => 'نوفمبر (11)',
            12 => 'ديسمبر (12)',
        ];
    }

    public function getMonthNameArAttribute(): string
    {
        return self::monthNamesAr()[$this->month] ?? "شهر {$this->month}";
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'paid'    => ['label' => 'مسدد وخالص ✅', 'class' => 'badge-paid', 'color' => '#059669', 'bg' => '#ecfdf5'],
            'pending' => ['label' => 'قيد المراجعة ⏳', 'class' => 'badge-pending', 'color' => '#d97706', 'bg' => '#fffbeb'],
            'waived'  => ['label' => 'إعفاء / منحة 🏷️', 'class' => 'badge-waived', 'color' => '#4f46e5', 'bg' => '#eef2ff'],
            default   => ['label' => 'غير مسدد ❌', 'class' => 'badge-unpaid', 'color' => '#dc2626', 'bg' => '#fef2f2'],
        };
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
