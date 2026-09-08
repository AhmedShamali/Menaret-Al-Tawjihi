<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name_ar', 'name_en', 'nid', 'email', 'password', 'age', 'gender', 'phone', 'whatsapp', 'photo', 'id_photo', 'stage_id', 'status',
        'streak_count', 'last_activity_date', 'total_points'
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
}
