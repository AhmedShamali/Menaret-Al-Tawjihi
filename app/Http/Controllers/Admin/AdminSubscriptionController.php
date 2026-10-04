<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Stage;
use App\Models\StudentMonthlySubscription;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSubscriptionController extends Controller
{
    /**
     * جدول ومصفوفة الاشتراكات والرسوم الفصلية للطلاب (الفصل الأول / الفصل الثاني / الفصلين معاً)
     */
    public function index(Request $request)
    {
        $year = $request->query('year', '2026-2027');
        $stageId = $request->query('stage_id');
        $search = $request->query('search');
        $semesterFilter = $request->query('semester'); // term_1, term_2, both
        $statusFilter = $request->query('status'); // paid, partial, unpaid, waived
        $monthFilter = $request->query('month'); // للتوافق العكسي

        $stages = Stage::orderBy('grade_level', 'desc')->get();

        $studentsQuery = Student::with(['stage', 'enrolledSubjects.teacher', 'semesterSubscriptions.subject']);

        if ($stageId) {
            $studentsQuery->where('stage_id', $stageId);
        }

        if ($search) {
            $search = trim($search);
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nid', 'like', "%{$search}%");
            });
        }

        // فلترة الفصول الدراسية
        if ($semesterFilter) {
            $studentsQuery->where(function ($q) use ($semesterFilter) {
                $q->whereHas('enrollments', function ($eq) use ($semesterFilter) {
                    if ($semesterFilter === 'both') {
                        $eq->where('semester', 'both');
                    } else {
                        $eq->whereIn('semester', [$semesterFilter, 'both']);
                    }
                })->orWhereHas('semesterSubscriptions', function ($sq) use ($semesterFilter) {
                    if ($semesterFilter === 'both') {
                        $sq->where('semester', 'both');
                    } else {
                        $sq->whereIn('semester', [$semesterFilter, 'both']);
                    }
                });
            });
        }

        // فلترة حالة السداد
        if ($statusFilter) {
            $studentsQuery->where(function ($query) use ($year, $statusFilter) {
                $query->whereHas('semesterSubscriptions', function ($sq) use ($year, $statusFilter) {
                    $sq->where('academic_year', $year)->where('status', $statusFilter);
                })->orWhereHas('monthlySubscriptions', function ($mq) use ($year, $statusFilter) {
                    $mq->where('academic_year', $year)->where('status', $statusFilter);
                });
            });
        }

        $students = $studentsQuery->latest()->paginate(25)->withQueryString();

        // مزامنة وتهيئة البيانات المالية الفصلية لكل طالب في الصفحة الحالية
        foreach ($students as $student) {
            $student->semester_summary = $student->getSemesterFinancialSummary($year);
            if ($student->monthlySubscriptions()->where('academic_year', $year)->count() < 12) {
                StudentMonthlySubscription::syncWithStudentPayments($student, $year);
            }
        }

        // إحصائيات مالية عامة ومؤشرات دقيقة للرسوم الفصلية في المنصة
        if (\Illuminate\Support\Facades\Schema::hasTable('student_semester_subscriptions')) {
            $allSubs = \App\Models\StudentSemesterSubscription::where('academic_year', $year)->get();
            $totalExpected = (float)$allSubs->where('status', '!=', 'waived')->sum('amount');
            $totalCollected = (float)$allSubs->sum(function ($s) {
                if ($s->status === 'waived') {
                    return 0.00;
                }
                if ($s->status === 'paid' && ((float)($s->paid_amount ?? 0) <= 0)) {
                    return (float) $s->amount;
                }
                return (float) ($s->paid_amount ?? 0);
            });
            $totalRemaining = max(0.00, round($totalExpected - $totalCollected, 2));

            $stats = [
                'total_expected'  => $totalExpected,
                'total_collected' => $totalCollected,
                'total_remaining' => $totalRemaining,
                'total_pending'   => (float)$allSubs->where('status', 'pending')->sum('amount'),
                'total_unpaid'    => (float)$allSubs->where('status', 'unpaid')->sum('amount'),
                'paid_count'      => $allSubs->where('status', 'paid')->count(),
                'partial_count'   => $allSubs->where('status', 'partial')->count(),
                'pending_count'   => $allSubs->where('status', 'pending')->count(),
                'unpaid_count'    => $allSubs->where('status', 'unpaid')->count(),
                'waived_count'    => $allSubs->where('status', 'waived')->count(),
                'collection_rate' => $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0,
            ];
        } else {
            $stats = [
                'total_expected'  => 0,
                'total_collected' => 0,
                'total_remaining' => 0,
                'total_pending'   => 0,
                'total_unpaid'    => 0,
                'paid_count'      => 0,
                'partial_count'   => 0,
                'pending_count'   => 0,
                'unpaid_count'    => 0,
                'waived_count'    => 0,
                'collection_rate' => 0,
            ];
        }

        $monthsNames = StudentMonthlySubscription::monthNames();

        return view('admin.subscriptions.monthly', compact(
            'students', 'stages', 'year', 'stats', 'monthsNames', 
            'stageId', 'search', 'semesterFilter', 'statusFilter', 'monthFilter'
        ));
    }

    /**
     * تحديث حالة ومبالغ الرسوم الفصلية للطالب (AJAX) - الفصل الأول / الفصل الثاني / الفصلين
     */
    public function updateSemesterStatus(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'semester'      => 'required|in:term_1,term_2,both',
            'status'        => 'required|in:paid,partial,unpaid,waived',
            'academic_year' => 'nullable|string',
            'paid_amount'   => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        $student = Student::findOrFail($request->student_id);
        $year = $request->input('academic_year', '2026-2027');
        $sem = $request->input('semester');
        $newStatus = $request->input('status');
        $paidAmount = (float) $request->input('paid_amount', 0);
        $notes = $request->input('notes');

        \App\Models\StudentSemesterSubscription::syncWithStudent($student, $year);

        $subsQuery = $student->semesterSubscriptions()->where('academic_year', $year);
        if ($sem !== 'both') {
            $subsQuery->where(function ($q) use ($sem) {
                $q->where('semester', $sem)->orWhere('semester', 'both');
            });
        }
        $subs = $subsQuery->get();
        $totalSubsDue = (float)$subs->where('status', '!=', 'waived')->sum('amount');

        foreach ($subs as $sub) {
            if ($newStatus === 'waived') {
                $sub->status = 'waived';
                $sub->paid_amount = 0.00;
            } elseif ($newStatus === 'paid') {
                $sub->status = 'paid';
                $sub->paid_amount = $sub->amount;
                $sub->paid_at = now();
            } elseif ($newStatus === 'unpaid') {
                $sub->status = 'unpaid';
                $sub->paid_amount = 0.00;
            } elseif ($newStatus === 'partial') {
                $sub->status = 'partial';
                if ($totalSubsDue > 0 && $paidAmount > 0) {
                    $ratio = $sub->amount / $totalSubsDue;
                    $sub->paid_amount = min((float)$sub->amount, round($paidAmount * $ratio, 2));
                } else {
                    $sub->paid_amount = min((float)$sub->amount, $paidAmount);
                }
            }
            $sub->is_manual = true;
            if ($notes) {
                $sub->notes = $notes;
            }
            $sub->save();

            // تفعيل قيد المادة للطالب إن تم سداد الرسوم
            $enrollment = $student->enrollments()->where('subject_id', $sub->subject_id)->first();
            if ($enrollment) {
                if (in_array($sub->status, ['paid', 'waived'])) {
                    $enrollment->status = 'active';
                    $enrollment->paid_amount = $sub->amount;
                }
                $enrollment->save();
            }
        }

        // تفعيل حساب الطالب رسمياً فور سداد أي رسوم
        if ($newStatus === 'paid' && $student->status !== 'active') {
            $student->status = 'active';
            $student->save();
        }

        $summary = $student->getSemesterFinancialSummary($year);

        $allSubs = \App\Models\StudentSemesterSubscription::where('academic_year', $year)->get();
        $totalExpected = (float)$allSubs->where('status', '!=', 'waived')->sum('amount');
        $totalCollected = (float)$allSubs->sum('paid_amount');
        $totalRemaining = max(0, $totalExpected - $totalCollected);
        $stats = [
            'total_expected'  => number_format($totalExpected, 2),
            'total_collected' => number_format($totalCollected, 2),
            'total_remaining' => number_format($totalRemaining, 2),
            'paid_count'      => $allSubs->where('status', 'paid')->count(),
            'partial_count'   => $allSubs->where('status', 'partial')->count(),
            'unpaid_count'    => $allSubs->where('status', 'unpaid')->count(),
            'waived_count'    => $allSubs->where('status', 'waived')->count(),
            'collection_rate' => $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0,
        ];

        $subsData = $student->semesterSubscriptions()->where('academic_year', $year)->with('subject')->get()->map(function($sub) {
            return [
                'id'               => $sub->id,
                'subject_name'     => $sub->subject->name ?? 'مادة تعليمية',
                'semester'         => $sub->semester,
                'semester_name'    => $sub->semester_name_ar,
                'amount'           => (float)$sub->amount,
                'paid_amount'      => (float)$sub->paid_amount,
                'remaining_amount' => (float)$sub->remaining_amount,
                'status'           => $sub->status,
                'status_label'     => $sub->status_badge['label'],
                'status_class'     => $sub->status_badge['class'],
                'paid_at'          => $sub->paid_at ? $sub->paid_at->format('Y-m-d') : '-',
                'notes'            => $sub->notes ?? '-'
            ];
        });

        return response()->json([
            'success'    => true,
            'message'    => 'تم تحديث حالة الرسوم الفصلية للطالب (' . ($student->name_ar ?? $student->name) . ') بنجاح! ✅',
            'student_id' => $student->id,
            'summary'    => $summary,
            'stats'      => $stats,
            'subs'       => $subsData,
        ]);
    }

    /**
     * تحديث حالة ومبالغ اشتراك شهر محدد لطالب (AJAX) مع دعم الدفع الجزئي والمتبقي
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'month'         => 'required|integer|between:1,12',
            'academic_year' => 'required|string',
            'status'        => 'nullable|string|in:paid,partial,pending,unpaid,waived',
            'amount'        => 'nullable|numeric|min:0',
            'paid_amount'   => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        $student = Student::findOrFail($request->student_id);
        $amount = $request->filled('amount') ? (float)$request->amount : (float)$student->monthlyAmountDue();
        $statusInput = $request->input('status');

        if ($statusInput === 'waived') {
            $amount = 0.00;
            $paidAmount = 0.00;
            $status = 'waived';
        } else {
            // إذا تم تمرير المبلغ المدفوع فعلياً
            if ($request->filled('paid_amount')) {
                $paidAmount = (float)$request->paid_amount;
                if ($paidAmount >= $amount && $amount > 0) {
                    $status = 'paid';
                } elseif ($paidAmount > 0 && $paidAmount < $amount) {
                    $status = 'partial';
                } elseif ($paidAmount <= 0) {
                    $status = ($statusInput === 'pending') ? 'pending' : 'unpaid';
                    $paidAmount = 0.00;
                } else {
                    $status = $statusInput ?: 'unpaid';
                }
            } else {
                // إذا تم اختيار الحالة بدون إدخال مدفوع محدد
                if ($statusInput === 'paid') {
                    $paidAmount = $amount;
                    $status = 'paid';
                } elseif ($statusInput === 'partial') {
                    $paidAmount = round($amount / 2, 2);
                    $status = 'partial';
                } elseif ($statusInput === 'pending') {
                    $paidAmount = 0.00;
                    $status = 'pending';
                } else {
                    $paidAmount = 0.00;
                    $status = 'unpaid';
                }
            }
        }

        $sub = StudentMonthlySubscription::updateOrCreate(
            [
                'student_id'    => $student->id,
                'academic_year' => $request->academic_year,
                'month'         => $request->month,
            ],
            [
                'status'      => $status,
                'amount'      => $amount,
                'paid_amount' => $paidAmount,
                'notes'       => $request->notes,
                'paid_at'     => in_array($status, ['paid', 'partial']) ? now() : null,
                'is_manual'   => true,
            ]
        );

        $monthName = StudentMonthlySubscription::monthNamesAr()[$request->month] ?? "شهر {$request->month}";

        if (in_array($status, ['paid', 'waived']) && $student) {
            // إذا كان الحساب مجمداً بسبب رسوم شهر، يتم فك التجميد تلقائياً إذا لم يعد مستحقاً
            if ($student->status === 'suspended' && !$student->isMonthlyFeeDue($request->academic_year)) {
                $student->status = 'active';
                $student->freeze_reason = null;
                $student->save();
            }

            try {
                NotificationService::notifyStudent(
                    $student->id,
                    "اعتماد اشتراك {$monthName} 💳",
                    "تم اعتماد سداد اشتراكك لـ ({$monthName}) بنجاح! تم تفعيل حسابك ومتابعة دراستك بالكامل.",
                    'payment',
                    route('student.subscriptions.index'),
                    'fa-circle-check'
                );
            } catch (\Throwable $e) {}
        }

        // حساب إحصائيات الطالب المالية المحدثة
        $studentSubs = StudentMonthlySubscription::where('student_id', $student->id)
            ->where('academic_year', $request->academic_year)
            ->get();

        $studentDue = (float)$studentSubs->where('status', '!=', 'waived')->sum('amount');
        $studentPaid = (float)$studentSubs->sum(function ($s) {
            return ($s->status === 'paid' && ((float)($s->paid_amount ?? 0) <= 0)) ? (float)$s->amount : (float)($s->paid_amount ?? 0);
        });
        $studentRemaining = max(0.00, round($studentDue - $studentPaid, 2));
        $studentPaidCount = $studentSubs->whereIn('status', ['paid', 'waived'])->count();
        $studentPercent = round(($studentPaidCount / 12) * 100);

        // إحصائيات عامة للمنصة بعد التحديث المباشر
        $allSubs = StudentMonthlySubscription::where('academic_year', $request->academic_year)->get();
        $allExpected = (float)$allSubs->where('status', '!=', 'waived')->sum('amount');
        $allCollected = (float)$allSubs->sum(function ($s) {
            if ($s->status === 'waived') return 0.00;
            if ($s->status === 'paid' && ((float)($s->paid_amount ?? 0) <= 0)) return (float)$s->amount;
            return (float)($s->paid_amount ?? 0);
        });
        $allRemaining = max(0.00, round($allExpected - $allCollected, 2));

        $stats = [
            'total_expected'  => number_format($allExpected, 2) . ' ₪',
            'total_collected' => number_format($allCollected, 2) . ' ₪',
            'total_remaining' => number_format($allRemaining, 2) . ' ₪',
            'total_unpaid'    => number_format($allSubs->where('status', 'unpaid')->sum('amount'), 2) . ' ₪',
            'total_pending'   => number_format($allSubs->where('status', 'pending')->sum('amount'), 2) . ' ₪',
            'paid_count'      => $allSubs->where('status', 'paid')->count(),
            'partial_count'   => $allSubs->where('status', 'partial')->count(),
            'unpaid_count'    => $allSubs->where('status', 'unpaid')->count(),
            'pending_count'   => $allSubs->where('status', 'pending')->count(),
            'waived_count'    => $allSubs->where('status', 'waived')->count(),
            'collection_rate' => ($allExpected > 0 ? round(($allCollected / $allExpected) * 100, 1) : 0) . '%',
        ];

        return response()->json([
            'success'               => true,
            'message'               => "تم تحديث اشتراك ({$monthName}) بنجاح: " . $sub->status_badge['label'],
            'badge'                 => $sub->status_badge,
            'status'                => $sub->status,
            'amount'                => (float)$sub->amount,
            'paid_amount'           => (float)$sub->paid_amount,
            'remaining_amount'      => (float)$sub->remaining_amount,
            'notes'                 => $sub->notes ?? '',
            'student_id'            => $student->id,
            'student_due'           => number_format($studentDue, 2) . ' ₪',
            'student_paid'          => number_format($studentPaid, 2) . ' ₪',
            'student_remaining'     => number_format($studentRemaining, 2) . ' ₪',
            'student_has_remaining' => $studentRemaining > 0,
            'student_paid_count'    => $studentPaidCount,
            'student_percent'       => $studentPercent,
            'stats'                 => $stats,
        ]);
    }

    /**
     * تحديث رسوم وخطة الطالب المالية والخصومات (من خلال المدير)
     */
    public function updateStudentFee(Request $request)
    {
        $request->validate([
            'student_id'              => 'required|exists:students,id',
            'monthly_fee'             => 'required|numeric|min:0',
            'custom_discount_percent' => 'nullable|numeric|min:0|max:100',
            'custom_discount_fixed'   => 'nullable|numeric|min:0',
            'discount_notes'          => 'nullable|string|max:255',
        ]);

        $student = Student::findOrFail($request->student_id);
        $student->monthly_fee = $request->monthly_fee;
        $student->custom_discount_percent = $request->custom_discount_percent ?? 0;
        $student->custom_discount_fixed = $request->custom_discount_fixed ?? 0;
        $student->discount_notes = $request->discount_notes;
        $student->save();

        $year = $request->input('academic_year', '2026-2027');
        $netFee = $student->monthlyAmountDue();

        // تحديث المبالغ للشهور غير المسددة التي لم تُحدد يدوياً بمبلغ خاص
        StudentMonthlySubscription::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->where('status', 'unpaid')
            ->where('is_manual', false)
            ->update(['amount' => $netFee]);

        if ($student->hasDiscount() && $student->custom_discount_percent >= 100) {
            StudentMonthlySubscription::where('student_id', $student->id)
                ->where('academic_year', $year)
                ->where('is_manual', false)
                ->update([
                    'status' => 'waived',
                    'amount' => 0.00,
                    'notes'  => 'معفى رسمياً - منحة دراسية كاملة 100%',
                ]);
        }

        return response()->json([
            'success'          => true,
            'message'          => 'تم تحديث خطة رسوم الطالب (' . ($student->name_ar ?? $student->name) . ') بنجاح.',
            'monthly_fee'      => (float)$student->monthly_fee,
            'discount_percent' => (float)$student->custom_discount_percent,
            'discount_fixed'   => (float)$student->custom_discount_fixed,
            'discount_notes'   => $student->discount_notes ?? '',
            'amount_due'       => number_format($netFee, 0),
        ]);
    }

    /**
     * تحديث القسط الشهري الافتراضي العام للمنصة (Global Default Fee)
     */
    public function updateGlobalFee(Request $request)
    {
        $request->validate([
            'default_monthly_fee' => 'required|numeric|min:0',
        ]);

        \App\Models\Setting::set('default_monthly_fee', $request->default_monthly_fee);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ القسط الشهري الافتراضي لمنصة Step by Step بنجاح: ' . number_format($request->default_monthly_fee, 0) . ' ₪',
            'fee'     => number_format($request->default_monthly_fee, 0),
        ]);
    }

    /**
     * واجهة استعراض الطالب لسجل اشتراكاته الفصلية الشخصي
     */
    public function studentIndex()
    {
        $student = auth('student')->user() ?? auth()->user();
        if (!$student) {
            return redirect()->route('login');
        }

        $year = '2026-2027';
        // مزامنة وربط الاشتراكات الفصلية لكل مادة مقيدة للطالب
        $semesterSubscriptions = \App\Models\StudentSemesterSubscription::syncWithStudent($student, $year);
        $semesterSummary = $student->getSemesterFinancialSummary($year);

        $totalDueAmount = (float) $semesterSummary['total_due'];
        $totalPaidAmount = (float) $semesterSummary['total_paid'];
        $totalRemainingAmount = (float) $semesterSummary['total_remaining'];

        $paidCount = $semesterSubscriptions->where('status', 'paid')->count();
        $unpaidCount = $semesterSubscriptions->where('status', 'unpaid')->count();
        $pendingCount = $semesterSubscriptions->where('status', 'pending')->count();
        $partialCount = $semesterSubscriptions->where('status', 'partial')->count();

        // كشف الموقف المالي الفصلي للطالب
        $financialSummary = [
            'total_due_now'           => $totalRemainingAmount,
            'total_year_due'          => $totalDueAmount,
            'total_year_paid'         => $totalPaidAmount,
            'total_year_remaining'    => $totalRemainingAmount,
            'paid_months_count'       => $paidCount,
            'unpaid_months_count'     => $unpaidCount,
            'pending_months_count'    => $pendingCount,
            'partial_months_count'    => $partialCount,
            'has_arrears'             => $totalRemainingAmount > 0 && $totalPaidAmount > 0,
            'active_due_month_name'   => 'الفصل الدراسي الحالي',
            'previous_unpaid_balance' => 0,
            'current_month_due'       => $totalRemainingAmount,
            'arrears_details'         => []
        ];

        return view('student.subscriptions.index', compact(
            'student', 'semesterSubscriptions', 'semesterSummary', 'year',
            'paidCount', 'unpaidCount', 'pendingCount', 'partialCount',
            'totalDueAmount', 'totalPaidAmount', 'totalRemainingAmount',
            'financialSummary'
        ));
    }

    /**
     * واجهة الإدارة المالية المستقلة والرسوم الفصلية لطالب محدد
     */
    public function studentProfile(Request $request, Student $student)
    {
        $year = $request->query('year', '2026-2027');

        // مزامنة والبيانات والاشتراكات الفصلية للطالب
        \App\Models\StudentSemesterSubscription::syncWithStudent($student, $year);
        $semesterSummary = $student->getSemesterFinancialSummary($year);
        $semesterSubscriptions = $semesterSummary['subscriptions'];

        // حساب المؤشرات المالية الرسمية للطالب
        $studentDue = (float)$semesterSummary['total_due'];
        $studentPaid = (float)$semesterSummary['total_paid'];
        $studentRemaining = (float)$semesterSummary['total_remaining'];
        $paidCount = $semesterSummary['paid_count'];
        $partialCount = $semesterSummary['partial_count'];
        $waivedCount = $semesterSubscriptions->where('status', 'waived')->count();
        $pendingCount = $semesterSummary['pending_count'];
        $unpaidCount = $semesterSummary['unpaid_count'];

        $collectionRate = $studentDue > 0 ? round(($studentPaid / $studentDue) * 100, 1) : 100;

        // الطلاب السابق والتالي للتنقل السريع والمريح بين السجلات
        $prevStudent = Student::where('stage_id', $student->stage_id)
            ->where('id', '<', $student->id)
            ->orderBy('id', 'desc')
            ->first() ?? Student::where('id', '<', $student->id)->orderBy('id', 'desc')->first();

        $nextStudent = Student::where('stage_id', $student->stage_id)
            ->where('id', '>', $student->id)
            ->orderBy('id', 'asc')
            ->first() ?? Student::where('id', '>', $student->id)->orderBy('id', 'asc')->first();

        // قائمة مختصرة لجميع طلاب المرحلة للتبديل الفوري
        $allStageStudents = Student::where('stage_id', $student->stage_id)
            ->select('id', 'name_ar', 'name_en', 'nid')
            ->orderBy('name_ar')
            ->get();

        return view('admin.subscriptions.student_profile', compact(
            'student',
            'semesterSummary',
            'semesterSubscriptions',
            'year',
            'studentDue',
            'studentPaid',
            'studentRemaining',
            'paidCount',
            'partialCount',
            'waivedCount',
            'pendingCount',
            'unpaidCount',
            'collectionRate',
            'prevStudent',
            'nextStudent',
            'allStageStudents'
        ));
    }
}
