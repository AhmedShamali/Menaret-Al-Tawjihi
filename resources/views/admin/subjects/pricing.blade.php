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

    <!-- 2. كروت الإحصائيات الأكاديمية الفسيحة للأسعار الفصليّة والمناطقيّة -->
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

        {{-- كرت متوسط ف1 للضفة --}}
        <div class="pricing-stat-card" style="--accent-color: #0284c7; --icon-bg: #f0f9ff;">
            <div class="pricing-stat-content">
                <span class="stat-title">{{ __('متوسط ف1 (الضفة)') }}</span>
                <span class="stat-val" style="color: #0369a1;">{{ $pricingStats['avg_term_wb'] }} <span style="font-size: 1.1rem; font-weight: 800;">₪</span></span>
            </div>
            <div class="pricing-stat-icon-wrap">
                <i class="fa-solid fa-landmark"></i>
            </div>
        </div>

        {{-- كرت متوسط ف1 لغزة --}}
        <div class="pricing-stat-card" style="--accent-color: #059669; --icon-bg: #ecfdf5;">
            <div class="pricing-stat-content">
                <span class="stat-title">{{ __('متوسط ف1 (غزة)') }}</span>
                <span class="stat-val" style="color: #047857;">{{ $pricingStats['avg_term_gaza'] }} <span style="font-size: 1.1rem; font-weight: 800;">₪</span></span>
            </div>
            <div class="pricing-stat-icon-wrap">
                <i class="fa-solid fa-location-dot"></i>
            </div>
        </div>

        {{-- كرت المواد المجانية --}}
        <div class="pricing-stat-card" style="--accent-color: #7c3aed; --icon-bg: #f5f3ff;">
            <div class="pricing-stat-content">
                <span class="stat-title">{{ __('مواد مجانية بالكامل') }}</span>
                <span class="stat-val" style="color: #6d28d9;">{{ $pricingStats['free_subjects'] }} <span style="font-size: 0.95rem; font-weight: 600; color: #6d28d9;">{{ __('مادة') }}</span></span>
            </div>
            <div class="pricing-stat-icon-wrap">
                <i class="fa-solid fa-gift"></i>
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

    <!-- 4. جدول أسعار المواد الأكاديمي بتصميم فسيح ومنظم فصلياً ومناطقيّاً -->
    <div class="pricing-table-card">
        <div class="pricing-table-container">
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th style="width: 250px;">{{ __('المادة الدراسية') }}</th>
                        <th style="width: 150px;">{{ __('الفرع') }}</th>
                        <th style="width: 240px;">
                            <span style="color: #1e40af; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-landmark"></i> {{ __('تسعيرة الضفة') }}
                            </span>
                        </th>
                        <th style="width: 240px;">
                            <span style="color: #065f46; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-location-dot"></i> {{ __('تسعيرة غزة') }}
                            </span>
                        </th>
                        <th style="width: 140px;">{{ __('الحالة') }}</th>
                        <th style="width: 130px;" class="text-center">{{ __('الإجراء') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $sub)
                        @php
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

                            $subIcon = $sub->icon ?? 'fa-book';
                            $isFaIcon = str_starts_with($subIcon, 'fa-') || str_contains($subIcon, 'fa-');

                            $p1Wb = $sub->getSemesterPrice('term_1', 'west_bank');
                            $p2Wb = $sub->getSemesterPrice('term_2', 'west_bank');
                            $pFullWb = $sub->getSemesterPrice('both', 'west_bank');

                            $p1Gaza = $sub->getSemesterPrice('term_1', 'gaza');
                            $p2Gaza = $sub->getSemesterPrice('term_2', 'gaza');
                            $pFullGaza = $sub->getSemesterPrice('both', 'gaza');
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

                            {{-- تسعيرة الضفة والقدس --}}
                            <td>
                                @if($sub->is_free)
                                    <span style="color: #059669; font-weight: 800;"><i class="fa-solid fa-gift"></i> {{ __('مجانية 100%') }}</span>
                                @else
                                    <div style="display: flex; flex-direction: column; gap: 3px; font-size: 0.85rem;">
                                        <span style="color: #1e3a8a;"><strong style="font-weight: 700;">فصل 1:</strong> <span class="font-mono font-bold">{{ number_format($p1Wb, 0) }} ₪</span></span>
                                        <span style="color: #1e3a8a;"><strong style="font-weight: 700;">فصل 2:</strong> <span class="font-mono font-bold">{{ number_format($p2Wb, 0) }} ₪</span></span>
                                        <span style="color: #0f172a; font-weight: 800; background: #eff6ff; padding: 2px 6px; border-radius: 6px; display: inline-block; width: fit-content;">
                                            {{ __('الفصلين:') }} <span class="font-mono text-primary font-bold">{{ number_format($pFullWb, 0) }} ₪</span>
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- تسعيرة قطاع غزة --}}
                            <td>
                                @if($sub->is_free)
                                    <span style="color: #059669; font-weight: 800;"><i class="fa-solid fa-gift"></i> {{ __('مجانية 100%') }}</span>
                                @else
                                    <div style="display: flex; flex-direction: column; gap: 3px; font-size: 0.85rem;">
                                        <span style="color: #065f46;"><strong style="font-weight: 700;">فصل 1:</strong> <span class="font-mono font-bold">{{ number_format($p1Gaza, 0) }} ₪</span></span>
                                        <span style="color: #065f46;"><strong style="font-weight: 700;">فصل 2:</strong> <span class="font-mono font-bold">{{ number_format($p2Gaza, 0) }} ₪</span></span>
                                        <span style="color: #064e3b; font-weight: 800; background: #ecfdf5; padding: 2px 6px; border-radius: 6px; display: inline-block; width: fit-content;">
                                            {{ __('الفصلين:') }} <span class="font-mono text-emerald font-bold">{{ number_format($pFullGaza, 0) }} ₪</span>
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- الحالة --}}
                            <td>
                                @if($sub->is_free)
                                    <span class="status-pill-badge status-free-tag">
                                        <i class="fa-solid fa-gift"></i>
                                        <span>{{ __('مجانية تجريبية') }}</span>
                                    </span>
                                @else
                                    <span class="status-pill-badge status-normal-tag">
                                        <i class="fa-solid fa-check"></i>
                                        <span>{{ __('نظام فصلي معتمد') }}</span>
                                    </span>
                                @endif
                            </td>

                            {{-- الإجراء --}}
                            <td class="text-center">
                                <button onclick="editPricing({{ json_encode($sub) }})" class="btn-edit-pricing-action">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>{{ __('تعديل التسعيرة') }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 50px 20px; text-align: center; color: #94a3b8;">
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

<!-- نافذة تعديل تسعيرة مادة (Modal) بنظام فصلي للضفة وغزة -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.65); backdrop-filter: blur(5px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 18px; max-width: 650px; width: 100%; padding: 28px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); border: 1px solid #cbd5e1; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: grid; place-items: center; font-size: 1.3rem;">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div>
                    <h3 id="modalSubjectTitle" style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 2px;">{{ __('تعديل تسعيرة المادة (نظام فصلي)') }}</h3>
                    <small style="color: #64748b; font-size: 0.8rem;">{{ __('تحديد أسعار الفصل الأول والفصل الثاني والفصلين معاً للضفة وغزة') }}</small>
                </div>
            </div>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.6rem; color: #94a3b8; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <form id="editPricingForm" onsubmit="submitPricing(event)">
            @csrf
            <input type="hidden" id="editSubjectId" name="subject_id">

            {{-- 1. تسعيرة الضفة الغربية والقدس 🏛️ --}}
            <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 14px; padding: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px; color: #1e40af; font-weight: 800; font-size: 0.95rem;">
                    <i class="fa-solid fa-landmark"></i>
                    <span>{{ __('تسعيرة الضفة') }}</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">{{ __('الفصل الأول (₪) *') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceTerm1Wb" name="price_term_1" required oninput="calcWbFullPreview()" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-weight: 800; font-family: monospace; color: #1e40af;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">{{ __('الفصل الثاني (₪) *') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceTerm2Wb" name="price_term_2" required oninput="calcWbFullPreview()" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-weight: 800; font-family: monospace; color: #1e40af;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #0f172a; margin-bottom: 5px;">{{ __('الفصلين معاً (₪) *') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceFullWb" name="price_full_year" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 2px solid #3b82f6; font-weight: 800; font-family: monospace; color: #0f172a; background: #eff6ff;">
                    </div>
                </div>
            </div>

            {{-- 2. تسعيرة غزة --}}
            <div style="background: #f0fdf4; border: 1.5px solid #a7f3d0; border-radius: 14px; padding: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px; color: #065f46; font-weight: 800; font-size: 0.95rem;">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ __('تسعيرة غزة') }}</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #065f46; margin-bottom: 5px;">{{ __('الفصل الأول (₪)') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceTerm1Gaza" name="price_term_1_gaza" oninput="calcGazaFullPreview()" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #a7f3d0; font-weight: 800; font-family: monospace; color: #047857;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #065f46; margin-bottom: 5px;">{{ __('الفصل الثاني (₪)') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceTerm2Gaza" name="price_term_2_gaza" oninput="calcGazaFullPreview()" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #a7f3d0; font-weight: 800; font-family: monospace; color: #047857;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #064e3b; margin-bottom: 5px;">{{ __('الفصلين معاً (₪)') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceFullGaza" name="price_full_year_gaza" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 2px solid #10b981; font-weight: 800; font-family: monospace; color: #064e3b; background: #ecfdf5;">
                    </div>
                </div>
            </div>

            {{-- 3. خيار المادة المجانية --}}
            <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 12px; padding: 12px 16px; margin-bottom: 18px; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="modalIsFree" name="is_free" value="1" onchange="onIsFreeToggle()" style="width: 18px; height: 18px; accent-color: #d97706; cursor: pointer;">
                <label for="modalIsFree" style="font-size: 0.88rem; font-weight: 800; color: #92400e; cursor: pointer; margin: 0;">
                    {{ __('تعيين المادة كمجانية بالكامل لكافة الطلاب في الضفة وغزة (0 ₪)') }}
                </label>
            </div>

            {{-- 4. وصف الباقة --}}
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 0.84rem; font-weight: 700; color: #475569; margin-bottom: 6px;">{{ __('وصف باقة المادة ومميزاتها للطلاب') }}</label>
                <textarea id="modalDescription" name="description" rows="2" placeholder="{{ __('مثال: تشمل شرح كامل المنهاج الوزاري للفصلين، حلول أسئلة السنوات السابقة، وبطاقات المذاكرة السريعة') }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 0.88rem; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeEditModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer;">{{ __('إلغاء') }}</button>
                <button type="submit" id="btnSavePrice" style="background: #1e3a8a; color: white; border: none; padding: 10px 26px; border-radius: 10px; font-weight: 800; font-size: 0.92rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25);">
                    <i class="fa-solid fa-check"></i> {{ __('حفظ وتطبيق التسعيرة الفصليّة') }}
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
    
    const isFree = (sub.is_free == 1);
    document.getElementById('modalIsFree').checked = isFree;

    const t1Wb = parseFloat(sub.price_term_1) || parseFloat(sub.price_ils / 2) || 250;
    const t2Wb = parseFloat(sub.price_term_2) || parseFloat(sub.price_ils / 2) || 250;
    const fullWb = parseFloat(sub.price_full_year) || parseFloat(sub.price_ils) || (t1Wb + t2Wb);

    const t1Gaza = parseFloat(sub.price_term_1_gaza) || Math.round(t1Wb * 0.6);
    const t2Gaza = parseFloat(sub.price_term_2_gaza) || Math.round(t2Wb * 0.6);
    const fullGaza = parseFloat(sub.price_full_year_gaza) || Math.round(fullWb * 0.6);

    document.getElementById('modalPriceTerm1Wb').value = t1Wb;
    document.getElementById('modalPriceTerm2Wb').value = t2Wb;
    document.getElementById('modalPriceFullWb').value = fullWb;

    document.getElementById('modalPriceTerm1Gaza').value = t1Gaza;
    document.getElementById('modalPriceTerm2Gaza').value = t2Gaza;
    document.getElementById('modalPriceFullGaza').value = fullGaza;

    document.getElementById('modalDescription').value = sub.description || '';

    onIsFreeToggle();

    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}

