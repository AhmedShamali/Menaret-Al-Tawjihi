@extends('layouts.app')

@section('title', __('بوابة الدفع الإلكتروني الفلسطينية | منارة التوجيهي'))

@section('content')
<div style="max-width: 1140px; margin: 0 auto; padding-bottom: 70px; animation: fadeIn 0.3s ease;">

    <!-- شريط تقدم الخطوات (Stepped Progress Bar) النظيف الأكاديمي -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 24px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="display: flex; justify-content: space-between; align-items: center; position: relative;">
            
            <!-- خط الربط بين الخطوات -->
            <div style="position: absolute; top: 50%; left: 60px; right: 60px; height: 2px; background: #e2e8f0; z-index: 1; transform: translateY(-50%);">
                <div style="width: 50%; height: 100%; background: #1d4ed8;"></div>
            </div>

            <!-- الخطوة 1 -->
            <div style="position: relative; z-index: 2; display: flex; align-items: center; gap: 10px; background: #ffffff; padding: 0 10px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #16a34a; color: white; display: grid; place-items: center; font-weight: 700; font-size: 0.85rem;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="d-none d-md-block">
                    <span style="font-size: 0.72rem; color: #64748b; font-weight: 600; display: block;">{{ __('الخطوة 1') }}</span>
                    <strong style="font-size: 0.82rem; color: #0f172a;">{{ __('اختيار المواد') }}</strong>
                </div>
            </div>

            <!-- الخطوة 2 (الحالية) -->
            <div style="position: relative; z-index: 2; display: flex; align-items: center; gap: 10px; background: #ffffff; padding: 0 10px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #1d4ed8; color: white; display: grid; place-items: center; font-weight: 800; font-size: 0.95rem; box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.15);">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div class="d-none d-md-block">
                    <span style="font-size: 0.72rem; color: #1d4ed8; font-weight: 700; display: block;">{{ __('الخطوة 2 (الآن)') }}</span>
                    <strong style="font-size: 0.85rem; color: #1d4ed8;">{{ __('سداد الرسوم والتحويل') }}</strong>
                </div>
            </div>

            <!-- الخطوة 3 -->
            <div style="position: relative; z-index: 2; display: flex; align-items: center; gap: 10px; background: #ffffff; padding: 0 10px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #f8fafc; color: #94a3b8; display: grid; place-items: center; font-weight: 700; font-size: 0.85rem; border: 1px solid #e2e8f0;">
                    <i class="fa-solid fa-rocket"></i>
                </div>
                <div class="d-none d-md-block">
                    <span style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; display: block;">{{ __('الخطوة 3') }}</span>
                    <span style="font-size: 0.82rem; color: #94a3b8; font-weight: 700;">{{ __('التفعيل وبدء الدراسة') }}</span>
                </div>
            </div>

        </div>
    </div>

    <!-- الترويسة العلوية -->
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #1d4ed8; padding: 3px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; margin-bottom: 4px; border: 1px solid #bfdbfe;">
                <i class="fa-solid fa-shield-check"></i> {{ __('دفع محلي آمن ومباشر 100% داخل فلسطين 🇵🇸') }}
            </div>
            <h1 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; margin: 0;">
                {{ __('بوابة سداد الرسوم وتفعيل باقة المواد') }}
            </h1>
        </div>

        <a href="{{ route('student.courses.catalog') }}" style="padding: 7px 16px; border-radius: 8px; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; background: #ffffff; transition: 0.2s;" onmouseover="this.style.borderColor='#1d4ed8';this.style.color='#1d4ed8'" onmouseout="this.style.borderColor='#cbd5e1';this.style.color='#475569'">
            <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('تعديل المواد المختارة') }}
        </a>
    </div>

    <!-- بطاقة التأكيد السريع والمباشر عبر واتساب -->
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #16a34a; color: white; display: grid; place-items: center; font-size: 1.3rem; flex-shrink: 0;">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <strong style="font-size: 0.92rem; color: #14532d; display: block; margin-bottom: 2px;">
                    {{ __('هل تفضل التأكيد والمتابعة الفورية عبر واتساب؟') }}
                </strong>
                <span style="font-size: 0.8rem; color: #166534;">
                    {{ __('تواصل مباشرة مع المشرف الأكاديمي على الرقم :phone وأرسل صورة الوصل لتفعيل موادك فوراً بنقرة واحدة!', ['phone' => '00970597694385']) }}
                </span>
            </div>
        </div>
        <a href="{{ $whatsappUrl ?? 'https://wa.me/970597694385' }}" target="_blank" style="background: #16a34a; color: white; text-decoration: none; padding: 8px 18px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
            <i class="fa-brands fa-whatsapp" style="font-size: 1rem;"></i>
            <span>{{ __('تأكيد الاشتراك عبر واتساب') }}</span>
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 20px; align-items: start;">

        <!-- قسم طرق الدفع الفلسطينية أو بطاقة الإعفاء الكامل 100% -->
        @if(($cart['total'] ?? 0) <= 0)
            <div style="background: #ffffff; border: 1px solid #86efac; border-radius: 14px; padding: 36px 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="width: 70px; height: 70px; border-radius: 20px; background: #ecfdf5; color: #059669; display: grid; place-items: center; font-size: 2.2rem; margin: 0 auto 16px auto; border: 1px solid #a7f3d0;">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <div style="display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; color: #059669; padding: 5px 14px; border-radius: 50px; font-size: 0.82rem; font-weight: 800; margin-bottom: 12px; border: 1px solid #a7f3d0;">
                    <i class="fa-solid fa-sparkles"></i> {{ __('منحة وإعفاء أكاديمي كامل 100% ✨') }}
                </div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: #065f46; margin-bottom: 8px;">
                    {{ __('مبارك يا بطل! حصلت على إعفاء كامل من الرسوم') }}
                </h2>
                <p style="color: #047857; font-size: 0.88rem; line-height: 1.65; max-width: 500px; margin: 0 auto 20px auto;">
                    {{ __('تم اعتماد اشتراكك في باقة المواد مجاناً بالكامل بموجب منحة وإعفاء خاص معتمد لك من قِبل إدارة المنصة') }}
                    @if(!empty($cart['student_discount_notes']))
                        <br><strong style="background: rgba(16, 185, 129, 0.15); padding: 3px 10px; border-radius: 6px; display: inline-block; margin-top: 6px;">({{ $cart['student_discount_notes'] }})</strong>
                    @endif
                    {{ __('. لا يلزمك أي دفع أو تحويل بنكي، يمكنك تفعيل موادك فوراً وبدء دراستك الآن!') }}
                </p>

                <form id="paymentForm" onsubmit="handlePaymentSubmit(event)">
                    @csrf
                    <input type="hidden" name="gateway" value="scholarship">
                    <button type="submit" id="btnConfirmPay" style="background: #16a34a; color: white; border: none; padding: 14px 32px; border-radius: 10px; font-weight: 800; font-size: 1rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        <span>{{ __('تأكيد الاشتراك وتفعيل موادي فوراً') }}</span>
                        <i class="fa-solid fa-graduation-cap"></i>
                    </button>
                </form>
            </div>
        @else
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-wallet" style="color: #1d4ed8;"></i> {{ __('اختر وسيلة التحويل والدفع المعتمدة:') }}
                    </h2>
                </div>

                <form id="paymentForm" onsubmit="handlePaymentSubmit(event)">
                    @csrf
                    <input type="hidden" name="gateway" id="selectedGateway" value="jawwal_pay">

                    <!-- تبويبات بوابات الدفع الثلاث المعتمدة في فلسطين -->
                    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">

                        <!-- 1. جوال باي (Jawwal Pay) -->
                        <label class="gateway-tab active" id="tab_jawwal_pay" onclick="selectGateway('jawwal_pay')" style="border: 2px solid #1d4ed8; background: #eff6ff; border-radius: 12px; padding: 14px 18px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.2s;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: #dcfce7; color: #166534; display: grid; place-items: center; font-size: 1.2rem;">
                                    <i class="fa-solid fa-mobile-screen-button"></i>
                                </div>
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <strong style="font-size: 0.92rem; color: #0f172a;">{{ __('محفظة جوال باي (Jawwal Pay)') }}</strong>
                                        <span style="background: #dcfce7; color: #166534; font-size: 0.68rem; font-weight: 800; padding: 2px 7px; border-radius: 4px;">{{ __('الأسرع تفعيلاً ⚡') }}</span>
                                    </div>
                                    <small style="color: #64748b; font-size: 0.78rem;">{{ __('تحويل فوري إلى رقم المحفظة: :phone', ['phone' => '0567897212']) }}</small>
                                </div>
                            </div>
                            <input type="radio" name="gateway_radio" checked style="accent-color: #1d4ed8; width: 18px; height: 18px;">
                        </label>

                        <!-- 2. بنك فلسطين (Bank of Palestine) -->
                        <label class="gateway-tab" id="tab_bop" onclick="selectGateway('bop')" style="border: 1px solid #e2e8f0; background: #ffffff; border-radius: 12px; padding: 14px 18px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.2s;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fee2e2; color: #b91c1c; display: grid; place-items: center; font-size: 1.2rem;">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                <div>
                                    <strong style="display: block; font-size: 0.92rem; color: #0f172a;">{{ __('بنك فلسطين (Bank of Palestine)') }}</strong>
                                    <small style="color: #64748b; font-size: 0.78rem;">{{ __('رقم الحساب: :acc | جوال: :phone', ['acc' => '2275913', 'phone' => '0567897212']) }}</small>
                                </div>
                            </div>
                            <input type="radio" name="gateway_radio" style="accent-color: #1d4ed8; width: 18px; height: 18px;">
                        </label>

                        <!-- 3. بال باي (PalPay) -->
                        <label class="gateway-tab" id="tab_palpay" onclick="selectGateway('palpay')" style="border: 1px solid #e2e8f0; background: #ffffff; border-radius: 12px; padding: 14px 18px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.2s;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: #e0f2fe; color: #0369a1; display: grid; place-items: center; font-size: 1.2rem;">
                                    <i class="fa-solid fa-credit-card"></i>
                                </div>
                                <div>
                                    <strong style="display: block; font-size: 0.92rem; color: #0f172a;">{{ __('بال باي (PalPay - محفظتي ونقاط البيع)') }}</strong>
                                    <small style="color: #64748b; font-size: 0.78rem;">{{ __('عبر تطبيق محفظتي للرقم :phone أو كود :code', ['phone' => '0567897212', 'code' => '99420']) }}</small>
                                </div>
                            </div>
                            <input type="radio" name="gateway_radio" style="accent-color: #1d4ed8; width: 18px; height: 18px;">
                        </label>

                    </div>

                    <!-- 1. تفاصيل حقول جوال باي -->
                    <div id="fields_jawwal_pay" class="gateway-fields" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">{{ __('صاحب الحساب المستفيد:') }}</span>
                            <strong style="font-size: 0.92rem; color: #0f172a;">{{ __($palOwner ?? 'م.أحمد شمالي') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">{{ __('رقم محفظة التحويل المعتمد:') }}</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong id="copyPhoneText1" style="font-size: 1.15rem; color: #16a34a; font-family: monospace; direction: ltr;">0567897212</strong>
                                <button type="button" onclick="copyToClipboard('0567897212', '{{ __('تم نسخ رقم محفظة جوال باي (0567897212)') }}')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> {{ __('نسخ') }}
                                </button>
                            </div>
                        </div>
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 4px;">{{ __('رقم المحفظة التي قمت بالتحويل منها (جوال باي) *') }}</label>
                            <input type="text" name="wallet_phone" id="input_wallet_phone" placeholder="056xxxxxxx / 059xxxxxxx" value="{{ $student->phone }}" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-size: 0.92rem; direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; box-sizing: border-box;">
                        </div>
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 8px 12px; font-size: 0.75rem; color: #166534; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-info" style="font-size: 0.9rem;"></i>
                            <span>{{ __('خطوات الدفع: افتح تطبيق جوال باي ⬅ تحويل ⬅ اكتب الرقم :phone والمبلغ ⬅ التقط صورة الوصل وأرفقها بالأسفل.', ['phone' => '0567897212']) }}</span>
                        </div>
                    </div>

                    <!-- 2. تفاصيل حقول بنك فلسطين -->
                    <div id="fields_bop" class="gateway-fields" style="display: none; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">{{ __('اسم صاحب الحساب المستفيد:') }}</span>
                            <strong style="font-size: 0.92rem; color: #0f172a;">{{ __($palOwner ?? 'م.أحمد شمالي') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">{{ __('رقم حساب بنك فلسطين:') }}</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 1.15rem; color: #0f172a; font-family: monospace; direction: ltr;">2275913</strong>
                                <button type="button" onclick="copyToClipboard('2275913', '{{ __('تم نسخ رقم حساب بنك فلسطين (2275913)') }}')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> {{ __('نسخ') }}
                                </button>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">{{ __('رقم الجوال للتحويل (Pay to Mobile):') }}</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 1.15rem; color: #b91c1c; font-family: monospace; direction: ltr;">0567897212</strong>
                                <button type="button" onclick="copyToClipboard('0567897212', '{{ __('تم نسخ رقم الجوال للتحويل البنكي (0567897212)') }}')" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 3px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> {{ __('نسخ') }}
                                </button>
                            </div>
                        </div>
                        <div style="margin-bottom: 12px; margin-top: 10px;">
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 4px;">{{ __('رقم الحوالة أو اسم صاحب الحساب المُحوِّل *') }}</label>
                            <input type="text" name="bop_ref" id="input_bop_ref" placeholder="{{ __('اسم صاحب الحساب أو رقم المرجع من تطبيق بنكي') }}" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-size: 0.92rem; box-sizing: border-box;">
                        </div>
                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 8px 12px; font-size: 0.75rem; color: #991b1b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-info" style="font-size: 0.9rem;"></i>
                            <span>{{ __('طريقة التحويل: عبر تطبيق بنكي (بنك فلسطين) اختر تحويل إلى حساب رقم :acc أو تحويل لموبايل على :phone.', ['acc' => '2275913', 'phone' => '0567897212']) }}</span>
                        </div>
                    </div>

                    <!-- 3. تفاصيل حقول بال باي -->
                    <div id="fields_palpay" class="gateway-fields" style="display: none; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">{{ __('صاحب الحساب المستفيد:') }}</span>
                            <strong style="font-size: 0.92rem; color: #0f172a;">{{ __($palOwner ?? 'م.أحمد شمالي') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">{{ __('رقم الحساب / محفظتي بال باي:') }}</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 1.15rem; color: #1d4ed8; font-family: monospace; direction: ltr;">0567897212</strong>
                                <button type="button" onclick="copyToClipboard('0567897212', '{{ __('تم نسخ رقم محفظتي بال باي (0567897212)') }}')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> {{ __('نسخ') }}
                                </button>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                            <span style="font-size: 0.82rem; color: #475569; font-weight: 700;">{{ __('كود خدمة بال باي في نقاط البيع:') }}</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 1rem; color: #1d4ed8; font-family: monospace;">99420</strong>
                                <button type="button" onclick="copyToClipboard('99420', '{{ __('تم نسخ كود الخدمة 99420') }}')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">
                                    <i class="fa-solid fa-copy"></i> {{ __('نسخ') }}
                                </button>
                            </div>
                        </div>
                        <div style="margin-bottom: 12px; margin-top: 10px;">
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 4px;">{{ __('رقم العملية أو المرجع (Reference No) من وصل بال باي *') }}</label>
                            <input type="text" name="palpay_ref" id="input_palpay_ref" placeholder="{{ __('مثال: PAL-458921 أو رقم الوصل') }}" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-size: 0.92rem; box-sizing: border-box;">
                        </div>
                    </div>

                    <!-- رفع إشعار السداد (Proof Upload Zone) -->
                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-bottom: 6px;">
                            <i class="fa-solid fa-cloud-arrow-up" style="color: #1d4ed8;"></i> {{ __('إرفاق صورة إشعار أو وصل التحويل البنكي/المحفظة') }} <span style="color: #ef4444;">*</span>
                        </label>
                        <div id="receiptUploadWrapper" onclick="document.getElementById('receipt_file_input').click()" style="border: 2px dashed #bfdbfe; background: #eff6ff; border-radius: 12px; padding: 22px 18px; text-align: center; cursor: pointer; transition: 0.2s;">
                            <div id="receiptUploadPlaceholder">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: #dbeafe; color: #1d4ed8; display: grid; place-items: center; font-size: 1.3rem; margin: 0 auto 10px auto;">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                                <span style="font-size: 0.88rem; font-weight: 700; color: #1e40af; display: block; margin-bottom: 4px;">
                                    {{ __('اضغط هنا لاختيار صورة إشعار التحويل (سكرين شوت أو وصل)') }}
                                </span>
                                <small style="color: #64748b; font-size: 0.74rem;">{{ __('يدعم: JPG, PNG, WEBP أو PDF (الحد الأقصى 8 ميجابايت)') }}</small>
                            </div>

                            <div id="receiptPreviewBox" style="display: none;">
                                <img id="receiptPreviewImg" src="" alt="{{ __('معاينة الإشعار') }}" style="max-height: 180px; max-width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 10px; object-fit: contain;">
                                <div id="receiptFileName" style="font-size: 0.82rem; font-weight: 700; color: #16a34a; margin-bottom: 6px;">
                                    <i class="fa-solid fa-check-circle"></i> {{ __('تم إرفاق الإشعار بنجاح') }}
                                </div>
                                <button type="button" onclick="event.stopPropagation(); document.getElementById('receipt_file_input').click()" style="background: #e2e8f0; color: #334155; border: none; padding: 5px 12px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">
                                    <i class="fa-solid fa-rotate"></i> {{ __('تغيير الملف المرفق') }}
                                </button>
                            </div>

                            <input type="file" name="receipt_file" id="receipt_file_input" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" required style="display: none;" onchange="handleReceiptFileChange(this)">
                        </div>
                    </div>

                    <!-- تنبيه فحص واعتماد الإدارة -->
                    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px; margin-bottom: 18px; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-shield-halved" style="color: #d97706; font-size: 1.2rem; flex-shrink: 0;"></i>
                        <div style="font-size: 0.78rem; color: #92400e; line-height: 1.55;">
                            <strong>{{ __('آلية اعتماد منصة منارة التوجيهي:') }}</strong> {{ __('فور إرسال الإشعار، يقوم المشرف العام بمطابقة الحوالة وتفعيل موادك رسمياً خلال دقائق، وستصلك رسالة تأكيد فورية في لوحة حسابك الدراسي.') }}
                        </div>
                    </div>

                    <button type="submit" id="btnConfirmPay" style="width: 100%; background: #1d4ed8; color: white; border: none; padding: 14px; border-radius: 10px; font-weight: 800; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s;" onmouseover="this.style.background='#1e40af'" onmouseout="this.style.background='#1d4ed8'">
                        <span>{{ __('إرسال إشعار السداد للمراجعة والتفعيل') }}</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        @endif

        <!-- العمود الأيسر: ملخص الطلب والفاتورة والأسئلة الشائعة -->
        <div>
            <!-- ملخص الفاتورة -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 18px;">
                <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-receipt" style="color: #16a34a;"></i> {{ __('ملخص باقة المواد المختارة') }}
                </h2>

                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                    @foreach($cart['items'] as $item)
                        @php
                            $itemName = (app()->getLocale() === 'en' && !empty($item['name_en'])) ? $item['name_en'] : ($item['name_ar'] ?? '');
                            $stageName = (app()->getLocale() === 'en' && !empty($item['stage_en'])) ? $item['stage_en'] : ($item['stage'] ?? '');
                        @endphp
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9;">
                            <div>
                                <strong style="color: #0f172a; font-size: 0.88rem; display: block;">{{ $itemName }}</strong>
                                <small style="color: #64748b; font-size: 0.72rem;">{{ $stageName }}</small>
                            </div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: #1d4ed8; font-family: monospace;">
                                {{ number_format($item['price'], 0) }} {{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="border-top: 1px solid #e2e8f0; padding-top: 14px; display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.84rem; color: #64748b;">
                        <span>{{ __('المجموع الجزئي:') }}</span>
                        <span style="font-family: monospace; font-weight: 700;">{{ number_format($cart['subtotal'], 0) }} {{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}</span>
                    </div>

                    @if($cart['bundle_discount'] > 0)
                        <div style="display: flex; justify-content: space-between; font-size: 0.84rem; color: #16a34a; font-weight: 700; background: #f0fdf4; padding: 5px 8px; border-radius: 6px;">
                            <span><i class="fa-solid fa-layer-group"></i> {{ __('خصم باقة التوجيهي (15%):') }}</span>
                            <span style="font-family: monospace;">- {{ number_format($cart['bundle_discount'], 0) }} {{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}</span>
                        </div>
                    @endif

                    @if(!empty($cart['student_discount']) && $cart['student_discount'] > 0)
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.84rem; color: #7c3aed; font-weight: 700; background: #faf5ff; padding: 6px 10px; border-radius: 8px; border: 1px dashed #ddd6fe;">
                            <span><i class="fa-solid fa-gift"></i> {{ __($cart['student_discount_label'] ?? 'خصم الطالب المعتمد') }}:</span>
                            <span style="font-family: monospace;">- {{ number_format($cart['student_discount'], 0) }} {{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}</span>
                        </div>
                    @endif

                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 800; color: #0f172a; border-top: 1px dashed #cbd5e1; padding-top: 10px; margin-top: 4px;">
                        <span>{{ __('المبلغ المستحق للدفع:') }}</span>
                        <span style="color: {{ ($cart['total'] ?? 0) <= 0 ? '#16a34a' : '#1d4ed8' }}; font-family: monospace;">
                            {{ ($cart['total'] ?? 0) <= 0 ? __('مجاناً 0 ₪ (إعفاء كامل)') : number_format($cart['total'], 0) . (app()->getLocale() === 'ar' ? ' ₪' : ' ILS') }}
                        </span>
                    </div>
                </div>

                <!-- ضمانات المنصة المعتمدة -->
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px;">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: #dcfce7; color: #166534; display: grid; place-items: center; font-size: 1rem; flex-shrink: 0;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div style="font-size: 0.76rem; color: #166534; line-height: 1.5;">
                        <strong>{{ __('اشتراك موثوق ومضمون:') }}</strong> {{ __('صلاحية كاملة تشمل شروحات المنهاج الوزاري، بنك الأسئلة، الاختبارات، والمراجعات حتى نهاية الدورة الوزارية.') }}
                    </div>
                </div>
            </div>

            <!-- الأسئلة الشائعة حول الدفع والتفعيل (FAQ) -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <h3 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-question" style="color: #1d4ed8;"></i> {{ __('الأسئلة الشائعة حول الدفع:') }}
                </h3>
                
                <details style="border-bottom: 1px solid #f1f5f9; padding: 6px 0; cursor: pointer;">
                    <summary style="font-size: 0.82rem; font-weight: 700; color: #334155;">{{ __('كم يستغرق تفعيل اشتراكي بعد إرسال الإشعار؟') }}</summary>
                    <p style="font-size: 0.78rem; color: #64748b; margin: 6px 0 0; line-height: 1.55;">
                        {{ __('يتم فحص وتفعيل الحسابات بشكل يدوي ودقيق من قِبل المشرف خلال 5 إلى 15 دقيقة، أو فورياً عند إرسال الإشعار عبر واتساب.') }}
                    </p>
                </details>

                <details style="border-bottom: 1px solid #f1f5f9; padding: 6px 0; cursor: pointer;">
                    <summary style="font-size: 0.82rem; font-weight: 700; color: #334155;">{{ __('هل يمكنني مراسلة المشرف مباشرة للتأكيد؟') }}</summary>
                    <p style="font-size: 0.78rem; color: #64748b; margin: 6px 0 0; line-height: 1.55;">
                        {{ __('نعم بكل تأكيد! يمكنك الضغط على زر الواتساب الأخضر في الأعلى ومراسلة المشرف على الرقم 00970597694385 ليتم تفعيل موادك باللحظة.') }}
                    </p>
                </details>

                <details style="padding: 6px 0 0; cursor: pointer;">
                    <summary style="font-size: 0.82rem; font-weight: 700; color: #334155;">{{ __('ما هي أرقام المحافظ المعتمدة في المنصة؟') }}</summary>
                    <p style="font-size: 0.78rem; color: #64748b; margin: 6px 0 0; line-height: 1.55;">
                        {{ __('رقم محفظة جوال باي وبنك فلسطين وبال باي المعتمد للمنصة هو: 0567897212 باسم م.أحمد شمالي.') }}
                    </p>
                </details>
            </div>
        </div>

    </div>
</div>

<script>
const checkoutI18n = {
    copySuccess: "{{ __('تم النسخ بنجاح 📋') }}",
    proofRequiredTitle: "{{ __('صورة الإشعار مطلوبة') }}",
    proofRequiredText: "{{ __('يرجى إرفاق صورة إشعار أو وصل التحويل البنكي/المحفظة لتتمكن إدارة المنصة من مطابقة الدفعة وتفعيل موادك.') }}",
    btnOk: "{{ __('حسناً') }}",
    activating: "{{ __('جاري تفعيل المواد...') }}",
    uploading: "{{ __('جاري رفع الإشعار وإرسال طلب الدفع...') }}",
    activatedTitle: "{{ __('تم تفعيل المواد بنجاح! 🎉') }}",
    submittedTitle: "{{ __('تم إرسال إشعار السداد بنجاح! 🎉') }}",
    viewReceipt: "{{ __('عرض إيصال المعاملة والتفعيل') }}",
    errorTitle: "{{ __('تعذر إرسال الدفعة') }}",
    errorDefault: "{{ __('تعذر استكمال السداد، يرجى مراجعة البيانات.') }}",
    btnSubmitFree: "{{ __('تأكيد الاشتراك وتفعيل موادي فوراً') }}",
    btnSubmitPaid: "{{ __('إرسال إشعار السداد للمراجعة والتفعيل') }}"
};

function selectGateway(gw) {
    document.getElementById('selectedGateway').value = gw;

    // تحديث مظهر التبويبات
    document.querySelectorAll('.gateway-tab').forEach(tab => {
        tab.style.border = '1px solid #e2e8f0';
        tab.style.background = '#ffffff';
        const radio = tab.querySelector('input[type="radio"]');
        if (radio) radio.checked = false;
    });

    const activeTab = document.getElementById('tab_' + gw);
    if (activeTab) {
        activeTab.style.border = '2px solid #1d4ed8';
        activeTab.style.background = '#eff6ff';
        const radio = activeTab.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    // إظهار حقول الطريقة المحددة
    document.querySelectorAll('.gateway-fields').forEach(f => f.style.display = 'none');
    const activeFields = document.getElementById('fields_' + gw);
    if (activeFields) activeFields.style.display = 'block';
}

function copyToClipboard(text, successMsg) {
    const msg = successMsg || checkoutI18n.copySuccess;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: msg,
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
            title: msg,
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
            wrapper.style.borderColor = '#16a34a';
            wrapper.style.background = '#f0fdf4';
        };
        reader.readAsDataURL(file);
    } else {
        previewImg.style.display = 'none';
        placeholder.style.display = 'none';
        previewBox.style.display = 'block';
        fileName.innerHTML = '<i class="fa-solid fa-file-pdf" style="color: #ef4444; font-size: 1.3rem;"></i> {{ __("مستند PDF:") }} ' + file.name;
        wrapper.style.borderColor = '#16a34a';
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
                title: checkoutI18n.proofRequiredTitle,
                text: checkoutI18n.proofRequiredText,
                confirmButtonColor: '#1d4ed8',
                confirmButtonText: checkoutI18n.btnOk
            });
            return;
        }
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (isFree ? checkoutI18n.activating : checkoutI18n.uploading);

    const formData = new FormData(form);

    axios.post('{{ route("student.checkout.process") }}', formData)
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: isFree ? checkoutI18n.activatedTitle : checkoutI18n.submittedTitle,
                text: res.data.message || '',
                confirmButtonColor: '#1d4ed8',
                confirmButtonText: checkoutI18n.viewReceipt
            }).then(() => {
                window.location.href = res.data.redirect;
            });
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = isFree ? `<span>${checkoutI18n.btnSubmitFree}</span> <i class="fa-solid fa-graduation-cap"></i>` : `<span>${checkoutI18n.btnSubmitPaid}</span> <i class="fa-solid fa-paper-plane"></i>`;
            let msg = checkoutI18n.errorDefault;
            if (err.response?.data?.errors) {
                const first = Object.values(err.response.data.errors)[0];
                if (Array.isArray(first)) msg = first[0];
            } else if (err.response?.data?.message) {
                msg = err.response.data.message;
            }
            Swal.fire({
                icon: 'error',
                title: checkoutI18n.errorTitle,
                text: msg,
                confirmButtonColor: '#ef4444',
                confirmButtonText: checkoutI18n.btnOk
            });
        });
}
</script>
@endsection
