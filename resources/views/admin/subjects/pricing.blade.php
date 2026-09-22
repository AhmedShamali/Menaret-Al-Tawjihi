@extends('layouts.app')

@section('title', __('إدارة أسعار المواد والعروض الترويجية'))

@section('content')
<style>
/* ========================================================
   تنسيقات شاشة إدارة تسعير المواد وباقات الاشتراك
   تصميم أكاديمي كلاسيكي فسيح ومنظم بدقة
   ======================================================== */
.pricing-page-wrapper {
    max-width: 1420px;
    margin: 0 auto;
    padding: 10px 20px 50px;
    animation: fadeIn 0.4s ease;
}

/* 1. الترويسة الرئيسية */
.pricing-header-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}
.pricing-header-title {
    display: flex;
    align-items: center;
    gap: 14px;
}
.pricing-header-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #e0f2fe, #dbeafe);
    color: #0284c7;
    display: grid;
    place-items: center;
    font-size: 1.35rem;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
}
.pricing-header-text h1 {
    font-size: 1.55rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
    letter-spacing: -0.3px;
}
.pricing-header-text p {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
}
.btn-seasonal-discount {
    background: linear-gradient(135deg, #059669, #10b981);
    color: #ffffff;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.92rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.28);
    transition: all 0.2s ease;
}
.btn-seasonal-discount:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
}

/* 2. بطاقات الإحصائيات الفسيحة */
.pricing-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    margin-bottom: 28px;
}
.pricing-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 22px 24px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.pricing-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
}
.pricing-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    height: 4px;
    background: var(--accent-color, #1e3a8a);
}
.pricing-stat-content .stat-title {
    display: block;
    color: #64748b;
    font-size: 0.86rem;
    font-weight: 700;
    margin-bottom: 8px;
}
.pricing-stat-content .stat-val {
    font-size: 1.85rem;
    font-weight: 800;
    color: #0f172a;
    font-family: 'Outfit', -apple-system, sans-serif;
    line-height: 1;
}
.pricing-stat-icon-wrap {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 1.45rem;
    background: var(--icon-bg, #f1f5f9);
    color: var(--accent-color, #1e3a8a);
}

/* 3. شريط تصفية الفروع */
.pricing-filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 26px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
}
.pricing-filter-label {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #1e293b;
    font-weight: 800;
    font-size: 0.92rem;
}
.pricing-filter-label i {
    color: #0284c7;
    font-size: 1rem;
}
.pricing-pills-list {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.pricing-pill {
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 0.86rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.pricing-pill.active {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
}
.pricing-pill:not(.active) {
    background: #f8fafc;
    color: #475569;
    border-color: #cbd5e1;
}
.pricing-pill:not(.active):hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* 4. كارت وجدول البيانات الأكاديمي */
.pricing-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.pricing-table-container {
    overflow-x: auto;
    width: 100%;
}
.pricing-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 1020px;
}
.pricing-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 0.86rem;
    font-weight: 800;
    padding: 16px 20px;
    border-bottom: 2px solid #e2e8f0;
    text-align: right;
    white-space: nowrap;
}
.pricing-table thead th.text-center {
    text-align: center;
}
.pricing-table tbody tr {
    transition: background 0.15s ease;
    border-bottom: 1px solid #f1f5f9;
}
.pricing-table tbody tr:hover {
    background: #f8fafc;
}
.pricing-table tbody tr:last-child {
    border-bottom: none;
}
.pricing-table tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    font-size: 0.92rem;
}

