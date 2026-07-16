<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name_ar', 'name_en', 'nid', 'email', 'password',
        'age', 'gender', 'phone', 'whatsapp', 'photo',
        'id_photo', 'stage_id', 'status'
    ];

    // حلقة الوصل مع الصف الدراسي
    public function stage() {
        return $this->belongsTo(Stage::class, 'stage_id');
    }
}
