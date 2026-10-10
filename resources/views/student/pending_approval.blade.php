@extends('layouts.app')

@php
    $isFrozen = in_array($student->status, ['suspended', 'frozen', 'inactive']);
    $studentDispName = (app()->getLocale() === 'en' && !empty($student->name_en)) 
        ? $student->name_en 
        : ($student->name_ar ?? $student->name ?? __('طالبنا العزيز'));
    $stageDispName = (app()->getLocale() === 'en' && !empty($student?->stage?->name_en))
        ? $student?->stage?->name_en
        : ($student?->stage?->label_ar ?? $student?->stage?->name_ar ?? __('الثانوية العامة (التوجيهي)'));

    // الأرقام الرسمية المعتمدة للمنصة
    $transferNumber = '0567897212';
    $directorWa = '00970597694385';
    $directorWaClean = '970597694385';
@endphp

@section('title', $isFrozen ? __('الحساب مجمد مؤقتاً | Step by Step') : __('بانتظار موافقة الإدارة وتفعيل الاشتراك | Step by Step'))

@section('content')
<div class="pending-approval-wrapper">

    <div class="pending-approval-card {{ $isFrozen ? 'frozen-card-border' : '' }}">
        @if($isFrozen)
            <!-- أيقونة الحساب المجمد -->
            <div class="pending-icon-bubble frozen-bubble">
                <i class="fa-solid fa-user-lock"></i>
            </div>

            <!-- شارات الحالة الرسمية -->
            <div class="status-badges-row">
                <span class="badge-tag danger"><i class="fa-solid fa-lock"></i> {{ __('الحساب مجمد بقرار إداري') }}</span>
                <span class="badge-tag palestine"><i class="fa-solid fa-landmark"></i> {{ __('Step by Step - فلسطين') }}</span>
            </div>

            <h1 class="card-title text-danger">{{ __('تم تجميد حساب الطالب مؤقتاً') }}</h1>
            
            <p class="card-desc">
                {{ __('نحيطك علماً يا') }} <strong>{{ $studentDispName }}</strong> {{ __('بأنه قد تم إيقاف وتجميد صلاحيات حسابك الدراسي مؤقتاً بقرار من إدارة المنصة.') }}
            </p>

            <!-- صندوق سبب التجميد الرسمي -->
            <div class="freeze-reason-official-card">
                <div class="freeze-reason-header">
                    <div class="freeze-icon-wrap">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="freeze-header-title">{{ __('سبب التجميد المسجل لدى إدارة المنصة:') }}</h3>
                        <p class="freeze-header-sub">{{ __('بيان إداري رسمي صادر عن المشرف العام') }}</p>
                    </div>
                </div>

                <div class="freeze-reason-quote-box">
                    <i class="fa-solid fa-quote-right quote-mark"></i>
                    <div class="freeze-reason-statement">
                        {{ $student->freeze_reason ? __($student->freeze_reason) : __('عدم سداد الرسوم الدراسية أو مراجعة النشاط الأكاديمي والالتزام.') }}
                    </div>
                </div>

                <div class="freeze-impact-note">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ __('يترتب على هذا الإجراء إيقاف مؤقت للوصول إلى الدروس المصورة، الملازم، وبنك الامتحانات حتى مراجعة الإدارة وفك التجميد.') }}</span>
                </div>
            </div>

            <!-- زر التواصل المباشر مع المشرف العام عبر واتساب لفك التجميد -->
            <div class="unfreeze-actions-strip">
                @php
                    $waUnfreeze = urlencode(app()->getLocale() === 'ar'
                        ? ("السلام عليكم م.أحمد شمالي، أنا الطالب (" . ($student->name_ar ?? $student->name) . ") ورقم هويتي (" . ($student->nid ?? '-') . ")، حسابي مجمد على المنصة بسبب: [" . ($student->freeze_reason ?: 'عدم سداد الرسوم أو مراجعة الإدارة') . "]. أرجو التكرم بمساعدتي لفك التجميد وإعادة تفعيل الحساب.")
                        : ("Hello Eng. Ahmed Shamali, I am student (" . ($student->name_en ?? $student->name) . ") ID (" . ($student->nid ?? '-') . "), my account is frozen. Please assist me in unfreezing and reactivating my account."));
                @endphp
                <a href="https://wa.me/{{ $directorWaClean }}?text={{ $waUnfreeze }}" 
                   target="_blank" 
                   class="btn-whatsapp-unfreeze">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>{{ __('تواصل مباشرة مع المشرف العام لفك التجميد (واتساب: :num)', ['num' => $directorWa]) }}</span>
                </a>
            </div>

        @else
            <!-- أيقونة الاعتماد الأكاديمي الفاتحة -->
            <div class="pending-icon-bubble">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <!-- شارات الحالة الرسمية -->
            <div class="status-badges-row">
                <span class="badge-tag pending"><i class="fa-solid fa-clock-rotate-left"></i> {{ __('قيد المراجعة والاعتماد الأكاديمي') }}</span>
                <span class="badge-tag palestine"><i class="fa-solid fa-landmark"></i> {{ __('Step by Step - فلسطين') }}</span>
            </div>

            <h1 class="card-title">{{ __('طلب التحاق الطالب قيد الاعتماد الأكاديمي') }}</h1>
            
            <p class="card-desc">
                {{ __('أهلاً بك يا') }} <strong>{{ $studentDispName }}</strong>{{ __('! تم استلام طلب التحاقك واكتمال تسجيلك المبدئي بنجاح.') }}
                {{ __('يقوم المشرف العام') }} <strong>({{ __('م.أحمد شمالي') }})</strong> {{ __('بمراجعة بياناتك واعتماد اشتراكك في المواد التعليمية فور تسديد الرسوم الأكاديمية المقررة.') }}
            </p>
        @endif

        <!-- بطاقة تفاصيل الطالب المسجلة (تصميم كلاسيكي راقي) -->
        <div class="student-info-strip">
            <div class="info-cell">
                <small><i class="fa-regular fa-user" style="margin-left: 4px;"></i>{{ __('اسم الطالب') }}</small>
                <strong>{{ $studentDispName }}</strong>
            </div>
            <div class="info-cell">
                <small><i class="fa-solid fa-graduation-cap" style="margin-left: 4px;"></i>{{ __('المرحلة والفرع') }}</small>
                <strong>{{ $stageDispName }}</strong>
            </div>
            <div class="info-cell">
                <small><i class="fa-solid fa-map-location-dot" style="margin-left: 4px;"></i>{{ __('المنطقة والمنهاج') }}</small>
                <strong>{{ $student->region_label }}</strong>
            </div>
            <div class="info-cell">
                <small><i class="fa-solid fa-phone" style="margin-left: 4px;"></i>{{ __('رقم التواصل') }}</small>
                <strong dir="ltr">{{ $student->phone ?? '—' }}</strong>
            </div>
            <div class="info-cell">
                <small><i class="fa-solid fa-shield-halved" style="margin-left: 4px;"></i>{{ __('حالة الحساب') }}</small>
                @if($isFrozen)
                    <span class="status-pill-danger"><i class="fa-solid fa-lock"></i> {{ __('مجمد مؤقتاً') }}</span>
                @else
                    <span class="status-pill-warning"><i class="fa-solid fa-clock-rotate-left"></i> {{ __('بانتظار الاعتماد') }}</span>
                @endif
            </div>
        </div>

        <!-- بطاقة بيان الرسوم الدراسية الأكاديمية المعتمدة (سند كلاسيكي راقي) -->
        <div class="academic-voucher-card">
            
            {{-- ترويسة السند الرسمية الكلاسيكية --}}
            <div class="academic-voucher-header">
                <div class="voucher-header-info">
                    <div class="voucher-header-seal">
                        <i class="fa-solid fa-landmark"></i>
                    </div>
                    <div>
                        <h3 class="voucher-title">
                            {{ __('بيان الرسوم الدراسية للمقررات الأكاديمية المعتمدة') }}
                        </h3>
                        <p class="voucher-subtitle">
                            {{ __('المواد والمباحث الدراسية المسجلة بحسابك') }} • {{ __('نظام الفصول الدراسية وتسعيرة :region المعتمدة', ['region' => $student->region_label]) }}
                        </p>
                    </div>
                </div>

                <div class="voucher-header-tags">
                    <span class="v-tag primary">
                        <i class="fa-solid fa-book-open"></i> {{ count($feeBreakdown['items'] ?? []) }} {{ __('مباحث مسجلة') }}
                    </span>
                    <span class="v-tag slate">
                        {{ $stageDispName }}
                    </span>
                </div>
            </div>

            {{-- جدول المقررات والرسوم الأكاديمي الكلاسيكي --}}
            <div class="table-responsive">
                <table class="academic-ledger-table">
                    <thead>
                        <tr>
                            <th class="col-num">#</th>
                            <th>{{ __('المقرر الدراسي') }}</th>
                            <th>{{ __('الفصل الدراسي') }}</th>
                            <th class="col-curr">{{ __('الرسوم المقررة') }}</th>
                            <th class="col-curr">{{ __('خصم الباقة') }}</th>
                            <th class="col-curr">{{ __('الصافي المستحق') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $hasBundle = isset($bundleDiscount) && $bundleDiscount > 0;
                            $bundlePercent = 0.15;
                        @endphp
                        @forelse($feeBreakdown['items'] ?? [] as $index => $item)
                            @php
                                $origPrice = (float)($item['orig_price'] ?? $item['price']);
                                $itemDiscount = $hasBundle ? round($origPrice * $bundlePercent, 2) : 0;
                                $itemNet = max(0, round($origPrice - $itemDiscount, 2));
                            @endphp
                            <tr>
                                <td class="col-num-cell">{{ $index + 1 }}</td>
                                <td>
                                    <div class="subject-title-cell">
                                        <span class="subj-icon">{{ $item['icon'] ?? '📘' }}</span>
                                        <strong>{{ $item['name_ar'] }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="semester-pill">
                                        {{ $item['semester_label'] ?? __('الفصلين معاً') }}
                                    </span>
                                </td>
                                <td class="col-curr-cell orig-price font-mono">
                                    {{ number_format($origPrice, 0) }} ₪
                                </td>
                                <td class="col-curr-cell discount-price font-mono">
                                    @if($itemDiscount > 0)
                                        - {{ number_format($itemDiscount, 0) }} ₪
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="col-curr-cell net-price font-mono">
                                    {{ number_format($itemNet, 0) }} ₪
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="table-empty-cell">
                                    {{ __('سيتم احتساب تفاصيل المواد فور اكتمال اعتماد التسجيل.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        {{-- إجمالي الرسوم الأساسية --}}
                        <tr class="tfoot-row-subtotal">
                            <td colspan="4" class="tfoot-label">
                                {{ __('إجمالي الرسوم الدراسية المقررة لكافة المقررات:') }}
                            </td>
                            <td colspan="2" class="tfoot-val font-mono">
                                {{ number_format($totalAmount ?? $monthlyFee, 0) }} ₪
                            </td>
                        </tr>

                        {{-- خصم باقة التوجيهي إن وجد --}}
                        @if(isset($bundleDiscount) && $bundleDiscount > 0)
                            <tr class="tfoot-row-bundle">
                                <td colspan="4" class="tfoot-label bundle-text">
                                    <i class="fa-solid fa-tags"></i>
                                    {{ __('خصم باقة التوجيهي (15%):') }}
                                    <small>({{ __('مطبق لاشتراكك في :count مواد', ['count' => count($feeBreakdown['items'] ?? [])]) }})</small>
                                </td>
                                <td colspan="2" class="tfoot-val bundle-text font-mono">
                                    - {{ number_format($bundleDiscount, 0) }} ₪
                                </td>
                            </tr>
                        @endif

                        {{-- المنحة أو الخصم الخاص إن وجد --}}
                        @if(isset($discountAmount) && $discountAmount > 0)
                            <tr class="tfoot-row-scholarship">
                                <td colspan="4" class="tfoot-label scholarship-text">
                                    <i class="fa-solid fa-gift"></i>
                                    {{ __('منحة دراسية خاصة معتمدة من الإدارة:') }}
                                </td>
                                <td colspan="2" class="tfoot-val scholarship-text font-mono">
                                    - {{ number_format($discountAmount, 0) }} ₪
                                </td>
                            </tr>
                        @endif

                        {{-- المتأخرات السابقة إن وجدت --}}
                        @if(isset($financialSummary) && $financialSummary['has_arrears'])
                            <tr class="tfoot-row-arrears">
                                <td colspan="4" class="tfoot-label arrears-text">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    {{ __('المتأخرات السابقة المستحقة:') }}
                                </td>
                                <td colspan="2" class="tfoot-val arrears-text font-mono">
                                    + {{ number_format($financialSummary['previous_unpaid_balance'], 0) }} ₪
                                </td>
                            </tr>
                        @endif

                        {{-- السطر النهائي: الصافي المطلوب للسداد --}}
                        <tr class="tfoot-row-grand-total">
                            <td colspan="4" class="tfoot-grand-label">
                                <i class="fa-solid fa-coins"></i>
                                {{ __('صافي المبلغ المطلوب سداده للفصل الدراسي:') }}
                            </td>
                            <td colspan="2" class="tfoot-grand-val font-mono">
                                {{ number_format($finalAmount, 0) }} ₪
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- إشعار ختامي رسمي للسند --}}
            <div class="voucher-footer-notice">
                <i class="fa-solid fa-circle-check text-emerald"></i>
                <span>{{ __('الرسوم معتمدة للفصل الدراسي، وتشمل الوصول الكامل لجميع الدروس المصورة، بنك الأسئلة، والملازم الشاملة فور سداد المبلغ واعتماد الإيصال.') }}</span>
            </div>
        </div>

        <!-- بطاقة وسائل الدفع والتحويل الفلسطينية المعتمدة (تصميم كلاسيكي رسمي ومؤطر) -->
        <div class="payment-channels-card classic-framed-section">
            <div class="classic-section-header">
                <div class="header-icon-wrap">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div>
                    <h3 class="classic-section-title">{{ __('وسائل الدفع والتحويل الرسمية المعتمدة') }}</h3>
                    <p class="classic-section-subtitle">{{ __('المستفيد المعتمد لكافة الحسابات: م. أحمد شمالي | التحويل متاح عبر التطبيقات البنكية والمحافظ الإلكترونية') }}</p>
                </div>
            </div>

            <!-- شريط رقم التحويل الموحد البارز والمؤطر -->
            <div class="unified-transfer-banner">
                <div class="unified-transfer-content">
                    <span class="unified-label">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                        {{ __('رقم التحويل الموحد لكافة الحسابات (بنك فلسطين، بال باي، جوال باي):') }}
                    </span>
                    <strong class="unified-number font-mono" dir="ltr">0567897212</strong>
                </div>
                <button type="button" class="btn-copy-unified" onclick="copyNumber('0567897212', '{{ __('رقم التحويل الموحد') }}')">
                    <i class="fa-regular fa-copy"></i>
                    <span>{{ __('نسخ رقم التحويل (0567897212)') }}</span>
                </button>
            </div>

            <p class="channels-instruction">
                <i class="fa-solid fa-circle-info"></i>
                {{ __('يرجى تحويل المبلغ المستحق (:amount ₪) إلى أحد الحسابات الآتية باسم (م. أحمد شمالي):', ['amount' => number_format($finalAmount ?? 150, 0)]) }}
            </p>

            <div class="channels-grid">
                <!-- بنك فلسطين -->
                <div class="channel-card classic-channel">
                    <div class="channel-card-top">
                        <div class="channel-icon bop"><i class="fa-solid fa-building-columns"></i></div>
                        <div>
                            <strong class="channel-name">{{ __('بنك فلسطين (Bank of Palestine)') }}</strong>
                            <span class="account-holder"><i class="fa-solid fa-user-check"></i> {{ __('المستفيد: م. أحمد شمالي') }}</span>
                        </div>
                    </div>
                    <div class="number-copy-row">
                        <span class="account-num font-mono" dir="ltr">0567897212</span>
                        <button type="button" class="copy-btn" onclick="copyNumber('0567897212', '{{ __('رقم بنك فلسطين') }}')">
                            <i class="fa-regular fa-copy"></i> {{ __('نسخ') }}
                        </button>
                    </div>
                </div>

                <!-- بال باي -->
                <div class="channel-card classic-channel">
                    <div class="channel-card-top">
                        <div class="channel-icon palpay"><i class="fa-solid fa-credit-card"></i></div>
                        <div>
                            <strong class="channel-name">{{ __('بال باي (PalPay)') }}</strong>
                            <span class="account-holder"><i class="fa-solid fa-user-check"></i> {{ __('المستفيد: م. أحمد شمالي') }}</span>
                        </div>
                    </div>
                    <div class="number-copy-row">
                        <span class="account-num font-mono" dir="ltr">0567897212</span>
                        <button type="button" class="copy-btn" onclick="copyNumber('0567897212', '{{ __('رقم PalPay') }}')">
                            <i class="fa-regular fa-copy"></i> {{ __('نسخ') }}
                        </button>
                    </div>
                </div>

                <!-- جوال باي -->
                <div class="channel-card classic-channel">
                    <div class="channel-card-top">
                        <div class="channel-icon jawwalpay"><i class="fa-solid fa-mobile-screen-button"></i></div>
                        <div>
                            <strong class="channel-name">{{ __('محفظة جوال باي (Jawwal Pay)') }}</strong>
                            <span class="account-holder"><i class="fa-solid fa-user-check"></i> {{ __('المستفيد: م. أحمد شمالي') }}</span>
                        </div>
                    </div>
                    <div class="number-copy-row">
                        <span class="account-num font-mono" dir="ltr">0567897212</span>
                        <button type="button" class="copy-btn" onclick="copyNumber('0567897212', '{{ __('رقم جوال باي') }}')">
                            <i class="fa-regular fa-copy"></i> {{ __('نسخ') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج إرسال إشعار السداد ورفع الإيصال (تصميم كلاسيكي مؤطر ورصين) -->
        <div class="receipt-submission-card classic-framed-section">
            <div class="classic-section-header receipt-theme">
                <div class="header-icon-wrap receipt-icon">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h3 class="classic-section-title">{{ __('نموذج توثيق الحوالة وإرفاق إيصال السداد الأكاديمي') }}</h3>
                    <p class="classic-section-subtitle">{{ __('بعد إتمام التحويل، يرجى تعبئة النموذج وإرفاق الوصل لتقوم الإدارة باعتماد وتفعيل الحساب فوراً') }}</p>
                </div>
            </div>

            @if(session('payment_success'))
                <div class="alert-success-box" style="background: #f0fdf4; border: 1.5px solid #86efac; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                    <i class="fa-solid fa-circle-check" style="font-size: 1.2rem;"></i>
                    <span>{{ session('payment_success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-danger-box" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 18px; font-weight: 700; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; display: flex; align-items: flex-start; gap: 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem; color: #dc2626; margin-top: 2px;"></i>
                    <div>
                        <div style="margin-bottom: 6px;">{{ __('يرجى تصحيح الأخطاء التالية:') }}</div>
                        <ul style="margin: 0; padding-inline-start: 18px; font-size: 0.88rem; font-weight: 600;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form id="pendingPaymentForm" action="{{ route('student.pendingPayment.submit') }}" method="POST" enctype="multipart/form-data" novalidate onsubmit="return validatePaymentForm(event)">
                @csrf

                <!-- 1. صندوق تحديد قيمة الدفعة المراد سدادها الذكي والمتطور -->
                <div class="smart-payment-amount-container">
                    <div class="due-amount-banner {{ (($financialSummary['previous_unpaid_balance'] ?? 0) > 0) ? 'has-arrears' : '' }}">
                        <div class="due-info">
                            <span class="due-lbl">
                                @if(($financialSummary['previous_unpaid_balance'] ?? 0) > 0)
                                    {{ __('إجمالي المبلغ المستحق للدفع الآن:') }}
                                @else
                                    {{ __('القسط الشهري المطلوب رسمياً:') }}
                                @endif
                            </span>
                            <div class="due-val-wrap">
                                <strong class="due-val font-mono">{{ number_format($finalAmount, 2) }} ₪</strong>
                                @if(($financialSummary['previous_unpaid_balance'] ?? 0) > 0)
                                    <span class="due-target-badge arrears-badge">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                        {{ __('يشمل متأخرات :arr ₪ + قسط :m :cur ₪', [
                                            'arr' => number_format($financialSummary['previous_unpaid_balance'], 0),
                                            'm' => $financialSummary['active_due_month_name'] ?? $dueMonthName,
                                            'cur' => number_format($financialSummary['current_month_due'] ?? $student->final_monthly_fee, 0)
                                        ]) }}
                                    </span>
                                @else
                                    <span class="due-target-badge">{{ __('عن :month', ['month' => $dueMonthName]) }}</span>
                                @endif
                            </div>
                        </div>
                        @if($student->hasDiscount())
                            <div class="due-discount-tag">
                                <i class="fa-solid fa-tag"></i>
                                <span>{{ __('خصم معتمد لك: :orig ₪ ➜ :final ₪', ['orig' => number_format($monthlyFee, 0), 'final' => number_format($student->final_monthly_fee, 0)]) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- أزرار اختيار نمط الدفع المرن (Tabs) -->
                    <div class="payment-mode-tabs {{ (($financialSummary['previous_unpaid_balance'] ?? 0) > 0) ? 'has-arrears-grid' : '' }}">
                        @if(($financialSummary['previous_unpaid_balance'] ?? 0) > 0)
                            <button type="button" class="mode-tab-btn active" id="tabModeFull" onclick="selectPaymentMode('full')">
                                <i class="fa-solid fa-circle-check text-emerald"></i>
                                <span>{{ __('سداد الإجمالي كاملاً (:amt ₪)', ['amt' => number_format($financialSummary['total_due_now'] ?? $finalAmount, 0)]) }}</span>
                            </button>
                            <button type="button" class="mode-tab-btn" id="tabModeArrearsOnly" onclick="selectPaymentMode('arrears_only')">
                                <i class="fa-solid fa-clock-rotate-left" style="color: #b45309;"></i>
                                <span>{{ __('سداد المتأخرات فقط (:amt ₪)', ['amt' => number_format($financialSummary['previous_unpaid_balance'], 0)]) }}</span>
                            </button>
                            <button type="button" class="mode-tab-btn" id="tabModeCurrentOnly" onclick="selectPaymentMode('current_only')">
                                <i class="fa-solid fa-calendar-day" style="color: #1d4ed8;"></i>
                                <span>{{ __('سداد قسط هذا الشهر فقط (:amt ₪)', ['amt' => number_format($financialSummary['current_month_due'] ?? $student->final_monthly_fee, 0)]) }}</span>
                            </button>
                        @else
                            <button type="button" class="mode-tab-btn active" id="tabModeFull" onclick="selectPaymentMode('full')">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>{{ __('سداد كامل القسط المطلوب (:amt ₪)', ['amt' => number_format($finalAmount, 0)]) }}</span>
                            </button>
                        @endif
                        <button type="button" class="mode-tab-btn" id="tabModeCustom" onclick="selectPaymentMode('custom')">
                            <i class="fa-solid fa-sliders"></i>
                            <span>{{ __('سداد دفعة مرنة مخصصة') }}</span>
                        </button>
                        <button type="button" class="mode-tab-btn" id="tabModeMulti" onclick="selectPaymentMode('multi')">
                            <i class="fa-solid fa-calendar-plus"></i>
                            <span>{{ __('سداد عدة أقساط مقدماً') }}</span>
                        </button>
                    </div>

                    <!-- محتوى الخيار 2: إدخال دفعة مخصصة -->
                    <div id="customAmountSection" class="custom-amount-panel" style="display: none;">
                        <div class="custom-input-row">
                            <label class="custom-input-label">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                {{ __('أدخل المبلغ الذي ترغب في دفعه الآن (₪):') }}
                            </label>
                            <div class="custom-input-wrap">
                                <input type="number" step="1" min="10" max="5000" id="customAmountInput" class="custom-number-input font-mono" placeholder="{{ __('مثال: 50 أو 100') }}" oninput="onCustomAmountChange(this.value)">
                                <span class="currency-symbol font-mono">₪ ILS</span>
                            </div>
                        </div>
                        <div class="quick-preset-chips">
                            <span>{{ __('مبالغ سريعة:') }}</span>
                            <button type="button" class="chip-btn font-mono" onclick="setPresetAmount(50)">50 ₪</button>
                            <button type="button" class="chip-btn font-mono" onclick="setPresetAmount(75)">75 ₪</button>
                            <button type="button" class="chip-btn font-mono" onclick="setPresetAmount(100)">100 ₪</button>
                            <button type="button" class="chip-btn font-mono" onclick="setPresetAmount({{ $finalAmount }})">{{ number_format($finalAmount, 0) }} ₪</button>
                        </div>
                    </div>

                    <!-- محتوى الخيار 3: سداد عدة أشهر مقدماً -->
                    <div id="multiMonthSection" class="custom-amount-panel" style="display: none;">
                        <span class="multi-panel-title"><i class="fa-solid fa-bolt"></i> {{ __('اختر عدد الأشهر التي ترغب بسدادها مقدماً:') }}</span>
                        <div class="multi-months-grid">
                            <button type="button" class="multi-card-btn" onclick="setMultiMonths(2)">
                                <strong class="m-title">{{ __('شهران (2)') }}</strong>
                                <span class="m-price font-mono">{{ number_format($finalAmount * 2, 0) }} ₪</span>
                                <small>{{ __('تغطية الشهرين القادمين') }}</small>
                            </button>
                            <button type="button" class="multi-card-btn" onclick="setMultiMonths(3)">
                                <strong class="m-title">{{ __('3 أشهر') }}</strong>
                                <span class="m-price font-mono">{{ number_format($finalAmount * 3, 0) }} ₪</span>
                                <small>{{ __('سداد فصل دراسي جزئي') }}</small>
                            </button>
                            <button type="button" class="multi-card-btn" onclick="setMultiMonths(5)">
                                <strong class="m-title">{{ __('فصل دراسي (5 أشهر)') }}</strong>
                                <span class="m-price font-mono">{{ number_format($finalAmount * 5, 0) }} ₪</span>
                                <small>{{ __('تغطية فصل كامل') }}</small>
                            </button>
                            <button type="button" class="multi-card-btn" onclick="setMultiMonths(10)">
                                <strong class="m-title">{{ __('سنة دراسية كاملة') }}</strong>
                                <span class="m-price font-mono">{{ number_format($finalAmount * 10, 0) }} ₪</span>
                                <small>{{ __('راحة تامة طوال العام') }}</small>
                            </button>
                        </div>
                    </div>

                    <!-- شريط المحاسبة الذكي المباشر (Live Financial Summary) -->
                    <div class="live-calc-box">
                        <div class="calc-item">
                            <span class="calc-lbl">{{ __('المبلغ المطلوب رسمياً:') }}</span>
                            <strong class="calc-val font-mono">{{ number_format($finalAmount, 2) }} ₪</strong>
                        </div>
                        <div class="calc-item highlight">
                            <span class="calc-lbl">{{ __('المبلغ الذي ستدفعه الآن:') }}</span>
                            <strong class="calc-val font-mono" id="displayActiveAmount">{{ number_format($finalAmount, 2) }} ₪</strong>
                        </div>
                        <div class="calc-item">
                            <span class="calc-lbl">{{ __('حالة الرصيد والتغطية:') }}</span>
                            <span class="calc-status" id="displayCoverageNote">
                                <i class="fa-solid fa-circle-check text-emerald"></i> {{ __('مسدد بالكامل رسمياً ✅ (الرصيد المتبقي: 0.00 ₪)') }}
                            </span>
                        </div>
                    </div>

                    <!-- الحقول المخفية للنموذج -->
                    <input type="hidden" name="amount" id="formActualAmountInput" value="{{ $finalAmount ?? 150 }}">
                    <input type="hidden" name="payment_mode" id="formPaymentModeInput" value="full">
                </div>

                <div class="form-grid-row" style="margin-top: 18px;">
                    <div class="form-group-cell">
                        <label class="input-label">{{ __('وسيلة الدفع المستخدمة') }} <span class="required">*</span></label>
                        <select name="payment_method" id="paymentMethodSelect" class="form-select-clean" required onchange="updateWhatsAppMessage()">
                            <option value="جوال باي (Jawwal Pay)">{{ __('محفظة جوال باي (Jawwal Pay)') }} - 0567897212</option>
                            <option value="بال باي (PalPay)">{{ __('بال باي (PalPay)') }} - 0567897212</option>
                            <option value="بنك فلسطين (Bank of Palestine)">{{ __('بنك فلسطين (Bank of Palestine)') }} - 0567897212</option>
                            <option value="ريفلكت (Reflect)">{{ __('ريفلكت (Reflect)') }}</option>
                            <option value="سداد نقدي مباشر للمشرف">{{ __('سداد نقدي مباشر للمشرف') }}</option>
                        </select>
                    </div>

                    <div class="form-group-cell">
                        <label class="input-label">{{ __('رقم العملية / الحوالة (اختياري)') }}</label>
                        <input type="text" name="reference_no" id="referenceNoInput" placeholder="{{ __('مثال: TRX-98214') }}" class="form-input-clean">
                    </div>
                </div>

                <div class="form-group-full">
                    <label class="input-label">{{ __('ملاحظات الدفعة (اختياري)') }}</label>
                    <input type="text" name="notes" id="paymentNotesInput" placeholder="{{ __('مثال: دفعة قسط شهر :m أو حوالة باسم والدي...', ['m' => $dueMonthName]) }}" class="form-input-clean">
                </div>

                <div class="form-group-full">
                    <label class="input-label">{{ __('إرفاق صورة الإيصال أو لقطة الشاشة أو ملف PDF') }} <span class="required">*</span></label>
                    <div class="file-upload-box classic-upload-box" id="fileUploadBox">
                        <input type="file" name="receipt_photo" id="receiptFileInput" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="file-input-hidden" onchange="handleFileSelected(this)">
                        <label for="receiptFileInput" class="file-upload-label">
                            <div class="upload-icon-circle">
                                <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                            </div>
                            <span id="uploadLabelText" class="upload-main-text">{{ __('اضغط هنا لاختيار صورة إيصال التحويل أو ملف PDF') }}</span>
                            <span class="upload-sub-text">{{ __('يقبل صيغ JPG, PNG, WEBP أو ملفات PDF رسمية (حجم أقصى 8 ميجابايت)') }}</span>
                            <span class="upload-cta-pill"><i class="fa-solid fa-folder-open"></i> {{ __('استعراض الملفات من جهازك') }}</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-submit-receipt classic-submit-btn" id="btnSubmitReceipt">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span id="submitBtnText">{{ __('تأكيد وإرسال إشعار السداد بمبلغ :amt ₪ للاعتماد', ['amt' => number_format($finalAmount, 0)]) }}</span>
                </button>
            </form>
        </div>

        <!-- بطاقة مكتب المشرف العام والمتابعة الأكاديمية (تصميم كلاسيكي مؤطر ورزين - يمنع أي عوم) -->
        <div class="director-office-classic-card">
            <div class="director-card-header">
                <div class="director-header-right">
                    <div class="director-seal-badge">
                        <i class="fa-solid fa-building-user"></i>
                    </div>
                    <div>
                        <h3 class="director-card-title">{{ __('مكتب المشرف العام والمتابعة الأكاديمية (م. أحمد شمالي)') }}</h3>
                        <p class="director-card-subtitle">{{ __('منصة Step by Step التعليمية - دولة فلسطين | المتابعة والاعتماد المباشر') }}</p>
                    </div>
                </div>
                <div class="director-status-pill">
                    <span class="live-dot"></span>
                    <span>{{ __('المشرف متاح للمتابعة') }}</span>
                </div>
            </div>

            <!-- أرقام التواصل والتحويل الرسمية المعتمدة بتنسيق كلاسيكي متين وعالي التباين -->
            <div class="director-numbers-grid">
                <!-- رقم الواتس الخاص بالمدير -->
                <div class="official-num-box whatsapp-box">
                    <div class="num-box-meta">
                        <i class="fa-brands fa-whatsapp text-emerald"></i>
                        <span class="num-label">{{ __('رقم الواتس الخاص بالمدير (للمراسلة والاعتماد):') }}</span>
                    </div>
                    <div class="num-val-row">
                        <strong class="num-val font-mono" dir="ltr">00970597694385</strong>
                        <button type="button" class="btn-box-copy" onclick="copyNumber('00970597694385', '{{ __('رقم واتساب المدير') }}')">
                            <i class="fa-regular fa-copy"></i> {{ __('نسخ') }}
                        </button>
                    </div>
                </div>

                <!-- رقم التحويل المعتمد -->
                <div class="official-num-box transfer-box">
                    <div class="num-box-meta">
                        <i class="fa-solid fa-money-bill-transfer text-primary"></i>
                        <span class="num-label">{{ __('رقم التحويل والسداد المعتمد (جوال باي، بنك فلسطين، بال باي):') }}</span>
                    </div>
                    <div class="num-val-row">
                        <strong class="num-val font-mono" dir="ltr">0567897212</strong>
                        <button type="button" class="btn-box-copy" onclick="copyNumber('0567897212', '{{ __('رقم التحويل المعتمد') }}')">
                            <i class="fa-regular fa-copy"></i> {{ __('نسخ') }}
                        </button>
                    </div>
                </div>
            </div>

            @php
                $waMsg = urlencode(app()->getLocale() === 'ar'
                    ? ("السلام عليكم بشمهندس أحمد شمالي، أنا الطالب (" . ($student->name_ar ?? $student->name) . ") ورقم هاتفي (" . ($student->phone ?? '') . ")، قمت بإنشاء حسابي في منصة Step by Step وسددت الرسوم الأكاديمية بمبلغ (" . number_format($finalAmount, 0) . " ₪). أرجو التكرم باعتماد وتفعيل حسابي واشتراكي.")
                    : ("Hello Eng. Ahmed Shamali, I am student (" . ($student->name_en ?? $student->name) . ") phone (" . ($student->phone ?? '') . "), I registered on Step by Step platform and paid tuition. Please verify and activate my enrollment."));
            @endphp

            <!-- زر المحادثة المباشر عبر واتساب المدير الرسمي -->
            <div class="director-whatsapp-action-strip">
                <a href="https://wa.me/{{ $directorWaClean }}?text={{ $waMsg }}" target="_blank" class="classic-whatsapp-btn" id="supervisorWhatsAppBtn">
                    <div class="wa-btn-icon-wrap">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div class="wa-btn-text-wrap">
                        <span class="wa-btn-title">{{ __('تواصل مع المشرف العام (م. أحمد شمالي) عبر واتساب') }}</span>
                        <span class="wa-btn-sub" dir="ltr">00970597694385 (Direct Official WhatsApp)</span>
                    </div>
                    <div class="wa-btn-arrow">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                </a>
            </div>

            <!-- شريط أدوات النظام والتحكم (مؤطر ورصين) -->
            <div class="classic-system-actions-toolbar">
                <button type="button" class="btn-classic-action refresh" onclick="checkStatusRefresh()">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>{{ __('فحص حالة الحساب وتحديث الصفحة') }}</span>
                </button>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline;">
                    @csrf
                    <button type="submit" class="btn-classic-action logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>{{ __('تسجيل الخروج') }}</span>
                    </button>
                </form>
            </div>

            <!-- ختم الأمان الأكاديمي والتوثيق الرسمي داخل الإطار -->
            <div class="classic-official-seal-footer">
                <div class="seal-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="seal-text">
                    <strong>{{ __('منصة Step by Step - فلسطين | نظام التوثيق والاعتماد الأكاديمي المعتمد') }}</strong>
                    <span>{{ __('كافة بياناتك ووثائقك المالية والدراسية مشفرة ومحفوظة رسمياً بأعلى معايير الحماية والأمان.') }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const pendingI18n = {
        attachProofTitle: "{{ __('يرجى إرفاق الإيصال أولاً') }}",
        attachProofText: "{{ __('يرجى النقر على مربع رفع الملف واختيار صورة إيصال التحويل أو ملف PDF ليتمكن المشرف من مطابقة ومراجعة سدادك فوراً.') }}",
        okBtn: "{{ __('حسناً، سأقوم برفع الإيصال') }}",
        sending: "{{ __('جاري إرسال الإشعار ورفع الإيصال للإدارة...') }}",
        sentSuccess: "{{ __('تم إرسال إشعار السداد بنجاح!') }}",
        sentDesc: "{{ __('تم تسليم إشعار السداد والإيصال للمشرف العام، وسيقوم بمطابقته وتفعيل اشتراكك وحسابك فورياً.') }}",
        okGotIt: "{{ __('حسناً، تم الاطلاع') }}",
        sessionExpired: "{{ __('انتهت صلاحية الجلسة المؤقتة') }}",
        sessionExpiredText: "{{ __('حرصاً على أمان بياناتك تم تجديد الصفحة، يرجى إعادة المحاولة الآن.') }}",
        refreshBtn: "{{ __('تحديث ومتابعة') }}",
        errorTitle: "{{ __('تعذر إرسال الإشعار') }}",
        errorDefault: "{{ __('تعذر إرسال الإشعار، يرجى التأكد من نوع وحجم الملف والمحاولة ثانية.') }}",
        checkingTitle: "{{ __('جاري فحص حالة الحساب...') }}",
        checkingText: "{{ __('يرجى الانتظار لحظات للتحقق من اعتماد المشرف') }}",
        fileSelectedText: "{{ __('تم اختيار الملف: :file ✅') }}",
        copySuccess: "{{ __('تم النسخ بنجاح 📋') }}"
    };

    const baseDueAmount = {{ (float)($finalAmount ?? 150) }};
    const arrearsAmount = {{ (float)($financialSummary['previous_unpaid_balance'] ?? 0) }};
    const currentMonthAmount = {{ (float)($financialSummary['current_month_due'] ?? $student->final_monthly_fee) }};
    const totalDueNow = {{ (float)($financialSummary['total_due_now'] ?? $finalAmount) }};
    const dueMonthName = "{{ addslashes($dueMonthName ?? 'الشهر الحالي') }}";
    const studentDisplayName = "{{ addslashes($studentDispName ?? 'طالبنا العزيز') }}";
    const studentPhone = "{{ addslashes($student->phone ?? '') }}";

    function selectPaymentMode(mode) {
        document.getElementById('tabModeFull')?.classList.remove('active');
        document.getElementById('tabModeArrearsOnly')?.classList.remove('active');
        document.getElementById('tabModeCurrentOnly')?.classList.remove('active');
        document.getElementById('tabModeCustom')?.classList.remove('active');
        document.getElementById('tabModeMulti')?.classList.remove('active');

        const customSec = document.getElementById('customAmountSection');
        const multiSec = document.getElementById('multiMonthSection');
        if (customSec) customSec.style.display = 'none';
        if (multiSec) multiSec.style.display = 'none';

        if (mode === 'full') {
            document.getElementById('tabModeFull')?.classList.add('active');
            updateCalculationsAndUI(totalDueNow > 0 ? totalDueNow : baseDueAmount, 'full', 1);
        } else if (mode === 'arrears_only') {
            document.getElementById('tabModeArrearsOnly')?.classList.add('active');
            updateCalculationsAndUI(arrearsAmount, 'arrears_only', 1);
        } else if (mode === 'current_only') {
            document.getElementById('tabModeCurrentOnly')?.classList.add('active');
            updateCalculationsAndUI(currentMonthAmount, 'current_only', 1);
        } else if (mode === 'custom') {
            document.getElementById('tabModeCustom')?.classList.add('active');
            if (customSec) customSec.style.display = 'block';
            let curVal = parseFloat(document.getElementById('customAmountInput')?.value) || baseDueAmount;
            document.getElementById('customAmountInput').value = curVal;
            updateCalculationsAndUI(curVal, 'custom', 1);
        } else if (mode === 'multi') {
            document.getElementById('tabModeMulti')?.classList.add('active');
            if (multiSec) multiSec.style.display = 'block';
            updateCalculationsAndUI(currentMonthAmount * 2, 'multi', 2);
        }
    }

    function onCustomAmountChange(val) {
        let amt = parseFloat(val);
        if (isNaN(amt) || amt < 0) amt = 0;
        updateCalculationsAndUI(amt, 'custom', 1);
    }

    function setPresetAmount(amt) {
        const inp = document.getElementById('customAmountInput');
        if (inp) inp.value = amt;
        updateCalculationsAndUI(amt, 'custom', 1);
    }

    function setMultiMonths(count) {
        const baseUnit = currentMonthAmount > 0 ? currentMonthAmount : (baseDueAmount > 0 ? baseDueAmount : 150);
        const total = arrearsAmount + (baseUnit * count);
        updateCalculationsAndUI(total, 'multi', count);
    }

    function updateCalculationsAndUI(amount, mode, count) {
        const formActualInput = document.getElementById('formActualAmountInput');
        const formModeInput = document.getElementById('formPaymentModeInput');
        const displayAmt = document.getElementById('displayActiveAmount');
        const submitText = document.getElementById('submitBtnText');
        const coverageEl = document.getElementById('displayCoverageNote');

        if (formActualInput) formActualInput.value = amount;
        if (formModeInput) formModeInput.value = mode;
        if (displayAmt) displayAmt.innerText = amount.toFixed(2) + ' ₪';

        if (submitText) {
            submitText.innerText = `{{ __('تأكيد وإرسال إشعار السداد بمبلغ') }} ${Math.round(amount)} ₪`;
        }

        if (coverageEl) {
            const targetTotal = totalDueNow > 0 ? totalDueNow : baseDueAmount;
            if (mode === 'full' || amount >= targetTotal) {
                if (arrearsAmount > 0) {
                    coverageEl.innerHTML = `<i class="fa-solid fa-circle-check text-emerald"></i> {{ __('تسديد كامل المتأخرات السابقة (:arr ₪) وقسط هذا الشهر (:cur ₪) بالكامل ✅', ['arr' => number_format($financialSummary['previous_unpaid_balance'] ?? 0, 0), 'cur' => number_format($financialSummary['current_month_due'] ?? 0, 0)]) }}`;
                } else {
                    coverageEl.innerHTML = `<i class="fa-solid fa-circle-check text-emerald"></i> {{ __('مسدد بالكامل رسمياً ✅ (الرصيد المتبقي: 0.00 ₪ عن') }} ${dueMonthName})`;
                }
            } else if (mode === 'arrears_only' || (arrearsAmount > 0 && Math.abs(amount - arrearsAmount) < 0.01)) {
                coverageEl.innerHTML = `<i class="fa-solid fa-check-double text-amber"></i> {{ __('تسديد المتأخرات السابقة بالكامل، ويبقى قسط هذا الشهر') }} (${currentMonthAmount.toFixed(2)} ₪)`;
            } else if (mode === 'current_only' || (currentMonthAmount > 0 && Math.abs(amount - currentMonthAmount) < 0.01)) {
                coverageEl.innerHTML = `<i class="fa-solid fa-circle-notch text-blue"></i> {{ __('سداد قسط هذا الشهر، ويبقى رصيد المتأخرات السابق') }} (${arrearsAmount.toFixed(2)} ₪)`;
            } else if (amount < targetTotal) {
                const rem = targetTotal - amount;
                coverageEl.innerHTML = `<i class="fa-solid fa-circle-half-stroke text-amber"></i> {{ __('دفعة جزئية (المتبقي من إجمالي المستحق:') }} ${rem.toFixed(2)} ₪)`;
            } else {
                const baseUnit = currentMonthAmount > 0 ? currentMonthAmount : 150;
                const monthsCovered = Math.floor(amount / baseUnit);
                coverageEl.innerHTML = `<i class="fa-solid fa-star text-emerald"></i> {{ __('سداد مسبق يغطي') }} (${monthsCovered}) {{ __('أشهر بنجاح! رصيد معتمد مقدماً 🎉') }}`;
            }
        }

        updateWhatsAppMessage();
    }

    function updateWhatsAppMessage() {
        const amount = document.getElementById('formActualAmountInput')?.value || baseDueAmount;
        const method = document.getElementById('paymentMethodSelect')?.value || 'محفظة جوال باي (Jawwal Pay)';
        const notes = document.getElementById('paymentNotesInput')?.value || '';
        
        let msg = `السلام عليكم م.أحمد شمالي، أنا الطالب (${studentDisplayName}) ورقم هاتفي (${studentPhone})، قمت بسداد رسوم منصة Step by Step بقيمة [${Math.round(amount)} ₪] عبر وسيلة [${method}].`;
        if (notes) {
            msg += ` ملاحظات: [${notes}].`;
        }
        msg += ` أرجو التكرم باعتماد وتفعيل حسابي واشتراكي.`;

        const waUrl = `https://wa.me/970597694385?text=${encodeURIComponent(msg)}`;
        const waBtn = document.getElementById('supervisorWhatsAppBtn');
        if (waBtn) {
            waBtn.href = waUrl;
        }
    }

    function fallbackCopy(text) {
        const tempInp = document.createElement('input');
        tempInp.value = text;
        tempInp.style.position = 'fixed';
        tempInp.style.opacity = '0';
        document.body.appendChild(tempInp);
        tempInp.focus();
        tempInp.select();
        try { document.execCommand('copy'); } catch(e) {}
        document.body.removeChild(tempInp);
    }

    function copyNumber(text, label) {
        const showToast = () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `${label} (${text})`,
                showConfirmButton: false,
                timer: 2000
            });
        };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(showToast).catch(() => {
                fallbackCopy(text);
                showToast();
            });
        } else {
            fallbackCopy(text);
            showToast();
        }
    }

    function handleFileSelected(input) {
        const labelText = document.getElementById('uploadLabelText');
        const uploadBox = document.getElementById('fileUploadBox');
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / (1024 * 1024)).toFixed(2);
            if (labelText) {
                labelText.innerHTML = `<i class="fa-solid fa-circle-check" style="color: #15803d; margin-inline-end: 6px;"></i> ${pendingI18n.fileSelectedText.replace(':file', fileName)} (${fileSize} MB)`;
                labelText.style.color = '#15803d';
                labelText.style.fontWeight = '800';
            }
            if (uploadBox) {
                uploadBox.classList.add('file-selected');
                uploadBox.style.borderColor = '#15803d';
                uploadBox.style.background = '#f0fdf4';
            }
        }
    }

    function validatePaymentForm(e) {
        e.preventDefault();
        const fileInput = document.getElementById('receiptFileInput');
        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: pendingI18n.attachProofTitle,
                text: pendingI18n.attachProofText,
                confirmButtonText: pendingI18n.okBtn,
                confirmButtonColor: '#1d4ed8'
            });
            return false;
        }

        const btn = document.getElementById('btnSubmitReceipt');
        const text = document.getElementById('submitBtnText');
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.75';
            btn.style.cursor = 'not-allowed';
            if (text) text.innerText = pendingI18n.sending;
        }

        const form = document.getElementById('pendingPaymentForm');
        const formData = new FormData(form);

        axios.post("{{ route('student.pendingPayment.submit') }}", formData)
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: pendingI18n.sentSuccess,
                    text: res.data?.message || pendingI18n.sentDesc,
                    confirmButtonText: pendingI18n.okGotIt,
                    confirmButtonColor: '#1d4ed8'
                }).then(() => {
                    window.location.reload();
                });
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                    if (text) text.innerText = "{{ __('إرسال إشعار السداد ورفع الإيصال للاعتماد') }}";
                }

                if (err.response && err.response.status === 419) {
                    Swal.fire({
                        icon: 'info',
                        title: pendingI18n.sessionExpired,
                        text: pendingI18n.sessionExpiredText,
                        confirmButtonText: pendingI18n.refreshBtn,
                        confirmButtonColor: '#1d4ed8'
                    }).then(() => {
                        window.location.reload();
                    });
                    return;
                }

                const msg = err.response?.data?.message || err.response?.data?.title || pendingI18n.errorDefault;
                Swal.fire({
                    icon: 'error',
                    title: pendingI18n.errorTitle,
                    text: msg,
                    confirmButtonText: "{{ __('حسناً') }}",
                    confirmButtonColor: '#dc2626'
                });
            });

        return false;
    }

    function checkStatusRefresh() {
        Swal.fire({
            title: pendingI18n.checkingTitle,
            text: pendingI18n.checkingText,
            timer: 1200,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        }).then(() => {
            window.location.reload();
        });
    }
