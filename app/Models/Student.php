<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name_ar', 'name_en', 'nid', 'email', 'password', 'plain_password', 'age', 'gender', 'phone', 'whatsapp', 'photo', 'id_photo', 'stage_id', 'monthly_fee', 'status', 'approved_at', 'freeze_reason',
        'city', 'school_name', 'guardian_phone',
        'streak_count', 'last_activity_date', 'total_points',
        'custom_discount_percent', 'custom_discount_fixed', 'discount_notes',
        'google_id', 'provider', 'provider_id', 'avatar_url'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'monthly_fee' => 'decimal:2',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function stage() {
        return $this->belongsTo(Stage::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledSubjects()
    {
        return $this->belongsToMany(Subject::class, 'enrollments', 'student_id', 'subject_id')
                    ->withPivot('status', 'access_mode', 'payment_status', 'activated_at')
                    ->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function examSubmissions()
    {
        return $this->hasMany(ExamSubmission::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'student_id');
    }

    /**
     * تسجيل الالتزام اليومي وحساب الـ Streak والنقاط التحفيزية
     */
    public function recordDailyStreak()
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        if ($this->last_activity_date === $today) {
            return $this->streak_count;
        }

        if ($this->last_activity_date === $yesterday) {
            $this->streak_count += 1;
            $this->total_points += 15;
        } else {
            $this->streak_count = 1;
            $this->total_points += 10;
        }

        $this->last_activity_date = $today;
        $this->save();

        return $this->streak_count;
    }

    /**
     * الاسم الكامل للطالب (عربي كأولوية أولى)
     */
    public function getNameAttribute(): string
    {
        return $this->name_ar ?: ($this->name_en ?: 'طالب التوجيهي');
    }

    /**
     * هل يمتلك الطالب خصماً خاصاً معتمداً من الإدارة؟
     */
    public function hasDiscount(): bool
    {
        return ((float)($this->custom_discount_percent ?? 0) > 0) || ((float)($this->custom_discount_fixed ?? 0) > 0);
    }

    /**
     * نص توصيفي أنيق للخصم
     */
    public function getDiscountLabelAttribute(): string
    {
        $percent = (float)($this->custom_discount_percent ?? 0);
        $fixed = (float)($this->custom_discount_fixed ?? 0);

        if ($percent >= 100) {
            return 'إعفاء كامل 100%';
        }
        if ($percent > 0) {
            return 'خصم ' . round($percent) . '%';
        }
        if ($fixed > 0) {
            return 'خصم ' . round($fixed) . ' ₪';
        }
        return 'بدون خصم';
    }

    /**
     * حساب قيمة الخصم لأي مبلغ محدد
     */
    public function calculateDiscount(float $amount): float
    {
        if ($amount <= 0) {
            return 0.0;
        }

        $percent = (float)($this->custom_discount_percent ?? 0);
        $fixed = (float)($this->custom_discount_fixed ?? 0);

        if ($percent >= 100) {
            return (float)$amount;
        }

        if ($percent > 0) {
            return round($amount * ($percent / 100.0), 2);
        }

        if ($fixed > 0) {
            return min((float)$amount, round($fixed, 2));
        }

        return 0.0;
    }

    /**
     * رابط الصورة الشخصية مع بديل تلقائي
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        if (!empty($this->avatar_url)) {
            return $this->avatar_url;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name_ar ?? 'طالب') . '&background=0284c7&color=fff&size=200&bold=true';
    }

    /**
     * رابط صورة الهوية الفلسطينية
     */
    public function getIdPhotoUrlAttribute(): ?string
    {
        if ($this->id_photo) {
            return asset('storage/' . $this->id_photo);
        }
        return null;
    }

    /**
     * هل رفع الطالب صورة هويته؟
     */
    public function getHasIdPhotoAttribute(): bool
    {
        return !empty($this->id_photo);
    }

    /**
     * الاشتراكات الشهرية للطالب على مدار السنة
     */
    public function monthlySubscriptions()
    {
        return $this->hasMany(\App\Models\StudentMonthlySubscription::class)->orderBy('month');
    }

    /**
     * حساب رقم الشهر الدراسي المنقضي للطالب بناءً على تاريخ اعتماده بالمنظومة
     * يبدأ العد (الشهر 1) من تاريخ الاعتماد approved_at، وكل 30 يوماً يدخل الطالب في شهر دراسي جديد.
     */
    public function currentAcademicMonthIndex(): int
    {
        $startDate = $this->approved_at ?? $this->created_at;
        if (!$startDate) {
            return 1;
        }

        $days = (int) $startDate->diffInDays(now());
        $monthIndex = (int) floor($days / 30) + 1;

        return min(12, max(1, $monthIndex));
    }

    /**
     * عدد الشهور المسددة أو المعفاة فعلياً للطالب في العام الأكاديمي
     */
    public function paidMonthsCount(?string $academicYear = null): int
    {
        $year = $academicYear ?? '2026-2027';

        return $this->monthlySubscriptions()
            ->where('academic_year', $year)
            ->whereIn('status', ['paid', 'waived'])
            ->count();
    }

    /**
     * هل يستحق على الطالب سداد قسط شهر جديد أو متأخرات سابقة؟
     */
    public function isMonthlyFeeDue(?string $academicYear = null): bool
    {
        if ($this->hasDiscount() && $this->custom_discount_percent >= 100) {
            return false;
        }

        $summary = $this->getFinancialSummary($academicYear);
        return ($summary['total_due_now'] ?? 0) > 0;
    }

    /**
     * رقم الشهر المستحق سداده حالياً (مع مراعاة أي شهر سابق به متبقي)
     */
    public function currentDueMonth(?string $academicYear = null): int
    {
        $summary = $this->getFinancialSummary($academicYear);
        return $summary['active_due_month'] ?? min(12, max(1, $this->paidMonthsCount($academicYear) + 1));
    }

    /**
     * اسم الشهر المستحق سداده حالياً (مثلاً: "الشهر الثاني")
     */
    public function currentDueMonthName(?string $academicYear = null): string
    {
        $monthNum = $this->currentDueMonth($academicYear);
        return \App\Models\StudentMonthlySubscription::monthNamesAr()[$monthNum] ?? "الشهر {$monthNum}";
    }

    /**
     * كشف الحساب والبيان المالي الشامل للطالب (المتأخرات السابقة + قسط الشهر الحالي + الإجمالي المطلوب)
     */
    public function getFinancialSummary(?string $academicYear = null): array
    {
        $year = $academicYear ?? '2026-2027';

        if ($this->monthlySubscriptions()->where('academic_year', $year)->count() < 12) {
            \App\Models\StudentMonthlySubscription::syncWithStudentPayments($this, $year);
        }

        $subscriptions = $this->monthlySubscriptions()
            ->where('academic_year', $year)
            ->orderBy('month')
            ->get();

        $currentCalMonth = $this->currentAcademicMonthIndex();

        $arrearsList = [];
        $previousUnpaidBalance = 0.0;
        $currentMonthDue = 0.0;
        $activeDueMonthNum = $currentCalMonth;

        // البحث عن أول شهر يحتوي على رصيد متبقي غير مسدد
        $firstIncompleteSub = $subscriptions->first(function ($s) {
            return !in_array($s->status, ['paid', 'waived']) && ((float)$s->remaining_amount > 0);
        });

        if ($firstIncompleteSub) {
            $activeDueMonthNum = $firstIncompleteSub->month;
        }

        // تقسيم الشهور: الشهور السابقة للشهر الحالي تعتبر متأخرات، والشهر الحالي يعتبر القسط النشط
        foreach ($subscriptions as $s) {
            if ($s->status === 'waived') continue;
            $rem = (float) $s->remaining_amount;
            if ($rem <= 0) continue;

            if ($s->month < $currentCalMonth) {
                // متأخرات مستحقة من شهور سابقة
                $previousUnpaidBalance += $rem;
                $arrearsList[] = [
                    'month'            => $s->month,
                    'name'             => $s->month_name_ar,
                    'month_name'       => $s->month_name_ar,
                    'status'           => $s->status,
                    'amount'           => (float) $s->amount,
                    'paid_amount'      => (float) $s->paid_amount,
                    'remaining'        => $rem,
                    'remaining_amount' => $rem,
                ];
            } elseif ($s->month == $currentCalMonth) {
                // قسط الشهر الحالي
                $currentMonthDue = $rem;
            }
        }

        // تحديد الشهر النشط للدفع
        if (!empty($arrearsList)) {
            $activeDueMonthNum = $arrearsList[0]['month'];
        } elseif ($currentMonthDue > 0) {
            $activeDueMonthNum = $currentCalMonth;
        } else {
            // جميع الشهور المنقضية والحالية مسددة بالكامل
            $activeDueMonthNum = $firstIncompleteSub ? $firstIncompleteSub->month : min(12, $currentCalMonth + 1);
        }

        $totalDueNow = round($previousUnpaidBalance + $currentMonthDue, 2);

        // إجمالي العام الدراسي
        $totalYearDue = (float) $subscriptions->where('status', '!=', 'waived')->sum('amount');
        $totalYearPaid = (float) $subscriptions->sum(function ($s) {
            if ($s->status === 'waived') return 0.0;
            if ($s->status === 'paid' && ((float)($s->paid_amount ?? 0) <= 0)) return (float)$s->amount;
            return (float)($s->paid_amount ?? 0);
        });
        $totalYearRemaining = max(0.0, round($totalYearDue - $totalYearPaid, 2));

        $paidMonthsCount = $subscriptions->whereIn('status', ['paid', 'waived'])->count();
        $partialMonthsCount = $subscriptions->where('status', 'partial')->count();
        $unpaidMonthsCount = $subscriptions->where('status', 'unpaid')->count();
        $pendingMonthsCount = $subscriptions->where('status', 'pending')->count();

        $monthNamesAr = \App\Models\StudentMonthlySubscription::monthNamesAr();
        $activeDueMonthName = $monthNamesAr[$activeDueMonthNum] ?? "الشهر {$activeDueMonthNum}";
        $currentCalMonthName = $monthNamesAr[$currentCalMonth] ?? "الشهر {$currentCalMonth}";

        return [
            'academic_year'           => $year,
            'current_academic_month'  => $currentCalMonth,
            'current_academic_name'   => $currentCalMonthName,
            'active_due_month'        => $activeDueMonthNum,
            'active_due_month_name'   => $activeDueMonthName,
            'has_arrears'             => $previousUnpaidBalance > 0,
            'previous_unpaid_balance' => round($previousUnpaidBalance, 2),
            'arrears_details'         => $arrearsList,
            'current_month_due'       => round($currentMonthDue, 2),
            'total_due_now'           => $totalDueNow,
            'total_year_due'          => round($totalYearDue, 2),
            'total_year_paid'         => round($totalYearPaid, 2),
            'total_year_remaining'    => $totalYearRemaining,
            'paid_months_count'       => $paidMonthsCount,
            'partial_months_count'    => $partialMonthsCount,
            'unpaid_months_count'     => $unpaidMonthsCount,
            'pending_months_count'    => $pendingMonthsCount,
            'is_fully_paid'           => $totalYearRemaining <= 0,
            'subscriptions'           => $subscriptions,
        ];
    }

    /**
     * احتساب التفصيل المالي الدقيق للمواد والاشتراك الشهري للطالب
     */
    public function getFeeBreakdown(): array
    {
        $enrollments = $this->enrollments()->with('subject.stage')->get();
        $items = [];
        $subtotal = 0;

        if ($enrollments->isNotEmpty()) {
            foreach ($enrollments as $e) {
                $sub = $e->subject;
                if (!$sub) continue;
                $price = (float) $sub->effective_price;
                $subtotal += $price;
                $items[] = [
                    'id'         => $sub->id,
                    'name_ar'    => $sub->name_ar,
                    'name_en'    => $sub->name_en ?? $sub->name_ar,
                    'icon'       => $sub->icon ?? '📘',
                    'stage'      => optional($sub->stage)->name_ar ?? 'توجيهي',
                    'price'      => $price,
                    'orig_price' => (float) $sub->price_ils,
                    'is_free'    => (bool) $sub->is_free,
                ];
            }
        } elseif ($this->stage) {
            $stageSubjects = $this->stage->subjects()->get();
            if ($stageSubjects->isNotEmpty()) {
                foreach ($stageSubjects as $sub) {
                    $price = (float) $sub->effective_price;
                    $subtotal += $price;
                    $items[] = [
                        'id'         => $sub->id,
                        'name_ar'    => $sub->name_ar,
                        'name_en'    => $sub->name_en ?? $sub->name_ar,
                        'icon'       => $sub->icon ?? '📘',
                        'stage'      => optional($sub->stage)->name_ar ?? 'توجيهي',
                        'price'      => $price,
                        'orig_price' => (float) $sub->price_ils,
                        'is_free'    => (bool) $sub->is_free,
                    ];
                }
            }
        }

        $bundleDiscount = (count($items) >= 3 && $subtotal > 0) ? round($subtotal * 0.15, 2) : 0;
        $baseAfterBundle = max(0, $subtotal - $bundleDiscount);

        // إذا لم توجد مواد مسجلة أو محددة، الاعتماد على القسط الشهري المحدد للطالب أو الإعداد العام
        if (empty($items)) {
            $baseAfterBundle = (float) ($this->monthly_fee ?: \App\Models\Setting::get('default_monthly_fee', 150.00));
            $subtotal = $baseAfterBundle;
        }

        // حساب الخصم أو المنحة المخصصة للطالب من قِبل الإدارة
        $studentDiscount = 0;
        $studentDiscountLabel = null;
        $customPercent = (float) ($this->custom_discount_percent ?? 0);
        $customFixed = (float) ($this->custom_discount_fixed ?? 0);

        if ($customPercent >= 100) {
            $studentDiscount = $baseAfterBundle;
            $studentDiscountLabel = 'إعفاء ومنحة كاملة 100%';
        } elseif ($customPercent > 0) {
            $studentDiscount = round($baseAfterBundle * ($customPercent / 100), 2);
            $studentDiscountLabel = 'منحة دراسية خاصة (' . round($customPercent) . '%)';
        } elseif ($customFixed > 0) {
            $studentDiscount = min($baseAfterBundle, round($customFixed, 2));
            $studentDiscountLabel = 'خصم خاص (' . round($customFixed) . ' ₪)';
        }

        $finalAmount = max(0, round($baseAfterBundle - $studentDiscount, 2));

        return [
            'items'                  => $items,
            'subtotal'               => round($subtotal, 2),
            'bundle_discount'        => round($bundleDiscount, 2),
            'base_after_bundle'      => round($baseAfterBundle, 2),
            'student_discount'       => round($studentDiscount, 2),
            'student_discount_label' => $studentDiscountLabel,
            'custom_percent'         => $customPercent,
            'custom_fixed'           => $customFixed,
            'final_amount'           => $finalAmount,
            'count'                  => count($items),
        ];
    }

    /**
     * القسط الشهري الأساسي قبل خصم الطالب (إما بناء على المواد أو القسط المحدد)
     */
    public function calculateBaseMonthlyFee(): float
    {
        // إذا كان هناك تسجيلات مواد فعلية للطالب، يتم احتسابها بدقة
        if ($this->enrollments()->exists()) {
            $breakdown = $this->getFeeBreakdown();
            return (float) $breakdown['base_after_bundle'];
        }

        return (float) ($this->monthly_fee ?: \App\Models\Setting::get('default_monthly_fee', 150.00));
    }

    /**
     * قيمة الرسوم الشهرية الصافية المستحقة بعد تطبيق الخصم
     */
    public function monthlyAmountDue(): float
    {
        $baseFee = $this->calculateBaseMonthlyFee();

        if ($this->hasDiscount()) {
            if ($this->custom_discount_percent > 0) {
                $baseFee = $baseFee * (1 - ($this->custom_discount_percent / 100));
            } elseif ($this->custom_discount_fixed > 0) {
                $baseFee = max(0, $baseFee - $this->custom_discount_fixed);
            }
        }

        return max(0, round($baseFee, 2));
    }
}