function calcWbFullPreview() {
    const t1 = parseFloat(document.getElementById('modalPriceTerm1Wb').value) || 0;
    const t2 = parseFloat(document.getElementById('modalPriceTerm2Wb').value) || 0;
    const fullInput = document.getElementById('modalPriceFullWb');
    if (!fullInput.dataset.manual) {
        fullInput.value = (t1 + t2);
    }
}

function calcGazaFullPreview() {
    const t1 = parseFloat(document.getElementById('modalPriceTerm1Gaza').value) || 0;
    const t2 = parseFloat(document.getElementById('modalPriceTerm2Gaza').value) || 0;
    const fullInput = document.getElementById('modalPriceFullGaza');
    if (!fullInput.dataset.manual) {
        fullInput.value = (t1 + t2);
    }
}

function onIsFreeToggle() {
    const isFree = document.getElementById('modalIsFree').checked;
    const fields = [
        'modalPriceTerm1Wb', 'modalPriceTerm2Wb', 'modalPriceFullWb',
        'modalPriceTerm1Gaza', 'modalPriceTerm2Gaza', 'modalPriceFullGaza'
    ];
    fields.forEach(f => {
        document.getElementById(f).disabled = isFree;
        if (isFree) document.getElementById(f).value = 0;
    });
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
    const isFree = document.getElementById('modalIsFree').checked ? 1 : 0;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

    const payload = {
        price_term_1: document.getElementById('modalPriceTerm1Wb').value,
        price_term_2: document.getElementById('modalPriceTerm2Wb').value,
        price_full_year: document.getElementById('modalPriceFullWb').value,
        price_term_1_gaza: document.getElementById('modalPriceTerm1Gaza').value,
        price_term_2_gaza: document.getElementById('modalPriceTerm2Gaza').value,
        price_full_year_gaza: document.getElementById('modalPriceFullGaza').value,
        is_free: isFree,
        description: document.getElementById('modalDescription').value
    };

    axios.post(`/admin/subjects/pricing/${id}/update`, payload)
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

