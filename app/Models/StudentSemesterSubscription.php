<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSemesterSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'academic_year',
        'semester', // term_1, term_2, both
        'region',   // west_bank, gaza
        'amount',
        'paid_amount',
        'status',   // paid, partial, pending, unpaid, waived
        'is_manual',
        'payment_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at'     => 'datetime',
        'is_manual'   => 'boolean',
    ];

    public static function semesterNamesAr(): array
    {
        return [
            'term_1' => 'الفصل الأول',
            'term_2' => 'الفصل الثاني',
            'both'   => 'الفصلين معاً (العام كامل)',
        ];
    }

    public static function semesterNames(): array
    {
        if (app()->getLocale() === 'en') {
            return [
                'term_1' => 'First Semester',
                'term_2' => 'Second Semester',
                'both'   => 'Both Semesters (Full Year)',
            ];
        }
        return self::semesterNamesAr();
    }

    public function getSemesterNameArAttribute(): string
    {
        return self::semesterNamesAr()[$this->semester] ?? $this->semester;
    }

    public function getRegionNameArAttribute(): string
    {
        return ($this->region === 'gaza') ? 'قطاع غزة 🌿' : 'الضفة الغربية والقدس 🏛️';
    }

    public function getRemainingAmountAttribute(): float
    {
        if ($this->status === 'waived') {
            return 0.00;
        }
        if ($this->status === 'paid' && ((float)($this->paid_amount ?? 0) <= 0)) {
            return 0.00;
        }
        $req = (float) $this->amount;
        $paid = (float) ($this->paid_amount ?? 0);
        return max(0.00, round($req - $paid, 2));
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'paid'    => ['label' => 'مسدد وخالص ✅', 'class' => 'badge-paid', 'color' => '#059669', 'bg' => '#ecfdf5', 'icon' => 'fa-check'],
            'partial' => ['label' => 'دفع جزئي (متبقي) ⚠️', 'class' => 'badge-partial', 'color' => '#b45309', 'bg' => '#fef3c7', 'icon' => 'fa-circle-half-stroke'],
            'pending' => ['label' => 'قيد المراجعة ⏳', 'class' => 'badge-pending', 'color' => '#d97706', 'bg' => '#fffbeb', 'icon' => 'fa-hourglass-half'],
            'waived'  => ['label' => 'إعفاء / منحة 🏷️', 'class' => 'badge-waived', 'color' => '#4f46e5', 'bg' => '#eef2ff', 'icon' => 'fa-tag'],
            default   => ['label' => 'غير مسدد ❌', 'class' => 'badge-unpaid', 'color' => '#dc2626', 'bg' => '#fef2f2', 'icon' => 'fa-xmark'],
        };
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * مزامنة وتحديث سجلات الاشتراكات الفصلية للطالب بناءً على مواده المسجلة ومنطقته الجغرافية
     */
    public static function syncWithStudent(Student $student, string $academicYear = '2026-2027'): \Illuminate\Database\Eloquent\Collection
    {
        $region = $student->resolved_region;
        $isFullWaived = $student->hasDiscount() && $student->custom_discount_percent >= 100;

        $enrollments = $student->enrollments()->with('subject')->get();
        $hasBundleDiscount = $enrollments->count() >= 3;

        foreach ($enrollments as $enrollment) {
            $sub = $enrollment->subject;
            if (!$sub) continue;

            $semester = $enrollment->semester ?: 'both';
            $feeAmount = (float) $sub->getSemesterPrice($semester, $region);

            // تطبيق خصم باقة التوجيهي (15%) عند اختيار 3 مواد أو أكثر
            if ($hasBundleDiscount && $feeAmount > 0) {
                $feeAmount = round($feeAmount * 0.85, 2);
            }

            // تطبيق خصم الطالب إن وُجد
            if ($student->hasDiscount()) {
                if ($student->custom_discount_percent >= 100) {
                    $feeAmount = 0.00;
                } elseif ($student->custom_discount_percent > 0) {
                    $feeAmount = round($feeAmount * (1 - ($student->custom_discount_percent / 100)), 2);
                } elseif ($student->custom_discount_fixed > 0) {
                    $feeAmount = max(0.00, round($feeAmount - $student->custom_discount_fixed, 2));
                }
            }

            // تحديث قيمة الرسوم في قيد الالتحاق
            $enrollment->fee_amount = $feeAmount;
            $enrollment->region_applied = $region;
            $enrollment->save();

            // فحص السجل الفصلي أو إنشائه
            $existing = self::where('student_id', $student->id)
                ->where('subject_id', $sub->id)
                ->where('academic_year', $academicYear)
                ->first();

            if (!$existing) {
                $status = $isFullWaived ? 'waived' : ($enrollment->status === 'active' ? 'paid' : 'unpaid');
                $paidAmt = in_array($status, ['paid']) ? $feeAmount : 0.00;

                self::create([
                    'student_id'    => $student->id,
                    'subject_id'    => $sub->id,
                    'academic_year' => $academicYear,
                    'semester'      => $semester,
                    'region'        => $region,
                    'amount'        => $feeAmount,
                    'paid_amount'   => $paidAmt,
                    'status'        => $status,
                    'is_manual'     => false,
                    'paid_at'       => $status === 'paid' ? now() : null,
                    'notes'         => $isFullWaived ? 'معفى رسمياً - منحة كاملة' : null,
                ]);
            } elseif (!$existing->is_manual) {
                if ($isFullWaived && $existing->status !== 'waived') {
                    $existing->update([
                        'status'      => 'waived',
                        'amount'      => 0.00,
                        'paid_amount' => 0.00,
                        'notes'       => 'معفى رسمياً - منحة كاملة',
                    ]);
                }
            }
        }

        return self::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->with('subject')
            ->get();
    }
}
