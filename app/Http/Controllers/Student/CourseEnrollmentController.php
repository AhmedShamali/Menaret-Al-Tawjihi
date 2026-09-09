<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Stage;
use App\Models\Enrollment;
use App\Support\CurrentActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseEnrollmentController extends Controller
{
    /**
     * كتالوج المواد وباقات الاشتراك لطلبة توجيهي فلسطين
     */
    public function catalog(Request $request)
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        $stageId = $request->query('stage_id') ?? $student?->stage_id;

        $query = Subject::with(['stage', 'teacher'])->withCount(['contents', 'exams']);
        if ($stageId) {
            $query->where('stage_id', $stageId);
        }

        $subjects = $query->get();
        $stages = Stage::all();

        // معرفات المواد التي يشترك فيها الطالب حالياً (فقط إن كان حسابه واشتراكه معتمداً ومفعلاً من المدير)
        $enrolledSubjectIds = [];
        $pendingSubjectIds  = [];
        if ($student && $student->status === 'active') {
            $enrolledSubjectIds = Enrollment::where('student_id', $student->id)
                ->where('status', 'active')
                ->pluck('subject_id')
                ->toArray();

            $pendingSubjectIds = Enrollment::where('student_id', $student->id)
                ->where('status', 'pending')
                ->pluck('subject_id')
                ->toArray();
        } elseif ($student) {
            // إذا كان حساب الطالب لم يعتمد بعد، فجميع المواد غير مفعلة
            $pendingSubjectIds = Enrollment::where('student_id', $student->id)
                ->pluck('subject_id')
                ->toArray();
        }

        return view('student.courses.catalog', compact('subjects', 'stages', 'enrolledSubjectIds', 'pendingSubjectIds', 'stageId', 'student'));
    }

    /**
     * تجهيز سلة المواد والانتقال لبوابة الدفع
     */
    public function prepareCheckout(Request $request)
    {
        $request->validate([
            'subject_ids'   => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
        ], [
            'subject_ids.required' => 'يرجى اختيار مادة واحدة على الأقل للاشتراك.',
            'subject_ids.min'      => 'يرجى اختيار مادة واحدة على الأقل للاشتراك.',
        ]);

        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً لإتمام الاشتراك.');
        }

        $selectedSubjects = Subject::whereIn('id', $request->subject_ids)->get();

        $subtotal = 0;
        $items = [];

        foreach ($selectedSubjects as $sub) {
            $effectivePrice = $sub->effective_price;
            $subtotal += $effectivePrice;

            $items[] = [
                'id'         => $sub->id,
                'name_ar'    => $sub->name_ar,
                'stage'      => optional($sub->stage)->name ?? optional($sub->stage)->name_ar ?? 'توجيهي',
                'price'      => $effectivePrice,
                'orig_price' => (float) $sub->price_ils,
                'is_free'    => (bool) $sub->is_free
            ];
        }

        // خصم باقة التوجيهي (Bundle Discount): إذا اختار 3 مواد أو أكثر، يمنح خصم إضافي 15%
        $bundleDiscount = 0;
        if (count($items) >= 3 && $subtotal > 0) {
            $bundleDiscount = round($subtotal * 0.15, 2);
        }

        $totalAmount = max(0, $subtotal - $bundleDiscount);

        // تخزين بيانات السلة في الجلسة للانتقال إلى بوابة الدفع الفلسطينية
        session([
            'checkout_cart' => [
                'student_id'      => $student->id,
                'items'           => $items,
                'subtotal'        => $subtotal,
                'bundle_discount' => $bundleDiscount,
                'total'           => $totalAmount,
                'currency'        => 'ILS'
            ]
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'   => 'success',
                'redirect' => route('student.checkout.show')
            ]);
        }

        return redirect()->route('student.checkout.show');
    }
}
