<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plain_password',
        'phone',
        'major',
        'bio',
        'photo',
        'role',
        'stage_id',
        'subject_id',
        'last_activity',
    ];

    protected $hidden = [
        'password',
        'plain_password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_activity'     => 'datetime',
    ];

    // دالة تفحص هل المستخدم متصل حالياً (آخر نشاط خلال آخر 5 دقائق)
    public function isOnline()
    {
        if (!$this->last_activity) {
            return false;
        }
        $lastAct = is_string($this->last_activity) ? \Carbon\Carbon::parse($this->last_activity) : $this->last_activity;
        return $lastAct->gt(now()->subMinutes(5));
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name_ar ?? $this->name ?? 'مستخدم') . '&background=0284c7&color=fff&size=200&bold=true';
    }

    // دالة تجلب نص آخر ظهور أو متصل الآن بشكل جاهز
    public function getLastSeenStatus()
    {
        if ($this->isOnline()) {
            return 'متصل الآن';
        }

        if (!$this->last_activity) {
            return 'غير متصل';
        }

        $lastAct = is_string($this->last_activity) ? \Carbon\Carbon::parse($this->last_activity) : $this->last_activity;
        return 'آخر ظهور ' . $lastAct->diffForHumans();
    }


    public function subjects()
    {
        return $this->hasMany(Subject::class, 'user_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function salaries()
    {
        return $this->hasMany(\App\Models\TeacherSalary::class, 'teacher_id')->orderBy('year', 'desc')->orderBy('month', 'desc');
    }
}

