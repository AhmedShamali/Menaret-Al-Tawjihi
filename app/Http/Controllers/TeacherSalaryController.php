<?php

namespace App\Http\Controllers;

use App\Models\TeacherSalary;
use App\Models\TeacherSalaryClaim;
use App\Models\Complaint;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherSalaryController extends Controller
{
    /**
     * واجهة حساب المعلم لاستعراض ومتابعة الرواتب ومسير الشهور وقسائم الراتب (Payslips)
     */
    public function teacherIndex(Request $request)
    {
        $teacher = Auth::user();
        if (!$teacher || $teacher->role !== 'teacher') {
            abort(403, 'غير مصرح لك بالوصول لبوابة رواتب المعلمين.');
        }

        $year = (int)$request->query('year', date('Y'));
        $monthsNames = TeacherSalary::monthNames();

        $salaries = TeacherSalary::where('teacher_id', $teacher->id)
            ->where('year', $year)
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // استفسارات المعلم المالية والردود الإدارية الواردة عن هذه السنة
        $myClaims = TeacherSalaryClaim::with('repliedBy')
            ->where('teacher_id', $teacher->id)
            ->where('year', $year)
            ->latest()
            ->get();
        $myClaimsByMonth = $myClaims->groupBy('month');

        // إحصائيات عامة لحساب المعلم عن السنة المحددة
        $totalPaid = $salaries->where('status', 'paid')->sum('net_salary');
        $totalBonus = $salaries->where('status', 'paid')->sum('bonus');
        $totalDeductions = $salaries->where('status', 'paid')->sum('deductions');
        $pendingAmount = $salaries->where('status', 'pending')->sum('net_salary');
        $paidMonthsCount = $salaries->where('status', 'paid')->count();

        return view('teacher.salary.index', compact(
            'teacher',
            'year',
            'salaries',
            'monthsNames',
            'totalPaid',
            'totalBonus',
            'totalDeductions',
            'pendingAmount',
            'paidMonthsCount',
            'myClaims',
            'myClaimsByMonth'
        ));
    }

    /**
     * إرسال ملاحظة أو مطالبة مالية من المعلم للإدارة بخصوص راتب شهر معين
     */
    public function submitTeacherClaim(Request $request)
    {
        $teacher = Auth::user();
        if (!$teacher || $teacher->role !== 'teacher') {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $request->validate([
            'year'    => 'required|integer|min:2024|max:2030',
            'month'   => 'required|integer|between:1,12',
            'message' => 'required|string|max:1000',
        ]);

        $monthName = TeacherSalary::monthNamesAr()[$request->month] ?? "شهر {$request->month}";

        // 1. تسجيل الاستفسار المالي في جدول المطالبات المالية للمعلمين
        $claim = TeacherSalaryClaim::create([
            'teacher_id' => $teacher->id,
            'year'       => (int)$request->year,
            'month'      => (int)$request->month,
            'message'    => trim($request->message),
            'status'     => 'pending',
        ]);

        // 2. تسجيل نسخة في جدول الشكاوى والاستفسارات لتظهر أيضاً في صندوق الوارد الإداري المركزي
        try {
            Complaint::create([
                'name'     => $teacher->name,
                'email'    => $teacher->email,
                'phone'    => $teacher->phone,
                'type'     => 'استفسار مالي',
                'category' => 'استفسار مالي - مستحقات المعلمين',
                'subject'  => "استفسار مالي بخصوص راتب ({$monthName} {$request->year}) - المعلم: {$teacher->name}",
                'message'  => trim($request->message),
                'status'   => 'new',
            ]);
        } catch (\Throwable $e) {}

        // 3. إشعار إدارة المنصة بمطالبة أو ملاحظة راتب المعلم
        try {
            NotificationService::notifyAdmin(
                "استفسار مالي بخصوص راتب {$monthName} 💵",
                "أرسل المعلم ({$teacher->name}) ملاحظة بخصوص راتب ({$monthName} {$request->year}): " . $request->message,
                'support',
                route('admin.teachers.salaries', ['teacher_id' => $teacher->id, 'year' => $request->year]),
                'fa-money-bill-wave'
            );
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => "تم إرسال استفسارك بخصوص راتب ({$monthName}) بنجاح، وستتم مراجعته والرد عليه من قِبل الإدارة فوراً.",
            'claim'   => $claim
        ]);
    }

    /**
     * اعتماد رد الإدارة على الاستفسار المالي للمعلم
     */
    public function replyTeacherClaim(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $request->validate([
            'reply' => 'required|string|min:2|max:1500',
        ]);

        $claim = TeacherSalaryClaim::with('teacher')->findOrFail($id);
        $claim->admin_reply = trim($request->reply);
        $claim->replied_by = $user->id;
        $claim->replied_at = now();
        $claim->status = 'replied';
        $claim->save();

        // إشعار المعلم فورياً باعتماد الرد
        try {
            NotificationService::notifyUser(
                $claim->teacher_id,
                "رد إداري على استفسارك المالي 📬",
                "وردك رد رسمي من الإدارة العامة بخصوص استفسار راتب ({$claim->month_name_ar} {$claim->year}): " . \Illuminate\Support\Str::limit($claim->admin_reply, 80),
                'support',
                route('teacher.salaries.index', ['year' => $claim->year]),
                'fa-reply'
            );
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ وإرسال الرد الرسمي للمعلم بنجاح ✅',
            'claim'   => $claim
        ]);
    }

    /**
     * واجهة الإدارة العامة لمسير رواتب المعلمين (Admin Payroll Management)
     */
    public function adminIndex(Request $request)
    {
        $year = (int)$request->query('year', date('Y'));
        $teacherId = $request->query('teacher_id');
        $monthFilter = $request->query('month');
        $statusFilter = $request->query('status');

        $teachers = User::where('role', 'teacher')->with(['stage', 'subject'])->get();

        $query = TeacherSalary::with(['teacher', 'creator'])->where('year', $year);

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        if ($monthFilter) {
            $query->where('month', (int)$monthFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $salaries = $query->orderBy('month', 'desc')->paginate(20)->withQueryString();

        // استفسارات وملاحظات المعلمين المالية
        $claimsQuery = TeacherSalaryClaim::with(['teacher', 'repliedBy'])->where('year', $year);
        if ($teacherId) {
            $claimsQuery->where('teacher_id', $teacherId);
        }
        if ($monthFilter) {
            $claimsQuery->where('month', (int)$monthFilter);
        }
        $financialClaims = $claimsQuery->latest()->get();

        // إحصائيات عامة للمدير
        $allSalaries = TeacherSalary::where('year', $year)->get();
        $stats = [
            'total_disbursed' => $allSalaries->where('status', 'paid')->sum('net_salary'),
            'total_pending'   => $allSalaries->where('status', 'pending')->sum('net_salary'),
            'total_bonus'     => $allSalaries->sum('bonus'),
            'total_records'   => $allSalaries->count(),
            'paid_records'    => $allSalaries->where('status', 'paid')->count(),
            'teachers_count'  => $teachers->count(),
            'pending_claims'  => $financialClaims->where('status', 'pending')->count(),
        ];

        $monthsNames = TeacherSalary::monthNames();

        return view('admin.teachers.salaries', compact(
            'teachers',
            'salaries',
            'year',
            'teacherId',
            'monthFilter',
            'statusFilter',
            'stats',
            'monthsNames',
            'financialClaims'
        ));
    }

    /**
     * حفظ أو تحديث راتب معلم لشهر محدد بواسطة الإدارة
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'teacher_id'     => 'required|exists:users,id',
            'year'           => 'required|integer|min:2024|max:2030',
            'month'          => 'required|integer|between:1,12',
            'basic_salary'   => 'required|numeric|min:0',
            'bonus'          => 'nullable|numeric|min:0',
            'deductions'     => 'nullable|numeric|min:0',
            'status'         => 'required|in:paid,pending',
            'payment_date'   => 'nullable|date',
            'payment_method' => 'nullable|string',
            'reference_no'   => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        $basic = (float)$request->basic_salary;
        $bonus = (float)($request->bonus ?? 0);
        $deductions = (float)($request->deductions ?? 0);
        $net = max(0, ($basic + $bonus) - $deductions);

        $salary = TeacherSalary::updateOrCreate(
            [
                'teacher_id' => $request->teacher_id,
                'year'       => $request->year,
                'month'      => $request->month,
            ],
            [
                'basic_salary'   => $basic,
                'bonus'          => $bonus,
                'deductions'     => $deductions,
                'net_salary'     => $net,
                'status'         => $request->status,
                'payment_date'   => $request->payment_date ?: ($request->status === 'paid' ? now()->toDateString() : null),
                'payment_method' => $request->payment_method ?: 'تحويل بنكي',
                'reference_no'   => $request->reference_no,
                'notes'          => $request->notes,
                'created_by'     => auth()->id(),
            ]
        );

        $teacher = User::find($request->teacher_id);
        $monthName = TeacherSalary::monthNamesAr()[$request->month] ?? "شهر {$request->month}";

        // إرسال إشعار فوري للمعلم
        try {
            if ($salary->status === 'paid') {
                DB::table('notifications')->insert([
                    'id'              => (string) \Illuminate\Support\Str::uuid(),
                    'type'            => 'App\\Notifications\\TeacherAlert',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $teacher->id,
                    'data'            => json_encode([
                        'title'      => "صرف راتب {$monthName} 💵",
                        'message'    => "تم إيداع وصرف راتبك لشهر ({$monthName} {$salary->year}) بمبلغ صافي ({$salary->net_salary} ₪). بإمكانك استعراض وطباعة قسيمة الراتب.",
                        'type'       => 'payment',
                        'action_url' => route('teacher.salaries.index', ['year' => $salary->year]),
                        'icon'       => 'fa-money-bill-transfer',
                        'created_at' => now()->toIso8601String(),
                    ], JSON_UNESCAPED_UNICODE),
                    'read_at'         => null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "تم تسجيل راتب المعلم ({$teacher->name}) لشهر ({$monthName}) بنجاح!",
                'salary'  => $salary
            ]);
        }

        return redirect()->back()->with('success', "تم تسجيل راتب المعلم ({$teacher->name}) لشهر ({$monthName}) بنجاح!");
    }

    /**
     * حذف سجل راتب
     */
    public function destroy($id)
    {
        $salary = TeacherSalary::findOrFail($id);
        $salary->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف سجل الراتب بنجاح.'
        ]);
    }
}
