@extends('layouts.app')

@section('title', 'بوابة الدفع الإلكتروني الفلسطينية | منارة التوجيهي')

@section('content')
<div style="max-width: 1140px; margin: 0 auto; padding-bottom: 70px; animation: fadeIn 0.4s ease;">

    <!-- شريط تقدم الخطوات (Stepped Progress Bar) مثل كبرى المنصات التعليمية -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 18px 25px; margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; position: relative;">
            
            <!-- خط الربط بين الخطوات -->
            <div style="position: absolute; top: 50%; left: 60px; right: 60px; height: 3px; background: #e2e8f0; z-index: 1; transform: translateY(-50%);">
                <div style="width: 50%; height: 100%; background: linear-gradient(90deg, #10b981 0%, #0284c7 100%);"></div>
            </div>

            <!-- الخطوة 1 -->
            <div style="position: relative; z-index: 2; display: flex; align-items: center; gap: 10px; background: white; padding: 0 10px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #10b981; color: white; display: grid; place-items: center; font-weight: 800; font-size: 0.9rem;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="d-none d-md-block">
                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; display: block;">الخطوة 1</span>
                    <strong style="font-size: 0.85rem; color: #0f172a;">اختيار المواد</strong>
                </div>
            </div>

            <!-- الخطوة 2 (الحالية) -->
            <div style="position: relative; z-index: 2; display: flex; align-items: center; gap: 10px; background: white; padding: 0 10px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #0284c7; color: white; display: grid; place-items: center; font-weight: 900; font-size: 1rem; box-shadow: 0 0 0 5px rgba(2, 132, 199, 0.15);">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div class="d-none d-md-block">
                    <span style="font-size: 0.75rem; color: #0284c7; font-weight: 800; display: block;">الخطوة 2 (الآن)</span>
                    <strong style="font-size: 0.9rem; color: #0284c7;">سداد الرسوم والتحويل</strong>
                </div>
            </div>

            <!-- الخطوة 3 -->
            <div style="position: relative; z-index: 2; display: flex; align-items: center; gap: 10px; background: white; padding: 0 10px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: grid; place-items: center; font-weight: 800; font-size: 0.9rem; border: 2px solid #e2e8f0;">
                    <i class="fa-solid fa-rocket"></i>
                </div>
                <div class="d-none d-md-block">
                    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; display: block;">الخطوة 3</span>
                    <span style="font-size: 0.85rem; color: #94a3b8; font-weight: 700;">التفعيل وبدء الدراسة</span>
                </div>
            </div>

        </div>
    </div>

    <!-- الترويسة العلوية -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #0284c7; padding: 4px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; margin-bottom: 6px;">
                <i class="fa-solid fa-shield-check"></i> دفع محلي آمن ومباشر 100% داخل فلسطين 🇵🇸
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0;">
                بوابة سداد الرسوم وتفعيل باقة المواد
            </h1>
        </div>

        <a href="{{ route('student.courses.catalog') }}" style="padding: 9px 18px; border-radius: 12px; border: 1.5px solid #cbd5e1; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; background: white; transition: 0.2s;" onmouseover="this.style.borderColor='#0284c7';this.style.color='#0284c7'" onmouseout="this.style.borderColor='#cbd5e1';this.style.color='#475569'">
            <i class="fa-solid fa-arrow-right"></i> تعديل المواد المختارة
        </a>
    </div>

    <!-- بطاقة التأكيد السريع والمباشر عبر واتساب (Official WhatsApp Instant Confirmation) -->
    <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1.5px solid #86efac; border-radius: 20px; padding: 18px 24px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: 0 4px 20px rgba(34, 197, 94, 0.08);">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #22c55e; color: white; display: grid; place-items: center; font-size: 1.6rem; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); flex-shrink: 0;">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <strong style="font-size: 1rem; color: #14532d; display: block; margin-bottom: 2px;">
                    هل تفضل التأكيد والمتابعة الفورية عبر واتساب؟
                </strong>
                <span style="font-size: 0.82rem; color: #166534;">
                    تواصل مباشرة مع المشرف الأكاديمي على الرقم <code>00970597694385</code> وأرسل صورة الوصل لتفعيل موادك فوراً بنقرة واحدة!
                </span>
            </div>
        </div>
        <a href="{{ $whatsappUrl ?? 'https://wa.me/970597694385' }}" target="_blank" style="background: #16a34a; color: white; text-decoration: none; padding: 10px 22px; border-radius: 12px; font-weight: 800; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3); transition: 0.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
            <i class="fa-brands fa-whatsapp" style="font-size: 1.1rem;"></i>
            <span>تأكيد الاشتراك عبر واتساب</span>
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 25px; align-items: start;">

        <!-- قسم طرق الدفع الفلسطينية أو بطاقة الإعفاء الكامل 100% -->
        @if(($cart['total'] ?? 0) <= 0)
            <div style="background: white; border: 2px solid #86efac; border-radius: 24px; padding: 45px 30px; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.08); text-align: center;">
                <div style="width: 85px; height: 85px; border-radius: 24px; background: #ecfdf5; color: #059669; display: grid; place-items: center; font-size: 2.7rem; margin: 0 auto 20px auto; box-shadow: 0 6px 20px rgba(5, 150, 105, 0.18);">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <div style="display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; color: #059669; padding: 6px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 800; margin-bottom: 14px;">
                    <i class="fa-solid fa-sparkles"></i> منحة وإعفاء أكاديمي كامل 100% ✨
                </div>
                <h2 style="font-size: 1.6rem; font-weight: 900; color: #065f46; margin-bottom: 10px;">
                    مبارك يا بطل! حصلت على إعفاء كامل من الرسوم
                </h2>
                <p style="color: #047857; font-size: 0.95rem; line-height: 1.7; max-width: 520px; margin: 0 auto 24px auto;">
                    تم اعتماد اشتراكك في باقة المواد مجاناً بالكامل بموجب منحة وإعفاء خاص معتمد لك من قِبل إدارة المنصة
                    @if(!empty($cart['student_discount_notes']))
                        <br><strong style="background: rgba(16, 185, 129, 0.15); padding: 4px 12px; border-radius: 8px; display: inline-block; margin-top: 8px;">({{ $cart['student_discount_notes'] }})</strong>
                    @endif
                    . لا يلزمك أي دفع أو تحويل بنكي، يمكنك تفعيل موادك فوراً وبدء دراستك الآن!
                </p>

                <form id="paymentForm" onsubmit="handlePaymentSubmit(event)">
                    @csrf
                    <input type="hidden" name="gateway" value="scholarship">
                    <button type="submit" id="btnConfirmPay" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; border: none; padding: 17px 40px; border-radius: 14px; font-weight: 900; font-size: 1.15rem; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                        <span>تأكيد الاشتراك وتفعيل موادي فوراً</span>
                        <i class="fa-solid fa-graduation-cap"></i>
                    </button>
                </form>
            </div>
        @else
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-wallet" style="color: #0284c7;"></i> اختر وسيلة التحويل والدفع المعتمدة:
                    </h2>
                </div>

                <form id="paymentForm" onsubmit="handlePaymentSubmit(event)">
                    @csrf
                    <input type="hidden" name="gateway" id="selectedGateway" value="jawwal_pay">

                    <!-- تبويبات بوابات الدفع الثلاث المعتمدة في فلسطين -->
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px;">

                        <!-- 1. جوال باي (Jawwal Pay) -->
                        <label class="gateway-tab active" id="tab_jawwal_pay" onclick="selectGateway('jawwal_pay')" style="border: 2px solid #0284c7; background: #f0f9ff; border-radius: 18px; padding: 16px 20px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.2s;">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <div style="width: 46px; height: 46px; border-radius: 12px; background: #dcfce7; color: #166534; display: grid; place-items: center; font-size: 1.35rem;">
                                    <i class="fa-solid fa-mobile-screen-button"></i>
                                </div>
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <strong style="font-size: 1rem; color: #0f172a;">محفظة جوال باي (Jawwal Pay)</strong>
                                        <span style="background: #dcfce7; color: #166534; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 6px;">الأسرع تفعيلاً ⚡</span>
                                    </div>
                                    <small style="color: #64748b; font-size: 0.8rem;">تحويل فوري إلى رقم المحفظة: <strong>0567897212</strong></small>
                                </div>
                            </div>
                            <input type="radio" name="gateway_radio" checked style="accent-color: #0284c7; width: 20px; height: 20px;">
                        </label>

                        <!-- 2. بنك فلسطين (Bank of Palestine) -->
                        <label class="gateway-tab" id="tab_bop" onclick="selectGateway('bop')" style="border: 2px solid #e2e8f0; background: white; border-radius: 18px; padding: 16px 20px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.2s;">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <div style="width: 46px; height: 46px; border-radius: 12px; background: #fee2e2; color: #b91c1c; display: grid; place-items: center; font-size: 1.35rem;">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                <div>
                                    <strong style="display: block; font-size: 1rem; color: #0f172a;">بنك فلسطين (Bank of Palestine)</strong>
                                    <small style="color: #64748b; font-size: 0.8rem;">تحويل بنكي / لموبايل (Pay to Mobile): <strong>0567897212</strong></small>
                                </div>
                            </div>
                            <input type="radio" name="gateway_radio" style="accent-color: #0284c7; width: 20px; height: 20px;">
                        </label>

                        <!-- 3. بال باي (PalPay) -->
                        <label class="gateway-tab" id="tab_palpay" onclick="selectGateway('palpay')" style="border: 2px solid #e2e8f0; background: white; border-radius: 18px; padding: 16px 20px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.2s;">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <div style="width: 46px; height: 46px; border-radius: 12px; background: #e0f2fe; color: #0369a1; display: grid; place-items: center; font-size: 1.35rem;">
                                    <i class="fa-solid fa-credit-card"></i>
                                </div>
                                <div>
                                    <strong style="display: block; font-size: 1rem; color: #0f172a;">بال باي (PalPay - محفظتي ونقاط البيع)</strong>
                                    <small style="color: #64748b; font-size: 0.8rem;">عبر تطبيق محفظتي للرقم <strong>0567897212</strong> أو كود 99420</small>
                                </div>
                            </div>
                            <input type="radio" name="gateway_radio" style="accent-color: #0284c7; width: 20px; height: 20px;">
                        </label>

                    </div>

                    <!-- 1. تفاصيل حقول جوال باي -->
                    <div id="fields_jawwal_pay" class="gateway-fields" style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 18px; padding: 22px; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.85rem; color: #475569; font-weight: 700;">صاحب الحساب المستفيد:</span>
                            <strong style="font-size: 1rem; color: #0f172a;">{{ $palOwner ?? 'أحمد حسين شمالي' }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.85rem; color: #475569; font-weight: 700;">رقم محفظة التحويل المعتمد:</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong id="copyPhoneText1" style="font-size: 1.3rem; color: #059669; font-family: monospace; direction: ltr;">0567897212</strong>
                                <button type="button" onclick="copyToClipboard('0567897212', 'تم نسخ رقم محفظة جوال باي (0567897212)')" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> نسخ
                                </button>
                            </div>
                        </div>
                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">رقم المحفظة التي قمت بالتحويل منها (جوال باي) *</label>
                            <input type="text" name="wallet_phone" id="input_wallet_phone" placeholder="056xxxxxxx أو 059xxxxxxx" value="{{ $student->phone }}" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; outline: none; font-size: 1rem; direction: ltr; text-align: right; box-sizing: border-box;">
                        </div>
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 10px 14px; font-size: 0.78rem; color: #166534; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-info" style="font-size: 1rem;"></i>
                            <span>خطوات الدفع: افتح تطبيق جوال باي ⬅ تحويل ⬅ اكتب الرقم <strong>0567897212</strong> والمبلغ ⬅ التقط صورة الوصل وأرفقها بالأسفل.</span>
                        </div>
                    </div>

                    <!-- 2. تفاصيل حقول بنك فلسطين -->
                    <div id="fields_bop" class="gateway-fields" style="display: none; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 18px; padding: 22px; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.85rem; color: #475569; font-weight: 700;">اسم صاحب الحساب المستفيد:</span>
                            <strong style="font-size: 1rem; color: #0f172a;">{{ $palOwner ?? 'أحمد حسين شمالي' }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.85rem; color: #475569; font-weight: 700;">التحويل لموبايل (Pay to Mobile):</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 1.25rem; color: #b91c1c; font-family: monospace; direction: ltr;">0567897212</strong>
                                <button type="button" onclick="copyToClipboard('0567897212', 'تم نسخ رقم الهاتف للتحويل البنكي (0567897212)')" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> نسخ
                                </button>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">الآيبان الدولي (IBAN):</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <code style="font-family: monospace; font-size: 0.85rem; direction: ltr;">PS91PALS0458000000123456001</code>
                                <button type="button" onclick="copyToClipboard('PS91PALS0458000000123456001', 'تم نسخ الآيبان الدولي بنجاح')" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> نسخ
                                </button>
                            </div>
                        </div>
                        <div style="margin-bottom: 14px; margin-top: 14px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">رقم الحوالة أو اسم صاحب الحساب المُحوِّل *</label>
                            <input type="text" name="bop_ref" id="input_bop_ref" placeholder="اسم صاحب الحساب أو رقم المرجع من تطبيق بنكي" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; outline: none; font-size: 0.95rem; box-sizing: border-box;">
                        </div>
                    </div>

                    <!-- 3. تفاصيل حقول بال باي -->
                    <div id="fields_palpay" class="gateway-fields" style="display: none; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 18px; padding: 22px; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.85rem; color: #475569; font-weight: 700;">صاحب الحساب المستفيد:</span>
                            <strong style="font-size: 1rem; color: #0f172a;">{{ $palOwner ?? 'أحمد حسين شمالي' }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.85rem; color: #475569; font-weight: 700;">رقم الحساب / محفظتي بال باي:</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 1.25rem; color: #0284c7; font-family: monospace; direction: ltr;">0567897212</strong>
                                <button type="button" onclick="copyToClipboard('0567897212', 'تم نسخ رقم محفظتي بال باي (0567897212)')" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> نسخ
                                </button>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.85rem; color: #475569; font-weight: 700;">كود خدمة بال باي في نقاط البيع:</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 1.1rem; color: #0284c7; font-family: monospace;">99420</strong>
                                <button type="button" onclick="copyToClipboard('99420', 'تم نسخ كود الخدمة 99420')" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> نسخ
                                </button>
                            </div>
                        </div>
                        <div style="margin-bottom: 14px; margin-top: 14px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">رقم العملية أو المرجع (Reference No) من وصل بال باي *</label>
                            <input type="text" name="palpay_ref" id="input_palpay_ref" placeholder="مثال: PAL-458921 أو رقم الوصل" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; outline: none; font-size: 0.95rem; box-sizing: border-box;">
                        </div>
                    </div>

                    <!-- رفع إشعار السداد (Proof Upload Zone) -->
                    <div style="margin-bottom: 22px;">
                        <label style="display: block; font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">
                            <i class="fa-solid fa-cloud-arrow-up" style="color: #0284c7;"></i> إرفاق صورة إشعار أو وصل التحويل البنكي/المحفظة <span style="color: #ef4444;">*</span>
                        </label>
                        <div id="receiptUploadWrapper" onclick="document.getElementById('receipt_file_input').click()" style="border: 2px dashed #93c5fd; background: #f0f9ff; border-radius: 18px; padding: 28px 20px; text-align: center; cursor: pointer; transition: 0.2s;">
                            <div id="receiptUploadPlaceholder">
                                <div style="width: 55px; height: 55px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: grid; place-items: center; font-size: 1.5rem; margin: 0 auto 12px auto;">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                                <span style="font-size: 0.95rem; font-weight: 800; color: #0369a1; display: block; margin-bottom: 4px;">
                                    اضغط هنا لاختيار صورة إشعار التحويل (سكرين شوت أو وصل)
                                </span>
                                <small style="color: #64748b; font-size: 0.78rem;">يدعم: JPG, PNG, WEBP أو PDF (الحد الأقصى 8 ميجابايت)</small>
                            </div>

                            <div id="receiptPreviewBox" style="display: none;">
                                <img id="receiptPreviewImg" src="" alt="معاينة الإشعار" style="max-height: 200px; max-width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 12px; object-fit: contain;">
                                <div id="receiptFileName" style="font-size: 0.85rem; font-weight: 800; color: #059669; margin-bottom: 8px;">
                                    <i class="fa-solid fa-check-circle"></i> تم إرفاق الإشعار بنجاح
                                </div>
                                <button type="button" onclick="event.stopPropagation(); document.getElementById('receipt_file_input').click()" style="background: #e2e8f0; color: #334155; border: none; padding: 7px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                                    <i class="fa-solid fa-rotate"></i> تغيير الملف المرفق
                                </button>
                            </div>

                            <input type="file" name="receipt_file" id="receipt_file_input" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" required style="display: none;" onchange="handleReceiptFileChange(this)">
                        </div>
                    </div>

                    <!-- تنبيه فحص واعتماد الإدارة -->
                    <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 16px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-shield-halved" style="color: #d97706; font-size: 1.3rem; flex-shrink: 0;"></i>
                        <div style="font-size: 0.82rem; color: #92400e; line-height: 1.6;">
                            <strong>آلية اعتماد منصة منارة التوجيهي:</strong> فور إرسال الإشعار، يقوم المشرف العام بمطابقة الحوالة وتفعيل موادك رسمياً خلال دقائق، وستصلك رسالة تأكيد فورية في لوحة حسابك الدراسي.
                        </div>
                    </div>

                    <button type="submit" id="btnConfirmPay" style="width: 100%; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; border: none; padding: 17px; border-radius: 16px; font-weight: 900; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 8px 25px rgba(2, 132, 199, 0.35); transition: 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                        <span>إرسال إشعار السداد للمراجعة والتفعيل</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        @endif

        <!-- العمود الأيسر: ملخص الطلب والفاتورة والأسئلة الشائعة -->
        <div>
            <!-- ملخص الفاتورة -->
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 26px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 20px;">
                <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-receipt" style="color: #10b981;"></i> ملخص باقة المواد المختارة
                </h2>

                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                    @foreach($cart['items'] as $item)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: #f8fafc; border-radius: 14px; border: 1px solid #f1f5f9;">
                            <div>
                                <strong style="color: #0f172a; font-size: 0.92rem; display: block;">{{ $item['name_ar'] }}</strong>
                                <small style="color: #64748b; font-size: 0.75rem;">{{ $item['stage'] }}</small>
                            </div>
                            <div style="font-weight: 800; font-size: 0.95rem; color: #0284c7; font-family: monospace;">
                                {{ number_format($item['price'], 0) }} ₪
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="border-top: 1px solid #e2e8f0; padding-top: 16px; display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: #64748b;">
                        <span>المجموع الجزئي:</span>
                        <span style="font-family: monospace; font-weight: 700;">{{ number_format($cart['subtotal'], 0) }} ₪</span>
                    </div>

                    @if($cart['bundle_discount'] > 0)
                        <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: #16a34a; font-weight: 800; background: #f0fdf4; padding: 6px 10px; border-radius: 8px;">
                            <span><i class="fa-solid fa-layer-group"></i> خصم باقة التوجيهي (15%):</span>
                            <span style="font-family: monospace;">- {{ number_format($cart['bundle_discount'], 0) }} ₪</span>
                        </div>
                    @endif

                    @if(!empty($cart['student_discount']) && $cart['student_discount'] > 0)
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.88rem; color: #7c3aed; font-weight: 800; background: #faf5ff; padding: 8px 12px; border-radius: 10px; border: 1.5px dashed #ddd6fe;">
                            <span><i class="fa-solid fa-gift"></i> {{ $cart['student_discount_label'] ?? 'خصم الطالب المعتمد' }}:</span>
                            <span style="font-family: monospace;">- {{ number_format($cart['student_discount'], 0) }} ₪</span>
                        </div>
                    @endif

                    <div style="display: flex; justify-content: space-between; font-size: 1.35rem; font-weight: 900; color: #0f172a; border-top: 1.5px dashed #cbd5e1; padding-top: 14px; margin-top: 5px;">
                        <span>المبلغ المستحق للدفع:</span>
                        <span style="color: {{ ($cart['total'] ?? 0) <= 0 ? '#059669' : '#0284c7' }}; font-family: monospace;">
                            {{ ($cart['total'] ?? 0) <= 0 ? 'مجاناً 0 ₪ (إعفاء كامل)' : number_format($cart['total'], 0) . ' ₪' }}
                        </span>
                    </div>
                </div>

                <!-- ضمانات المنصة المعتمدة -->
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: #dcfce7; color: #166534; display: grid; place-items: center; font-size: 1.2rem; flex-shrink: 0;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div style="font-size: 0.8rem; color: #166534; line-height: 1.5;">
                        <strong>اشتراك موثوق ومضمون:</strong> صلاحية كاملة تشمل شروحات المنهاج الوزاري، بنك الأسئلة، الاختبارات، والمراجعات حتى نهاية الدورة الوزارية.
                    </div>
                </div>
            </div>

            <!-- الأسئلة الشائعة حول الدفع والتفعيل (FAQ) -->
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 22px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <h3 style="font-size: 1rem; font-weight: 800; color: #1e293b; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-question" style="color: #0284c7;"></i> الأسئلة الشائعة حول الدفع:
                </h3>
                
                <details style="border-bottom: 1px solid #f1f5f9; padding: 8px 0; cursor: pointer;">
                    <summary style="font-size: 0.85rem; font-weight: 700; color: #334155;">كم يستغرق تفعيل اشتراكي بعد إرسال الإشعار؟</summary>
                    <p style="font-size: 0.8rem; color: #64748b; margin: 8px 0 0; line-height: 1.6;">
                        يتم فحص وتفعيل الحسابات بشكل يدوي ودقيق من قِبل المشرف خلال 5 إلى 15 دقيقة، أو فورياً عند إرسال الإشعار عبر واتساب.
                    </p>
                </details>

                <details style="border-bottom: 1px solid #f1f5f9; padding: 8px 0; cursor: pointer;">
                    <summary style="font-size: 0.85rem; font-weight: 700; color: #334155;">هل يمكنني مراسلة المشرف مباشرة للتأكيد؟</summary>
                    <p style="font-size: 0.8rem; color: #64748b; margin: 8px 0 0; line-height: 1.6;">
                        نعم بكل تأكيد! يمكنك الضغط على زر الواتساب الأخضر في الأعلى ومراسلة المشرف على الرقم <code>00970597694385</code> ليتم تفعيل موادك باللحظة.
                    </p>
                </details>

                <details style="padding: 8px 0 0; cursor: pointer;">
                    <summary style="font-size: 0.85rem; font-weight: 700; color: #334155;">ما هي أرقام المحافظ المعتمدة في المنصة؟</summary>
                    <p style="font-size: 0.8rem; color: #64748b; margin: 8px 0 0; line-height: 1.6;">
                        رقم محفظة جوال باي وبنك فلسطين وبال باي المعتمد للمنصة هو: <strong>0567897212</strong> باسم <strong>أحمد حسين شمالي</strong>.
                    </p>
                </details>
            </div>
        </div>

    </div>
</div>

<script>
function selectGateway(gw) {
    document.getElementById('selectedGateway').value = gw;

    // تحديث مظهر التبويبات
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

function copyToClipboard(text, successMsg) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: successMsg || 'تم النسخ للحافظة بنجاح 📋',
                showConfirmButton: false,
                timer: 2000
            });
        });
    } else {
        const dummy = document.createElement("textarea");
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: successMsg || 'تم النسخ بنجاح 📋',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

function handleReceiptFileChange(input) {
    const file = input.files[0];
    if (!file) return;

    const placeholder = document.getElementById('receiptUploadPlaceholder');
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
        previewImg.style.display = 'none';
        placeholder.style.display = 'none';
        previewBox.style.display = 'block';
        fileName.innerHTML = '<i class="fa-solid fa-file-pdf" style="color: #ef4444; font-size: 1.5rem;"></i> مستند PDF: ' + file.name;
        wrapper.style.borderColor = '#10b981';
        wrapper.style.background = '#f0fdf4';
    }
}

function handlePaymentSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('paymentForm');
    const btn = document.getElementById('btnConfirmPay');
    const fileInput = document.getElementById('receipt_file_input');
    const isFree = {{ ($cart['total'] ?? 0) <= 0 ? 'true' : 'false' }};

    if (!isFree) {
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'صورة الإشعار مطلوبة',
                text: 'يرجى إرفاق صورة إشعار أو وصل التحويل البنكي/المحفظة لتتمكن إدارة المنصة من مطابقة الدفعة وتفعيل موادك.',
                confirmButtonColor: '#0284c7',
                confirmButtonText: 'حسناً'
            });
            return;
        }
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (isFree ? 'جاري تفعيل المواد...' : 'جاري رفع الإشعار وإرسال طلب الدفع...');

    const formData = new FormData(form);

    axios.post('{{ route("student.checkout.process") }}', formData)
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: isFree ? 'تم تفعيل المواد بنجاح! 🎉' : 'تم إرسال إشعار السداد بنجاح! 🎉',
                text: res.data.message || 'تمت العملية بنجاح.',
                confirmButtonColor: '#0284c7',
                confirmButtonText: 'عرض إيصال المعاملة والتفعيل'
            }).then(() => {
                window.location.href = res.data.redirect;
            });
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = isFree ? '<span>تأكيد الاشتراك وتفعيل موادي فوراً</span> <i class="fa-solid fa-graduation-cap"></i>' : '<span>إرسال إشعار السداد للمراجعة والتفعيل</span> <i class="fa-solid fa-paper-plane"></i>';
            let msg = 'تعذر استكمال السداد، يرجى مراجعة البيانات.';
            if (err.response?.data?.errors) {
                const first = Object.values(err.response.data.errors)[0];
                if (Array.isArray(first)) msg = first[0];
            } else if (err.response?.data?.message) {
                msg = err.response.data.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'تعذر إرسال الدفعة',
                text: msg,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'حسناً'
            });
        });
}
</script>
@endsection
