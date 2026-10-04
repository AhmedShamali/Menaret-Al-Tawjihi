@extends('layouts.app')

@section('title', __('إدارة أسعار المواد والرسوم الفصلية') . ' - ' . config('app.name', 'Step by Step'))

@section('content')
<div class="academic-pricing-page" style="max-width: 1400px; margin: 0 auto; padding: 12px 4px 60px 4px;">

    {{-- 1. ترويسة الصفحة الرسمية الأكاديمية (هادئة وواضحة) --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 24px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1.25rem; border: 1px solid #bfdbfe; flex-shrink: 0;">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <h1 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; font-family: 'Alexandria', 'Cairo', sans-serif;">
                    {{ __('جدول تسعير المقررات والرسوم الأكاديمية') }}
                </h1>
                <div style="font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 6px;">
                    <a href="{{ route('admin.dashboard') }}" style="color: #64748b; text-decoration: none;">{{ __('الرئيسية') }}</a>
                    <span>/</span>
                    <span style="color: #1e293b; font-weight: 600;">{{ __('الرسوم والاشتراكات الفصلية') }}</span>
                    <span>•</span>
                    <span>{{ __('محافظات الضفة الغربية، القدس، وقطاع غزة') }}</span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('admin.subscriptions.monthly') }}" style="display: inline-flex; align-items: center; gap: 7px; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 0.84rem; text-decoration: none; transition: background 0.15s;">
                <i class="fa-solid fa-table-list" style="color: #64748b;"></i>
                <span>{{ __('سجل الاشتراكات والتحصيلات') }}</span>
            </a>
            <button type="button" onclick="openSeasonalModal()" style="display: inline-flex; align-items: center; gap: 7px; background: #1d4ed8; color: #ffffff; border: 1px solid #1e40af; padding: 9px 18px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; cursor: pointer; transition: background 0.15s; box-shadow: 0 1px 2px rgba(29, 78, 216, 0.15);">
                <i class="fa-solid fa-percent"></i>
                <span>{{ __('خصم موسمي شامل') }}</span>
            </button>
        </div>
    </div>

    {{-- رسائل التنبيه والنجاح --}}
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 8px; padding: 12px 18px; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; font-size: 0.88rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.1rem; color: #059669;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- 2. شريط المؤشرات الأكاديمية المدمج (Compact KPI Bar) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 20px;">
        
        <!-- كرت 1: إجمالي المواد -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
            <div>
                <span style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 2px;">
                    {{ __('إجمالي المواد المسجلة') }}
                </span>
                <span style="font-size: 1.35rem; font-weight: 800; color: #0f172a; font-family: 'Alexandria', sans-serif;">
                    {{ $pricingStats['total_subjects'] }} <small style="font-size: 0.78rem; font-weight: 600; color: #64748b;">{{ __('مادة') }}</small>
                </span>
            </div>
            <div style="width: 38px; height: 38px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1.05rem;">
                <i class="fa-solid fa-book-open"></i>
            </div>
        </div>

        <!-- كرت 2: متوسط الضفة والقدس -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
            <div>
                <span style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 2px;">
                    {{ __('متوسط رسوم الفصل (الضفة)') }}
                </span>
                <span style="font-size: 1.35rem; font-weight: 800; color: #1e40af; font-family: 'Alexandria', sans-serif;">
                    {{ $pricingStats['avg_term_wb'] }} <small style="font-size: 0.78rem; font-weight: 700; color: #1e40af;">₪</small>
                </span>
            </div>
            <div style="width: 38px; height: 38px; border-radius: 8px; background: #eff6ff; color: #1e40af; display: grid; place-items: center; font-size: 1.05rem;">
                <i class="fa-solid fa-landmark"></i>
            </div>
        </div>

        <!-- كرت 3: متوسط غزة -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
            <div>
                <span style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 2px;">
                    {{ __('متوسط رسوم الفصل (غزة)') }}
                </span>
                <span style="font-size: 1.35rem; font-weight: 800; color: #065f46; font-family: 'Alexandria', sans-serif;">
                    {{ $pricingStats['avg_term_gaza'] }} <small style="font-size: 0.78rem; font-weight: 700; color: #065f46;">₪</small>
                </span>
            </div>
            <div style="width: 38px; height: 38px; border-radius: 8px; background: #ecfdf5; color: #059669; display: grid; place-items: center; font-size: 1.05rem;">
                <i class="fa-solid fa-location-dot"></i>
            </div>
        </div>

        <!-- كرت 4: مواد مجانية -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
            <div>
                <span style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 2px;">
                    {{ __('المواد المجانية بالكامل') }}
                </span>
                <span style="font-size: 1.35rem; font-weight: 800; color: #d97706; font-family: 'Alexandria', sans-serif;">
                    {{ $pricingStats['free_subjects'] }} <small style="font-size: 0.78rem; font-weight: 600; color: #64748b;">{{ __('مادة') }}</small>
                </span>
            </div>
            <div style="width: 38px; height: 38px; border-radius: 8px; background: #fffbeb; color: #d97706; display: grid; place-items: center; font-size: 1.05rem;">
                <i class="fa-solid fa-gift"></i>
            </div>
        </div>

    </div>

    {{-- 3. البطاقة الرئيسية للجدول والتصفية (Classic Academic Ledger) --}}
    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); overflow: hidden;">
        
        {{-- شريط الفلاتر والبحث المدمج كلاسيكياً داخل البطاقة --}}
        <div style="padding: 12px 18px; background: #f8fafc; border-bottom: 1.5px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            
            {{-- تبويبات الفروع الأكاديمية (Flat Academic Segmented Tabs) --}}
            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                <span style="font-size: 0.8rem; font-weight: 700; color: #475569; margin-left: 4px;">
                    <i class="fa-solid fa-filter" style="color: #64748b;"></i> {{ __('الفرع:') }}
                </span>

                <a href="{{ route('admin.subjects.pricing') }}" style="padding: 6px 14px; border-radius: 6px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.15s; border: 1px solid {{ empty($stageId) ? '#1d4ed8' : '#cbd5e1' }}; background: {{ empty($stageId) ? '#1d4ed8' : '#ffffff' }}; color: {{ empty($stageId) ? '#ffffff' : '#334155' }};">
                    {{ __('كافة الفروع') }} ({{ $pricingStats['total_subjects'] }})
                </a>

                @foreach($stages as $stg)
                    @php
                        $stgRaw = $stg->label_ar ?? $stg->name ?? $stg->name_ar ?? 'المرحلة';
                        if (str_contains($stgRaw, 'علمي')) $stgTitle = __('الفرع العلمي');
                        elseif (str_contains($stgRaw, 'أدبي')) $stgTitle = __('الفرع الأدبي');
                        elseif (str_contains($stgRaw, 'ريادة') || str_contains($stgRaw, 'أعمال')) $stgTitle = __('الريادة والأعمال');
                        elseif (str_contains($stgRaw, 'صناعي')) $stgTitle = __('الفرع الصناعي');
                        elseif (str_contains($stgRaw, 'شرعي')) $stgTitle = __('الفرع الشرعي');
                        else $stgTitle = $stgRaw;

                        $isActive = ($stageId == $stg->id);
                    @endphp
                    <a href="{{ route('admin.subjects.pricing', ['stage_id' => $stg->id]) }}" style="padding: 6px 14px; border-radius: 6px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.15s; border: 1px solid {{ $isActive ? '#1d4ed8' : '#cbd5e1' }}; background: {{ $isActive ? '#1d4ed8' : '#ffffff' }}; color: {{ $isActive ? '#ffffff' : '#334155' }};">
                        {{ $stgTitle }}
                    </a>
                @endforeach
            </div>

            {{-- حقل البحث السريع المباشر --}}
            <div style="position: relative; min-width: 240px;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 10px; top: 10px; color: #94a3b8; font-size: 0.85rem;"></i>
                <input type="text" id="ledgerSearchInput" oninput="filterLedgerTable()" placeholder="{{ __('بحث فوري باسم المادة...') }}" style="width: 100%; height: 34px; padding: 0 32px 0 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem; color: #0f172a; outline: none; background: #ffffff;">
            </div>

        </div>

        {{-- جدول الأسعار الأكاديمي الكلاسيكي بنظام القيد المزدوج للمحافظات --}}
        <div style="overflow-x: auto;">
            <table id="academicLedgerTable" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 0.85rem; font-family: 'Alexandria', 'Cairo', sans-serif;">
                <thead>
                    {{-- الصف الأول من الترويسة --}}
                    <tr style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #334155; font-weight: 800; font-size: 0.8rem;">
                        <th rowspan="2" style="padding: 10px 14px; width: 44px; text-align: center; border-left: 1px solid #e2e8f0;">#</th>
                        <th rowspan="2" style="padding: 10px 16px; border-left: 1px solid #e2e8f0; min-width: 220px;">
                            {{ __('المادة الدراسية والمنهاج') }}
                        </th>
                        <th rowspan="2" style="padding: 10px 14px; width: 140px; text-align: center; border-left: 1px solid #e2e8f0;">
                            {{ __('الفرع الأكاديمي') }}
                        </th>
                        <th colspan="2" style="padding: 8px 12px; text-align: center; background: #eff6ff; color: #1e40af; border-left: 1px solid #bfdbfe; border-bottom: 1px solid #bfdbfe;">
                            <i class="fa-solid fa-landmark" style="margin-left: 4px;"></i> {{ __('محافظات الضفة الغربية والقدس') }}
                        </th>
                        <th colspan="2" style="padding: 8px 12px; text-align: center; background: #ecfdf5; color: #065f46; border-left: 1px solid #a7f3d0; border-bottom: 1px solid #a7f3d0;">
                            <i class="fa-solid fa-location-dot" style="margin-left: 4px;"></i> {{ __('محافظات قطاع غزة') }}
                        </th>
                        <th rowspan="2" style="padding: 10px 14px; width: 120px; text-align: center; border-left: 1px solid #e2e8f0;">
                            {{ __('النظام') }}
                        </th>
                        <th rowspan="2" style="padding: 10px 16px; width: 110px; text-align: center;">
                            {{ __('الإجراء') }}
                        </th>
                    </tr>
                    {{-- الصف الثاني من الترويسة لتقسيم الفصول --}}
                    <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 0.76rem;">
                        <th style="padding: 6px 10px; text-align: center; background: #f8fafc; border-left: 1px solid #e2e8f0; width: 130px;">
                            {{ __('فصل 1 / فصل 2') }}
                        </th>
                        <th style="padding: 6px 10px; text-align: center; background: #f1f5f9; border-left: 1px solid #cbd5e1; width: 120px; color: #1e40af;">
                            {{ __('الفصلين معاً') }}
                        </th>
                        <th style="padding: 6px 10px; text-align: center; background: #f8fafc; border-left: 1px solid #e2e8f0; width: 130px;">
                            {{ __('فصل 1 / فصل 2') }}
                        </th>
                        <th style="padding: 6px 10px; text-align: center; background: #f1f5f9; border-left: 1px solid #cbd5e1; width: 120px; color: #065f46;">
                            {{ __('الفصلين معاً') }}
                        </th>
                    </tr>
                </thead>
                <tbody style="font-variant-numeric: tabular-nums;">
                    @forelse($subjects as $index => $sub)
                        @php
                            $rawStage = optional($sub->stage)->label_ar ?? optional($sub->stage)->name ?? optional($sub->stage)->name_ar ?? 'توجيهي عام';
                            if (str_contains($rawStage, 'علمي')) $stageClean = 'العلمي';
                            elseif (str_contains($rawStage, 'أدبي')) $stageClean = 'الأدبي';
                            elseif (str_contains($rawStage, 'ريادة') || str_contains($rawStage, 'أعمال')) $stageClean = 'الريادة والأعمال';
                            elseif (str_contains($rawStage, 'صناعي')) $stageClean = 'الصناعي';
                            elseif (str_contains($rawStage, 'شرعي')) $stageClean = 'الشرعي';
                            else $stageClean = $rawStage;

                            $p1Wb = $sub->getSemesterPrice('term_1', 'west_bank');
                            $p2Wb = $sub->getSemesterPrice('term_2', 'west_bank');
                            $pFullWb = $sub->getSemesterPrice('both', 'west_bank');

                            $p1Gaza = $sub->getSemesterPrice('term_1', 'gaza');
                            $p2Gaza = $sub->getSemesterPrice('term_2', 'gaza');
                            $pFullGaza = $sub->getSemesterPrice('both', 'gaza');
                        @endphp
                        <tr class="ledger-row" style="border-bottom: 1px solid #e2e8f0; transition: background 0.1s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                            
                            {{-- الرقم --}}
                            <td style="padding: 10px 14px; text-align: center; color: #94a3b8; font-weight: 600; font-size: 0.8rem; border-left: 1px solid #f1f5f9;">
                                {{ $index + 1 }}
                            </td>

                            {{-- اسم المادة --}}
                            <td style="padding: 10px 16px; border-left: 1px solid #f1f5f9;">
                                <strong class="subject-name-cell" style="display: block; color: #0f172a; font-size: 0.9rem; font-weight: 700; margin-bottom: 2px;">
                                    {{ $sub->name_ar }}
                                </strong>
                                <span style="font-size: 0.74rem; color: #64748b;">
                                    <i class="fa-solid fa-play" style="font-size: 0.65rem; color: #1d4ed8;"></i> {{ $sub->contents_count ?? 0 }} {{ __('درس') }}
                                    • <i class="fa-solid fa-file-pen" style="font-size: 0.65rem; color: #059669;"></i> {{ $sub->exams_count ?? 0 }} {{ __('اختبار') }}
                                </span>
                            </td>

                            {{-- الفرع --}}
                            <td style="padding: 10px 14px; text-align: center; border-left: 1px solid #f1f5f9;">
                                <span style="display: inline-block; background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; padding: 3px 8px; border-radius: 4px; font-size: 0.76rem; font-weight: 700; white-space: nowrap;">
                                    {{ $stageClean }}
                                </span>
                            </td>

                            {{-- الضفة: ف1 / ف2 --}}
                            <td style="padding: 10px 12px; text-align: center; border-left: 1px solid #f1f5f9; font-weight: 700; color: #334155; font-size: 0.86rem;">
                                @if($sub->is_free)
                                    <span style="color: #059669; font-size: 0.8rem;">{{ __('مجاني') }}</span>
                                @else
                                    <span>{{ number_format($p1Wb, 0) }} ₪</span>
                                    <span style="color: #94a3b8; font-weight: 400; margin: 0 2px;">/</span>
                                    <span>{{ number_format($p2Wb, 0) }} ₪</span>
                                @endif
                            </td>

                            {{-- الضفة: الفصلين معاً --}}
                            <td style="padding: 10px 12px; text-align: center; border-left: 1px solid #e2e8f0; font-weight: 800; color: #1e40af; font-size: 0.9rem; background: #fafcff;">
                                @if($sub->is_free)
                                    <span style="color: #059669; font-size: 0.8rem;">0 ₪</span>
                                @else
                                    {{ number_format($pFullWb, 0) }} ₪
                                @endif
                            </td>

                            {{-- غزة: ف1 / ف2 --}}
                            <td style="padding: 10px 12px; text-align: center; border-left: 1px solid #f1f5f9; font-weight: 700; color: #334155; font-size: 0.86rem;">
                                @if($sub->is_free)
                                    <span style="color: #059669; font-size: 0.8rem;">{{ __('مجاني') }}</span>
                                @else
                                    <span>{{ number_format($p1Gaza, 0) }} ₪</span>
                                    <span style="color: #94a3b8; font-weight: 400; margin: 0 2px;">/</span>
                                    <span>{{ number_format($p2Gaza, 0) }} ₪</span>
                                @endif
                            </td>

                            {{-- غزة: الفصلين معاً --}}
                            <td style="padding: 10px 12px; text-align: center; border-left: 1px solid #e2e8f0; font-weight: 800; color: #065f46; font-size: 0.9rem; background: #fbfdfc;">
                                @if($sub->is_free)
                                    <span style="color: #059669; font-size: 0.8rem;">0 ₪</span>
                                @else
                                    {{ number_format($pFullGaza, 0) }} ₪
                                @endif
                            </td>

                            {{-- نظام الاشتراك --}}
                            <td style="padding: 10px 14px; text-align: center; border-left: 1px solid #f1f5f9;">
                                @if($sub->is_free)
                                    <span style="display: inline-block; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 2px 7px; border-radius: 4px; font-size: 0.72rem; font-weight: 700;">
                                        <i class="fa-solid fa-gift"></i> {{ __('مجاني') }}
                                    </span>
                                @else
                                    <span style="display: inline-block; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 2px 7px; border-radius: 4px; font-size: 0.72rem; font-weight: 700;">
                                        {{ __('نظام فصلي') }}
                                    </span>
                                @endif
                            </td>

                            {{-- الإجراء (تعديل التسعيرة) --}}
                            <td style="padding: 10px 16px; text-align: center;">
                                <button type="button" onclick='editPricing(@json($sub))' style="display: inline-flex; align-items: center; gap: 5px; background: #ffffff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 5px 12px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#1d4ed8'; this.style.color='#ffffff';" onmouseout="this.style.background='#ffffff'; this.style.color='#1d4ed8';">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                    <span>{{ __('تعديل') }}</span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 40px; text-align: center; color: #64748b;">
                                <i class="fa-solid fa-inbox" style="font-size: 2rem; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                                {{ __('لا توجد مواد مسجلة في هذا الفرع حالياً.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

