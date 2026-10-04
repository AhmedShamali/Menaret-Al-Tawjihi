<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name_ar', 'name_en', 'nid', 'email', 'password', 'plain_password', 'age', 'gender', 'phone', 'whatsapp', 'photo', 'id_photo', 'stage_id', 'monthly_fee', 'status', 'approved_at', 'freeze_reason',
        'city', 'region', 'school_name', 'guardian_phone',
        'streak_count', 'last_activity_date', 'total_points',
        'custom_discount_percent', 'custom_discount_fixed', 'discount_notes',
        'google_id', 'provider', 'provider_id', 'avatar_url'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'monthly_fee' => 'decimal:2',
    ];

    protected $hidden = [
        'password', 'plain_password', 'remember_token',
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
     * استنتاج المنطقة التعليمية (غزة / الضفة) بدقة من حقل المدينة
     */
    public static function inferRegionFromCity(?string $city = null): string
    {
        $city = trim($city ?? '');
        if (empty($city)) {
            return 'west_bank'; // الافتراضي
        }

        $gazaKeywords = ['غزة', 'شمال غزة', 'خان يونس', 'رفح', 'دير البلح', 'الوسطى', 'جباليا', 'بيت لاهيا', 'بيت حانون'];
        foreach ($gazaKeywords as $kw) {
            if (mb_strpos($city, $kw) !== false) {
                return 'gaza';
            }
        }

        return 'west_bank';
    }

    /**
     * المنطقة التعليمية المحسومة للطالب (إما المسجلة مباشرة أو المستنتجة من المدينة)
     */
    public function getResolvedRegionAttribute(): string
    {
        $reg = trim($this->region ?? '');
        if (!empty($reg) && in_array($reg, ['gaza', 'west_bank'])) {
            return $reg;
        }

        return self::inferRegionFromCity($this->city);
    }

    /**
     * نص المنطقة التعليمية للطالب بالعربية
     */
    public function getRegionLabelAttribute(): string
    {
        return $this->resolved_region === 'gaza' ? 'غزة' : 'الضفة';
    }

    /**
     * بيانات تصميم وشارة المنطقة التعليمية للطالب
     */
    public function getRegionBadgeAttribute(): array
    {
        if ($this->resolved_region === 'gaza') {
            return [
                'label' => 'غزة',
                'bg'    => '#ecfdf5',
                'color' => '#065f46',
                'border'=> '#a7f3d0',
                'icon'  => 'fa-solid fa-location-dot',
            ];
        }

        return [
            'label' => 'الضفة',
            'bg'    => '#eff6ff',
            'color' => '#1e40af',
            'border'=> '#bfdbfe',
            'icon'  => 'fa-solid fa-location-dot',
        ];
    }

    public function isGaza(): bool
    {
        return $this->resolved_region === 'gaza';
    }

    public function isWestBank(): bool
    {
        return $this->resolved_region === 'west_bank';
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
        $name = $this->name_ar ?? $this->name_en ?? 'طالب';
        if (!empty($this->photo)) {
            return \App\Support\MediaHelper::avatarUrl($this->photo, $name, 'student');
        }
        if (!empty($this->avatar_url)) {
            return \App\Support\MediaHelper::avatarUrl($this->avatar_url, $name, 'student');
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=0284c7&color=fff&size=200&bold=true';
    }

    /**
     * رابط صورة الهوية الفلسطينية
     */
    public function getIdPhotoUrlAttribute(): ?string
    {
        if (!empty($this->id_photo)) {
            return \App\Support\MediaHelper::url($this->id_photo);
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
     * هل وثيقة الهوية المرفقة بصيغة PDF؟
     */
    public function getIsIdPdfAttribute(): bool
    {
        if (empty($this->id_photo)) {
            return false;
        }
        $clean = strtolower(parse_url($this->id_photo, PHP_URL_PATH) ?? $this->id_photo);
        return str_ends_with($clean, '.pdf');
    }

    /**
     * اسم الملف المخصص والمناسب عند تنزيل وثيقة الهوية
     */
    public function getIdPhotoDownloadNameAttribute(): string
    {
        $name = preg_replace('/[^\p{L}\p{N}_\-]/u', '_', $this->name_ar ?: $this->name_en ?: 'student');
        $ext = $this->is_id_pdf ? 'pdf' : (pathinfo($this->id_photo ?? '', PATHINFO_EXTENSION) ?: 'jpg');
        return "وثيقة_هوية_{$this->nid}_{$name}.{$ext}";
    }

    /**
     * الاشتراكات والذمم الفصلية للطالب بالمقررات الدراسية
     */
    public function semesterSubscriptions()
    {
        return $this->hasMany(\App\Models\StudentSemesterSubscription::class);
    }

    /**
     * الاشتراكات الشهرية للطالب على مدار السنة
     */
    public function monthlySubscriptions()
    {
        return $this->hasMany(\App\Models\StudentMonthlySubscription::class)->orderBy('month');
    }

    /**
     * هل يستحق على الطالب سداد رسوم فصل دراسي حالي أو متأخرات؟
     */
    public function isSemesterFeeDue(?string $academicYear = null): bool
    {
        if ($this->hasDiscount() && $this->custom_discount_percent >= 100) {
            return false;
        }

        $summary = $this->getSemesterFinancialSummary($academicYear);
        return ($summary['total_remaining'] ?? 0) > 0;
    }

    /**
     * حساب كشف الحساب والبيان المالي الفصلي الشامل للطالب بناءً على منطقته ومواده
     */
    public function getSemesterFinancialSummary(?string $academicYear = null): array
    {
        $year = $academicYear ?? '2026-2027';
        
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('student_semester_subscriptions')) {
                \App\Models\StudentSemesterSubscription::syncWithStudent($this, $year);

                $subscriptions = $this->semesterSubscriptions()
                    ->where('academic_year', $year)
                    ->with('subject')
                    ->get();
            } else {
                $subscriptions = collect();
            }
        } catch (\Throwable $e) {
            $subscriptions = collect();
        }

        $totalDue = (float) $subscriptions->where('status', '!=', 'waived')->sum('amount');
        $totalPaid = (float) $subscriptions->sum(function ($s) {
            if ($s->status === 'waived') return 0.00;
            if ($s->status === 'paid' && ((float)($s->paid_amount ?? 0) <= 0)) return (float) $s->amount;
            return (float) ($s->paid_amount ?? 0);
        });
        $totalRemaining = max(0.00, round($totalDue - $totalPaid, 2));

        $term1Subs = $subscriptions->filter(fn($s) => in_array($s->semester, ['term_1', 'both']));
        $term2Subs = $subscriptions->filter(fn($s) => in_array($s->semester, ['term_2', 'both']));

        $term1Due = 0.00;
        $term1Paid = 0.00;
        $term2Due = 0.00;
        $term2Paid = 0.00;

        foreach ($subscriptions as $sub) {
            $amt = (float)$sub->amount;
            $pAmt = ($sub->status === 'waived') ? 0.00 : (($sub->status === 'paid' && ((float)($sub->paid_amount ?? 0) <= 0)) ? $amt : (float)($sub->paid_amount ?? 0));

            if ($sub->semester === 'term_1') {
                $term1Due += $amt;
                $term1Paid += $pAmt;
            } elseif ($sub->semester === 'term_2') {
                $term2Due += $amt;
                $term2Paid += $pAmt;
            } else { // both
                $halfAmt = round($amt / 2, 2);
                $halfPaid = round($pAmt / 2, 2);
                $term1Due += $halfAmt;
                $term1Paid += $halfPaid;
                $term2Due += round($amt - $halfAmt, 2);
                $term2Paid += round($pAmt - $halfPaid, 2);
            }
        }

        $term1Remaining = max(0.00, round($term1Due - $term1Paid, 2));
        $term2Remaining = max(0.00, round($term2Due - $term2Paid, 2));

        $term1Status = $term1Due <= 0 ? 'empty' : ($term1Remaining <= 0 ? 'paid' : ($term1Paid > 0 ? 'partial' : 'unpaid'));
        $term2Status = $term2Due <= 0 ? 'empty' : ($term2Remaining <= 0 ? 'paid' : ($term2Paid > 0 ? 'partial' : 'unpaid'));

        return [
            'academic_year'          => $year,
            'region'                 => $this->resolved_region,
            'region_label'           => $this->region_label,
            'total_due'              => round($totalDue, 2),
            'total_semester_tuition' => round($totalDue, 2), // متوافق مع واجهة استعراض الملف الأكاديمي
            'total_paid'             => round($totalPaid, 2),
            'total_remaining'        => $totalRemaining,
            'remaining_balance'      => $totalRemaining,      // متوافق مع واجهة استعراض الملف الأكاديمي
            'is_fully_paid'          => $totalRemaining <= 0,
            'subscriptions'          => $subscriptions,
            'term_1_items'           => $term1Subs,
            'term_2_items'           => $term2Subs,
            'term_1_due'             => round($term1Due, 2),
            'term_1_paid'            => round($term1Paid, 2),
            'term_1_remaining'       => $term1Remaining,
            'term_1_status'          => $term1Status,
            'term_1_count'           => $term1Subs->count(),
            'term_2_due'             => round($term2Due, 2),
            'term_2_paid'            => round($term2Paid, 2),
            'term_2_remaining'       => $term2Remaining,
            'term_2_status'          => $term2Status,
            'term_2_count'           => $term2Subs->count(),
            'paid_count'             => $subscriptions->whereIn('status', ['paid', 'waived'])->count(),
            'partial_count'          => $subscriptions->where('status', 'partial')->count(),
            'unpaid_count'           => $subscriptions->where('status', 'unpaid')->count(),
            'pending_count'          => $subscriptions->where('status', 'pending')->count(),
        ];
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
     * رقم الشهر الأكاديمي الحالي المحسوب للطالب منذ تاريخ الاعتماد (بمعدل 30 يوماً لكل شهر)
     */
    public function currentAcademicMonthIndex(): int
    {
        $startDate = $this->approved_at ?? $this->created_at ?? now();
        $days = (int) $startDate->diffInDays(now());
        $month = (int) floor($days / 30) + 1;
        return min(12, max(1, $month));
    }

    /**
     * عدد الأشهر المسددة بالكامل
     */
    public function paidMonthsCount(?string $academicYear = null): int
    {
        $summary = $this->getFinancialSummary($academicYear);
        return (int) ($summary['paid_months_count'] ?? 0);
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
     * احتساب التفصيل المالي الدقيق للمواد والاشتراكات الفصلية للطالب بناءً على منطقته
     */
    public function getFeeBreakdown(): array
    {
        $enrollments = $this->enrollments()->with('subject.stage')->get();
        $items = [];
        $subtotal = 0;
        $region = $this->resolved_region;

        if ($enrollments->isNotEmpty()) {
            foreach ($enrollments as $e) {
                $sub = $e->subject;
                if (!$sub) continue;
                $semester = $e->semester ?: 'both';
                $price = (float) $sub->getSemesterPrice($semester, $region);
                $subtotal += $price;
                $items[] = [
                    'id'             => $sub->id,
                    'name_ar'        => $sub->name_ar,
                    'name_en'        => $sub->name_en ?? $sub->name_ar,
                    'icon'           => $sub->icon ?? '📘',
                    'stage'          => optional($sub->stage)->name_ar ?? 'توجيهي',
                    'semester'       => $semester,
                    'semester_label' => $e->semester_label ?? 'الفصلين معاً',
                    'region'         => $region,
                    'price'          => $price,
                    'orig_price'     => (float) $sub->getSemesterPrice($semester, $region),
                    'is_free'        => (bool) $sub->is_free,
                ];
            }
        } elseif ($this->stage) {
            $stageSubjects = $this->stage->subjects()->get();
            if ($stageSubjects->isNotEmpty()) {
                foreach ($stageSubjects as $sub) {
                    $price = (float) $sub->getSemesterPrice('both', $region);
                    $subtotal += $price;
                    $items[] = [
                        'id'             => $sub->id,
                        'name_ar'        => $sub->name_ar,
                        'name_en'        => $sub->name_en ?? $sub->name_ar,
                        'icon'           => $sub->icon ?? '📘',
                        'stage'          => optional($sub->stage)->name_ar ?? 'توجيهي',
                        'semester'       => 'both',
                        'semester_label' => 'الفصلين معاً',
                        'region'         => $region,
                        'price'          => $price,
                        'orig_price'     => $price,
                        'is_free'        => (bool) $sub->is_free,
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

