@extends('layouts.app')

@section('title', __('إعدادات المنصة والهوية وبيانات الدفع') . ' - ' . __('إدارة المنصة'))

@section('content')
<div class="settings-page-wrapper">

    <div class="settings-header-card">
        <div>
            <div class="badge-tag">
                <i class="fa-solid fa-gears"></i>
                <span>{{ __('النظام والإعدادات المركزية') }}</span>
            </div>
            <h1 class="settings-title">{{ __('إعدادات المنصة وهوية الدفع المركزية') }}</h1>
            <p class="settings-subtitle">{{ __('تحكم باسم المنصة، الشعار، بيانات بوابات الدفع الفلسطينية، وأرقام التواصل الرسمية.') }}</p>
        </div>
        <div class="system-badge-live">
            <span class="live-pulse-dot"></span>
            {{ __('المزامنة اللحظية مفعلة') }}
        </div>
    </div>

    <form id="brandForm">
        @csrf
        <div class="settings-main-layout-grid">

            <div style="display: flex; flex-direction: column; gap: 25px;">
                
                <!-- 0. بطاقة رفع شعار المنصة وأيقونة المتصفح -->
                <div class="settings-card">
                    <div class="settings-card-header" style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 18px 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-image" style="color: #6366f1; font-size: 1.2rem;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">{{ __('شعار المنصة الرسمي وأيقونة المتصفح (Logo & Favicon)') }}</h3>
                    </div>
                    <div style="padding: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                        
                        <!-- شعار المنصة الرئيسي -->
                        <div>
                            <label class="field-label">{{ __('شعار المنصة الرئيسي (يظهر في الهيدر، السايدبار، وصولات الدفع، والشهادات)') }}</label>
                            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                                <div id="current_logo_preview_box" style="width: 85px; height: 85px; border-radius: 16px; border: 2px dashed #cbd5e1; display: grid; place-items: center; overflow: hidden; background: #f8fafc; flex-shrink: 0;">
                                    @if(\App\Models\Setting::get('site_logo'))
                                        <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" id="logo_img_preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    @else
                                        <span id="logo_placeholder" style="font-size: 2.2rem;">🏛️</span>
                                        <img id="logo_img_preview" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none;">
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="site_logo" id="site_logo_input" accept="image/*" class="field-input" onchange="previewLogoFile(this)" style="padding: 8px;">
                                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">{{ __('يدعم PNG, JPG, SVG, WebP (يفضل خلفية شفافة مقاس مربع أو مستطيل).') }}</small>
                                </div>
                            </div>
                            @if(\App\Models\Setting::get('site_logo'))
                                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #ef4444; cursor: pointer;">
                                    <input type="checkbox" name="remove_logo" value="1">
                                    <span>{{ __('حذف الشعار الحالي واستعادة الشعار الرمزي الافتراضي') }}</span>
                                </label>
                            @endif
                        </div>

                        <!-- أيقونة المتصفح Favicon -->
                        <div>
                            <label class="field-label">{{ __('أيقونة المتصفح المصغرة (Favicon - تظهر في شريط التبويب)') }}</label>
                            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                                <div id="current_fav_preview_box" style="width: 65px; height: 65px; border-radius: 14px; border: 2px dashed #cbd5e1; display: grid; place-items: center; overflow: hidden; background: #f8fafc; flex-shrink: 0;">
                                    @if(\App\Models\Setting::get('site_favicon'))
                                        <img src="{{ asset(\App\Models\Setting::get('site_favicon')) }}" id="fav_img_preview" style="width: 36px; height: 36px; object-fit: contain;">
                                    @else
                                        <span id="fav_placeholder" style="font-size: 1.8rem;">🇵🇸</span>
                                        <img id="fav_img_preview" style="width: 36px; height: 36px; object-fit: contain; display: none;">
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="site_favicon" id="site_fav_input" accept="image/*,.ico" class="field-input" onchange="previewFavFile(this)" style="padding: 8px;">
                                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">{{ __('صيغة ICO أو PNG مقاس 32x32 أو 64x64 بكسل.') }}</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 0.5. بطاقة الأختام الرسمية والتواقيع الرقمية للشهادات والوثائق -->
                <div class="settings-card">
                    <div class="settings-card-header" style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 18px 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-stamp" style="color: #d97706; font-size: 1.2rem;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">{{ __('الأختام الرسمية والتواقيع الرقمية للشهادات والوثائق (Official Stamp & Signatures)') }}</h3>
                    </div>
                    <div style="padding: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                        
                        <!-- ختم المنصة الرسمي -->
                        <div>
                            <label class="field-label">{{ __('ختم المنصة الرسمي المعتمد (Official Stamp)') }}</label>
                            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                                <div style="width: 75px; height: 75px; border-radius: 14px; border: 2px dashed #cbd5e1; display: grid; place-items: center; overflow: hidden; background: #fffbeb; flex-shrink: 0;">
                                    @if(\App\Models\Setting::get('official_stamp'))
                                        <img src="{{ asset(\App\Models\Setting::get('official_stamp')) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    @else
                                        <span style="font-size: 1.8rem; color: #d97706;">🏛️</span>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="official_stamp" accept="image/png,image/webp" class="field-input" style="padding: 8px;">
                                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">{{ __('يفضل PNG شفاف دائري بدقة عالية ليظهر على الشهادات.') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- شعار المدير العام -->
                        <div>
                            <label class="field-label">{{ __('شعار / ختم المدير العام (Director Emblem)') }}</label>
                            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                                <div style="width: 75px; height: 75px; border-radius: 14px; border: 2px dashed #cbd5e1; display: grid; place-items: center; overflow: hidden; background: #f8fafc; flex-shrink: 0;">
                                    @if(\App\Models\Setting::get('director_logo'))
                                        <img src="{{ asset(\App\Models\Setting::get('director_logo')) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    @else
                                        <span style="font-size: 1.8rem; color: #1d4ed8;">🎖️</span>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="director_logo" accept="image/*" class="field-input" style="padding: 8px;">
                                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">{{ __('يظهر في ترويسة الشهادة الرسمية والوثائق الأكاديمية.') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- التوقيع الرقمي للمدير -->
                        <div>
                            <label class="field-label">{{ __('توقيع المدير العام الرقمي (Director Digital Signature)') }}</label>
                            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                                <div style="width: 75px; height: 75px; border-radius: 14px; border: 2px dashed #cbd5e1; display: grid; place-items: center; overflow: hidden; background: #f8fafc; flex-shrink: 0;">
                                    @if(\App\Models\Setting::get('admin_signature'))
                                        <img src="{{ asset(\App\Models\Setting::get('admin_signature')) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    @else
                                        <span style="font-size: 1.8rem; color: #475569;">✍️</span>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="admin_signature" accept="image/png,image/webp" class="field-input" style="padding: 8px;">
                                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">{{ __('PNG شفاف يمثل التوقيع الفعلي المعتمد للمشرف العام.') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- التوقيع الرقمي للمعلم الأكاديمي -->
                        <div>
                            <label class="field-label">{{ __('توقيع المعلم / المشرف الأكاديمي (Teacher Signature)') }}</label>
                            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                                <div style="width: 75px; height: 75px; border-radius: 14px; border: 2px dashed #cbd5e1; display: grid; place-items: center; overflow: hidden; background: #f8fafc; flex-shrink: 0;">
                                    @if(\App\Models\Setting::get('teacher_signature'))
                                        <img src="{{ asset(\App\Models\Setting::get('teacher_signature')) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    @else
                                        <span style="font-size: 1.8rem; color: #475569;">✍️</span>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="teacher_signature" accept="image/png,image/webp" class="field-input" style="padding: 8px;">
                                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">{{ __('PNG شفاف يدرج في خانة توقيع معلم المساق على الشهادات.') }}</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 1. بطاقة تسمية وهوية المنصة -->
                <div class="settings-card">
                    <div class="settings-card-header" style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 18px 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-signature" style="color: #0284c7; font-size: 1.2rem;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">{{ __('تسمية وعنونة المنصة (تتغير تلقائياً في كل الصفحات)') }}</h3>
                    </div>
                    <div style="padding: 25px; display: flex; flex-direction: column; gap: 20px;">
                        <div>
                            <label class="field-label">{{ __('اسم المنصة الرسمي (يظهر في الشريط العلوي والعناوين والشهادات)') }}</label>
                            <input type="text" name="site_name" id="name_input"
                                   value="{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}"
                                   class="field-input" oninput="livePreview(this.value)">
                            <small style="color: #94a3b8; font-size: 0.78rem; display: block; margin-top: 6px;">{{ __('* يتغير فورياً في شريط المتصفح، الفواتير، الإيصالات، ولوحة الطلاب والمعلمين.') }}</small>
                        </div>

                        <div>
                            <label class="field-label">{{ __('شعار ووصف المنصة (Slogan)') }}</label>
                            <input type="text" name="site_slogan" id="slogan_input"
                                   value="{{ \App\Models\Setting::get('site_slogan', 'المنصة الوطنية الرائدة لطلبة الثانوية العامة في فلسطين 🇵🇸') }}"
                                   class="field-input" oninput="document.getElementById('slogan_preview').innerText = this.value">
                        </div>
                    </div>
                </div>

                <!-- 2. بطاقة بوابات الدفع وحساب التحويل الفلسطيني -->
                <div class="settings-card">
                    <div class="settings-card-header" style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 18px 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-wallet" style="color: #059669; font-size: 1.2rem;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">{{ __('بيانات الدفع والتحويل الفلسطيني المعتمدة 🇵🇸') }}</h3>
                    </div>
                    <div style="padding: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div style="grid-column: span 2;">
                            <label class="field-label">{{ __('اسم صاحب الحساب المستفيد المعتمد (الاسم الثلاثي)') }}</label>
                            <input type="text" name="payment_account_name" id="owner_input"
                                   value="{{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}"
                                   class="field-input" oninput="document.getElementById('owner_preview').innerText = this.value">
                            <small style="color: #64748b; font-size: 0.78rem; display: block; margin-top: 4px;">{{ __('يظهر في صفحة السداد والإيصالات الرسمية لتأكيد التحويل البنكي وجوال باي.') }}</small>
                        </div>

                        <div>
                            <label class="field-label">{{ __('رقم محفظة التحويل (جوال باي / أوريدو)') }}</label>
                            <input type="text" name="payment_phone" id="phone_input"
                                   value="{{ \App\Models\Setting::get('payment_phone', '0567897212') }}"
                                   class="field-input" style="direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};"
                                   oninput="document.getElementById('phone_preview').innerText = this.value">
                        </div>

                        <div>
                            <label class="field-label">{{ __('رمز خدمة بال باي (PalPay Service Code)') }}</label>
                            <input type="text" name="palpay_service_code"
                                   value="{{ \App\Models\Setting::get('palpay_service_code', '99420') }}"
                                   class="field-input" style="direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                        </div>

                        <div>
                            <label class="field-label">{{ __('اسم البنك المعتمد') }}</label>
                            <input type="text" name="payment_bank_name"
                                   value="{{ \App\Models\Setting::get('payment_bank_name', 'بنك فلسطين') }}"
                                   class="field-input">
                        </div>

                        <div>
                            <label class="field-label">{{ __('رقم حساب بنك فلسطين') }}</label>
                            <input type="text" name="payment_account_no"
                                   value="{{ \App\Models\Setting::get('payment_account_no', '2275913') }}"
                                   class="field-input" style="direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; font-weight: 700; font-family: monospace;">
                        </div>
                    </div>
                </div>

                <!-- 3. بطاقة قنوات التواصل والدعم -->
                <div class="settings-card">
                    <div class="settings-card-header" style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 18px 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-headset" style="color: #6366f1; font-size: 1.2rem;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">{{ __('قنوات التواصل والدعم الفني') }}</h3>
                    </div>
                    <div style="padding: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="field-label">{{ __('رقم الواتساب الرسمي (للتواصل مع الطلاب)') }}</label>
                            <input type="text" name="contact_whatsapp"
                                   value="{{ \App\Models\Setting::get('contact_whatsapp', '00970597694385') }}"
                                   class="field-input" style="direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                        </div>
                        <div>
                            <label class="field-label">{{ __('البريد الإلكتروني المعتمد') }}</label>
                            <input type="email" name="contact_email"
                                   value="{{ \App\Models\Setting::get('contact_email', 'support@tawjihi.ps') }}"
                                   class="field-input" style="direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                        </div>
                    </div>
                </div>

            </div>

            <!-- الجانب الأيسر: المعاينة الذكية + زر الحفظ (فاتحة وأنيقة بالكامل) -->
            <aside style="display: flex; flex-direction: column; gap: 20px; position: sticky; top: 90px;">

                <!-- بطاقة المعاينة الفورية للهوية (فواتح بالكامل) -->
                <div class="settings-card" style="background: #f8fafc; color: #0f172a; padding: 22px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.72rem; font-weight: 800; color: #1d4ed8; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 14px;">
                        {{ __('المعاينة الحية الفورية 👁️') }}
                    </span>

                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 18px;">
                        <div id="logo_letter" style="width: 50px; height: 50px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; display: grid; place-items: center; font-size: 1.5rem; font-weight: 900; color: #1d4ed8; overflow: hidden; padding: 3px; flex-shrink: 0;">
                            @if(\App\Models\Setting::get('site_logo'))
                                <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" id="sidebar_preview_logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px; background: white; padding: 2px;">
                                <span id="sidebar_preview_letter" style="display: none;">{{ mb_substr(\App\Models\Setting::get('site_name', 'منارة التوجيهي'), 0, 1) }}</span>
                            @else
                                <span id="sidebar_preview_letter">{{ mb_substr(\App\Models\Setting::get('site_name', 'منارة التوجيهي'), 0, 1) }}</span>
                                <img id="sidebar_preview_logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px; background: white; padding: 2px; display: none;">
                            @endif
                        </div>
                        <div>
                            <h3 id="logo_name_preview" style="font-size: 1.15rem; font-weight: 800; margin: 0; color: #0f172a;">
                                {{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}
                            </h3>
                            <small id="slogan_preview" style="color: #64748b; font-size: 0.76rem; display: block; margin-top: 2px;">
                                {{ __(\App\Models\Setting::get('site_slogan', 'المنصة الوطنية الرائدة لطلبة الثانوية العامة في فلسطين 🇵🇸')) }}
                            </small>
                        </div>
                    </div>

                    <div style="background: #ffffff; border-radius: 10px; padding: 12px 14px; border: 1px solid #cbd5e1;">
                        <div style="font-size: 0.74rem; color: #64748b; margin-bottom: 4px;">{{ __('بيانات التحويل المسجلة حالياً:') }}</div>
                        <div style="font-size: 0.88rem; font-weight: 800; color: #1d4ed8;" id="owner_preview">
                            {{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}
                        </div>
                        <div style="font-family: monospace; font-size: 0.85rem; color: #059669; direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};" id="phone_preview">
                            {{ \App\Models\Setting::get('payment_phone', '0567897212') }}
                        </div>
                    </div>
                </div>

                <!-- خيار حالة التسجيل -->
                <div class="settings-card" style="padding: 20px;">
                    <label class="field-label">{{ __('حالة فتح بوابة تسجيل الطلاب الجدد') }}</label>
                    <select name="registration_status" class="field-select">
                        <option value="open" {{ \App\Models\Setting::get('registration_status', 'open') == 'open' ? 'selected' : '' }}>{{ __('🟢 متاح للجميع والتسجيل مفتوح') }}</option>
                        <option value="closed" {{ \App\Models\Setting::get('registration_status') == 'closed' ? 'selected' : '' }}>{{ __('🔴 مغلق مؤقتاً للصيانة') }}</option>
                    </select>
                </div>

                <!-- زر حفظ الإعدادات واعتمادها -->
                <button type="button" onclick="savePlatformSettings()" id="saveBtn" style="width: 100%; padding: 16px; border-radius: 12px; background: #1d4ed8; color: white; border: none; font-size: 1rem; font-weight: 800; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px; box-shadow: 0 4px 14px rgba(29, 78, 216, 0.25); transition: 0.2s;">
                    <span>{{ __('حفظ واعتماد التغييرات') }}</span>
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </button>

            </aside>

        </div>
    </form>

    <!-- منطقة العمليات الحساسة وتصفير المنصة للعام الجديد -->
    <div style="margin-top: 40px; background: #fff; border: 1px solid #fecaca; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(239, 68, 68, 0.05);">
        <div style="background: #fef2f2; padding: 20px 24px; border-bottom: 1px solid #fecaca; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #dc2626; color: white; display: grid; place-items: center; font-size: 1.2rem; flex-shrink: 0;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 style="margin: 0 0 3px; font-size: 1.15rem; font-weight: 800; color: #991b1b;">{{ __('منطقة العمليات الحساسة وإعادة ضبط وتصفير المنصة (Danger Zone)') }}</h3>
                    <p style="margin: 0; font-size: 0.85rem; color: #b91c1c;">{{ __('تصفير وحذف حسابات الطلبة والمعلمين دفعة واحدة عند بداية العام الدراسي الجديد أو تفريغ البيانات التجريبية.') }}</p>
                </div>
            </div>
            <span style="background: #fee2e2; color: #991b1b; padding: 5px 12px; border-radius: 20px; font-weight: 800; font-size: 0.78rem; border: 1px solid #fecaca;">
                {{ __('⚠️ صلاحيات المدير العام فقط') }}
            </span>
        </div>

        <div style="padding: 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            
            <!-- بطاقة حذف جميع الطلاب -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 1.5rem;">🎓</span>
                        <span style="background: #eff6ff; color: #1d4ed8; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; border: 1px solid #bfdbfe;">
                            {{ \App\Models\Student::count() }} {{ __('طالب مسجل') }}
                        </span>
                    </div>
                    <h4 style="margin: 0 0 6px; font-size: 1.05rem; font-weight: 800; color: #0f172a;">{{ __('حذف جميع الطلاب دفعة واحدة') }}</h4>
                    <p style="margin: 0 0 16px; font-size: 0.82rem; color: #64748b; line-height: 1.5;">
                        {{ __('حذف كافة الطلاب المسجلين وجميع اشتراكاتهم بالمواد وامتحاناتهم وتصفير سجل الطلاب بالكامل.') }}
                    </p>
                </div>
                <button type="button" onclick="purgeAllStudentsDirect()" style="width: 100%; background: #dc2626; color: white; border: none; padding: 11px; border-radius: 8px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>{{ __('حذف وتصفير جميع الطلاب') }}</span>
                </button>
            </div>

            <!-- بطاقة حذف جميع المعلمين -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 1.5rem;">👨‍🏫</span>
                        <span style="background: #f0fdf4; color: #166534; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; border: 1px solid #bbf7d0;">
                            {{ \App\Models\User::where('role', 'teacher')->count() }} {{ __('معلم مسجل') }}
                        </span>
                    </div>
                    <h4 style="margin: 0 0 6px; font-size: 1.05rem; font-weight: 800; color: #0f172a;">{{ __('حذف جميع المعلمين دفعة واحدة') }}</h4>
                    <p style="margin: 0 0 16px; font-size: 0.82rem; color: #64748b; line-height: 1.5;">
                        {{ __('حذف حسابات جميع المعلمين وإخلاء المواد المسندة لهم دون المساس بحسابات الإدارة.') }}
                    </p>
                </div>
                <button type="button" onclick="purgeAllTeachersDirect()" style="width: 100%; background: #b91c1c; color: white; border: none; padding: 11px; border-radius: 8px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 8px rgba(185, 28, 28, 0.2);">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>{{ __('حذف وتصفير جميع المعلمين') }}</span>
                </button>
            </div>

            <!-- بطاقة الحذف الشامل (الطلاب + المعلمين معاً) -->
            <div style="background: #fef2f2; border: 1.5px dashed #f87171; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 1.5rem;">🔥</span>
                        <span style="background: #dc2626; color: white; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 6px;">
                            {{ __('تصفير شامل مشترك') }}
                        </span>
                    </div>
                    <h4 style="margin: 0 0 6px; font-size: 1.05rem; font-weight: 800; color: #991b1b;">{{ __('حذف الطلاب والمعلمين معاً دفعة واحدة') }}</h4>
                    <p style="margin: 0 0 16px; font-size: 0.82rem; color: #7f1d1d; line-height: 1.5;">
                        {{ __('إعادة تهيئة وتصفير المنظومة الأكاديمية بالكامل لدورة جديدة، بحذف كافة الطلاب والمعلمين معاً بنقرة واحدة.') }}
                    </p>
                </div>
                <button type="button" onclick="purgeAllBothDirect()" style="width: 100%; background: #991b1b; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 800; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 10px rgba(153, 27, 27, 0.25);">
                    <i class="fa-solid fa-burst"></i>
                    <span>{{ __('حذف جميع الطلاب والمعلمين معاً') }}</span>
                </button>
            </div>

        </div>
    </div>

</div>

<style>
.settings-page-wrapper {
    max-width: 1350px;
    margin: 1rem auto 3rem;
    padding: 0 1rem;
}

.settings-header-card {
    background: #ffffff;
    border: 1px solid var(--ed-border, #e2e8f0);
    border-radius: 14px;
    padding: 22px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
    border-inline-start: 4px solid var(--ed-primary, #1d4ed8);
    box-shadow: var(--ed-shadow-card, 0 1px 3px rgba(0,0,0,0.05));
}

.badge-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 6px;
}

.settings-title {
    font-size: 1.4rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
}

.settings-subtitle {
    color: #64748b;
    font-size: 0.86rem;
    margin: 0;
}

.system-badge-live {
    background: #ecfdf5;
    color: #059669;
    padding: 6px 16px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #bbf7d0;
}

.live-pulse-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
}

.settings-main-layout-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 25px;
    align-items: start;
}

@media (max-width: 1024px) {
    .settings-main-layout-grid {
        grid-template-columns: 1fr;
    }
}

.settings-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: var(--ed-shadow-card, 0 1px 3px rgba(0,0,0,0.05));
}