{{-- 4. نافذة تعديل تسعيرة المادة الأكاديمية (Classic Modal) --}}
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 99999; place-items: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 10px; max-width: 580px; width: 100%; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); overflow: hidden; animation: modalFadeIn 0.15s ease;">
        
        {{-- رأس النافذة --}}
        <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <strong id="modalSubjectTitle" style="font-size: 1rem; color: #0f172a; font-weight: 800; font-family: 'Alexandria', sans-serif;">{{ __('تعديل تسعيرة المادة') }}</strong>
                <span style="display: block; font-size: 0.75rem; color: #64748b;">{{ __('حدد رسوم الفصل الأول، الفصل الثاني، وكامل العام للضفة وغزة') }}</span>
            </div>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- محتوى الفورم --}}
        <form id="editPricingForm" onsubmit="submitPricing(event)" style="padding: 20px;">
            <input type="hidden" id="editSubjectId">

            {{-- 1. تسعيرة محافظات الضفة والقدس --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 14px;">
                <div style="font-size: 0.84rem; font-weight: 800; color: #1e40af; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-landmark"></i>
                    <span>{{ __('محافظات الضفة الغربية والقدس الشريف (شيكل ₪)') }}</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <div>
                        <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569; margin-bottom: 4px;">{{ __('الفصل الأول') }}</label>
                        <input type="number" id="modalPriceTerm1Wb" oninput="calcWbFullPreview()" min="0" required style="width: 100%; height: 36px; padding: 0 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; font-weight: 700; color: #0f172a;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569; margin-bottom: 4px;">{{ __('الفصل الثاني') }}</label>
                        <input type="number" id="modalPriceTerm2Wb" oninput="calcWbFullPreview()" min="0" required style="width: 100%; height: 36px; padding: 0 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; font-weight: 700; color: #0f172a;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #1e40af; margin-bottom: 4px;">{{ __('الفصلين معاً (عرض)') }}</label>
                        <input type="number" id="modalPriceFullWb" min="0" required style="width: 100%; height: 36px; padding: 0 10px; border: 1.5px solid #bfdbfe; background: #eff6ff; border-radius: 6px; font-size: 0.9rem; font-weight: 800; color: #1e40af;">
                    </div>
                </div>
            </div>

            {{-- 2. تسعيرة محافظات قطاع غزة --}}
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px; margin-bottom: 14px;">
                <div style="font-size: 0.84rem; font-weight: 800; color: #065f46; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ __('محافظات قطاع غزة (تسعيرة مراعية للظروف ₪)') }}</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <div>
                        <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569; margin-bottom: 4px;">{{ __('الفصل الأول') }}</label>
                        <input type="number" id="modalPriceTerm1Gaza" oninput="calcGazaFullPreview()" min="0" required style="width: 100%; height: 36px; padding: 0 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; font-weight: 700; color: #0f172a;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569; margin-bottom: 4px;">{{ __('الفصل الثاني') }}</label>
                        <input type="number" id="modalPriceTerm2Gaza" oninput="calcGazaFullPreview()" min="0" required style="width: 100%; height: 36px; padding: 0 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; font-weight: 700; color: #0f172a;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #065f46; margin-bottom: 4px;">{{ __('الفصلين معاً (عرض)') }}</label>
                        <input type="number" id="modalPriceFullGaza" min="0" required style="width: 100%; height: 36px; padding: 0 10px; border: 1.5px solid #a7f3d0; background: #ffffff; border-radius: 6px; font-size: 0.9rem; font-weight: 800; color: #065f46;">
                    </div>
                </div>
            </div>

            {{-- 3. خيار مادة مجانية بالكامل --}}
            <label style="display: flex; align-items: center; gap: 8px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; cursor: pointer;">
                <input type="checkbox" id="modalIsFree" onchange="onIsFreeToggle()" style="width: 16px; height: 16px; accent-color: #d97706; cursor: pointer;">
                <span style="font-size: 0.82rem; font-weight: 700; color: #92400e;">
                    {{ __('إتاحة هذه المادة مجاناً لكافة الطلبة (0 شيكل لكلا الفصلين)') }}
                </span>
            </label>

            {{-- أزرار الحفظ والإلغاء --}}
            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                <button type="button" onclick="closeEditModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    {{ __('إلغاء') }}
                </button>
                <button type="submit" id="btnSavePrice" style="background: #1d4ed8; color: #ffffff; border: none; padding: 8px 20px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    <i class="fa-solid fa-check"></i> {{ __('حفظ التسعيرة الأكاديمية') }}
                </button>
            </div>
        </form>

    </div>
</div>

{{-- 5. نافذة الخصم الموسمي الشامل --}}
<div id="seasonalModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 99999; place-items: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 10px; max-width: 480px; width: 100%; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); overflow: hidden;">
        <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <strong style="font-size: 0.98rem; color: #0f172a; font-weight: 800;">{{ __('تطبيق خصم موسمي موحد') }}</strong>
                <span style="display: block; font-size: 0.75rem; color: #64748b;">{{ __('تخفيض نسبة مئوية موحدة على جميع المواد أو فرع محدد') }}</span>
            </div>
            <button type="button" onclick="closeSeasonalModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.subjects.pricing.seasonal') }}" method="POST" style="padding: 20px;">
            @csrf
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">{{ __('نسبة الخصم المئوية (%)') }}</label>
                <input type="number" name="discount_percentage" min="1" max="90" required placeholder="مثلاً: 20" style="width: 100%; height: 38px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; font-weight: 700;">
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">{{ __('تطبيق على فرع محدد (اختياري)') }}</label>
                <select name="stage_id" style="width: 100%; height: 38px; padding: 0 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; background: #fff;">
                    <option value="">{{ __('جميع فروع الثانوية العامة') }}</option>
                    @foreach($stages as $stg)
                        <option value="{{ $stg->id }}">{{ $stg->label_ar ?? $stg->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeSeasonalModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    {{ __('إلغاء') }}
                </button>
                <button type="submit" style="background: #1d4ed8; color: #ffffff; border: none; padding: 8px 20px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    {{ __('تطبيق الخصم فوراً') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // البحث الحي الفوري في الجدول
    function filterLedgerTable() {
        const query = document.getElementById('ledgerSearchInput').value.trim().toLowerCase();
        const rows = document.querySelectorAll('#academicLedgerTable tbody tr.ledger-row');

        rows.forEach(row => {
            const nameEl = row.querySelector('.subject-name-cell');
            if (nameEl) {
                const text = nameEl.textContent.trim().toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            }
        });
    }

    // فتح نافذة التعديل
    function editPricing(sub) {
        document.getElementById('editSubjectId').value = sub.id;
        document.getElementById('modalSubjectTitle').textContent = 'تعديل تسعيرة: ' + sub.name_ar;
        
        const isFree = (sub.is_free == 1);
        document.getElementById('modalIsFree').checked = isFree;

        const t1Wb = parseFloat(sub.price_term_1) || 75;
        const t2Wb = parseFloat(sub.price_term_2) || 75;
        const fullWb = parseFloat(sub.price_full_year) || (t1Wb + t2Wb);

        const t1Gaza = parseFloat(sub.price_term_1_gaza) || 45;
        const t2Gaza = parseFloat(sub.price_term_2_gaza) || 45;
        const fullGaza = parseFloat(sub.price_full_year_gaza) || (t1Gaza + t2Gaza);

        document.getElementById('modalPriceTerm1Wb').value = t1Wb;
        document.getElementById('modalPriceTerm2Wb').value = t2Wb;
        document.getElementById('modalPriceFullWb').value = fullWb;

        document.getElementById('modalPriceTerm1Gaza').value = t1Gaza;
        document.getElementById('modalPriceTerm2Gaza').value = t2Gaza;
        document.getElementById('modalPriceFullGaza').value = fullGaza;

        onIsFreeToggle();

        document.getElementById('editModal').style.display = 'grid';
    }

    function calcWbFullPreview() {
        const t1 = parseFloat(document.getElementById('modalPriceTerm1Wb').value) || 0;
        const t2 = parseFloat(document.getElementById('modalPriceTerm2Wb').value) || 0;
        document.getElementById('modalPriceFullWb').value = (t1 + t2);
    }

    function calcGazaFullPreview() {
        const t1 = parseFloat(document.getElementById('modalPriceTerm1Gaza').value) || 0;
        const t2 = parseFloat(document.getElementById('modalPriceTerm2Gaza').value) || 0;
        document.getElementById('modalPriceFullGaza').value = (t1 + t2);
    }

    function onIsFreeToggle() {
        const isFree = document.getElementById('modalIsFree').checked;
        const fields = [
            'modalPriceTerm1Wb', 'modalPriceTerm2Wb', 'modalPriceFullWb',
            'modalPriceTerm1Gaza', 'modalPriceTerm2Gaza', 'modalPriceFullGaza'
        ];
        fields.forEach(f => {
            const el = document.getElementById(f);
            el.disabled = isFree;
            if (isFree) el.value = 0;
        });
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function openSeasonalModal() {
        document.getElementById('seasonalModal').style.display = 'grid';
    }

    function closeSeasonalModal() {
        document.getElementById('seasonalModal').style.display = 'none';
    }

    // إرسال التعديل عبر Axios
    function submitPricing(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSavePrice');
        const id = document.getElementById('editSubjectId').value;
        const isFree = document.getElementById('modalIsFree').checked ? 1 : 0;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';

        const payload = {
            price_term_1: document.getElementById('modalPriceTerm1Wb').value,
            price_term_2: document.getElementById('modalPriceTerm2Wb').value,
            price_full_year: document.getElementById('modalPriceFullWb').value,
            price_term_1_gaza: document.getElementById('modalPriceTerm1Gaza').value,
            price_term_2_gaza: document.getElementById('modalPriceTerm2Gaza').value,
            price_full_year_gaza: document.getElementById('modalPriceFullGaza').value,
            is_free: isFree,
        };

        axios.post(`/admin/subjects/pricing/${id}/update`, payload)
        .then(res => {
            closeEditModal();
            Swal.fire({
                icon: 'success',
                title: 'تم التحديث بنجاح!',
                text: res.data.message || 'تم تحديث تسعيرة المادة بنجاح.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> حفظ التسعيرة الأكاديمية';
            const msg = err.response?.data?.message || 'تعذر تحديث التسعيرة.';
            Swal.fire({ icon: 'error', title: 'خطأ', text: msg });
        });
    }

    // إغلاق النوافذ عند النقر بالخلفية
    ['editModal', 'seasonalModal'].forEach(id => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) this.style.display = 'none';
            });
        }
    });
</script>
@endsection
