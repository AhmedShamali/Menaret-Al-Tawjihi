<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'stage_id',
        'title',
        'duration_minutes',
        'starts_at',
        'ends_at',
        'show_result_immediately',
        'is_published',
        'is_active',
        'status'
    ];

    protected $casts = [
        'show_result_immediately' => 'boolean',
        'is_published' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * هل الاختبار مفتوح ومتاح حالياً للطلاب
     */
    public function isOpen(): bool
    {
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }
        return true;
    }

    /**
     * هل الاختبار قادم ولم يبدأ موعده بعد
     */
    public function isUpcoming(): bool
    {
        return $this->starts_at ? now()->lt($this->starts_at) : false;
    }

    /**
     * هل انتهت الفترة الزمنية المحددة للاختبار
     */
    public function isExpired(): bool
    {
        return $this->ends_at ? now()->gt($this->ends_at) : false;
    }

    /**
     * حالة توقيت الاختبار
     */
    public function getTimeStatusAttribute(): string
    {
        if ($this->isUpcoming()) {
            return 'upcoming';
        }
        if ($this->isExpired()) {
            return 'expired';
        }
        if ($this->ends_at) {
            return 'active_limited';
        }
        return 'always_open';
    }

    /**
     * تنسيق التاريخ والوقت بتوقيت القدس مع استبدال ص/م
     */
    public function formatScheduleDateTime(?\Carbon\Carbon $datetime): string
    {
        if (!$datetime) return '';
        $tz = config('app.timezone', 'Asia/Gaza');
        $dt = $datetime->copy()->timezone($tz);
        $timeStr = $dt->format('Y/m/d h:i A');
        return str_replace(['AM', 'PM'], [__('ص'), __('م')], $timeStr);
    }

    /**
     * تنسيق الوقت فقط مع ص/م
     */
    public function formatScheduleTimeOnly(?\Carbon\Carbon $datetime): string
    {
        if (!$datetime) return '';
        $tz = config('app.timezone', 'Asia/Gaza');
        $dt = $datetime->copy()->timezone($tz);
        $timeStr = $dt->format('h:i A');
        return str_replace(['AM', 'PM'], [__('ص'), __('م')], $timeStr);
    }

    /**
     * نص توقيت وساعات فتح الاختبار بشكل واضح وأكاديمي
     */
    public function getFormattedTimingTextAttribute(): string
    {
        $tz = config('app.timezone', 'Asia/Gaza');

        if ($this->starts_at && $this->ends_at) {
            $start = $this->starts_at->copy()->timezone($tz);
            $end = $this->ends_at->copy()->timezone($tz);

            if ($start->isSameDay($end)) {
                return $start->format('Y/m/d') . ' (' . __('من') . ' ' . $this->formatScheduleTimeOnly($start) . ' ' . __('إلى') . ' ' . $this->formatScheduleTimeOnly($end) . ')';
            }

            return __('من:') . ' ' . $this->formatScheduleDateTime($start) . ' ' . __('إلى:') . ' ' . $this->formatScheduleDateTime($end);
        }

        if ($this->starts_at) {
            return __('يبدأ ويفتح في:') . ' ' . $this->formatScheduleDateTime($this->starts_at);
        }

        if ($this->ends_at) {
            return __('مفتوح حتى موعد الإغلاق:') . ' ' . $this->formatScheduleDateTime($this->ends_at);
        }

        return __('متاح للتقديم دائماً (بدون قيود زمنية)');
    }

    /**
     * شارة التوقيت وتفاصيل الفتح
     */
    public function getTimingBadgeDataAttribute(): array
    {
        if ($this->isUpcoming()) {
            return [
                'status' => 'upcoming',
                'class'  => 'badge-upcoming',
                'icon'   => 'fa-regular fa-clock',
                'label'  => __('يفتح في:') . ' ' . $this->formatScheduleDateTime($this->starts_at),
                'is_open'=> false,
            ];
        }

        if ($this->isExpired()) {
            return [
                'status' => 'expired',
                'class'  => 'badge-expired',
                'icon'   => 'fa-solid fa-lock',
                'label'  => __('انتهى موعد الاختبار') . ($this->ends_at ? ' (' . $this->formatScheduleDateTime($this->ends_at) . ')' : ''),
                'is_open'=> false,
            ];
        }

        if ($this->ends_at) {
            return [
                'status' => 'active_limited',
                'class'  => 'badge-limited',
                'icon'   => 'fa-solid fa-hourglass-half',
                'label'  => __('مفتوح حالياً - ينتهي:') . ' ' . $this->formatScheduleDateTime($this->ends_at),
                'is_open'=> true,
            ];
        }

        return [
            'status' => 'always_open',
            'class'  => 'badge-always-open',
            'icon'   => 'fa-solid fa-bolt',
            'label'  => __('متاح للتقديم دائماً'),
            'is_open'=> true,
        ];
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function submissions()
    {
        return $this->hasMany(ExamSubmission::class);
    }
}
