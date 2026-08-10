<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'admin_id',
        'teacher_id',
        'sender_type',
        'message',
        'is_read',
    ];

    /**
     * العلاقة مع موديل الطالب
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * العلاقة مع موديل الأدمن/المستخدم
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * العلاقة مع موديل المدرس
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
