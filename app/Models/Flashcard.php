<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_name',
        'branch',
        'category',
        'front_text',
        'back_text',
        'difficulty',
    ];
}
