@extends('layouts.app')

@php
    $isFrozen = in_array($student->status, ['suspended', 'frozen', 'inactive']);
    $studentDispName = (app()->getLocale() === 'en' && !empty($student->name_en)) 
        ? $student->name_en 
        : ($student->name_ar ?? $student->name ?? __('طالبنا العزيز'));
    $stageDispName = (app()->getLocale() === 'en' && !empty($student->stage->name_en))
        ? $student->stage->name_en
        : (optional($student->stage)->label_ar ?? optional($student->stage)->name_ar ?? __('الثانوية العامة (التوجيهي)'));
@endphp

@section('title', $isFrozen ? __('الحساب مجمد مؤقتاً | منارة التوجيهي') : __('بانتظار موافقة الإدارة وتفعيل الاشتراك | منارة التوجيهي'))

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
                <span class="badge-tag palestine"><i class="fa-solid fa-landmark"></i> {{ __('منارة التوجيهي - فلسطين') }}</span>
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
                        ? ("السلام عليكم أ. أحمد شمالي، أنا الطالب (" . ($student->name_ar ?? $student->name) . ") ورقم هويتي (" . ($student->nid ?? '-') . ")، حسابي مجمد على المنصة بسبب: [" . ($student->freeze_reason ?: 'عدم سداد الرسوم أو مراجعة الإدارة') . "]. أرجو التكرم بمساعدتي لفك التجميد وإعادة تفعيل الحساب.")
                        : ("Hello Mr. Ahmed Shamali, I am student (" . ($student->name_en ?? $student->name) . ") ID (" . ($student->nid ?? '-') . "), my account is frozen. Please assist me in unfreezing and reactivating my account."));
                @endphp
                <a href="https://wa.me/970567897212?text={{ $waUnfreeze }}" 
                   target="_blank" 
                   class="btn-whatsapp-unfreeze">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>{{ __('تواصل مباشرة مع المشرف العام لفك التجميد (واتساب: 0567897212)') }}</span>
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
                <span class="badge-tag palestine"><i class="fa-solid fa-landmark"></i> {{ __('منارة التوجيهي - فلسطين') }}</span>
            </div>

            <h1 class="card-title">{{ __('طلب التحاق الطالب قيد الاعتماد الأكاديمي') }}</h1>
            
            <p class="card-desc">
                {{ __('أهلاً بك يا') }} <strong>{{ $studentDispName }}</strong>{{ __('! تم استلام طلب التحاقك واكتمال تسجيلك المبدئي بنجاح.') }}
                {{ __('يقوم المشرف العام') }} <strong>({{ __('أ. أحمد حسين شمالي') }})</strong> {{ __('بمراجعة بياناتك واعتماد اشتراكك في المواد التعليمية فور تسديد الرسوم الأكاديمية المقررة.') }}
            </p>
        @endif

        <!-- بطاقة تفاصيل الطالب المسجلة -->
        <div class="student-info-strip">
            <div class="info-cell">
                <small>{{ __('اسم الطالب') }}</small>
                <strong>{{ $studentDispName }}</strong>
            </div>
            <div class="info-cell">
                <small>{{ __('المرحلة والفرع') }}</small>
                <strong>{{ $stageDispName }}</strong>
            </div>
            <div class="info-cell">
                <small>{{ __('رقم الهاتف') }}</small>
                <strong dir="ltr">{{ $student->phone ?? '—' }}</strong>
            </div>
            <div class="info-cell">
                <small>{{ __('حالة الحساب') }}</small>
                @if($isFrozen)
                    <span class="status-pill-danger"><i class="fa-solid fa-lock"></i> {{ __('مجمد مؤقتاً') }}</span>
                @else
                    <span class="status-pill-warning"><i class="fa-solid fa-clock-rotate-left"></i> {{ __('بانتظار الاعتماد') }}</span>
                @endif
            </div>
        </div>

        <!-- بطاقة تفاصيل الرسوم والخصم المعتمد -->
        <div class="fees-summary-card">
            <div class="fees-header">
                <div class="fees-header-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div>
                    <h3>{{ __('الرسوم الدراسية والمنح المعتمدة') }}</h3>
                    <p>{{ __('تفاصيل الرسوم المقررة للمساقات الأكاديمية المسجلة') }}</p>
                </div>
            </div>
            <div class="fees-grid">
                <div class="fee-box">
                    <span class="fee-title">{{ __('إجمالي الرسوم') }}</span>
                    <span class="fee-value">{{ number_format($totalAmount ?? 150, 0) }} {{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}</span>
                </div>
                <div class="fee-box discount">
                    <span class="fee-title">{{ __('الخصم / المنحة') }}</span>
                    <span class="fee-value text-emerald">- {{ number_format($discountAmount ?? 0, 0) }} {{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}</span>
                </div>
                <div class="fee-box net-amount">
                    <span class="fee-title">{{ __('المبلغ المطلوب سداده') }}</span>
                    <span class="fee-value text-primary-net">{{ number_format($finalAmount ?? 150, 0) }} {{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}</span>
                </div>
            </div>
            @if(isset($discountAmount) && $discountAmount > 0)
                <div class="fee-note-alert">
                    <i class="fa-solid fa-gift"></i>
                    <span>{{ __('مبارك! تم تطبيق منحة خاصة لحسابك بقيمة (:amount ₪) تخفيضاً على الرسوم.', ['amount' => number_format($discountAmount, 0)]) }}</span>
                </div>
            @endif
        </div>

        <!-- بطاقة وسائل الدفع الفلسطينية المعتمدة -->
        <div class="payment-channels-card">
            <div class="channels-title-row">
                <i class="fa-solid fa-building-columns" style="color: var(--ed-primary);"></i>
                <h4>{{ __('وسائل الدفع والتحويل الفلسطينية المعتمدة:') }}</h4>
            </div>
            <p class="channels-desc">{{ __('يرجى تحويل المبلغ المطلوب (:amount ₪) عبر إحدى القنوات الآتية باسم (أحمد حسين شمالي):', ['amount' => number_format($finalAmount ?? 150, 0)]) }}</p>

            <div class="channels-grid">
                <!-- بنك فلسطين -->
                <div class="channel-card">
                    <div class="channel-icon" style="color: #b91c1c;"><i class="fa-solid fa-building-columns"></i></div>
                    <div class="channel-details">
                        <strong>{{ __('بنك فلسطين (Bank of Palestine)') }}</strong>
                        <span class="account-holder">{{ __('المستفيد المعتمد: أ. أحمد حسين شمالي') }}</span>
                        <div class="number-copy-row">
                            <span class="account-num" dir="ltr">0567897212</span>
                            <button type="button" class="copy-btn" onclick="copyNumber('0567897212', '{{ __('رقم بنك فلسطين') }}')">
                                <i class="fa-regular fa-copy"></i> {{ __('نسخ') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- بال باي -->
                <div class="channel-card">
                    <div class="channel-icon" style="color: #0284c7;"><i class="fa-solid fa-credit-card"></i></div>
                    <div class="channel-details">
                        <strong>{{ __('بال باي (PalPay)') }}</strong>
                        <span class="account-holder">{{ __('المستفيد المعتمد: أ. أحمد حسين شمالي') }}</span>
                        <div class="number-copy-row">
                            <span class="account-num" dir="ltr">0567897212</span>
                            <button type="button" class="copy-btn" onclick="copyNumber('0567897212', '{{ __('رقم PalPay') }}')">
                                <i class="fa-regular fa-copy"></i> {{ __('نسخ') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- جوال باي -->
                <div class="channel-card">
                    <div class="channel-icon" style="color: #16a34a;"><i class="fa-solid fa-mobile-screen-button"></i></div>
                    <div class="channel-details">
                        <strong>{{ __('محفظة جوال باي (Jawwal Pay)') }}</strong>
                        <span class="account-holder">{{ __('المستفيد المعتمد: أ. أحمد حسين شمالي') }}</span>
                        <div class="number-copy-row">
                            <span class="account-num" dir="ltr">0567897212</span>
                            <button type="button" class="copy-btn" onclick="copyNumber('0567897212', '{{ __('رقم جوال باي') }}')">
                                <i class="fa-regular fa-copy"></i> {{ __('نسخ') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج إرسال إشعار السداد ورفع الإيصال -->
        <div class="receipt-submission-card">
            <div class="receipt-header">
                <i class="fa-solid fa-receipt text-emerald"></i>
                <div>
                    <h3>{{ __('إرسال إشعار السداد وإرفاق الإيصال') }}</h3>
                    <p>{{ __('بعد إتمام التحويل، يرجى ملء النموذج لتقوم الإدارة بتفعيل حسابك مباشرة') }}</p>
                </div>
            </div>

            @if(session('payment_success'))
                <div class="alert-success-box" style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 10px 14px; border-radius: 8px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; font-weight: 700;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('payment_success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-danger-box" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-weight: 700; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; display: flex; align-items: flex-start; gap: 8px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.1rem; color: #dc2626; margin-top: 2px;"></i>
                    <div>
                        <div style="margin-bottom: 4px;">{{ __('يرجى تصحيح الأخطاء التالية:') }}</div>
                        <ul style="margin: 0; padding-inline-start: 18px; font-size: 0.85rem; font-weight: 600;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form id="pendingPaymentForm" action="{{ route('student.pendingPayment.submit') }}" method="POST" enctype="multipart/form-data" onsubmit="return validatePaymentForm(event)">
                @csrf
                <input type="hidden" name="amount" value="{{ $finalAmount ?? 150 }}">

                <div class="form-grid-row">
                    <div class="form-group-cell">
                        <label class="input-label">{{ __('وسيلة الدفع المستخدمة') }} <span class="required">*</span></label>
                        <select name="payment_method" id="paymentMethodSelect" class="form-select-clean" required>
                            <option value="جوال باي (Jawwal Pay)">{{ __('محفظة جوال باي (Jawwal Pay)') }} - 0567897212</option>
                            <option value="بال باي (PalPay)">{{ __('بال باي (PalPay)') }} - 0567897212</option>
                            <option value="بنك فلسطين (Bank of Palestine)">{{ __('بنك فلسطين (Bank of Palestine)') }} - 0567897212</option>
                            <option value="ريفلكت (Reflect)">{{ __('ريفلكت (Reflect)') }}</option>
                            <option value="سداد نقدي مباشر للمشرف">{{ __('سداد نقدي مباشر للمشرف') }}</option>
                        </select>
                    </div>

                    <div class="form-group-cell">
                        <label class="input-label">{{ __('رقم العملية / الحوالة (اختياري)') }}</label>
                        <input type="text" name="reference_no" placeholder="{{ __('مثال: TRX-98214') }}" class="form-input-clean">
                    </div>
                </div>

                <div class="form-group-full">
                    <label class="input-label">{{ __('إرفاق صورة الإيصال أو لقطة الشاشة') }} <span class="required">*</span></label>
                    <div class="file-upload-box" id="fileUploadBox">
                        <input type="file" name="receipt_photo" id="receiptFileInput" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="file-input-hidden" onchange="handleFileSelected(this)">
                        <label for="receiptFileInput" class="file-upload-label">
                            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                            <span id="uploadLabelText">{{ __('اضغط هنا لرفع صورة الإيصال أو ملف PDF') }}</span>
                            <small>{{ __('يقبل صور JPG, PNG أو ملف PDF بحجم أقصى 8 ميجابايت') }}</small>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-submit-receipt" id="btnSubmitReceipt">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span id="submitBtnText">{{ __('إرسال إشعار السداد ورفع الإيصال للاعتماد') }}</span>
                </button>
            </form>
        </div>

        <!-- أزرار الإجراء والتواصل المباشر مع المشرف -->
        <div class="pending-actions-wrap">
            @php
                $waMsg = urlencode(app()->getLocale() === 'ar'
                    ? ("مرحباً أستاذ أحمد شمالي، أنا الطالب (" . ($student->name_ar ?? $student->name) . ") ورقم هاتفي (" . ($student->phone ?? '') . ")، قمت بإنشاء حسابي في منصة منارة التوجيهي وقمت بسداد الرسوم الأكاديمية وأرجو من حضرتك التكرم باعتماد وتفعيل حسابي واشتراكي.")
                    : ("Hello Mr. Ahmed Shamali, I am student (" . ($student->name_en ?? $student->name) . ") phone (" . ($student->phone ?? '') . "), I registered on Menaret Al-Tawjihi platform and paid tuition. Please verify and activate my enrollment."));
            @endphp
            <a href="https://wa.me/970567897212?text={{ $waMsg }}" target="_blank" class="btn-action-primary whatsapp" id="supervisorWhatsAppBtn">
                <i class="fa-brands fa-whatsapp"></i> {{ __('تواصل مع المشرف العام (أ. أحمد شمالي) عبر واتساب') }}
            </a>

            <div class="whatsapp-direct-info">
                <i class="fa-solid fa-phone"></i>
                <span>{{ __('رقم التواصل المباشر / واتساب:') }}</span>
                <a href="https://wa.me/970567897212?text={{ $waMsg }}" target="_blank" dir="ltr" class="phone-link">0567897212</a>
            </div>

            <div class="secondary-actions-row">
                <button type="button" class="btn-action-secondary" onclick="checkStatusRefresh()">
                    <i class="fa-solid fa-rotate-right"></i> {{ __('فحص حالة الحساب وتحديث الصفحة') }}
                </button>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-action-logout">
                        <i class="fa-solid fa-right-from-bracket"></i> {{ __('تسجيل الخروج') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="pending-footer-note">
            <i class="fa-solid fa-shield-halved" style="color: var(--ed-success);"></i>
            <span>{{ __('منصة منارة التوجيهي - فلسطين 🇵🇸 | بياناتك ووثائقك محفوظة بأعلى معايير الأمان الأكاديمي.') }}</span>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    function copyNumber(text, label) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `${label} (${text})`,
                showConfirmButton: false,
                timer: 2000
            });
        });
    }

    function handleFileSelected(input) {
        const labelText = document.getElementById('uploadLabelText');
        if (input.files && input.files[0]) {
            labelText.innerText = pendingI18n.fileSelectedText.replace(':file', input.files[0].name);
            labelText.style.color = '#16a34a';
            labelText.style.fontWeight = '700';
            const uploadBox = document.getElementById('fileUploadBox');
            if (uploadBox) uploadBox.style.borderColor = '#16a34a';
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
    .pending-approval-wrapper {
        min-height: calc(100vh - 100px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 20px;
        background: #f8fafc;
    }

    .pending-approval-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 32px 28px;
        max-width: 740px;
        width: 100%;
        text-align: center;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        position: relative;
    }

    /* الأيقونة الأكاديمية الفاتحة */
    .pending-icon-bubble {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 14px;
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

    /* بطاقة تفاصيل الرسوم */
    .fees-summary-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 18px;
    }
    html[dir="rtl"] .fees-summary-card { text-align: right; }
    html[dir="ltr"] .fees-summary-card { text-align: left; }

    .fees-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    .fees-header-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .fees-header h3 {
        margin: 0;
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
    }
    .fees-header p {
        margin: 0;
        font-size: 11.5px;
        color: #64748b;
    }

    .fees-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1.2fr;
        gap: 10px;
        margin-bottom: 10px;
    }
    @media (max-width: 580px) { .fees-grid { grid-template-columns: 1fr; } }

    .fee-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        text-align: center;
    }
    .fee-box.discount { background: #f0fdf4; border-color: #bbf7d0; }
    .fee-box.net-amount { background: #eff6ff; border-color: #bfdbfe; }
    .fee-title {
        display: block;
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .fee-value {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        font-family: monospace;
    }
    .text-emerald { color: #16a34a !important; }
    .text-primary-net { color: #1d4ed8 !important; }

    .fee-note-alert {
        background: #f0fdf4;
        border: 1px solid #86efac;
        color: #166534;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* وسائل الدفع المعتمدة */
    .payment-channels-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 18px;
    }
    html[dir="rtl"] .payment-channels-card { text-align: right; }
    html[dir="ltr"] .payment-channels-card { text-align: left; }

    .channels-title-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }
    .channels-title-row h4 {
        margin: 0;
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
    }
    .channels-desc {
        color: #64748b;
        font-size: 12px;
        margin-bottom: 12px;
    }

    .channels-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 10px;
    }
    .channel-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    html[dir="rtl"] .channel-card { text-align: right; }
    html[dir="ltr"] .channel-card { text-align: left; }

    .channel-icon { font-size: 1.2rem; flex-shrink: 0; margin-top: 2px; }
    .channel-details strong {
        display: block;
        font-size: 12.5px;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .account-holder {
        display: block;
        font-size: 11px;
        color: #475569;
        margin-bottom: 4px;
    }
    .number-copy-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .account-num {
        font-size: 12.5px;
        font-weight: 700;
        color: #1d4ed8;
        font-family: monospace;
    }
    .copy-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: 0.2s;
    }
    .copy-btn:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    /* نموذج إرسال الإشعار */
    .receipt-submission-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 18px;
    }
    html[dir="rtl"] .receipt-submission-card { text-align: right; }
    html[dir="ltr"] .receipt-submission-card { text-align: left; }

    .receipt-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }
    .receipt-header h3 {
        margin: 0;
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
    }
    .receipt-header p {
        margin: 0;
        font-size: 11.5px;
        color: #64748b;
    }

    .form-grid-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 12px;
    }
    @media (max-width: 580px) { .form-grid-row { grid-template-columns: 1fr; } }

    .form-group-cell, .form-group-full {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 10px;
    }
    .input-label {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
    }
    .input-label .required { color: #ef4444; }

    .form-select-clean, .form-input-clean {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12.5px;
        font-family: inherit;
        background: #ffffff;
        outline: none;
    }
    .form-select-clean:focus, .form-input-clean:focus {
        border-color: #1d4ed8;
    }

    .file-upload-box {
        border: 2px dashed #bfdbfe;
        background: #eff6ff;
        border-radius: 8px;
        padding: 16px 12px;
        text-align: center;
        cursor: pointer;
        transition: 0.2s;
    }
    .file-input-hidden { display: none; }
    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .upload-icon {
        font-size: 1.5rem;
        color: #1d4ed8;
    }
    .file-upload-label span {
        font-size: 12.5px;
        font-weight: 700;
        color: #1e40af;
    }
    .file-upload-label small {
        font-size: 11px;
        color: #64748b;
    }

    .btn-submit-receipt {
        width: 100%;
        background: #1d4ed8;
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: var(--transition);
        margin-top: 6px;
    }
    .btn-submit-receipt:hover { background: #1e40af; }

    /* أزرار الإجراء */
    .pending-actions-wrap {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 14px;
    }
    .btn-action-primary.whatsapp {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #16a34a;
        color: white;
        padding: 9px 14px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
    }
    .whatsapp-direct-info {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 11.5px;
        color: #64748b;
    }
    .phone-link {
        font-weight: 700;
        color: #16a34a;
    }

    .secondary-actions-row {
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 4px;
    }
    .btn-action-secondary {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-action-logout {
        background: #ffffff;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .pending-footer-note {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 11px;
        color: #64748b;
        margin-top: 14px;
        padding-top: 10px;
        border-top: 1px solid #e2e8f0;
    }
</style>
@endsection
