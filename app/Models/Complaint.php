<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'type',
        'category',
        'subject',
        'message',
        'reply',
        'replied_at',
        'status',
        'admin_notes',
    ];

    public function getCategoryAttribute($val)
    {
        return $val ?: ($this->attributes['type'] ?? 'استفسار عام');
    }
}
