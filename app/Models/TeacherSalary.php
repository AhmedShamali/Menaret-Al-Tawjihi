<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'year',
        'month',
        'basic_salary',
        'bonus',
        'deductions',
        'net_salary',
        'status',
        'payment_date',
        'payment_method',
        'reference_no',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'year'         => 'integer',
        'month'        => 'integer',
        'basic_salary' => 'decimal:2',
        'bonus'        => 'decimal:2',
        'deductions'   => 'decimal:2',
        'net_salary'   => 'decimal:2',
        'payment_date' => 'date',
    ];

    public static function monthNamesAr(): array
    {
        return [
            1  => 'كانون الثاني / يناير (1)',
            2  => 'شباط / فبراير (2)',
            3  => 'آذار / مارس (3)',
            4  => 'نيسان / أبريل (4)',
            5  => 'أيار / مايو (5)',
            6  => 'حزيران / يونيو (6)',
            7  => 'تموز / يوليو (7)',
            8  => 'آب / أغسطس (8)',
            9  => 'أيلول / سبتمبر (9)',
            10 => 'تشرين الأول / أكتوبر (10)',
            11 => 'تشرين الثاني / نوفمبر (11)',
            12 => 'كانون الأول / ديسمبر (12)',
        ];
    }

    public function getMonthNameArAttribute(): string
    {
        return self::monthNamesAr()[$this->month] ?? "شهر {$this->month}";
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'paid'    => ['label' => 'تم الصرف والاستلام ✅', 'class' => 'badge-paid', 'color' => '#059669', 'bg' => '#ecfdf5'],
            'pending' => ['label' => 'قيد الاعتماد والصرف ⏳', 'class' => 'badge-pending', 'color' => '#d97706', 'bg' => '#fffbeb'],
            default   => ['label' => 'غير محدد', 'class' => 'badge-neutral', 'color' => '#64748b', 'bg' => '#f1f5f9'],
        };
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
