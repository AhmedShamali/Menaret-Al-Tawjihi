@extends('layouts.app')

@section('title', __('إدارة أسعار المواد والرسوم الفصلية') . ' - ' . config('app.name', 'Step by Step'))

@section('content')
<style>
/* ========================================================
   لوحة إدارة أسعار المواد والرسوم الفصلية - تصميم كلاسيكي رايق
   طابع أكاديمي فلسطيني رصين، ألوان هادئة، خطوط واضحة، وتنظيم متقن
   ======================================================== */
:root {
    --cls-navy: #1e3a8a;
    --cls-navy-dark: #0f172a;
    --cls-navy-subtle: #f0f4f8;
    --cls-border: #e2e8f0;
    --cls-border-subtle: #edf2f7;
    --cls-text-main: #1e293b;
    --cls-text-muted: #64748b;
    --cls-surface: #ffffff;
    --cls-bg-page: #f8fafc;
    --cls-emerald: #059669;
    --cls-emerald-subtle: #ecfdf5;
    --cls-emerald-border: #a7f3d0;
    --cls-amber: #b45309;
    --cls-amber-subtle: #fffbeb;
    --cls-amber-border: #fde68a;
}

.classic-pricing-wrapper {
    max-width: 1440px;
    margin: 0 auto;
    padding: 16px 20px 60px;
    color: var(--cls-text-main);
    font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
}

/* 1. الترويسة الأكاديمية الكلاسيكية */
.classic-page-header {
    background: var(--cls-surface);
    border: 1px solid var(--cls-border);
    border-radius: 12px;
    padding: 22px 28px;
    margin-bottom: 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.classic-header-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.classic-header-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: #f1f5f9;
    color: var(--cls-navy);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.classic-header-text h1 {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--cls-navy-dark);
    margin: 0 0 4px;
    letter-spacing: -0.2px;
}

.classic-header-text p {
    color: var(--cls-text-muted);
    font-size: 0.88rem;
    margin: 0;
}

.classic-header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-classic-secondary {
    background: #ffffff;
    color: var(--cls-text-main);
    border: 1px solid var(--cls-border);
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-classic-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--cls-navy-dark);
}

.btn-classic-primary {
    background: var(--cls-navy);
    color: #ffffff;
    border: 1px solid var(--cls-navy);
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-classic-primary:hover {
    background: #172554;
    border-color: #172554;
    color: #ffffff;
}

/* 2. بطاقات المؤشرات الكلاسيكية الهادئة */
.classic-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 22px;
}

@media (max-width: 1024px) {
    .classic-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .classic-stats-grid {
        grid-template-columns: 1fr;
    }
}

.classic-stat-card {
    background: var(--cls-surface);
    border: 1px solid var(--cls-border);
    border-radius: 10px;
    padding: 18px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: border-color 0.2s ease;
}

.classic-stat-card:hover {
    border-color: #cbd5e1;
}

.classic-stat-main {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.classic-stat-title {
    font-size: 0.84rem;
    font-weight: 700;
    color: var(--cls-text-muted);
}

.classic-stat-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--cls-navy-dark);
    line-height: 1.1;
    display: inline-flex;
    align-items: baseline;
    gap: 6px;
}

.classic-stat-currency {
    font-size: 0.92rem;
    font-weight: 700;
    color: var(--cls-text-muted);
}

.classic-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    color: var(--cls-navy);
}

/* 3. شريط الفلاتر الكلاسيكي الأنيق */
.classic-filter-strip {
    background: var(--cls-surface);
    border: 1px solid var(--cls-border);
    border-radius: 10px;
    padding: 12px 20px;
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}

.classic-filter-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 0.88rem;
    color: var(--cls-text-main);
}

.classic-filter-label i {
    color: var(--cls-navy);
}

