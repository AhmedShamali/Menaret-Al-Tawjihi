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
        'phone',
        'major',
        'bio',
        'photo',
        'role',
        'subject_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // دالة تفحص هل المستخدم متصل حالياً (آخر نشاط خلال آخر دقيقتين)
    public function isOnline()
    {
        return $this->last_activity && $this->last_activity->gt(now()->subMinutes(2));
    }

// دالة تجلب نص آخر ظهور أو متصل الآن بشكل جاهز
    public function getLastSeenStatus()
    {
        if ($this->isOnline()) {
            return 'متصل الآن';
        }

        return $this->last_activity
            ? 'آخر ظهور: ' . $this->last_activity->timezone('Asia/Gaza')->diffForHumans()
            : 'غير متصل';
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
}
