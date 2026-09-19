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
        'status',
        'payment_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'month'   => 'integer',
        'paid_at' => 'datetime',
    ];

    public static function monthNamesAr(): array
    {
        return [
            1  => 'الشهر الأول',
            2  => 'الشهر الثاني',
            3  => 'الشهر الثالث',
            4  => 'الشهر الرابع',
            5  => 'الشهر الخامس',
            6  => 'الشهر السادس',
            7  => 'الشهر السابع',
            8  => 'الشهر الثامن',
            9  => 'الشهر التاسع',
            10 => 'الشهر العاشر',
            11 => 'الشهر الحادي عشر',
            12 => 'الشهر الثاني عشر',
        ];
    }

    public static function monthNames(): array
    {
        if (app()->getLocale() === 'en') {
            return [
                1  => 'Month 1',
                2  => 'Month 2',
                3  => 'Month 3',
                4  => 'Month 4',
                5  => 'Month 5',
                6  => 'Month 6',
                7  => 'Month 7',
                8  => 'Month 8',
                9  => 'Month 9',
                10 => 'Month 10',
                11 => 'Month 11',
                12 => 'Month 12',
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

        $completedCount = $completedPayments->count();
        $pendingCount = $pendingPayments->count();

        // إن كان الطالب مفعلاً بالمنصة (Active)، يعتبر الشهر الأول مسدداً كحد أدنى عند التسجيل
        $paidMonthsCount = max($completedCount, ($student->status === 'active' ? 1 : 0));

        for ($m = 1; $m <= 12; $m++) {
            $sub = $existing->get($m);

            if ($isFullWaived) {
                $status = 'waived';
                $amount = 0.00;
                $notes = 'معفى رسمياً - منحة دراسية كاملة 100%';
                $paymentId = null;
                $paidAt = null;
            } elseif ($m <= $paidMonthsCount) {
                $status = 'paid';
                $amount = $baseAmount;
                $pIndex = $m - 1;
                $payment = $completedPayments->get($pIndex);
                if ($payment) {
                    $paymentId = $payment->id;
                    $paidAt = $payment->updated_at ?? $payment->created_at;
                    $notes = "مسدد ومعتمد (" . ($payment->gateway_name_ar) . ")";
                } else {
                    $paymentId = null;
                    $paidAt = $student->created_at ?? now();
                    $notes = 'تم السداد واعتماد الاشتراك عند بداية التسجيل';
                }
            } elseif ($m <= ($paidMonthsCount + $pendingCount)) {
                $status = 'pending';
                $amount = $baseAmount;
                $pendIndex = ($m - $paidMonthsCount) - 1;
                $pendPayment = $pendingPayments->get($pendIndex);
                $paymentId = $pendPayment ? $pendPayment->id : null;
                $paidAt = null;
                $notes = 'إشعار الدفع قيد المراجعة والاعتماد من الإدارة';
            } else {
                $status = $sub ? $sub->status : 'unpaid';
                if ($status === 'paid' && $m > $paidMonthsCount) {
                    $status = 'paid';
                } elseif ($status !== 'waived') {
                    $status = 'unpaid';
                }
                $amount = $baseAmount;
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
                    'status'        => $status,
                    'payment_id'    => $paymentId,
                    'paid_at'       => $paidAt,
                    'notes'         => $notes,
                ]);
            } else {
                if (($sub->status === 'unpaid' && in_array($status, ['paid', 'pending', 'waived'])) ||
                    ($m === 1 && $student->status === 'active' && $sub->status === 'unpaid')) {
                    $sub->update([
                        'status'     => $status,
                        'payment_id' => $paymentId ?? $sub->payment_id,
                        'paid_at'    => $paidAt ?? $sub->paid_at,
                        'notes'      => $notes ?? $sub->notes,
                    ]);
                }
            }
        }

        return self::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->orderBy('month')
            ->get();
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'paid'    => ['label' => 'مسدد وخالص ✅', 'class' => 'badge-paid', 'color' => '#059669', 'bg' => '#ecfdf5'],
            'pending' => ['label' => 'قيد المراجعة ⏳', 'class' => 'badge-pending', 'color' => '#d97706', 'bg' => '#fffbeb'],
            'waived'  => ['label' => 'إعفاء / منحة 🏷️', 'class' => 'badge-waived', 'color' => '#4f46e5', 'bg' => '#eef2ff'],
            default   => ['label' => 'غير مسدد ❌', 'class' => 'badge-unpaid', 'color' => '#dc2626', 'bg' => '#fef2f2'],
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