.classic-tabs-wrap {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.classic-tab-btn {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.86rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.15s ease;
    border: 1px solid var(--cls-border);
    background: #f8fafc;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.classic-tab-btn:hover {
    background: #edf2f7;
    color: var(--cls-navy-dark);
}

.classic-tab-btn.is-active {
    background: var(--cls-navy);
    color: #ffffff;
    border-color: var(--cls-navy);
}

/* 4. كرت وجدول البيانات الكلاسيكي */
.classic-table-card {
    background: var(--cls-surface);
    border: 1px solid var(--cls-border);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

.classic-table-container {
    overflow-x: auto;
    width: 100%;
}

.classic-data-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 980px;
    text-align: right;
}

.classic-data-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 0.84rem;
    font-weight: 800;
    padding: 14px 18px;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
    letter-spacing: -0.1px;
}

.classic-data-table thead th.text-center {
    text-align: center;
}

.classic-data-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}

.classic-data-table tbody tr:hover {
    background: #fbfcfe;
}

.classic-data-table tbody tr:last-child {
    border-bottom: none;
}

.classic-data-table tbody td {
    padding: 16px 18px;
    vertical-align: middle;
    font-size: 0.9rem;
    color: var(--cls-text-main);
}

/* خلايا الجدول الداخلية */
.cell-subject-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cell-subject-avatar {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: var(--cls-navy);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
}

.cell-subject-info strong {
    display: block;
    color: var(--cls-navy-dark);
    font-size: 0.95rem;
    font-weight: 800;
    margin-bottom: 3px;
}