</script>

<style>
    /* ==========================================================================
       التصميم الكلاسيكي الملكي الأكاديمي لواجهة اعتماد الطالب وسداد الرسوم
       Classic Royal Academic Aesthetic (مؤطرة، رصينة، متينة، بدون أي عوم)
       ========================================================================== */

    .pending-approval-wrapper {
        min-height: calc(100vh - 90px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 36px 20px;
        background: #edf2f7; /* خلفية حجرية رزينة وأكاديمية */
    }

    .pending-approval-card {
        background: #ffffff;
        border: 2px solid #cbd5e1;
        border-top: 6px solid #0f2744; /* ترويسة كحلي ملكي عميق */
        border-radius: 12px;
        padding: 36px 30px;
        max-width: 920px;
        width: 100%;
        text-align: center;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        position: relative;
    }

    /* الأيقونة الأكاديمية الملكية */
    .pending-icon-bubble {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        background: #0f2744;
        border: 2px solid #1e3a8a;
        color: #f8fafc;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 16px;
        box-shadow: 0 4px 14px rgba(15, 39, 68, 0.2);
    }

    .status-badges-row {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }
    .badge-tag {
        font-size: 11.5px;
        font-weight: 700;
        padding: 3px 12px;
        border-radius: 50px;
    }
    .badge-tag.pending {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-tag.palestine {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .card-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
        line-height: 1.35;
    }
    .card-desc {
        color: #475569;
        font-size: 13px;
        line-height: 1.65;
        margin-bottom: 20px;
    }

    /* تفاصيل الطالب */
    .student-info-strip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 10px;
        margin-bottom: 18px;
    }
    html[dir="rtl"] .student-info-strip { text-align: right; }
    html[dir="ltr"] .student-info-strip { text-align: left; }

    .info-cell small {
        display: block;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .info-cell strong {
        font-size: 13px;
        color: #0f172a;
        font-weight: 700;
    }
    .status-pill-warning {
        display: inline-block;
        background: #fef3c7;
        color: #b45309;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
        border: 1px solid #fde68a;
    }
    .status-pill-danger {
        display: inline-block;
        background: #fef2f2;
        color: #b91c1c;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
        border: 1px solid #fecaca;
    }

    /* أنماط بطاقة الحساب المجمد */
    .frozen-card-border {
        border-top: 3px solid #dc2626 !important;
    }
    .pending-icon-bubble.frozen-bubble {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }
    .badge-tag.danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .text-danger { color: #b91c1c !important; }

    /* صندوق سبب التجميد */
    .freeze-reason-official-card {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 18px;
    }
    html[dir="rtl"] .freeze-reason-official-card { text-align: right; }
    html[dir="ltr"] .freeze-reason-official-card { text-align: left; }

    .freeze-reason-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        border-bottom: 1px solid #fecaca;
        padding-bottom: 8px;
    }
    .freeze-icon-wrap {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #fee2e2;
        color: #dc2626;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .freeze-header-title {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #991b1b;
    }
    .freeze-header-sub {
        margin: 0;
        font-size: 11px;
        color: #b91c1c;
    }
    .freeze-reason-quote-box {
        background: #ffffff;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 10px;
        border: 1px solid #fecaca;
    }
    html[dir="rtl"] .freeze-reason-quote-box { border-right: 3px solid #dc2626; }
    html[dir="ltr"] .freeze-reason-quote-box { border-left: 3px solid #dc2626; }

    .freeze-reason-statement {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.55;
    }
    .freeze-impact-note {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 600;
        color: #7f1d1d;
        line-height: 1.5;
    }

    .unfreeze-actions-strip { margin-bottom: 18px; }
    .btn-whatsapp-unfreeze {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        background: #16a34a;
        color: #ffffff;
        border: 1px solid #15803d;
        padding: 11px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: var(--transition);
    }
    .btn-whatsapp-unfreeze:hover {
        background: #15803d;
        color: #ffffff;
    }

    /* بطاقة سند الرسوم الأكاديمي الكلاسيكي */
    .academic-voucher-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        margin-bottom: 22px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        text-align: right;
    }
    html[dir="ltr"] .academic-voucher-card { text-align: left; }

    .academic-voucher-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1.5px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .voucher-header-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .voucher-header-seal {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        display: grid;
        place-items: center;
        font-size: 1.15rem;
        border: 1px solid #bfdbfe;
        flex-shrink: 0;
    }
    .voucher-title {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
    }
    .voucher-subtitle {
        margin: 3px 0 0 0;
        font-size: 0.78rem;
        color: #64748b;
    }
    .voucher-header-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .v-tag {
        font-size: 0.76rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .v-tag.primary {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .v-tag.slate {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    /* جدول السند الأكاديمي */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .academic-ledger-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        text-align: right;
    }
    html[dir="ltr"] .academic-ledger-table { text-align: left; }

    .academic-ledger-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid #cbd5e1;
        color: #475569;
        font-weight: 700;
        font-size: 0.78rem;
    }
    .academic-ledger-table th {
        padding: 11px 14px;
        white-space: nowrap;
    }
    .academic-ledger-table th.col-num {
        width: 44px;
        text-align: center;
    }
    .academic-ledger-table th.col-curr {
        text-align: center;
    }

    .academic-ledger-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.12s ease;
    }
    .academic-ledger-table tbody tr:hover {
        background: #f8fafc;
    }
    .academic-ledger-table td {
        padding: 11px 14px;
        vertical-align: middle;
    }
    .col-num-cell {
        text-align: center;
        color: #94a3b8;
        font-weight: 700;
    }
    .subject-title-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .subj-icon {
        font-size: 1.05rem;
    }
    .subject-title-cell strong {
        color: #0f172a;
        font-size: 0.88rem;
        font-weight: 700;
    }
    .semester-pill {
        display: inline-block;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 5px;
        font-weight: 600;
        font-size: 0.76rem;
    }
    .col-curr-cell {
        text-align: center;
        white-space: nowrap;
    }
    .col-curr-cell.orig-price {
        color: #64748b;
        font-weight: 600;
    }
    .col-curr-cell.discount-price {
        color: #059669;
        font-weight: 700;
    }
    .col-curr-cell.net-price {
        color: #0f172a;
        font-weight: 800;
        font-size: 0.92rem;
    }
    .table-empty-cell {
        padding: 24px;
        text-align: center;
        color: #64748b;
    }

    /* تذييل الجدول / الحسابات */
    .academic-ledger-table tfoot {
        border-top: 2px solid #cbd5e1;
    }
    .academic-ledger-table tfoot td {
        padding: 10px 18px;
    }
    .tfoot-label {
        text-align: left;
        font-weight: 700;
        color: #475569;
        font-size: 0.85rem;
    }
    html[dir="ltr"] .tfoot-label { text-align: right; }
    .tfoot-val {
        text-align: center;
        font-weight: 800;
        color: #0f172a;
        font-size: 0.92rem;
        white-space: nowrap;
    }
    .tfoot-row-subtotal {
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .tfoot-row-bundle {
        border-bottom: 1px solid #e2e8f0;
        background: #f0fdf4;
    }
    .tfoot-row-bundle .bundle-text {
        color: #166534;
    }
    .tfoot-row-scholarship {
        border-bottom: 1px solid #e2e8f0;
        background: #eff6ff;
    }
    .tfoot-row-scholarship .scholarship-text {
        color: #1d4ed8;
    }
    .tfoot-row-arrears {
        border-bottom: 1px solid #e2e8f0;
        background: #fef2f2;
    }
    .tfoot-row-arrears .arrears-text {
        color: #b91c1c;
    }

    /* السطر النهائي لسند الرسوم */
    .tfoot-row-grand-total {
        background: #0f172a;
        color: #ffffff;
    }
    .tfoot-grand-label {
        text-align: left;
        font-weight: 800;
        font-size: 0.95rem;
        color: #f8fafc;
    }
    html[dir="ltr"] .tfoot-grand-label { text-align: right; }
    .tfoot-grand-label i {
        color: #38bdf8;
        margin-left: 6px;
    }
    html[dir="ltr"] .tfoot-grand-label i {
        margin-left: 0;
        margin-right: 6px;
    }
    .tfoot-grand-val {
        text-align: center;
        font-weight: 800;
        font-size: 1.25rem;
        color: #38bdf8;
        white-space: nowrap;
    }

    .voucher-footer-notice {
        padding: 10px 16px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.76rem;
        font-weight: 600;
        color: #475569;
        line-height: 1.5;
    }
    .voucher-footer-notice i {
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .text-emerald { color: #16a34a !important; }
    .text-primary-net { color: #1d4ed8 !important; }

    /* ==========================================================================
       الأقسام المؤطرة الرسمية وقنوات التحويل ونموذج الإيصال الكلاسيكي
       ========================================================================== */
    .classic-framed-section {
        background: #ffffff;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        padding: 22px 22px;
        margin-bottom: 22px;
        text-align: right;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.04);
    }
    html[dir="rtl"] .classic-framed-section { text-align: right; }
    html[dir="ltr"] .classic-framed-section { text-align: left; }

    .classic-section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
    }
    .header-icon-wrap {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: #0f2744;
        color: #ffffff;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .classic-section-header.receipt-theme .header-icon-wrap {
        background: #1e3a8a;
    }
    .classic-section-title {
        margin: 0;
        font-size: 14.5px;
        font-weight: 800;
        color: #0f172a;
    }
    .classic-section-subtitle {
        margin: 3px 0 0 0;
        font-size: 12px;
        color: #64748b;
    }

    /* شريط رقم التحويل الموحد */
    .unified-transfer-banner {
        background: #0f2744;
        border: 1.5px solid #1e3a8a;
        border-radius: 8px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
        color: #ffffff;
    }
    .unified-transfer-content {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .unified-label {
        font-size: 12.5px;
        font-weight: 700;
        color: #e2e8f0;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .unified-number {
        font-size: 1.35rem;
        font-weight: 900;
        color: #38bdf8;
        background: rgba(15, 23, 42, 0.7);
        padding: 3px 12px;
        border-radius: 6px;
        border: 1px solid #38bdf8;
        letter-spacing: 0.5px;
    }
    .btn-copy-unified {
        background: #38bdf8;
        color: #0f172a;
        border: none;
        padding: 7px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: 0.2s ease;
    }
    .btn-copy-unified:hover {
        background: #7dd3fc;
        transform: translateY(-1px);
    }
    .channels-instruction {
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .channels-instruction i { color: #0284c7; }

    /* بطاقات القنوات */
    .channels-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    @media (max-width: 768px) {
        .channels-grid { grid-template-columns: 1fr; }
    }
    .classic-channel {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 10px;
        transition: 0.2s;
    }
    .classic-channel:hover {
        border-color: #0f2744;
        background: #ffffff;
    }
    .channel-card-top {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .channel-icon {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: grid;
        place-items: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .channel-icon.bop { background: #fee2e2; color: #b91c1c; }
    .channel-icon.palpay { background: #e0f2fe; color: #0284c7; }
    .channel-icon.jawwalpay { background: #dcfce7; color: #15803d; }
    .channel-name {
        display: block;
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .account-holder {
        display: block;
        font-size: 11.5px;
        color: #475569;
        font-weight: 600;
    }
    .number-copy-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding-top: 8px;
        border-top: 1px dashed #cbd5e1;
    }
    .account-num {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: 0.5px;
    }
    .copy-btn {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #334155;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: 0.2s;
    }
    .copy-btn:hover {
        background: #0f2744;
        color: #ffffff;
        border-color: #0f2744;
    }

    /* حقول النموذج */
    .form-grid-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }
    @media (max-width: 580px) { .form-grid-row { grid-template-columns: 1fr; } }

    .form-group-cell, .form-group-full {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 12px;
    }
    .input-label {
        font-size: 12.5px;
        font-weight: 700;
        color: #0f172a;
    }
    .input-label .required { color: #dc2626; }

    .form-select-clean, .form-input-clean {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        box-sizing: border-box;
        transition: 0.2s ease;
    }
    .form-select-clean:focus, .form-input-clean:focus {
        border-color: #0f2744;
        box-shadow: 0 0 0 3px rgba(15, 39, 68, 0.12);
    }

    /* صندوق رفع الإيصال الكلاسيكي المعتمد (Structured Deposit Dropzone) */
    .classic-upload-box {
        border: 2px dashed #475569;
        background: #f8fafc;
        border-radius: 8px;
        padding: 24px 18px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .classic-upload-box:hover {
        border-color: #0f2744;
        background: #f1f5f9;
    }
    .classic-upload-box.file-selected {
        border: 2px solid #15803d !important;
        background: #f0fdf4 !important;
    }
    .file-input-hidden { display: none; }
    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .upload-icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #0f2744;
        display: inline-grid;
        place-items: center;
        font-size: 1.5rem;
        margin-bottom: 8px;
        transition: 0.2s;
    }
    .classic-upload-box:hover .upload-icon-circle {
        background: #0f2744;
        color: #ffffff;
    }
    .upload-main-text {
        display: block;
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 3px;
    }
    .upload-sub-text {
        display: block;
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 8px;
    }
    .upload-cta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 700;
        color: #0f2744;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 4px 12px;
        border-radius: 50px;
    }

    /* زر إرسال الإشعار الكلاسيكي الأكاديمي */
    .classic-submit-btn {
        width: 100%;
        background: #0f2744;
        color: #ffffff;
        border: 2px solid #1e3a8a;
        padding: 13px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        margin-top: 10px;
        box-shadow: 0 4px 14px rgba(15, 39, 68, 0.2);
    }
    .classic-submit-btn:hover {
        background: #1e3a8a;
        box-shadow: 0 6px 18px rgba(15, 39, 68, 0.28);
        transform: translateY(-1px);
    }

    /* ==========================================================================
       بطاقة مكتب المشرف العام والمتابعة الأكاديمية (The Institutional Office Card)
       تلغي أي أزرار عائمة تماماً وتؤطر بيانات المدير في وعاء ملكي رصين
       ========================================================================== */
    .director-office-classic-card {
        background: #ffffff;
        border: 2px solid #0f2744;
        border-top: 5px solid #15803d;
        border-radius: 10px;
        padding: 22px 22px 18px 22px;
        margin-top: 10px;
        margin-bottom: 16px;
        text-align: right;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.07);
    }
    html[dir="rtl"] .director-office-classic-card { text-align: right; }
    html[dir="ltr"] .director-office-classic-card { text-align: left; }

    .director-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 14px;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .director-header-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .director-seal-badge {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: #0f2744;
        color: #ffffff;
        display: grid;
        place-items: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .director-card-title {
        margin: 0;
        font-size: 14.5px;
        font-weight: 800;
        color: #0f172a;
    }
    .director-card-subtitle {
        margin: 2px 0 0 0;
        font-size: 11.5px;
        color: #64748b;
    }
    .director-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        color: #166534;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 50px;
    }
    .live-dot {
        width: 8px;
        height: 8px;
        background: #16a34a;
        border-radius: 50%;
        animation: pulseLive 2s infinite;
    }
    @keyframes pulseLive {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.25); }
    }

    .director-numbers-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }
    @media (max-width: 640px) {
        .director-numbers-grid { grid-template-columns: 1fr; }
    }
    .official-num-box {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 8px;
    }
    .official-num-box.whatsapp-box {
        border-right: 4px solid #16a34a;
    }
    html[dir="ltr"] .official-num-box.whatsapp-box {
        border-right: 1.5px solid #cbd5e1;
        border-left: 4px solid #16a34a;
    }
    .official-num-box.transfer-box {
        border-right: 4px solid #1e3a8a;
    }
    html[dir="ltr"] .official-num-box.transfer-box {
        border-right: 1.5px solid #cbd5e1;
        border-left: 4px solid #1e3a8a;
    }
    .num-box-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
    }
    .num-val-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .num-val {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: 0.5px;
    }
    .btn-box-copy {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #334155;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: 0.2s;
    }
    .btn-box-copy:hover {
        background: #0f2744;
        color: #ffffff;
        border-color: #0f2744;
    }

    .director-whatsapp-action-strip {
        margin-bottom: 14px;
    }
    .classic-whatsapp-btn {
        width: 100%;
        background: #15803d;
        color: #ffffff;
        border: 2px solid #166534;
        border-radius: 8px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        text-decoration: none;
        box-sizing: border-box;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(21, 128, 61, 0.2);
    }
    .classic-whatsapp-btn:hover {
        background: #166534;
        box-shadow: 0 6px 18px rgba(21, 128, 61, 0.3);
        transform: translateY(-1px);
        color: #ffffff;
    }
    .wa-btn-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: grid;
        place-items: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .wa-btn-text-wrap {
        flex: 1;
        display: flex;
        flex-direction: column;
        text-align: right;
    }
    html[dir="ltr"] .wa-btn-text-wrap { text-align: left; }
    .wa-btn-title {
        font-size: 13.5px;
        font-weight: 800;
    }
    .wa-btn-sub {
        font-size: 11.5px;
        opacity: 0.9;
        font-weight: 600;
    }
    .wa-btn-arrow {
        font-size: 1.1rem;
    }
    html[dir="rtl"] .wa-btn-arrow i { transform: rotate(0deg); }
    html[dir="ltr"] .wa-btn-arrow i { transform: rotate(180deg); }

    .classic-system-actions-toolbar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 14px;
    }
    @media (max-width: 520px) {
        .classic-system-actions-toolbar { grid-template-columns: 1fr; }
    }
    .btn-classic-action {
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: 0.2s ease;
        text-decoration: none;
        box-sizing: border-box;
    }
    .btn-classic-action.refresh {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #1e293b;
    }
    .btn-classic-action.refresh:hover {
        background: #f1f5f9;
        border-color: #0f2744;
    }
    .btn-classic-action.logout {
        background: #ffffff;
        border: 1.5px solid #fca5a5;
        color: #b91c1c;
        width: 100%;
    }
    .btn-classic-action.logout:hover {
        background: #fef2f2;
        border-color: #ef4444;
    }

    .classic-official-seal-footer {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-top: 14px;
        border-top: 1px solid #e2e8f0;
        font-size: 11px;
        color: #64748b;
        line-height: 1.5;
    }
    .classic-official-seal-footer .seal-icon {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #15803d;
        display: grid;
        place-items: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .classic-official-seal-footer strong {
        display: block;
        color: #334155;
        font-size: 11.5px;
    }

    /* ==========================================================================
       أنماط بوابة الدفع الذكية والمتطورة (Smart Flexible Payment Portal)
       ========================================================================== */
    .smart-payment-amount-container {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        text-align: right;
    }

    .due-amount-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 14px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .due-lbl {
        font-size: 12px;
        color: #334155;
        font-weight: 700;
        display: block;
        margin-bottom: 2px;
    }
    .due-val-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .due-val {
        font-size: 1.35rem;
        color: #0f2744;
        font-weight: 900;
    }
    .due-target-badge {
        background: #e2e8f0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
    }
    .due-discount-tag {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .due-amount-banner.has-arrears {
        background: #fffbeb;
        border-color: #fcd34d;
    }
    .due-amount-banner.has-arrears .due-lbl {
        color: #92400e;
    }
    .due-amount-banner.has-arrears .due-val {
        color: #b45309;
    }
    .due-target-badge.arrears-badge {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .payment-mode-tabs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 8px;
        margin-bottom: 14px;
    }
    .payment-mode-tabs.has-arrears-grid {
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    }
    .mode-tab-btn {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 8px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        font-weight: 700;
        color: #334155;
        transition: all 0.2s ease;
    }
    .mode-tab-btn i {
        font-size: 1.05rem;
    }
    .mode-tab-btn.active {
        background: #0f2744;
        border-color: #0f2744;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(15, 39, 68, 0.2);
    }
    .mode-tab-btn.active i {
        color: #38bdf8 !important;
    }

    .custom-amount-panel {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 14px;
        animation: fadeIn 0.2s ease;
    }
    .custom-input-row {
        margin-bottom: 8px;
    }
    .custom-input-label {
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        display: block;
        margin-bottom: 6px;
    }
    .custom-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .custom-number-input {
        width: 100%;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 12px 8px 60px;
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        box-sizing: border-box;
        outline: none;
    }
    .custom-number-input:focus {
        border-color: #0f2744;
        box-shadow: 0 0 0 3px rgba(15, 39, 68, 0.12);
    }
    .currency-symbol {
        position: absolute;
        left: 12px;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
    }

    .quick-preset-chips {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        font-size: 11px;
        color: #64748b;
    }
    .chip-btn {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 3px 10px;
        font-size: 11.5px;
        font-weight: 700;
        color: #0f2744;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .chip-btn:hover {
        background: #0f2744;
        color: #ffffff;
        border-color: #0f2744;
    }

    .multi-panel-title {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }
    .multi-months-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    .multi-card-btn {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 6px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: all 0.15s ease;
    }
    .multi-card-btn:hover {
        border-color: #0f2744;
        background: #f8fafc;
    }
    .multi-card-btn .m-title {
        font-size: 11px;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .multi-card-btn .m-price {
        font-size: 13.5px;
        color: #0f2744;
        font-weight: 800;
    }
    .multi-card-btn small {
        font-size: 10px;
        color: #64748b;
        margin-top: 2px;
    }

    .live-calc-box {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1.8fr;
        gap: 8px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 14px;
        align-items: center;
    }
    .calc-item {
        display: flex;
        flex-direction: column;
    }
    .calc-item.highlight .calc-val {
        color: #0f2744;
        font-size: 1.15rem;
    }
    .calc-lbl {
        font-size: 11px;
        color: #64748b;
        font-weight: 700;
    }
    .calc-val {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
    }
    .calc-status {
        font-size: 12px;
        font-weight: 700;
        color: #15803d;
        line-height: 1.4;
    }

    @media (max-width: 680px) {
        .payment-mode-tabs {
            grid-template-columns: 1fr;
        }
        .multi-months-grid {
            grid-template-columns: 1fr 1fr;
        }
        .live-calc-box {
            grid-template-columns: 1fr;
            gap: 6px;
        }
    }

    /* ألوان وأدوات مساعدة */
    .text-emerald { color: #15803d !important; }
    .text-primary { color: #0f2744 !important; }
    .text-amber { color: #b45309 !important; }
    .text-blue { color: #1d4ed8 !important; }
    .font-mono { font-family: monospace, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, sans-serif; }
</style>
@endsection
