@extends('layouts.app')

@section('title', __('مصفوفة وسجل الاشتراكات والذمم المالية للطلاب') . ' - ' . __('منارة التوجيهي'))

@section('content')
<div class="subs-matrix-wrapper">
    {{-- 1. الترويسة الأكاديمية الملكية الكلاسيكية (مطابقة للهوية الرسمية الفلسطينية وشعار المنصة) --}}
    <div class="royal-academic-header-card">
        <div class="royal-header-frame">
            <div class="header-col-ar">
                <h3 class="state-title-ar">دولة فلسطين 🇵🇸</h3>
                <p class="inst-title-ar">{{ __('منظومة منارة التوجيهي للتعليم الأكاديمي') }}</p>
                <span class="dept-badge">{{ __('الإدارة العامة والشؤون المالية والمتابعة') }}</span>
            </div>

            <div class="header-emblem-center">
                <div class="emblem-wrapper">
                    @if(\App\Models\Setting::get('director_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('director_logo')) }}" alt="شعار الإدارة" class="header-logo-img">
                    @elseif(\App\Models\Setting::get('site_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="شعار المنصة" class="header-logo-img">
                    @else
                        <div class="emblem-circle-icon">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    @endif
                </div>
                <span class="emblem-sub-tag">{{ __('سجل الاشتراكات والذمم المعتمد') }}</span>
                <span class="academic-year-tag font-mono">{{ $year }} {{ app()->getLocale() === 'ar' ? 'م' : 'AD' }}</span>
            </div>

            <div class="header-col-en">
                <h3 class="state-title-en">STATE OF PALESTINE</h3>
                <p class="inst-title-en">Menaret Al-Tawjihi Educational Platform</p>
                <span class="dept-badge-en">Financial Administration & Students Registry</span>
            </div>
        </div>

        {{-- شريط أدوات التحكم العلوي --}}
        <div class="royal-toolbar-strip">
            <div class="toolbar-left-info">
                <i class="fa-solid fa-coins text-amber"></i>
                <span>{{ __('الرسوم الشهرية العامة للمنصة:') }}</span>
                <strong class="font-mono text-navy font-bold" id="globalFeeDisplay">{{ number_format(\App\Models\Setting::get('default_monthly_fee', 150), 0) }} ₪</strong>
                <button type="button" class="btn-royal-small" onclick="openGlobalFeeModal()" title="{{ __('تعديل الرسوم الافتراضية للمنصة') }}">
                    <i class="fa-solid fa-sliders"></i> {{ __('تعديل الرسوم العامة') }}
                </button>
            </div>

            <div class="toolbar-right-tools">
                <form method="GET" action="{{ route('admin.subscriptions.monthly') }}" class="year-select-form">
                    <label class="year-label"><i class="fa-regular fa-calendar"></i> {{ __('العام الدراسي:') }}</label>
                    <select name="year" class="year-dropdown" onchange="this.form.submit()">
                        <option value="2026-2027" {{ $year === '2026-2027' ? 'selected' : '' }}>2026 / 2027 {{ app()->getLocale() === 'ar' ? 'م' : 'AD' }}</option>
                        <option value="2025-2026" {{ $year === '2025-2026' ? 'selected' : '' }}>2025 / 2026 {{ app()->getLocale() === 'ar' ? 'م' : 'AD' }}</option>
                    </select>
                </form>

                <button type="button" class="btn-royal-print-all" onclick="printGeneralMatrixDoc()" title="{{ __('طباعة كشف مالي شامل') }}">
                    <i class="fa-solid fa-print"></i> {{ __('طباعة الكشف العام') }}
                </button>
            </div>
        </div>
    </div>

    {{-- 2. العدادات والمؤشرات المالية الكبرى للمدير (اللي لازم يصلني، اللي وصلني، المتبقي بذمة الطلاب) --}}
    <div class="financial-kpi-grid">
        {{-- عداد 1: اللي لازم يصلني --}}
        <div class="kpi-card-royal card-expected" style="--kpi-theme: #1e3a8a;">
            <div class="kpi-header">
                <span class="kpi-tag-pill bg-navy-subtle">{{ __('المستحق الإجمالي المطلوب') }}</span>
                <div class="kpi-icon-wrap text-navy">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
            <div class="kpi-body">
                <span class="kpi-title">{{ __('اللي لازم يصلني') }}</span>
                <div class="kpi-amount font-mono text-navy" id="stat_total_expected">{{ number_format($stats['total_expected'], 2) }} ₪</div>
                <p class="kpi-subtext">{{ __('إجمالي الرسوم المقررة لكافة الطلاب (12 شهراً)') }}</p>
            </div>
            <div class="kpi-footer">
                <span><i class="fa-solid fa-users"></i> {{ __('إجمالي الطلاب:') }} {{ $students->total() }} {{ __('طالب') }}</span>
            </div>
        </div>

        {{-- عداد 2: اللي وصلني --}}
        <div class="kpi-card-royal card-collected" style="--kpi-theme: #059669;">
            <div class="kpi-header">
                <span class="kpi-tag-pill bg-emerald-subtle">{{ __('المحصل الفعلي المعتمد') }}</span>
                <div class="kpi-icon-wrap text-emerald">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="kpi-body">
                <span class="kpi-title text-emerald">{{ __('اللي وصلني') }}</span>
                <div class="kpi-amount font-mono text-emerald" id="stat_total_collected">{{ number_format($stats['total_collected'], 2) }} ₪</div>
                <p class="kpi-subtext">{{ __('المبالغ المقبوضة فعلياً في خزينة المنصة') }}</p>
            </div>
            <div class="kpi-footer">
                <span><i class="fa-solid fa-receipt"></i> <span id="stat_paid_count">{{ $stats['paid_count'] }}</span> {{ __('شهر خالص بالكامل') }}</span>
            </div>
        </div>

        {{-- عداد 3: المتبقي بذمة الطلاب --}}
        <div class="kpi-card-royal card-remaining" style="--kpi-theme: #dc2626;">
            <div class="kpi-header">
                <span class="kpi-tag-pill bg-rose-subtle">{{ __('عجز التحصيل والمتأخرات') }}</span>
                <div class="kpi-icon-wrap text-rose">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <div class="kpi-body">
                <span class="kpi-title text-rose">{{ __('المتبقي بذمة الطلاب') }}</span>
                <div class="kpi-amount font-mono text-rose" id="stat_total_remaining">{{ number_format($stats['total_remaining'], 2) }} ₪</div>
                <p class="kpi-subtext">{{ __('أقساط غير مسددة + متبقيات الدفعات الجزئية') }}</p>
            </div>
            <div class="kpi-footer">
                <span><i class="fa-solid fa-triangle-exclamation"></i> <span id="stat_partial_count">{{ $stats['partial_count'] }}</span> {{ __('دفع جزئي') }} | <span id="stat_unpaid_count">{{ $stats['unpaid_count'] }}</span> {{ __('غير مسدد') }}</span>
            </div>
        </div>

        {{-- عداد 4: نسبة التحصيل والالتزام المالي --}}
        <div class="kpi-card-royal card-rate" style="--kpi-theme: #b45309;">
            <div class="kpi-header">
                <span class="kpi-tag-pill bg-amber-subtle">{{ __('مؤشر الالتزام والتحصيل') }}</span>
                <div class="kpi-icon-wrap text-amber">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
            </div>
            <div class="kpi-body">
                <span class="kpi-title">{{ __('نسبة التحصيل العام') }}</span>
                <div class="kpi-amount font-mono text-amber" id="stat_collection_rate">{{ $stats['collection_rate'] }}%</div>
                <div class="kpi-progress-track">
                    <div class="kpi-progress-fill" id="stat_progress_fill" style="width: {{ min(100, $stats['collection_rate']) }}%;"></div>
                </div>
            </div>
            <div class="kpi-footer">
                <span><i class="fa-solid fa-clock-rotate-left text-amber"></i> <span id="stat_pending_count">{{ $stats['pending_count'] }}</span> {{ __('إشعار قيد المراجعة') }}</span>
            </div>
        </div>
    </div>

    {{-- 3. الفلاتر ودليل الحالات --}}
    <div class="filter-box-card">
        <form method="GET" action="{{ route('admin.subscriptions.monthly') }}" class="filters-wrap">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="search-cell">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('ابحث باسم الطالب، رقم الهوية، أو الهاتف...') }}" class="search-input">
            </div>

            <div class="filter-cell">
                <select name="stage_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('كافة الفروع والمراحل') }}</option>
                    @foreach($stages as $st)
                        <option value="{{ $st->id }}" {{ $stageId == $st->id ? 'selected' : '' }}>{{ (app()->getLocale() === 'en' && !empty($st->name_en)) ? $st->name_en : ($st->label_ar ?? $st->name_ar) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-cell">
                <select name="month" class="filter-select">
                    <option value="">{{ __('كل الأشهر (1 - 12)') }}</option>
                    @foreach($monthsNames as $mNum => $mLabel)
                        <option value="{{ $mNum }}" {{ $monthFilter == $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-cell">
                <select name="status" class="filter-select">
                    <option value="">{{ __('كافة حالات الدفع') }}</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>{{ __('مسدد وخالص بالكامل') }} ✅</option>
                    <option value="partial" {{ $statusFilter === 'partial' ? 'selected' : '' }}>{{ __('دفع جزئي (متبقي عليه)') }} ⚠️</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>{{ __('قيد المراجعة والاعتماد') }} ⏳</option>
                    <option value="unpaid" {{ $statusFilter === 'unpaid' ? 'selected' : '' }}>{{ __('غير مسدد نهائياً') }} ❌</option>
                    <option value="waived" {{ $statusFilter === 'waived' ? 'selected' : '' }}>{{ __('إعفاء / منحة دراسية') }} 🏷️</option>
                </select>
            </div>

            <button type="submit" class="btn-filter-submit"><i class="fa-solid fa-filter"></i> {{ __('تطبيق الفلترة') }}</button>

            @if($search || $stageId || $monthFilter || $statusFilter)
                <a href="{{ route('admin.subscriptions.monthly', ['year' => $year]) }}" class="btn-reset-filter">{{ __('تصفير') }}</a>
            @endif
        </form>

        <div class="legend-strip">
            <span class="legend-title">{{ __('دليل الحالات والألوان:') }}</span>
            <span class="legend-item"><span class="badge-mini bg-paid"></span> {{ __('خالص ومسدد بالكامل') }} ✅</span>
            <span class="legend-item"><span class="badge-mini bg-partial"></span> {{ __('دفع جزئي ومتبقي عليه') }} ⚠️</span>
            <span class="legend-item"><span class="badge-mini bg-pending"></span> {{ __('قيد المراجعة') }} ⏳</span>
            <span class="legend-item"><span class="badge-mini bg-unpaid"></span> {{ __('غير مسدد') }} ❌</span>
            <span class="legend-item"><span class="badge-mini bg-waived"></span> {{ __('إعفاء / منحة') }} 🏷️</span>
        </div>
    </div>

    {{-- 4. مصفوفة وجدول اشتراكات الطلاب مع كشف الذمم والمتبقي --}}
    <div class="students-list-wrapper">
        <div class="list-header-row">
            <span class="col-head-student">{{ __('بيانات الطالب والمرحلة') }}</span>
            <span class="col-head-finance">{{ __('الموقف المالي (كم عليه / كم دفع / كم ضل قسط مستحق)') }}</span>
            <span class="col-head-timeline">{{ __('مسير الشهور الـ 12 (انقر على أي شهر لتعديله أو تسجيل دفع جزئي)') }}</span>
            <span class="col-head-actions">{{ __('سند وكشف الذمة') }}</span>
        </div>

        @forelse($students as $student)
            @php
                $subsByMonth = $student->monthlySubscriptions->keyBy('month');
                $studentDue = (float) $student->monthlySubscriptions->where('status', '!=', 'waived')->sum('amount');
                $studentPaid = (float) $student->monthlySubscriptions->sum(function($s) {
                    if ($s->status === 'waived') return 0.00;
                    if ($s->status === 'paid' && ((float)($s->paid_amount ?? 0) <= 0)) return (float)$s->amount;
                    return (float)($s->paid_amount ?? 0);
                });
                $studentRemaining = max(0.00, round($studentDue - $studentPaid, 2));
                $paidCount = $student->monthlySubscriptions->where('status', 'paid')->count();
                $partialCount = $student->monthlySubscriptions->where('status', 'partial')->count();
                $waivedCount = $student->monthlySubscriptions->where('status', 'waived')->count();
                $isFull = ($paidCount + $waivedCount) >= 12;
                $percent = round((($paidCount + $waivedCount) / 12) * 100);
                $studentDisplayName = (app()->getLocale() === 'en' && !empty($student->name_en)) ? $student->name_en : ($student->name_ar ?? $student->name);
                $stageDisplayName = (app()->getLocale() === 'en' && !empty($student->stage->name_en)) ? $student->stage->name_en : ($student->stage->label_ar ?? ($student->stage->name_ar ?? __('عام')));
            @endphp
            <div class="student-matrix-row" id="student_row_{{ $student->id }}">
                {{-- تعريف الطالب --}}
                <div class="student-profile-block">
                    <img src="{{ $student->photo_url }}" class="student-avatar" alt="{{ $studentDisplayName }}">
                    <div class="student-text">
                        <a href="{{ route('admin.students.show', $student->id) }}" class="student-name">
                            {{ $studentDisplayName }}
                        </a>
                        <div class="student-sub-line">
                            <span class="branch-pill">{{ $stageDisplayName }}</span>
                            <span class="phone-text font-mono" dir="ltr">{{ $student->phone ?? ($student->nid ?? '-') }}</span>
                            <button type="button" 
                                    class="student-fee-badge-btn" 
                                    id="fee_btn_{{ $student->id }}"
                                    data-student-id="{{ $student->id }}"
                                    data-student-name="{{ $studentDisplayName }}"
                                    data-monthly-fee="{{ (float)($student->monthly_fee ?: 150) }}"
                                    data-discount-percent="{{ (float)($student->custom_discount_percent ?: 0) }}"
                                    data-discount-fixed="{{ (float)($student->custom_discount_fixed ?: 0) }}"
                                    data-discount-notes="{{ $student->discount_notes ?? '' }}"
                                    onclick="openStudentFeeModalFromEl(this)"
                                    title="{{ __('تعديل الرسوم والخصم المعتمد للطالب') }}">
                                <i class="fa-solid fa-coins text-amber"></i>
                                <span class="font-mono font-bold" id="student_fee_label_{{ $student->id }}">{{ number_format($student->monthlyAmountDue(), 0) }} ₪/شهر</span>
                                @if($student->hasDiscount())
                                    <span class="badge-discount-tag"><i class="fa-solid fa-percent"></i></span>
                                @endif
                                <i class="fa-solid fa-pen-to-square edit-pen-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- الموقف المالي للطالب (كم عليه / كم دفع / كم ضل قسط مستحق) --}}
                <div class="student-financial-summary-block">
                    <div class="fin-pill-group">
                        <div class="fin-pill fin-due" title="{{ __('إجمالي الرسوم المطلوبة من الطالب طوال السنة (كم عليه)') }}">
                            <span class="fin-lbl">{{ __('كم عليه:') }}</span>
                            <strong class="font-mono" id="std_due_{{ $student->id }}">{{ number_format($studentDue, 0) }} ₪</strong>
                        </div>
                        <div class="fin-pill fin-paid" title="{{ __('إجمالي ما قام الطالب بسداده فعلياً (كم دفع)') }}">
                            <span class="fin-lbl">{{ __('كم دفع:') }}</span>
                            <strong class="font-mono text-emerald font-bold" id="std_paid_{{ $student->id }}">{{ number_format($studentPaid, 0) }} ₪</strong>
                        </div>
                        <div class="fin-pill fin-remaining {{ $studentRemaining > 0 ? 'has-remaining-alert' : 'is-clear' }}" title="{{ __('المبلغ المتبقي بذمة الطالب (كم ضل قسط مستحق)') }}">
                            <span class="fin-lbl">{{ __('كم ضل عليه:') }}</span>
                            <strong class="font-mono font-bold" id="std_rem_{{ $student->id }}">
                                @if($studentRemaining > 0)
                                    {{ number_format($studentRemaining, 0) }} ₪ ⚠️
                                @else
                                    0 ₪ ✅
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>

                {{-- شريط الشهور الـ 12 التفاعلي مع إبراز الدفع الجزئي والمتبقي --}}
                <div class="months-strip-grid">
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $sub = $subsByMonth[$m] ?? null;
                            $st = $sub ? $sub->status : 'unpaid';
                            $amt = $sub ? (float)$sub->amount : (float)$student->monthlyAmountDue();
                            $paidAmt = $sub ? (float)($sub->paid_amount ?? 0) : 0.00;
                            if ($st === 'paid' && $paidAmt <= 0) {
                                $paidAmt = $amt;
                            }
                            $remAmt = $sub ? (float)$sub->remaining_amount : ($st === 'waived' ? 0.00 : $amt);
                            $notes = $sub ? ($sub->notes ?? '') : '';
                            $mTitle = $monthsNames[$m] ?? (app()->getLocale() === 'en' ? "Month $m" : "شهر $m");
                            $isHighlight = ($monthFilter && (int)$monthFilter === $m);
                        @endphp
                        <div id="badge_{{ $student->id }}_{{ $m }}" 
                             class="month-micro-badge badge-{{ $st }} {{ $isHighlight ? 'month-highlight-col' : '' }}"
                             data-student-id="{{ $student->id }}"
                             data-student-name="{{ $studentDisplayName }}"
                             data-month="{{ $m }}"
                             data-month-title="{{ $mTitle }}"
                             data-status="{{ $st }}"
                             data-amount="{{ $amt }}"
                             data-paid-amount="{{ $paidAmt }}"
                             data-remaining-amount="{{ $remAmt }}"
                             data-notes="{{ $notes }}"
                             data-student-fee="{{ (float)$student->monthlyAmountDue() }}"
                             onclick="openEditMonthModalFromEl(this)"
                             title="{{ $mTitle }} | المطلوب: {{ round($amt) }} ₪ | المدفوع: {{ round($paidAmt) }} ₪ | المتبقي: {{ round($remAmt) }} ₪ - انقر لتعديل الشهر">
                            <span class="m-digit font-mono">{{ $m }}</span>
                            @if($st === 'paid')
                                <i class="fa-solid fa-check badge-icon"></i>
                            @elseif($st === 'partial')
                                <i class="fa-solid fa-circle-half-stroke badge-icon" style="color: #b45309;"></i>
                            @elseif($st === 'pending')
                                <i class="fa-solid fa-hourglass-half badge-icon"></i>
                            @elseif($st === 'waived')
                                <i class="fa-solid fa-tag badge-icon"></i>
                            @else
                                <i class="fa-solid fa-xmark badge-icon"></i>
                            @endif

                            @if($st === 'partial')
                                <span class="badge-partial-sub font-mono">{{ round($paidAmt) }}/{{ round($amt) }}</span>
                            @endif
                        </div>
                    @endfor
                </div>

                {{-- أزرار الإجراءات والكشف الرسمي --}}
                <div class="student-actions-block">
                    <button type="button" 
                            class="btn-statement-royal"
                            onclick="openStudentStatementModal({{ $student->id }})"
                            title="{{ __('عرض وطباعة سند كشف الذمة المالي الرسمي المعتمد للطالب') }}">
                        <i class="fa-solid fa-receipt"></i>
                        <span>{{ __('كشف رسمي') }}</span>
                    </button>

                    <button type="button" 
                            class="btn-toggle-drawer"
                            onclick="toggleStudentTableDrawer({{ $student->id }})"
                            title="{{ __('استعراض جدول الشهور الـ 12 والمبالغ التفصيلية') }}">
                        <i class="fa-solid fa-table-list"></i>
                    </button>
                </div>

                {{-- جدول الشهور الـ 12 التفصيلي للطالب (Drawer قابل للفتح والإغلاق) --}}
                <div class="student-drawer-table-wrap" id="drawer_table_{{ $student->id }}" style="display: none;">
                    <div class="drawer-header-bar">
                        <span class="drawer-title"><i class="fa-solid fa-list-check"></i> {{ __('كشف أقساط واشتراكات الشهور الـ 12 المفصل للطالب:') }} <strong>{{ $studentDisplayName }}</strong></span>
                        <button type="button" class="btn-close-drawer" onclick="toggleStudentTableDrawer({{ $student->id }})">&times;</button>
                    </div>
                    <div class="table-responsive-box">
                        <table class="classic-ledger-table">
                            <thead>
                                <tr>
                                    <th>{{ __('الشهر') }}</th>
                                    <th>{{ __('المطلوب - كم عليه (₪)') }}</th>
                                    <th>{{ __('المدفوع - كم دفع (₪)') }}</th>
                                    <th>{{ __('المتبقي - كم ضل قسط مستحق (₪)') }}</th>
                                    <th>{{ __('حالة الدفعة') }}</th>
                                    <th>{{ __('تاريخ السداد') }}</th>
                                    <th>{{ __('البيان والملاحظات') }}</th>
                                    <th>{{ __('إجراء') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($dm = 1; $dm <= 12; $dm++)
                                    @php
                                        $dSub = $subsByMonth[$dm] ?? null;
                                        $dSt = $dSub ? $dSub->status : 'unpaid';
                                        $dAmt = $dSub ? (float)$dSub->amount : (float)$student->monthlyAmountDue();
                                        $dPaid = $dSub ? (float)($dSub->paid_amount ?? 0) : 0.00;
                                        if ($dSt === 'paid' && $dPaid <= 0) $dPaid = $dAmt;
                                        $dRem = $dSub ? (float)$dSub->remaining_amount : ($dSt === 'waived' ? 0.00 : $dAmt);
                                        $dNotes = $dSub ? ($dSub->notes ?? '') : '';
                                        $dPaidAt = $dSub && $dSub->paid_at ? $dSub->paid_at->format('Y-m-d') : '-';
                                        $dTitle = $monthsNames[$dm] ?? (app()->getLocale() === 'en' ? "Month $dm" : "شهر $dm");
                                    @endphp
                                    <tr id="drawer_row_{{ $student->id }}_{{ $dm }}">
                                        <td>
                                            <strong class="font-mono text-navy">{{ sprintf('%02d', $dm) }}</strong> - {{ $dTitle }}
                                        </td>
                                        <td class="font-mono font-bold" id="drawer_amt_{{ $student->id }}_{{ $dm }}">{{ number_format($dAmt, 2) }} ₪</td>
                                        <td class="font-mono font-bold text-emerald" id="drawer_paid_{{ $student->id }}_{{ $dm }}">{{ number_format($dPaid, 2) }} ₪</td>
                                        <td class="font-mono font-bold {{ $dRem > 0 ? 'text-rose' : 'text-emerald' }}" id="drawer_rem_{{ $student->id }}_{{ $dm }}">
                                            {{ number_format($dRem, 2) }} ₪
                                        </td>
                                        <td id="drawer_status_{{ $student->id }}_{{ $dm }}">
                                            <span class="status-pill-small badge-{{ $dSt }}">
                                                @if($dSt === 'paid')
                                                    <i class="fa-solid fa-check"></i> {{ __('خالص ومسدد') }}
                                                @elseif($dSt === 'partial')
                                                    <i class="fa-solid fa-circle-half-stroke"></i> {{ __('دفع جزئي (متبقي)') }}
                                                @elseif($dSt === 'pending')
                                                    <i class="fa-solid fa-hourglass-half"></i> {{ __('قيد المراجعة') }}
                                                @elseif($dSt === 'waived')
                                                    <i class="fa-solid fa-tag"></i> {{ __('إعفاء / منحة') }}
                                                @else
                                                    <i class="fa-solid fa-xmark"></i> {{ __('غير مسدد') }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="font-mono text-muted" style="font-size: 0.8rem;">{{ $dPaidAt }}</td>
                                        <td class="text-notes" id="drawer_notes_{{ $student->id }}_{{ $dm }}">{{ $dNotes ?: '-' }}</td>
                                        <td>
                                            <button type="button" 
                                                    class="btn-edit-row"
                                                    data-student-id="{{ $student->id }}"
                                                    data-student-name="{{ $studentDisplayName }}"
                                                    data-month="{{ $dm }}"
                                                    data-month-title="{{ $dTitle }}"
                                                    data-status="{{ $dSt }}"
                                                    data-amount="{{ $dAmt }}"
                                                    data-paid-amount="{{ $dPaid }}"
                                                    data-remaining-amount="{{ $dRem }}"
                                                    data-notes="{{ $dNotes }}"
                                                    data-student-fee="{{ (float)$student->monthlyAmountDue() }}"
                                                    onclick="openEditMonthModalFromEl(this)">
                                                <i class="fa-solid fa-pen"></i> {{ __('تعديل') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-matrix-card">
                <i class="fa-solid fa-users-slash"></i>
                <p>{{ __('لم يتم العثور على أي طلاب مطابقين لشروط البحث والفلترة.') }}</p>
            </div>
        @endforelse

        <div style="margin-top: 24px;">
            {{ $students->links() }}
        </div>
    </div>
</div>

{{-- 5. نافذة (مودال) تعديل حالة الاشتراك الشهري والمبالغ وحاسبة المتبقي الحية --}}
<div id="editMonthModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box modal-royal-theme">
        <div class="modal-header-royal">
            <div class="modal-title-wrap">
                <div class="modal-crest">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h3 id="modalStudentNameTitle" class="modal-student-name">{{ __('تحديث اشتراك الشهر والمبالغ') }}</h3>
                    <p id="modalMonthSubtitle" class="modal-month-desc">{{ __('شهر محدد') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close-x" onclick="closeEditMonthModal()">&times;</button>
        </div>

        <form id="updateMonthForm" onsubmit="saveMonthSubscription(event)">
            @csrf
            <input type="hidden" name="student_id" id="formStudentId">
            <input type="hidden" name="academic_year" value="{{ $year }}">
            <input type="hidden" name="month" id="formMonth">
            <input type="hidden" id="formStudentFeeHidden" value="150">

            <div class="form-body-wrap">
                {{-- أزرار سريعة للحالة --}}
                <div class="form-field-group">
                    <label class="field-label">{{ __('حالة الاشتراك والسداد لهذا الشهر') }} <span class="required">*</span></label>
                    <div class="status-options-grid">
                        <label class="status-option-label opt-paid">
                            <input type="radio" name="status" value="paid" id="optStatusPaid" onchange="onStatusRadioChange('paid')">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-check"></i>
                                <strong>{{ __('مسدد وخالص') }}</strong>
                                <small>{{ __('سدد كامل المبلغ') }}</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-partial">
                            <input type="radio" name="status" value="partial" id="optStatusPartial" onchange="onStatusRadioChange('partial')">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-half-stroke"></i>
                                <strong>{{ __('دفع جزئي') }}</strong>
                                <small>{{ __('سدد جزءاً وعليه متبقي') }}</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-unpaid">
                            <input type="radio" name="status" value="unpaid" id="optStatusUnpaid" onchange="onStatusRadioChange('unpaid')">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <strong>{{ __('غير مسدد') }}</strong>
                                <small>{{ __('قسط كامل متأخر') }}</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-pending">
                            <input type="radio" name="status" value="pending" id="optStatusPending" onchange="onStatusRadioChange('pending')">
                            <div class="opt-content">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <strong>{{ __('قيد المراجعة') }}</strong>
                                <small>{{ __('أرسل إشعار تحويل') }}</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-waived">
                            <input type="radio" name="status" value="waived" id="optStatusWaived" onchange="onStatusRadioChange('waived')">
                            <div class="opt-content">
                                <i class="fa-solid fa-award"></i>
                                <strong>{{ __('إعفاء / منحة') }}</strong>
                                <small>{{ __('معفى رسمياً 100%') }}</small>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- شبكة المبالغ (المطلوب + المدفوع فعلياً) --}}
                <div class="amounts-calc-grid">
                    <div class="form-field-group">
                        <label class="field-label">{{ __('المبلغ المطلوب للشهر (₪)') }} <span class="required">*</span></label>
                        <input type="number" step="0.01" min="0" name="amount" id="formAmount" class="clean-input font-mono font-bold" required oninput="calcRemainingLive()">
                        <small class="field-hint">{{ __('المبلغ المستحق لهذا الشهر') }}</small>
                    </div>

                    <div class="form-field-group">
                        <label class="field-label">{{ __('المبلغ المدفوع فعلياً (₪)') }} <span class="required">*</span></label>
                        <input type="number" step="0.01" min="0" name="paid_amount" id="formPaidAmount" class="clean-input font-mono font-bold text-emerald" required oninput="calcRemainingLive()">
                        <small class="field-hint">{{ __('ما دفعه الطالب بالفعل (100، 150، إلخ)') }}</small>
                    </div>
                </div>

                {{-- أزرار مساعدة سريعة للمبالغ --}}
                <div class="quick-amount-presets">
                    <span class="preset-label">{{ __('أزرار سريعة:') }}</span>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('full')">{{ __('سداد كامل 100%') }}</button>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('half')">{{ __('دفع النصف 50%') }}</button>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('zero')">{{ __('لم يدفع (0 ₪)') }}</button>
                </div>

                {{-- بطاقة الحاسبة الحية للمبلغ المتبقي --}}
                <div class="live-calc-box" id="liveCalcBox">
                    <div class="calc-label-row">
                        <span class="calc-text">{{ __('المبلغ المتبقي بذمة الطالب للشهر:') }}</span>
                        <strong class="calc-value font-mono" id="formRemainingPreview">0.00 ₪</strong>
                    </div>
                    <div class="calc-status-indicator" id="formStatusNotice">
                        <i class="fa-solid fa-circle-check"></i> <span>{{ __('مسدد بالكامل وخالص') }}</span>
                    </div>
                </div>

                <div class="form-field-group">
                    <label class="field-label">{{ __('ملاحظات وبيان الدفعة (تظهر في السند)') }}</label>
                    <input type="text" name="notes" id="formNotes" class="clean-input" placeholder="{{ __('مثال: دفع 100 شيكل نقداً ومتبقي 50 لنهاية الأسبوع، رقم الإيصال 492...') }}">
                </div>
            </div>

            <div class="modal-footer-row">
                <button type="submit" class="btn-save-sub" id="btnSaveSub">
                    <i class="fa-solid fa-check"></i> {{ __('حفظ التحديث فورياً') }}
                </button>
                <button type="button" class="btn-cancel-sub" onclick="closeEditMonthModal()">{{ __('إلغاء') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- 6. نافذة (مودال) سند كشف الحساب والذمة المالي الرسمي المعتمد (طباعة كلاسيكية ملكية كالصورة تماماً) --}}
<div id="statementModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box modal-statement-sheet-wrap">
        <div class="statement-toolbar">
            <span class="statement-title-info"><i class="fa-solid fa-stamp text-amber"></i> {{ __('سند كشف حساب وذمة مالية رسمي معتمد للطباعة') }}</span>
            <div style="display: flex; gap: 8px;">
                <button type="button" class="btn-statement-print" onclick="printStatementDoc()"><i class="fa-solid fa-print"></i> {{ __('طباعة السند الرسمي') }}</button>
                <button type="button" class="btn-close-x" onclick="closeStatementModal()">&times;</button>
            </div>
        </div>

        <div class="statement-printable-sheet" id="statementPrintableArea">
            {{-- الإطارات الملكية الكلاسيكية المزدوجة --}}
            <div class="royal-outer-border"></div>
            <div class="royal-inner-border"></div>

            {{-- الترويسة الرسمية كالصورة --}}
            <div class="sheet-header">
                <div class="sheet-col-ar">
                    <h3>دولة فلسطين 🇵🇸</h3>
                    <p>{{ __('منظومة منارة التوجيهي للتعليم الأكاديمي') }}</p>
                    <small>{{ __('إشراف ومتابعة الثانوية العامة - الشؤون المالية') }}</small>
                </div>

                <div class="sheet-emblem">
                    @if(\App\Models\Setting::get('director_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('director_logo')) }}" alt="شعار الإدارة" class="sheet-logo-img">
                    @elseif(\App\Models\Setting::get('site_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="شعار المنصة" class="sheet-logo-img">
                    @else
                        <div class="sheet-icon-emblem"><i class="fa-solid fa-award"></i></div>
                    @endif
                    <span class="sheet-badge-tag">{{ __('سند كشف حساب رسمي معتمد') }}</span>
                </div>

                <div class="sheet-col-en">
                    <h3>STATE OF PALESTINE</h3>
                    <p>Menaret Al-Tawjihi Educational Platform</p>
                    <small>Official Academic & Financial Statement</small>
                </div>
            </div>

            <div class="sheet-heading">
                <h2>{{ __('سند كشف حساب الاشتراكات والذمم المالية') }}</h2>
                <div class="sheet-subhead">OFFICIAL FINANCIAL STATEMENT & SUBSCRIPTION LEDGER</div>
                <div class="sheet-meta-strip font-mono">
                    <span>{{ __('العام الدراسي:') }} <strong>{{ $year }}</strong></span> | 
                    <span>{{ __('تاريخ الاستخراج:') }} <strong>{{ date('Y/m/d') }}</strong></span>
                </div>
            </div>

            {{-- بيانات الطالب --}}
            <div class="sheet-student-card">
                <div class="std-cell">
                    <span class="sc-lbl">{{ __('اسم الطالب:') }}</span>
                    <strong class="sc-val" id="stmtStudentName">-</strong>
                </div>
                <div class="std-cell">
                    <span class="sc-lbl">{{ __('الفرع الأكاديمي:') }}</span>
                    <span class="sc-val" id="stmtStudentStage">-</span>
                </div>
                <div class="std-cell">
                    <span class="sc-lbl">{{ __('رقم الهوية / الجوال:') }}</span>
                    <span class="sc-val font-mono" id="stmtStudentIdPhone">-</span>
                </div>
                <div class="std-cell">
                    <span class="sc-lbl">{{ __('القسط المعتمد:') }}</span>
                    <strong class="sc-val font-mono" id="stmtStudentBaseFee">-</strong>
                </div>
            </div>

            {{-- ملخص الأرقام الكبرى للسند --}}
            <div class="sheet-kpi-row">
                <div class="sheet-kpi-item">
                    <span>{{ __('إجمالي المطلوب (كم عليه):') }}</span>
                    <strong class="font-mono" id="stmtTotalDue">0 ₪</strong>
                </div>
                <div class="sheet-kpi-item text-emerald">
                    <span>{{ __('إجمالي المسدد (كم دفع):') }}</span>
                    <strong class="font-mono" id="stmtTotalPaid">0 ₪</strong>
                </div>
                <div class="sheet-kpi-item text-rose">
                    <span>{{ __('المتبقي بذمته (كم ضل قسط مستحق):') }}</span>
                    <strong class="font-mono" id="stmtTotalRemaining">0 ₪</strong>
                </div>
            </div>

            {{-- جدول الشهور الـ 12 للطباعة --}}
            <table class="sheet-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('الشهر الدراسي') }}</th>
                        <th>{{ __('المطلوب - كم عليه (₪)') }}</th>
                        <th>{{ __('المدفوع - كم دفع (₪)') }}</th>
                        <th>{{ __('المتبقي - ضل عليه (₪)') }}</th>
                        <th>{{ __('حالة الدفعة') }}</th>
                        <th>{{ __('تاريخ السداد') }}</th>
                        <th>{{ __('ملاحظات وبيان الدفعة') }}</th>
                    </tr>
                </thead>
                <tbody id="stmtTableBody">
                    <!-- تُملأ ديناميكياً بواسطة JavaScript -->
                </tbody>
            </table>

            {{-- التواقيع والأختام الرسمية المعتمدة --}}
            <div class="sheet-footer-stamps">
                <div class="stamp-col">
                    <span class="stamp-title">{{ __('المشرف العام وإدارة المنصة') }}</span>
                    <div class="signature-line">م. أحمد شمالي</div>
                    <small>{{ __('منارة التوجيهي للتعليم الأكاديمي') }}</small>
                </div>

                <div class="stamp-col stamp-center">
                    <div class="official-seal-box">
                        <i class="fa-solid fa-certificate"></i>
                        <span>{{ __('ختم الشؤون المالية') }}</span>
                        <small>{{ __('منارة التوجيهي') }}</small>
                    </div>
                    <div class="doc-verification-code font-mono">
                        TAWJIHI-FIN-{{ date('Y') }}-CONFIRMED
                    </div>
                    <small style="color: #059669; font-weight: 700;"><i class="fa-solid fa-shield-check"></i> {{ __('وثيقة مالية رسمية صادرة ومعتمدة') }}</small>
                </div>

                <div class="stamp-col">
                    <span class="stamp-title">{{ __('معتمد الحسابات والتحصيل') }}</span>
                    <div class="signature-line">قسم المحاسبة والمالية</div>
                    <small>{{ __('تم التدقيق والمطابقة') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 7. مودال تعديل رسوم وخطة الطالب المالية والخصم المخصص --}}
<div id="editStudentFeeModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box">
        <div class="modal-header-row">
            <div>
                <h3 id="feeStudentNameTitle" style="margin: 0 0 4px; font-size: 1.2rem; color: #0f172a;">{{ __('تعديل الرسوم والخصم المعتمد للطالب') }}</h3>
                <p style="margin: 0; font-size: 0.85rem; color: #64748b;">{{ __('تحديد القسط الشهري، الخصم الخاص، والمنح المعتمدة') }}</p>
            </div>
            <button type="button" class="btn-close-x" onclick="closeStudentFeeModal()">&times;</button>
        </div>

        <form id="updateStudentFeeForm" onsubmit="saveStudentFee(event)">
            @csrf
            <input type="hidden" name="student_id" id="feeFormStudentId">
            <input type="hidden" name="academic_year" value="{{ $year }}">

            <div class="form-body-wrap">
                <div class="form-field-group">
                    <label class="field-label">{{ __('القسط الشهري الأساسي للطالب (₪)') }} <span class="required">*</span></label>
                    <input type="number" step="1" min="0" name="monthly_fee" id="feeFormMonthlyFee" class="clean-input font-mono font-bold" required oninput="calcNetFeePreview()">
                    <small style="color: #64748b; font-size: 0.74rem;">{{ __('القسط الرسمي المعتمد للطالب قبل تطبيق أي خصومات') }}</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="form-field-group">
                        <label class="field-label">{{ __('نسبة الخصم (%)') }}</label>
                        <input type="number" step="1" min="0" max="100" name="custom_discount_percent" id="feeFormDiscountPercent" class="clean-input font-mono" placeholder="0" oninput="calcNetFeePreview()">
                        <small style="color: #64748b; font-size: 0.74rem;">{{ __('مثال: 50% أو 100% لمنحة كاملة') }}</small>
                    </div>
                    <div class="form-field-group">
                        <label class="field-label">{{ __('أو خصم ثابت (₪)') }}</label>
                        <input type="number" step="1" min="0" name="custom_discount_fixed" id="feeFormDiscountFixed" class="clean-input font-mono" placeholder="0" oninput="calcNetFeePreview()">
                        <small style="color: #64748b; font-size: 0.74rem;">{{ __('مثال: خصم 50 شيكل شهرياً') }}</small>
                    </div>
                </div>

                <!-- معاينة الصافي المستحق -->
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.84rem; font-weight: 700; color: #334155;">{{ __('صافي القسط المطلوب شهرياً من الطالب:') }}</span>
                    <strong style="font-size: 1.15rem; color: #15803d;" class="font-mono" id="previewNetFee">150 ₪</strong>
                </div>

                <div class="form-field-group">
                    <label class="field-label">{{ __('بيان وملاحظات الخصم أو المنحة (تظهر للطالب بالسند)') }}</label>
                    <input type="text" name="discount_notes" id="feeFormDiscountNotes" class="clean-input" placeholder="{{ __('مثال: منحة تفوق، خصم إخوة، إعفاء جزئي...') }}">
                </div>
            </div>

            <div class="modal-footer-row">
                <button type="submit" class="btn-save-sub" id="btnSaveFee">
                    <i class="fa-solid fa-floppy-disk"></i> {{ __('حفظ الرسوم وتحديث الخطة') }}
                </button>
                <button type="button" class="btn-cancel-sub" onclick="closeStudentFeeModal()">{{ __('إلغاء') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- 8. مودال تعديل القسط الشهري العام الافتراضي للمنصة --}}
<div id="editGlobalFeeModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box" style="max-width: 440px;">
        <div class="modal-header-row">
            <div>
                <h3 style="margin: 0 0 4px; font-size: 1.2rem; color: #0f172a;">{{ __('الرسوم الشهرية العامة للمنصة') }}</h3>
                <p style="margin: 0; font-size: 0.85rem; color: #64748b;">{{ __('القسط الشهري الافتراضي لكافة طلاب منارة التوجيهي') }}</p>
            </div>
            <button type="button" class="btn-close-x" onclick="closeGlobalFeeModal()">&times;</button>
        </div>

        <form id="updateGlobalFeeForm" onsubmit="saveGlobalFee(event)">
            @csrf
            <div class="form-body-wrap">
                <div class="form-field-group">
                    <label class="field-label">{{ __('القسط الشهري الافتراضي الجديد (₪)') }} <span class="required">*</span></label>
                    <input type="number" step="1" min="0" name="default_monthly_fee" id="formGlobalFeeInput" value="{{ \App\Models\Setting::get('default_monthly_fee', 150) }}" class="clean-input font-mono font-bold" style="font-size: 1.2rem; text-align: center;" required>
                    <small style="color: #64748b; font-size: 0.74rem;">{{ __('يُطبق هذا القسط تلقائياً على أي طالب جديد لا يوجد له خطة خاصة') }}</small>
                </div>
            </div>

            <div class="modal-footer-row">
                <button type="submit" class="btn-save-sub" id="btnSaveGlobalFee">
                    <i class="fa-solid fa-check"></i> {{ __('اعتماد وتطبيق الرسوم') }}
                </button>
                <button type="button" class="btn-cancel-sub" onclick="closeGlobalFeeModal()">{{ __('إلغاء') }}</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function yearString() {
        return '{{ $year }}';
    }

    // تبديل درج الجدول التفصيلي للشهور
    function toggleStudentTableDrawer(studentId) {
        const drawer = document.getElementById(`drawer_table_${studentId}`);
        if (drawer) {
            drawer.style.display = (drawer.style.display === 'none' || drawer.style.display === '') ? 'block' : 'none';
        }
    }

    // حساب المتبقي الحي عند كتابة المبالغ في المودال
    function calcRemainingLive() {
        const amtInput = document.getElementById('formAmount');
        const paidInput = document.getElementById('formPaidAmount');
        const remPreview = document.getElementById('formRemainingPreview');
        const statusNotice = document.getElementById('formStatusNotice');
        const statusRadioPaid = document.getElementById('optStatusPaid');
        const statusRadioPartial = document.getElementById('optStatusPartial');
        const statusRadioUnpaid = document.getElementById('optStatusUnpaid');

        const amt = parseFloat(amtInput.value) || 0;
        const paid = parseFloat(paidInput.value) || 0;
        const remaining = Math.max(0, amt - paid);

        remPreview.innerText = remaining.toFixed(2) + ' ₪';

        // ضبط إشعار الحالة والأيقونة تلقائياً
        if (paid >= amt && amt > 0) {
            statusNotice.className = 'calc-status-indicator is-paid';
            statusNotice.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>{{ __('مسدد بالكامل وخالص ✅ (متبقي 0 ₪)') }}</span>';
            if (statusRadioPaid) statusRadioPaid.checked = true;
        } else if (paid > 0 && paid < amt) {
            statusNotice.className = 'calc-status-indicator is-partial';
            statusNotice.innerHTML = '<i class="fa-solid fa-circle-half-stroke"></i> <span>{{ __('دفع جزئي ⚠️ (متبقي بذمته ') }}' + remaining.toFixed(2) + ' ₪)</span>';
            if (statusRadioPartial) statusRadioPartial.checked = true;
        } else if (paid === 0 && amt > 0) {
            statusNotice.className = 'calc-status-indicator is-unpaid';
            statusNotice.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> <span>{{ __('غير مسدد ❌ (مستحق كامل: ') }}' + amt.toFixed(2) + ' ₪)</span>';
            if (statusRadioUnpaid) statusRadioUnpaid.checked = true;
        }
    }

    // أزرار المبالغ السريعة
    function setPresetPaid(type) {
        const amtInput = document.getElementById('formAmount');
        const paidInput = document.getElementById('formPaidAmount');
        const amt = parseFloat(amtInput.value) || 0;

        if (type === 'full') {
            paidInput.value = amt.toFixed(2);
        } else if (type === 'half') {
            paidInput.value = (amt / 2).toFixed(2);
        } else if (type === 'zero') {
            paidInput.value = '0.00';
        }
        calcRemainingLive();
    }

    // استجابة تغيير أزرار الراديو
    function onStatusRadioChange(val) {
        const amtInput = document.getElementById('formAmount');
        const paidInput = document.getElementById('formPaidAmount');
        const hiddenFee = document.getElementById('formStudentFeeHidden');
        const defaultFee = parseFloat(hiddenFee.value) || 150;

        if (val === 'waived') {
            amtInput.value = '0.00';
            paidInput.value = '0.00';
        } else if (val === 'paid') {
            if (parseFloat(amtInput.value) === 0) amtInput.value = defaultFee.toFixed(2);
            paidInput.value = amtInput.value;
        } else if (val === 'partial') {
            if (parseFloat(amtInput.value) === 0) amtInput.value = defaultFee.toFixed(2);
            const curAmt = parseFloat(amtInput.value);
            paidInput.value = (curAmt > 0) ? (curAmt / 2).toFixed(2) : '75.00';
        } else if (val === 'unpaid' || val === 'pending') {
            if (parseFloat(amtInput.value) === 0) amtInput.value = defaultFee.toFixed(2);
            paidInput.value = '0.00';
        }
        calcRemainingLive();
    }

    function openEditMonthModalFromEl(el) {
        const studentId = el.dataset.studentId;
        const studentName = el.dataset.studentName;
        const month = el.dataset.month;
        const monthTitle = el.dataset.monthTitle;
        const currentStatus = el.dataset.status;
        const currentAmount = el.dataset.amount;
        const currentPaidAmount = el.dataset.paidAmount !== undefined ? el.dataset.paidAmount : (currentStatus === 'paid' ? currentAmount : '0.00');
        const currentNotes = el.dataset.notes || '';
        const studentFee = el.dataset.studentFee || '150';

        document.getElementById('formStudentId').value = studentId;
        document.getElementById('formMonth').value = month;
        document.getElementById('formStudentFeeHidden').value = studentFee;
        document.getElementById('modalStudentNameTitle').innerText = studentName;
        document.getElementById('modalMonthSubtitle').innerText = '{{ __('اشتراك') }} ' + monthTitle + ' (' + yearString() + ')';
        document.getElementById('formAmount').value = currentAmount;
        document.getElementById('formPaidAmount').value = currentPaidAmount;
        document.getElementById('formNotes').value = currentNotes;

        document.querySelectorAll('#updateMonthForm input[name="status"]').forEach(r => r.checked = false);
        const radio = document.querySelector(`#updateMonthForm input[name="status"][value="${currentStatus}"]`);
        if (radio) {
            radio.checked = true;
        }

        calcRemainingLive();
        document.getElementById('editMonthModal').style.display = 'flex';
    }

    // دعم الاستدعاء المباشر القديم للتوافقية
    function openEditMonthModal(studentId, studentName, month, monthLabel, currentStatus, currentAmount, currentNotes) {
        const badge = document.getElementById(`badge_${studentId}_${month}`);
        if (badge) {
            openEditMonthModalFromEl(badge);
        } else {
            document.getElementById('formStudentId').value = studentId;
            document.getElementById('formMonth').value = month;
            document.getElementById('modalStudentNameTitle').innerText = studentName;
            document.getElementById('modalMonthSubtitle').innerText = '{{ __('اشتراك') }} ' + monthLabel + ' (' + yearString() + ')';
            document.getElementById('formAmount').value = currentAmount;
            document.getElementById('formPaidAmount').value = (currentStatus === 'paid') ? currentAmount : '0.00';
            document.getElementById('formNotes').value = currentNotes || '';
            const radio = document.querySelector(`#updateMonthForm input[name="status"][value="${currentStatus}"]`);
            if (radio) radio.checked = true;
            calcRemainingLive();
            document.getElementById('editMonthModal').style.display = 'flex';
        }
    }

    function closeEditMonthModal() {
        document.getElementById('editMonthModal').style.display = 'none';
    }

    // حفظ الاشتراك عبر AJAX مع تحديث حي للعدادات ولجدول الطالب
    function saveMonthSubscription(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveSub');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

        const form = document.getElementById('updateMonthForm');
        const formData = new FormData(form);

        axios.post("{{ route('admin.subscriptions.monthly.update') }}", Object.fromEntries(formData))
        .then(res => {
            closeEditMonthModal();
            const sId = formData.get('student_id');
            const m = formData.get('month');
            const targetBadge = document.getElementById(`badge_${sId}_${m}`);

            if (targetBadge) {
                targetBadge.dataset.status = res.data.status;
                targetBadge.dataset.amount = res.data.amount;
                targetBadge.dataset.paidAmount = res.data.paid_amount;
                targetBadge.dataset.remainingAmount = res.data.remaining_amount;
                targetBadge.dataset.notes = res.data.notes || '';

                const isHighlight = targetBadge.classList.contains('month-highlight-col');
                targetBadge.className = `month-micro-badge badge-${res.data.status} ${isHighlight ? 'month-highlight-col' : ''}`;
                targetBadge.title = `${targetBadge.dataset.monthTitle} | المطلوب: ${Math.round(res.data.amount)} ₪ | المدفوع: ${Math.round(res.data.paid_amount)} ₪ | المتبقي: ${Math.round(res.data.remaining_amount)} ₪ - انقر لتعديل الشهر`;

                let icon = '<i class="fa-solid fa-xmark badge-icon"></i>';
                if (res.data.status === 'paid') icon = '<i class="fa-solid fa-check badge-icon"></i>';
                else if (res.data.status === 'partial') icon = '<i class="fa-solid fa-circle-half-stroke badge-icon" style="color: #b45309;"></i>';
                else if (res.data.status === 'pending') icon = '<i class="fa-solid fa-hourglass-half badge-icon"></i>';
                else if (res.data.status === 'waived') icon = '<i class="fa-solid fa-tag badge-icon"></i>';

                let subText = '';
                if (res.data.status === 'partial') {
                    subText = `<span class="badge-partial-sub font-mono">${Math.round(res.data.paid_amount)}/${Math.round(res.data.amount)}</span>`;
                }

                targetBadge.innerHTML = `<span class="m-digit font-mono">${m}</span> ${icon} ${subText}`;
            }

            // تحديث صف الجدول في الدرج Drawer إن كان مفتوحاً
            const dAmt = document.getElementById(`drawer_amt_${sId}_${m}`);
            const dPaid = document.getElementById(`drawer_paid_${sId}_${m}`);
            const dRem = document.getElementById(`drawer_rem_${sId}_${m}`);
            const dSt = document.getElementById(`drawer_status_${sId}_${m}`);
            const dNotes = document.getElementById(`drawer_notes_${sId}_${m}`);

            if (dAmt) dAmt.innerText = parseFloat(res.data.amount).toFixed(2) + ' ₪';
            if (dPaid) dPaid.innerText = parseFloat(res.data.paid_amount).toFixed(2) + ' ₪';
            if (dRem) {
                dRem.innerText = parseFloat(res.data.remaining_amount).toFixed(2) + ' ₪';
                if (res.data.remaining_amount > 0) {
                    dRem.className = 'font-mono font-bold text-rose';
                } else {
                    dRem.className = 'font-mono font-bold text-emerald';
                }
            }
            if (dSt) {
                dSt.innerHTML = `<span class="status-pill-small badge-${res.data.status}">${res.data.badge.label}</span>`;
            }
            if (dNotes) dNotes.innerText = res.data.notes || '-';

            // تحديث شارات الموقف المالي للطالب
            if (res.data.student_due) {
                const sDueEl = document.getElementById(`std_due_${sId}`);
                if (sDueEl) sDueEl.innerText = res.data.student_due;
            }
            if (res.data.student_paid) {
                const sPaidEl = document.getElementById(`std_paid_${sId}`);
                if (sPaidEl) sPaidEl.innerText = res.data.student_paid;
            }
            if (res.data.student_remaining) {
                const sRemEl = document.getElementById(`std_rem_${sId}`);
                if (sRemEl) {
                    sRemEl.innerText = res.data.student_has_remaining ? res.data.student_remaining + ' ⚠️' : '0 ₪ ✅';
                    const parentPill = sRemEl.closest('.fin-remaining');
                    if (parentPill) {
                        if (res.data.student_has_remaining) {
                            parentPill.classList.add('has-remaining-alert');
                            parentPill.classList.remove('is-clear');
                        } else {
                            parentPill.classList.remove('has-remaining-alert');
                            parentPill.classList.add('is-clear');
                        }
                    }
                }
            }

            // تحديث بطاقات الإحصائيات العامة للمنصة مباشرة (اللي لازم يصلني، اللي وصلني، المتبقي)
            if (res.data.stats) {
                const expEl = document.getElementById('stat_total_expected');
                if (expEl) expEl.innerText = res.data.stats.total_expected;

                const colEl = document.getElementById('stat_total_collected');
                if (colEl) colEl.innerText = res.data.stats.total_collected;

                const remEl = document.getElementById('stat_total_remaining');
                if (remEl) remEl.innerText = res.data.stats.total_remaining;

                const rateEl = document.getElementById('stat_collection_rate');
                if (rateEl) rateEl.innerText = res.data.stats.collection_rate;

                const progFill = document.getElementById('stat_progress_fill');
                if (progFill) progFill.style.width = res.data.stats.collection_rate;

                const pCount = document.getElementById('stat_paid_count');
                if (pCount) pCount.innerText = res.data.stats.paid_count;

                const partCount = document.getElementById('stat_partial_count');
                if (partCount) partCount.innerText = res.data.stats.partial_count;

                const uCount = document.getElementById('stat_unpaid_count');
                if (uCount) uCount.innerText = res.data.stats.unpaid_count;

                const pendCount = document.getElementById('stat_pending_count');
                if (pendCount) pendCount.innerText = res.data.stats.pending_count;
            }

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.data.message,
                showConfirmButton: false,
                timer: 2400
            });
        })
        .catch(err => {
            Swal.fire('{{ __('خطأ') }}', err.response?.data?.message || '{{ __('فشل تحديث بيانات الاشتراك') }}', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> {{ __('حفظ التحديث فورياً') }}';
        });
    }

    // فتح مودال كشف الحساب والسند الرسمي المعتمد للطالب
    function openStudentStatementModal(studentId) {
        const row = document.getElementById(`student_row_${studentId}`);
        if (!row) return;

        const nameEl = row.querySelector('.student-name');
        const branchEl = row.querySelector('.branch-pill');
        const phoneEl = row.querySelector('.phone-text');
        const feeEl = document.getElementById(`student_fee_label_${studentId}`);
        const dueEl = document.getElementById(`std_due_${studentId}`);
        const paidEl = document.getElementById(`std_paid_${studentId}`);
        const remEl = document.getElementById(`std_rem_${studentId}`);

        document.getElementById('stmtStudentName').innerText = nameEl ? nameEl.innerText.trim() : '';
        document.getElementById('stmtStudentStage').innerText = branchEl ? branchEl.innerText.trim() : '';
        document.getElementById('stmtStudentIdPhone').innerText = phoneEl ? phoneEl.innerText.trim() : '';
        document.getElementById('stmtStudentBaseFee').innerText = feeEl ? feeEl.innerText.trim() : '';
        document.getElementById('stmtTotalDue').innerText = dueEl ? dueEl.innerText.trim() : '';
        document.getElementById('stmtTotalPaid').innerText = paidEl ? paidEl.innerText.trim() : '';
        document.getElementById('stmtTotalRemaining').innerText = remEl ? remEl.innerText.trim() : '';

        // تجميع بيانات الشهور الـ 12 في جدول السند
        const tbody = document.getElementById('stmtTableBody');
        tbody.innerHTML = '';

        for (let m = 1; m <= 12; m++) {
            const badge = document.getElementById(`badge_${studentId}_${m}`);
            if (!badge) continue;

            const mTitle = badge.dataset.monthTitle || ('{{ __('شهر') }} ' + m);
            const amt = parseFloat(badge.dataset.amount || 0).toFixed(2);
            const paid = parseFloat(badge.dataset.paidAmount || 0).toFixed(2);
            const rem = parseFloat(badge.dataset.remainingAmount || 0).toFixed(2);
            const st = badge.dataset.status;
            const notes = badge.dataset.notes || '-';

            let stBadge = '';
            if (st === 'paid') stBadge = '<span class="sheet-status bg-p">{{ __('خالص ومسدد') }} ✅</span>';
            else if (st === 'partial') stBadge = '<span class="sheet-status bg-part">{{ __('دفع جزئي (متبقي)') }} ⚠️</span>';
            else if (st === 'pending') stBadge = '<span class="sheet-status bg-pend">{{ __('قيد المراجعة') }} ⏳</span>';
            else if (st === 'waived') stBadge = '<span class="sheet-status bg-w">{{ __('إعفاء / منحة') }} 🏷️</span>';
            else stBadge = '<span class="sheet-status bg-u">{{ __('غير مسدد') }} ❌</span>';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="font-mono text-center"><strong>${m}</strong></td>
                <td><strong>${mTitle}</strong></td>
                <td class="font-mono text-center">${amt} ₪</td>
                <td class="font-mono text-center text-emerald"><strong>${paid} ₪</strong></td>
                <td class="font-mono text-center ${rem > 0 ? 'text-rose font-bold' : 'text-emerald'}">${rem} ₪</td>
                <td class="text-center">${stBadge}</td>
                <td class="font-mono text-center text-muted" style="font-size: 0.76rem;">${st === 'paid' || st === 'partial' ? '{{ date("Y-m-d") }}' : '-'}</td>
                <td style="font-size: 0.8rem; color: #475569;">${notes}</td>
            `;
            tbody.appendChild(tr);
        }

        document.getElementById('statementModal').style.display = 'flex';
    }

    function closeStatementModal() {
        document.getElementById('statementModal').style.display = 'none';
    }

    function printGeneralMatrixDoc() {
        document.body.classList.remove('print-statement-active');
        document.body.classList.add('print-matrix-active');
        window.print();
    }

    function printStatementDoc() {
        document.body.classList.remove('print-matrix-active');
        document.body.classList.add('print-statement-active');
        window.print();
    }

    // مزامنة الطباعة التلقائية مع اختصار Ctrl+P وأمر طباعة المتصفح
    window.addEventListener('beforeprint', function() {
        const statementModal = document.getElementById('statementModal');
        const isStatementOpen = statementModal && (statementModal.style.display === 'flex' || statementModal.style.display === 'block');
        if (isStatementOpen) {
            document.body.classList.add('print-statement-active');
            document.body.classList.remove('print-matrix-active');
        } else {
            document.body.classList.add('print-matrix-active');
            document.body.classList.remove('print-statement-active');
        }
    });

    window.addEventListener('afterprint', function() {
        document.body.classList.remove('print-matrix-active');
        document.body.classList.remove('print-statement-active');
    });

    // مودال الرسوم الفردية للطالب
    function openStudentFeeModalFromEl(el) {
        const studentId = el.dataset.studentId;
        const studentName = el.dataset.studentName;
        const monthlyFee = el.dataset.monthlyFee || 150;
        const discountPercent = el.dataset.discountPercent || '';
        const discountFixed = el.dataset.discountFixed || '';
        const discountNotes = el.dataset.discountNotes || '';

        document.getElementById('feeFormStudentId').value = studentId;
        document.getElementById('feeStudentNameTitle').innerText = '{{ __('تعديل رسوم الطالب:') }} ' + studentName;
        document.getElementById('feeFormMonthlyFee').value = monthlyFee;
        document.getElementById('feeFormDiscountPercent').value = discountPercent;
        document.getElementById('feeFormDiscountFixed').value = discountFixed;
        document.getElementById('feeFormDiscountNotes').value = discountNotes;
        calcNetFeePreview();
        document.getElementById('editStudentFeeModal').style.display = 'flex';
    }

    function openStudentFeeModal(studentId, studentName, monthlyFee, discountPercent, discountFixed, discountNotes) {
        const btn = document.getElementById(`fee_btn_${studentId}`);
        if (btn) {
            openStudentFeeModalFromEl(btn);
        } else {
            document.getElementById('feeFormStudentId').value = studentId;
            document.getElementById('feeStudentNameTitle').innerText = '{{ __('تعديل رسوم الطالب:') }} ' + studentName;
            document.getElementById('feeFormMonthlyFee').value = monthlyFee;
            document.getElementById('feeFormDiscountPercent').value = discountPercent || '';
            document.getElementById('feeFormDiscountFixed').value = discountFixed || '';
            document.getElementById('feeFormDiscountNotes').value = discountNotes || '';
            calcNetFeePreview();
            document.getElementById('editStudentFeeModal').style.display = 'flex';
        }
    }

    function closeStudentFeeModal() {
        document.getElementById('editStudentFeeModal').style.display = 'none';
    }

    function calcNetFeePreview() {
        const base = parseFloat(document.getElementById('feeFormMonthlyFee').value) || 0;
        const pct = parseFloat(document.getElementById('feeFormDiscountPercent').value) || 0;
        const fix = parseFloat(document.getElementById('feeFormDiscountFixed').value) || 0;

        let net = base;
        if (pct > 0) {
            net = base * (1 - (pct / 100));
        } else if (fix > 0) {
            net = Math.max(0, base - fix);
        }
        document.getElementById('previewNetFee').innerText = Math.round(net) + ' ₪';
    }

    function saveStudentFee(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveFee');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

        const form = document.getElementById('updateStudentFeeForm');
        const formData = new FormData(form);

        axios.post("{{ route('admin.subscriptions.monthly.updateStudentFee') }}", Object.fromEntries(formData))
        .then(res => {
            closeStudentFeeModal();
            const sId = formData.get('student_id');
            const lbl = document.getElementById(`student_fee_label_${sId}`);
            if (lbl) {
                lbl.innerText = res.data.amount_due + ' ₪/شهر';
            }

            const feeBtn = document.getElementById(`fee_btn_${sId}`);
            if (feeBtn) {
                feeBtn.dataset.monthlyFee = res.data.monthly_fee;
                feeBtn.dataset.discountPercent = res.data.discount_percent;
                feeBtn.dataset.discountFixed = res.data.discount_fixed;
                feeBtn.dataset.discountNotes = res.data.discount_notes || '';
            }

            // تحديث بطاقات الشهور غير المسددة للطالب
            for (let m = 1; m <= 12; m++) {
                const b = document.getElementById(`badge_${sId}_${m}`);
                if (b) {
                    b.dataset.studentFee = res.data.amount_due;
                    if (b.dataset.status === 'unpaid') {
                        b.dataset.amount = res.data.amount_due;
                        b.dataset.remainingAmount = res.data.amount_due;
                        b.title = `${b.dataset.monthTitle} | المطلوب: ${Math.round(res.data.amount_due)} ₪ | المتبقي: ${Math.round(res.data.amount_due)} ₪ - انقر لتعديل الشهر`;
                    }
                }
            }

            Swal.fire({
                icon: 'success',
                title: '{{ __('تم التحديث بنجاح') }}',
                text: res.data.message,
                confirmButtonColor: '#1d4ed8'
            });
        })
        .catch(err => {
            Swal.fire('{{ __('خطأ') }}', err.response?.data?.message || '{{ __('فشل تحديث رسوم الطالب') }}', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> {{ __('حفظ الرسوم وتحديث الخطة') }}';
        });
    }

    // مودال الرسوم العامة للمنصة
    function openGlobalFeeModal() {
        document.getElementById('editGlobalFeeModal').style.display = 'flex';
    }

    function closeGlobalFeeModal() {
        document.getElementById('editGlobalFeeModal').style.display = 'none';
    }

    function saveGlobalFee(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveGlobalFee');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري التطبيق...') }}';

        const form = document.getElementById('updateGlobalFeeForm');
        const formData = new FormData(form);

        axios.post("{{ route('admin.subscriptions.monthly.updateGlobalFee') }}", Object.fromEntries(formData))
        .then(res => {
            closeGlobalFeeModal();
            document.getElementById('globalFeeDisplay').innerText = res.data.fee + ' ₪';
            Swal.fire({
                icon: 'success',
                title: '{{ __('تم اعتماد الرسوم العامة') }}',
                text: res.data.message,
                confirmButtonColor: '#1d4ed8'
            });
        })
        .catch(err => {
            Swal.fire('{{ __('خطأ') }}', err.response?.data?.message || '{{ __('فشل تحديث الرسوم العامة') }}', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> {{ __('اعتماد وتطبيق الرسوم') }}';
        });
    }

    window.onclick = function(e) {
        const modal1 = document.getElementById('editMonthModal');
        const modal2 = document.getElementById('editStudentFeeModal');
        const modal3 = document.getElementById('editGlobalFeeModal');
        const modal4 = document.getElementById('statementModal');
        if (e.target === modal1) closeEditMonthModal();
        if (e.target === modal2) closeStudentFeeModal();
        if (e.target === modal3) closeGlobalFeeModal();
        if (e.target === modal4) closeStatementModal();
    }
</script>

<style>
    /* =========================================================================
       التصميم الأكاديمي الملكي الكلاسيكي - منصة منارة التوجيهي
       مطابق للألوان والخطوط والترويسة الرسمية في صورة الشهادة
       ========================================================================= */
    .subs-matrix-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding-bottom: 50px;
    }

    /* 1. الترويسة الأكاديمية الكلاسيكية المزدوجة */
    .royal-academic-header-card {
        background: #ffffff;
        border: 2px solid #1e3a8a;
        border-radius: 16px;
        padding: 24px 28px 18px;
        box-shadow: 0 4px 20px rgba(30, 58, 138, 0.08);
        position: relative;
        overflow: hidden;
    }
    .royal-academic-header-card::before {
        content: '';
        position: absolute;
        inset: 4px;
        border: 1px solid #d97706;
        border-radius: 12px;
        pointer-events: none;
    }

    .royal-header-frame {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 20px;
        padding-bottom: 18px;
        border-bottom: 1px dashed #cbd5e1;
    }

    .header-col-ar {
        text-align: right;
    }
    .state-title-ar {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 900;
        color: #0f172a;
    }
    .inst-title-ar {
        margin: 2px 0 6px;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3a8a;
    }
    .dept-badge {
        display: inline-block;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 6px;
    }

    .header-emblem-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .emblem-wrapper {
        width: 76px;
        height: 76px;
        display: grid;
        place-items: center;
        margin-bottom: 6px;
    }
    .header-logo-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .emblem-circle-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #eff6ff;
        border: 2px solid #1e3a8a;
        display: grid;
        place-items: center;
        font-size: 1.8rem;
        color: #1e3a8a;
    }
    .emblem-sub-tag {
        font-size: 0.78rem;
        font-weight: 800;
        color: #b45309;
        letter-spacing: 0.5px;
    }
    .academic-year-tag {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        font-size: 0.74rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        margin-top: 4px;
    }

    .header-col-en {
        text-align: left;
    }
    .state-title-en {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: 0.5px;
    }
    .inst-title-en {
        margin: 2px 0 6px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e3a8a;
    }
    .dept-badge-en {
        display: inline-block;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 6px;
    }

    .royal-toolbar-strip {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .toolbar-left-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        color: #334155;
    }
    .btn-royal-small {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #1e3a8a;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .btn-royal-small:hover {
        background: #eff6ff;
        border-color: #1d4ed8;
    }

    .toolbar-right-tools {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .year-select-form {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .year-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
    }
    .year-dropdown {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        cursor: pointer;
    }
    .btn-royal-print-all {
        background: #1e3a8a;
        color: #ffffff;
        border: 1px solid #1e3a8a;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-royal-print-all:hover {
        background: #1d4ed8;
    }

    /* 2. العدادات الأكاديمية الكبرى (اللي لازم يصلني، اللي وصلني، المتبقي) */
    .financial-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    @media (max-width: 1100px) {
        .financial-kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .financial-kpi-grid { grid-template-columns: 1fr; }
        .royal-header-frame { grid-template-columns: 1fr; text-align: center; }
        .header-col-ar, .header-col-en { text-align: center; }
    }

    .kpi-card-royal {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-top: 4px solid var(--kpi-theme);
        border-radius: 14px;
        padding: 16px 18px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card-royal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    }

    .kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .kpi-tag-pill {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
    }
    .bg-navy-subtle { background: #eff6ff; color: #1e3a8a; }
    .bg-emerald-subtle { background: #ecfdf5; color: #059669; }
    .bg-rose-subtle { background: #fef2f2; color: #dc2626; }
    .bg-amber-subtle { background: #fffbeb; color: #b45309; }

    .kpi-icon-wrap {
        font-size: 1.3rem;
    }

    .kpi-title {
        font-size: 0.88rem;
        font-weight: 800;
        color: #334155;
        display: block;
        margin-bottom: 4px;
    }
    .kpi-amount {
        font-size: 1.65rem;
        font-weight: 900;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
    }
    .kpi-subtext {
        font-size: 0.72rem;
        color: #64748b;
        margin: 0;
        line-height: 1.3;
    }

    .kpi-footer {
        margin-top: 12px;
        padding-top: 8px;
        border-top: 1px dashed #f1f5f9;
        font-size: 0.74rem;
        font-weight: 700;
        color: #475569;
        display: flex;
        justify-content: space-between;
    }
    .kpi-progress-track {
        height: 6px;
        background: #f1f5f9;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 6px;
    }
    .kpi-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #d97706, #059669);
        border-radius: 4px;
        transition: width 0.4s ease;
    }

    /* 3. صندوق الفلاتر ودليل الحالات */
    .filter-box-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.02);
    }
    .filters-wrap {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .search-cell {
        flex: 1;
        min-width: 220px;
        position: relative;
    }
    .search-cell i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .search-input {
        width: 100%;
        padding: 8px 36px 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.84rem;
        outline: none;
    }
    .search-input:focus {
        border-color: #1e3a8a;
    }
    .filter-select {
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.84rem;
        background: #ffffff;
        color: #0f172a;
    }
    .btn-filter-submit {
        background: #1e3a8a;
        color: #ffffff;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.84rem;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-reset-filter {
        color: #64748b;
        font-size: 0.82rem;
        text-decoration: underline;
        margin-right: 6px;
    }

    .legend-strip {
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px dashed #f1f5f9;
        display: flex;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
        font-size: 0.74rem;
        color: #475569;
    }
    .legend-title { font-weight: 800; color: #1e293b; }
    .legend-item { display: inline-flex; align-items: center; gap: 5px; }
    .badge-mini {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .bg-paid { background: #059669; }
    .bg-partial { background: #b45309; }
    .bg-pending { background: #d97706; }
    .bg-unpaid { background: #dc2626; }
    .bg-waived { background: #4f46e5; }

    /* 4. قائمة مصفوفة الطلاب */
    .students-list-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .list-header-row {
        display: grid;
        grid-template-columns: 280px 240px 1fr 140px;
        gap: 14px;
        padding: 8px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 800;
        color: #475569;
    }
    @media (max-width: 1200px) {
        .list-header-row { display: none; }
    }

    .student-matrix-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        display: grid;
        grid-template-columns: 280px 240px 1fr 140px;
        gap: 14px;
        align-items: center;
        box-shadow: 0 1px 4px rgba(0,0,0,0.02);
        transition: all 0.2s;
    }
    .student-matrix-row:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    @media (max-width: 1200px) {
        .student-matrix-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    .student-profile-block {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .student-avatar {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid #cbd5e1;
    }
    .student-name {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
        text-decoration: none;
    }
    .student-name:hover { color: #1d4ed8; }
    .student-sub-line {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 2px;
    }
    .branch-pill {
        background: #eff6ff;
        color: #1e40af;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .phone-text {
        font-size: 0.72rem;
        color: #64748b;
    }
    .student-fee-badge-btn {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 0.72rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-discount-tag {
        background: #b45309;
        color: white;
        font-size: 0.62rem;
        padding: 0 3px;
        border-radius: 3px;
    }

    /* ملخص الموقف المالي لكل طالب */
    .student-financial-summary-block {
        display: flex;
        align-items: center;
    }
    .fin-pill-group {
        display: flex;
        gap: 6px;
        width: 100%;
    }
    .fin-pill {
        flex: 1;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 4px 8px;
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .fin-lbl {
        font-size: 0.65rem;
        color: #64748b;
        font-weight: 700;
    }
    .fin-due strong { color: #1e3a8a; font-size: 0.86rem; }
    .fin-paid strong { color: #059669; font-size: 0.86rem; }
    .fin-remaining strong { font-size: 0.86rem; }
    .has-remaining-alert {
        background: #fff1f2;
        border-color: #fecdd3;
    }
    .has-remaining-alert strong { color: #dc2626; }
    .is-clear {
        background: #ecfdf5;
        border-color: #a7f3d0;
    }
    .is-clear strong { color: #059669; }

    /* أزرار الشهور الـ 12 */
    .months-strip-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 5px;
    }
    .month-micro-badge {
        height: 38px;
        border-radius: 7px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        font-size: 0.68rem;
        border: 1px solid transparent;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .month-micro-badge:hover {
        transform: scale(1.08);
        z-index: 5;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }
    .m-digit { font-size: 0.65rem; font-weight: 800; line-height: 1; }
    .badge-icon { font-size: 0.68rem; margin-top: 1px; }
    .badge-partial-sub {
        font-size: 0.58rem;
        font-weight: 800;
        color: #92400e;
        line-height: 1;
        margin-top: 1px;
    }

    .badge-paid {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #059669;
    }
    .badge-partial {
        background: #fef3c7;
        border-color: #fcd34d;
        color: #b45309;
    }
    .badge-pending {
        background: #fffbeb;
        border-color: #fef08a;
        color: #d97706;
    }
    .badge-unpaid {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .badge-waived {
        background: #eef2ff;
        border-color: #c7d2fe;
        color: #4f46e5;
    }
    .month-highlight-col {
        outline: 2px solid #1e3a8a;
        box-shadow: 0 0 6px rgba(30, 58, 138, 0.4);
    }

    /* أزرار الإجراءات */
    .student-actions-block {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
    }
    .btn-statement-royal {
        background: #fffdf9;
        border: 1px solid #d97706;
        color: #92400e;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 0.74rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .btn-statement-royal:hover {
        background: #fef3c7;
        border-color: #b45309;
    }
    .btn-toggle-drawer {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 6px 9px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.8rem;
    }
    .btn-toggle-drawer:hover {
        background: #eff6ff;
        border-color: #1e3a8a;
        color: #1e3a8a;
    }

    /* درج جدول الشهور الـ 12 */
    .student-drawer-table-wrap {
        grid-column: 1 / -1;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 14px;
        margin-top: 8px;
    }
    .drawer-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .drawer-title {
        font-size: 0.84rem;
        font-weight: 700;
        color: #0f172a;
    }
    .btn-close-drawer {
        background: none;
        border: none;
        font-size: 1.3rem;
        color: #64748b;
        cursor: pointer;
    }
    .classic-ledger-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
        background: #ffffff;
        border-radius: 8px;
        overflow: hidden;
    }
    .classic-ledger-table th {
        background: #1e3a8a;
        color: #ffffff;
        padding: 8px 12px;
        font-weight: 700;
        text-align: right;
    }
    .classic-ledger-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #e2e8f0;
    }
    .status-pill-small {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-edit-row {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        cursor: pointer;
    }

    /* مودال تعديل الشهر - الحاسبة الحية */
    .modal-royal-theme {
        max-width: 580px;
    }
    .modal-header-royal {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 14px;
        border-bottom: 1px solid #e2e8f0;
    }
    .modal-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .modal-crest {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        display: grid;
        place-items: center;
        color: #1e3a8a;
        font-size: 1.3rem;
    }
    .modal-student-name {
        margin: 0;
        font-size: 1.15rem;
        color: #0f172a;
        font-weight: 800;
    }
    .modal-month-desc {
        margin: 2px 0 0;
        font-size: 0.8rem;
        color: #64748b;
    }
    .amounts-calc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-top: 10px;
    }
    .quick-amount-presets {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
    }
    .preset-label {
        font-size: 0.74rem;
        font-weight: 700;
        color: #64748b;
    }
    .btn-preset {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-preset:hover {
        background: #eff6ff;
        border-color: #1e3a8a;
        color: #1e3a8a;
    }

    .live-calc-box {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        margin: 12px 0;
    }
    .calc-label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .calc-text {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
    }
    .calc-value {
        font-size: 1.4rem;
        font-weight: 900;
        color: #dc2626;
    }
    .calc-status-indicator {
        margin-top: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .is-paid { color: #059669; }
    .is-partial { color: #b45309; }
    .is-unpaid { color: #dc2626; }

    /* مودال سند كشف الحساب الرسمي المعتمد (طباعة ملكية كالصورة) */
    .modal-statement-sheet-wrap {
        max-width: 900px;
        width: 95%;
        padding: 0;
        overflow: hidden;
    }
    .statement-toolbar {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .statement-title-info {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
    }
    .btn-statement-print {
        background: #1e3a8a;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .statement-printable-sheet {
        background: #fffdf9;
        padding: 36px 40px;
        position: relative;
        color: #0f172a;
        min-height: 800px;
    }
    .royal-outer-border {
        position: absolute;
        inset: 12px;
        border: 2.5px solid #1e3a8a;
        pointer-events: none;
    }
    .royal-inner-border {
        position: absolute;
        inset: 17px;
        border: 1px solid #d97706;
        pointer-events: none;
    }

    .sheet-header {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }
    .sheet-col-ar { text-align: right; }
    .sheet-col-ar h3 { margin: 0; font-size: 1.15rem; font-weight: 900; }
    .sheet-col-ar p { margin: 2px 0 0; font-size: 0.82rem; font-weight: 700; color: #1e3a8a; }
    .sheet-col-ar small { font-size: 0.7rem; color: #64748b; }

    .sheet-emblem {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .sheet-logo-img { max-height: 56px; max-width: 90px; object-fit: contain; margin-bottom: 4px; }
    .sheet-icon-emblem {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #eff6ff;
        border: 2px solid #1e3a8a;
        display: grid;
        place-items: center;
        color: #1e3a8a;
        font-size: 1.5rem;
    }
    .sheet-badge-tag {
        font-size: 0.72rem;
        font-weight: 800;
        color: #b45309;
        border: 1px solid #fde68a;
        background: #fffbeb;
        padding: 1px 8px;
        border-radius: 4px;
        margin-top: 3px;
    }

    .sheet-col-en { text-align: left; }
    .sheet-col-en h3 { margin: 0; font-size: 1.05rem; font-weight: 800; }
    .sheet-col-en p { margin: 2px 0 0; font-size: 0.78rem; font-weight: 600; color: #1e3a8a; }
    .sheet-col-en small { font-size: 0.68rem; color: #64748b; }

    .sheet-heading {
        text-align: center;
        margin: 16px 0 20px;
        position: relative;
        z-index: 2;
    }
    .sheet-heading h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 900;
        color: #1e3a8a;
    }
    .sheet-subhead {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: #b45309;
        margin-top: 2px;
    }
    .sheet-meta-strip {
        font-size: 0.78rem;
        color: #475569;
        margin-top: 6px;
    }

    .sheet-student-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 16px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 14px;
        position: relative;
        z-index: 2;
    }
    .std-cell { display: flex; flex-direction: column; }
    .sc-lbl { font-size: 0.68rem; color: #64748b; font-weight: 700; }
    .sc-val { font-size: 0.86rem; color: #0f172a; font-weight: 800; }

    .sheet-kpi-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 16px;
        position: relative;
        z-index: 2;
    }
    .sheet-kpi-item {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        padding: 8px 12px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.84rem;
        font-weight: 700;
    }
    .sheet-kpi-item strong { font-size: 1.15rem; font-weight: 900; }

    .sheet-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        margin-bottom: 24px;
        position: relative;
        z-index: 2;
    }
    .sheet-table th {
        background: #1e3a8a;
        color: white;
        padding: 7px 10px;
        font-weight: 700;
        text-align: center;
        border: 1px solid #1e3a8a;
    }
    .sheet-table td {
        padding: 6px 10px;
        border: 1px solid #e2e8f0;
    }
    .sheet-status {
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .bg-p { background: #ecfdf5; color: #059669; }
    .bg-part { background: #fef3c7; color: #b45309; }
    .bg-pend { background: #fffbeb; color: #d97706; }
    .bg-u { background: #fef2f2; color: #dc2626; }
    .bg-w { background: #eef2ff; color: #4f46e5; }

    .sheet-footer-stamps {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        text-align: center;
        padding-top: 16px;
        border-top: 1px dashed #cbd5e1;
        position: relative;
        z-index: 2;
    }
    .stamp-col { display: flex; flex-direction: column; align-items: center; }
    .stamp-title { font-size: 0.8rem; font-weight: 800; color: #1e3a8a; }
    .signature-line {
        margin: 16px 0 4px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        font-family: 'Amiri', serif;
    }
    .stamp-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
    }
    .official-seal-box {
        width: 70px;
        height: 70px;
        border: 2px dashed #b45309;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #b45309;
        font-size: 0.62rem;
        font-weight: 800;
    }
    .official-seal-box i { font-size: 1.2rem; margin-bottom: 2px; }
    .doc-verification-code {
        font-size: 0.68rem;
        font-weight: 800;
        color: #64748b;
    }

    /* =========================================================
       أنماط الطباعة الرسمية المزدوجة (الكشف العام + سند الحساب الفردي)
       ========================================================= */
    @media print {
        @page {
            size: auto;
            margin: 8mm;
        }

        /* -------------------------------------------------------------
           الوضع الأول: طباعة الكشف المالي العام للمصفوفة والطلاب
           ------------------------------------------------------------- */
        body:not(.print-statement-active) .modal-overlay,
        body:not(.print-statement-active) #statementModal,
        body:not(.print-statement-active) #editMonthModal,
        body:not(.print-statement-active) #globalFeeModal,
        body:not(.print-statement-active) #editStudentFeeModal,
        body:not(.print-statement-active) .royal-toolbar-strip,
        body:not(.print-statement-active) .matrix-filter-card,
        body:not(.print-statement-active) .matrix-pagination-wrap,
        body:not(.print-statement-active) .actions-statement-block,
        body:not(.print-statement-active) .col-head-actions,
        body:not(.print-statement-active) .btn-toggle-drawer,
        body:not(.print-statement-active) .student-drawer-table-wrap,
        body:not(.print-statement-active) .edit-pen-icon,
        body:not(.print-statement-active) .student-fee-badge-btn i.fa-pen-to-square {
            display: none !important;
        }

        body:not(.print-statement-active) .subs-matrix-wrapper {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        body:not(.print-statement-active) .royal-academic-header-card {
            border: 1.5px solid #1e3a8a !important;
            box-shadow: none !important;
            background: #ffffff !important;
            margin-bottom: 12px !important;
            padding: 10px 14px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        body:not(.print-statement-active) .financial-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 8px !important;
            margin-bottom: 12px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        body:not(.print-statement-active) .kpi-card-royal {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
            background: #f8fafc !important;
            padding: 8px 10px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        body:not(.print-statement-active) .kpi-card-royal .kpi-amount {
            font-size: 1.15rem !important;
        }

        body:not(.print-statement-active) .students-list-wrapper {
            display: flex !important;
            flex-direction: column !important;
            gap: 6px !important;
            width: 100% !important;
        }

        body:not(.print-statement-active) .list-header-row {
            display: grid !important;
            grid-template-columns: 240px 220px 1fr !important;
            gap: 10px !important;
            padding: 6px 12px !important;
            background: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            font-size: 0.76rem !important;
        }

        body:not(.print-statement-active) .student-matrix-row {
            display: grid !important;
            grid-template-columns: 240px 220px 1fr !important;
            gap: 10px !important;
            padding: 6px 12px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin-bottom: 4px !important;
        }

        body:not(.print-statement-active) .student-avatar {
            width: 28px !important;
            height: 28px !important;
        }

        body:not(.print-statement-active) .student-name {
            font-size: 0.86rem !important;
            font-weight: 800 !important;
        }

        body:not(.print-statement-active) .months-strip-grid {
            display: grid !important;
            grid-template-columns: repeat(12, 1fr) !important;
            gap: 3px !important;
        }

        body:not(.print-statement-active) .month-micro-badge {
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
            padding: 2px 1px !important;
            font-size: 0.68rem !important;
            min-height: 38px !important;
        }

        body:not(.print-statement-active) .month-micro-badge .m-digit {
            font-size: 0.65rem !important;
        }

        body:not(.print-statement-active) .badge-paid {
            background: #ecfdf5 !important;
            color: #065f46 !important;
            border-color: #a7f3d0 !important;
        }

        body:not(.print-statement-active) .badge-partial {
            background: #fffbeb !important;
            color: #92400e !important;
            border-color: #fde68a !important;
        }

        body:not(.print-statement-active) .badge-unpaid {
            background: #fef2f2 !important;
            color: #991b1b !important;
            border-color: #fecaca !important;
        }

        body:not(.print-statement-active) .badge-pending {
            background: #eff6ff !important;
            color: #1e40af !important;
            border-color: #bfdbfe !important;
        }

        body:not(.print-statement-active) .badge-waived {
            background: #f1f5f9 !important;
            color: #475569 !important;
            border-color: #cbd5e1 !important;
        }

        /* -------------------------------------------------------------
           الوضع الثاني: طباعة سند كشف حساب وذمة الطالب الفردي
           ------------------------------------------------------------- */
        body.print-statement-active .subs-matrix-wrapper {
            display: none !important;
        }

        body.print-statement-active #statementModal {
            display: block !important;
            position: static !important;
            width: 100% !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: visible !important;
        }

        body.print-statement-active .modal-statement-sheet-wrap {
            max-width: 100% !important;
            width: 100% !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            background: transparent !important;
            overflow: visible !important;
        }

        body.print-statement-active .statement-toolbar,
        body.print-statement-active .btn-close-x {
            display: none !important;
        }

        body.print-statement-active #statementPrintableArea {
            display: block !important;
            width: 100% !important;
            padding: 16px 20px !important;
            margin: 0 !important;
            box-shadow: none !important;
            background: #ffffff !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
    }
</style>
@endsection
