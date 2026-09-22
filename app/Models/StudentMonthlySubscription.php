<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMonthlySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year',
        'month',
        'amount',
        'paid_amount',
        'status',
        'is_manual',
        'payment_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'month'       => 'integer',
        'paid_at'     => 'datetime',
        'is_manual'   => 'boolean',
    ];

    public static function monthNamesAr(): array
    {
        return [
            1  => '1- الشهر الأول',
            2  => '2- الشهر الثاني',
            3  => '3- الشهر الثالث',
            4  => '4- الشهر الرابع',
            5  => '5- الشهر الخامس',
            6  => '6- الشهر السادس',
            7  => '7- الشهر السابع',
            8  => '8- الشهر الثامن',
            9  => '9- الشهر التاسع',
            10 => '10- الشهر العاشر',
            11 => '11- الشهر الحادي عشر',
            12 => '12- الشهر الثاني عشر',
        ];
    }

    public static function monthNames(): array
    {
        if (app()->getLocale() === 'en') {
            return [
                1  => '1- Month 1',
                2  => '2- Month 2',
                3  => '3- Month 3',
                4  => '4- Month 4',
                5  => '5- Month 5',
                6  => '6- Month 6',
                7  => '7- Month 7',
                8  => '8- Month 8',
                9  => '9- Month 9',
                10 => '10- Month 10',
                11 => '11- Month 11',
                12 => '12- Month 12',
            ];
        }
        return self::monthNamesAr();
    }

    public function getMonthNameArAttribute(): string
    {
        return self::monthNamesAr()[$this->month] ?? "الشهر {$this->month}";
    }

    /**
     * مزامنة وتحديث سجلات الشهور الـ 12 للطالب تلقائياً وربطها بعمليات الدفع وحالة التسجيل
     * مع حماية كاملة لأي شهر تم تعديله يدوياً من قبل الإدارة
     */
    public static function syncWithStudentPayments(Student $student, string $academicYear = '2026-2027'): \Illuminate\Database\Eloquent\Collection
    {
        $existing = self::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $baseAmount = $student->monthlyAmountDue();
        if ($baseAmount <= 0) {
            $baseAmount = (float)($student->monthly_fee ?: 150.00);
        }

        $isFullWaived = $student->hasDiscount() && $student->custom_discount_percent >= 100;

        // جلب دفعات الطالب المعتمدة وقيد المراجعة
        $completedPayments = Payment::where('student_id', $student->id)
            ->where('status', 'completed')
            ->orderBy('created_at')
            ->get();

        $pendingPayments = Payment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $totalCompletedAmount = (float) $completedPayments->sum('amount');
        $totalPendingAmount = (float) $pendingPayments->sum('amount');

        $completedCount = $completedPayments->count();
        $pendingCount = $pendingPayments->count();

        // حساب عدد الشهور التي تغطيها المبالغ المدفوعة فعلياً
        $monthsCoveredByPaidAmount = ($baseAmount > 0) ? (int) floor($totalCompletedAmount / $baseAmount) : 0;
        $paidMonthsCount = max($monthsCoveredByPaidAmount, $completedCount, ($student->status === 'active' ? 1 : 0));

        // حساب عدد الشهور التي تغطيها إشعارات الدفع قيد المراجعة
        $monthsCoveredByPendingAmount = ($baseAmount > 0) ? (int) ceil($totalPendingAmount / $baseAmount) : 0;
        $totalPendingMonths = max($monthsCoveredByPendingAmount, $pendingCount);

        for ($m = 1; $m <= 12; $m++) {
            $sub = $existing->get($m);

            // الحماية الذهبية: إذا كان السجل موجوداً ومعدلاً يدوياً من قبل الإدارة، لا يتم لمسه إطلاقاً!
            if ($sub && !empty($sub->is_manual)) {
                continue;
            }

            if ($isFullWaived) {
                $status = 'waived';
                $amount = 0.00;
                $paidAmount = 0.00;
                $notes = 'معفى رسمياً - منحة دراسية كاملة 100%';
                $paymentId = null;
                $paidAt = null;
            } elseif ($sub && in_array($sub->status, ['paid', 'waived'])) {
                // إذا كان الشهر محفوظاً كمسدد أو معفى، نحافظ على حالته تماماً
                $status = $sub->status;
                $amount = $sub->amount ?: $baseAmount;
                $paidAmount = ($sub->status === 'paid') ? ((float)($sub->paid_amount ?: $amount)) : 0.00;
                $paymentId = $sub->payment_id;
                $paidAt = $sub->paid_at ?? now();
                $notes = $sub->notes ?: 'معتمد ومسدد بقرار الإدارة';
            } elseif (!$sub && $m <= $paidMonthsCount) {
                // تهيئة أولية فقط إذا لم يكن السجل موجوداً في قاعدة البيانات
                $status = 'paid';
                $amount = $baseAmount;
                $paidAmount = $baseAmount;
                $pIndex = $m - 1;
                $payment = $completedPayments->get($pIndex);
                if ($payment) {
                    $paymentId = $payment->id;
                    $paidAt = $payment->updated_at ?? $payment->created_at;
                    $notes = "مسدد ومعتمد (" . ($payment->gateway_name_ar) . ")";
                } else {
                    $paymentId = null;
                    $paidAt = $student->created_at ?? now();
                    $notes = 'تم السداد واعتماد الاشتراك الشهري';
                }
            } elseif (!$sub && $m <= ($paidMonthsCount + $totalPendingMonths)) {
                // تهيئة أولية لإشعار قيد المراجعة
                $status = 'pending';
                $amount = $baseAmount;
                $paidAmount = 0.00;
                $pendIndex = ($m - $paidMonthsCount) - 1;
                $pendPayment = $pendingPayments->get($pendIndex);
                $paymentId = $pendPayment ? $pendPayment->id : null;
                $paidAt = null;
                $notes = 'إشعار الدفع قيد المراجعة والاعتماد من الإدارة';
            } else {
                // الحفاظ على حالة السجل الحالية إذا كان موجوداً، أو جعله غير مسدد إذا كان جديداً
                $status = $sub ? $sub->status : 'unpaid';
                $amount = $sub ? $sub->amount : $baseAmount;
                $paidAmount = $sub ? (float)($sub->paid_amount ?? 0.00) : 0.00;
                $paymentId = $sub ? $sub->payment_id : null;
                $paidAt = $sub ? $sub->paid_at : null;
                $notes = $sub ? $sub->notes : null;
            }

            if (!$sub) {
                self::create([
                    'student_id'    => $student->id,
                    'academic_year' => $academicYear,
                    'month'         => $m,
                    'amount'        => $amount,
                    'paid_amount'   => $paidAmount,
                    'status'        => $status,
                    'is_manual'     => false,
                    'payment_id'    => $paymentId,
                    'paid_at'       => $paidAt,
                    'notes'         => $notes,
                ]);
            } else {
                // إذا كان السجل موجوداً وغير يدوي، نحدّثه فقط في حالة المنحة الكاملة 100%
                if ($isFullWaived && $sub->status !== 'waived') {
                    $sub->update([
                        'status'      => 'waived',
                        'amount'      => 0.00,
                        'paid_amount' => 0.00,
                        'notes'       => 'معفى رسمياً - منحة دراسية كاملة 100%',
                    ]);
                }
            }
        }

        return self::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->orderBy('month')
            ->get();
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
            'partial' => ['label' => 'دفع جزئي (متبقي عليه) ⚠️', 'class' => 'badge-partial', 'color' => '#b45309', 'bg' => '#fef3c7', 'icon' => 'fa-circle-half-stroke'],
            'pending' => ['label' => 'قيد المراجعة ⏳', 'class' => 'badge-pending', 'color' => '#d97706', 'bg' => '#fffbeb', 'icon' => 'fa-hourglass-half'],
            'waived'  => ['label' => 'إعفاء / منحة 🏷️', 'class' => 'badge-waived', 'color' => '#4f46e5', 'bg' => '#eef2ff', 'icon' => 'fa-tag'],
            default   => ['label' => 'غير مسدد ❌', 'class' => 'badge-unpaid', 'color' => '#dc2626', 'bg' => '#fef2f2', 'icon' => 'fa-xmark'],
        };
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
