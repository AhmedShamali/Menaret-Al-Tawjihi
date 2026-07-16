<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalContent extends Model
{
    /** @use HasFactory<\Database\Factories\EducationalContentFactory> */
    use HasFactory;

    protected $fillable =
    [
        'subject_id',
        'title',
        'type',
        'url_path',
        'channel_name',
        'file_size',
        'order'
    ];

    public function subject() {
        return $this->belongsTo(Subject::class);
}
}
