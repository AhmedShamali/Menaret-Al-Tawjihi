<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Stage;
use Illuminate\Http\Request;

class SubjectPricingController extends Controller
{
    /**
     * عرض قائمة أسعار المواد والتحكم بالعروض الترويجية
     */
    public function index(Request $request)
    {
        $stageId = $request->query('stage_id');
        $query = Subject::with(['stage', 'teacher'])->withCount(['contents', 'exams']);

        if ($stageId) {
            $query->where('stage_id', $stageId);
        }

        $subjects = $query->orderBy('stage_id')->get();
        $stages = Stage::all();

        // إحصائيات سريعة للأسعار الفصليّة والمناطقيّة
        $pricingStats = [
            'total_subjects'    => $subjects->count(),
            'free_subjects'     => $subjects->where('is_free', true)->count(),
            'discounted'        => $subjects->filter(fn($s) => $s->discount_price_ils > 0 && !$s->is_free)->count(),
            'avg_term_wb'       => round($subjects->avg('price_term_1') ?? 150),
            'avg_term_gaza'     => round($subjects->avg('price_term_1_gaza') ?? 100),
            'avg_full_wb'       => round($subjects->avg('price_full_year') ?? 300),
            'avg_full_gaza'     => round($subjects->avg('price_full_year_gaza') ?? 200),
        ];

        return view('admin.subjects.pricing', compact('subjects', 'stages', 'pricingStats', 'stageId'));
    }

    /**
     * تحديث تسعيرة مادة دراسية محددة للفصول الدراسية ومناطق الضفة وغزة
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            // أسعار الضفة الغربية والقدس
            'price_term_1'         => 'nullable|numeric|min:0',
            'price_term_2'         => 'nullable|numeric|min:0',
            'price_full_year'      => 'nullable|numeric|min:0',
            'price_ils'            => 'nullable|numeric|min:0',

            // أسعار قطاع غزة
            'price_term_1_gaza'    => 'nullable|numeric|min:0',
            'price_term_2_gaza'    => 'nullable|numeric|min:0',
            'price_full_year_gaza' => 'nullable|numeric|min:0',

            'discount_percentage'  => 'nullable|numeric|min:0|max:100',
            'discount_price_ils'   => 'nullable|numeric|min:0',
            'is_free'              => 'nullable|boolean',
            'description'          => 'nullable|string|max:1000',
        ]);

        $subject = Subject::findOrFail($id);

        $isFree = $request->has('is_free') && ($request->is_free == '1' || $request->is_free === true);
        
        $pFullWb = $request->filled('price_full_year') 
            ? (float) $request->price_full_year 
            : ($request->filled('price_ils') ? (float) $request->price_ils : (float) ($subject->price_full_year ?: $subject->price_ils));

        $pTerm1Wb = $request->filled('price_term_1') 
            ? (float) $request->price_term_1 
            : round($pFullWb / 2, 2);

        $pTerm2Wb = $request->filled('price_term_2') 
            ? (float) $request->price_term_2 
            : round($pFullWb / 2, 2);

        // أسعار غزة (إن لم تدخل، تحسب تلقائياً بنسبة مناسبة أو مطابقة)
        $pTerm1Gaza = $request->filled('price_term_1_gaza') ? (float)$request->price_term_1_gaza : round($pTerm1Wb * 0.6, 2);
        $pTerm2Gaza = $request->filled('price_term_2_gaza') ? (float)$request->price_term_2_gaza : round($pTerm2Wb * 0.6, 2);
        $pFullGaza  = $request->filled('price_full_year_gaza') ? (float)$request->price_full_year_gaza : round($pFullWb * 0.6, 2);

        if ($isFree) {
            $pTerm1Wb = 0.00;
            $pTerm2Wb = 0.00;
            $pFullWb  = 0.00;
            $pTerm1Gaza = 0.00;
            $pTerm2Gaza = 0.00;
            $pFullGaza  = 0.00;
        }

        // الخصومات والعروض
        $discountPrice = null;
        $discountPct = null;
        if ($request->filled('discount_percentage') && (float)$request->discount_percentage > 0) {
            $discountPct = (float) $request->discount_percentage;
            $discountPrice = max(0, round($pFullWb - ($pFullWb * ($discountPct / 100)), 2));
        } elseif ($request->filled('discount_price_ils') && (float)$request->discount_price_ils > 0) {
            $discountPrice = (float) $request->discount_price_ils;
            $discountPct = $pFullWb > 0 ? round((($pFullWb - $discountPrice) / $pFullWb) * 100, 1) : 0;
        }

        $subject->update([
            'price_term_1'         => $pTerm1Wb,
            'price_term_2'         => $pTerm2Wb,
            'price_full_year'      => $pFullWb,
            'price_term_1_gaza'    => $pTerm1Gaza,
            'price_term_2_gaza'    => $pTerm2Gaza,
            'price_full_year_gaza' => $pFullGaza,
            'price_ils'            => $pFullWb, // الحفاظ على الحقل القديم متوافقاً
            'discount_price_ils'   => $discountPrice,
            'discount_percentage'  => $discountPct,
            'is_free'              => $isFree,
            'description'          => $request->description,
        ]);

        $subject->refresh();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'                => 'success',
                'message'               => "تم تحديث تسعيرة ({$subject->name_ar}) الفصليّة للضفة وغزة بنجاح! 💰",
                'subject'               => $subject,
                'price_ils'             => $subject->price_ils,
                'has_discount'          => (bool)$subject->has_discount,
                'discount_percentage'   => (float)$subject->discount_percentage,
                'price_after_discount'  => (float)$subject->price_after_discount,
            ]);
        }

        return back()->with('success', "تم تحديث تسعيرة ({$subject->name_ar}) الفصليّة للضفة وغزة بنجاح! 💰");
    }

    /**
     * تطبيق خصم جماعي أو عروض موسمية (مثال: تخفيض كل المواد 20% بمناسبة الفصل الثاني)
     */
    public function applySeasonalDiscount(Request $request)
    {
        $request->validate([
            'discount_percentage' => 'required|numeric|min:5|max:90',
            'stage_id'            => 'nullable|exists:stages,id',
        ]);

        $percentage = (float) $request->discount_percentage;
        $query = Subject::query();
        if ($request->stage_id) {
            $query->where('stage_id', $request->stage_id);
        }

        $subjects = $query->get();
        foreach ($subjects as $sub) {
            if (!$sub->is_free && $sub->price_ils > 0) {
                $discountPrice = round($sub->price_ils * (1 - ($percentage / 100)), 2);
                $sub->update([
                    'discount_price_ils' => $discountPrice
                ]);
            }
        }

        return back()->with('success', "تم تطبيق خصم {$percentage}% بنجاح على المواد المحددة! 🎉");
    }
}
