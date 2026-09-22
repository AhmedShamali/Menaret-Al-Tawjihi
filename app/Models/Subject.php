<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;


    protected $fillable = [
        'stage_id',
        'user_id',
        'teacher_id',
        'teacher_name',
        'name_ar',
        'subject_key',
        'icon',
        'color',
        'price_ils',
        'discount_price_ils',
        'is_free',
        'description'
    ];

    /**
     * هل يوجد خصم ترويجي ساري للمادة؟
     */
    public function getHasDiscountAttribute(): bool
    {
        return !$this->is_free && $this->discount_price_ils !== null && (float)$this->discount_price_ils > 0 && (float)$this->discount_price_ils < (float)$this->price_ils;
    }

    /**
     * نسبة الخصم المئوية المحسوبة (%)
     */
    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->has_discount || empty($this->price_ils) || (float)$this->price_ils <= 0) {
            return 0;
        }
        $discountAmount = (float)$this->price_ils - (float)$this->discount_price_ils;
        return (int) round(($discountAmount / (float)$this->price_ils) * 100);
    }

    /**
     * السعر بعد الخصم بالشيكل (₪)
     */
    public function getPriceAfterDiscountAttribute(): float
    {
        return $this->getEffectivePriceAttribute();
    }

    /**
     * قيمة الخصم المالي المباشر (₪)
     */
    public function getDiscountAmountAttribute(): float
    {
        if ($this->has_discount) {
            return max(0.00, round((float)$this->price_ils - (float)$this->discount_price_ils, 2));
        }
        return 0.00;
    }

    /**
     * حساب السعر الفعلي للمادة بعد الخصومات أو المجانية
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->is_free) {
            return 0.00;
        }
        if ($this->discount_price_ils !== null && $this->discount_price_ils > 0) {
            return (float) $this->discount_price_ils;
        }
        return (float) ($this->price_ils ?? 150.00);
    }

    /**
     * اسم معلّم المادة المعتمد فقط، أو رسالة تشويقية راقية توحي بأنه سيتوفر قريباً
     */
    public function getTeacherDisplayNameAttribute(): string
    {
        // 1. إذا تم تحديد اسم المعلم يدوياً
        if (!empty($this->teacher_name)) {
            return $this->teacher_name;
        }

        // 2. التحقق من علاقة المعلم وأن دوره الفعلي معلم (وليس مديراً للنظام)
        if ($this->teacher && $this->teacher->role === 'teacher') {
            return $this->teacher->name_ar ?? $this->teacher->name;
        }

        // 3. التحقق من حقل teacher_id
        if (!empty($this->teacher_id)) {
            $tUser = User::find($this->teacher_id);
            if ($tUser && $tUser->role === 'teacher') {
                return $tUser->name_ar ?? $tUser->name;
            }
        }

        // 4. التحقق من وجود معلم مرتبط بهذه المادة عبر subject_id
        $linkedTeacher = User::where('role', 'teacher')->where('subject_id', $this->id)->first();
        if ($linkedTeacher) {
            return $linkedTeacher->name_ar ?? $linkedTeacher->name;
        }

        // 5. إذا لم يتم تعيين معلم بعد: رسالة راقية وجميلة
        return 'قريباً • نخبة معلّمي التوجيهي ⏳';
    }

    /**
     * هل تم تعيين معلّم فعلي للمادة؟
     */
    public function hasAssignedTeacher(): bool
    {
        if (!empty($this->teacher_name)) {
            return true;
        }
        if ($this->teacher && $this->teacher->role === 'teacher') {
            return true;
        }
        if (!empty($this->teacher_id)) {
            $tUser = User::find($this->teacher_id);
            if ($tUser && $tUser->role === 'teacher') {
                return true;
            }
        }
        return User::where('role', 'teacher')->where('subject_id', $this->id)->exists();
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contents()
    {
        return $this->hasMany(EducationalContent::class);
    }

    public function educationalContents()
    {
        return $this->hasMany(EducationalContent::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}