.cell-subject-info small {
    color: var(--cls-text-muted);
    font-size: 0.78rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* شارات الفروع الأكاديمية الكلاسيكية */
.branch-pill-classic {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    white-space: nowrap;
}

.branch-pill-classic.sci {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1e40af;
}

.branch-pill-classic.lit {
    background: #fdf2f8;
    border-color: #fbcfe8;
    color: #9d174d;
}

.branch-pill-classic.bus {
    background: #fefce8;
    border-color: #fef08a;
    color: #854d0e;
}

/* عرض الأسعار المنظم الهادئ */
.price-strip-classic {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.price-terms-line {
    font-size: 0.82rem;
    color: var(--cls-text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
}

.price-terms-line strong {
    color: var(--cls-text-main);
    font-weight: 700;
    font-family: monospace;
}

.price-full-box {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 800;
    width: fit-content;
}

.price-full-box.wb {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #dbeafe;
}

.price-full-box.gaza {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #d1fae5;
}

/* شارات الحالة */
.status-tag-classic {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    white-space: nowrap;
}

.status-tag-classic.normal {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
}

.status-tag-classic.free {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}

/* زر التعديل الكلاسيكي */
.btn-classic-edit {
    background: #ffffff;
    color: var(--cls-navy);
    border: 1px solid #cbd5e1;
    padding: 6px 14px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.82rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.btn-classic-edit:hover {
    background: var(--cls-navy);
    color: #ffffff;
    border-color: var(--cls-navy);
}

/* تنبيه النجاح الكلاسيكي */
.classic-alert-success {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
    padding: 12px 18px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 700;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* 5. نوافذ الحوار الكلاسيكية (Modal) */
.classic-modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.classic-modal-card {
    background: #ffffff;
    border-radius: 12px;
    max-width: 620px;
    width: 100%;
    padding: 24px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    max-height: 92vh;
    overflow-y: auto;
}

.classic-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid #f1f5f9;
}

.classic-modal-title {
    font-size: 1.18rem;
    font-weight: 800;
    color: var(--cls-navy-dark);
    margin: 0 0 3px;
}

.classic-modal-subtitle {
    color: var(--cls-text-muted);
    font-size: 0.8rem;
    margin: 0;
}

.classic-modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
    padding: 4px;
}

.classic-modal-close:hover {
    color: #0f172a;
}

.pricing-section-box {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    background: #ffffff;
}

.pricing-section-box.wb {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.pricing-section-box.gaza {
    background: #f0fdf4;
    border-color: #bbf7d0;
}

.pricing-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 800;
    margin-bottom: 12px;
}

.pricing-section-title.wb {
    color: #1e40af;
}

.pricing-section-title.gaza {
    color: #065f46;
}

.grid-3-inputs {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.form-group-classic label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
}

.form-input-classic {
    width: 100%;
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    font-size: 0.95rem;
    font-weight: 700;
    font-family: monospace;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    box-sizing: border-box;
}

.form-input-classic:focus {
    border-color: var(--cls-navy);
}

.classic-checkbox-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.classic-checkbox-box label {
    font-size: 0.84rem;
    font-weight: 700;
    color: #92400e;
    cursor: pointer;
    margin: 0;
}

.classic-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}
</style>

<div class="classic-pricing-wrapper">

    {{-- 1. رأس الصفحة الأكاديمي الكلاسيكي --}}
    <div class="classic-page-header">
        <div class="classic-header-info">
            <div class="classic-header-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="classic-header-text">
                <h1>{{ __('إدارة أسعار المواد وباقات الاشتراك') }}</h1>
                <p>{{ __('تحديد أسعار المواد والرسوم الفصلية للضفة الغربية وقطاع غزة، وإدارة العروض الترويجية لطلبة التوجيهي.') }}</p>
            </div>
        </div>

        <div class="classic-header-actions">
            <a href="{{ route('admin.subscriptions.monthly') }}" class="btn-classic-secondary">
                <i class="fa-solid fa-table-cells"></i>
                <span>{{ __('مصفوفة الاشتراكات') }}</span>
            </a>
            <button type="button" onclick="openSeasonalModal()" class="btn-classic-primary">
                <i class="fa-solid fa-percent"></i>
                <span>{{ __('خصم موسمي شامل') }}</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="classic-alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- 2. بطاقات المؤشرات الأكاديمية الكلاسيكية الهادئة --}}
    <div class="classic-stats-grid">
        {{-- كرت 1: إجمالي المواد --}}
        <div class="classic-stat-card">
            <div class="classic-stat-main">
                <span class="classic-stat-title">{{ __('إجمالي المواد الدراسية') }}</span>
                <span class="classic-stat-value">
                    <span>{{ $pricingStats['total_subjects'] }}</span>
                    <span class="classic-stat-currency">{{ __('مادة') }}</span>
                </span>
            </div>
            <div class="classic-stat-icon">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
        </div>

        {{-- كرت 2: متوسط رسوم الضفة --}}
        <div class="classic-stat-card">
            <div class="classic-stat-main">
                <span class="classic-stat-title">{{ __('متوسط رسوم الفصل (الضفة)') }}</span>
                <span class="classic-stat-value">
                    <span>{{ $pricingStats['avg_term_wb'] }}</span>
                    <span class="classic-stat-currency">₪</span>
                </span>
            </div>
            <div class="classic-stat-icon">
                <i class="fa-solid fa-landmark"></i>
            </div>
        </div>

        {{-- كرت 3: متوسط رسوم غزة --}}
        <div class="classic-stat-card">
            <div class="classic-stat-main">
                <span class="classic-stat-title">{{ __('متوسط رسوم الفصل (غزة)') }}</span>
                <span class="classic-stat-value">
                    <span>{{ $pricingStats['avg_term_gaza'] }}</span>
                    <span class="classic-stat-currency">₪</span>
                </span>
            </div>
            <div class="classic-stat-icon">
                <i class="fa-solid fa-location-dot"></i>
            </div>
        </div>

        {{-- كرت 4: مواد مجانية بالكامل --}}
        <div class="classic-stat-card">
            <div class="classic-stat-main">
                <span class="classic-stat-title">{{ __('مواد مجانية بالكامل') }}</span>
                <span class="classic-stat-value">
                    <span>{{ $pricingStats['free_subjects'] }}</span>
                    <span class="classic-stat-currency">{{ __('مادة') }}</span>
                </span>
            </div>
            <div class="classic-stat-icon">
                <i class="fa-solid fa-gift"></i>
            </div>
        </div>
    </div>

    {{-- 3. شريط تصفية الفروع الكلاسيكي الرايق --}}
    <div class="classic-filter-strip">
        <div class="classic-filter-label">
            <i class="fa-solid fa-filter"></i>
            <span>{{ __('الفرع الأكاديمي:') }}</span>
        </div>
        <div class="classic-tabs-wrap">
            <a href="{{ route('admin.subjects.pricing') }}" class="classic-tab-btn {{ empty($stageId) ? 'is-active' : '' }}">
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
                <a href="{{ route('admin.subjects.pricing', ['stage_id' => $stg->id]) }}" class="classic-tab-btn {{ $stageId == $stg->id ? 'is-active' : '' }}">
                    <i class="fa-solid {{ $stgIcon }}"></i>
                    <span>{{ $stgTitle }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- 4. جدول أسعار المواد الأكاديمي الكلاسيكي --}}
    <div class="classic-table-card">
        <div class="classic-table-container">
            <table class="classic-data-table">
                <thead>
                    <tr>
                        <th style="width: 280px;">{{ __('المادة الدراسية') }}</th>
                        <th style="width: 140px;">{{ __('الفرع') }}</th>
                        <th style="width: 250px;">
                            <span><i class="fa-solid fa-landmark text-navy"></i> {{ __('تسعيرة الضفة والقدس') }}</span>
                        </th>
                        <th style="width: 250px;">
                            <span><i class="fa-solid fa-location-dot text-emerald"></i> {{ __('تسعيرة قطاع غزة') }}</span>
                        </th>
                        <th style="width: 130px;">{{ __('الحالة') }}</th>
                        <th style="width: 120px;" class="text-center">{{ __('الإجراء') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $sub)
                        @php
                            $rawStage = optional($sub->stage)->label_ar ?? optional($sub->stage)->name ?? optional($sub->stage)->name_ar ?? 'توجيهي عام';
                            if (str_contains($rawStage, 'علمي')) {
                                $stageShort = __('العلمي');
                                $stageClass = 'sci';
                                $stageIcon  = 'fa-atom';
                            } elseif (str_contains($rawStage, 'أدبي')) {
                                $stageShort = __('الأدبي');
                                $stageClass = 'lit';
                                $stageIcon  = 'fa-book-open';
                            } elseif (str_contains($rawStage, 'ريادة') || str_contains($rawStage, 'أعمال')) {
                                $stageShort = __('ريادة وأعمال');
                                $stageClass = 'bus';
                                $stageIcon  = 'fa-briefcase';
                            } else {
                                $stageShort = $rawStage;
                                $stageClass = 'gen';
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
                            {{-- المادة الدراسية --}}
                            <td>
                                <div class="cell-subject-wrap">
                                    <div class="cell-subject-avatar">
                                        @if($isFaIcon)
                                            <i class="fa-solid {{ $subIcon }}"></i>
                                        @else
                                            <span>{{ $subIcon }}</span>
                                        @endif
                                    </div>
                                    <div class="cell-subject-info">
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
                                <span class="branch-pill-classic {{ $stageClass }}">
                                    <i class="fa-solid {{ $stageIcon }}"></i>
                                    <span>{{ $stageShort }}</span>
                                </span>
                            </td>

                            {{-- تسعيرة الضفة --}}
                            <td>
                                @if($sub->is_free)
                                    <span style="color: #059669; font-weight: 700;"><i class="fa-solid fa-gift"></i> {{ __('مجانية 100%') }}</span>
                                @else
                                    <div class="price-strip-classic">
                                        <div class="price-terms-line">
                                            <span>{{ __('فصل 1:') }} <strong>{{ number_format($p1Wb, 0) }} ₪</strong></span>
                                            <span>•</span>
                                            <span>{{ __('فصل 2:') }} <strong>{{ number_format($p2Wb, 0) }} ₪</strong></span>
                                        </div>
                                        <div class="price-full-box wb">
                                            <span>{{ __('الفصلين معاً:') }}</span>
                                            <span style="font-family: monospace;">{{ number_format($pFullWb, 0) }} ₪</span>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            {{-- تسعيرة غزة --}}
                            <td>
                                @if($sub->is_free)
                                    <span style="color: #059669; font-weight: 700;"><i class="fa-solid fa-gift"></i> {{ __('مجانية 100%') }}</span>
                                @else
                                    <div class="price-strip-classic">
                                        <div class="price-terms-line">
                                            <span>{{ __('فصل 1:') }} <strong>{{ number_format($p1Gaza, 0) }} ₪</strong></span>
                                            <span>•</span>
                                            <span>{{ __('فصل 2:') }} <strong>{{ number_format($p2Gaza, 0) }} ₪</strong></span>
                                        </div>
                                        <div class="price-full-box gaza">
                                            <span>{{ __('الفصلين معاً:') }}</span>
                                            <span style="font-family: monospace;">{{ number_format($pFullGaza, 0) }} ₪</span>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            {{-- الحالة --}}
                            <td>
                                @if($sub->is_free)
                                    <span class="status-tag-classic free">
                                        <i class="fa-solid fa-gift"></i>
                                        <span>{{ __('مجانية بالكامل') }}</span>
                                    </span>
                                @else
                                    <span class="status-tag-classic normal">
                                        <i class="fa-solid fa-check"></i>
                                        <span>{{ __('نظام فصلي') }}</span>
                                    </span>
                                @endif
                            </td>

                            {{-- الإجراء --}}
                            <td class="text-center">
                                <button type="button" onclick="editPricing({{ json_encode($sub) }})" class="btn-classic-edit" title="{{ __('تعديل أسعار المادة') }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>{{ __('تعديل التسعيرة') }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px 20px; text-align: center; color: #94a3b8;">
                                <i class="fa-solid fa-tags" style="font-size: 2rem; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                                <span style="font-size: 0.95rem; color: #64748b; font-weight: 700;">{{ __('لا توجد مواد مسجلة مطابقة للفرع المحدد.') }}</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- نافذة تعديل تسعيرة مادة (Classic Modal) --}}
<div id="editModal" class="classic-modal-backdrop">
    <div class="classic-modal-card">
        <div class="classic-modal-header">
            <div>
                <h3 id="modalSubjectTitle" class="classic-modal-title">{{ __('تعديل تسعيرة المادة (نظام فصلي)') }}</h3>
                <p class="classic-modal-subtitle">{{ __('تحديد أسعار الفصل الأول، الفصل الثاني، والفصلين معاً لمناطق الضفة وغزة.') }}</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="classic-modal-close">&times;</button>
        </div>

        <form id="editPricingForm" onsubmit="submitPricing(event)">
            @csrf
            <input type="hidden" id="editSubjectId" name="subject_id">

            {{-- 1. تسعيرة الضفة الغربية والقدس --}}
            <div class="pricing-section-box wb">
                <div class="pricing-section-title wb">
                    <i class="fa-solid fa-landmark"></i>
                    <span>{{ __('تسعيرة الضفة الغربية والقدس (شيكل ₪)') }}</span>
                </div>
                <div class="grid-3-inputs">
                    <div class="form-group-classic">
                        <label>{{ __('الفصل الأول *') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceTerm1Wb" name="price_term_1" required oninput="calcWbFullPreview()" class="form-input-classic">
                    </div>
                    <div class="form-group-classic">
                        <label>{{ __('الفصل الثاني *') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceTerm2Wb" name="price_term_2" required oninput="calcWbFullPreview()" class="form-input-classic">
                    </div>
                    <div class="form-group-classic">
                        <label>{{ __('الفصلين معاً *') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceFullWb" name="price_full_year" required class="form-input-classic" style="border-color: #3b82f6; background: #eff6ff;">
                    </div>
                </div>
            </div>

            {{-- 2. تسعيرة قطاع غزة --}}
            <div class="pricing-section-box gaza">
                <div class="pricing-section-title gaza">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ __('تسعيرة قطاع غزة (شيكل ₪)') }}</span>
                </div>
                <div class="grid-3-inputs">
                    <div class="form-group-classic">
                        <label>{{ __('الفصل الأول') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceTerm1Gaza" name="price_term_1_gaza" oninput="calcGazaFullPreview()" class="form-input-classic">
                    </div>
                    <div class="form-group-classic">
                        <label>{{ __('الفصل الثاني') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceTerm2Gaza" name="price_term_2_gaza" oninput="calcGazaFullPreview()" class="form-input-classic">
                    </div>
                    <div class="form-group-classic">
                        <label>{{ __('الفصلين معاً') }}</label>
                        <input type="number" step="1" min="0" id="modalPriceFullGaza" name="price_full_year_gaza" class="form-input-classic" style="border-color: #10b981; background: #ecfdf5;">
                    </div>
                </div>
            </div>

            {{-- 3. خيار المادة المجانية --}}
            <div class="classic-checkbox-box">
                <input type="checkbox" id="modalIsFree" name="is_free" value="1" onchange="onIsFreeToggle()" style="width: 17px; height: 17px; cursor: pointer;">
                <label for="modalIsFree">
                    {{ __('تعيين المادة كمجانية بالكامل لكافة الطلبة (0 ₪)') }}
                </label>
            </div>

            {{-- 4. وصف الباقة --}}
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">{{ __('وصف باقة المادة وملاحظات التسعير (اختياري)') }}</label>
                <textarea id="modalDescription" name="description" rows="2" placeholder="{{ __('مثال: تشمل شرح كامل المنهاج الوزاري للفصلين، حلول أسئلة السنوات السابقة، والمتابعة الأكاديمية') }}" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; font-size: 0.86rem; resize: vertical; box-sizing: border-box;"></textarea>
            </div>

            <div class="classic-modal-actions">
                <button type="button" onclick="closeEditModal()" class="btn-classic-secondary">{{ __('إلغاء') }}</button>
                <button type="submit" id="btnSavePrice" class="btn-classic-primary">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('حفظ وتطبيق التسعيرة') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- نافذة الخصم الموسمي الشامل (Seasonal Modal) --}}
<div id="seasonalModal" class="classic-modal-backdrop">
    <div class="classic-modal-card" style="max-width: 480px;">
        <div class="classic-modal-header">
            <div>
                <h3 class="classic-modal-title">{{ __('تطبيق خصم موسمي شامل') }}</h3>
                <p class="classic-modal-subtitle">{{ __('تطبيق نسبة خصم ترويجية على أسعار المواد الدراسية.') }}</p>
            </div>
            <button type="button" onclick="closeSeasonalModal()" class="classic-modal-close">&times;</button>
        </div>

        <form action="{{ route('admin.subjects.pricing.seasonal') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 5px;">{{ __('نسبة الخصم المئوية (%) *') }}</label>
                <input type="number" name="discount_percentage" min="5" max="90" value="20" required class="form-input-classic" style="font-size: 1.1rem; color: #059669;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 5px;">{{ __('تطبيق على فرع محدد (اختياري)') }}</label>
                <select name="stage_id" class="form-input-classic" style="font-family: inherit;">
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

            <div class="classic-modal-actions">
                <button type="button" onclick="closeSeasonalModal()" class="btn-classic-secondary">{{ __('إلغاء') }}</button>
                <button type="submit" class="btn-classic-primary">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('تطبيق الخصم فوراً') }}</span>
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
