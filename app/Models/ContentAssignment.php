<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentAssignment extends Model
{
    protected $fillable = [
        'enrollment_id',
        'educational_content_id',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(EducationalContent::class, 'educational_content_id');
    }
}
