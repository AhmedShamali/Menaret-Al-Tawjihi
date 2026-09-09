@extends('layouts.app')

@section('title', 'بوابة الدفع الإلكتروني الفلسطينية | منارة التوجيهي')

@section('content')
<div style="max-width: 1100px; margin: 0 auto; animation: fadeIn 0.4s ease;">

    <!-- ترويسة البوابة -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #0284c7; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; margin-bottom: 6px;">
                <i class="fa-solid fa-shield-check"></i> دفع آمن ومحلي 100% داخل فلسطين 🇵🇸
            </div>
            <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0;">
                بوابة سداد الرسوم وتفعيل اشتراك المواد
            </h1>
        </div>

        <a href="{{ route('student.courses.catalog') }}" style="padding: 9px 18px; border-radius: 10px; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; background: white;">
            <i class="fa-solid fa-arrow-right"></i> تعديل المواد المختارة
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 25px; align-items: start;">

        <!-- قسم طرق الدفع الفلسطينية -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 22px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-wallet" style="color: #0284c7;"></i> اختر وسيلة الدفع الفلسطينية المعتمدة
            </h2>

            <form id="paymentForm" onsubmit="handlePaymentSubmit(event)">
                @csrf
                <input type="hidden" name="gateway" id="selectedGateway" value="jawwal_pay">

                <!-- تبويبات بوابات الدفع -->
                <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 25px;">

                    <!-- 1. جوال باي -->
                    <label class="gateway-tab active" id="tab_jawwal_pay" onclick="selectGateway('jawwal_pay')" style="border: 2px solid #0284c7; background: #f0f9ff; border-radius: 16px; padding: 16px 20px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.2s;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #dcfce7; color: #166534; display: grid; place-items: center; font-size: 1.3rem;">
                                <i class="fa-solid fa-mobile-screen-button"></i>
                            </div>
                            <div>
                                <strong style="display: block; font-size: 0.95rem; color: #0f172a;">محفظة جوال باي (Jawwal Pay)</strong>
                                <small style="color: #64748b; font-size: 0.78rem;">تحويل فوري وسلس عبر رقم الهاتف أو QR</small>
                            </div>
                        </div>
                        <input type="radio" name="gateway_radio" checked style="accent-color: #0284c7; width: 18px; height: 18px;">
                    </label>

                    <!-- 2. بال باي -->
                    <label class="gateway-tab" id="tab_palpay" onclick="selectGateway('palpay')" style="border: 2px solid #e2e8f0; background: white; border-radius: 16px; padding: 16px 20px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.2s;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #e0f2fe; color: #0369a1; display: grid; place-items: center; font-size: 1.3rem;">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <div>
                                <strong style="display: block; font-size: 0.95rem; color: #0f172a;">بال باي (PalPay - محفظتي ونقاط البيع)</strong>
                                <small style="color: #64748b; font-size: 0.78rem;">عبر تطبيق محفظتي أو أي نقطة بال باي في فلسطين</small>
                            </div>
                        </div>
                        <input type="radio" name="gateway_radio" style="accent-color: #0284c7; width: 18px; height: 18px;">
                    </label>

                    <!-- 3. بنك فلسطين -->
                    <label class="gateway-tab" id="tab_bop" onclick="selectGateway('bop')" style="border: 2px solid #e2e8f0; background: white; border-radius: 16px; padding: 16px 20px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.2s;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #fee2e2; color: #b91c1c; display: grid; place-items: center; font-size: 1.3rem;">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div>
                                <strong style="display: block; font-size: 0.95rem; color: #0f172a;">بنك فلسطين (Bank of Palestine)</strong>
                                <small style="color: #64748b; font-size: 0.78rem;">حوالة بنكية مباشرة عبر الآيبان (IBAN) أو الإيداع</small>
                            </div>
                        </div>
                        <input type="radio" name="gateway_radio" style="accent-color: #0284c7; width: 18px; height: 18px;">
                    </label>

                </div>

                <!-- حقول تفاصيل طريقة الدفع المختارة -->
                <div id="fields_jawwal_pay" class="gateway-fields" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1;">
                        <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">صاحب الحساب المستفيد:</span>
                        <strong style="font-size: 0.95rem; color: #0f172a;">{{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1;">
                        <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">رقم محفظة التحويل (جوال / أوريدو):</span>
                        <strong style="font-size: 1.2rem; color: #059669; font-family: monospace; direction: ltr;">{{ \App\Models\Setting::get('payment_phone', '0567897212') }}</strong>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">رقم المحفظة التي قمت بالتحويل منها *</label>
                        <input type="text" name="wallet_phone" id="input_wallet_phone" placeholder="056xxxxxxx أو 059xxxxxxx" value="{{ $student->phone }}" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem; direction: ltr; text-align: right;">
                    </div>
                    <small style="color: #64748b; font-size: 0.75rem;">
                        <i class="fa-solid fa-circle-info"></i> سيتم مطابقة رقم العملية وتفعيل الحساب بشكل فوري وتلقائي.
                    </small>
                </div>

                <div id="fields_palpay" class="gateway-fields" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1;">
                        <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">المستفيد المعتمد:</span>
                        <strong style="font-size: 0.95rem; color: #0f172a;">{{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1;">
                        <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">كود السداد في بال باي:</span>
                        <strong style="font-size: 1.2rem; color: #0284c7; font-family: monospace;">{{ \App\Models\Setting::get('palpay_service_code', '99420') }}</strong>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">رقم إيصال السداد أو المرجع في بال باي *</label>
                        <input type="text" name="palpay_ref" id="input_palpay_ref" placeholder="مثال: PAL-458921" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;">
                    </div>
                </div>

                <div id="fields_bop" class="gateway-fields" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 25px;">
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px dashed #cbd5e1;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                            <span style="color: #64748b;">اسم المستفيد:</span>
                            <strong style="color: #0f172a;">{{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                            <span style="color: #64748b;">رقم الهاتف المرتبط:</span>
                            <strong style="font-family: monospace; direction: ltr;">{{ \App\Models\Setting::get('payment_phone', '0567897212') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                            <span style="color: #64748b;">رقم الحساب:</span>
                            <strong style="font-family: monospace;">{{ \App\Models\Setting::get('payment_account_no', '0458-123456-001') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                            <span style="color: #64748b;">رقم الآيبان (IBAN):</span>
                            <strong style="font-family: monospace; font-size: 0.8rem; direction: ltr;">{{ \App\Models\Setting::get('payment_iban', 'PS91PALS0458000000123456001') }}</strong>
                        </div>
                    </div>
                    <div style="margin-bottom: 6px;">
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">رقم الحوالة أو اسم صاحب الحساب المحوّل *</label>
                        <input type="text" name="bop_ref" id="input_bop_ref" placeholder="رقم الحوالة أو اسم المحوّل" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;">
                    </div>
                </div>

                <!-- قسم رفع صورة إشعار التحويل البنكي أو المحفظة (مطلوب لجميع البوابات) -->
                <div style="background: #ffffff; border: 2px dashed #0284c7; border-radius: 18px; padding: 22px; margin-bottom: 25px; transition: 0.2s;" id="receiptUploadWrapper">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <label style="font-size: 0.92rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; margin: 0;">
                            <i class="fa-solid fa-camera-retro" style="color: #0284c7; font-size: 1.1rem;"></i>
                            <span>إرفاق صورة إشعار أو وصل التحويل (ضروري للاعتماد)</span>
                            <span style="color: #ef4444;">*</span>
                        </label>
                        <span style="font-size: 0.75rem; color: #0284c7; background: #f0f9ff; padding: 3px 10px; border-radius: 12px; font-weight: 700;">
                            يدعم: JPG, PNG, WEBP, PDF
                        </span>
                    </div>

                    <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 14px; line-height: 1.5;">
                        يرجى أخذ لقطة شاشة (سكرين شوت) واضحة لرسالة التحويل من تطبيق المحفظة (جوال باي / بال باي) أو تصوير وصل التحويل البنكي ورفعه هنا لتتم مراجعته واعتماد اشتراكك من قِبل إدارة المنصة.
                    </p>

                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; border-radius: 14px; padding: 18px; border: 1px solid #e2e8f0; cursor: pointer; text-align: center;" onclick="document.getElementById('receipt_file_input').click()" id="dropzoneArea">
                        <div id="uploadPlaceholder">
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: grid; place-items: center; font-size: 1.3rem; margin: 0 auto 10px;">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <strong style="display: block; font-size: 0.88rem; color: #1e293b; margin-bottom: 4px;">اضغط هنا لاختيار صورة الإشعار أو السكرين شوت</strong>
                            <small style="color: #94a3b8; font-size: 0.75rem;">الحد الأقصى للملف: 8 ميجابايت</small>
                        </div>

                        <!-- معاينة الصورة عند اختيارها -->
                        <div id="receiptPreviewBox" style="display: none; width: 100%;">
                            <img id="receiptPreviewImg" src="" alt="معاينة الإشعار" style="max-height: 180px; max-width: 100%; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 10px; object-fit: contain;">
                            <div id="receiptFileName" style="font-size: 0.82rem; font-weight: 700; color: #059669; margin-bottom: 6px;">
                                <i class="fa-solid fa-check-circle"></i> تم إرفاق الإشعار بنجاح
                            </div>
                            <button type="button" onclick="event.stopPropagation(); document.getElementById('receipt_file_input').click()" style="background: #e2e8f0; color: #334155; border: none; padding: 6px 14px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                                تغيير الملف المرفق
                            </button>
                        </div>

                        <input type="file" name="receipt_file" id="receipt_file_input" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" required style="display: none;" onchange="handleReceiptFileChange(this)">
                    </div>
                </div>

                <!-- تنبيه نظام المراجعة والاعتماد -->
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; padding: 12px 16px; margin-bottom: 22px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-shield-halved" style="color: #d97706; font-size: 1.1rem; flex-shrink: 0;"></i>
                    <div style="font-size: 0.8rem; color: #92400e; line-height: 1.5;">
                        <strong>نظام منصة منارة التوجيهي:</strong> بعد إرسال الإشعار، سيقوم مدير المنصة بمطابقة الحوالة واعتماد تفعيل موادك فوراً، وستصلك رسالة تأكيد في حسابك.
                    </div>
                </div>

                <button type="submit" id="btnConfirmPay" style="width: 100%; background: linear-gradient(135deg, #0284c7, #0369a1); color: white; border: none; padding: 16px; border-radius: 14px; font-weight: 800; font-size: 1.05rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 8px 25px rgba(2, 132, 199, 0.3); transition: 0.2s;">
                    <span>إرسال إشعار السداد للمراجعة والاعتماد</span>
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>

        <!-- ملخص الطلب والفاتورة -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 22px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-receipt" style="color: #10b981;"></i> ملخص باقة المواد المختارة
            </h2>

            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 22px;">
                @foreach($cart['items'] as $item)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                        <div>
                            <strong style="color: #0f172a; font-size: 0.9rem; display: block;">{{ $item['name_ar'] }}</strong>
                            <small style="color: #64748b; font-size: 0.75rem;">{{ $item['stage'] }}</small>
                        </div>
                        <div style="font-weight: 800; font-size: 0.95rem; color: #0284c7; font-family: monospace;">
                            {{ number_format($item['price'], 0) }} ₪
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="border-top: 1px solid #e2e8f0; padding-top: 18px; display: flex; flex-direction: column; gap: 10px; margin-bottom: 22px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: #64748b;">
                    <span>المجموع الجزئي:</span>
                    <span style="font-family: monospace; font-weight: 700;">{{ number_format($cart['subtotal'], 0) }} ₪</span>
                </div>

                @if($cart['bundle_discount'] > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: #16a34a; font-weight: 700;">
                        <span>خصم باقة التوجيهي (15%):</span>
                        <span style="font-family: monospace;">- {{ number_format($cart['bundle_discount'], 0) }} ₪</span>
                    </div>
                @endif

                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 900; color: #0f172a; border-top: 1px dashed #cbd5e1; padding-top: 12px; margin-top: 5px;">
                    <span>المبلغ المستحق للدفع:</span>
                    <span style="color: #0284c7; font-family: monospace;">{{ number_format($cart['total'], 0) }} ₪</span>
                </div>
            </div>

            <!-- ضمان المنصة -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #dcfce7; color: #166534; display: grid; place-items: center; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div style="font-size: 0.78rem; color: #166534; line-height: 1.5;">
                    <strong>اشتراك موثوق ومضمون:</strong> صلاحية كاملة تشمل كافة الشروحات والاختبارات التفاعلية حتى انتهاء الدورة الوزارية.
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function selectGateway(gw) {
    document.getElementById('selectedGateway').value = gw;

    // تحديث شكل التبويبات
    document.querySelectorAll('.gateway-tab').forEach(tab => {
        tab.style.borderColor = '#e2e8f0';
        tab.style.background = 'white';
        const radio = tab.querySelector('input[type="radio"]');
        if (radio) radio.checked = false;
    });

    const activeTab = document.getElementById('tab_' + gw);
    if (activeTab) {
        activeTab.style.borderColor = '#0284c7';
        activeTab.style.background = '#f0f9ff';
        const radio = activeTab.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    // إظهار حقول الطريقة المحددة
    document.querySelectorAll('.gateway-fields').forEach(f => f.style.display = 'none');
    const activeFields = document.getElementById('fields_' + gw);
    if (activeFields) activeFields.style.display = 'block';
}

function handleReceiptFileChange(input) {
    const file = input.files[0];
    if (!file) return;

    const placeholder = document.getElementById('uploadPlaceholder');
    const previewBox = document.getElementById('receiptPreviewBox');
    const previewImg = document.getElementById('receiptPreviewImg');
    const fileName = document.getElementById('receiptFileName');
    const wrapper = document.getElementById('receiptUploadWrapper');

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            placeholder.style.display = 'none';
            previewBox.style.display = 'block';
            fileName.innerHTML = '<i class="fa-solid fa-check-circle"></i> ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
            wrapper.style.borderColor = '#10b981';
            wrapper.style.background = '#f0fdf4';
        };
        reader.readAsDataURL(file);
    } else {
        // PDF
        previewImg.style.display = 'none';
        placeholder.style.display = 'none';
        previewBox.style.display = 'block';
        fileName.innerHTML = '<i class="fa-solid fa-file-pdf" style="color: #ef4444; font-size: 1.4rem;"></i> مستند PDF: ' + file.name;
        wrapper.style.borderColor = '#10b981';
        wrapper.style.background = '#f0fdf4';
    }
}

function handlePaymentSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('paymentForm');
    const btn = document.getElementById('btnConfirmPay');
    const fileInput = document.getElementById('receipt_file_input');

    if (!fileInput.files || fileInput.files.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'صورة الإشعار مطلوبة',
            text: 'يرجى إرفاق صورة إشعار أو وصل التحويل البنكي/المحفظة لتتمكن إدارة المنصة من مطابقة الدفعة وتفعيل موادك.'
        });
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري رفع الإشعار وإرسال طلب الدفع...';

    const formData = new FormData(form);

    axios.post('{{ route("student.checkout.process") }}', formData)
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: 'تم إرسال إشعار السداد بنجاح! 🎉',
                text: res.data.message || 'طلبك قيد المراجعة والتدقيق من قِبل إدارة المنصة، وسيتم تفعيل موادك فور التأكد من الإشعار.',
                confirmButtonColor: '#0284c7',
                confirmButtonText: 'عرض إيصال المعاملة'
            }).then(() => {
                window.location.href = res.data.redirect;
            });
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span>إرسال إشعار السداد للمراجعة والاعتماد</span> <i class="fa-solid fa-paper-plane"></i>';
            let msg = 'تعذر استكمال السداد، يرجى مراجعة البيانات وصورة الإشعار.';
            if (err.response?.data?.errors) {
                const first = Object.values(err.response.data.errors)[0];
                if (Array.isArray(first)) msg = first[0];
            } else if (err.response?.data?.message) {
                msg = err.response.data.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'تعذر إرسال الدفعة',
                text: msg
            });
        });
}
</script>
@endsection