.field-label {
    display: block;
    font-size: 0.84rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 8px;
}

.field-input {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    font-size: 0.9rem;
    font-weight: 600;
    color: #0f172a;
    outline: none;
    transition: 0.2s;
}

.field-input:focus {
    border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
}

.field-select {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    font-size: 0.9rem;
    font-weight: 700;
    color: #0f172a;
    outline: none;
    cursor: pointer;
}

.field-select:focus {
    border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
}
</style>

<script>
    const settingsI18n = {
        defaultSiteName: @json(__('منارة التوجيهي')),
        savingBranding: @json(__('جاري رفع الشعار وحفظ الهوية...')),
        savedTitle: @json(__('تم تحديث الإعدادات بنجاح! 🎉')),
        savedText: @json(__('تم تعميم الشعار وهوية المنصة وبيانات الدفع على كافة أرجاء النظام.')),
        greatBtn: @json(__('ممتاز')),
        saveErrorTitle: @json(__('خطأ في الحفظ')),
        saveErrorDefault: @json(__('حدث خطأ أثناء حفظ الإعدادات، يرجى إعادة المحاولة.')),
        saveBtnHtml: @json(__('حفظ واعتماد التغييرات')),
        purgeStudentsTitle: @json(__('حذف وتصفير جميع الطلاب ⚠️')),
        purgeStudentsWarning: @json(__('سيتم حذف كافة الطلاب المسجلين وسجلاتهم واشتراكاتهم وامتحاناتهم نهائياً.')),
        securityWarningTag: @json(__('تحذير أمني شديد:')),
        toConfirmType: @json(__('للتأكيد، اكتب:')),
        confirmDeletePhrase: @json(__('تأكيد الحذف')),
        typeConfirmDeletePlaceholder: @json(__('اكتب: تأكيد الحذف')),
        confirmDeleteStudentsBtn: @json(__('تأكيد وحذف جميع الطلاب')),
        cancelBtn: @json(__('إلغاء')),
        mismatchValidation: @json(__('العبارة غير متطابقة! اكتب: تأكيد الحذف')),
        deletingStudentsLoading: @json(__('جاري حذف جميع الطلاب...')),
        successWord: @json(__('تم بنجاح!')),
        errorWord: @json(__('خطأ')),
        purgeTeachersTitle: @json(__('حذف وتصفير جميع المعلمين ⚠️')),
        warningTag: @json(__('تحذير:')),
        purgeTeachersWarning: @json(__('سيتم حذف كافة المعلمين وإخلاء إسناد المواد الدراسية. (حسابات الإدارة محمية).')),
        confirmDeleteTeachersBtn: @json(__('تأكيد وحذف جميع المعلمين')),
        deletingTeachersLoading: @json(__('جاري حذف جميع المعلمين...')),
        purgeBothTitle: @json(__('حذف جميع الطلاب والمعلمين معاً ⚠️🔥')),
        purgeBothNoticeTag: @json(__('⚠️ إجراء التصفير الشامل للعام الجديد:')),
        purgeBothNoticeText: @json(__('سيتم حذف جميع الطلاب وجميع المعلمين معاً دفعة واحدة، وتفريغ كافة السجلات والاشتراكات بالكامل، وإعادة المنصة لنقطة الصفر مع الحفاظ على المواد وحسابات الإدارة.')),
        toCompleteFullResetType: @json(__('لإتمام التصفير الشامل، يرجى كتابة العبارة بدقة:')),
        confirmFullResetPhrase: @json(__('تأكيد الحذف الشامل')),
        typeConfirmFullResetPlaceholder: @json(__('اكتب: تأكيد الحذف الشامل')),
        executeFullResetBtn: @json(__('نعم، نفذ التصفير الشامل الآن 🚀')),
        backAndCancelBtn: @json(__('تراجع وإلغاء')),
        mismatchFullResetValidation: @json(__('العبارة غير متطابقة! اكتب بدقة: تأكيد الحذف الشامل')),
        fullResetLoadingTitle: @json(__('جاري التصفير الشامل للطلاب والمعلمين...')),
        fullResetLoadingText: @json(__('يرجى الانتظار لحين معالجة البيانات وإعادة الضبط')),
        fullResetSuccessTitle: @json(__('تم التصفير الشامل بنجاح! 🚀')),
        fullResetErrorDefault: @json(__('حدث خطأ أثناء التصفير الشامل'))
    };

    function livePreview(val) {
        if (!val) val = settingsI18n.defaultSiteName;
        document.getElementById('logo_name_preview').innerText = val;
        const letterElem = document.getElementById('sidebar_preview_letter');
        if (letterElem) letterElem.innerText = val.charAt(0);
    }

    function previewLogoFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logo_img_preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
                const placeholder = document.getElementById('logo_placeholder');
                if (placeholder) placeholder.style.display = 'none';

                // أيضاً في كرت المعاينة الجانبي
                const sidePreview = document.getElementById('sidebar_preview_logo');
                if (sidePreview) {
                    sidePreview.src = e.target.result;
                    sidePreview.style.display = 'block';
                }
                const sideLetter = document.getElementById('sidebar_preview_letter');
                if (sideLetter) sideLetter.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewFavFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('fav_img_preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
                const placeholder = document.getElementById('fav_placeholder');
                if (placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function savePlatformSettings() {
        const btn = document.getElementById('saveBtn');
        const form = document.getElementById('brandForm');

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> ${settingsI18n.savingBranding}`;

        axios.post("{{ route('admin.settings.update') }}", new FormData(form), {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(res => {
            if (res.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: res.data.title || settingsI18n.savedTitle,
                    text: settingsI18n.savedText,
                    confirmButtonText: settingsI18n.greatBtn,
                    confirmButtonColor: '#1d4ed8'
                }).then(() => location.reload());
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: settingsI18n.saveErrorTitle,
                text: err.response?.data?.message || settingsI18n.saveErrorDefault
            });
            btn.disabled = false;
            btn.innerHTML = `<span>${settingsI18n.saveBtnHtml}</span> <i class="fa-solid fa-cloud-arrow-up"></i>`;
        });
    }

    // دوال الحذف الشامل والتصفير من صفحة الإعدادات
    function purgeAllStudentsDirect() {
        Swal.fire({
            title: settingsI18n.purgeStudentsTitle,
            html: `
                <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 14px; text-align: start; margin-bottom: 12px; font-size: 0.88rem; color: #991b1b; line-height: 1.6;">
                    <strong>${settingsI18n.securityWarningTag}</strong><br>
                    ${settingsI18n.purgeStudentsWarning}
                </div>
                <p style="font-size: 0.85rem; color: #475569; margin-bottom: 8px;">${settingsI18n.toConfirmType}<br><strong style="color: #dc2626; font-size: 1rem;">${settingsI18n.confirmDeletePhrase}</strong></p>
            `,
            input: 'text',
            inputPlaceholder: settingsI18n.typeConfirmDeletePlaceholder,
            showCancelButton: true,
            confirmButtonText: settingsI18n.confirmDeleteStudentsBtn,
            cancelButtonText: settingsI18n.cancelBtn,
            confirmButtonColor: '#dc2626',
            preConfirm: (val) => {
                if (val !== 'تأكيد الحذف' && val !== 'DELETE' && val !== settingsI18n.confirmDeletePhrase) {
                    Swal.showValidationMessage(settingsI18n.mismatchValidation);
                    return false;
                }
                return val;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: settingsI18n.deletingStudentsLoading, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                axios.post("{{ route('admin.students.purgeAll') }}", { confirm_text: result.value })
                .then(res => Swal.fire(settingsI18n.successWord, res.data.message, 'success').then(() => location.reload()))
                .catch(err => Swal.fire(settingsI18n.errorWord, err.response?.data?.message || 'Error', 'error'));
            }
        });
    }

    function purgeAllTeachersDirect() {
        Swal.fire({
            title: settingsI18n.purgeTeachersTitle,
            html: `
                <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 14px; text-align: start; margin-bottom: 12px; font-size: 0.88rem; color: #991b1b; line-height: 1.6;">
                    <strong>${settingsI18n.warningTag}</strong><br>
                    ${settingsI18n.purgeTeachersWarning}
                </div>
                <p style="font-size: 0.85rem; color: #475569; margin-bottom: 8px;">${settingsI18n.toConfirmType}<br><strong style="color: #dc2626; font-size: 1rem;">${settingsI18n.confirmDeletePhrase}</strong></p>
            `,
            input: 'text',
            inputPlaceholder: settingsI18n.typeConfirmDeletePlaceholder,
            showCancelButton: true,
            confirmButtonText: settingsI18n.confirmDeleteTeachersBtn,
            cancelButtonText: settingsI18n.cancelBtn,
            confirmButtonColor: '#dc2626',
            preConfirm: (val) => {
                if (val !== 'تأكيد الحذف' && val !== 'DELETE' && val !== settingsI18n.confirmDeletePhrase) {
                    Swal.showValidationMessage(settingsI18n.mismatchValidation);
                    return false;
                }
                return val;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: settingsI18n.deletingTeachersLoading, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                axios.post("{{ route('admin.teachers.purgeAll') }}", { confirm_text: result.value })
                .then(res => Swal.fire(settingsI18n.successWord, res.data.message, 'success').then(() => location.reload()))
                .catch(err => Swal.fire(settingsI18n.errorWord, err.response?.data?.message || 'Error', 'error'));
            }
        });
    }

    function purgeAllBothDirect() {
        Swal.fire({
            title: settingsI18n.purgeBothTitle,
            html: `
                <div style="background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b; border-radius: 12px; padding: 16px; text-align: start; margin-bottom: 14px; font-size: 0.88rem; line-height: 1.6;">
                    <strong>${settingsI18n.purgeBothNoticeTag}</strong><br>
                    ${settingsI18n.purgeBothNoticeText}
                </div>
                <p style="font-size: 0.85rem; color: #475569; margin-bottom: 8px;">${settingsI18n.toCompleteFullResetType}<br><strong style="color: #991b1b; font-size: 1rem;">${settingsI18n.confirmFullResetPhrase}</strong></p>
            `,
            input: 'text',
            inputPlaceholder: settingsI18n.typeConfirmFullResetPlaceholder,
            showCancelButton: true,
            confirmButtonText: settingsI18n.executeFullResetBtn,
            cancelButtonText: settingsI18n.backAndCancelBtn,
            confirmButtonColor: '#991b1b',
            preConfirm: (val) => {
                if (val !== 'تأكيد الحذف الشامل' && val !== 'DELETE ALL' && val !== 'تأكيد الحذف' && val !== settingsI18n.confirmFullResetPhrase) {
                    Swal.showValidationMessage(settingsI18n.mismatchFullResetValidation);
                    return false;
                }
                return val;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: settingsI18n.fullResetLoadingTitle,
                    text: settingsI18n.fullResetLoadingText,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                axios.post("{{ route('admin.system.purgeAllUsers') }}", { confirm_text: result.value })
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: settingsI18n.fullResetSuccessTitle,
                        text: res.data.message
                    }).then(() => location.reload());
                })
                .catch(err => {
                    Swal.fire(settingsI18n.errorWord, err.response?.data?.message || settingsI18n.fullResetErrorDefault, 'error');
                });
            }
        });
    }
</script>
@endsection
