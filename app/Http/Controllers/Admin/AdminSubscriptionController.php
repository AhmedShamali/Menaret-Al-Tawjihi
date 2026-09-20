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
     * جدول ومصفوفة الاشتراكات الشهرية للطلاب لجميع أشهر السنة (12 شهراً)
     */
    public function index(Request $request)
    {
        $year = $request->query('year', '2026-2027');
        $stageId = $request->query('stage_id');
        $search = $request->query('search');
        $monthFilter = $request->query('month');
        $statusFilter = $request->query('status');

        $stages = Stage::orderBy('grade_level', 'desc')->get();

        $studentsQuery = Student::with(['stage', 'monthlySubscriptions' => function ($q) use ($year) {
            $q->where('academic_year', $year)->orderBy('month');
        }]);

        if ($stageId) {
            $studentsQuery->where('stage_id', $stageId);
        }

        if ($search) {
            $search = trim($search);
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        // فلاتر الشهور والحالات بمرونة كاملة
        if ($statusFilter && $monthFilter) {
            $studentsQuery->whereHas('monthlySubscriptions', function ($q) use ($year, $monthFilter, $statusFilter) {
                $q->where('academic_year', $year)
                  ->where('month', (int)$monthFilter)
                  ->where('status', $statusFilter);
            });
        } elseif ($statusFilter) {
            $studentsQuery->whereHas('monthlySubscriptions', function ($q) use ($year, $statusFilter) {
                $q->where('academic_year', $year)
                  ->where('status', $statusFilter);
            });
        } elseif ($monthFilter) {
            $studentsQuery->whereHas('monthlySubscriptions', function ($q) use ($year, $monthFilter) {
                $q->where('academic_year', $year)
                  ->where('month', (int)$monthFilter);
            });
        }

        $students = $studentsQuery->latest()->paginate(25)->withQueryString();

        // التأكد من تهيئة الشهور الـ 12 للطلاب الجدد فقط بدون المساس بأي تعديل يدوي للإدارة
        foreach ($students as $student) {
            if ($student->monthlySubscriptions->count() < 12) {
                StudentMonthlySubscription::syncWithStudentPayments($student, $year);
                $student->load(['monthlySubscriptions' => function ($q) use ($year) {
                    $q->where('academic_year', $year)->orderBy('month');
                }]);
            }
        }

        // إحصائيات عامة للمدير
        $allSubs = StudentMonthlySubscription::where('academic_year', $year)->get();
        $totalCollected = (float)$allSubs->where('status', 'paid')->sum('amount');
        $totalUnpaid = (float)$allSubs->where('status', 'unpaid')->sum('amount');
        $totalPending = (float)$allSubs->where('status', 'pending')->sum('amount');
        $totalExpected = (float)$allSubs->sum('amount');

        $stats = [
            'total_expected'  => $totalExpected,
            'total_collected' => $totalCollected,
            'total_pending'   => $totalPending,
            'total_unpaid'    => $totalUnpaid,
            'paid_count'      => $allSubs->where('status', 'paid')->count(),
            'pending_count'   => $allSubs->where('status', 'pending')->count(),
            'unpaid_count'    => $allSubs->where('status', 'unpaid')->count(),
            'waived_count'    => $allSubs->where('status', 'waived')->count(),
            'collection_rate' => $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0,
        ];

        $monthsNames = StudentMonthlySubscription::monthNames();

        return view('admin.subscriptions.monthly', compact('students', 'stages', 'year', 'stats', 'monthsNames', 'stageId', 'search', 'monthFilter', 'statusFilter'));
    }

    /**
     * تحديث حالة اشتراك شهر محدد لطالب (AJAX) مع الحفظ اليدوي التام وتحديث الإحصائيات
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'month'         => 'required|integer|between:1,12',
            'academic_year' => 'required|string',
            'status'        => 'required|in:paid,pending,unpaid,waived',
            'amount'        => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        $sub = StudentMonthlySubscription::updateOrCreate(
            [
                'student_id'    => $request->student_id,
                'academic_year' => $request->academic_year,
                'month'         => $request->month,
            ],
            [
                'status'    => $request->status,
                'amount'    => $request->amount ?? 150.00,
                'notes'     => $request->notes,
                'paid_at'   => ($request->status === 'paid') ? now() : null,
                'is_manual' => true,
            ]
        );

        $student = Student::find($request->student_id);
        $monthName = StudentMonthlySubscription::monthNamesAr()[$request->month] ?? "شهر {$request->month}";

        if (in_array($request->status, ['paid', 'waived']) && $student) {
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

        // حساب إحصائيات الطالب المحدثة
        $studentSubs = StudentMonthlySubscription::where('student_id', $request->student_id)
            ->where('academic_year', $request->academic_year)
            ->get();
        $studentPaidCount = $studentSubs->whereIn('status', ['paid', 'waived'])->count();
        $studentPercent = round(($studentPaidCount / 12) * 100);

        // إحصائيات عامة للمنصة بعد التحديث المباشر
        $allSubs = StudentMonthlySubscription::where('academic_year', $request->academic_year)->get();
        $totalCollected = (float)$allSubs->where('status', 'paid')->sum('amount');
        $totalUnpaid = (float)$allSubs->where('status', 'unpaid')->sum('amount');
        $totalPending = (float)$allSubs->where('status', 'pending')->sum('amount');
        $totalExpected = (float)$allSubs->sum('amount');

        $stats = [
            'total_collected' => number_format($totalCollected, 2) . ' ₪',
            'total_unpaid'    => number_format($totalUnpaid, 2) . ' ₪',
            'total_pending'   => number_format($totalPending, 2) . ' ₪',
            'paid_count'      => $allSubs->where('status', 'paid')->count(),
            'unpaid_count'    => $allSubs->where('status', 'unpaid')->count(),
            'pending_count'   => $allSubs->where('status', 'pending')->count(),
            'collection_rate' => ($totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0) . '%',
        ];

        return response()->json([
            'success'            => true,
            'message'            => "تم تحديث اشتراك الطالب لشهر ({$monthName}) إلى: " . $sub->status_badge['label'],
            'badge'              => $sub->status_badge,
            'status'             => $sub->status,
            'amount'             => (float)$sub->amount,
            'notes'              => $sub->notes ?? '',
            'student_paid_count' => $studentPaidCount,
            'student_percent'    => $studentPercent,
            'stats'              => $stats,
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
            'message' => 'تم حفظ القسط الشهري الافتراضي لمنصة منارة التوجيهي بنجاح: ' . number_format($request->default_monthly_fee, 0) . ' ₪',
            'fee'     => number_format($request->default_monthly_fee, 0),
        ]);
    }

    /**
     * واجهة استعراض الطالب لسجل اشتراكاته الشهرية الشخصي
     */
    public function studentIndex()
    {
        $student = auth('student')->user() ?? auth()->user();
        if (!$student) {
            return redirect()->route('login');
        }

        $year = '2026-2027';
        // مزامنة وربط الشهور الـ 12 تلقائياً مع مدفوعات الطالب وحالة تسجيله
        $subscriptions = StudentMonthlySubscription::syncWithStudentPayments($student, $year);

        $monthsNames = StudentMonthlySubscription::monthNames();
        $paidCount = $subscriptions->where('status', 'paid')->count();
        $unpaidCount = $subscriptions->where('status', 'unpaid')->count();
        $pendingCount = $subscriptions->where('status', 'pending')->count();
        $totalPaidAmount = $subscriptions->where('status', 'paid')->sum('amount');

        return view('student.subscriptions.index', compact('student', 'subscriptions', 'year', 'monthsNames', 'paidCount', 'unpaidCount', 'pendingCount', 'totalPaidAmount'));
    }
}
