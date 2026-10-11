<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_type',
        'actor_id',
        'title',
        'category',
        'symbol_mode',
    ];

    public function messages()
    {
        return $this->hasMany(AiMessage::class, 'conversation_id')->orderBy('id', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(AiMessage::class, 'conversation_id')->latestOfMany();
    }
}
