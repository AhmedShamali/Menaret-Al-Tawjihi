<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name_ar', 'name_en', 'nid', 'email', 'password', 'age', 'gender', 'phone', 'whatsapp', 'photo', 'id_photo', 'stage_id', 'status',
        'streak_count', 'last_activity_date', 'total_points',
        'custom_discount_percent', 'custom_discount_fixed', 'discount_notes'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function stage() {
        return $this->belongsTo(Stage::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledSubjects()
    {
        return $this->belongsToMany(Subject::class, 'enrollments', 'student_id', 'subject_id')
                    ->withPivot('status', 'access_mode', 'payment_status', 'activated_at')
                    ->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function examSubmissions()
    {
        return $this->hasMany(ExamSubmission::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'student_id');
    }

    /**
     * تسجيل الالتزام اليومي وحساب الـ Streak والنقاط التحفيزية
     */
    public function recordDailyStreak()
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        if ($this->last_activity_date === $today) {
            return $this->streak_count;
        }

        if ($this->last_activity_date === $yesterday) {
            $this->streak_count += 1;
            $this->total_points += 15;
        } else {
            $this->streak_count = 1;
            $this->total_points += 10;
        }

        $this->last_activity_date = $today;
        $this->save();

        return $this->streak_count;
    }

    /**
     * الاسم الكامل للطالب (عربي كأولوية أولى)
     */
    public function getNameAttribute(): string
    {
        return $this->name_ar ?: ($this->name_en ?: 'طالب التوجيهي');
    }

    /**
     * هل يمتلك الطالب خصماً خاصاً معتمداً من الإدارة؟
     */
    public function hasDiscount(): bool
    {
        return ((float)($this->custom_discount_percent ?? 0) > 0) || ((float)($this->custom_discount_fixed ?? 0) > 0);
    }

    /**
     * نص توصيفي أنيق للخصم
     */
    public function getDiscountLabelAttribute(): string
    {
        $percent = (float)($this->custom_discount_percent ?? 0);
        $fixed = (float)($this->custom_discount_fixed ?? 0);

        if ($percent >= 100) {
            return 'إعفاء كامل 100%';
        }
        if ($percent > 0) {
            return 'خصم ' . round($percent) . '%';
        }
        if ($fixed > 0) {
            return 'خصم ' . round($fixed) . ' ₪';
        }
        return 'بدون خصم';
    }

    /**
     * حساب قيمة الخصم لأي مبلغ محدد
     */
    public function calculateDiscount(float $amount): float
    {
        if ($amount <= 0) {
            return 0.0;
        }

        $percent = (float)($this->custom_discount_percent ?? 0);
        $fixed = (float)($this->custom_discount_fixed ?? 0);

        if ($percent >= 100) {
            return (float)$amount;
        }

        if ($percent > 0) {
            return round($amount * ($percent / 100.0), 2);
        }

        if ($fixed > 0) {
            return min((float)$amount, round($fixed, 2));
        }

        return 0.0;
    }
}
