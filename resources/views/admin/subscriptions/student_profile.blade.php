@extends('layouts.app')

@php
    $studentDisplayName = (app()->getLocale() === 'en' && !empty($student->name_en)) ? $student->name_en : ($student->name_ar ?? $student->name);
    $stageDisplayName = (app()->getLocale() === 'en' && !empty($student->stage->name_en)) ? $student->stage->name_en : ($student->stage->label_ar ?? ($student->stage->name_ar ?? __('عام')));
@endphp

@section('title', __('الملف المالي وسجل اشتراكات الطالب') . ' | ' . $studentDisplayName . ' | ' . __(\App\Models\Setting::get('site_name', 'Step by Step')))

@section('content')
<div class="student-profile-finance-wrap">

    {{-- 1. شريط التنقل والترويسة الكلاسيكية العليا --}}
    <div class="top-nav-bar-classic">
        <div class="nav-breadcrumbs">
            <a href="{{ route('admin.dashboard') }}" class="crumb-link"><i class="fa-solid fa-house"></i> {{ __('الرئيسية') }}</a>
            <span class="crumb-sep">/</span>
            <a href="{{ route('admin.subscriptions.monthly', ['year' => $year]) }}" class="crumb-link">{{ __('سجل الاشتراكات والرسوم الفصلية') }}</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">{{ $studentDisplayName }}</span>
        </div>

        <div class="nav-actions-group">
            <a href="{{ route('admin.subscriptions.monthly', ['year' => $year]) }}" class="btn-classic-outline">
                <i class="fa-solid fa-arrow-right"></i>
                <span>{{ __('العودة لسجل الاشتراكات') }}</span>
            </a>

            <button type="button" class="btn-classic-print" onclick="openStatementModal()">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة كشف الذمة المعتمد') }}</span>
            </button>
        </div>
    </div>

    {{-- 2. بطاقة هوية الطالب الأكاديمية والمالية الفاخرة --}}
    <div class="student-hero-classic-card">
        <div class="hero-main-details">
            <div class="avatar-holder">
                <img src="{{ $student->photo_url }}" alt="{{ $studentDisplayName }}" class="student-photo-royal">
                <span class="status-indicator-dot {{ $studentRemaining == 0 ? 'is-clear' : 'has-due' }}"></span>
            </div>

            <div class="student-identity-text">
                <div class="name-badge-row">
                    <h1 class="student-full-title">{{ $studentDisplayName }}</h1>
                    <span class="stage-tag-classic"><i class="fa-solid fa-graduation-cap"></i> {{ $stageDisplayName }}</span>
                    @if($studentRemaining == 0)
                        <span class="clearance-pill-royal"><i class="fa-solid fa-shield-check"></i> {{ __('ذمة مسددة ومبرأة بالكامل') }}</span>
                    @else
                        <span class="due-pill-royal"><i class="fa-solid fa-circle-exclamation"></i> {{ __('يوجد رصيد متبقي بذمة الطالب') }}</span>
                    @endif
                </div>

                <div class="student-meta-strip">
                    <span class="meta-item"><i class="fa-solid fa-id-card text-muted"></i> <strong>{{ __('رقم الهوية:') }}</strong> <span class="font-mono" dir="ltr">{{ $student->nid ?? '-' }}</span></span>
                    @if(!empty($student->plain_password))
                        <span class="meta-item"><i class="fa-solid fa-key text-amber"></i> <strong>{{ __('كلمة المرور:') }}</strong> <code class="font-mono" style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; border: 1px solid #fde68a; font-weight: 800; cursor: pointer;" title="{{ __('انقر لنسخ كلمة المرور') }}" onclick="if(typeof Swal !== 'undefined'){ navigator.clipboard.writeText('{{ $student->plain_password }}'); Swal.fire({toast:true,position:'top-end',icon:'success',title:'{{ __('تم نسخ كلمة المرور') }}',showConfirmButton:false,timer:1500}); } else { alert('{{ __('تم نسخ كلمة المرور') }}'); }">{{ $student->plain_password }}</code></span>
                    @else
                        <span class="meta-item"><i class="fa-solid fa-shield-halved text-muted"></i> <strong>{{ __('كلمة المرور:') }}</strong> <span style="background: #f1f5f9; color: #64748b; padding: 2px 6px; border-radius: 4px; border: 1px dashed #cbd5e1; font-size: 0.78rem; font-weight: 700;">{{ __('مشفرة بأمان') }}</span></span>
                    @endif
                    <span class="meta-item"><i class="fa-solid fa-phone text-muted"></i> <strong>{{ __('هاتف الطالب:') }}</strong> <a href="tel:{{ $student->phone }}" class="phone-link font-mono" dir="ltr">{{ $student->phone ?? '-' }}</a></span>
                    @if($student->guardian_phone)
                        <span class="meta-item"><i class="fa-solid fa-user-shield text-muted"></i> <strong>{{ __('ولي الأمر:') }}</strong> <a href="tel:{{ $student->guardian_phone }}" class="phone-link font-mono" dir="ltr">{{ $student->guardian_phone }}</a></span>
                    @endif
                    <span class="meta-item"><i class="fa-solid fa-location-dot text-muted"></i> <strong>{{ __('المنطقة:') }}</strong> <span>{{ $student->region_label }}</span></span>
                    <span class="meta-item"><i class="fa-solid fa-calendar-days text-muted"></i> <strong>{{ __('العام الدراسي:') }}</strong> <span class="font-mono">{{ $year }}</span></span>
                </div>
            </div>
        </div>

        {{-- تفاصيل خطة الرسوم الفصلية والمنطقة --}}
        <div class="fee-plan-box">
            <div class="fee-plan-header">
                <span class="fee-plan-lbl"><i class="fa-solid fa-file-invoice-dollar text-amber"></i> {{ __('خطة الرسوم الفصلية المعتمدة') }}</span>
                <button type="button" class="btn-edit-fee-mini" onclick="openSemesterPaymentModal({{ $student->id }}, '{{ addslashes($studentDisplayName) }}', 'both', {{ $semesterSummary['total_due'] }}, {{ $semesterSummary['total_paid'] }}, '{{ $studentRemaining == 0 ? "paid" : ($studentPaid > 0 ? "partial" : "unpaid") }}')" title="{{ __('تسديد / تحديث الرسوم') }}">
                    <i class="fa-solid fa-coins"></i> {{ __('سداد / تعديل') }}
                </button>
            </div>
            <div class="fee-plan-body">
                <div class="fee-val-row">
                    <span class="text-muted">{{ __('المنطقة والتسعيرة:') }}</span>
                    <strong>{{ $student->region_label }}</strong>
                </div>
                <div class="fee-val-row">
                    <span class="text-muted">{{ __('المواد المقيدة:') }}</span>
                    <strong class="font-mono">{{ $semesterSubscriptions->count() }} {{ __('مواد') }}</strong>
                </div>
                <div class="fee-val-row net-due-row">
                    <span>{{ __('إجمالي المقرر (فصلين):') }}</span>
                    <strong class="font-mono text-emerald" id="student_fee_label_hero">{{ number_format($studentDue, 2) }} ₪</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. بطاقات المؤشرات المالية الأربعة الكلاسيكية للطالب --}}
    <div class="financial-kpi-grid-classic">
        {{-- كرت المستحق المطلوب --}}
        <div class="kpi-card-classic kpi-due">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">{{ __('إجمالي الرسوم المقررة للفصلين') }}</span>
                <div class="kpi-num-wrap font-mono" id="hero_total_due">{{ number_format($studentDue, 2) }} ₪</div>
                <small class="kpi-sub-text">{{ __('إجمالي رسوم الفصل الأول + الفصل الثاني') }}</small>
            </div>
        </div>

        {{-- كرت المسدد المعتمد --}}
        <div class="kpi-card-classic kpi-paid">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">{{ __('إجمالي المبلغ المسدد المعتمد') }}</span>
                <div class="kpi-num-wrap font-mono text-emerald" id="hero_total_paid">{{ number_format($studentPaid, 2) }} ₪</div>
                <small class="kpi-sub-text">{{ __('المبالغ المقبوضة فعلياً في خزينة المنصة') }}</small>
            </div>
        </div>

        {{-- كرت الرصيد المتبقي --}}
        <div class="kpi-card-classic kpi-remaining {{ $studentRemaining > 0 ? 'is-alert' : 'is-safe' }}">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">{{ __('الرصيد المتبقي بذمة الطالب') }}</span>
                <div class="kpi-num-wrap font-mono {{ $studentRemaining > 0 ? 'text-rose' : 'text-emerald' }}" id="hero_total_remaining">
                    {{ number_format($studentRemaining, 2) }} ₪
                </div>
                <small class="kpi-sub-text">
                    @if($studentRemaining > 0)
                        {{ __('مستحق للسداد بموجب الرسوم الفصلية') }} ⚠️
                    @else
                        {{ __('ذمة مالية بريئة ومسددة 100%') }} ✅
                    @endif
                </small>
            </div>
        </div>

        {{-- كرت نسبة الإنجاز وحالة الأقساط --}}
        <div class="kpi-card-classic kpi-rate">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">{{ __('نسبة السداد والتحصيل الفصلي') }}</span>
                <div class="kpi-num-wrap font-mono" id="hero_installments_count">
                    {{ $collectionRate }}%
                </div>
                <div class="progress-bar-classic">
                    <div class="progress-fill-classic" style="width: {{ $collectionRate }}%;"></div>
                </div>
                <small class="kpi-sub-text font-mono">{{ __('المواد المسددة بالكامل:') }} {{ $paidCount }} / {{ $semesterSubscriptions->count() }}</small>
            </div>
        </div>
    </div>

    {{-- كشف الحساب والذمة اللحظية المتأخرة والمستحقة الآن --}}
    @if($studentRemaining > 0)
        <div class="financial-arrears-alert-card has-arrears-alert">
            <div class="alert-content-left">
                <div class="alert-icon-royal">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="alert-text-royal">
                    <h3 class="alert-royal-title">{{ __('ذمة مالية مستحقة التحصيل بذمة الطالب') }}</h3>
                    <p class="alert-royal-desc">
                        {{ __('يوجد بذمة الطالب رصيد متبقي غير مسدد بقيمة') }} 
                        <strong class="font-mono text-danger">{{ number_format($studentRemaining, 2) }} ₪</strong>
                        @if(($semesterSummary['term_1_remaining'] ?? 0) > 0)
                            <span class="arrears-month-pill">{{ __('الفصل الأول:') }} {{ number_format($semesterSummary['term_1_remaining'], 2) }} ₪</span>
                        @endif
                        @if(($semesterSummary['term_2_remaining'] ?? 0) > 0)
                            <span class="arrears-month-pill">{{ __('الفصل الثاني:') }} {{ number_format($semesterSummary['term_2_remaining'], 2) }} ₪</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="alert-action-right">
                <div class="due-now-badge-box">
                    <span class="badge-title">{{ __('إجمالي المتبقي المطلوب') }}</span>
                    <strong class="badge-amt font-mono">{{ number_format($studentRemaining, 2) }} ₪</strong>
                </div>
            </div>
        </div>
    @endif

    {{-- 4. لوحة التحكم والتحكم الفصلي باستحقاقات وسداد الفصول الدراسية --}}
    <div class="months-control-section-classic">
        <div class="section-classic-header">
            <div class="header-titles">
                <h2 class="sec-title"><i class="fa-solid fa-calendar-check text-primary"></i> {{ __('سجل استحقاقات وسداد الفصول الدراسية') }}</h2>
                <p class="sec-desc">{{ __('يمكنك استعراض ومراجعة بيانات كل فصل دراسي، تسجيل دفع كامل أو جزئي، أو منح إعفاء فوري.') }}</p>
            </div>

            {{-- التبديل السريع بين الطلاب --}}
            <div class="student-fast-navigator">
                <span class="nav-title-lbl">{{ __('التنقل بين الطلاب:') }}</span>
                @if($prevStudent)
                    <a href="{{ route('admin.subscriptions.student', ['student' => $prevStudent->id, 'year' => $year]) }}" class="btn-nav-step" title="{{ $prevStudent->name_ar ?? $prevStudent->name }}">
                        <i class="fa-solid fa-chevron-right"></i> {{ __('السابق') }}
                    </a>
                @endif

                <select class="select-fast-student" onchange="if(this.value) window.location.href=this.value;">
                    <option value="">-- {{ __('اختر طالباً آخر') }} --</option>
                    @foreach($allStageStudents as $st)
                        <option value="{{ route('admin.subscriptions.student', ['student' => $st->id, 'year' => $year]) }}" {{ $st->id == $student->id ? 'selected' : '' }}>
                            {{ $st->name_ar ?? $st->name_en }} ({{ $st->nid ?? '-' }})
                        </option>
                    @endforeach
                </select>

                @if($nextStudent)
                    <a href="{{ route('admin.subscriptions.student', ['student' => $nextStudent->id, 'year' => $year]) }}" class="btn-nav-step" title="{{ $nextStudent->name_ar ?? $nextStudent->name }}">
                        {{ __('التالي') }} <i class="fa-solid fa-chevron-left"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- بطاقات الفصول الدراسية الكبرى (الفصل الأول + الفصل الثاني + تسديد العام كاملاً) --}}
        <div class="semester-cards-grid-royal">
            {{-- كرت الفصل الدراسي الأول --}}
            <div class="semester-card-royal status-border-{{ $semesterSummary['term_1_status'] ?? 'unpaid' }}">
                <div class="sem-card-top">
                    <div class="sem-badge-icon blue">
                        <i class="fa-solid fa-1"></i>
                    </div>
                    <div>
                        <h3 class="sem-title">{{ __('الفصل الدراسي الأول (Term 1)') }}</h3>
                        <small class="sem-subtitle">{{ $semesterSummary['term_1_count'] }} {{ __('مواد مقيدة') }}</small>
                    </div>
                    <span class="status-pill-royal st-{{ $semesterSummary['term_1_status'] ?? 'unpaid' }}">
                        @if(($semesterSummary['term_1_status'] ?? '') === 'paid')
                            <i class="fa-solid fa-circle-check"></i> {{ __('مسدد بالكامل') }}
                        @elseif(($semesterSummary['term_1_status'] ?? '') === 'partial')
                            <i class="fa-solid fa-circle-half-stroke"></i> {{ __('سداد جزئي') }}
                        @elseif(($semesterSummary['term_1_status'] ?? '') === 'waived')
                            <i class="fa-solid fa-tag"></i> {{ __('إعفاء / منحة') }}
                        @else
                            <i class="fa-solid fa-circle-xmark"></i> {{ __('غير مسدد') }}
                        @endif
                    </span>
                </div>

                <div class="sem-figures-strip">
                    <div class="sem-fig">
                        <span class="lbl">{{ __('المقرر:') }}</span>
                        <strong class="font-mono">{{ number_format($semesterSummary['term_1_due'] ?? 0, 2) }} ₪</strong>
                    </div>
                    <div class="sem-fig">
                        <span class="lbl">{{ __('المسدد:') }}</span>
                        <strong class="font-mono text-emerald">{{ number_format($semesterSummary['term_1_paid'] ?? 0, 2) }} ₪</strong>
                    </div>
                    <div class="sem-fig">
                        <span class="lbl">{{ __('المتبقي:') }}</span>
                        <strong class="font-mono {{ ($semesterSummary['term_1_remaining'] ?? 0) > 0 ? 'text-rose font-bold' : 'text-emerald' }}">
                            {{ number_format($semesterSummary['term_1_remaining'] ?? 0, 2) }} ₪
                        </strong>
                    </div>
                </div>

                <div class="sem-actions-row">
                    <button type="button" class="btn-sem-action btn-blue" onclick="openSemesterPaymentModal({{ $student->id }}, '{{ addslashes($studentDisplayName) }}', 'term_1', {{ $semesterSummary['term_1_due'] }}, {{ $semesterSummary['term_1_paid'] }}, '{{ $semesterSummary['term_1_status'] }}')">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>{{ __('سداد / تعديل الفصل الأول') }}</span>
                    </button>
                </div>
            </div>

            {{-- كرت الفصل الدراسي الثاني --}}
            <div class="semester-card-royal status-border-{{ $semesterSummary['term_2_status'] ?? 'unpaid' }}">
                <div class="sem-card-top">
                    <div class="sem-badge-icon purple">
                        <i class="fa-solid fa-2"></i>
                    </div>
                    <div>
                        <h3 class="sem-title">{{ __('الفصل الدراسي الثاني (Term 2)') }}</h3>
                        <small class="sem-subtitle">{{ $semesterSummary['term_2_count'] }} {{ __('مواد مقيدة') }}</small>
                    </div>
                    <span class="status-pill-royal st-{{ $semesterSummary['term_2_status'] ?? 'unpaid' }}">
                        @if(($semesterSummary['term_2_status'] ?? '') === 'paid')
                            <i class="fa-solid fa-circle-check"></i> {{ __('مسدد بالكامل') }}
                        @elseif(($semesterSummary['term_2_status'] ?? '') === 'partial')
                            <i class="fa-solid fa-circle-half-stroke"></i> {{ __('سداد جزئي') }}
                        @elseif(($semesterSummary['term_2_status'] ?? '') === 'waived')
                            <i class="fa-solid fa-tag"></i> {{ __('إعفاء / منحة') }}
                        @else
                            <i class="fa-solid fa-circle-xmark"></i> {{ __('غير مسدد') }}
                        @endif
                    </span>
                </div>

                <div class="sem-figures-strip">
                    <div class="sem-fig">
                        <span class="lbl">{{ __('المقرر:') }}</span>
                        <strong class="font-mono">{{ number_format($semesterSummary['term_2_due'] ?? 0, 2) }} ₪</strong>
                    </div>
                    <div class="sem-fig">
                        <span class="lbl">{{ __('المسدد:') }}</span>
                        <strong class="font-mono text-emerald">{{ number_format($semesterSummary['term_2_paid'] ?? 0, 2) }} ₪</strong>
                    </div>
                    <div class="sem-fig">
                        <span class="lbl">{{ __('المتبقي:') }}</span>
                        <strong class="font-mono {{ ($semesterSummary['term_2_remaining'] ?? 0) > 0 ? 'text-rose font-bold' : 'text-emerald' }}">
                            {{ number_format($semesterSummary['term_2_remaining'] ?? 0, 2) }} ₪
                        </strong>
                    </div>
                </div>

                <div class="sem-actions-row">
                    <button type="button" class="btn-sem-action btn-purple" onclick="openSemesterPaymentModal({{ $student->id }}, '{{ addslashes($studentDisplayName) }}', 'term_2', {{ $semesterSummary['term_2_due'] }}, {{ $semesterSummary['term_2_paid'] }}, '{{ $semesterSummary['term_2_status'] }}')">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>{{ __('سداد / تعديل الفصل الثاني') }}</span>
                    </button>
                </div>
            </div>

            {{-- كرت سداد العام كاملاً --}}
            <div class="semester-card-royal status-border-both">
                <div class="sem-card-top">
                    <div class="sem-badge-icon amber">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="sem-title">{{ __('سداد العام كاملاً (الفصلين معاً)') }}</h3>
                        <small class="sem-subtitle">{{ __('تسجيل دفعة إجمالية لكلا الفصلين') }}</small>
                    </div>
                    <span class="status-pill-royal {{ $studentRemaining == 0 ? 'st-paid' : ($studentPaid > 0 ? 'st-partial' : 'st-unpaid') }}">
                        @if($studentRemaining == 0)
                            <i class="fa-solid fa-circle-check"></i> {{ __('مسدد 100%') }}
                        @elseif($studentPaid > 0)
                            <i class="fa-solid fa-circle-half-stroke"></i> {{ __('سداد جزئي') }}
                        @else
                            <i class="fa-solid fa-circle-xmark"></i> {{ __('غير مسدد') }}
                        @endif
                    </span>
                </div>

                <div class="sem-figures-strip">
                    <div class="sem-fig">
                        <span class="lbl">{{ __('إجمالي المطلوب:') }}</span>
                        <strong class="font-mono">{{ number_format($studentDue, 2) }} ₪</strong>
                    </div>
                    <div class="sem-fig">
                        <span class="lbl">{{ __('المسدد:') }}</span>
                        <strong class="font-mono text-emerald">{{ number_format($studentPaid, 2) }} ₪</strong>
                    </div>
                    <div class="sem-fig">
                        <span class="lbl">{{ __('المتبقي:') }}</span>
                        <strong class="font-mono {{ $studentRemaining > 0 ? 'text-rose font-bold' : 'text-emerald' }}">
                            {{ number_format($studentRemaining, 2) }} ₪
                        </strong>
                    </div>
                </div>

                <div class="sem-actions-row">
                    <button type="button" class="btn-sem-action btn-amber" onclick="openSemesterPaymentModal({{ $student->id }}, '{{ addslashes($studentDisplayName) }}', 'both', {{ $semesterSummary['total_due'] }}, {{ $semesterSummary['total_paid'] }}, '{{ $studentRemaining == 0 ? "paid" : ($studentPaid > 0 ? "partial" : "unpaid") }}')">
                        <i class="fa-solid fa-receipt"></i>
                        <span>{{ __('سداد العام كاملاً (الفصلين)') }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- جدول المواد الدراسية المقيدة والرسوم الفصلية --}}
        <div class="registered-subjects-section-box">
            <div class="subs-sec-header">
                <h3><i class="fa-solid fa-book-open-reader text-primary"></i> {{ __('المواد الدراسية المقيدة والاشتراكات الفصلية للطالب') }}</h3>
                <span class="badge-count">{{ $semesterSubscriptions->count() }} {{ __('مواد مقيدة') }}</span>
            </div>

            <div class="table-responsive" style="overflow-x: auto; width: 100%; max-width: 100%;">
                <table class="classic-subjects-table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th>{{ __('المادة الدراسية') }}</th>
                            <th>{{ __('الفصل الدراسي المسجل') }}</th>
                            <th>{{ __('الرسوم المقررة') }}</th>
                            <th>{{ __('المسدد فعلياً') }}</th>
                            <th>{{ __('الرصيد المتبقي') }}</th>
                            <th>{{ __('الحالة المعتمدة') }}</th>
                            <th>{{ __('الملاحظات وبيان السداد') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semesterSubscriptions as $index => $sub)
                            @php
                                $sDue = (float)$sub->amount;
                                $sPaid = ($sub->status === 'waived') ? 0.00 : (($sub->status === 'paid' && ((float)($sub->paid_amount ?? 0) <= 0)) ? $sDue : (float)($sub->paid_amount ?? 0));
                                $sRem = ($sub->status === 'waived') ? 0.00 : max(0.00, round($sDue - $sPaid, 2));
                            @endphp
                            <tr>
                                <td class="font-mono text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $sub->subject->name ?? __('مادة تعليمية') }}</strong>
                                </td>
                                <td>
                                    @if($sub->semester === 'term_1')
                                        <span class="badge-sem term-1"><i class="fa-solid fa-calendar-check"></i> {{ __('الفصل الأول') }}</span>
                                    @elseif($sub->semester === 'term_2')
                                        <span class="badge-sem term-2"><i class="fa-solid fa-calendar-days"></i> {{ __('الفصل الثاني') }}</span>
                                    @else
                                        <span class="badge-sem term-both"><i class="fa-solid fa-layer-group"></i> {{ __('كلا الفصلين') }}</span>
                                    @endif
                                </td>
                                <td class="font-mono text-center"><strong>{{ number_format($sDue, 2) }} ₪</strong></td>
                                <td class="font-mono text-center text-emerald"><strong>{{ number_format($sPaid, 2) }} ₪</strong></td>
                                <td class="font-mono text-center {{ $sRem > 0 ? 'text-rose font-bold' : 'text-emerald' }}">{{ number_format($sRem, 2) }} ₪</td>
                                <td class="text-center">
                                    <span class="status-pill-royal st-{{ $sub->status }}">
                                        {{ $sub->status_badge['label'] ?? $sub->status }}
                                    </span>
                                </td>
                                <td style="font-size: 0.82rem; color: #475569;">
                                    {{ $sub->notes ?: '-' }}
                                    @if($sub->paid_at)
                                        <div style="font-size: 0.72rem; color: #64748b;">
                                            <i class="fa-regular fa-clock"></i> {{ $sub->paid_at->format('Y-m-d') }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 30px; color: #94a3b8;">
                                    <i class="fa-solid fa-inbox" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                                    {{ __('لا توجد مواد مقيدة للطالب حتى الآن.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-totals-row">
                            <td colspan="3" class="text-left font-bold">{{ __('الإجمالي الفصلي العام:') }}</td>
                            <td class="font-mono text-center font-bold">{{ number_format($studentDue, 2) }} ₪</td>
                            <td class="font-mono text-center font-bold text-emerald">{{ number_format($studentPaid, 2) }} ₪</td>
                            <td class="font-mono text-center font-bold {{ $studentRemaining > 0 ? 'text-rose' : 'text-emerald' }}">{{ number_format($studentRemaining, 2) }} ₪</td>
                            <td colspan="2" class="text-center font-bold">
                                @if($studentRemaining <= 0)
                                    <span class="text-emerald">{{ __('مبرأة الذمة 100%') }} ✅</span>
                                @else
                                    <span class="text-rose">{{ __('متبقي بذمة الطالب') }} ⚠️</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- 5. نافذة (مودال) سداد وتعديل الرسوم الفصلية للطالب --}}
<div id="editSemesterModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box modal-royal-theme">
        <div class="modal-header-royal">
            <div class="modal-title-wrap">
                <div class="modal-crest">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h3 id="modalStudentNameTitle" class="modal-student-name">{{ __('تحديث الرسوم الفصلية والمبالغ') }}</h3>
                    <p id="modalSemesterSubtitle" class="modal-month-desc">{{ __('الفصل الدراسي') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close-x" onclick="closeSemesterPaymentModal()">&times;</button>
        </div>

        <form id="updateSemesterForm" onsubmit="saveSemesterSubscription(event)">
            @csrf
            <input type="hidden" name="student_id" id="formStudentId" value="{{ $student->id }}">
            <input type="hidden" name="academic_year" value="{{ $year }}">

            <div class="form-body-wrap">
                {{-- اختيار الفصل الدراسي المستهدف --}}
                <div class="form-field-group">
                    <label class="field-label-royal">{{ __('الفصل الدراسي المستهدف للسداد / التعديل') }} <span class="required">*</span></label>
                    <div class="semester-radio-group">
                        <label class="sem-radio-pill">
                            <input type="radio" name="semester" value="term_1" id="radioSemTerm1" onchange="onSemesterRadioChange()">
                            <span><i class="fa-solid fa-calendar-check text-blue"></i> {{ __('الفصل الأول') }}</span>
                        </label>
                        <label class="sem-radio-pill">
                            <input type="radio" name="semester" value="term_2" id="radioSemTerm2" onchange="onSemesterRadioChange()">
                            <span><i class="fa-solid fa-calendar-days text-indigo"></i> {{ __('الفصل الثاني') }}</span>
                        </label>
                        <label class="sem-radio-pill">
                            <input type="radio" name="semester" value="both" id="radioSemBoth" onchange="onSemesterRadioChange()">
                            <span><i class="fa-solid fa-layer-group text-amber"></i> {{ __('كلا الفصلين (العام)') }}</span>
                        </label>
                    </div>
                </div>

                {{-- أزرار سريعة للحالة --}}
                <div class="form-field-group">
                    <label class="field-label-royal">{{ __('حالة سداد الرسوم') }} <span class="required">*</span></label>
                    <div class="status-options-grid">
                        <label class="status-option-label opt-paid">
                            <input type="radio" name="status" value="paid" id="optStatusPaid" onchange="onStatusRadioChange('paid')">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-check"></i>
                                <strong>{{ __('مسدد بالكامل') }}</strong>
                                <small>{{ __('تم سداد كامل الرسوم') }}</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-partial">
                            <input type="radio" name="status" value="partial" id="optStatusPartial" onchange="onStatusRadioChange('partial')">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-half-stroke"></i>
                                <strong>{{ __('سداد جزئي') }}</strong>
                                <small>{{ __('دفعة مع بقاء رصيد') }}</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-unpaid">
                            <input type="radio" name="status" value="unpaid" id="optStatusUnpaid" onchange="onStatusRadioChange('unpaid')">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <strong>{{ __('غير مسدد') }}</strong>
                                <small>{{ __('رسوم مستحقة متأخرة') }}</small>
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

                {{-- شبكة المبالغ --}}
                <div class="amounts-calc-grid">
                    <div class="form-field-group">
                        <label class="field-label-royal">{{ __('إجمالي الرسوم المقررة للفصل (₪)') }}</label>
                        <input type="number" step="0.01" min="0" id="formSemAmount" class="clean-input font-mono font-bold" readonly style="background: #f1f5f9;">
                        <small class="field-hint">{{ __('محسوب تلقائياً حسب المواد والمنطقة') }}</small>
                    </div>

                    <div class="form-field-group">
                        <label class="field-label-royal">{{ __('المبلغ المسدد فعلياً (₪)') }} <span class="required">*</span></label>
                        <input type="number" step="0.01" min="0" name="paid_amount" id="formPaidAmount" class="clean-input font-mono font-bold text-emerald" required oninput="calcRemainingLive()">
                        <small class="field-hint">{{ __('المبلغ المقبوض من الطالب فعلياً') }}</small>
                    </div>
                </div>

                {{-- أزرار سريعة للمبالغ --}}
                <div class="presets-row">
                    <span class="presets-label">{{ __('خيارات سريعة:') }}</span>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('full')">{{ __('سداد كامل 100%') }}</button>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('half')">{{ __('سداد 50%') }}</button>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('zero')">{{ __('غير مسدد (0 ₪)') }}</button>
                </div>

                {{-- حاسبة حية للمتبقي --}}
                <div class="live-calc-box">
                    <div class="calc-label-row">
                        <span class="calc-text">{{ __('الرصيد المتبقي بذمة الطالب:') }}</span>
                        <strong class="font-mono remaining-display" id="formRemainingPreview">0.00 ₪</strong>
                    </div>
                    <div class="calc-status-indicator" id="formStatusNotice">
                        <i class="fa-solid fa-circle-check"></i> <span>{{ __('مسدد بالكامل رسمياً') }}</span>
                    </div>
                </div>

                <div class="form-field-group">
                    <label class="field-label-royal">{{ __('ملاحظات وبيان الدفعة (تظهر في السند)') }}</label>
                    <input type="text" name="notes" id="formNotes" class="clean-input" placeholder="{{ __('مثال: إشعار سداد بنكي رقم... دفعة نقدية معتمدة') }}">
                </div>
            </div>

            <div class="modal-footer-royal">
                <button type="button" class="btn-cancel-sub" onclick="closeSemesterPaymentModal()">{{ __('إلغاء') }}</button>
                <button type="submit" class="btn-save-sub" id="btnSaveSub">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('حفظ واعتماد التحديث') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 6. نافذة تعديل رسوم الطالب والخصومات المعتمدة --}}
<div id="studentFeeModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box modal-fee-box">
        <div class="modal-header-royal">
            <div class="modal-header-info">
                <div class="modal-avatar-badge text-amber">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <h3 class="modal-student-name">{{ $studentDisplayName }}</h3>
                    <p class="modal-month-subtitle">{{ __('تعديل خطة القسط الشهري والخصومات المعتمدة') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close-x" onclick="closeStudentFeeModal()">&times;</button>
        </div>

        <form id="studentFeeForm" onsubmit="submitStudentFeeForm(event)">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <input type="hidden" name="academic_year" value="{{ $year }}">

            <div class="form-body-wrap">
                <div class="form-field-group">
                    <label class="field-label-royal">{{ __('القسط الشهري الأساسي للطالب (₪) *') }}</label>
                    <div class="input-with-currency">
                        <input type="number" step="1" min="0" name="monthly_fee" id="feeInputMonthly" class="clean-input" value="{{ (float)($student->monthly_fee ?: 150) }}" required oninput="calcNetFeePreview()">
                        <span class="curr-tag">₪</span>
                    </div>
                </div>

                <div class="amounts-calc-grid">
                    <div class="form-field-group">
                        <label class="field-label-royal">{{ __('نسبة الخصم المئوية (%)') }}</label>
                        <input type="number" step="0.5" min="0" max="100" name="custom_discount_percent" id="feeInputPercent" class="clean-input" value="{{ (float)($student->custom_discount_percent ?: 0) }}" oninput="calcNetFeePreview()">
                    </div>

                    <div class="form-field-group">
                        <label class="field-label-royal">{{ __('خصم مبلغ مقطوع (₪)') }}</label>
                        <input type="number" step="1" min="0" name="custom_discount_fixed" id="feeInputFixed" class="clean-input" value="{{ (float)($student->custom_discount_fixed ?: 0) }}" oninput="calcNetFeePreview()">
                    </div>
                </div>

                <div class="live-calc-box">
                    <div class="calc-label-row">
                        <span>{{ __('القسط الشهري الصافي بعد الخصم:') }}</span>
                        <strong class="font-mono remaining-display text-emerald" id="feeNetPreview">150 ₪/شهر</strong>
                    </div>
                </div>

                <div class="form-field-group">
                    <label class="field-label-royal">{{ __('سبب الخصم أو المنحة (ملاحظات إدارية)') }}</label>
                    <input type="text" name="discount_notes" id="feeInputNotes" class="clean-input" value="{{ $student->discount_notes ?? '' }}" placeholder="{{ __('مثال: منحة تفوق، إعفاء أبناء شهداء، خصم إخوة...') }}">
                </div>

                <div class="checkbox-box-royal">
                    <label class="custom-chk-label">
                        <input type="checkbox" name="apply_to_future_months" value="1" checked>
                        <span>{{ __('تحديث الشهور غير المسددة المتبقية تلقائياً بهذا القسط الجديد') }}</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer-royal">
                <button type="button" class="btn-cancel-sub" onclick="closeStudentFeeModal()">{{ __('إلغاء') }}</button>
                <button type="submit" class="btn-save-sub" id="btnSaveFee">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('حفظ وتطبيق الخطة المالية') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 7. نافذة طباعة سند كشف الذمة المعتمد (Statement Modal) --}}
<div id="statementModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box modal-statement-sheet-wrap">
        <div class="statement-toolbar">
            <div class="tb-left">
                <button type="button" class="btn-print-action" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> {{ __('طباعة السند الرسمي') }}
                </button>
            </div>
            <button type="button" class="btn-close-x" onclick="closeStatementModal()">&times;</button>
        </div>

        <div id="statementPrintableArea" class="statement-document">
            {{-- الترويسة الوزارية الرسمية لسند كشف الحساب --}}
            <div class="doc-header">
                <div class="doc-header-col text-right">
                    <strong>{{ __('دولة فلسطين') }} 🇵🇸</strong>
                    <span>{{ __('منظومة التعليم الأكاديمي المعتمدة') }}</span>
                    <span>{{ __(\App\Models\Setting::get('site_name', 'Step by Step')) }}</span>
                </div>
                <div class="doc-header-logo">
                    @php
                        $directorLogo = \App\Models\Setting::get('director_logo');
                        $siteLogo = \App\Models\Setting::get('site_logo');
                        $fallbackLogo = asset('images/logo.png');
                        $sheetLogo = !empty($directorLogo) ? asset($directorLogo) : (!empty($siteLogo) ? asset($siteLogo) : $fallbackLogo);
                    @endphp
                    <img src="{{ $sheetLogo }}" alt="Logo" class="doc-logo-img" onerror="this.onerror=null; this.src='{{ $fallbackLogo }}';">
                    <h2 class="doc-main-title">{{ __('سند كشف حساب وذمة مالية') }}</h2>
                    <span class="doc-badge-year">{{ __('العام الدراسي') }} {{ $year }}</span>
                </div>
                <div class="doc-header-col text-left font-mono">
                    <span><strong>{{ __('تاريخ الاستخراج:') }}</strong> {{ date('Y-m-d') }}</span>
                    <span><strong>{{ __('رقم السند:') }}</strong> PAL-STMT-{{ $student->id }}-{{ date('Ym') }}</span>
                    <span><strong>{{ __('المشرف العام:') }}</strong> م. أحمد شمالي</span>
                </div>
            </div>

            <hr class="doc-divider">

            {{-- بيانات الطالب --}}
            <div class="doc-student-info-grid">
                <div class="info-cell"><span>{{ __('اسم الطالب:') }}</span> <strong>{{ $studentDisplayName }}</strong></div>
                <div class="info-cell"><span>{{ __('رقم الهوية:') }}</span> <strong class="font-mono" dir="ltr">{{ $student->nid ?? '-' }}</strong></div>
                <div class="info-cell"><span>{{ __('المرحلة والفرع:') }}</span> <strong>{{ $stageDisplayName }}</strong></div>
                <div class="info-cell"><span>{{ __('رقم الهاتف:') }}</span> <strong class="font-mono" dir="ltr">{{ $student->phone ?? '-' }}</strong></div>
            </div>

            {{-- جدول المواد والرسوم الفصلية المعتمد --}}
            <div class="table-responsive" style="overflow-x: auto; width: 100%; max-width: 100%;">
            <table class="doc-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">#</th>
                        <th>{{ __('المادة الدراسية') }}</th>
                        <th>{{ __('الفصل الدراسي') }}</th>
                        <th>{{ __('المبلغ المقرر (₪)') }}</th>
                        <th>{{ __('المبلغ المسدد (₪)') }}</th>
                        <th>{{ __('الرصيد المتبقي (₪)') }}</th>
                        <th>{{ __('الحالة المعتمدة') }}</th>
                        <th>{{ __('البيان والملاحظات') }}</th>
                    </tr>
                </thead>
                <tbody id="statementTableBody">
                    @forelse($semesterSubscriptions as $idx => $sub)
                        @php
                            $sAmt = (float)$sub->amount;
                            $sPaidAmt = ($sub->status === 'waived') ? 0.00 : (($sub->status === 'paid' && ((float)($sub->paid_amount ?? 0) <= 0)) ? $sAmt : (float)($sub->paid_amount ?? 0));
                            $sRemAmt = ($sub->status === 'waived') ? 0.00 : max(0.00, round($sAmt - $sPaidAmt, 2));
                            $notes = $sub->notes ?: '-';
                        @endphp
                        <tr>
                            <td class="font-mono text-center">{{ $idx + 1 }}</td>
                            <td><strong>{{ $sub->subject->name ?? __('مادة تعليمية') }}</strong></td>
                            <td class="text-center">
                                @if($sub->semester === 'term_1')
                                    <span>{{ __('الفصل الأول') }}</span>
                                @elseif($sub->semester === 'term_2')
                                    <span>{{ __('الفصل الثاني') }}</span>
                                @else
                                    <span>{{ __('كلا الفصلين') }}</span>
                                @endif
                            </td>
                            <td class="font-mono text-center">{{ number_format($sAmt, 2) }} ₪</td>
                            <td class="font-mono text-center text-emerald"><strong>{{ number_format($sPaidAmt, 2) }} ₪</strong></td>
                            <td class="font-mono text-center {{ $sRemAmt > 0 ? 'text-rose font-bold' : 'text-emerald' }}">{{ number_format($sRemAmt, 2) }} ₪</td>
                            <td class="text-center">
                                @if($sub->status === 'paid')
                                    <span class="sheet-status bg-p">{{ __('مسدد بالكامل') }} ✅</span>
                                @elseif($sub->status === 'partial')
                                    <span class="sheet-status bg-part">{{ __('سداد جزئي') }} ⚠️</span>
                                @elseif($sub->status === 'pending')
                                    <span class="sheet-status bg-pend">{{ __('قيد المراجعة') }} ⏳</span>
                                @elseif($sub->status === 'waived')
                                    <span class="sheet-status bg-w">{{ __('إعفاء / منحة') }} 🏷️</span>
                                @else
                                    <span class="sheet-status bg-u">{{ __('غير مسدد') }} ❌</span>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem; color: #475569;">{{ $notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 20px;">{{ __('لا توجد مواد مقيدة للطالب.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="doc-totals-row">
                        <td colspan="3" class="text-left font-bold">{{ __('الإجماليات الرسمية للفصلين:') }}</td>
                        <td class="font-mono text-center font-bold" id="docTotalDue">{{ number_format($studentDue, 2) }} ₪</td>
                        <td class="font-mono text-center font-bold text-emerald" id="docTotalPaid">{{ number_format($studentPaid, 2) }} ₪</td>
                        <td class="font-mono text-center font-bold {{ $studentRemaining > 0 ? 'text-rose' : 'text-emerald' }}" id="docTotalRem">{{ number_format($studentRemaining, 2) }} ₪</td>
                        <td colspan="2" class="text-center font-bold">
                            @if($studentRemaining <= 0)
                                <span class="text-emerald">{{ __('مبرأة الذمة بالكامل 100%') }} ✅</span>
                            @else
                                <span class="text-rose">{{ __('متبقي بذمة الطالب') }} ⚠️</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
            </div>

            {{-- إقرار براءة الذمة وتوقيع الإدارة --}}
            <div class="doc-footer-clearance">
                <div class="clearance-notice-box">
                    <strong>{{ __('إشعار الاعتماد المالي:') }}</strong>
                    @if($studentRemaining <= 0)
                        <span>{{ __('يشهد قسم الشؤون المالية والقبول في المنصة بأن الطالب المذكور أعلاه قد أوفى بكامل التزاماته المالية عن العام الدراسي (:year)، وتعتبر ذمته المالية مبرأة ومسددة بالكامل بنسبة 100% عن كافة الفصول والمواد المقررة.', ['year' => $year]) }}</span>
                    @else
                        <span>{{ __('يفيد هذا الكشف بوجود رصيد متبقي بذمة الطالب المذكور أعلاه وقدره (:rem ₪)، ويتوجب سداد الرسوم الفصلية المتبقية وفقاً لتعليمات قسم الشؤون المالية والاشتراكات.', ['rem' => number_format($studentRemaining, 2)]) }}</span>
                    @endif
                </div>

                <div class="doc-signatures-row">
                    <div class="sig-block">
                        <span>{{ __('توقيع قسم الحسابات والمالية') }}</span>
                        <div class="sig-space"></div>
                    </div>
                    <div class="doc-stamp-box">
                        <div class="stamp-circle">
                            <span>{{ __('Step by Step') }}</span>
                            <small>{{ __('معتمد رسمياً') }}</small>
                            <i class="fa-solid fa-stamp"></i>
                        </div>
                    </div>
                    <div class="sig-block">
                        <span>{{ __('المشرف العام') }}</span>
                        <strong style="color: #0f172a; margin-top: 4px; display: block;">م. أحمد شمالي</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   التصميم الكلاسيكي الفاخر للواجهة المالية المستقلة للطالب
   ========================================================================== */
.student-profile-finance-wrap {
    padding: 24px 28px 60px;
    max-width: 1400px;
    margin: 0 auto;
    font-family: 'Outfit', 'Cairo', -apple-system, BlinkMacSystemFont, sans-serif;
    color: #1e293b;
}

/* 1. شريط التنقل الكلاسيكي */
.top-nav-bar-classic {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
.nav-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.88rem;
    font-weight: 600;
}
.crumb-link {
    color: #64748b;
    text-decoration: none;
    transition: color 0.2s;
}
.crumb-link:hover {
    color: #1e3a8a;
}
.crumb-sep {
    color: #cbd5e1;
}
.crumb-current {
    color: #0f172a;
    font-weight: 700;
}
.nav-actions-group {
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn-classic-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #334155;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-classic-outline:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.btn-classic-print {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #1e3a8a;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-classic-print:hover {
    background: #1e40af;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
}

/* 2. بطاقة الطالب الرئيسية الكلاسيكية */
.student-hero-classic-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 24px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.hero-main-details {
    display: flex;
    align-items: center;
    gap: 20px;
}
.avatar-holder {
    position: relative;
    flex-shrink: 0;
}
.student-photo-royal {
    width: 84px;
    height: 84px;
    border-radius: 20px;
    object-fit: cover;
    border: 3px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
.status-indicator-dot {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid #ffffff;
}
.status-indicator-dot.is-clear { background: #10b981; }
.status-indicator-dot.has-due { background: #f59e0b; }

.student-identity-text {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.name-badge-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.student-full-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}
.stage-tag-classic {
    background: #eff6ff;
    color: #1e40af;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid #dbeafe;
}
.clearance-pill-royal {
    background: #ecfdf5;
    color: #065f46;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid #a7f3d0;
}
.due-pill-royal {
    background: #fffbeb;
    color: #92400e;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid #fde68a;
}
.student-meta-strip {
    display: flex;
    align-items: center;
    gap: 18px;
    font-size: 0.84rem;
    color: #475569;
    flex-wrap: wrap;
}
.phone-link {
    color: #1e40af;
    text-decoration: none;
    font-weight: 600;
}

.fee-plan-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 18px;
    min-width: 250px;
}
.fee-plan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.fee-plan-lbl {
    font-size: 0.82rem;
    font-weight: 800;
    color: #1e293b;
}
.btn-edit-fee-mini {
    background: none;
    border: none;
    color: #1e40af;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
}
.fee-val-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.82rem;
    margin-bottom: 4px;
}
.net-due-row {
    margin-top: 8px;
    padding-top: 6px;
    border-top: 1px dashed #cbd5e1;
    font-weight: 800;
}

/* 3. شبكة المؤشرات المالية الأربعة */
.financial-kpi-grid-classic {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}
.kpi-card-classic {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 20px 22px;
    display: flex;
    gap: 16px;
    align-items: center;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    transition: transform 0.2s, box-shadow 0.2s;
}
.kpi-card-classic:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
}
.kpi-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 1.45rem;
    flex-shrink: 0;
}
.kpi-due .kpi-icon-wrap { background: #eff6ff; color: #1e40af; }
.kpi-paid .kpi-icon-wrap { background: #ecfdf5; color: #059669; }
.kpi-remaining.is-alert .kpi-icon-wrap { background: #fff1f2; color: #e11d48; }
.kpi-remaining.is-safe .kpi-icon-wrap { background: #ecfdf5; color: #059669; }
.kpi-rate .kpi-icon-wrap { background: #f5f3ff; color: #7c3aed; }

.kpi-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 4px;
}
.kpi-num-wrap {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.kpi-denom {
    font-size: 0.95rem;
    color: #94a3b8;
}
.kpi-sub-text {
    display: block;
    font-size: 0.74rem;
    color: #64748b;
    margin-top: 4px;
}
.progress-bar-classic {
    height: 6px;
    background: #e2e8f0;
    border-radius: 6px;
    margin-top: 6px;
    overflow: hidden;
}
.progress-fill-classic {
    height: 100%;
    background: #7c3aed;
    border-radius: 6px;
    transition: width 0.4s ease;
}

/* كرت التنبيه المالي اللحظي والذمة المتأخرة */
.financial-arrears-alert-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 16px;
    padding: 18px 24px;
    margin-bottom: 26px;
    gap: 20px;
    flex-wrap: wrap;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
}
.financial-arrears-alert-card.has-arrears-alert {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1.5px solid #fde68a;
}
.financial-arrears-alert-card.is-normal-due {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1.5px solid #bfdbfe;
}
.alert-content-left {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    flex: 1;
}
.alert-icon-royal {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}
.has-arrears-alert .alert-icon-royal {
    background: #fef08a;
    color: #b45309;
    border: 1px solid #fcd34d;
}
.is-normal-due .alert-icon-royal {
    background: #bfdbfe;
    color: #1d4ed8;
    border: 1px solid #93c5fd;
}
.alert-text-royal {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.alert-royal-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
}
.has-arrears-alert .alert-royal-title {
    color: #92400e;
}
.is-normal-due .alert-royal-title {
    color: #1e3a8a;
}
.alert-royal-desc {
    margin: 0;
    font-size: 0.85rem;
    color: #334155;
    line-height: 1.5;
}
.arrears-month-pill {
    display: inline-block;
    background: #ffffff;
    border: 1px solid #f59e0b;
    color: #b45309;
    padding: 1px 8px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.76rem;
    margin: 0 2px;
}
.due-now-badge-box {
    background: #ffffff;
    border-radius: 12px;
    padding: 12px 20px;
    text-align: center;
    border: 1.5px solid #cbd5e1;
    min-width: 170px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.has-arrears-alert .due-now-badge-box {
    border-color: #f59e0b;
}
.due-now-badge-box .badge-title {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 2px;
}
.due-now-badge-box .badge-amt {
    display: block;
    font-size: 1.35rem;
    font-weight: 900;
    color: #0f172a;
}
.has-arrears-alert .due-now-badge-box .badge-amt {
    color: #b45309;
}

/* 4. لوحة استعراض الشهور الـ 12 */
.months-control-section-classic {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 26px 28px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
}
.section-classic-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 18px;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.sec-title {
    font-size: 1.2rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
}
.sec-desc {
    font-size: 0.82rem;
    color: #64748b;
    margin: 0;
}
.student-fast-navigator {
    display: flex;
    align-items: center;
    gap: 8px;
}
.nav-title-lbl {
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
}
.btn-nav-step {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
    text-decoration: none;
}
.btn-nav-step:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.select-fast-student {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #1e293b;
    max-width: 220px;
}

/* شبكة بطاقات الشهور الـ 12 */
.months-cards-grid-classic {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 20px;
}
.month-card-classic {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative;
    overflow: hidden;
}
.month-card-classic:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
}
.month-card-classic.status-border-paid { border-top: 4px solid #10b981; }
.month-card-classic.status-border-partial { border-top: 4px solid #f59e0b; }
.month-card-classic.status-border-pending { border-top: 4px solid #6366f1; }
.month-card-classic.status-border-waived { border-top: 4px solid #8b5cf6; }
.month-card-classic.status-border-unpaid { border-top: 4px solid #e2e8f0; }

.card-head-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}
.month-identity {
    display: flex;
    align-items: center;
    gap: 8px;
}
.month-number-circle {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #1e3a8a;
    font-size: 0.84rem;
    font-weight: 800;
    display: grid;
    place-items: center;
}
.month-name-text {
    font-size: 0.96rem;
    font-weight: 800;
    color: #0f172a;
}
.status-badge-classic {
    font-size: 0.74rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}
.status-badge-classic.badge-paid { background: #dcfce7; color: #166534; }
.status-badge-classic.badge-partial { background: #fef3c7; color: #92400e; }
.status-badge-classic.badge-pending { background: #e0e7ff; color: #3730a3; }
.status-badge-classic.badge-waived { background: #f3e8ff; color: #6b21a8; }
.status-badge-classic.badge-unpaid { background: #f1f5f9; color: #64748b; }

.card-financial-figures {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 12px;
    text-align: center;
}
.fig-lbl {
    display: block;
    font-size: 0.7rem;
    color: #64748b;
    margin-bottom: 2px;
}
.fig-val {
    font-size: 0.86rem;
    font-weight: 700;
    color: #1e293b;
}

.card-notes-preview {
    font-size: 0.76rem;
    color: #475569;
    margin-bottom: 14px;
    min-height: 38px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 3px;
}
.date-stamp-row {
    font-size: 0.72rem;
    color: #64748b;
}
.note-snippet {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.btn-manage-month-action {
    width: 100%;
    padding: 9px 12px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #1e293b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}
.btn-manage-month-action:hover {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
}

/* ==========================================================================
   نظام النوافذ والمودالات الملكية المعتمدة (Classic Royal Academic Modals)
   ========================================================================== */
.modal-overlay {
    position: fixed !important;
    inset: 0 !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(15, 23, 42, 0.72) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    z-index: 999999 !important;
    display: none;
    align-items: center !important;
    justify-content: center !important;
    padding: 20px !important;
    overflow-y: auto !important;
    box-sizing: border-box !important;
}

.modal-card-box {
    background: #ffffff !important;
    border-radius: 24px !important;
    box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.45), 0 0 0 1px rgba(226, 232, 240, 0.8) !important;
    width: 100% !important;
    max-width: 620px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative !important;
    margin: auto !important;
    padding: 28px 32px;
    animation: modalScaleIn 0.24s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-sizing: border-box !important;
}

@keyframes modalScaleIn {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(14px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.modal-card-box::-webkit-scrollbar {
    width: 6px;
}
.modal-card-box::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 8px;
}
.modal-card-box::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 8px;
}
.modal-card-box::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* رأس المودال الملكي */
.modal-header-royal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 18px;
    border-bottom: 1.5px solid #e2e8f0;
}
.modal-header-info {
    display: flex;
    align-items: center;
    gap: 14px;
}
.modal-avatar-badge {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
    display: grid;
    place-items: center;
    color: #1e3a8a;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
}
.modal-avatar-badge.text-amber {
    background: #fffbeb;
    border-color: #fde68a;
    color: #d97706;
}
.modal-student-name {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.3;
}
.modal-month-subtitle {
    margin: 3px 0 0;
    font-size: 0.84rem;
    color: #64748b;
    font-weight: 600;
}
.btn-close-x {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 1.35rem;
    line-height: 1;
    color: #64748b;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.btn-close-x:hover {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fca5a5;
    transform: rotate(90deg);
}

/* جسم النموذج */
.form-body-wrap {
    padding: 20px 0 6px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.section-label-royal {
    display: block;
    font-size: 0.88rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 2px;
}
.form-field-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.field-label-royal {
    font-size: 0.85rem;
    font-weight: 700;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 4px;
}
.field-hint {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 2px;
}

/* حقول الإدخال النظيفة */
.clean-input {
    width: 100%;
    height: 48px;
    padding: 0 16px;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    transition: all 0.2s ease;
    outline: none;
    box-sizing: border-box;
    font-family: inherit;
}
.clean-input:focus {
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3.5px rgba(30, 58, 138, 0.12);
}
.clean-input::placeholder {
    color: #94a3b8;
    font-weight: 400;
}

/* حقل العملة بالشيكل ₪ */
.input-with-currency {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}
.input-with-currency .clean-input {
    padding-left: 48px;
    padding-right: 16px;
    font-family: monospace, inherit;
    font-size: 1.05rem;
    font-weight: 800;
}
.input-with-currency .curr-tag {
    position: absolute;
    left: 12px;
    font-size: 0.95rem;
    font-weight: 800;
    color: #475569;
    background: #f1f5f9;
    padding: 3px 9px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    pointer-events: none;
}

/* شبكة كروت اختيار حالة السداد الملكية */
.status-options-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 4px;
}
.status-card-opt {
    position: relative;
    cursor: pointer;
    display: block;
    user-select: none;
}
.status-card-opt input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
    width: 0;
    height: 0;
}
.status-card-opt .opt-content {
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    padding: 10px 12px;
    background: #f8fafc;
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 10px;
    height: 100%;
    box-sizing: border-box;
}
.status-card-opt .opt-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
    background: #ffffff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: all 0.2s;
}
.status-card-opt .opt-text {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.status-card-opt .opt-text strong {
    font-size: 0.86rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1.25;
}
.status-card-opt .opt-text small {
    font-size: 0.7rem;
    color: #64748b;
    margin-top: 2px;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.status-card-opt:hover .opt-content {
    border-color: #cbd5e1;
    background: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* تلوين الحالات عند التحديد */
.opt-paid .opt-icon { color: #059669; }
.opt-paid input[type="radio"]:checked + .opt-content,
.opt-paid.is-selected .opt-content {
    border-color: #10b981;
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.2);
    transform: translateY(-2px);
}
.opt-paid input[type="radio"]:checked + .opt-content strong,
.opt-paid.is-selected .opt-content strong { color: #065f46; }

.opt-partial .opt-icon { color: #d97706; }
.opt-partial input[type="radio"]:checked + .opt-content,
.opt-partial.is-selected .opt-content {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.2);
    transform: translateY(-2px);
}
.opt-partial input[type="radio"]:checked + .opt-content strong,
.opt-partial.is-selected .opt-content strong { color: #92400e; }

.opt-unpaid .opt-icon { color: #dc2626; }
.opt-unpaid input[type="radio"]:checked + .opt-content,
.opt-unpaid.is-selected .opt-content {
    border-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.2);
    transform: translateY(-2px);
}
.opt-unpaid input[type="radio"]:checked + .opt-content strong,
.opt-unpaid.is-selected .opt-content strong { color: #991b1b; }

.opt-pending .opt-icon { color: #2563eb; }
.opt-pending input[type="radio"]:checked + .opt-content,
.opt-pending.is-selected .opt-content {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.2);
    transform: translateY(-2px);
}
.opt-pending input[type="radio"]:checked + .opt-content strong,
.opt-pending.is-selected .opt-content strong { color: #1e40af; }

.opt-waived .opt-icon { color: #8b5cf6; }
.opt-waived input[type="radio"]:checked + .opt-content,
.opt-waived.is-selected .opt-content {
    border-color: #8b5cf6;
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    box-shadow: 0 4px 14px rgba(139, 92, 246, 0.2);
    transform: translateY(-2px);
}
.opt-waived input[type="radio"]:checked + .opt-content strong,
.opt-waived.is-selected .opt-content strong { color: #5b21b6; }

/* شبكة مبالغ الاستحقاق والسداد */
.amounts-calc-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
@media (max-width: 520px) {
    .amounts-calc-grid {
        grid-template-columns: 1fr;
    }
}

/* خيارات سريعة */
.presets-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    padding: 8px 12px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
}
.presets-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
}
.btn-preset {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    color: #1e293b;
    font-size: 0.76rem;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.18s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.btn-preset:hover {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
    transform: translateY(-1.5px);
    box-shadow: 0 3px 8px rgba(30, 58, 138, 0.25);
}

/* شريط الحساب اللحظي المباشر */
.live-calc-box {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 18px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
}
.calc-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.88rem;
    font-weight: 700;
    color: #1e293b;
}
.remaining-display {
    font-size: 1.35rem;
    font-weight: 900;
    color: #dc2626;
    letter-spacing: -0.5px;
}
.calc-status-indicator {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed #cbd5e1;
    font-size: 0.82rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
}
.calc-status-indicator.is-paid { color: #059669; }
.calc-status-indicator.is-partial { color: #d97706; }
.calc-status-indicator.is-unpaid { color: #dc2626; }

/* أزرار أسفل المودال */
.modal-footer-royal {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1.5px solid #e2e8f0;
}
.btn-save-sub {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    color: #ffffff;
    border: none;
    height: 46px;
    padding: 0 24px;
    border-radius: 12px;
    font-size: 0.92rem;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(30, 58, 138, 0.28);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-save-sub:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
    transform: translateY(-1.5px);
    box-shadow: 0 6px 18px rgba(30, 58, 138, 0.38);
}
.btn-save-sub:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}
.btn-cancel-sub {
    background: #f1f5f9;
    color: #475569;
    border: 1.5px solid #cbd5e1;
    height: 46px;
    padding: 0 20px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.18s ease;
}
.btn-cancel-sub:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* مودال رسوم الطالب */
.modal-fee-box {
    max-width: 520px;
}
.checkbox-box-royal {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
}
.custom-chk-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #1e293b;
    cursor: pointer;
}
.custom-chk-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #1e3a8a;
    cursor: pointer;
}

/* مودال كشف الحساب وسند الذمة */
.modal-statement-sheet-wrap {
    max-width: 880px;
    width: 95%;
    max-height: 90vh;
    padding: 0;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 18px;
}
.statement-toolbar {
    background: #1e293b;
    padding: 14px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 18px 18px 0 0;
    flex-shrink: 0;
}
.btn-print-action {
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 9px 18px;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    transition: all 0.2s;
}
.btn-print-action:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}
.statement-document {
    padding: 24px 30px;
    overflow-y: auto !important;
    flex: 1;
    min-height: 0;
    -webkit-overflow-scrolling: touch;
}
.doc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.doc-header-col {
    display: flex;
    flex-direction: column;
    gap: 2px;
    font-size: 0.78rem;
    color: #475569;
}
.doc-logo-img {
    height: 48px;
    display: block;
    margin: 0 auto 6px;
}
.doc-main-title {
    font-size: 1.35rem;
    font-weight: 900;
    color: #0f172a;
    margin: 0;
    text-align: center;
}
.doc-badge-year {
    font-size: 0.78rem;
    background: #f1f5f9;
    padding: 2px 8px;
    border-radius: 6px;
    color: #475569;
    font-weight: 600;
    display: table;
    margin: 4px auto 0;
}
.doc-divider {
    border: none;
    border-top: 2px solid #0f172a;
    margin: 12px 0 16px;
}
.doc-student-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 0.82rem;
    margin-bottom: 18px;
}
.info-cell span {
    color: #64748b;
    margin-left: 6px;
}
.doc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
    margin-bottom: 20px;
}
.doc-table th, .doc-table td {
    border: 1px solid #cbd5e1;
    padding: 7px 10px;
}
.doc-table th {
    background: #f1f5f9;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
}
.doc-totals-row {
    background: #f8fafc;
    font-weight: 800;
}
.sheet-status {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
}
.bg-p { background: #dcfce7; color: #166534; }
.bg-part { background: #fef3c7; color: #92400e; }
.bg-pend { background: #e0e7ff; color: #3730a3; }
.bg-w { background: #f3e8ff; color: #6b21a8; }
.bg-u { background: #f1f5f9; color: #64748b; }

.clearance-notice-box {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 0.8rem;
    color: #334155;
    margin-bottom: 24px;
    line-height: 1.6;
}
.doc-signatures-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}
.sig-block {
    text-align: center;
    font-size: 0.82rem;
    font-weight: 700;
    color: #475569;
}
.sig-space {
    height: 45px;
}
.stamp-circle {
    width: 76px;
    height: 76px;
    border: 2px dashed #059669;
    border-radius: 50%;
    color: #059669;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 0.68rem;
    font-weight: 800;
    transform: rotate(-8deg);
}

@media print {
    @page {
        size: auto;
        margin: 8mm;
    }

    body.print-statement-active * {
        visibility: hidden;
    }
    body.print-statement-active #statementModal, 
    body.print-statement-active #statementModal * {
        visibility: visible;
    }
    body.print-statement-active #statementModal {
        position: static !important;
        display: block !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }
    body.print-statement-active .modal-statement-sheet-wrap {
        box-shadow: none !important;
        border: none !important;
        max-width: 100% !important;
        width: 100% !important;
        max-height: none !important;
        height: auto !important;
        overflow: visible !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
    }
    body.print-statement-active .statement-toolbar,
    body.print-statement-active .btn-close-x {
        display: none !important;
    }
    body.print-statement-active .statement-document {
        padding: 10px 14px !important;
        max-height: none !important;
        overflow: visible !important;
        page-break-inside: auto !important;
    }

    body:not(.print-statement-active) #statementModal {
        display: none !important;
    }
    body:not(.print-statement-active) .sidebar,
    body:not(.print-statement-active) .top-bar,
    body:not(.print-statement-active) .mobile-bottom-nav,
    body:not(.print-statement-active) .no-print,
    body:not(.print-statement-active) .btn-manage-fee,
    body:not(.print-statement-active) .btn-statement-royal {
        display: none !important;
    }
}

/* ==========================================================================
   تنسيقات الفصول الدراسية والمواد المسجلة في ملف الطالب
   ========================================================================== */
.semester-cards-grid-royal {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 28px;
}
.semester-card-royal {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 22px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.25s ease;
}
.semester-card-royal:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
}
.semester-card-royal.status-border-paid {
    border-right: 5px solid #10b981;
}
.semester-card-royal.status-border-partial {
    border-right: 5px solid #f59e0b;
}
.semester-card-royal.status-border-unpaid {
    border-right: 5px solid #ef4444;
}
.semester-card-royal.status-border-both {
    border-right: 5px solid #6366f1;
}

.sem-card-top {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
}
.sem-badge-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    font-weight: 800;
}
.sem-badge-icon.blue { background: #dbeafe; color: #1e40af; }
.sem-badge-icon.purple { background: #ede9fe; color: #6d28d9; }
.sem-badge-icon.amber { background: #fef3c7; color: #b45309; }

.sem-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}
.sem-subtitle {
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 600;
}
.status-pill-royal {
    margin-right: auto;
    font-size: 0.76rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.status-pill-royal.st-paid { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.status-pill-royal.st-partial { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.status-pill-royal.st-unpaid { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.status-pill-royal.st-waived { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }

.sem-figures-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    background: #f8fafc;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 18px;
    border: 1px solid #f1f5f9;
}
.sem-fig {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.sem-fig .lbl {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
}
.sem-fig strong {
    font-size: 0.95rem;
}

.sem-actions-row {
    display: flex;
    gap: 10px;
}
.btn-sem-action {
    width: 100%;
    padding: 10px 14px;
    border: none;
    border-radius: 10px;
    font-size: 0.86rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}
.btn-sem-action.btn-blue { background: #1e3a8a; color: #fff; }
.btn-sem-action.btn-blue:hover { background: #1e40af; }
.btn-sem-action.btn-purple { background: #5b21b6; color: #fff; }
.btn-sem-action.btn-purple:hover { background: #6d28d9; }
.btn-sem-action.btn-amber { background: #b45309; color: #fff; }
.btn-sem-action.btn-amber:hover { background: #d97706; }

/* جدول المواد المقيدة */
.registered-subjects-section-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 22px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
}
.subs-sec-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.subs-sec-header h3 {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.badge-count {
    background: #f1f5f9;
    color: #475569;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
}
.classic-subjects-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.86rem;
}
.classic-subjects-table th, .classic-subjects-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #e2e8f0;
    text-align: right;
}
.classic-subjects-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    font-size: 0.82rem;
}
.classic-subjects-table tbody tr:hover {
    background: #f8fafc;
}
.table-totals-row {
    background: #f8fafc;
    font-size: 0.9rem;
}
.badge-sem {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 700;
}
.badge-sem.term-1 { background: #dbeafe; color: #1e40af; }
.badge-sem.term-2 { background: #ede9fe; color: #6d28d9; }
.badge-sem.term-both { background: #fef3c7; color: #b45309; }

/* أزرار راديو اختيار الفصل والمودال */
.semester-radio-group {
    display: flex;
    gap: 12px;
    margin-top: 8px;
    flex-wrap: wrap;
}
.sem-radio-pill {
    flex: 1;
    min-width: 140px;
    cursor: pointer;
}
.sem-radio-pill input {
    display: none;
}
.sem-radio-pill span {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 14px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    font-weight: 700;
    font-size: 0.86rem;
    color: #334155;
    transition: all 0.2s;
}
.sem-radio-pill input:checked + span {
    border-color: #1e3a8a;
    background: #eff6ff;
    color: #1e3a8a;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.15);
}

.status-option-label {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    transition: all 0.2s;
}
.status-option-label input {
    margin-left: 8px;
}
.status-option-label:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.status-option-label input:checked ~ .opt-content {
    color: #1e3a8a;
}
</style>

<script>
const studentSemData = {
    term_1: {
        due: {{ (float)($semesterSummary['term_1_due'] ?? 0) }},
        paid: {{ (float)($semesterSummary['term_1_paid'] ?? 0) }},
        status: '{{ $semesterSummary['term_1_status'] ?? 'unpaid' }}'
    },
    term_2: {
        due: {{ (float)($semesterSummary['term_2_due'] ?? 0) }},
        paid: {{ (float)($semesterSummary['term_2_paid'] ?? 0) }},
        status: '{{ $semesterSummary['term_2_status'] ?? 'unpaid' }}'
    },
    both: {
        due: {{ (float)($semesterSummary['total_due'] ?? 0) }},
        paid: {{ (float)($semesterSummary['total_paid'] ?? 0) }},
        status: '{{ $studentRemaining == 0 ? 'paid' : ($studentPaid > 0 ? 'partial' : 'unpaid') }}'
    }
};

// فتح نافذة سداد وتعديل الرسوم الفصلية
function openSemesterPaymentModal(studentId, studentName, semester, due, paid, status) {
    document.getElementById('formStudentId').value = studentId;
    document.getElementById('modalStudentNameTitle').innerText = studentName;
    
    const semTitle = (semester === 'term_1') ? '{{ __('الفصل الأول (Term 1)') }}' : ((semester === 'term_2') ? '{{ __('الفصل الثاني (Term 2)') }}' : '{{ __('كلا الفصلين (العام كامل)') }}');
    document.getElementById('modalSemesterSubtitle').innerText = semTitle + ' ({{ $year }})';

    // ضبط راديو الفصل
    document.querySelectorAll('input[name="semester"]').forEach(r => r.checked = false);
    const semRadio = document.querySelector(`input[name="semester"][value="${semester}"]`);
    if (semRadio) semRadio.checked = true;

    // ضبط المبالغ
    const dueVal = parseFloat(due) || 0;
    const paidVal = (status === 'paid' && parseFloat(paid) <= 0) ? dueVal : (parseFloat(paid) || 0);

    document.getElementById('formSemAmount').value = dueVal.toFixed(2);
    document.getElementById('formPaidAmount').value = paidVal.toFixed(2);
    document.getElementById('formNotes').value = '';

    // ضبط راديو الحالة
    document.querySelectorAll('input[name="status"]').forEach(r => r.checked = false);
    const effectiveStatus = (status === 'empty' || !status) ? 'unpaid' : status;
    const stRadio = document.querySelector(`input[name="status"][value="${effectiveStatus}"]`);
    if (stRadio) stRadio.checked = true;

    calcRemainingLive();
    document.getElementById('editSemesterModal').style.display = 'flex';
}

function closeSemesterPaymentModal() {
    document.getElementById('editSemesterModal').style.display = 'none';
}

// عند تغيير اختيار الفصل في المودال
function onSemesterRadioChange() {
    const sem = document.querySelector('input[name="semester"]:checked')?.value || 'term_1';
    const data = studentSemData[sem] || { due: 0, paid: 0, status: 'unpaid' };

    document.getElementById('formSemAmount').value = data.due.toFixed(2);
    document.getElementById('formPaidAmount').value = (data.status === 'paid' && data.paid <= 0) ? data.due.toFixed(2) : data.paid.toFixed(2);

    document.querySelectorAll('input[name="status"]').forEach(r => r.checked = false);
    const effectiveStatus = (data.status === 'empty' || !data.status) ? 'unpaid' : data.status;
    const stRadio = document.querySelector(`input[name="status"][value="${effectiveStatus}"]`);
    if (stRadio) stRadio.checked = true;

    calcRemainingLive();
}

// حساب المتبقي الحي
function calcRemainingLive() {
    const amtInput = document.getElementById('formSemAmount');
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

    if (paid >= amt && amt > 0) {
        statusNotice.className = 'calc-status-indicator is-paid';
        statusNotice.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>{{ __('مسدد بالكامل رسمياً ✅ (الرصيد المتبقي: 0.00 ₪)') }}</span>';
        if (statusRadioPaid) statusRadioPaid.checked = true;
    } else if (paid > 0 && paid < amt) {
        statusNotice.className = 'calc-status-indicator is-partial';
        statusNotice.innerHTML = '<i class="fa-solid fa-circle-half-stroke"></i> <span>{{ __('سداد جزئي ⚠️ (الرصيد المتبقي: ') }}' + remaining.toFixed(2) + ' ₪)</span>';
        if (statusRadioPartial) statusRadioPartial.checked = true;
    } else if (paid === 0 && amt > 0) {
        statusNotice.className = 'calc-status-indicator is-unpaid';
        statusNotice.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> <span>{{ __('غير مسدد ❌ (إجمالي المستحق: ') }}' + amt.toFixed(2) + ' ₪)</span>';
        if (statusRadioUnpaid) statusRadioUnpaid.checked = true;
    }
}

// خيارات سريعة للمبالغ
function setPresetPaid(type) {
    const amtInput = document.getElementById('formSemAmount');
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

// تغيير أزرار الراديو للحالة
function onStatusRadioChange(val) {
    const amtInput = document.getElementById('formSemAmount');
    const paidInput = document.getElementById('formPaidAmount');
    const amt = parseFloat(amtInput.value) || 0;

    if (val === 'waived') {
        paidInput.value = '0.00';
    } else if (val === 'paid') {
        paidInput.value = amt.toFixed(2);
    } else if (val === 'partial') {
        paidInput.value = (amt > 0) ? (amt / 2).toFixed(2) : '0.00';
    } else if (val === 'unpaid') {
        paidInput.value = '0.00';
    }
    calcRemainingLive();
}

// حفظ الرسوم الفصلية عبر AJAX
function saveSemesterSubscription(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSaveSub');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

    const form = document.getElementById('updateSemesterForm');
    const formData = new FormData(form);

    fetch("{{ route('admin.subscriptions.semester.update') }}", {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>{{ __('حفظ واعتماد التحديث') }}</span>';

        if (data.success) {
            closeSemesterPaymentModal();
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء الحفظ.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>{{ __('حفظ واعتماد التحديث') }}</span>';
        alert('تعذر الاتصال بالخادم، يرجى المحاولة ثانية.');
    });
}

// نافذة تعديل الرسوم الفردية
function openStudentFeeModal() {
    calcNetFeePreview();
    document.getElementById('studentFeeModal').style.display = 'flex';
}
function closeStudentFeeModal() {
    document.getElementById('studentFeeModal').style.display = 'none';
}

function calcNetFeePreview() {
    const base = parseFloat(document.getElementById('feeInputMonthly').value) || 0;
    const pct = parseFloat(document.getElementById('feeInputPercent').value) || 0;
    const fix = parseFloat(document.getElementById('feeInputFixed').value) || 0;

    let net = base;
    if (pct > 0) net = net * (1 - (pct / 100));
    if (fix > 0) net = net - fix;
    net = Math.max(0, net);

    document.getElementById('feeNetPreview').innerText = Math.round(net) + ' ₪/شهر';
}

function submitStudentFeeForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSaveFee');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

    const form = document.getElementById('studentFeeForm');
    const formData = new FormData(form);

    fetch("{{ route('admin.subscriptions.monthly.updateStudentFee') }}", {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>{{ __('حفظ وتطبيق الخطة المالية') }}</span>';
        if (data.success) {
            closeStudentFeeModal();
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء الحفظ.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>{{ __('حفظ وتطبيق الخطة المالية') }}</span>';
        alert('تعذر الاتصال بالخادم.');
    });
}

// نافذة كشف الذمة
function openStatementModal() {
    document.getElementById('statementModal').style.display = 'flex';
    document.body.classList.add('print-statement-active');
}
function closeStatementModal() {
    document.getElementById('statementModal').style.display = 'none';
    document.body.classList.remove('print-statement-active');
}
function printStatementDoc() {
    document.body.classList.add('print-statement-active');
    window.print();
}

window.addEventListener('beforeprint', function() {
    const modal = document.getElementById('statementModal');
    if (modal && modal.style.display === 'flex') {
        document.body.classList.add('print-statement-active');
    } else {
        document.body.classList.remove('print-statement-active');
    }
});

window.addEventListener('afterprint', function() {
    const modal = document.getElementById('statementModal');
    if (!modal || modal.style.display !== 'flex') {
        document.body.classList.remove('print-statement-active');
    }
});

// إغلاق النوافذ عند النقر على الخلفية المعتمة أو الضغط على Escape
window.addEventListener('click', function(e) {
    if (e.target && e.target.classList && e.target.classList.contains('modal-overlay')) {
        e.target.style.display = 'none';
    }
});
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['editSemesterModal', 'studentFeeModal', 'statementModal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }
});
</script>
@endsection
