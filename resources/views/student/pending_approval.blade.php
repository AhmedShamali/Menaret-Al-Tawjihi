@extends('layouts.app')

@section('title', 'بانتظار موافقة الإدارة وتفعيل الاشتراك | منارة التوجيهي')

@section('content')
<div class="pending-approval-wrapper">

    <div class="pending-approval-card">
        <!-- أيقونة الحالة المتحركة -->
        <div class="pending-icon-bubble">
            <div class="pulse-ring"></div>
            <i class="fa-solid fa-hourglass-half"></i>
        </div>

        <!-- شارات الحالة -->
        <div class="status-badges-row">
            <span class="badge-tag pending"><i class="fa-solid fa-clock"></i> بانتظار موافقة المشرف العام</span>
            <span class="badge-tag palestine">🇵🇸 منارة التوجيهي</span>
        </div>

        <h1 class="card-title">طلبك قيد المراجعة والاعتماد الأكاديمي ⏳</h1>
        
        <p class="card-desc">
            أهلاً بك يا <strong>{{ $student->name_ar ?? $student->name ?? 'بطل التوجيهي' }}</strong>! تم استلام طلبك واكتمال تسجيلك بنجاح.
            يقوم المشرف العام <strong>(أ. أحمد حسين شمالي)</strong> بمراجعة بياناتك وتأكيد اشتراكك في المساقات التعليمية فور سداد الرسوم الأكاديمية المقررة.
        </p>

        <!-- بطاقة تفاصيل الطالب المسجلة -->
        <div class="student-info-strip">
            <div class="info-cell">
                <small>اسم الطالب</small>
                <strong>{{ $student->name_ar ?? $student->name }}</strong>
            </div>
            <div class="info-cell">
                <small>المرحلة والفرع</small>
                <strong>{{ optional($student->stage)->label_ar ?? optional($student->stage)->name_ar ?? 'الثانوية العامة (التوجيهي)' }}</strong>
            </div>
            <div class="info-cell">
                <small>رقم الهاتف</small>
                <strong dir="ltr">{{ $student->phone ?? '—' }}</strong>
            </div>
            <div class="info-cell">
                <small>حالة الحساب</small>
                <span class="status-pill-warning">بانتظار التفعيل ⏳</span>
            </div>
        </div>

        <!-- بطاقة تفاصيل الرسوم والخصم المعتمد -->
        <div class="fees-summary-card">
            <div class="fees-header">
                <div class="fees-header-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div>
                    <h3>الرسوم الدراسية والمنح المعتمدة</h3>
                    <p>تفاصيل الرسوم المقررة للمساقات الأكاديمية المسجلة</p>
                </div>
            </div>
            <div class="fees-grid">
                <div class="fee-box">
                    <span class="fee-title">إجمالي الرسوم</span>
                    <span class="fee-value">{{ number_format($totalAmount ?? 150, 0) }} ₪</span>
                </div>
                <div class="fee-box discount">
                    <span class="fee-title">الخصم / المنحة</span>
                    <span class="fee-value text-emerald">- {{ number_format($discountAmount ?? 0, 0) }} ₪</span>
                </div>
                <div class="fee-box net-amount">
                    <span class="fee-title">المبلغ المطلوب سداده</span>
                    <span class="fee-value text-primary-net">{{ number_format($finalAmount ?? 150, 0) }} ₪</span>
                </div>
            </div>
            @if(isset($discountAmount) && $discountAmount > 0)
                <div class="fee-note-alert">
                    <i class="fa-solid fa-gift"></i>
                    <span>مبارك! تم تطبيق منحة خاصة لحسابك بقيمة ({{ number_format($discountAmount, 0) }} ₪) تخفيضاً على الرسوم.</span>
                </div>
            @endif
        </div>

        <!-- بطاقة وسائل الدفع الفلسطينية المعتمدة -->
        <div class="payment-channels-card">
            <div class="channels-title-row">
                <i class="fa-solid fa-building-columns text-primary"></i>
                <h4>وسائل الدفع والتحويل الفلسطينية المعتمدة:</h4>
            </div>
            <p class="channels-desc">يرجى تحويل المبلغ المطلوب (<strong>{{ number_format($finalAmount ?? 150, 0) }} ₪</strong>) عبر إحدى القنوات الآتية باسم <strong>(أحمد حسين شمالي)</strong>:</p>

            <div class="channels-grid">
                <!-- بنك فلسطين -->
                <div class="channel-card">
                    <div class="channel-icon bank">🏛️</div>
                    <div class="channel-details">
                        <strong>بنك فلسطين (Bank of Palestine)</strong>
                        <span class="account-holder">المستفيد: أحمد حسين شمالي</span>
                        <div class="number-copy-row">
                            <span class="account-num" dir="ltr">0567897212</span>
                            <button type="button" class="copy-btn" onclick="copyNumber('0567897212', 'رقم بنك فلسطين')">
                                <i class="fa-regular fa-copy"></i> نسخ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- بال باي -->
                <div class="channel-card">
                    <div class="channel-icon palpay">🇵🇸</div>
                    <div class="channel-details">
                        <strong>بال باي (PalPay)</strong>
                        <span class="account-holder">المستفيد: أحمد حسين شمالي</span>
                        <div class="number-copy-row">
                            <span class="account-num" dir="ltr">0567897212</span>
                            <button type="button" class="copy-btn" onclick="copyNumber('0567897212', 'رقم PalPay')">
                                <i class="fa-regular fa-copy"></i> نسخ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- جوال باي -->
                <div class="channel-card">
                    <div class="channel-icon jpay">📱</div>
                    <div class="channel-details">
                        <strong>جوال باي (Jawwal Pay)</strong>
                        <span class="account-holder">المستفيد: أحمد حسين شمالي</span>
                        <div class="number-copy-row">
                            <span class="account-num" dir="ltr">0567897212</span>
                            <button type="button" class="copy-btn" onclick="copyNumber('0567897212', 'رقم جوال باي')">
                                <i class="fa-regular fa-copy"></i> نسخ
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
                    <h3>إرسال إشعار السداد وإرفاق الإيصال</h3>
                    <p>بعد إتمام التحويل، يرجى ملء النموذج لتقوم الإدارة بتفعيل حسابك مباشرة</p>
                </div>
            </div>

            @if(session('payment_success'))
                <div class="alert-success-box">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('payment_success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-danger-box" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; text-align: right; display: flex; align-items: flex-start; gap: 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem; color: #dc2626; margin-top: 2px;"></i>
                    <div>
                        <div style="margin-bottom: 4px;">يرجى تصحيح الأخطاء التالية:</div>
                        <ul style="margin: 0; padding-right: 20px; font-size: 0.88rem; font-weight: 600;">
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
                        <label class="input-label">وسيلة الدفع المستخدمة <span class="required">*</span></label>
                        <select name="payment_method" id="paymentMethodSelect" class="form-select-clean" required>
                            <option value="جوال باي (Jawwal Pay)">جوال باي (Jawwal Pay) - 0567897212</option>
                            <option value="بال باي (PalPay)">بال باي (PalPay) - 0567897212</option>
                            <option value="بنك فلسطين (Bank of Palestine)">بنك فلسطين - 0567897212</option>
                            <option value="ريفلكت (Reflect)">ريفلكت (Reflect)</option>
                            <option value="سداد نقدي مباشر للمشرف">سداد نقدي مباشر للمشرف</option>
                        </select>
                    </div>

                    <div class="form-group-cell">
                        <label class="input-label">رقم العملية / الحوالة (اختياري)</label>
                        <input type="text" name="reference_no" placeholder="مثال: TRX-98214" class="form-input-clean">
                    </div>
                </div>

                <div class="form-group-full">
                    <label class="input-label">إرفاق صورة الإيصال أو لقطة الشاشة <span class="required">*</span></label>
                    <div class="file-upload-box" id="fileUploadBox">
                        <input type="file" name="receipt_photo" id="receiptFileInput" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="file-input-hidden" onchange="handleFileSelected(this)">
                        <label for="receiptFileInput" class="file-upload-label">
                            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                            <span id="uploadLabelText">اضغط هنا لرفع صورة الإيصال أو ملف PDF</span>
                            <small>يقبل صور JPG, PNG أو ملف PDF بحجم أقصى 8 ميجابايت</small>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-submit-receipt" id="btnSubmitReceipt">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span id="submitBtnText">تأكيد إرسال الإشعار للإدارة لتفعيل الحساب</span>
                </button>
            </form>
        </div>

        <!-- أزرار الإجراء والتواصل المباشر مع المشرف -->
        <div class="pending-actions-wrap">
            @php
                $waMsg = urlencode("مرحباً أستاذ أحمد شمالي، أنا الطالب (" . ($student->name_ar ?? $student->name) . ") ورقم هاتفي (" . ($student->phone ?? '') . ")، قمت بإنشاء حسابي في منصة منارة التوجيهي وقمت بسداد الرسوم الأكاديمية وأرجو من حضرتك التكرم باعتماد وتفعيل حسابي واشتراكي.");
            @endphp
            <a href="https://wa.me/970567897212?text={{ $waMsg }}" target="_blank" class="btn-action-primary whatsapp" id="supervisorWhatsAppBtn">
                <i class="fa-brands fa-whatsapp"></i> تواصل مع المشرف العام (أ. أحمد شمالي) عبر واتساب
            </a>

            <div class="whatsapp-direct-info">
                <i class="fa-solid fa-phone"></i>
                <span>رقم التواصل المباشر / واتساب:</span>
                <a href="https://wa.me/970567897212?text={{ $waMsg }}" target="_blank" dir="ltr" class="phone-link">0567897212</a>
            </div>

            <div class="secondary-actions-row">
                <button type="button" class="btn-action-secondary" onclick="checkStatusRefresh()">
                    <i class="fa-solid fa-rotate-right"></i> فحص حالة الحساب وتحديث الصفحة
                </button>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-action-logout">
                        <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>

        <div class="pending-footer-note">
            <i class="fa-solid fa-shield-halved text-success"></i>
            <span>منصة منارة التوجيهي - فلسطين 🇵🇸 | بياناتك ووثائقك محفوظة بأعلى معايير الأمان الأكاديمي.</span>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function copyNumber(text, label) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `تم نسخ ${label} (${text})`,
                showConfirmButton: false,
                timer: 2000
            });
        });
    }

    function handleFileSelected(input) {
        const labelText = document.getElementById('uploadLabelText');
        if (input.files && input.files[0]) {
            labelText.innerText = `تم اختيار الملف: ${input.files[0].name} ✅`;
            labelText.style.color = '#059669';
            labelText.style.fontWeight = '800';
            const uploadBox = document.getElementById('fileUploadBox');
            if (uploadBox) uploadBox.style.borderColor = '#10b981';
        }
    }

    function validatePaymentForm(e) {
        const fileInput = document.getElementById('receiptFileInput');
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'يرجى إرفاق الإيصال 📄',
                text: 'يرجى النقر على مربع رفع الملف واختيار صورة إيصال التحويل أو ملف PDF ليتمكن المشرف من مطابقة ومراجعة سدادك فوراً.',
                confirmButtonText: 'حسناً، سأقوم برفع الإيصال',
                confirmButtonColor: '#2563eb'
            });
            return false;
        }

        const btn = document.getElementById('btnSubmitReceipt');
        const text = document.getElementById('submitBtnText');
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.75';
            btn.style.cursor = 'not-allowed';
            if (text) text.innerText = 'جاري إرسال الإشعار ورفع الإيصال للإدارة...';
        }
        return true;
    }

    function checkStatusRefresh() {
        Swal.fire({
            title: 'جاري فحص حالة الحساب...',
            text: 'يرجى الانتظار لحظات للتحقق من اعتماد المشرف',
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
        padding: 35px 20px;
        background: #f8fafc;
    }

    .pending-approval-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 26px;
        padding: 40px 32px;
        max-width: 760px;
        width: 100%;
        text-align: center;
        box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.07);
        position: relative;
    }

    /* الأيقونة النباضة */
    .pending-icon-bubble {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.3rem;
        margin-bottom: 18px;
        position: relative;
        box-shadow: 0 10px 22px rgba(245, 158, 11, 0.3);
    }
    .pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 3px solid #f59e0b;
        animation: pulseRing 2s infinite ease-out;
    }
    @keyframes pulseRing {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(1.35); opacity: 0; }
    }

    .status-badges-row {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .badge-tag {
        font-size: 0.82rem;
        font-weight: 800;
        padding: 5px 14px;
        border-radius: 50px;
    }
    .badge-tag.pending {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-tag.palestine {
        background: #0f172a;
        color: #f8fafc;
    }

    .card-title {
        font-size: 1.65rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 10px;
        line-height: 1.35;
    }
    .card-desc {
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.75;
        margin-bottom: 24px;
    }

    /* تفاصيل الطالب */
    .student-info-strip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 12px;
        margin-bottom: 22px;
        text-align: right;
    }
    .info-cell small {
        display: block;
        color: #64748b;
        font-size: 0.76rem;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .info-cell strong {
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 800;
    }
    .status-pill-warning {
        display: inline-block;
        background: #fef3c7;
        color: #b45309;
        font-weight: 800;
        font-size: 0.8rem;
        padding: 3px 10px;
        border-radius: 6px;
    }

    /* بطاقة تفاصيل الرسوم */
    .fees-summary-card {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 22px;
        text-align: right;
    }
    .fees-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .fees-header-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .fees-header h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }
    .fees-header p {
        margin: 0;
        font-size: 0.8rem;
        color: #64748b;
    }

    .fees-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1.3fr;
        gap: 12px;
        margin-bottom: 12px;
    }
    @media (max-width: 580px) {
        .fees-grid {
            grid-template-columns: 1fr;
        }
    }
    .fee-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        text-align: center;
    }
    .fee-box.discount {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }
    .fee-box.net-amount {
        background: #eff6ff;
        border-color: #bfdbfe;
    }
    .fee-title {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .fee-value {
        font-size: 1.3rem;
        font-weight: 900;
        color: #0f172a;
        font-family: inherit;
    }
    .text-emerald { color: #059669 !important; }
    .text-primary-net { color: #1d4ed8 !important; }

    .fee-note-alert {
        background: #f0fdf4;
        border: 1px solid #86efac;
        color: #166534;
        font-size: 0.82rem;
        font-weight: 700;
        border-radius: 10px;
        padding: 8px 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* وسائل الدفع المعتمدة */
    .payment-channels-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 22px;
        text-align: right;
    }
    .channels-title-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }
    .channels-title-row h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
    }
    .channels-desc {
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 16px;
    }

    .channels-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 12px;
    }
    .channel-card {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        text-align: right;
        transition: 0.2s;
    }
    .channel-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }
    .channel-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .channel-details strong {
        display: block;
        font-size: 0.86rem;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .account-holder {
        display: block;
        font-size: 0.75rem;
        color: #475569;
        margin-bottom: 6px;
    }
    .number-copy-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .account-num {
        font-size: 0.88rem;
        font-weight: 800;
        color: #1d4ed8;
        font-family: monospace;
    }
    .copy-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: 0.2s;
    }
    .copy-btn:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    /* بطاقة رفع الإيصال */
    .receipt-submission-card {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 22px;
        text-align: right;
    }
    .receipt-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .receipt-header i {
        font-size: 1.4rem;
        color: #059669;
    }
    .receipt-header h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }
    .receipt-header p {
        margin: 0;
        font-size: 0.8rem;
        color: #64748b;
    }

    .alert-success-box {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.88rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }

    .form-grid-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }
    @media (max-width: 580px) {
        .form-grid-row {
            grid-template-columns: 1fr;
        }
    }
    .form-group-cell, .form-group-full {
        text-align: right;
    }
    .input-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .required { color: #ef4444; }

    .form-select-clean, .form-input-clean {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-family: inherit;
        font-size: 0.88rem;
        background: #ffffff;
        box-sizing: border-box;
    }
    .form-select-clean:focus, .form-input-clean:focus {
        border-color: #3b82f6;
        outline: none;
    }

    .file-upload-box {
        position: relative;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 16px;
    }
    .file-input-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
        opacity: 0;
    }
    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        cursor: pointer;
        transition: 0.2s;
    }
    .file-upload-label:hover {
        background: #f1f5f9;
    }
    .upload-icon {
        font-size: 1.8rem;
        color: #3b82f6;
        margin-bottom: 6px;
    }
    .file-upload-label span {
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
    }
    .file-upload-label small {
        color: #64748b;
        font-size: 0.72rem;
        margin-top: 4px;
    }

    .btn-submit-receipt {
        width: 100%;
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
        border: none;
        padding: 13px 20px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
        transition: 0.2s;
    }
    .btn-submit-receipt:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
    }

    /* أزرار الإجراء */
    .pending-actions-wrap {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }
    .btn-action-primary.whatsapp {
        background: #25d366;
        color: white;
        text-decoration: none;
        padding: 13px 22px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 6px 16px rgba(37, 211, 102, 0.28);
        transition: 0.2s;
    }
    .btn-action-primary.whatsapp:hover {
        background: #1eb956;
        transform: translateY(-2px);
    }
    .whatsapp-direct-info {
        font-size: 0.85rem;
        color: #166534;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .phone-link {
        color: #15803d;
        text-decoration: underline;
        font-weight: 800;
    }

    .secondary-actions-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 4px;
    }
    .btn-action-secondary {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: 0.2s;
    }
    .btn-action-secondary:hover {
        background: #e2e8f0;
    }
    .btn-action-logout {
        background: none;
        border: none;
        color: #ef4444;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        padding: 8px 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-action-logout:hover {
        text-decoration: underline;
    }

    .pending-footer-note {
        font-size: 0.78rem;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-top: 1px solid #f1f5f9;
        padding-top: 16px;
    }
</style>
@endsection
