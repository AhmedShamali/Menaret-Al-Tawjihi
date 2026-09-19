@extends('layouts.app')

@section('title', __('سند قبض مالي رسمي') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
@php
    $student = $payment->student;
    $studentName = (app()->getLocale() === 'en' && !empty($student?->name_en)) 
        ? $student->name_en 
        : ($student?->name_ar ?? $student?->name ?? __('طالب الثانوية العامة'));
    $stageName = (app()->getLocale() === 'en' && !empty($student?->stage?->name_en))
        ? $student->stage->name_en
        : (optional($student?->stage)->label_ar ?? optional($student?->stage)->name_ar ?? __('الثانوية العامة (التوجيهي)'));
    $studentNid = $student?->nid ?: ($student?->id_number ?: __('غير مسجل'));
    $studentEmail = $student?->email ?: '-';
    $studentPhone = $student?->phone ?: '-';

    $details = is_array($payment->payment_details) 
        ? $payment->payment_details 
        : json_decode($payment->payment_details, true);
    $refNumber = $details['reference_no'] ?? ($details['bop_ref'] ?? ($details['palpay_ref'] ?? ($details['wallet_phone'] ?? null)));
    $monthTarget = $details['month_target'] ?? null;
    $isPaid = ($payment->status === 'completed');
    $isPending = ($payment->status === 'pending');

    $amountInWords = \App\Support\Tafqeet::inArabic($payment->amount);
    $items = is_array($payment->items) ? $payment->items : json_decode($payment->items, true);
@endphp

<div class="school-voucher-page-wrapper">

    <!-- شريط الأوامر العلوي (يختفي في الطباعة) -->
    <div class="no-print voucher-top-actions">
        <div class="actions-left">
            <a href="{{ route('student.subjects.index') }}" class="btn-action-light">
                <i class="fa-solid fa-arrow-right"></i>
                <span>{{ __('العودة للمقررات والدروس') }}</span>
            </a>
            <a href="{{ route('student.subscriptions.index') }}" class="btn-action-light">
                <i class="fa-solid fa-calendar-check text-emerald"></i>
                <span>{{ __('سجل الاشتراكات الشهرية') }}</span>
            </a>
        </div>

        <div class="actions-right">
            <button type="button" onclick="window.print()" class="btn-action-print">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة السند المدرسي الرسمي (ورقة A4)') }}</span>
            </button>
        </div>
    </div>

    <!-- وثيقة سند القبض المالي المدرسي الكلاسيكي (Classical School Cash Receipt Voucher) -->
    <div class="school-cash-voucher-sheet" id="schoolPrintableReceipt">
        
        <!-- الإطار الرسمي المزدوج الكلاسيكي لسندات المدارس -->
        <div class="voucher-double-border">

            <!-- 1. ترويسة السند المالي الرسمية المعتمدة -->
            <header class="voucher-gov-header">
                <div class="gov-header-col right-col">
                    <div class="gov-text-line"><strong>دولة فلسطين</strong></div>
                    <div class="gov-text-line">وزارة التربية والتعليم العالي</div>
                    <div class="gov-text-line">منصة منارة التوجيهي للثانوية العامة</div>
                    <div class="gov-text-sub">الدائرة المالية • قسم الاشتراكات والتحصيل</div>
                </div>

                <div class="gov-header-col center-col">
                    <div class="voucher-official-emblem">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h1 class="voucher-headline">سَنَدُ قَبْضٍ مَالِيّ</h1>
                    <span class="voucher-headline-en">OFFICIAL FINANCIAL RECEIPT VOUCHER</span>
                    <div class="voucher-serial-tag">
                        <span>رقم السند:</span>
                        <strong class="font-mono">{{ $payment->transaction_number }}</strong>
                    </div>
                </div>

                <div class="gov-header-col left-col">
                    <table class="voucher-meta-mini-table">
                        <tr>
                            <td class="lbl">{{ __('التاريخ:') }}</td>
                            <td class="val font-mono">{{ $payment->created_at ? $payment->created_at->format('Y/m/d') : date('Y/m/d') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">{{ __('العام الدراسي:') }}</td>
                            <td class="val font-mono">{{ \App\Models\Setting::academicYear() }} م</td>
                        </tr>
                        <tr>
                            <td class="lbl">{{ __('حالة السند:') }}</td>
                            <td class="val">
                                @if($isPaid)
                                    <span class="state-badge-paid"><i class="fa-solid fa-check"></i> {{ __('مسدد ومقبوض') }}</span>
                                @elseif($isPending)
                                    <span class="state-badge-pending"><i class="fa-solid fa-clock"></i> {{ __('قيد التدقيق') }}</span>
                                @else
                                    <span class="state-badge-cancelled">{{ __('غير معتمد') }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </header>

            <div class="voucher-hairline"></div>

            <!-- 2. بيانات السند الكلاسيكية وسرد الإقرار المالي (Classical Statement Format) -->
            <div class="voucher-statement-block">
                
                <div class="statement-row">
                    <div class="statement-field full-width">
                        <span class="field-label">وصلنا من الطالب/ـة المكرم/ـة:</span>
                        <span class="field-content student-name-highlight">{{ $studentName }}</span>
                        <span class="field-label-inline">رقم الهوية:</span>
                        <span class="field-content font-mono">{{ $studentNid }}</span>
                        <span class="field-label-inline">الفرع الدراسي:</span>
                        <span class="field-content">{{ $stageName }}</span>
                    </div>
                </div>

                <div class="statement-row">
                    <div class="statement-field flex-2">
                        <span class="field-label">مبلغاً وقدره (بالأرقام):</span>
                        <span class="field-content font-mono bold-currency">{{ number_format($payment->amount, 2) }} ₪</span>
                        <span class="field-sub">(شيكل فلسطيني جديد)</span>
                    </div>
                    <div class="statement-field flex-3">
                        <span class="field-label">فقط وقدره كتابةً:</span>
                        <span class="field-content words-content">{{ $amountInWords }}</span>
                    </div>
                </div>

                <div class="statement-row">
                    <div class="statement-field full-width">
                        <span class="field-label">وذلك لقاء / عن:</span>
                        <span class="field-content">
                            @if($monthTarget)
                                سداد وتفعيل اشتراك رسوم ({{ $monthTarget }}) لمنهاج الثانوية العامة.
                            @else
                                سداد وتفعيل اشتراك المقررات والمباحث الدراسية المعتمدة المقيدة بالجدول المالي أدناه.
                            @endif
                        </span>
                    </div>
                </div>

                <div class="statement-row">
                    <div class="statement-field flex-1">
                        <span class="field-label">طريقة السداد / التحصيل:</span>
                        <span class="field-content">
                            <i class="fa-solid fa-building-columns"></i>
                            {{ __($payment->gateway_name_ar ?? $payment->gateway ?? 'سداد مصرفي معتمد') }}
                        </span>
                    </div>
                    <div class="statement-field flex-1">
                        <span class="field-label">رقم الحوالة / المرجع:</span>
                        <span class="field-content font-mono">{{ $refNumber ?: ('TXN-' . substr(md5($payment->id . $payment->created_at), 0, 8)) }}</span>
                    </div>
                    <div class="statement-field flex-1">
                        <span class="field-label">رقم هاتف المشترك:</span>
                        <span class="field-content font-mono" dir="ltr">{{ $studentPhone }}</span>
                    </div>
                </div>

            </div>

            <!-- 3. جدول بيان الرسوم والمقررات الدراسية المشمولة بالسند (Natural Financial Table) -->
            <div class="voucher-table-wrapper">
                <table class="voucher-natural-table">
                    <thead>
                        <tr>
                            <th style="width: 35px; text-align: center;">#</th>
                            <th style="text-align: right;">{{ __('بيان المقرر / بند القسط الدراسي') }}</th>
                            <th style="width: 130px; text-align: center;">{{ __('العام الأكاديمي') }}</th>
                            <th style="width: 95px; text-align: center;">{{ __('الرسوم المقررة') }}</th>
                            <th style="width: 85px; text-align: center;">{{ __('الخصم/الإعفاء') }}</th>
                            <th style="width: 105px; text-align: center;">{{ __('المقبوض فعلياً') }}</th>
                            <th style="width: 105px; text-align: center;">{{ __('المتبقي بذمته') }}</th>
                            <th style="width: 105px; text-align: center;">{{ __('حالة الاعتماد') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $lineCounter = 1; @endphp
                        @if(!empty($items))
                            @foreach($items as $item)
                                @php
                                    $itemSubName = (app()->getLocale() === 'en' && !empty($item['name_en'])) ? $item['name_en'] : ($item['name_ar'] ?? __('مادة دراسية'));
                                    $itemPrice = (float)($item['price'] ?? 0);
                                @endphp
                                <tr>
                                    <td class="text-center font-mono">{{ sprintf('%02d', $lineCounter++) }}</td>
                                    <td>
                                        <strong>{{ $itemSubName }}</strong>
                                        <span class="sub-item-note">({{ __('تفعيل كامل للمحاضرات وبنك الأسئلة والامتحانات') }})</span>
                                    </td>
                                    <td class="text-center font-mono">{{ \App\Models\Setting::academicYear() }}</td>
                                    <td class="text-center font-mono">{{ number_format($itemPrice, 2) }} ₪</td>
                                    <td class="text-center font-mono text-muted">0.00 ₪</td>
                                    <td class="text-center font-mono bold-text">{{ number_format($itemPrice, 2) }} ₪</td>
                                    <td class="text-center font-mono bold-text" style="color: #15803d;">0.00 ₪</td>
                                    <td class="text-center">
                                        @if($isPaid)
                                            <span class="natural-status-active">{{ __('مفعل ومعتمد ✅') }}</span>
                                        @else
                                            <span class="natural-status-pending">{{ __('قيد التدقيق ⏳') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center font-mono">01</td>
                                <td>
                                    <strong>{{ $monthTarget ? __('رسوم اشتراك :month المعتمد', ['month' => $monthTarget]) : __('رسوم الاشتراك الدراسي المعتمد لبرنامج التوجيهي') }}</strong>
                                    <span class="sub-item-note">({{ __('شامل المتابعة الأكاديمية وبنك الاختبارات') }})</span>
                                </td>
                                <td class="text-center font-mono">{{ \App\Models\Setting::academicYear() }}</td>
                                <td class="text-center font-mono">{{ number_format($payment->amount, 2) }} ₪</td>
                                <td class="text-center font-mono text-muted">0.00 ₪</td>
                                <td class="text-center font-mono bold-text">{{ number_format($payment->amount, 2) }} ₪</td>
                                <td class="text-center font-mono bold-text" style="color: #15803d;">0.00 ₪</td>
                                <td class="text-center">
                                    @if($isPaid)
                                        <span class="natural-status-active">{{ __('مفعل ومعتمد ✅') }}</span>
                                    @else
                                        <span class="natural-status-pending">{{ __('قيد التدقيق ⏳') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="voucher-total-summary-row">
                            <td colspan="3" class="total-label-cell">
                                <strong>{{ __('المجموع الإجمالي للسند المالي:') }}</strong>
                            </td>
                            <td class="text-center font-mono font-bold">{{ number_format($payment->amount, 2) }} ₪</td>
                            <td class="text-center font-mono">0.00 ₪</td>
                            <td class="text-center font-mono font-bold text-success">{{ number_format($payment->amount, 2) }} ₪</td>
                            <td class="text-center font-mono font-bold text-success">
                                {{ $isPaid ? '0.00 ₪' : '0.00 ₪' }}
                            </td>
                            <td class="text-center">
                                @if($isPaid)
                                    <strong style="color: #15803d; font-size: 0.8rem;">{{ __('خالص ومسدد ✅') }}</strong>
                                @else
                                    <strong style="color: #b45309; font-size: 0.8rem;">{{ __('قيد الاعتماد') }}</strong>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- 4. محضر الذمة المالية وبراءة الطالب (Financial Clearance Notice) -->
            <div class="voucher-clearance-box {{ $isPaid ? 'clearance-paid' : 'clearance-pending' }}">
                <div class="clearance-icon">
                    <i class="fa-solid {{ $isPaid ? 'fa-shield-check' : 'fa-hourglass-half' }}"></i>
                </div>
                <div class="clearance-text">
                    @if($isPaid)
                        <strong>{{ __('إقرار براءة الذمة المالية:') }}</strong>
                        <span>{{ __('تم استلام وقبض كامل الرسوم المقيدة أعلاه، وتعتبر ذمة الطالب/ـة') }} <u>{{ $studentName }}</u> {{ __('خالصة تماماً ومسددة بالكامل بنسبة 100% ولا يترتب عليه أي التزامات مالية عن هذا السند.') }}</span>
                    @else
                        <strong>{{ __('حالة التدقيق المصرفي:') }}</strong>
                        <span>{{ __('تم استلام إشعار التوريد برقم مرجعي (:ref) بمبلغ (:amt ₪)، والمعاملة قيد المطابقة البنكية تمهيداً للتفعيل النهائي.', ['ref' => $payment->transaction_number, 'amt' => number_format($payment->amount, 2)]) }}</span>
                    @endif
                </div>
                <div class="clearance-remaining">
                    <span class="rem-lbl">{{ __('المتبقي بذمة الطالب:') }}</span>
                    <strong class="rem-val font-mono">{{ $isPaid ? '0.00 ₪ (خالص بالكامل)' : '0.00 ₪ (بانتظار الاعتماد)' }}</strong>
                </div>
            </div>

            <!-- 5. الأختام والتواقيع الرسمية الثلاثية (Official Traditional Signatures & Stamp) -->
            <footer class="voucher-signatures-section">
                
                <!-- 1. توقيع أمين الصندوق / المحاسب المستلم -->
                <div class="sig-column">
                    <div class="sig-header">{{ __('أمين الصندوق / المحاسب المستلم') }}</div>
                    <div class="sig-space">
                        <span class="digital-stamp-text">معتمد مالياً</span>
                        <div class="sig-handwritten-line">........................................</div>
                    </div>
                    <div class="sig-name">{{ __('قسم الحسابات والتحصيل') }}</div>
                </div>

                <!-- 2. خاتم المنصة والاعتماد المالي الدائري الرسمي الأزرق -->
                <div class="sig-column stamp-center-col">
                    <div class="authentic-school-stamp">
                        <div class="stamp-outer-circle">
                            <div class="stamp-middle-circle">
                                <div class="stamp-text-arc-top">منارة التوجيهي • بوابة الثانوية العامة</div>
                                <div class="stamp-center-content">
                                    <i class="fa-solid fa-stamp stamp-inner-icon"></i>
                                    <div class="stamp-state-txt">{{ $isPaid ? 'معتمد ومقبوض' : 'قيد التدقيق' }}</div>
                                    <div class="stamp-gov-txt">دولة فلسطين</div>
                                </div>
                                <div class="stamp-text-arc-bottom">الدائرة المالية • {{ \App\Models\Setting::academicYear() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="stamp-caption">{{ __('خاتم السداد والاعتماد المالي الرسمي') }}</div>
                </div>

                <!-- 3. المشرف العام وإدارة المنظومة -->
                <div class="sig-column">
                    <div class="sig-header">{{ __('المشرف العام وإدارة المنصة') }}</div>
                    <div class="sig-space">
                        <span class="official-signature-facsimile">أحمد حسين شمالي</span>
                        <div class="sig-handwritten-line">........................................</div>
                    </div>
                    <div class="sig-name">{{ __('أ. أحمد حسين شمالي') }}</div>
                </div>

            </footer>

            <!-- شريط الملاحظة القانونية في أسفل السند -->
            <div class="voucher-legal-footer">
                <span>{{ __('ملاحظة هامة: هذا السند وثيقة مالية رسمية صادرة إلكترونياً عن منصة منارة التوجيهي وموثقة بالسجلات المصرفية. يعتبر السند لاغياً في حال أي تعديل أو شطب يدوي دون مصادقة الإدارة.') }}</span>
                <span class="footer-ref font-mono">{{ $payment->transaction_number }} • {{ date('Y-m-d H:i') }}</span>
            </div>

        </div>
    </div>

</div>

<style>
    /* ==========================================================================
       تصميم السند المدرسي الطبيعي الكلاسيكي (Classical Palestinian School Voucher)
       ========================================================================== */
    .school-voucher-page-wrapper {
        min-height: 100vh;
        background: #f1f5f9;
        padding: 24px 16px 60px;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-family: 'Alexandria', 'Tajawal', 'Segoe UI', Tahoma, sans-serif;
        color: #0f172a;
    }

    /* شريط الأزرار العلوي */
    .voucher-top-actions {
        width: 100%;
        max-width: 860px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .actions-left, .actions-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-action-light {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .btn-action-light:hover {
        background: #f8fafc;
        border-color: #1e3a8a;
        color: #1e3a8a;
    }

    .btn-action-print {
        background: #1e3a8a;
        color: #ffffff;
        border: 1px solid #1e3a8a;
        padding: 9px 20px;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
        transition: all 0.2s ease;
    }
    .btn-action-print:hover {
        background: #172554;
    }

    /* جسم ورقة السند الطبيعية */
    .school-cash-voucher-sheet {
        width: 100%;
        max-width: 860px;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 16px;
        border-radius: 4px;
        box-sizing: border-box;
    }

    /* الإطار المزدوج الكلاسيكي */
    .voucher-double-border {
        border: 2px solid #0f172a;
        outline: 1px solid #0f172a;
        outline-offset: -5px;
        padding: 20px 22px 14px;
        box-sizing: border-box;
        background: #ffffff;
    }

    /* ترويسة السند */
    .voucher-gov-header {
        display: grid;
        grid-template-columns: 1.2fr 1.4fr 1fr;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
    }

    .gov-header-col.right-col {
        text-align: right;
        font-size: 0.82rem;
        line-height: 1.45;
        color: #1e293b;
    }
    .gov-text-line strong {
        font-size: 0.96rem;
        color: #0f172a;
    }
    .gov-text-sub {
        font-size: 0.76rem;
        color: #64748b;
        margin-top: 2px;
    }

    .gov-header-col.center-col {
        text-align: center;
    }
    .voucher-official-emblem {
        width: 38px;
        height: 38px;
        margin: 0 auto 4px;
        border-radius: 50%;
        border: 1.5px solid #1e3a8a;
        color: #1e3a8a;
        display: grid;
        place-items: center;
        font-size: 1.15rem;
    }
    .voucher-headline {
        font-size: 1.55rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        letter-spacing: 0.5px;
        font-family: 'Amiri', 'Traditional Arabic', serif;
    }
    .voucher-headline-en {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        color: #475569;
        letter-spacing: 1.5px;
        margin-top: 1px;
    }
    .voucher-serial-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 0.76rem;
        margin-top: 4px;
        color: #1e3a8a;
    }

    .gov-header-col.left-col {
        text-align: left;
        display: flex;
        justify-content: flex-end;
    }
    .voucher-meta-mini-table {
        font-size: 0.76rem;
        border-collapse: collapse;
    }
    .voucher-meta-mini-table td {
        padding: 2px 6px;
    }
    .voucher-meta-mini-table .lbl {
        color: #475569;
        font-weight: 600;
        text-align: right;
    }
    .voucher-meta-mini-table .val {
        font-weight: 700;
        color: #0f172a;
        text-align: left;
    }

    .state-badge-paid {
        color: #15803d;
        font-weight: 800;
    }
    .state-badge-pending {
        color: #b45309;
        font-weight: 800;
    }
    .state-badge-cancelled {
        color: #b91c1c;
        font-weight: 800;
    }

    .voucher-hairline {
        height: 1.5px;
        background: #0f172a;
        margin: 6px 0 14px;
    }

    /* بيانات الإقرار الكلاسيكية */
    .voucher-statement-block {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        margin-bottom: 12px;
        font-size: 0.84rem;
        line-height: 1.8;
    }

    .statement-row {
        display: flex;
        align-items: baseline;
        gap: 12px;
        margin-bottom: 4px;
        flex-wrap: wrap;
    }
    .statement-row:last-child {
        margin-bottom: 0;
    }

    .statement-field {
        display: flex;
        align-items: baseline;
        gap: 6px;
        flex-wrap: wrap;
    }
    .statement-field.full-width {
        width: 100%;
    }
    .statement-field.flex-1 { flex: 1; min-width: 180px; }
    .statement-field.flex-2 { flex: 2; min-width: 200px; }
    .statement-field.flex-3 { flex: 3; min-width: 250px; }

    .field-label {
        color: #334155;
        font-weight: 700;
        white-space: nowrap;
    }
    .field-label-inline {
        color: #334155;
        font-weight: 700;
        margin-right: 12px;
        white-space: nowrap;
    }
    .field-content {
        color: #0f172a;
        font-weight: 700;
        border-bottom: 1px dotted #94a3b8;
        padding: 0 4px;
    }
    .student-name-highlight {
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 800;
    }
    .bold-currency {
        font-size: 0.98rem;
        color: #0f172a;
        font-weight: 800;
    }
    .field-sub {
        font-size: 0.72rem;
        color: #64748b;
    }
    .words-content {
        color: #1e3a8a;
        font-weight: 700;
    }

    /* جدول السند الطبيعي */
    .voucher-table-wrapper {
        margin-bottom: 12px;
    }

    .voucher-natural-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
    }
    .voucher-natural-table th, 
    .voucher-natural-table td {
        border: 1px solid #334155;
        padding: 6px 8px;
    }
    .voucher-natural-table thead th {
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 800;
        font-size: 0.78rem;
    }
    .voucher-natural-table tbody tr:nth-child(even) {
        background: #fcfcfc;
    }
    .sub-item-note {
        display: block;
        font-size: 0.68rem;
        color: #64748b;
        font-weight: 500;
    }
    .natural-status-active {
        color: #15803d;
        font-weight: 700;
        font-size: 0.74rem;
    }
    .natural-status-pending {
        color: #b45309;
        font-weight: 700;
        font-size: 0.74rem;
    }

    .voucher-total-summary-row td {
        background: #f8fafc;
        border-top: 2px solid #0f172a;
        border-bottom: 2px solid #0f172a;
    }
    .total-label-cell {
        text-align: right;
        font-size: 0.82rem;
        color: #0f172a;
    }

    /* صندوق براءة الذمة */
    .voucher-clearance-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        margin-bottom: 14px;
        gap: 12px;
        font-size: 0.78rem;
    }
    .clearance-paid {
        background: #f0fdf4;
        border-color: #86efac;
    }
    .clearance-pending {
        background: #fffbeb;
        border-color: #fde68a;
    }

    .clearance-icon {
        font-size: 1.25rem;
        color: #15803d;
        flex-shrink: 0;
    }
    .clearance-pending .clearance-icon {
        color: #b45309;
    }
    .clearance-text {
        flex: 1;
        color: #1e293b;
        line-height: 1.45;
    }
    .clearance-text strong {
        color: #0f172a;
    }
    .clearance-remaining {
        text-align: left;
        white-space: nowrap;
        background: #ffffff;
        padding: 4px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
    }
    .rem-lbl {
        display: block;
        font-size: 0.68rem;
        color: #64748b;
    }
    .rem-val {
        font-size: 0.82rem;
        color: #15803d;
        font-weight: 800;
    }

    /* قسم التواقيع والأختام الرسمية */
    .voucher-signatures-section {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
        align-items: center;
        padding: 8px 0 6px;
        text-align: center;
    }

    .sig-column {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .sig-header {
        font-size: 0.78rem;
        font-weight: 800;
        color: #334155;
        margin-bottom: 4px;
    }
    .sig-space {
        height: 52px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        width: 100%;
        position: relative;
    }
    .sig-handwritten-line {
        color: #94a3b8;
        font-size: 0.75rem;
        letter-spacing: 2px;
    }
    .digital-stamp-text {
        font-size: 0.72rem;
        color: #059669;
        font-weight: 800;
        margin-bottom: -2px;
    }
    .official-signature-facsimile {
        font-family: 'Amiri', 'Traditional Arabic', serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: -4px;
        transform: rotate(-2deg);
    }
    .sig-name {
        font-size: 0.76rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 4px;
    }

    /* الخاتم الحبري الدائري الأصيل */
    .authentic-school-stamp {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        margin: 0 auto;
        display: grid;
        place-items: center;
        transform: rotate(-3deg);
        filter: drop-shadow(0 1px 2px rgba(30, 58, 138, 0.15));
    }
    .stamp-outer-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 2px solid #1e3a8a;
        padding: 2px;
        display: grid;
        place-items: center;
        box-sizing: border-box;
    }
    .stamp-middle-circle {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 1px dashed #1e3a8a;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 3px 2px;
        box-sizing: border-box;
        text-align: center;
    }
    .stamp-text-arc-top {
        font-size: 0.52rem;
        font-weight: 800;
        color: #1e3a8a;
        line-height: 1;
    }
    .stamp-center-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .stamp-inner-icon {
        font-size: 0.8rem;
        color: #1e3a8a;
        margin-bottom: 1px;
    }
    .stamp-state-txt {
        font-size: 0.62rem;
        font-weight: 900;
        color: #b91c1c;
        border: 1px solid #b91c1c;
        padding: 1px 4px;
        border-radius: 2px;
        line-height: 1;
    }
    .stamp-gov-txt {
        font-size: 0.5rem;
        color: #1e3a8a;
        font-weight: 700;
        margin-top: 1px;
    }
    .stamp-text-arc-bottom {
        font-size: 0.48rem;
        font-weight: 700;
        color: #1e3a8a;
        line-height: 1;
    }
    .stamp-caption {
        font-size: 0.68rem;
        font-weight: 700;
        color: #475569;
        margin-top: 3px;
    }

    /* الذيل والملاحظة القانونية */
    .voucher-legal-footer {
        border-top: 1px solid #cbd5e1;
        margin-top: 10px;
        padding-top: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.66rem;
        color: #64748b;
        line-height: 1.4;
    }

    /* ==========================================================================
       محددات الطباعة الدقيقة (Strict A4 Single Sheet Guarantee)
       ========================================================================== */
    @media print {
        @page {
            size: A4 portrait;
            margin: 8mm 8mm 8mm 8mm;
        }

        html, body {
            background: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print,
        .sidebar,
        .navbar,
        .topbar,
        .footer,
        #topNavbar,
        #sideMenu {
            display: none !important;
        }

        .school-voucher-page-wrapper {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: auto !important;
        }

        .school-cash-voucher-sheet {
            max-width: 100% !important;
            width: 100% !important;
            box-shadow: none !important;
            padding: 0 !important;
            border-radius: 0 !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .voucher-double-border {
            border: 2px solid #000000 !important;
            outline: 1px solid #000000 !important;
            padding: 12px 14px 10px !important;
        }

        .voucher-gov-header {
            padding-bottom: 8px !important;
        }

        .voucher-statement-block {
            border-color: #000000 !important;
            padding: 8px 10px !important;
            margin-bottom: 8px !important;
            font-size: 0.8rem !important;
        }

        .voucher-natural-table th, 
        .voucher-natural-table td {
            border-color: #000000 !important;
            padding: 4px 6px !important;
            font-size: 0.75rem !important;
        }

        .voucher-clearance-box {
            padding: 6px 10px !important;
            margin-bottom: 8px !important;
            font-size: 0.74rem !important;
        }

        .sig-space {
            height: 40px !important;
        }

        .authentic-school-stamp {
            width: 72px !important;
            height: 72px !important;
        }
        .stamp-outer-circle {
            width: 70px !important;
            height: 70px !important;
        }
    }
</style>
@endsection
