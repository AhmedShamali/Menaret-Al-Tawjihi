@extends('layouts.app')

@section('title', __('مصفوفة وسجل الاشتراكات والرسوم الفصلية للطلاب') . ' - ' . __('Step by Step'))

@section('content')
<div class="subs-matrix-wrapper">
    {{-- 1. رأس الصفحة الأكاديمي الكلاسيكي الهادئ --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 24px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1.25rem; border: 1px solid #bfdbfe; flex-shrink: 0;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <h1 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; font-family: 'Alexandria', 'Cairo', sans-serif;">
                    {{ __('مصفوفة وسجل الاشتراكات والرسوم الفصلية') }}
                </h1>
                <div style="font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 6px;">
                    <a href="{{ route('admin.dashboard') }}" style="color: #64748b; text-decoration: none;">{{ __('الرئيسية') }}</a>
                    <span>/</span>
                    <span style="color: #1e293b; font-weight: 600;">{{ __('سجل الاشتراكات والتحصيلات') }}</span>
                    <span>•</span>
                    <span>{{ __('العام الدراسي') }} {{ $year }}</span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.subscriptions.monthly') }}" style="display: inline-flex; align-items: center; gap: 6px; margin: 0;">
                <label style="font-size: 0.82rem; font-weight: 600; color: #475569;"><i class="fa-regular fa-calendar"></i> {{ __('العام:') }}</label>
                <select name="year" onchange="this.form.submit()" style="padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem; font-weight: 600; color: #1e293b; background: #ffffff;">
                    <option value="2026-2027" {{ $year === '2026-2027' ? 'selected' : '' }}>2026 / 2027 {{ app()->getLocale() === 'ar' ? 'م' : 'AD' }}</option>
                    <option value="2025-2026" {{ $year === '2025-2026' ? 'selected' : '' }}>2025 / 2026 {{ app()->getLocale() === 'ar' ? 'م' : 'AD' }}</option>
                </select>
            </form>

            <a href="{{ route('admin.subjects.pricing') }}" style="display: inline-flex; align-items: center; gap: 7px; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 6px; font-weight: 600; font-size: 0.84rem; text-decoration: none; transition: background 0.15s;">
                <i class="fa-solid fa-tags" style="color: #64748b;"></i>
                <span>{{ __('تسعير وباقات المواد') }}</span>
            </a>

            <button type="button" onclick="printGeneralMatrixDoc()" style="display: inline-flex; align-items: center; gap: 7px; background: #1d4ed8; color: #ffffff; border: 1px solid #1e40af; padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 0.84rem; cursor: pointer; transition: background 0.15s; box-shadow: 0 1px 2px rgba(29, 78, 216, 0.15);">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة الكشف') }}</span>
            </button>
        </div>
    </div>

    {{-- 2. شريط المؤشرات المالية المدمج (Compact Academic KPI Grid) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 20px;">
        
        <!-- كرت 1: إجمالي المستحق المطلوب -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
            <div>
                <span style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 2px;">
                    {{ __('إجمالي المستحق المطلوب') }}
                </span>
                <span style="font-size: 1.35rem; font-weight: 800; color: #0f172a; font-family: 'Alexandria', sans-serif;" id="stat_total_expected">
                    {{ number_format($stats['total_expected'], 2) }} ₪
                </span>
                <span style="display: block; font-size: 0.72rem; color: #94a3b8; margin-top: 2px;">
                    <i class="fa-solid fa-users"></i> {{ $students->total() }} {{ __('طالب مسجل') }}
                </span>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1.1rem; flex-shrink: 0;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>

        <!-- كرت 2: المحصل الفعلي المعتمد -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
            <div>
                <span style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 2px;">
                    {{ __('إجمالي الإيراد المحصل') }}
                </span>
                <span style="font-size: 1.35rem; font-weight: 800; color: #059669; font-family: 'Alexandria', sans-serif;" id="stat_total_collected">
                    {{ number_format($stats['total_collected'], 2) }} ₪
                </span>
                <span style="display: block; font-size: 0.72rem; color: #059669; margin-top: 2px;">
                    <i class="fa-solid fa-check"></i> <span id="stat_paid_count">{{ $stats['paid_count'] }}</span> {{ __('اشتراك مسدد') }}
                </span>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 8px; background: #ecfdf5; color: #059669; display: grid; place-items: center; font-size: 1.1rem; flex-shrink: 0;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- كرت 3: عجز التحصيل والذمم -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
            <div>
                <span style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 2px;">
                    {{ __('إجمالي الرصيد المتبقي') }}
                </span>
                <span style="font-size: 1.35rem; font-weight: 800; color: #dc2626; font-family: 'Alexandria', sans-serif;" id="stat_total_remaining">
                    {{ number_format($stats['total_remaining'], 2) }} ₪
                </span>
                <span style="display: block; font-size: 0.72rem; color: #dc2626; margin-top: 2px;">
                    <span id="stat_partial_count">{{ $stats['partial_count'] }}</span> {{ __('سداد جزئي') }} | <span id="stat_unpaid_count">{{ $stats['unpaid_count'] }}</span> {{ __('غير مسدد') }}
                </span>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 8px; background: #fef2f2; color: #dc2626; display: grid; place-items: center; font-size: 1.1rem; flex-shrink: 0;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>

        <!-- كرت 4: مؤشر الالتزام والتحصيل -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
            <div>
                <span style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 2px;">
                    {{ __('مؤشر الالتزام والتحصيل') }}
                </span>
                <span style="font-size: 1.35rem; font-weight: 800; color: #1e293b; font-family: 'Alexandria', sans-serif;" id="stat_collection_rate">
                    {{ $stats['collection_rate'] }}%
                </span>
                <span style="display: block; font-size: 0.72rem; color: #94a3b8; margin-top: 2px;">
                    <span id="stat_pending_count">{{ $stats['pending_count'] }}</span> {{ __('إشعار مراجعة') }}
                </span>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 8px; background: #f8fafc; color: #475569; display: grid; place-items: center; font-size: 1.1rem; flex-shrink: 0;">
                <i class="fa-solid fa-chart-pie"></i>
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
                <select name="semester" class="filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('كافة الفصول الدراسية') }}</option>
                    <option value="term_1" {{ $semesterFilter === 'term_1' ? 'selected' : '' }}>{{ __('الفصل الأول') }} (Term 1)</option>
                    <option value="term_2" {{ $semesterFilter === 'term_2' ? 'selected' : '' }}>{{ __('الفصل الثاني') }} (Term 2)</option>
                    <option value="both" {{ $semesterFilter === 'both' ? 'selected' : '' }}>{{ __('الفصلين معاً') }} (Full Year)</option>
                </select>
            </div>

            <div class="filter-cell">
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('كافة حالات الدفع') }}</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>{{ __('مسدد بالكامل رسمياً') }} ✅</option>
                    <option value="partial" {{ $statusFilter === 'partial' ? 'selected' : '' }}>{{ __('سداد جزئي (يوجد رصيد متبقي)') }} ⚠️</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>{{ __('قيد المراجعة والاعتماد') }} ⏳</option>
                    <option value="unpaid" {{ $statusFilter === 'unpaid' ? 'selected' : '' }}>{{ __('غير مسدد نهائياً') }} ❌</option>
                    <option value="waived" {{ $statusFilter === 'waived' ? 'selected' : '' }}>{{ __('إعفاء / منحة دراسية') }} 🏷️</option>
                </select>
            </div>

            <button type="submit" class="btn-filter-submit"><i class="fa-solid fa-filter"></i> {{ __('تطبيق الفلترة') }}</button>

            @if($search || $stageId || $semesterFilter || $statusFilter)
                <a href="{{ route('admin.subscriptions.monthly', ['year' => $year]) }}" class="btn-reset-filter">{{ __('تصفير') }}</a>
            @endif
        </form>

        <div class="legend-strip">
            <span class="legend-title">{{ __('دليل الحالات:') }}</span>
            <span class="legend-item"><span class="badge-mini bg-paid"></span> {{ __('مسدد بالكامل') }} ✅</span>
            <span class="legend-item"><span class="badge-mini bg-partial"></span> {{ __('سداد جزئي مع بقاء رصيد') }} ⚠️</span>
            <span class="legend-item"><span class="badge-mini bg-pending"></span> {{ __('قيد المراجعة') }} ⏳</span>
            <span class="legend-item"><span class="badge-mini bg-unpaid"></span> {{ __('غير مسدد') }} ❌</span>
            <span class="legend-item"><span class="badge-mini bg-waived"></span> {{ __('إعفاء / منحة') }} 🏷️</span>
        </div>
    </div>

    {{-- 4. مصفوفة وجدول اشتراكات الطلاب بنظام الفصلين الدراسيين --}}
    <div class="students-list-wrapper">
        <div class="list-header-row list-header-semesters">
            <span class="col-head-student">{{ __('بيانات الطالب والمرحلة والمنطقة') }}</span>
            <span class="col-head-term1">{{ __('الفصل الأول (Term 1)') }}</span>
            <span class="col-head-term2">{{ __('الفصل الثاني (Term 2)') }}</span>
            <span class="col-head-finance">{{ __('الموقف المالي الإجمالي') }}</span>
            <span class="col-head-actions">{{ __('التحكم والسندات') }}</span>
        </div>

        @forelse($students as $student)
            @php
                $semFin = $student->semester_summary ?? $student->getSemesterFinancialSummary($year);
                $studentDisplayName = (app()->getLocale() === 'en' && !empty($student->name_en)) ? $student->name_en : ($student->name_ar ?? $student->name);
                $stageDisplayName = (app()->getLocale() === 'en' && !empty($student->stage->name_en)) ? $student->stage->name_en : ($student->stage->label_ar ?? ($student->stage->name_ar ?? __('عام')));
                $regionBadge = ($student->resolved_region === 'gaza') ? '🌿 قطاع غزة' : '🏛️ الضفة الغربية';
                $subsArray = ($student->semesterSubscriptions ?? collect())->map(function($sub) {
                    return [
                        'id' => $sub->id,
                        'subject_name' => $sub->subject->name ?? 'مادة تعليمية',
                        'semester' => $sub->semester,
                        'semester_name' => $sub->semester_name_ar,
                        'amount' => (float)$sub->amount,
                        'paid_amount' => (float)$sub->paid_amount,
                        'remaining_amount' => (float)$sub->remaining_amount,
                        'status' => $sub->status,
                        'status_label' => $sub->status_badge['label'],
                        'status_class' => $sub->status_badge['class'],
                        'paid_at' => $sub->paid_at ? $sub->paid_at->format('Y-m-d') : '-',
                        'notes' => $sub->notes ?? '-'
                    ];
                })->values()->all();
                $subsJson = json_encode($subsArray, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            @endphp
            <div class="student-matrix-row student-semester-row" 
                 id="student_row_{{ $student->id }}"
                 data-student-id="{{ $student->id }}"
                 data-student-name="{{ $studentDisplayName }}"
                 data-student-stage="{{ $stageDisplayName }}"
                 data-student-phone="{{ $student->phone ?? ($student->nid ?? '-') }}"
                 data-student-region="{{ $regionBadge }}"
                 data-term1-due="{{ $semFin['term_1_due'] }}"
                 data-term1-paid="{{ $semFin['term_1_paid'] }}"
                 data-term1-status="{{ $semFin['term_1_status'] }}"
                 data-term2-due="{{ $semFin['term_2_due'] }}"
                 data-term2-paid="{{ $semFin['term_2_paid'] }}"
                 data-term2-status="{{ $semFin['term_2_status'] }}"
                 data-total-due="{{ $semFin['total_due'] }}"
                 data-total-paid="{{ $semFin['total_paid'] }}"
                 data-total-remaining="{{ $semFin['total_remaining'] }}"
                 data-subs='{!! $subsJson !!}'
            >
                {{-- تعريف الطالب --}}
                <div class="student-profile-block">
                    <img src="{{ $student->photo_url }}" class="student-avatar" alt="{{ $studentDisplayName }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($studentDisplayName) }}&background=1d4ed8&color=fff&size=50&bold=true';">
                    <div class="student-text">
                        <a href="{{ route('admin.subscriptions.student', ['student' => $student->id, 'year' => $year]) }}" class="student-name" title="{{ __('فتح الواجهة المالية وسجل اشتراكات الطالب') }}">
                            {{ $studentDisplayName }}
                        </a>
                        <div class="student-sub-line">
                            <span class="branch-pill">{{ $stageDisplayName }}</span>
                            <span class="region-pill-mini {{ $student->resolved_region === 'gaza' ? 'bg-gaza' : 'bg-wb' }}">{{ $regionBadge }}</span>
                            <span class="phone-text font-mono" dir="ltr">{{ $student->phone ?? ($student->nid ?? '-') }}</span>
                            @if(!empty($student->plain_password))
                                <code class="pass-chip font-mono" title="{{ __('كلمة المرور') }}" onclick="if(typeof Swal !== 'undefined'){ navigator.clipboard.writeText('{{ $student->plain_password }}'); Swal.fire({toast:true,position:'top-end',icon:'success',title:'{{ __('تم نسخ كلمة المرور') }}',showConfirmButton:false,timer:1500}); }">{{ $student->plain_password }}</code>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- بطاقة الفصل الأول --}}
                <div class="term-col-box term-col-1" id="term1_box_{{ $student->id }}">
                    <div class="term-box-header">
                        <span class="term-title-tag"><i class="fa-solid fa-calendar-check text-blue"></i> {{ __('الفصل الأول') }}</span>
                        <span class="term-count-badge font-mono">{{ $semFin['term_1_count'] }} {{ __('مواد') }}</span>
                    </div>
                    <div class="term-figures-row font-mono">
                        <span>{{ __('مستحق:') }} <strong>{{ number_format($semFin['term_1_due'], 0) }} ₪</strong></span>
                        <span class="text-emerald">{{ __('مسدد:') }} <strong>{{ number_format($semFin['term_1_paid'], 0) }} ₪</strong></span>
                        <span class="{{ $semFin['term_1_remaining'] > 0 ? 'text-rose font-bold' : 'text-emerald' }}">
                            {{ __('متبقي:') }} <strong>{{ number_format($semFin['term_1_remaining'], 0) }} ₪</strong>
                        </span>
                    </div>
                    <div class="term-status-action-row">
                        @php
                            $t1St = $semFin['term_1_status'];
                        @endphp
                        <span class="term-status-pill st-{{ $t1St }}">
                            @if($t1St === 'paid')
                                <i class="fa-solid fa-circle-check"></i> {{ __('مسدد بالكامل ✅') }}
                            @elseif($t1St === 'partial')
                                <i class="fa-solid fa-circle-half-stroke"></i> {{ __('سداد جزئي ⚠️') }}
                            @elseif($t1St === 'waived')
                                <i class="fa-solid fa-tag"></i> {{ __('إعفاء / منحة 🏷️') }}
                            @elseif($t1St === 'empty')
                                <i class="fa-solid fa-minus"></i> {{ __('غير مسجل') }}
                            @else
                                <i class="fa-solid fa-circle-xmark"></i> {{ __('غير مسدد ❌') }}
                            @endif
                        </span>
                        <button type="button" class="btn-term-quick-edit" 
                                onclick="openSemesterPaymentModal({{ $student->id }}, '{{ addslashes($studentDisplayName) }}', 'term_1', {{ $semFin['term_1_due'] }}, {{ $semFin['term_1_paid'] }}, '{{ $t1St }}')"
                                title="{{ __('تسجيل دفعة أو تعديل الفصل الأول') }}">
                            <i class="fa-solid fa-pen-to-square"></i> {{ __('سداد / تعديل') }}
                        </button>
                    </div>
                </div>

                {{-- بطاقة الفصل الثاني --}}
                <div class="term-col-box term-col-2" id="term2_box_{{ $student->id }}">
                    <div class="term-box-header">
                        <span class="term-title-tag"><i class="fa-solid fa-calendar-days text-indigo"></i> {{ __('الفصل الثاني') }}</span>
                        <span class="term-count-badge font-mono">{{ $semFin['term_2_count'] }} {{ __('مواد') }}</span>
                    </div>
                    <div class="term-figures-row font-mono">
                        <span>{{ __('مستحق:') }} <strong>{{ number_format($semFin['term_2_due'], 0) }} ₪</strong></span>
                        <span class="text-emerald">{{ __('مسدد:') }} <strong>{{ number_format($semFin['term_2_paid'], 0) }} ₪</strong></span>
                        <span class="{{ $semFin['term_2_remaining'] > 0 ? 'text-rose font-bold' : 'text-emerald' }}">
                            {{ __('متبقي:') }} <strong>{{ number_format($semFin['term_2_remaining'], 0) }} ₪</strong>
                        </span>
                    </div>
                    <div class="term-status-action-row">
                        @php
                            $t2St = $semFin['term_2_status'];
                        @endphp
                        <span class="term-status-pill st-{{ $t2St }}">
                            @if($t2St === 'paid')
                                <i class="fa-solid fa-circle-check"></i> {{ __('مسدد بالكامل ✅') }}
                            @elseif($t2St === 'partial')
                                <i class="fa-solid fa-circle-half-stroke"></i> {{ __('سداد جزئي ⚠️') }}
                            @elseif($t2St === 'waived')
                                <i class="fa-solid fa-tag"></i> {{ __('إعفاء / منحة 🏷️') }}
                            @elseif($t2St === 'empty')
                                <i class="fa-solid fa-minus"></i> {{ __('غير مسجل') }}
                            @else
                                <i class="fa-solid fa-circle-xmark"></i> {{ __('غير مسدد ❌') }}
                            @endif
                        </span>
                        <button type="button" class="btn-term-quick-edit" 
                                onclick="openSemesterPaymentModal({{ $student->id }}, '{{ addslashes($studentDisplayName) }}', 'term_2', {{ $semFin['term_2_due'] }}, {{ $semFin['term_2_paid'] }}, '{{ $t2St }}')"
                                title="{{ __('تسجيل دفعة أو تعديل الفصل الثاني') }}">
                            <i class="fa-solid fa-pen-to-square"></i> {{ __('سداد / تعديل') }}
                        </button>
                    </div>
                </div>

                {{-- الموقف المالي الإجمالي للطالب --}}
                <div class="student-financial-summary-block">
                    <div class="fin-pill-group">
                        <div class="fin-pill fin-due" title="{{ __('إجمالي الرسوم الفصلية المقررة') }}">
                            <span class="fin-lbl">{{ __('المطلوب:') }}</span>
                            <strong class="font-mono" id="std_due_{{ $student->id }}">{{ number_format($semFin['total_due'], 0) }} ₪</strong>
                        </div>
                        <div class="fin-pill fin-paid" title="{{ __('إجمالي المبالغ المسددة فعلياً') }}">
                            <span class="fin-lbl">{{ __('المسدد:') }}</span>
                            <strong class="font-mono text-emerald font-bold" id="std_paid_{{ $student->id }}">{{ number_format($semFin['total_paid'], 0) }} ₪</strong>
                        </div>
                        <div class="fin-pill fin-remaining {{ $semFin['total_remaining'] > 0 ? 'has-remaining-alert' : 'is-clear' }}" title="{{ __('الرصيد المتبقي بذمة الطالب') }}">
                            <span class="fin-lbl">{{ __('المتبقي:') }}</span>
                            <strong class="font-mono font-bold" id="std_rem_{{ $student->id }}">
                                @if($semFin['total_remaining'] > 0)
                                    {{ number_format($semFin['total_remaining'], 0) }} ₪ ⚠️
                                @else
                                    0 ₪ ✅
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>

                {{-- أزرار التحكم والسندات --}}
                <div class="student-actions-block">
                    <button type="button" 
                            class="btn-statement-royal"
                            onclick="openStudentStatementModal({{ $student->id }})"
                            title="{{ __('عرض وطباعة سند كشف الذمة المالي الرسمي المعتمد للطالب') }}">
                        <i class="fa-solid fa-receipt"></i>
                        <span>{{ __('كشف رسمي') }}</span>
                    </button>

                    <a href="{{ route('admin.subscriptions.student', ['student' => $student->id, 'year' => $year]) }}" 
                       class="btn-student-profile-link"
                       title="{{ __('فتح الواجهة المالية والاشتراكات المستقلة للطالب') }}">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>{{ __('الملف المالي') }}</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-matrix-card">
                <i class="fa-solid fa-users-slash"></i>
                <p>{{ __('لم يتم العثور على أي طلاب مطابقين لشروط البحث والفلترة.') }}</p>
            </div>
        @endforelse

        <div style="margin-top: 24px;">
            {{ $students->links('vendor.pagination.bootstrap-5') }}
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
            <input type="hidden" name="student_id" id="formStudentId">
            <input type="hidden" name="academic_year" value="{{ $year }}">

            <div class="form-body-wrap">
                {{-- اختيار الفصل الدراسي المستهدف --}}
                <div class="form-field-group">
                    <label class="field-label">{{ __('الفصل الدراسي المستهدف للسداد / التعديل') }} <span class="required">*</span></label>
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
                    <label class="field-label">{{ __('حالة سداد الرسوم') }} <span class="required">*</span></label>
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
                        <label class="field-label">{{ __('إجمالي الرسوم المقررة للفصل (₪)') }}</label>
                        <input type="number" step="0.01" min="0" id="formSemAmount" class="clean-input font-mono font-bold" readonly style="background: #f1f5f9;">
                        <small class="field-hint">{{ __('محسوب تلقائياً حسب المواد والمنطقة') }}</small>
                    </div>

                    <div class="form-field-group">
                        <label class="field-label">{{ __('المبلغ المسدد فعلياً (₪)') }} <span class="required">*</span></label>
                        <input type="number" step="0.01" min="0" name="paid_amount" id="formPaidAmount" class="clean-input font-mono font-bold text-emerald" required oninput="calcRemainingLive()">
                        <small class="field-hint">{{ __('المبلغ المقبوض من الطالب فعلياً') }}</small>
                    </div>
                </div>

                {{-- أزرار سريعة للمبالغ --}}
                <div class="quick-amount-presets">
                    <span class="preset-label">{{ __('خيارات سريعة:') }}</span>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('full')">{{ __('سداد كامل 100%') }}</button>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('half')">{{ __('سداد 50%') }}</button>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('zero')">{{ __('غير مسدد (0 ₪)') }}</button>
                </div>

                {{-- حاسبة حية للمتبقي --}}
                <div class="live-calc-box" id="liveCalcBox">
                    <div class="calc-label-row">
                        <span class="calc-text">{{ __('الرصيد المتبقي بذمة الطالب:') }}</span>
                        <strong class="calc-value font-mono" id="formRemainingPreview">0.00 ₪</strong>
                    </div>
                    <div class="calc-status-indicator" id="formStatusNotice">
                        <i class="fa-solid fa-circle-check"></i> <span>{{ __('مسدد بالكامل رسمياً') }}</span>
                    </div>
                </div>

                <div class="form-field-group">
                    <label class="field-label">{{ __('ملاحظات وبيان الدفعة (تظهر في السند)') }}</label>
                    <input type="text" name="notes" id="formNotes" class="clean-input" placeholder="{{ __('مثال: إشعار سداد بنكي رقم... دفعة نقدية معتمدة') }}">
                </div>
            </div>

            <div class="modal-footer-row">
                <button type="submit" class="btn-save-sub" id="btnSaveSub">
                    <i class="fa-solid fa-check"></i> {{ __('حفظ واعتماد التحديث') }}
                </button>
                <button type="button" class="btn-cancel-sub" onclick="closeSemesterPaymentModal()">{{ __('إلغاء') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- 6. نافذة (مودال) سند كشف الحساب والذمة المالي الرسمي المعتمد للطباعة --}}
<div id="statementModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box modal-statement-sheet-wrap">
        <div class="statement-toolbar">
            <span class="statement-title-info"><i class="fa-solid fa-stamp text-amber"></i> {{ __('سند كشف حساب وذمة مالية رسمي معتمد للطباعة (نظام الفصول الدراسية)') }}</span>
            <div style="display: flex; gap: 8px;">
                <button type="button" class="btn-statement-print" onclick="printStatementDoc()"><i class="fa-solid fa-print"></i> {{ __('طباعة السند الرسمي') }}</button>
                <button type="button" class="btn-close-x" onclick="closeStatementModal()">&times;</button>
            </div>
        </div>

        <div class="statement-printable-sheet" id="statementPrintableArea">
            {{-- الإطارات الملكية الكلاسيكية المزدوجة --}}
            <div class="royal-outer-border"></div>
            <div class="royal-inner-border"></div>

            {{-- الترويسة الرسمية --}}
            <div class="sheet-header">
                <div class="sheet-col-ar">
                    <h3>دولة فلسطين 🇵🇸</h3>
                    <p>{{ __('منظومة Step by Step للتعليم الأكاديمي') }}</p>
                    <small>{{ __('إشراف ومتابعة الثانوية العامة - الشؤون المالية') }}</small>
                </div>

                <div class="sheet-emblem">
                    @php
                        $directorLogo = \App\Models\Setting::get('director_logo');
                        $siteLogo = \App\Models\Setting::get('site_logo');
                        $fallbackLogo = asset('images/logo.png');
                        $sheetLogo = !empty($directorLogo) ? asset($directorLogo) : (!empty($siteLogo) ? asset($siteLogo) : $fallbackLogo);
                    @endphp
                    <img src="{{ $sheetLogo }}" alt="شعار المنصة" class="sheet-logo-img" onerror="this.onerror=null; this.src='{{ $fallbackLogo }}';">
                    <span class="sheet-badge-tag">{{ __('سند كشف حساب رسمي معتمد') }}</span>
                </div>

                <div class="sheet-col-en">
                    <h3>STATE OF PALESTINE</h3>
                    <p>Step by Step Educational Platform</p>
                    <small>Official Academic & Financial Statement</small>
                </div>
            </div>

            <div class="sheet-heading">
                <h2>{{ __('سند كشف حساب الاشتراكات والرسوم الفصلية') }}</h2>
                <div class="sheet-subhead">OFFICIAL SEMESTER TUITION & SUBSCRIPTION LEDGER</div>
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
                    <span class="sc-lbl">{{ __('المنطقة الجغرافية:') }}</span>
                    <span class="sc-val" id="stmtStudentRegion">-</span>
                </div>
                <div class="std-cell">
                    <span class="sc-lbl">{{ __('رقم الهوية / الجوال:') }}</span>
                    <span class="sc-val font-mono" id="stmtStudentIdPhone">-</span>
                </div>
            </div>

            {{-- ملخص الأرقام الكبرى للسند --}}
            <div class="sheet-kpi-row">
                <div class="sheet-kpi-item">
                    <span>{{ __('إجمالي الرسوم المقررة:') }}</span>
                    <strong class="font-mono" id="stmtTotalDue">0 ₪</strong>
                </div>
                <div class="sheet-kpi-item text-emerald">
                    <span>{{ __('إجمالي المبالغ المسددة:') }}</span>
                    <strong class="font-mono" id="stmtTotalPaid">0 ₪</strong>
                </div>
                <div class="sheet-kpi-item text-rose">
                    <span>{{ __('الرصيد المتبقي بذمة الطالب:') }}</span>
                    <strong class="font-mono" id="stmtTotalRemaining">0 ₪</strong>
                </div>
            </div>

            {{-- جدول المواد والرسوم الفصلية للطباعة --}}
            <div class="table-responsive" style="overflow-x: auto; width: 100%; max-width: 100%;">
                <table class="sheet-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('المادة الدراسية') }}</th>
                            <th>{{ __('الفصل الدراسي') }}</th>
                            <th>{{ __('الرسوم المقررة (₪)') }}</th>
                            <th>{{ __('المسدد فعلياً (₪)') }}</th>
                            <th>{{ __('الرصيد المتبقي (₪)') }}</th>
                            <th>{{ __('حالة الدفعة') }}</th>
                            <th>{{ __('تاريخ السداد / ملاحظات') }}</th>
                        </tr>
                    </thead>
                    <tbody id="stmtTableBody">
                        <!-- تُملأ ديناميكياً بواسطة JavaScript -->
                    </tbody>
                </table>
            </div>

            {{-- التواقيع والأختام الرسمية المعتمدة --}}
            <div class="sheet-footer-stamps">
                <div class="stamp-col">
                    <span class="stamp-title">{{ __('المشرف العام وإدارة المنصة') }}</span>
                    <div class="signature-line">م. أحمد شمالي</div>
                    <small>{{ __('Step by Step للتعليم الأكاديمي') }}</small>
                </div>

                <div class="stamp-col stamp-center">
                    <div class="official-seal-box">
                        <i class="fa-solid fa-certificate"></i>
                        <span>{{ __('ختم الشؤون المالية') }}</span>
                        <small>{{ __('Step by Step') }}</small>
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

<script>
    function yearString() {
        return '{{ $year }}';
    }

    // فتح مودال سداد وتعديل الرسوم الفصلية
    function openSemesterPaymentModal(studentId, studentName, semester, due, paid, status) {
        document.getElementById('formStudentId').value = studentId;
        document.getElementById('modalStudentNameTitle').innerText = studentName;
        
        const semTitle = (semester === 'term_1') ? '{{ __('الفصل الأول (Term 1)') }}' : ((semester === 'term_2') ? '{{ __('الفصل الثاني (Term 2)') }}' : '{{ __('كلا الفصلين (العام كامل)') }}');
        document.getElementById('modalSemesterSubtitle').innerText = semTitle + ' (' + yearString() + ')';

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

    // عند تغيير راديو الفصل داخل المودال
    function onSemesterRadioChange() {
        const studentId = document.getElementById('formStudentId').value;
        const row = document.getElementById(`student_row_${studentId}`);
        if (!row) return;

        const sem = document.querySelector('input[name="semester"]:checked')?.value || 'term_1';
        let due = 0;
        let paid = 0;
        let status = 'unpaid';

        if (sem === 'term_1') {
            due = parseFloat(row.dataset.term1Due || 0);
            paid = parseFloat(row.dataset.term1Paid || 0);
            status = row.dataset.term1Status || 'unpaid';
        } else if (sem === 'term_2') {
            due = parseFloat(row.dataset.term2Due || 0);
            paid = parseFloat(row.dataset.term2Paid || 0);
            status = row.dataset.term2Status || 'unpaid';
        } else {
            due = parseFloat(row.dataset.totalDue || 0);
            paid = parseFloat(row.dataset.totalPaid || 0);
            status = (paid >= due && due > 0) ? 'paid' : (paid > 0 ? 'partial' : 'unpaid');
        }

        document.getElementById('formSemAmount').value = due.toFixed(2);
        document.getElementById('formPaidAmount').value = (status === 'paid' && paid <= 0) ? due.toFixed(2) : paid.toFixed(2);

        document.querySelectorAll('input[name="status"]').forEach(r => r.checked = false);
        const effectiveStatus = (status === 'empty' || !status) ? 'unpaid' : status;
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

        axios.post("{{ route('admin.subscriptions.semester.update') }}", Object.fromEntries(formData))
        .then(res => {
            closeSemesterPaymentModal();
            const sId = formData.get('student_id');
            const row = document.getElementById(`student_row_${sId}`);
            const summary = res.data.summary;

            if (row && summary) {
                // تحديث سمات بيانات الصف
                row.dataset.term1Due = summary.term_1_due;
                row.dataset.term1Paid = summary.term_1_paid;
                row.dataset.term1Status = summary.term_1_status;
                row.dataset.term2Due = summary.term_2_due;
                row.dataset.term2Paid = summary.term_2_paid;
                row.dataset.term2Status = summary.term_2_status;
                row.dataset.totalDue = summary.total_due;
                row.dataset.totalPaid = summary.total_paid;
                row.dataset.totalRemaining = summary.total_remaining;
                if (res.data.subs) {
                    row.dataset.subs = JSON.stringify(res.data.subs);
                }

                // تحديث بطاقة الفصل الأول في الواجهة
                const t1Box = document.getElementById(`term1_box_${sId}`);
                if (t1Box) {
                    const dueSpan = t1Box.querySelector('.term-figures-row span:nth-child(1) strong');
                    const paidSpan = t1Box.querySelector('.term-figures-row span:nth-child(2) strong');
                    const remSpan = t1Box.querySelector('.term-figures-row span:nth-child(3) strong');
                    const pill = t1Box.querySelector('.term-status-pill');
                    const editBtn = t1Box.querySelector('.btn-term-quick-edit');

                    if (dueSpan) dueSpan.innerText = Math.round(summary.term_1_due) + ' ₪';
                    if (paidSpan) paidSpan.innerText = Math.round(summary.term_1_paid) + ' ₪';
                    if (remSpan) {
                        remSpan.innerText = Math.round(summary.term_1_remaining) + ' ₪';
                        remSpan.className = (summary.term_1_remaining > 0) ? 'text-rose font-bold' : 'text-emerald';
                    }
                    if (pill) {
                        pill.className = `term-status-pill st-${summary.term_1_status}`;
                        pill.innerHTML = getStatusPillContent(summary.term_1_status);
                    }
                    if (editBtn) {
                        editBtn.setAttribute('onclick', `openSemesterPaymentModal(${sId}, '${addslashes(row.dataset.studentName)}', 'term_1', ${summary.term_1_due}, ${summary.term_1_paid}, '${summary.term_1_status}')`);
                    }
                }

                // تحديث بطاقة الفصل الثاني في الواجهة
                const t2Box = document.getElementById(`term2_box_${sId}`);
                if (t2Box) {
                    const dueSpan = t2Box.querySelector('.term-figures-row span:nth-child(1) strong');
                    const paidSpan = t2Box.querySelector('.term-figures-row span:nth-child(2) strong');
                    const remSpan = t2Box.querySelector('.term-figures-row span:nth-child(3) strong');
                    const pill = t2Box.querySelector('.term-status-pill');
                    const editBtn = t2Box.querySelector('.btn-term-quick-edit');

                    if (dueSpan) dueSpan.innerText = Math.round(summary.term_2_due) + ' ₪';
                    if (paidSpan) paidSpan.innerText = Math.round(summary.term_2_paid) + ' ₪';
                    if (remSpan) {
                        remSpan.innerText = Math.round(summary.term_2_remaining) + ' ₪';
                        remSpan.className = (summary.term_2_remaining > 0) ? 'text-rose font-bold' : 'text-emerald';
                    }
                    if (pill) {
                        pill.className = `term-status-pill st-${summary.term_2_status}`;
                        pill.innerHTML = getStatusPillContent(summary.term_2_status);
                    }
                    if (editBtn) {
                        editBtn.setAttribute('onclick', `openSemesterPaymentModal(${sId}, '${addslashes(row.dataset.studentName)}', 'term_2', ${summary.term_2_due}, ${summary.term_2_paid}, '${summary.term_2_status}')`);
                    }
                }

                // تحديث شارات الموقف المالي الإجمالي
                const sDueEl = document.getElementById(`std_due_${sId}`);
                const sPaidEl = document.getElementById(`std_paid_${sId}`);
                const sRemEl = document.getElementById(`std_rem_${sId}`);

                if (sDueEl) sDueEl.innerText = Math.round(summary.total_due) + ' ₪';
                if (sPaidEl) sPaidEl.innerText = Math.round(summary.total_paid) + ' ₪';
                if (sRemEl) {
                    sRemEl.innerText = (summary.total_remaining > 0) ? Math.round(summary.total_remaining) + ' ₪ ⚠️' : '0 ₪ ✅';
                    const parentPill = sRemEl.closest('.fin-remaining');
                    if (parentPill) {
                        if (summary.total_remaining > 0) {
                            parentPill.classList.add('has-remaining-alert');
                            parentPill.classList.remove('is-clear');
                        } else {
                            parentPill.classList.remove('has-remaining-alert');
                            parentPill.classList.add('is-clear');
                        }
                    }
                }
            }

            // تحديث بطاقات الإحصائيات العامة للمنصة
            if (res.data.stats) {
                const s = res.data.stats;
                const expEl = document.getElementById('stat_total_expected');
                if (expEl) expEl.innerText = s.total_expected + ' ₪';

                const colEl = document.getElementById('stat_total_collected');
                if (colEl) colEl.innerText = s.total_collected + ' ₪';

                const remEl = document.getElementById('stat_total_remaining');
                if (remEl) remEl.innerText = s.total_remaining + ' ₪';

                const rateEl = document.getElementById('stat_collection_rate');
                if (rateEl) rateEl.innerText = s.collection_rate + '%';

                const progFill = document.getElementById('stat_progress_fill');
                if (progFill) progFill.style.width = Math.min(100, s.collection_rate) + '%';
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
            Swal.fire('{{ __('خطأ') }}', err.response?.data?.message || '{{ __('فشل تحديث بيانات الرسوم الفصلية') }}', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> {{ __('حفظ واعتماد التحديث') }}';
        });
    }

    function getStatusPillContent(st) {
        if (st === 'paid') return '<i class="fa-solid fa-circle-check"></i> {{ __('مسدد بالكامل ✅') }}';
        if (st === 'partial') return '<i class="fa-solid fa-circle-half-stroke"></i> {{ __('سداد جزئي ⚠️') }}';
        if (st === 'waived') return '<i class="fa-solid fa-tag"></i> {{ __('إعفاء / منحة 🏷️') }}';
        if (st === 'empty') return '<i class="fa-solid fa-minus"></i> {{ __('غير مسجل') }}';
        return '<i class="fa-solid fa-circle-xmark"></i> {{ __('غير مسدد ❌') }}';
    }

    function addslashes(str) {
        return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
    }

    // فتح مودال كشف الحساب والسند الرسمي المعتمد للطالب
    function openStudentStatementModal(studentId) {
        const row = document.getElementById(`student_row_${studentId}`);
        if (!row) return;

        document.getElementById('stmtStudentName').innerText = row.dataset.studentName || '-';
        document.getElementById('stmtStudentStage').innerText = row.dataset.studentStage || '-';
        document.getElementById('stmtStudentRegion').innerText = row.dataset.studentRegion || '-';
        document.getElementById('stmtStudentIdPhone').innerText = row.dataset.studentPhone || '-';

        document.getElementById('stmtTotalDue').innerText = Math.round(parseFloat(row.dataset.totalDue || 0)) + ' ₪';
        document.getElementById('stmtTotalPaid').innerText = Math.round(parseFloat(row.dataset.totalPaid || 0)) + ' ₪';
        document.getElementById('stmtTotalRemaining').innerText = Math.round(parseFloat(row.dataset.totalRemaining || 0)) + ' ₪';

        // تجميع بيانات المواد والرسوم الفصلية
        const tbody = document.getElementById('stmtTableBody');
        tbody.innerHTML = '';

        let subs = [];
        try {
            subs = JSON.parse(row.dataset.subs || '[]');
        } catch (e) {
            subs = [];
        }

        if (subs.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="8" class="text-center" style="padding: 24px; color: #64748b;">{{ __('لا توجد مواد مسجلة لهذا الطالب في العام الحالي') }}</td>`;
            tbody.appendChild(tr);
        } else {
            subs.forEach((item, index) => {
                const tr = document.createElement('tr');
                const rem = parseFloat(item.remaining_amount || 0);

                let badgeHtml = '';
                if (item.status === 'paid') {
                    badgeHtml = '<span class="sheet-status bg-p">{{ __('مسدد وخالص') }} ✅</span>';
                } else if (item.status === 'partial') {
                    badgeHtml = '<span class="sheet-status bg-part">{{ __('سداد جزئي') }} ⚠️</span>';
                } else if (item.status === 'waived') {
                    badgeHtml = '<span class="sheet-status bg-w">{{ __('إعفاء / منحة') }} 🏷️</span>';
                } else {
                    badgeHtml = '<span class="sheet-status bg-u">{{ __('غير مسدد') }} ❌</span>';
                }

                tr.innerHTML = `
                    <td class="font-mono text-center"><strong>${index + 1}</strong></td>
                    <td><strong>${item.subject_name}</strong></td>
                    <td class="text-center"><span class="sem-tag font-bold">${item.semester_name}</span></td>
                    <td class="font-mono text-center">${Math.round(item.amount)} ₪</td>
                    <td class="font-mono text-center text-emerald"><strong>${Math.round(item.paid_amount)} ₪</strong></td>
                    <td class="font-mono text-center ${rem > 0 ? 'text-rose font-bold' : 'text-emerald'}">${Math.round(rem)} ₪</td>
                    <td class="text-center">${badgeHtml}</td>
                    <td style="font-size: 0.8rem; color: #475569;">${item.paid_at !== '-' ? item.paid_at : ''} ${item.notes !== '-' ? item.notes : ''}</td>
                `;
                tbody.appendChild(tr);
            });
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

    window.onclick = function(e) {
        const modal1 = document.getElementById('editSemesterModal');
        const modal2 = document.getElementById('statementModal');
        if (e.target === modal1) closeSemesterPaymentModal();
        if (e.target === modal2) closeStatementModal();
    }
</script>

<style>
    /* =========================================================================
       التصميم الأكاديمي الملكي الكلاسيكي - منصة Step by Step
       مطابق للألوان والخطوط والترويسة الرسمية في صورة الشهادة
       ========================================================================= */
    .subs-matrix-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding-bottom: 50px;
    }

    /* أزرار اختيار الفصل في المودال */
    .semester-radio-group {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 6px;
    }
    .sem-radio-pill {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 14px;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.9rem;
        color: #334155;
        transition: all 0.2s ease;
    }
    .sem-radio-pill:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .sem-radio-pill input[type="radio"] {
        margin: 0;
        accent-color: #1e3a8a;
        width: 17px;
        height: 17px;
    }
    .sem-radio-pill:has(input[type="radio"]:checked) {
        border-color: #1e3a8a;
        background: #dbeafe;
        color: #1e3a8a;
        box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.2);
    }
    .sem-tag {
        display: inline-block;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 0.8rem;
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

    /* 2. العدادات الأكاديمية الكبرى (المستحق المطلوب، المحصل الفعلي، الرصيد المتبقي) */
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
    .btn-student-profile-link {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-student-profile-link:hover {
        background: #1e3a8a;
        color: #ffffff;
        border-color: #1e3a8a;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(30, 58, 138, 0.2);
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

    /* =================================================================
       نظام النوافذ والمودالات الملكية المعتمدة (Classic Royal Academic Modals)
       ================================================================= */
    .modal-overlay {
        position: fixed !important;
        inset: 0 !important;
        background: rgba(15, 23, 42, 0.72) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        z-index: 999999 !important;
        display: none;
        align-items: center !important;
        justify-content: center !important;
        padding: 20px !important;
        overflow-y: auto !important;
    }
    .modal-card-box {
        background: #ffffff !important;
        border-radius: 20px !important;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(226, 232, 240, 0.8) !important;
        width: 100% !important;
        max-width: 620px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative !important;
        margin: auto !important;
        padding: 28px 32px;
        animation: modalScaleIn 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    @keyframes modalScaleIn {
        from { opacity: 0; transform: scale(0.96) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .modal-header-royal {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 16px;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .modal-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 16px;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .modal-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .modal-crest {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #eff6ff;
        border: 1.5px solid #bfdbfe;
        display: grid;
        place-items: center;
        color: #1e3a8a;
        font-size: 1.35rem;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.08);
    }
    .modal-student-name {
        margin: 0;
        font-size: 1.2rem;
        color: #0f172a;
        font-weight: 800;
    }
    .modal-month-desc {
        margin: 3px 0 0;
        font-size: 0.82rem;
        color: #64748b;
    }

    .btn-close-x {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        font-size: 1.3rem;
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
        border-color: #fecaca;
        transform: rotate(90deg);
    }

    .form-body-wrap {
        padding: 18px 0 6px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .form-field-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .field-label {
        font-size: 0.86rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .field-label .required {
        color: #dc2626;
    }
    .clean-input {
        width: 100%;
        height: 46px;
        padding: 0 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 0.95rem;
        color: #0f172a;
        background: #ffffff;
        transition: all 0.2s ease;
        outline: none;
    }
    .clean-input:focus {
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3.5px rgba(30, 58, 138, 0.12);
    }
    .field-hint {
        font-size: 0.74rem;
        color: #64748b;
        margin-top: 2px;
    }

    /* شبكة خيارات الحالات بنمط كروت ملكية راقية */
    .status-options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 10px;
        margin-top: 6px;
    }
    .status-option-label {
        position: relative;
        cursor: pointer;
        display: block;
    }
    .status-option-label input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .status-option-label .opt-content {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 10px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    .status-option-label .opt-content i {
        font-size: 1.4rem;
        margin-bottom: 2px;
    }
    .status-option-label .opt-content strong {
        font-size: 0.86rem;
        font-weight: 800;
        color: #0f172a;
    }
    .status-option-label .opt-content small {
        font-size: 0.7rem;
        color: #64748b;
        line-height: 1.2;
    }
    .status-option-label:hover .opt-content {
        border-color: #cbd5e1;
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .status-option-label input[type="radio"]:checked + .opt-content {
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .opt-paid input[type="radio"]:checked + .opt-content {
        border-color: #059669;
        background: #ecfdf5;
    }
    .opt-paid .opt-content i { color: #059669; }
    .opt-paid input[type="radio"]:checked + .opt-content strong { color: #065f46; }

    .opt-partial input[type="radio"]:checked + .opt-content {
        border-color: #d97706;
        background: #fffbeb;
    }
    .opt-partial .opt-content i { color: #d97706; }
    .opt-partial input[type="radio"]:checked + .opt-content strong { color: #92400e; }

    .opt-unpaid input[type="radio"]:checked + .opt-content {
        border-color: #dc2626;
        background: #fef2f2;
    }
    .opt-unpaid .opt-content i { color: #dc2626; }
    .opt-unpaid input[type="radio"]:checked + .opt-content strong { color: #991b1b; }

    .opt-pending input[type="radio"]:checked + .opt-content {
        border-color: #2563eb;
        background: #eff6ff;
    }
    .opt-pending .opt-content i { color: #2563eb; }
    .opt-pending input[type="radio"]:checked + .opt-content strong { color: #1e40af; }

    .opt-waived input[type="radio"]:checked + .opt-content {
        border-color: #8b5cf6;
        background: #f5f3ff;
    }
    .opt-waived .opt-content i { color: #8b5cf6; }
    .opt-waived input[type="radio"]:checked + .opt-content strong { color: #5b21b6; }

    .amounts-calc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-top: 4px;
    }
    .quick-amount-presets {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
        flex-wrap: wrap;
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
        font-size: 0.74rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-preset:hover {
        background: #eff6ff;
        border-color: #1e3a8a;
        color: #1e3a8a;
    }

    .live-calc-box {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        margin: 6px 0;
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
        font-size: 0.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .is-paid { color: #059669; }
    .is-partial { color: #b45309; }
    .is-unpaid { color: #dc2626; }

    .modal-footer-row {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        align-items: center;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1.5px solid #e2e8f0;
    }
    .btn-save-sub {
        background: #1e3a8a;
        color: #ffffff;
        border: none;
        height: 44px;
        padding: 0 24px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
        transition: all 0.2s ease;
    }
    .btn-save-sub:hover {
        background: #1e40af;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.35);
    }
    .btn-cancel-sub {
        background: #f1f5f9;
        color: #475569;
        border: 1.5px solid #cbd5e1;
        height: 44px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-cancel-sub:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* مودال سند كشف الحساب الرسمي المعتمد (طباعة ملكية كالصورة) */
    .modal-statement-sheet-wrap {
        max-width: 900px;
        width: 95%;
        padding: 0;
        overflow: hidden;
    }
    .statement-toolbar {
        background: #0f172a;
        color: #ffffff;
        border-bottom: 1px solid #334155;
        padding: 14px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .statement-title-info {
        font-size: 0.92rem;
        color: #ffffff;
        font-weight: 700;
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
       التجاوب الكامل مع شاشات الجوال والأجهزة اللوحية
       ========================================================= */
    @media (max-width: 1024px) {
        .royal-header-frame {
            grid-template-columns: 1fr auto 1fr;
            gap: 12px;
        }
        .financial-kpi-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .list-header-row {
            display: none !important;
        }
        .student-matrix-row {
            grid-template-columns: 1fr !important;
            gap: 12px;
        }
    }

    @media (max-width: 768px) {
        .subs-matrix-wrapper {
            gap: 14px;
            padding-bottom: 24px;
        }
        .royal-academic-header-card {
            padding: 14px 16px;
            border-radius: 12px;
        }
        .royal-academic-header-card::before {
            inset: 2px;
        }
        .royal-header-frame {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 12px;
        }
        .header-col-ar, .header-col-en {
            text-align: center;
        }
        .state-title-ar {
            font-size: 1.15rem;
        }
        .state-title-en {
            font-size: 0.98rem;
        }
        .royal-toolbar-strip {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .toolbar-left-info {
            width: 100%;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 6px;
        }
        .toolbar-right-tools {
            width: 100%;
            flex-direction: column;
            gap: 8px;
        }
        .year-select-form {
            width: 100%;
            justify-content: space-between;
        }
        .year-dropdown {
            flex: 1;
        }
        .btn-royal-print-all {
            width: 100%;
            justify-content: center;
            text-align: center;
        }
        .financial-kpi-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px;
        }
        .kpi-card-royal {
            padding: 12px 14px;
        }
        .kpi-amount {
            font-size: 1.35rem;
        }
        .filter-box-card {
            padding: 12px 14px;
        }
        .filters-wrap {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .search-cell {
            min-width: 100%;
        }
        .filter-select, .btn-filter-submit {
            width: 100%;
        }
        .student-matrix-row {
            padding: 12px;
            border-radius: 10px;
        }
        .months-strip-grid {
            grid-template-columns: repeat(6, 1fr) !important;
            gap: 6px !important;
        }
        .student-actions-block {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .btn-statement-royal, .btn-student-profile-link {
            width: 100%;
            justify-content: center;
            text-align: center;
        }
        .modal-card-box {
            padding: 16px 14px !important;
            width: 95vw !important;
            max-width: 95vw !important;
            border-radius: 14px !important;
        }
        .modal-statement-sheet-wrap {
            width: 98vw !important;
            max-width: 98vw !important;
        }
        .statement-printable-sheet {
            padding: 16px 12px !important;
            min-height: auto !important;
        }
        .sheet-header {
            grid-template-columns: 1fr !important;
            text-align: center !important;
            gap: 8px !important;
        }
        .sheet-col-ar, .sheet-col-en {
            text-align: center !important;
        }
    }

    @media (max-width: 480px) {
        .financial-kpi-grid {
            grid-template-columns: 1fr !important;
        }
        .fin-pill-group {
            gap: 4px;
        }
        .fin-pill {
            padding: 3px 6px;
        }
        .fin-due strong, .fin-paid strong, .fin-remaining strong {
            font-size: 0.78rem;
        }
        .months-strip-grid {
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 5px !important;
        }
        .month-micro-badge {
            height: 40px;
        }
        .student-profile-block {
            align-items: flex-start;
        }
        .student-sub-line {
            gap: 4px;
        }
        .student-fee-badge-btn {
            font-size: 0.68rem;
        }
        .modal-footer-row {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .btn-save-sub, .btn-cancel-sub {
            width: 100%;
            justify-content: center;
        }
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
