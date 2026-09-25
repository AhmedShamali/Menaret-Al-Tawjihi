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
     * دليل ومقررات المنهاج الفلسطيني لطلبة الثانوية العامة (التوجيهي)
     */
    public function catalog(Request $request)
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        $stageId = $request->query('stage_id');
        $branch  = $request->query('branch');

        // دعم التصفية حسب اسم الفرع القادم من الصفحة الرئيسية
        if (!$stageId && $branch) {
            $branchSlug = strtolower(trim($branch));
            if (in_array($branchSlug, ['scientific', 'sci', 'علمي'])) {
                $stageId = Stage::where('grade_level', 122)->orWhere('label_ar', 'LIKE', '%علمي%')->value('id');
            } elseif (in_array($branchSlug, ['literary', 'lit', 'أدبي'])) {
                $stageId = Stage::where('grade_level', 121)->orWhere('label_ar', 'LIKE', '%أدبي%')->value('id');
            } elseif (in_array($branchSlug, ['business', 'entrepreneurship', 'ريادة'])) {
                $stageId = Stage::where('grade_level', 123)->orWhere('label_ar', 'LIKE', '%ريادة%')->value('id');
            } elseif (in_array($branchSlug, ['vocational', 'sharia', 'شرعي', 'صناعي'])) {
                $stageId = Stage::where('label_ar', 'LIKE', '%صناعي%')->orWhere('label_ar', 'LIKE', '%شرعي%')->value('id');
            }
        }

        // إذا كان طالباً مسجلاً ولم يحدد فرعاً معيناً، يُعرض له فرعه الدراسي أولاً مع إمكانية التبديل
        if (!$stageId && !$branch && $student && $student->stage_id) {
            $stageId = $student->stage_id;
        }

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

        // حظر شراء أو الاشتراك في مواد تتبع فرعاً دراسياً غير فرع الطالب الأصلي
        if ($student->stage_id) {
            $invalidSubject = $selectedSubjects->first(function ($sub) use ($student) {
                return $sub->stage_id && (int)$sub->stage_id !== (int)$student->stage_id;
            });

            if ($invalidSubject) {
                return redirect()->route('student.courses.catalog')->with('error', 'عذراً، يمكنك الاشتراك فقط في مواد فرعك الدراسي الأصلي (' . optional($student->stage)->label_ar . ').');
            }
        }

        $subtotal = 0;
        $items = [];

        foreach ($selectedSubjects as $sub) {
            $effectivePrice = $sub->effective_price;
            $subtotal += $effectivePrice;

            $items[] = [
                'id'                  => $sub->id,
                'name_ar'             => $sub->name_ar,
                'stage'               => optional($sub->stage)->name ?? optional($sub->stage)->name_ar ?? 'توجيهي',
                'price'               => $effectivePrice,
                'orig_price'          => (float) $sub->price_ils,
                'has_discount'        => (bool) $sub->has_discount,
                'discount_percentage' => (int) $sub->discount_percentage,
                'is_free'             => (bool) $sub->is_free
            ];
        }

        // خصم باقة التوجيهي (Bundle Discount): إذا اختار 3 مواد أو أكثر، يمنح خصم إضافي 15%
        $bundleDiscount = 0;
        if (count($items) >= 3 && $subtotal > 0) {
            $bundleDiscount = round($subtotal * 0.15, 2);
        }

        $baseAfterBundle = max(0, $subtotal - $bundleDiscount);

        // حساب الخصم أو المنحة المخصصة للطالب من قِبل المدير
        $studentDiscount = 0;
        $studentDiscountLabel = null;
        $customPercent = (float) ($student->custom_discount_percent ?? 0);
        $customFixed = (float) ($student->custom_discount_fixed ?? 0);

        if ($customPercent >= 100) {
            $studentDiscount = $baseAfterBundle;
            $studentDiscountLabel = 'إعفاء ومنحة كاملة 100%';
        } elseif ($customPercent > 0) {
            $studentDiscount = round($baseAfterBundle * ($customPercent / 100), 2);
            $studentDiscountLabel = 'خصم خاص من الإدارة (' . round($customPercent) . '%)';
        } elseif ($customFixed > 0) {
            $studentDiscount = min($baseAfterBundle, round($customFixed, 2));
            $studentDiscountLabel = 'خصم خاص من الإدارة (' . round($customFixed) . ' ₪)';
        }

        $totalAmount = max(0, $baseAfterBundle - $studentDiscount);

        // تخزين بيانات السلة في الجلسة للانتقال إلى بوابة الدفع الفلسطينية
        session([
            'checkout_cart' => [
                'student_id'              => $student->id,
                'items'                   => $items,
                'subtotal'                => $subtotal,
                'bundle_discount'         => $bundleDiscount,
                'student_discount'        => $studentDiscount,
                'student_discount_label'  => $studentDiscountLabel,
                'student_discount_notes'  => $student->discount_notes,
                'custom_discount_percent' => $customPercent,
                'custom_discount_fixed'   => $customFixed,
                'total'                   => $totalAmount,
                'currency'                => 'ILS'
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
