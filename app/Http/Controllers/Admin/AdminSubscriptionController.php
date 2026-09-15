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
            $q->where('academic_year', $year);
        }]);

        if ($stageId) {
            $studentsQuery->where('stage_id', $stageId);
        }

        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        if ($statusFilter && $monthFilter) {
            $studentsQuery->whereHas('monthlySubscriptions', function ($q) use ($year, $monthFilter, $statusFilter) {
                $q->where('academic_year', $year)
                  ->where('month', (int)$monthFilter)
                  ->where('status', $statusFilter);
            });
        }

        $students = $studentsQuery->latest()->paginate(25)->withQueryString();

        // فحص ومزامنة الشهور الـ 12 تلقائياً مع مدفوعات الطالب وحالة تسجيله
        foreach ($students as $student) {
            StudentMonthlySubscription::syncWithStudentPayments($student, $year);
            $student->load(['monthlySubscriptions' => function ($q) use ($year) {
                $q->where('academic_year', $year)->orderBy('month');
            }]);
        }

        // إحصائيات عامة للمدير
        $allSubs = StudentMonthlySubscription::where('academic_year', $year)->get();
        $stats = [
            'total_expected'  => $allSubs->sum('amount'),
            'total_collected' => $allSubs->where('status', 'paid')->sum('amount'),
            'total_pending'   => $allSubs->where('status', 'pending')->sum('amount'),
            'total_unpaid'    => $allSubs->where('status', 'unpaid')->sum('amount'),
            'paid_count'      => $allSubs->where('status', 'paid')->count(),
            'pending_count'   => $allSubs->where('status', 'pending')->count(),
            'unpaid_count'    => $allSubs->where('status', 'unpaid')->count(),
            'waived_count'    => $allSubs->where('status', 'waived')->count(),
            'collection_rate' => $allSubs->sum('amount') > 0 ? round(($allSubs->where('status', 'paid')->sum('amount') / $allSubs->sum('amount')) * 100, 1) : 0,
        ];

        $monthsNames = StudentMonthlySubscription::monthNamesAr();

        return view('admin.subscriptions.monthly', compact('students', 'stages', 'year', 'stats', 'monthsNames', 'stageId', 'search', 'monthFilter', 'statusFilter'));
    }

    /**
     * تحديث حالة اشتراك شهر محدد لطالب (AJAX)
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
                'status'  => $request->status,
                'amount'  => $request->amount ?? 150.00,
                'notes'   => $request->notes,
                'paid_at' => ($request->status === 'paid') ? now() : null,
            ]
        );

        $student = Student::find($request->student_id);
        $monthName = StudentMonthlySubscription::monthNamesAr()[$request->month] ?? "شهر {$request->month}";

        if ($request->status === 'paid') {
            try {
                NotificationService::notifyStudent(
                    $student->id,
                    "اعتماد اشتراك {$monthName} 💳",
                    "تم اعتماد سداد اشتراكك لشهر ({$monthName}) بنجاح! نرجو لك دوام التوفيق والتميز.",
                    'payment',
                    route('student.subscriptions.index'),
                    'fa-circle-check'
                );
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success'   => true,
            'message'   => "تم تحديث اشتراك الطالب لشهر ({$monthName}) إلى: " . $sub->status_badge['label'],
            'badge'     => $sub->status_badge,
            'status'    => $sub->status,
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

        $monthsNames = StudentMonthlySubscription::monthNamesAr();
        $paidCount = $subscriptions->where('status', 'paid')->count();
        $unpaidCount = $subscriptions->where('status', 'unpaid')->count();
        $pendingCount = $subscriptions->where('status', 'pending')->count();
        $totalPaidAmount = $subscriptions->where('status', 'paid')->sum('amount');

        return view('student.subscriptions.index', compact('student', 'subscriptions', 'year', 'monthsNames', 'paidCount', 'unpaidCount', 'pendingCount', 'totalPaidAmount'));
    }
}
