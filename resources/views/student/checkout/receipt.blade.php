@extends('layouts.app')

@section('title', __('سند قبض واستلام مالي رسمي') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="financial-voucher-container">

    <!-- 1. شريط الإجراءات والتحكم العلوي الأنيق (يختفي عند الطباعة) -->
    <div class="no-print voucher-actions-toolbar">
        <div class="toolbar-nav-group">
            <a href="{{ route('student.subjects.index') }}" class="btn-voucher-nav" title="{{ __('العودة لمقرراتي الدراسية') }}">
                <i class="fa-solid fa-arrow-right"></i>
                <span>{{ __('العودة للمقررات والدروس') }}</span>
            </a>

            <a href="{{ route('student.subscriptions.index') }}" class="btn-voucher-nav" title="{{ __('سجل الاشتراكات الشهرية') }}">
                <i class="fa-solid fa-calendar-check text-primary"></i>
                <span>{{ __('سجل الاشتراكات الشهرية') }}</span>
            </a>
        </div>

        <div class="toolbar-print-group">
            <button type="button" onclick="window.print()" class="btn-voucher-print" title="{{ __('طباعة السند المالي الرسمي أو حفظه بصيغة PDF') }}">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة السند المالي / حفظ PDF') }}</span>
            </button>

            <a href="{{ route('courses.catalog') }}" class="btn-voucher-catalog" title="{{ __('استعراض باقات ومساقات إضافية') }}">
                <i class="fa-solid fa-layer-group"></i>
                <span>{{ __('تصفح باقات المواد') }}</span>
            </a>
        </div>
    </div>

    <!-- 2. السند المالي الأكاديمي الملكي الرسمي (Imperial Financial Voucher Document) -->
    <div class="official-financial-voucher" id="printableFinancialVoucher">
        
        <!-- الإطار الأمني المذهب العلوي -->
        <div class="voucher-top-security-bar"></div>

        <!-- ترويسة السند المالي الرسمية للديوان الأكاديمي -->
        <header class="voucher-official-header">
            <div class="header-authority-row">
                <div class="authority-seal-wrap">
                    <i class="fa-solid fa-building-columns authority-crest-icon"></i>
                    <div>
                        <span class="authority-gov-tag">{{ __('دولة فلسطين • وزارة التربية والتعليم العالي') }}</span>
                        <h2 class="authority-office-title">{{ __('ديوان الشؤون المالية والاشتراكات المركزية') }}</h2>
                    </div>
                </div>

                <!-- شارة حالة المعاملة المالية الرسمية -->
                <div class="voucher-status-stamp-wrap">
                    @if($payment->status === 'completed')
                        <div class="status-stamp-badge certified">
                            <i class="fa-solid fa-circle-check"></i>
                            <div class="stamp-text-col">
                                <strong>{{ __('معتمد ومقبوض رسمياً') }}</strong>
                                <small>{{ __('سند مالي مؤكد ونافذ') }}</small>
                            </div>
                        </div>
                    @elseif($payment->status === 'pending')
                        <div class="status-stamp-badge auditing">
                            <i class="fa-solid fa-hourglass-half fa-spin-pulse"></i>
                            <div class="stamp-text-col">
                                <strong>{{ __('قيد التدقيق والمطابقة المصرفية') }}</strong>
                                <small>{{ __('بانتظار اعتماد الإدارة المالية') }}</small>
                            </div>
                        </div>
                    @else
                        <div class="status-stamp-badge rejected">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <div class="stamp-text-col">
                                <strong>{{ __('معاملة ملغاة أو غير معتمدة') }}</strong>
                                <small>{{ __('يرجى مراجعة الإدارة') }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- عنوان الوثيقة الرسمية -->
            <div class="voucher-document-title-block">
                <h1 class="voucher-doc-main-title">
                    {{ __('سند قبض واستلام مالي إلكتروني معتمد') }}
                </h1>
                <p class="voucher-doc-sub-title">
                    Official Verified Electronic Tuition Receipt • {{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}
                </p>
                <div class="voucher-session-ribbon">
                    <span>{{ __('دورة الامتحانات العامة: :session م (العام الدراسي: :academic)', [
                        'session' => \App\Models\Setting::tawjihiSession(),
                        'academic' => \App\Models\Setting::academicYear()
                    ]) }}</span>
                </div>
            </div>
        </header>

        <!-- 3. سجل بيانات المعاملة المصرفية والسند المرجعي (Master Transaction Ledger) -->
        @php
            $details = is_array($payment->payment_details) 
                ? $payment->payment_details 
                : json_decode($payment->payment_details, true);
            $refNumber = $details['reference_no'] ?? ($details['bop_ref'] ?? ($details['palpay_ref'] ?? null));
            $monthTarget = $details['month_target'] ?? null;
        @endphp
        <div class="voucher-section-card">
            <div class="section-title-strip">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i>
                <span>{{ __('بيانات السند والمعاملة المالية:') }}</span>
            </div>

            <div class="transaction-meta-grid">
                <!-- رقم السند المالي -->
                <div class="meta-field-cell">
                    <span class="meta-field-label">{{ __('رقم السند المالي المرجعي (Voucher ID):') }}</span>
                    <strong class="meta-field-val font-mono text-primary">{{ $payment->transaction_number }}</strong>
                </div>

                <!-- تاريخ وتوقيت المعاملة -->
                <div class="meta-field-cell">
                    <span class="meta-field-label">{{ __('تاريخ وساعة التوريد المالي:') }}</span>
                    <strong class="meta-field-val font-mono">
                        {{ $payment->created_at ? $payment->created_at->format('Y-m-d | h:i A') : now()->format('Y-m-d | h:i A') }}
                    </strong>
                </div>

                <!-- وسيلة السداد / المزود المصرفي -->
                <div class="meta-field-cell">
                    <span class="meta-field-label">{{ __('وسيلة السداد والمزود المصرفي:') }}</span>
                    <strong class="meta-field-val">
                        <i class="fa-solid fa-building-columns text-primary"></i>
                        {{ __($payment->gateway_name_ar ?? $payment->gateway ?? 'سداد مصرفي معتمد') }}
                    </strong>
                </div>

                <!-- رقم الحوالة / الإشعار البنكي -->
                <div class="meta-field-cell">
                    <span class="meta-field-label">{{ __('رقم الحوالة / الإشعار المرجعي:') }}</span>
                    <strong class="meta-field-val font-mono">
                        {{ $refNumber ?: ('TXN-' . substr(md5($payment->id . $payment->created_at), 0, 8)) }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- 4. بيانات الطالب الأكاديمية المقيد لحسابه السند (Student Identity Roster) -->
        @php
            $student = $payment->student;
            $studentName = (app()->getLocale() === 'en' && !empty($student?->name_en)) 
                ? $student->name_en 
                : ($student?->name_ar ?? $student?->name ?? __('طالب الثانوية العامة'));
            $stageName = (app()->getLocale() === 'en' && !empty($student?->stage?->name_en))
                ? $student->stage->name_en
                : (optional($student?->stage)->label_ar ?? optional($student?->stage)->name_ar ?? __('الثانوية العامة (التوجيهي)'));
            $studentNid = $student?->nid ?: __('غير مسجل');
            $studentEmail = $student?->email ?: '-';
        @endphp
        <div class="voucher-section-card">
            <div class="section-title-strip">
                <i class="fa-solid fa-id-card-clip text-primary"></i>
                <span>{{ __('بيانات الطالب المقيد لحسابه السند الأكاديمي:') }}</span>
            </div>

            <div class="student-identity-grid">
                <!-- اسم الطالب الرباعي -->
                <div class="meta-field-cell">
                    <span class="meta-field-label">{{ __('اسم الطالب المشترك:') }}</span>
                    <strong class="meta-field-val">{{ $studentName }}</strong>
                </div>

                <!-- رقم الهوية الفلسطينية NID -->
                <div class="meta-field-cell">
                    <span class="meta-field-label">{{ __('رقم الهوية الوطنية الفلسطينية:') }}</span>
                    <strong class="meta-field-val font-mono">{{ $studentNid }}</strong>
                </div>

                <!-- الفرع والمرحلة الأكاديمية -->
                <div class="meta-field-cell">
                    <span class="meta-field-label">{{ __('الفرع الأكاديمي والمسار:') }}</span>
                    <strong class="meta-field-val">
                        <span class="academic-stage-pill">{{ $stageName }}</span>
                    </strong>
                </div>

                <!-- البريد الأكاديمي الرسمي -->
                <div class="meta-field-cell">
                    <span class="meta-field-label">{{ __('البريد الأكاديمي الرسمي للطالب:') }}</span>
                    <strong class="meta-field-val font-mono" dir="ltr">{{ $studentEmail }}</strong>
                </div>
            </div>
        </div>

        <!-- 5. جدول كشف البنود والمقررات الدراسية المشمولة بالسند (Itemized Financial Ledger) -->
        <div class="voucher-section-card no-padding-bottom">
            <div class="section-title-strip">
                <i class="fa-solid fa-list-check text-primary"></i>
                <span>{{ __('كشف الرسوم والمقررات الدراسية المفعلة بموجب هذا السند:') }}</span>
            </div>

            <div class="table-responsive">
                <table class="voucher-ledger-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">#</th>
                            <th style="min-width: 260px;">{{ __('المبحث الأكاديمي / بند القسط الدراسي') }}</th>
                            <th style="min-width: 180px;">{{ __('الصلاحية والاعتماد الأكاديمي') }}</th>
                            <th style="min-width: 140px; text-align: center;">{{ __('حالة التفعيل') }}</th>
                            <th style="min-width: 130px; text-align: left;">{{ __('المبلغ المقيد (ILS)') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $items = is_array($payment->items) ? $payment->items : json_decode($payment->items, true);
                            $counter = 1;
                        @endphp
                        @if(!empty($items))
                            @foreach($items as $item)
                                @php
                                    $itemSubName = (app()->getLocale() === 'en' && !empty($item['name_en'])) ? $item['name_en'] : ($item['name_ar'] ?? __('مادة دراسية'));
                                    $itemPrice = (float)($item['price'] ?? 0);
                                @endphp
                                <tr>
                                    <td style="text-align: center; color: #64748b; font-weight: 700; font-family: monospace;">
                                        {{ sprintf('%02d', $counter++) }}
                                    </td>
                                    <td>
                                        <div class="item-title-col">
                                            <strong>{{ $itemSubName }}</strong>
                                            <small>{{ __('شاملة الشروحات المرئية، الملازم الوزارية، وبنك الامتحانات التقييمية') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="item-validity-tag">
                                            <i class="fa-solid fa-calendar-days text-muted"></i>
                                            {{ __('دورة كاملة حتى نهاية امتحانات :session م', ['session' => \App\Models\Setting::tawjihiSession()]) }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($payment->status === 'completed')
                                            <span class="item-status-tag active">
                                                <i class="fa-solid fa-check"></i> {{ __('مفعل بالحساب ✅') }}
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span class="item-status-tag pending">
                                                <i class="fa-solid fa-clock"></i> {{ __('بانتظار الاعتماد ⏳') }}
                                            </span>
                                        @else
                                            <span class="item-status-tag inactive">
                                                <i class="fa-solid fa-ban"></i> {{ __('غير مفعل ❌') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: left; font-weight: 800; font-family: monospace; color: #0f172a;">
                                        {{ number_format($itemPrice, 2) }} ₪
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td style="text-align: center; color: #64748b; font-weight: 700; font-family: monospace;">01</td>
                                <td>
                                    <div class="item-title-col">
                                        <strong>{{ $monthTarget ? __('رسوم اشتراك :month', ['month' => $monthTarget]) : __('رسوم الاشتراك الدراسي المعتمد لبرنامج التوجيهي') }}</strong>
                                        <small>{{ __('قسط دراسي معتمد يشمل كامل المساقات والمحاضرات وبنك الأسئلة') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="item-validity-tag">
                                        <i class="fa-solid fa-calendar-days text-muted"></i>
                                        {{ __('العام الدراسي :academic (دورة :session م)', [
                                            'academic' => \App\Models\Setting::academicYear(),
                                            'session' => \App\Models\Setting::tawjihiSession()
                                        ]) }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    @if($payment->status === 'completed')
                                        <span class="item-status-tag active"><i class="fa-solid fa-check"></i> {{ __('مفعل ومعتمد ✅') }}</span>
                                    @else
                                        <span class="item-status-tag pending"><i class="fa-solid fa-clock"></i> {{ __('بانتظار التدقيق ⏳') }}</span>
                                    @endif
                                </td>
                                <td style="text-align: left; font-weight: 800; font-family: monospace; color: #0f172a;">
                                    {{ number_format($payment->amount, 2) }} ₪
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. صورة إشعار السداد المصرفي المرفقة للمراجعة (إن وجدت) -->
        @if($payment->receipt_path)
            <div class="voucher-section-card proof-card">
                <div class="section-title-strip">
                    <i class="fa-solid fa-receipt text-primary"></i>
                    <span>{{ __('وثيقة وإشعار التحويل المصرفي المرفق مع المعاملة:') }}</span>
                </div>

                <div class="proof-content-wrap">
                    @php
                        $isPdf = \Illuminate\Support\Str::endsWith(strtolower($payment->receipt_path), '.pdf');
                    @endphp
                    @if($isPdf)
                        <div class="pdf-proof-box">
                            <i class="fa-solid fa-file-pdf"></i>
                            <div>
                                <strong>{{ __('مستند إشعار السداد المرفق (ملف PDF معتمد)') }}</strong>
                                <p>{{ __('تم إرفاق إشعار التحويل البنكي بصيغة PDF وتخزينه في الأرشيف المالي.') }}</p>
                            </div>
                            <a href="{{ asset('storage/' . $payment->receipt_path) }}" target="_blank" class="btn-view-proof">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                <span>{{ __('استعراض المستند المالي') }}</span>
                            </a>
                        </div>
                    @else
                        <div class="image-proof-box">
                            <a href="{{ asset('storage/' . $payment->receipt_path) }}" target="_blank" title="{{ __('انقر للتكبير بالحجم الكامل') }}">
                                <img src="{{ asset('storage/' . $payment->receipt_path) }}" alt="{{ __('إشعار التحويل المصرفي') }}" class="proof-img-thumb">
                            </a>
                            <small class="proof-caption">
                                <i class="fa-solid fa-circle-check text-emerald"></i>
                                {{ __('إشعار مصرفي مؤرشف ومقيد برقم المعاملة :tx', ['tx' => $payment->transaction_number]) }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- 7. خلاصة الحساب المالي والتفقيط الرسمي (Financial Reconciliation Box) -->
        <div class="voucher-reconciliation-row">
            <div class="reconciliation-notes-col">
                <div class="currency-legal-note">
                    <i class="fa-solid fa-scale-balanced text-amber"></i>
                    <div>
                        <strong>{{ __('الإبراء والاعتماد المالي:') }}</strong>
                        <p>{{ __('يعتبر هذا السند إشعاراً مالياً رسمياً صادراً عن منظومة منارة التوجيهي، ويخضع للتدقيق والمطابقة مع الكشوفات المصرفية المعتمدة وفق الأنظمة المعمول بها في دولة فلسطين.') }}</p>
                    </div>
                </div>
            </div>

            <div class="reconciliation-totals-col">
                <div class="totals-summary-box">
                    <div class="totals-row">
                        <span class="totals-lbl">{{ __('العملة الرسمية:') }}</span>
                        <strong class="totals-val">{{ __('الشيكل الفلسطيني الجديد (ILS ₪)') }}</strong>
                    </div>

                    <div class="totals-row">
                        <span class="totals-lbl">{{ __('رسوم المعالجة الإلكترونية:') }}</span>
                        <strong class="totals-val text-emerald">{{ __('0.00 ₪ (معفية مجاناً)') }}</strong>
                    </div>

                    <div class="totals-divider"></div>

                    <div class="totals-net-row">
                        <span class="net-lbl">{{ __('صافي المبلغ المقبوض / المستحق:') }}</span>
                        <span class="net-amount-display font-mono">
                            {{ number_format($payment->amount, 2) }} ₪
                        </span>
                    </div>

                    <div class="amount-words-badge">
                        <i class="fa-solid fa-feather-pointed text-amber"></i>
                        <span>{{ __('فقط :amount شيكل فلسطيني لا غير', ['amount' => number_format($payment->amount, 0)]) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. محضر الأختام الرسمية والتوقيعات والتحقق الرقمي (Official Accreditation Block) -->
        <footer class="voucher-official-footer">
            
            <!-- 1. الختم المالي الرسمي المشفر -->
            <div class="footer-stamp-column">
                <div class="official-circular-seal {{ $payment->status === 'completed' ? 'seal-success' : 'seal-pending' }}">
                    <div class="seal-inner-ring">
                        <i class="fa-solid {{ $payment->status === 'completed' ? 'fa-stamp' : 'fa-hourglass-start' }}"></i>
                        <span class="seal-state-text">
                            {{ $payment->status === 'completed' ? __('معتمد ومقبوض') : __('قيد التدقيق') }}
                        </span>
                        <span class="seal-org-text">{{ __('منارة التوجيهي') }}</span>
                        <span class="seal-year-text">{{ \App\Models\Setting::academicYear() }}</span>
                    </div>
                </div>
                <div class="seal-caption-col">
                    <strong class="caption-title">{{ __('خاتم السداد والتحصيل الرسمي') }}</strong>
                    <span class="caption-sub">{{ __('ديوان الشؤون المالية والاشتراكات') }}</span>
                </div>
            </div>

            <!-- 2. رمز التحقق الرقمي المعتمد QR Code -->
            <div class="footer-qr-column">
                <div class="qr-code-box">
                    <i class="fa-solid fa-qrcode qr-icon"></i>
                    <span class="qr-hash-text font-mono">TX-{{ substr(md5($payment->transaction_number), 0, 10) }}</span>
                </div>
                <span class="qr-caption">{{ __('رمز التحقق الفوري والتدقيق الرقمي') }}</span>
            </div>

            <!-- 3. التوقيع والاعتماد الإداري -->
            <div class="footer-signature-column">
                <div class="signature-title">{{ __('المشرف العام وإدارة المنظومة:') }}</div>
                <div class="signature-name">{{ __('أ. أحمد حسين شمالي') }}</div>
                <div class="signature-accreditation">
                    <i class="fa-solid fa-file-circle-check text-emerald"></i>
                    <span>{{ __('توثيق مالي معتمد بموجب المنظومة المركزية') }}</span>
                </div>
            </div>

        </footer>

    </div>

    <!-- 9. شريط المتابعة المباشرة مع المشرف عبر واتساب (يختفي عند الطباعة) -->
    <div class="no-print voucher-followup-card">
        @if($payment->status === 'completed')
            <div class="followup-inner success">
                <div class="followup-icon-wrap">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="followup-text-wrap">
                    <h3>{{ __('مبارك تفعيل اشتراكك الأكاديمي بنجاح! 🎓') }}</h3>
                    <p>{{ __('تم اعتماد وتأكيد استلام الرسوم المالية لحسابك، وأصبحت كافة المساقات والدروس والاختبارات جاهزة ومتاحة للدراسة فوراً.') }}</p>
                </div>
                <a href="{{ route('student.subjects.index') }}" class="btn-followup-action primary">
                    <i class="fa-solid fa-book-open"></i>
                    <span>{{ __('الانتقال لموادي ومقرراتي الدراسية') }}</span>
                </a>
            </div>
        @elseif($payment->status === 'pending')
            <div class="followup-inner pending">
                <div class="followup-icon-wrap">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="followup-text-wrap">
                    <h3>{{ __('إشعار السداد قيد التدقيق والمطابقة المصرفية ⏳') }}</h3>
                    <p>{{ __('تم استلام بيانات الإشعار المرفوعة بنجاح. تقوم الإدارة بمطابقة التحويل المصرفي وتفعيل المواد لحسابك فورياً خلال وقت وجيز.') }}</p>
                </div>
                @php
                    $waText = urlencode("السلام عليكم أ. أحمد شمالي، أنا الطالب ({$studentName}) ورقم هويتي ({$studentNid})، قمت بسداد الرسوم ورفع إشعار العملية برقم مرجعي: [{$payment->transaction_number}] بمبلغ [{$payment->amount} ₪]. أرجو التكرم بالاعتماد وتفعيل المواد.");
                @endphp
                <a href="https://wa.me/970567897212?text={{ $waText }}" target="_blank" class="btn-followup-action whatsapp">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>{{ __('متابعة فورية مع المشرف العام (واتساب: 0567897212)') }}</span>
                </a>
            </div>
        @else
            <div class="followup-inner rejected">
                <div class="followup-icon-wrap">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="followup-text-wrap">
                    <h3>{{ __('لم يتم اعتماد عملية السداد أو تم تعليقها') }}</h3>
                    <p>{{ __('إذا قمت بالتحويل المصرفي مسبقاً، يرجى التواصل مع المشرف العام لتأكيد العملية وإعادة الاعتماد فوراً.') }}</p>
                </div>
                <a href="https://wa.me/970567897212" target="_blank" class="btn-followup-action whatsapp">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>{{ __('تواصل مع الإدارة للتحقق من المعاملة') }}</span>
                </a>
            </div>
        @endif
    </div>

</div>

<style>
    /* ==========================================================================
       التصميم المالي الملكي الكلاسيكي لسند القبض والإيصال الأكاديمي الرسمي
       (Royal Classic Academic Financial Voucher & Official E-Receipt)
       - أسلوب سندات القبض الجامعية والوزارية الفاخرة
       - باليت رسمية: كحلي ملكي (#0d1b2a, #1e3a8a)، ذهبي معتق (#b45309)، ورق رسمي (#ffffff, #f8fafc)
       - محضر أختام دائرية وتوقيع رسمي مع باركود QR معتمد
       - تحسين كامل 100% لطباعة الـ A4 وحفظ الـ PDF
       ========================================================================== */

    :root {
        --vch-navy: #0d1b2a;
        --vch-navy-light: #1e3a8a;
        --vch-gold: #b45309;
        --vch-gold-soft: #fef3c7;
        --vch-gold-border: #fde68a;
        --vch-surface: #ffffff;
        --vch-surface-alt: #f8fafc;
        --vch-border: #cbd5e1;
        --vch-border-subtle: #e2e8f0;
        --vch-text-main: #0f172a;
        --vch-text-body: #334155;
        --vch-text-muted: #64748b;
        --vch-success: #15803d;
        --vch-danger: #b91c1c;
    }

    .financial-voucher-container {
        max-width: 960px;
        margin: 0 auto;
        padding: 4px 8px 60px;
        box-sizing: border-box;
    }

    /* 1. شريط الإجراءات والتحكم العلوي */
    .voucher-actions-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
        background: #ffffff;
        border: 1px solid var(--vch-border);
        border-radius: 6px;
        padding: 12px 18px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .toolbar-nav-group,
    .toolbar-print-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-voucher-nav {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #f8fafc;
        color: var(--vch-text-body);
        border: 1px solid var(--vch-border);
        padding: 7px 14px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-voucher-nav:hover {
        background: #ffffff;
        border-color: var(--vch-navy-light);
        color: var(--vch-navy-light);
    }

    .btn-voucher-print {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--vch-navy-light);
        color: #ffffff !important;
        border: 1px solid var(--vch-navy-light);
        padding: 8px 18px;
        border-radius: 4px;
        font-size: 0.84rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(30, 58, 138, 0.2);
    }
    .btn-voucher-print:hover {
        background: #1e40af;
    }

    .btn-voucher-catalog {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #ffffff;
        color: #16a34a;
        border: 1px solid #86efac;
        padding: 7px 14px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-voucher-catalog:hover {
        background: #f0fdf4;
    }

    /* 2. وثيقة السند المالي الرسمي (The Document) */
    .official-financial-voucher {
        background: #ffffff;
        border: 1px solid var(--vch-border);
        border-radius: 6px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
        position: relative;
        overflow: hidden;
        padding: 28px 32px 34px;
        outline: 1px solid rgba(180, 83, 9, 0.2);
        outline-offset: -5px;
    }

    .voucher-top-security-bar {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 5px;
        background: linear-gradient(90deg, #0d1b2a 0%, #1e3a8a 35%, #b45309 70%, #15803d 100%);
    }

    .voucher-official-header {
        border-bottom: 2px solid var(--vch-navy);
        padding-bottom: 20px;
        margin-bottom: 22px;
    }

    .header-authority-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 16px;
    }

    .authority-seal-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .authority-crest-icon {
        font-size: 2.2rem;
        color: var(--vch-navy-light);
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        padding: 8px 10px;
    }

    .authority-gov-tag {
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--vch-gold);
        display: block;
        margin-bottom: 2px;
    }

    .authority-office-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--vch-navy);
        margin: 0;
    }

    /* شارة حالة السند المالي */
    .status-stamp-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 6px 14px;
        border-radius: 4px;
        border: 1px solid;
    }

    .status-stamp-badge i {
        font-size: 1.3rem;
    }

    .stamp-text-col {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
    }

    .stamp-text-col strong {
        font-size: 0.84rem;
    }

    .stamp-text-col small {
        font-size: 0.68rem;
    }

    .status-stamp-badge.certified {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: var(--vch-success);
    }

    .status-stamp-badge.auditing {
        background: #fffbeb;
        border-color: #fde68a;
        color: var(--vch-gold);
    }

    .status-stamp-badge.rejected {
        background: #fef2f2;
        border-color: #fecaca;
        color: var(--vch-danger);
    }

    .voucher-document-title-block {
        text-align: center;
        margin-top: 8px;
    }

    .voucher-doc-main-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--vch-navy);
        margin: 0 0 4px;
        letter-spacing: -0.2px;
    }

    .voucher-doc-sub-title {
        font-size: 0.78rem;
        color: var(--vch-text-muted);
        font-weight: 600;
        margin: 0 0 8px;
    }

    .voucher-session-ribbon {
        display: inline-block;
        background: var(--vch-gold-soft);
        border: 1px solid var(--vch-gold-border);
        color: var(--vch-gold);
        font-size: 0.76rem;
        font-weight: 700;
        padding: 3px 12px;
        border-radius: 4px;
    }

    /* 3. أقسام السند المالي */
    .voucher-section-card {
        background: #ffffff;
        border: 1px solid var(--vch-border-subtle);
        border-radius: 5px;
        margin-bottom: 18px;
        overflow: hidden;
    }

    .voucher-section-card.no-padding-bottom {
        padding-bottom: 0;
    }

    .section-title-strip {
        background: #f8fafc;
        border-bottom: 1px solid var(--vch-border-subtle);
        padding: 10px 14px;
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--vch-text-main);
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .transaction-meta-grid,
    .student-identity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        padding: 14px 16px;
    }

    .meta-field-cell {
        display: flex;
        flex-direction: column;
        gap: 3px;
        line-height: 1.35;
    }

    .meta-field-label {
        font-size: 0.72rem;
        color: var(--vch-text-muted);
        font-weight: 600;
    }

    .meta-field-val {
        font-size: 0.86rem;
        color: var(--vch-text-main);
    }

    .academic-stage-pill {
        display: inline-block;
        background: #eff6ff;
        color: var(--vch-navy-light);
        border: 1px solid #bfdbfe;
        padding: 2px 7px;
        border-radius: 3px;
        font-size: 0.76rem;
        font-weight: 700;
    }

    /* 4. جدول كشف البنود والمقررات الدراسية */
    .voucher-ledger-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
        text-align: right;
    }

    .voucher-ledger-table thead th {
        background-color: var(--vch-navy);
        color: #f8fafc;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 10px 14px;
        border-bottom: 2px solid var(--vch-gold);
        white-space: nowrap;
    }

    .voucher-ledger-table tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid var(--vch-border-subtle);
        vertical-align: middle;
        color: var(--vch-text-body);
        background: #ffffff;
    }

    .voucher-ledger-table tbody tr:nth-child(even) td {
        background-color: #fbfcfd;
    }

    .item-title-col {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .item-title-col strong {
        color: var(--vch-text-main);
        font-size: 0.86rem;
    }

    .item-title-col small {
        color: var(--vch-text-muted);
        font-size: 0.7rem;
    }

    .item-validity-tag {
        font-size: 0.74rem;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .item-status-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 3px;
        border: 1px solid;
    }

    .item-status-tag.active {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: var(--vch-success);
    }

    .item-status-tag.pending {
        background: #fffbeb;
        border-color: #fde68a;
        color: var(--vch-gold);
    }

    .item-status-tag.inactive {
        background: #fef2f2;
        border-color: #fecaca;
        color: var(--vch-danger);
    }

    /* 5. إشعار التحويل المرفوع */
    .proof-card {
        background: #fcfdfd;
    }

    .proof-content-wrap {
        padding: 14px 16px;
    }

    .pdf-proof-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        background: #ffffff;
        border: 1px solid var(--vch-border);
        border-radius: 5px;
        padding: 12px 16px;
    }

    .pdf-proof-box i {
        font-size: 2rem;
        color: #ef4444;
    }

    .pdf-proof-box strong {
        font-size: 0.84rem;
        color: var(--vch-text-main);
        display: block;
        margin-bottom: 2px;
    }

    .pdf-proof-box p {
        font-size: 0.72rem;
        color: var(--vch-text-muted);
        margin: 0;
    }

    .btn-view-proof {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: var(--vch-navy-light);
        border: 1px solid var(--vch-border);
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.76rem;
        font-weight: 700;
        text-decoration: none;
    }
    .btn-view-proof:hover {
        background: #e2e8f0;
    }

    .image-proof-box {
        text-align: center;
        background: #ffffff;
        border: 1px solid var(--vch-border);
        border-radius: 5px;
        padding: 12px;
    }

    .proof-img-thumb {
        max-height: 200px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 4px;
        border: 1px solid var(--vch-border-subtle);
    }

    .proof-caption {
        display: block;
        font-size: 0.72rem;
        color: var(--vch-text-muted);
        margin-top: 6px;
    }

    /* 6. خلاصة الحساب والتفقيط */
    .voucher-reconciliation-row {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        margin-bottom: 22px;
        align-items: center;
    }

    .currency-legal-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid var(--vch-border-subtle);
        border-radius: 5px;
        padding: 12px 14px;
    }

    .currency-legal-note i {
        font-size: 1.2rem;
        margin-top: 2px;
    }

    .currency-legal-note strong {
        font-size: 0.8rem;
        color: var(--vch-text-main);
        display: block;
        margin-bottom: 3px;
    }

    .currency-legal-note p {
        font-size: 0.72rem;
        color: var(--vch-text-muted);
        margin: 0;
        line-height: 1.5;
    }

    .totals-summary-box {
        background: #f8fafc;
        border: 1px solid var(--vch-border);
        border-radius: 5px;
        padding: 14px 16px;
    }

    .totals-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.78rem;
        margin-bottom: 6px;
        color: var(--vch-text-muted);
    }

    .totals-val {
        color: var(--vch-text-main);
    }

    .totals-divider {
        height: 1px;
        background: var(--vch-border);
        margin: 8px 0;
    }

    .totals-net-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 8px;
    }

    .net-lbl {
        font-size: 0.86rem;
        font-weight: 800;
        color: var(--vch-navy);
    }

    .net-amount-display {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--vch-navy-light);
    }

    .amount-words-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid var(--vch-border-subtle);
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--vch-gold);
    }

    /* 7. محضر الأختام الرسمية والتوقيعات والتحقق */
    .voucher-official-footer {
        display: grid;
        grid-template-columns: 1.2fr 1fr 1.2fr;
        gap: 16px;
        align-items: center;
        border-top: 2px solid var(--vch-navy);
        padding-top: 20px;
    }

    .footer-stamp-column {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .official-circular-seal {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        border: 2px double;
        display: grid;
        place-items: center;
        padding: 3px;
        flex-shrink: 0;
        transform: rotate(-3deg);
    }

    .seal-inner-ring {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 1px dashed;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2px;
    }

    .official-circular-seal.seal-success {
        border-color: var(--vch-success);
        background: #f0fdf4;
        color: var(--vch-success);
    }

    .official-circular-seal.seal-pending {
        border-color: var(--vch-gold);
        background: #fffbeb;
        color: var(--vch-gold);
    }

    .seal-state-text {
        font-size: 0.58rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .seal-org-text {
        font-size: 0.52rem;
        font-weight: 700;
    }

    .seal-year-text {
        font-size: 0.48rem;
        font-family: monospace;
    }

    .seal-caption-col {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .caption-title {
        font-size: 0.78rem;
        color: var(--vch-text-main);
    }

    .caption-sub {
        font-size: 0.68rem;
        color: var(--vch-text-muted);
    }

    .footer-qr-column {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        text-align: center;
    }

    .qr-code-box {
        background: #ffffff;
        border: 1px solid var(--vch-border);
        border-radius: 5px;
        padding: 6px 10px;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
    }

    .qr-icon {
        font-size: 2.2rem;
        color: var(--vch-navy);
    }

    .qr-hash-text {
        font-size: 0.64rem;
        color: var(--vch-text-muted);
        margin-top: 2px;
    }

    .qr-caption {
        font-size: 0.66rem;
        color: var(--vch-text-muted);
    }

    .footer-signature-column {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    html[dir="rtl"] .footer-signature-column {
        text-align: left;
    }
    html[dir="ltr"] .footer-signature-column {
        text-align: right;
    }

    .signature-title {
        font-size: 0.72rem;
        color: var(--vch-text-muted);
    }

    .signature-name {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--vch-navy);
    }

    .signature-accreditation {
        font-size: 0.68rem;
        color: var(--vch-success);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* 8. بطاقة المتابعة المباشرة عبر واتساب */
    .voucher-followup-card {
        margin-top: 20px;
        background: #ffffff;
        border: 1px solid var(--vch-border);
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .followup-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        padding: 18px 22px;
        border-left: 4px solid;
    }

    html[dir="rtl"] .followup-inner {
        border-left: none;
        border-right: 4px solid;
    }

    .followup-inner.success {
        border-color: var(--vch-success);
        background: #f0fdf4;
    }
    .followup-inner.pending {
        border-color: var(--vch-gold);
        background: #fffbeb;
    }
    .followup-inner.rejected {
        border-color: var(--vch-danger);
        background: #fef2f2;
    }

    .followup-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        display: grid;
        place-items: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .followup-inner.success .followup-icon-wrap { background: #dcfce7; color: var(--vch-success); }
    .followup-inner.pending .followup-icon-wrap { background: #fef3c7; color: var(--vch-gold); }
    .followup-inner.rejected .followup-icon-wrap { background: #fee2e2; color: var(--vch-danger); }

    .followup-text-wrap {
        flex: 1;
        min-width: 260px;
    }

    .followup-text-wrap h3 {
        font-size: 0.95rem;
        font-weight: 800;
        margin: 0 0 3px;
        color: var(--vch-text-main);
    }

    .followup-text-wrap p {
        font-size: 0.78rem;
        color: var(--vch-text-muted);
        margin: 0;
        line-height: 1.5;
    }

    .btn-followup-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 4px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-followup-action.primary {
        background: var(--vch-navy-light);
        color: #ffffff !important;
    }
    .btn-followup-action.primary:hover {
        background: #1e40af;
    }

    .btn-followup-action.whatsapp {
        background: #16a34a;
        color: #ffffff !important;
    }
    .btn-followup-action.whatsapp:hover {
        background: #15803d;
    }

    /* أدوات عامة */
    .text-primary { color: var(--vch-navy-light) !important; }
    .text-amber { color: var(--vch-gold) !important; }
    .text-emerald { color: var(--vch-success) !important; }
    .font-mono { font-family: monospace, sans-serif; }

    /* استجابة الشاشات الصغيرة */
    @media (max-width: 820px) {
        .voucher-reconciliation-row {
            grid-template-columns: 1fr;
        }
        .voucher-official-footer {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 20px;
        }
        .footer-stamp-column {
            justify-content: center;
        }
        .footer-signature-column {
            text-align: center !important;
        }
    }

    /* 10. تحسينات الطباعة الرسمية للوثائق والفواتير (Print & PDF) */
    @media print {
        .no-print, aside, header, nav, footer, .top-bar, .sidebar {
            display: none !important;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-family: 'Tajawal', sans-serif !important;
        }
        .financial-voucher-container {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .official-financial-voucher {
            border: 1px solid #000000 !important;
            box-shadow: none !important;
            padding: 20px !important;
            outline: none !important;
        }
        .voucher-ledger-table thead th {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
            border-bottom: 2px solid #000000 !important;
        }
    }
</style>
@endsection
