<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    // الحقول المسموح بتعبئتها (ضروري جداً لكي تعمل دالة log)
    protected $fillable = [
        'student_id',
        'type',
        'action',
        'description',
        'severity',
        'ip_address',
        'user_agent'
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    // --- ضع الدالة هنا ---
    public static function log($studentId, $type, $action, $description, $severity = 'info')
    {
        return self::create([
            'student_id'  => $studentId,
            'type'        => $type,
            'action'      => $action,
            'description' => $description,
            'severity'    => $severity,
            'ip_address'  => request()->ip(), // يلتقط الـ IP تلقائياً
            'user_agent'  => request()->userAgent(), // يلتقط نوع الجهاز والمتصفح تلقائياً
        ]);
    }
}
