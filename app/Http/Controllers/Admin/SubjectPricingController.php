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

        // إحصائيات سريعة للأسعار
        $pricingStats = [
            'total_subjects'  => $subjects->count(),
            'free_subjects'   => $subjects->where('is_free', true)->count(),
            'discounted'      => $subjects->filter(fn($s) => $s->discount_price_ils > 0 && !$s->is_free)->count(),
            'avg_price'       => $subjects->avg('price_ils') ?? 150,
        ];

        return view('admin.subjects.pricing', compact('subjects', 'stages', 'pricingStats', 'stageId'));
    }

    /**
     * تحديث سعر مادة دراسية محددة
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'price_ils'           => 'required|numeric|min:0',
            'discount_price_ils'  => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_free'             => 'nullable|boolean',
            'description'         => 'nullable|string|max:1000',
        ], [
            'price_ils.required' => 'يرجى إدخال السعر الأساسي بالشيكل.',
            'price_ils.min'      => 'السعر يجب أن لا يكون سالباً.',
        ]);

        $subject = Subject::findOrFail($id);

        $isFree = $request->has('is_free') && ($request->is_free == '1' || $request->is_free === true);
        $price = (float) $request->price_ils;
        $discountPrice = $request->filled('discount_price_ils') ? (float) $request->discount_price_ils : null;

        // إذا أدخل نسبة الخصم ولم يدخل السعر بعد الخصم
        if ($request->filled('discount_percentage') && (float)$request->discount_percentage > 0 && !$request->filled('discount_price_ils') && $price > 0) {
            $pct = (float) $request->discount_percentage;
            $discountPrice = round($price * (1 - ($pct / 100)), 2);
        }

        // إذا كانت مجانية
        if ($isFree) {
            $discountPrice = null;
        }

        $subject->update([
            'price_ils'          => $price,
            'discount_price_ils' => $discountPrice,
            'is_free'            => $isFree,
            'description'        => $request->description,
        ]);

        $subject->refresh();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'                => 'success',
                'message'               => "تم تحديث تسعيرة ({$subject->name_ar}) بنجاح! 💰",
                'subject'               => $subject,
                'has_discount'          => $subject->has_discount,
                'discount_percentage'   => $subject->discount_percentage,
                'price_after_discount'  => $subject->price_after_discount,
                'effective_price'       => $subject->effective_price,
            ]);
        }

        return back()->with('success', "تم تحديث تسعيرة ({$subject->name_ar}) بنجاح! 💰");
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
