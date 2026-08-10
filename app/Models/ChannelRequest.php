<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelRequest extends Model
{
    // الحقول المسموح بتعبئتها
    protected $fillable = ['student_id', 'channel_name', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