/* تفاصيل الأعمدة داخل الجدول */
.subject-meta-cell {
    display: flex;
    align-items: center;
    gap: 14px;
}
.subject-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    font-size: 1.35rem;
    box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
}
.subject-title-wrap strong {
    display: block;
    color: #0f172a;
    font-size: 1rem;
    font-weight: 800;
    margin-bottom: 3px;
}
.subject-title-wrap small {
    color: #64748b;
    font-size: 0.78rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* شارات الفروع الأكاديمية */
.branch-tag-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.82rem;
    font-weight: 800;
    white-space: nowrap;
}
.branch-sci {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}
.branch-lit {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.branch-bus {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.branch-gen {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
}

/* شارات الأسعار والخصومات */
.discount-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 0.82rem;
    font-weight: 800;
    font-family: monospace;
}
.discount-active {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}
.discount-free {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.discount-none {
    color: #94a3b8;
    font-size: 0.85rem;
}

/* شارات الحالة المعتمدة */
.status-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.82rem;
    font-weight: 800;
    white-space: nowrap;
}
.status-free-tag {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.status-disc-tag {
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fde68a;
}
.status-normal-tag {
    background: #f8fafc;
    color: #334155;
    border: 1px solid #e2e8f0;
}

/* زر التعديل */
.btn-edit-pricing-action {
    background: #ffffff;
    color: #1e3a8a;
    border: 1.5px solid #cbd5e1;
    padding: 8px 18px;
    border-radius: 10px;
    font-weight: 800;
    font-size: 0.85rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
    white-space: nowrap;
}
.btn-edit-pricing-action:hover {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(30, 58, 138, 0.2);
}
</style>

<div class="pricing-page-wrapper">

    <!-- 1. الترويسة الرئيسية -->
    <div class="pricing-header-box">
        <div class="pricing-header-title">
            <div class="pricing-header-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="pricing-header-text">
                <h1>{{ __('إدارة تسعير المواد وباقات الاشتراك') }}</h1>
                <p>{{ __('تحديد أسعار المواد بالشيكل (₪)، ضبط الخصومات الترويجية، وتفعيل المواد المجانية لطلبة توجيهي فلسطين.') }}</p>
            </div>
        </div>

        <button onclick="openSeasonalModal()" class="btn-seasonal-discount">
            <i class="fa-solid fa-percent"></i>
            <span>{{ __('تطبيق خصم موسمي شامل') }}</span>
        </button>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.15rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 2. كروت الإحصائيات الأكاديمية الفسيحة -->
    <div class="pricing-stats-grid">
        {{-- كرت إجمالي المواد --}}
        <div class="pricing-stat-card" style="--accent-color: #1e3a8a; --icon-bg: #eff6ff;">
            <div class="pricing-stat-content">
                <span class="stat-title">{{ __('إجمالي المواد الدراسية') }}</span>
                <span class="stat-val">{{ $pricingStats['total_subjects'] }} <span style="font-size: 0.95rem; font-weight: 600; color: #64748b;">{{ __('مادة') }}</span></span>
            </div>
            <div class="pricing-stat-icon-wrap">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
        </div>

        {{-- كرت المواد المخفضة --}}
        <div class="pricing-stat-card" style="--accent-color: #d97706; --icon-bg: #fffbeb;">
            <div class="pricing-stat-content">
                <span class="stat-title">{{ __('عروض وخصومات جارية') }}</span>
                <span class="stat-val" style="color: #b45309;">{{ $pricingStats['discounted'] }} <span style="font-size: 0.95rem; font-weight: 600; color: #b45309;">{{ __('مادة') }}</span></span>
            </div>
            <div class="pricing-stat-icon-wrap">
                <i class="fa-solid fa-fire"></i>
            </div>
        </div>

        {{-- كرت المواد المجانية --}}
        <div class="pricing-stat-card" style="--accent-color: #059669; --icon-bg: #ecfdf5;">
            <div class="pricing-stat-content">
                <span class="stat-title">{{ __('مواد مجانية / تجريبية') }}</span>
                <span class="stat-val" style="color: #047857;">{{ $pricingStats['free_subjects'] }} <span style="font-size: 0.95rem; font-weight: 600; color: #047857;">{{ __('مادة') }}</span></span>
            </div>
            <div class="pricing-stat-icon-wrap">
                <i class="fa-solid fa-gift"></i>
            </div>
        </div>

        {{-- كرت متوسط السعر --}}
        <div class="pricing-stat-card" style="--accent-color: #4f46e5; --icon-bg: #eef2ff;">
            <div class="pricing-stat-content">
                <span class="stat-title">{{ __('متوسط سعر المادة') }}</span>
                <span class="stat-val" style="color: #4338ca;">{{ round($pricingStats['avg_price']) }} <span style="font-size: 1.1rem; font-weight: 800;">₪</span></span>
            </div>
            <div class="pricing-stat-icon-wrap">
                <i class="fa-solid fa-shekel-sign"></i>
            </div>
        </div>
    </div>

    <!-- 3. شريط تصفية الفروع الأكاديمية بتصميم أنيق ومختصر -->
    <div class="pricing-filter-bar">
        <div class="pricing-filter-label">
            <i class="fa-solid fa-filter"></i>
            <span>{{ __('تصفية حسب الفرع الأكاديمي:') }}</span>
        </div>
        <div class="pricing-pills-list">
            <a href="{{ route('admin.subjects.pricing') }}" class="pricing-pill {{ empty($stageId) ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i>
                <span>{{ __('جميع الفروع') }} ({{ $pricingStats['total_subjects'] }})</span>
            </a>
            @foreach($stages as $stg)
                @php
                    $stgRaw = $stg->label_ar ?? $stg->name ?? $stg->name_ar ?? 'المرحلة';
                    if (str_contains($stgRaw, 'علمي')) {
                        $stgTitle = __('الفرع العلمي');
                        $stgIcon = 'fa-atom';
                    } elseif (str_contains($stgRaw, 'أدبي')) {
                        $stgTitle = __('الفرع الأدبي');
                        $stgIcon = 'fa-book-open';
                    } elseif (str_contains($stgRaw, 'ريادة') || str_contains($stgRaw, 'أعمال')) {
                        $stgTitle = __('فرع الريادة والأعمال');
                        $stgIcon = 'fa-briefcase';
                    } else {
                        $stgTitle = $stgRaw;
                        $stgIcon = 'fa-graduation-cap';
                    }
                @endphp
                <a href="{{ route('admin.subjects.pricing', ['stage_id' => $stg->id]) }}" class="pricing-pill {{ $stageId == $stg->id ? 'active' : '' }}">
                    <i class="fa-solid {{ $stgIcon }}"></i>
                    <span>{{ $stgTitle }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- 4. جدول أسعار المواد الأكاديمي بتصميم فسيح ومنظم -->
    <div class="pricing-table-card">
        <div class="pricing-table-container">
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th style="width: 260px;">{{ __('المادة الدراسية') }}</th>
                        <th style="width: 170px;">{{ __('الفرع الأكاديمي') }}</th>
                        <th style="width: 130px;">{{ __('السعر الأساسي') }}</th>
                        <th style="width: 120px;">{{ __('نسبة الخصم') }}</th>
                        <th style="width: 140px;">{{ __('السعر بعد الخصم') }}</th>
                        <th style="width: 220px;">{{ __('الحالة والتسعير الفعلي') }}</th>
                        <th style="width: 130px;" class="text-center">{{ __('الإجراء') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $sub)
                        @php
                            // ضبط مظهر وتسمية الفرع الدراسي بدون تشابك نصي
                            $rawStage = optional($sub->stage)->label_ar ?? optional($sub->stage)->name ?? optional($sub->stage)->name_ar ?? 'توجيهي عام';
                            if (str_contains($rawStage, 'علمي')) {
                                $stageShort = __('توجيهي علمي');
                                $stageClass = 'branch-sci';
                                $stageIcon  = 'fa-atom';
                            } elseif (str_contains($rawStage, 'أدبي')) {
                                $stageShort = __('توجيهي أدبي');
                                $stageClass = 'branch-lit';
                                $stageIcon  = 'fa-book-open';
                            } elseif (str_contains($rawStage, 'ريادة') || str_contains($rawStage, 'أعمال')) {
                                $stageShort = __('توجيهي ريادة');
                                $stageClass = 'branch-bus';
                                $stageIcon  = 'fa-briefcase';
                            } else {
                                $stageShort = $rawStage;
                                $stageClass = 'branch-gen';
                                $stageIcon  = 'fa-graduation-cap';
                            }

                            // فحص أيقونة المادة (هل هي إيموجي أم FontAwesome)
                            $subIcon = $sub->icon ?? 'fa-book';
                            $isFaIcon = str_starts_with($subIcon, 'fa-') || str_contains($subIcon, 'fa-');
                        @endphp
                        <tr>
                            {{-- المادة الدراسية والأيقونة --}}
                            <td>
                                <div class="subject-meta-cell">
                                    <div class="subject-icon-box" style="background: {{ $sub->color ?? '#0284c7' }}18; color: {{ $sub->color ?? '#0284c7' }};">
                                        @if($isFaIcon)
                                            <i class="fa-solid {{ $subIcon }}"></i>
                                        @else
                                            <span>{{ $subIcon }}</span>
                                        @endif
                                    </div>
                                    <div class="subject-title-wrap">
                                        <strong>{{ $sub->name_ar }}</strong>
                                        <small>
                                            <span><i class="fa-solid fa-play" style="font-size: 0.65rem; color: #0284c7;"></i> {{ $sub->contents_count ?? 0 }} {{ __('درس') }}</span>
                                            <span>•</span>
                                            <span><i class="fa-solid fa-file-pen" style="font-size: 0.65rem; color: #10b981;"></i> {{ $sub->exams_count ?? 0 }} {{ __('اختبار') }}</span>
                                        </small>
                                    </div>
                                </div>
                            </td>

                            {{-- الفرع الأكاديمي --}}
                            <td>
                                <span class="branch-tag-pill {{ $stageClass }}">
                                    <i class="fa-solid {{ $stageIcon }}"></i>
                                    <span>{{ $stageShort }}</span>
                                </span>
                            </td>

                            {{-- السعر الأساسي --}}
                            <td style="font-family: monospace; font-size: 1rem; font-weight: 700; color: #334155;">
                                {{ number_format($sub->price_ils ?? 150, 2) }} ₪
                            </td>

                            {{-- نسبة الخصم --}}
                            <td>
                                @if($sub->is_free)
                                    <span class="discount-badge discount-free">
                                        <i class="fa-solid fa-gift"></i> 100%
                                    </span>
                                @elseif($sub->has_discount)
                                    <span class="discount-badge discount-active">
                                        <i class="fa-solid fa-arrow-down" style="font-size: 0.72rem;"></i> {{ $sub->discount_percentage }}%
                                    </span>
                                @else
                                    <span class="discount-none">0%</span>
                                @endif
                            </td>

                            {{-- السعر بعد الخصم --}}
                            <td style="font-family: monospace; font-size: 1.05rem; font-weight: 800;">
                                @if($sub->is_free)
                                    <span style="color: #059669;">0.00 ₪ <small style="font-size: 0.75rem; font-weight: 700;">({{ __('مجاناً') }})</small></span>
                                @elseif($sub->has_discount)
                                    <span style="color: #ea580c;">{{ number_format($sub->price_after_discount, 2) }} ₪</span>
                                @else
                                    <span style="color: #1e293b;">{{ number_format($sub->price_ils ?? 150, 2) }} ₪</span>
                                @endif
                            </td>

                            {{-- الحالة والتسعير الفعلي --}}
                            <td>
                                @if($sub->is_free)
                                    <span class="status-pill-badge status-free-tag">
                                        <i class="fa-solid fa-gift"></i>
                                        <span>{{ __('مجانية تجريبية 100%') }}</span>
                                    </span>
                                @elseif($sub->has_discount)
                                    <span class="status-pill-badge status-disc-tag">
                                        <i class="fa-solid fa-fire"></i>
                                        <span>{{ __('خصم') }} {{ $sub->discount_percentage }}% • {{ number_format($sub->price_after_discount, 0) }} ₪</span>
                                    </span>
                                @else
                                    <span class="status-pill-badge status-normal-tag">
                                        <i class="fa-solid fa-check"></i>
                                        <span>{{ __('سعر معتمد:') }} {{ number_format($sub->price_ils ?? 150, 0) }} ₪</span>
                                    </span>
                                @endif
                            </td>

                            {{-- الإجراء --}}
                            <td class="text-center">
                                <button onclick="editPricing({{ json_encode($sub->append(['has_discount', 'discount_percentage', 'price_after_discount'])) }})" class="btn-edit-pricing-action">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>{{ __('تعديل السعر') }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 50px 20px; text-align: center; color: #94a3b8;">
                                <i class="fa-solid fa-tags" style="font-size: 2.2rem; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
                                <strong style="font-size: 1rem; color: #64748b;">{{ __('لا توجد مواد مسجلة مطابقة للبحث.') }}</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- نافذة تعديل تسعيرة مادة (Modal) بتصميم كلاسيكي وحاسبة تفاعلية متبادلة -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.65); backdrop-filter: blur(5px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 18px; max-width: 530px; width: 100%; padding: 28px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); border: 1px solid #cbd5e1; animation: zoomIn 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: grid; place-items: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div>
                    <h3 id="modalSubjectTitle" style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 2px;">{{ __('تعديل تسعيرة المادة') }}</h3>
                    <small style="color: #64748b; font-size: 0.78rem;">{{ __('حاسبة تفاعلية لحساب نسبة الخصم والسعر النهائي') }}</small>
                </div>
            </div>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.6rem; color: #94a3b8; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <form id="editPricingForm" onsubmit="submitPricing(event)">
            @csrf
            <input type="hidden" id="editSubjectId" name="subject_id">

            {{-- 1. السعر الأساسي --}}
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.86rem; font-weight: 700; color: #334155; margin-bottom: 6px;">{{ __('السعر الأساسي بالشيكل (₪) *') }}</label>
                <div style="position: relative;">
                    <input type="number" step="1" min="0" id="modalPrice" name="price_ils" required oninput="onBasePriceChange()" style="width: 100%; padding: 11px 14px 11px 40px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 1.05rem; font-weight: 800; font-family: monospace; color: #0f172a;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748b; font-weight: 800;">₪</span>
                </div>
            </div>

            {{-- 2. نسبة الخصم والسعر بعد الخصم تفاعليين جنباً إلى جنب --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div>
                    <label style="display: block; font-size: 0.84rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        <i class="fa-solid fa-percent" style="color: #d97706;"></i> {{ __('نسبة الخصم المئوية (%) *') }}
                    </label>
                    <div style="position: relative;">
                        <input type="number" step="1" min="0" max="100" id="modalDiscountPercent" name="discount_percentage" placeholder="0" oninput="onDiscountPercentChange()" style="width: 100%; padding: 11px 14px 11px 36px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 1rem; font-weight: 800; font-family: monospace; color: #dc2626;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #dc2626; font-weight: 800;">%</span>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.84rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        <i class="fa-solid fa-coins" style="color: #059669;"></i> {{ __('السعر بعد الخصم (₪)') }}
                    </label>
                    <div style="position: relative;">
                        <input type="number" step="0.5" min="0" id="modalDiscountPrice" name="discount_price_ils" placeholder="{{ __('نفس الأساسي') }}" oninput="onDiscountPriceChange()" style="width: 100%; padding: 11px 14px 11px 40px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 1rem; font-weight: 800; font-family: monospace; color: #ea580c;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #ea580c; font-weight: 800;">₪</span>
                    </div>
                </div>
            </div>

            {{-- بطاقة توضيحية لملخص الحساب الحي --}}
            <div id="liveCalcBox" style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; font-size: 0.86rem;">
                <span style="color: #64748b; font-weight: 600;">{{ __('قيمة التوفير للطالب:') }} <strong class="font-mono text-rose font-bold" id="previewSavedAmount" style="color: #e11d48; margin-right: 4px;">0 ₪</strong></span>
                <span style="color: #334155; font-weight: 700;">{{ __('السعر النهائي المقرر:') }} <strong class="font-mono text-emerald font-bold" style="font-size: 1.15rem; color: #059669; margin-right: 4px;" id="previewFinalPrice">150 ₪</strong></span>
            </div>

            {{-- 3. خيار المادة المجانية --}}
            <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: 12px; padding: 12px 16px; margin-bottom: 18px; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="modalIsFree" name="is_free" value="1" onchange="onIsFreeChange()" style="width: 18px; height: 18px; accent-color: #059669; cursor: pointer;">
                <label for="modalIsFree" style="font-size: 0.88rem; font-weight: 800; color: #065f46; cursor: pointer; margin: 0;">
                    {{ __('تعيين المادة كمجانية بالكامل لكافة الطلاب (0 ₪)') }}
                </label>
            </div>

            {{-- 4. وصف الباقة --}}
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 0.84rem; font-weight: 700; color: #475569; margin-bottom: 6px;">{{ __('وصف باقة المادة ومميزاتها للطلاب') }}</label>
                <textarea id="modalDescription" name="description" rows="2" placeholder="{{ __('مثال: تشمل شرح كامل المنهاج الوزاري، حلول أسئلة السنوات السابقة، وبطاقات المذاكرة السريعة') }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 0.88rem; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeEditModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer;">{{ __('إلغاء') }}</button>
                <button type="submit" id="btnSavePrice" style="background: #1e3a8a; color: white; border: none; padding: 10px 26px; border-radius: 10px; font-weight: 800; font-size: 0.92rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25);">
                    <i class="fa-solid fa-check"></i> {{ __('حفظ وتطبيق التسعيرة') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- نافذة الخصم الشامل (Seasonal Modal) -->
<div id="seasonalModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.65); backdrop-filter: blur(5px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 18px; max-width: 500px; width: 100%; padding: 28px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); border: 1px solid #cbd5e1;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #ecfdf5; color: #059669; display: grid; place-items: center; font-size: 1.15rem;">
                    <i class="fa-solid fa-percent"></i>
                </div>
                <h3 style="font-size: 1.18rem; font-weight: 800; color: #0f172a; margin: 0;">{{ __('تطبيق خصم موسمي شامل') }}</h3>
            </div>
            <button onclick="closeSeasonalModal()" style="background: none; border: none; font-size: 1.6rem; color: #94a3b8; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <form action="{{ route('admin.subjects.pricing.seasonal') }}" method="POST">
            @csrf
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.86rem; font-weight: 700; color: #475569; margin-bottom: 6px;">{{ __('نسبة الخصم المئوية (%) *') }}</label>
                <input type="number" name="discount_percentage" min="5" max="90" value="20" required style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 1.15rem; font-weight: 800; color: #059669; font-family: monospace;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.86rem; font-weight: 700; color: #475569; margin-bottom: 6px;">{{ __('تطبيق الخصم على فرع محدد (اختياري)') }}</label>
                <select name="stage_id" style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 0.92rem; color: #0f172a;">
                    <option value="">{{ __('جميع فروع الثانوية العامة') }}</option>
                    @foreach($stages as $stg)
                        @php
                            $stgRaw = $stg->label_ar ?? $stg->name ?? $stg->name_ar;
                            if (str_contains($stgRaw, 'علمي')) $stgTitle = __('الفرع العلمي');
                            elseif (str_contains($stgRaw, 'أدبي')) $stgTitle = __('الفرع الأدبي');
                            elseif (str_contains($stgRaw, 'ريادة') || str_contains($stgRaw, 'أعمال')) $stgTitle = __('فرع الريادة والأعمال');
                            else $stgTitle = $stgRaw;
                        @endphp
                        <option value="{{ $stg->id }}">{{ $stgTitle }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeSeasonalModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer;">{{ __('إلغاء') }}</button>
                <button type="submit" style="background: #059669; color: white; border: none; padding: 10px 26px; border-radius: 10px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);">
                    <i class="fa-solid fa-check"></i> {{ __('تطبيق الخصم فوراً') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentSubject = null;

function editPricing(sub) {
    currentSubject = sub;
    document.getElementById('editSubjectId').value = sub.id;
    document.getElementById('modalSubjectTitle').textContent = 'تعديل تسعيرة: ' + sub.name_ar;
    
    const basePrice = parseFloat(sub.price_ils) || 150;
    const discountPrice = (sub.discount_price_ils !== null && parseFloat(sub.discount_price_ils) > 0) ? parseFloat(sub.discount_price_ils) : '';
    const isFree = (sub.is_free == 1);
    
    document.getElementById('modalPrice').value = basePrice;
    document.getElementById('modalDiscountPrice').value = discountPrice;
    document.getElementById('modalIsFree').checked = isFree;
    document.getElementById('modalDescription').value = sub.description || '';

    if (discountPrice && basePrice > 0 && discountPrice < basePrice) {
        const pct = Math.round(((basePrice - discountPrice) / basePrice) * 100);
        document.getElementById('modalDiscountPercent').value = pct;
    } else {
        document.getElementById('modalDiscountPercent').value = '';
    }

    updateLiveCalcBox();

    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}

function onBasePriceChange() {
    const isFree = document.getElementById('modalIsFree').checked;
    if (isFree) return;

    const basePrice = parseFloat(document.getElementById('modalPrice').value) || 0;
    const pct = parseFloat(document.getElementById('modalDiscountPercent').value) || 0;

    if (pct > 0 && basePrice > 0) {
        const after = Math.round((basePrice * (1 - (pct / 100))) * 100) / 100;
        document.getElementById('modalDiscountPrice').value = after;
    }
    updateLiveCalcBox();
}

function onDiscountPercentChange() {
    const isFree = document.getElementById('modalIsFree').checked;
    if (isFree) return;

    const basePrice = parseFloat(document.getElementById('modalPrice').value) || 0;
    const pct = parseFloat(document.getElementById('modalDiscountPercent').value);

    if (basePrice > 0 && !isNaN(pct) && pct >= 0 && pct <= 100) {
        if (pct === 0) {
            document.getElementById('modalDiscountPrice').value = '';
        } else {
            const after = Math.round((basePrice * (1 - (pct / 100))) * 100) / 100;
            document.getElementById('modalDiscountPrice').value = after;
        }
    }
    updateLiveCalcBox();
}

function onDiscountPriceChange() {
    const isFree = document.getElementById('modalIsFree').checked;
    if (isFree) return;

    const basePrice = parseFloat(document.getElementById('modalPrice').value) || 0;
    const after = parseFloat(document.getElementById('modalDiscountPrice').value);

    if (basePrice > 0 && !isNaN(after) && after >= 0) {
        if (after >= basePrice) {
            document.getElementById('modalDiscountPercent').value = '0';
        } else {
            const pct = Math.round(((basePrice - after) / basePrice) * 100);
            document.getElementById('modalDiscountPercent').value = pct;
        }
    } else if (isNaN(after) || after === 0) {
        document.getElementById('modalDiscountPercent').value = '';
    }
    updateLiveCalcBox();
}

function onIsFreeChange() {
    const isFree = document.getElementById('modalIsFree').checked;
    const pInput = document.getElementById('modalPrice');
    const pctInput = document.getElementById('modalDiscountPercent');
    const afterInput = document.getElementById('modalDiscountPrice');

    if (isFree) {
        pctInput.value = '100';
        afterInput.value = '0';
        pctInput.disabled = true;
        afterInput.disabled = true;
    } else {
        pctInput.disabled = false;
        afterInput.disabled = false;
        onBasePriceChange();
    }
    updateLiveCalcBox();
}

function updateLiveCalcBox() {
    const isFree = document.getElementById('modalIsFree').checked;
    const basePrice = parseFloat(document.getElementById('modalPrice').value) || 0;
    const after = parseFloat(document.getElementById('modalDiscountPrice').value);

    let finalPrice = basePrice;
    let saved = 0;

    if (isFree) {
        finalPrice = 0;
        saved = basePrice;
    } else if (!isNaN(after) && after > 0 && after < basePrice) {
        finalPrice = after;
        saved = Math.max(0, basePrice - after);
    }

    document.getElementById('previewSavedAmount').innerText = saved.toFixed(2) + ' ₪';
    document.getElementById('previewFinalPrice').innerText = finalPrice.toFixed(2) + ' ₪';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openSeasonalModal() {
    document.getElementById('seasonalModal').style.display = 'flex';
}

function closeSeasonalModal() {
    document.getElementById('seasonalModal').style.display = 'none';
}

function submitPricing(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSavePrice');
    const id = document.getElementById('editSubjectId').value;
    const price = document.getElementById('modalPrice').value;
    const discountPrice = document.getElementById('modalDiscountPrice').value;
    const discountPercent = document.getElementById('modalDiscountPercent').value;
    const isFree = document.getElementById('modalIsFree').checked ? 1 : 0;
    const desc = document.getElementById('modalDescription').value;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

    axios.post(`/admin/subjects/pricing/${id}/update`, {
        price_ils: price,
        discount_price_ils: discountPrice,
        discount_percentage: discountPercent,
        is_free: isFree,
        description: desc
    })
    .then(res => {
        closeEditModal();
        Swal.fire({
            icon: 'success',
            title: '{{ __('تم التحديث بنجاح!') }}',
            text: res.data.message || '{{ __('تم تحديث تسعيرة المادة بنجاح.') }}',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.reload();
        });
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> {{ __('حفظ وتطبيق التسعيرة') }}';
        const msg = err.response?.data?.message || '{{ __('تعذر تحديث التسعيرة.') }}';
        Swal.fire({ icon: 'error', title: '{{ __('خطأ') }}', text: msg });
    });
}
</script>
@endsection
