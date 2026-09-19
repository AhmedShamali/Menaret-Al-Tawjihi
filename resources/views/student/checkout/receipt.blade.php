@extends('layouts.app')

@section('title', __('إيصال سداد وتفعيل اشتراك رقمي') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="receipt-container" style="max-width: 820px; margin: 1.5rem auto; padding: 0 1rem;">
    <!-- أزرار الإجراءات السريعة في الأعلى (تختفي في الطباعة) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <a href="{{ route('student.dashboard') }}" class="btn-return" style="display: inline-flex; align-items: center; gap: 8px; color: #475569; text-decoration: none; font-weight: 600; padding: 8px 14px; border-radius: 8px; background: #ffffff; border: 1px solid #e2e8f0; font-size: 0.84rem;">
            <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
            <span>{{ __('العودة للوحة المواد') }}</span>
        </a>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-print" style="display: inline-flex; align-items: center; gap: 6px; background: #1d4ed8; color: white; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; cursor: pointer; transition: 0.2s;">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة الإيصال / حفظ PDF') }}</span>
            </button>
            <a href="{{ route('student.courses.catalog') }}" class="btn-catalog" style="display: inline-flex; align-items: center; gap: 6px; background: #ffffff; color: #16a34a; border: 1px solid #86efac; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; transition: 0.2s;">
                <i class="fa-solid fa-book-open"></i>
                <span>{{ __('تصفح مواد إضافية') }}</span>
            </a>
        </div>
    </div>

    <!-- بطاقة الإيصال الرسمية المعتمَدة - فاتحة بالكامل -->
    <div class="receipt-card" style="background: #ffffff; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; position: relative; overflow: hidden; padding: 2.2rem 2rem;">
        
        <!-- الشريط العلوي الملون بنمط الهوية الفلسطينية -->
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #000000 25%, #dc2626 25%, #dc2626 50%, #ffffff 50%, #ffffff 75%, #16a34a 75%);"></div>

        <!-- شارة الحالة: مدفوع ومعتمد أو قيد المراجعة -->
        @php
            $badgePos = app()->getLocale() === 'ar' ? 'left: 24px;' : 'right: 24px;';
        @endphp
        @if($payment->status === 'completed')
            <div class="paid-badge" style="position: absolute; top: 20px; {{ $badgePos }} background: #ecfdf5; border: 1px solid #86efac; color: #047857; font-size: 0.82rem; font-weight: 800; padding: 4px 14px; border-radius: 50px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ __('مدفوع ومفعل رسمياً') }}</span>
            </div>
        @elseif($payment->status === 'pending')
            <div class="paid-badge" style="position: absolute; top: 20px; {{ $badgePos }} background: #fffbeb; border: 1px solid #fde68a; color: #b45309; font-size: 0.82rem; font-weight: 800; padding: 4px 14px; border-radius: 50px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-clock"></i>
                <span>{{ __('قيد المراجعة والتدقيق') }}</span>
            </div>
        @else
            <div class="paid-badge" style="position: absolute; top: 20px; {{ $badgePos }} background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: 0.82rem; font-weight: 800; padding: 4px 14px; border-radius: 50px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-circle-xmark"></i>
                <span>{{ __('ملغي أو لم يُعتمد') }}</span>
            </div>
        @endif

        <!-- ترويسة الإيصال الرسمية -->
        <div class="receipt-header" style="text-align: center; border-bottom: 1px dashed #cbd5e1; padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 0.5rem;">
                @if(\App\Models\Setting::get('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}" style="max-height: 48px; max-width: 90px; object-fit: contain;">
                @else
                    <span style="font-size: 1.8rem;">🇵🇸</span>
                @endif
                <h1 style="margin: 0; font-size: 1.45rem; font-weight: 800; color: #0f172a;">{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}</h1>
            </div>
            <p style="margin: 0 0 0.4rem; color: #64748b; font-size: 0.85rem; font-weight: 500;">
                {{ __(\App\Models\Setting::get('site_slogan', 'المنصة الوطنية الرائدة لطلبة الثانوية العامة في فلسطين')) }}
            </p>
            <div style="display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 3px 12px; font-size: 0.78rem; color: #334155; font-weight: 700;">
                {{ __('إيصال استلام مالي وتفعيل اشتراك دراسي معتمد • Official Verified E-Receipt') }}
            </div>
        </div>

        <!-- معلومات الفاتورة الأساسية في شبكة -->
        <div class="receipt-meta-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; background: #f8fafc; padding: 1rem 1.25rem; border-radius: 10px; border: 1px solid #e2e8f0;">
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 3px;">{{ __('رقم الإيصال المرجعي') }}</div>
                <div style="color: #0f172a; font-weight: 800; font-size: 0.88rem; font-family: monospace; direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">{{ $payment->transaction_number }}</div>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 3px;">{{ __('تاريخ ووقت المعاملة') }}</div>
                <div style="color: #0f172a; font-weight: 700; font-size: 0.85rem;">{{ $payment->created_at ? $payment->created_at->format('Y-m-d - h:i A') : now()->format('Y-m-d') }}</div>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 3px;">{{ __('طريقة السداد / المزود') }}</div>
                <div style="color: #1d4ed8; font-weight: 800; font-size: 0.88rem; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-wallet"></i>
                    <span>{{ __($payment->gateway_name_ar ?? $payment->gateway ?? 'دفع إلكتروني') }}</span>
                </div>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 3px;">{{ __('حالة المعاملة') }}</div>
                @if($payment->status === 'completed')
                    <div style="color: #16a34a; font-weight: 800; font-size: 0.88rem;">{{ __('مكتمل ومؤكد بنجاح ✅') }}</div>
                @elseif($payment->status === 'pending')
                    <div style="color: #d97706; font-weight: 800; font-size: 0.88rem;">{{ __('بانتظار تدقيق الإشعار ⏳') }}</div>
                @else
                    <div style="color: #dc2626; font-weight: 800; font-size: 0.88rem;">{{ __('ملغي ❌') }}</div>
                @endif
            </div>
        </div>

        <!-- بيانات الطالب -->
        <div class="student-info-section" style="margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.25rem;">
            <h3 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin: 0 0 0.75rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-graduation-cap" style="color: #1d4ed8;"></i>
                <span>{{ __('بيانات الطالب المشترك:') }}</span>
            </h3>
            @php
                $studentName = (app()->getLocale() === 'en' && !empty($payment->student->name_en)) 
                    ? $payment->student->name_en 
                    : ($payment->student->name_ar ?? $payment->student->name ?? __('طالب توجيهي'));
                $stageName = (app()->getLocale() === 'en' && !empty($payment->student->stage->name_en))
                    ? $payment->student->stage->name_en
                    : ($payment->student->stage->name_ar ?? __('الثانوية العامة (التوجيهي)'));
            @endphp
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 0.75rem;">
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px;">
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">{{ __('الاسم الثلاثي:') }}</span>
                    <strong style="color: #0f172a; font-size: 0.88rem;">{{ $studentName }}</strong>
                </div>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px;">
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">{{ __('الفرع الأكاديمي / الصف:') }}</span>
                    <strong style="color: #0f172a; font-size: 0.88rem;">{{ $stageName }}</strong>
                </div>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px;">
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">{{ __('البريد الإلكتروني / الحساب:') }}</span>
                    <strong style="color: #0f172a; font-size: 0.85rem; direction: ltr; display: inline-block;">{{ $payment->student->email ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- جدول المواد المفعّلة -->
        <div class="items-table-section" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin: 0 0 0.75rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: #16a34a;"></i>
                <span>{{ __('المواد والمقررات المفعلة بموجب هذا الإيصال:') }}</span>
            </h3>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; font-size: 0.84rem;">
                    <thead>
                        <tr style="background: #f8fafc; color: #334155; font-size: 0.8rem; font-weight: 700; border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 9px 12px;">#</th>
                            <th style="padding: 9px 12px;">{{ __('المادة التعليمية') }}</th>
                            <th style="padding: 9px 12px;">{{ __('نوع الاشتراك وصلاحيته') }}</th>
                            <th style="padding: 9px 12px;">{{ __('حالة الصلاحية') }}</th>
                            <th style="padding: 9px 12px; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">{{ __('القيمة الفردية') }}</th>
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
                                @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px 12px; font-weight: 700; color: #64748b;">{{ $counter++ }}</td>
                                    <td style="padding: 10px 12px;">
                                        <strong style="color: #0f172a; font-size: 0.88rem;">{{ $itemSubName }}</strong>
                                        <div style="color: #64748b; font-size: 0.72rem;">{{ __('شاملة الشروحات، بنوك الأسئلة، والامتحانات الوزارية التجريبية') }}</div>
                                    </td>
                                    <td style="padding: 10px 12px; color: #334155; font-size: 0.8rem;">
                                        {{ __('عام دراسي كامل (حتى امتحانات 2026/2027)') }}
                                    </td>
                                    <td style="padding: 10px 12px;">
                                        @if($payment->status === 'completed')
                                            <span style="background: #ecfdf5; color: #047857; font-weight: 700; padding: 2px 8px; border-radius: 20px; font-size: 0.74rem; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-check"></i>
                                                <span>{{ __('مفعّل الآن') }}</span>
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span style="background: #fffbeb; color: #b45309; font-weight: 700; padding: 2px 8px; border-radius: 20px; font-size: 0.74rem; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-hourglass-half"></i>
                                                <span>{{ __('بانتظار اعتماد الإدارة') }}</span>
                                            </span>
                                        @else
                                            <span style="background: #fef2f2; color: #b91c1c; font-weight: 700; padding: 2px 8px; border-radius: 20px; font-size: 0.74rem; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-ban"></i>
                                                <span>{{ __('غير مفعّل') }}</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 10px 12px; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; font-weight: 700; color: #0f172a; font-family: monospace;">
                                        {{ number_format($item['price'] ?? 0, 2) }} {{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 1.25rem; color: #64748b;">{{ __('لا توجد تفاصيل للمواد') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if($payment->receipt_path)
        <!-- قسم إشعار التحويل المرفوع للمراجعة -->
        <div class="receipt-proof-section" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                <span style="font-weight: 700; font-size: 0.85rem; color: #1e293b; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-file-circle-check" style="color: #1d4ed8;"></i>
                    <span>{{ __('صورة إشعار السداد البنكي المرفوعة:') }}</span>
                </span>
                <span style="font-size: 0.75rem; color: #64748b;">{{ __('مُرفقة مع المعاملة :tx', ['tx' => $payment->transaction_number]) }}</span>
            </div>
            <div style="text-align: center; background: #ffffff; border-radius: 8px; padding: 10px; border: 1px solid #e2e8f0;">
                @php
                    $isPdf = \Illuminate\Support\Str::endsWith(strtolower($payment->receipt_path), '.pdf');
                @endphp
                @if($isPdf)
                    <div style="padding: 18px;">
                        <i class="fa-solid fa-file-pdf" style="font-size: 2.8rem; color: #ef4444; margin-bottom: 6px; display: block;"></i>
                        <div style="font-weight: 700; color: #1e293b; font-size: 0.85rem;">{{ __('تم إرفاق مستند الإشعار بصيغة PDF') }}</div>
                        <a href="{{ asset('storage/' . $payment->receipt_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; margin-top: 8px; color: #1d4ed8; text-decoration: none; font-weight: 700; font-size: 0.8rem;">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('عرض المستند') }}
                        </a>
                    </div>
                @else
                    <img src="{{ asset('storage/' . $payment->receipt_path) }}" alt="{{ __('صورة إشعار السداد البنكي المرفوعة:') }}" style="max-height: 240px; max-width: 100%; object-fit: contain; border-radius: 6px; border: 1px solid #e2e8f0;">
                @endif
            </div>
        </div>
        @endif

        <!-- ملخص الحساب والإجمالي المالي -->
        <div class="financial-summary" style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
            <div style="width: 100%; max-width: 320px; background: #f8fafc; border-radius: 10px; padding: 1rem; border: 1px solid #e2e8f0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #64748b; font-size: 0.82rem;">
                    <span>{{ __('العملة الرسمية:') }}</span>
                    <strong style="color: #0f172a;">{{ __('الشيكل الفلسطيني الجديد (ILS ₪)') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #64748b; font-size: 0.82rem;">
                    <span>{{ __('رسوم المعاملة الإلكترونية:') }}</span>
                    <strong style="color: #16a34a;">{{ __('0.00 ₪ (معفية مجاناً)') }}</strong>
                </div>
                <div style="border-top: 1px dashed #cbd5e1; margin: 8px 0; padding-top: 8px; display: flex; justify-content: space-between; align-items: baseline;">
                    <span style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">{{ __('المبلغ المطلوب سداده:') }}</span>
                    <span style="font-size: 1.3rem; font-weight: 800; color: #1d4ed8; font-family: monospace;">{{ number_format($payment->amount, 2) }} {{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}</span>
                </div>
            </div>
        </div>

        <!-- الختم الرقمي الفلسطيني والـ QR Code والمعلومات القانونية -->
        <div class="receipt-footer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid #e2e8f0; padding-top: 1.25rem;">
            
            <!-- الختم الدائري المعتمد -->
            <div class="digital-stamp" style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 76px; height: 76px; border-radius: 50%; border: 2px double {{ $payment->status === 'completed' ? '#16a34a' : '#d97706' }}; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: {{ $payment->status === 'completed' ? '#16a34a' : '#d97706' }}; padding: 4px; transform: rotate(-4deg); background: {{ $payment->status === 'completed' ? '#f0fdf4' : '#fffbeb' }};">
                    <i class="fa-solid {{ $payment->status === 'completed' ? 'fa-stamp' : 'fa-hourglass-half' }}" style="font-size: 1rem; margin-bottom: 2px;"></i>
                    <span style="font-size: 0.58rem; font-weight: 800; line-height: 1.1;">{{ $payment->status === 'completed' ? __('معتمد رسمياً') : __('قيد التدقيق') }}<br>{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}<br>2026/2027</span>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 0.82rem; color: #0f172a;">{{ __('توثيق المعاملة والاعتماد الإلكتروني') }}</div>
                    <div style="font-size: 0.72rem; color: #64748b;">{{ __('هذا الإيصال مُصدر ومسجل في النظام المالي للمنصة') }}</div>
                    <div style="font-size: 0.72rem; color: #1d4ed8; font-weight: 600;">{{ __('الدعم الفني: واتساب 00970597694385 • جوال باي وبنك فلسطين 0567897212 • فلسطين') }}</div>
                </div>
            </div>

            <!-- الباركود / كود التحقق الرقمي -->
            <div style="text-align: center;">
                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; display: inline-block;">
                    <i class="fa-solid fa-qrcode" style="font-size: 2.4rem; color: #0f172a;"></i>
                    <div style="font-family: monospace; font-size: 0.68rem; color: #64748b; margin-top: 2px;">TX-{{ substr($payment->transaction_number, -8) }}</div>
                </div>
            </div>
        </div>

    </div>

    @if($payment->status === 'completed')
        <!-- رسالة تحفيزية عند اكتمال التفعيل -->
        <div class="no-print mt-4 text-center p-3" style="background: #f0fdf4; border-radius: 12px; border: 1px solid #bbf7d0;">
            <h3 style="color: #15803d; font-weight: 800; font-size: 1.05rem; margin-bottom: 4px;">
                {{ __('🎓 انطلاقة موفقة نحو التفوق والـ 99% بإذن الله!') }}
            </h3>
            <p style="color: #334155; margin-bottom: 1rem; font-size: 0.88rem;">
                {{ __('تم اعتماد تفعيل المواد بنجاح، بإمكانك الآن حضور الدروس والشروحات وتقديم الامتحانات التجريبية فوراً.') }}
            </p>
            <a href="{{ route('student.dashboard') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #1d4ed8; color: white; text-decoration: none; padding: 9px 24px; border-radius: 8px; font-weight: 700; font-size: 0.88rem; transition: 0.2s;">
                <span>{{ __('ابدأ دراسة المواد الآن') }}</span>
                <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            </a>
        </div>
    @elseif($payment->status === 'pending')
        <!-- رسالة تنبيهية عند الانتظار للمراجعة من الإدارة -->
        <div class="no-print mt-4 text-center p-3" style="background: #fffbeb; border-radius: 12px; border: 1px solid #fde68a;">
            <h3 style="color: #b45309; font-weight: 800; font-size: 1.05rem; margin-bottom: 6px;">
                {{ __('⏳ إشعار الدفع قيد التدقيق والمراجعة من الإدارة') }}
            </h3>
            <p style="color: #78350f; margin-bottom: 1rem; font-size: 0.85rem; max-width: 580px; margin-left: auto; margin-right: auto; line-height: 1.55;">
                {{ __('تم رفع صورة الإشعار بنجاح إلى الإدارة. يقوم المسؤول الآن بمطابقة عملية التحويل وتفعيل المواد لحسابك رسمياً. ستصلك رسالة وإشعار فوري عند اكتمال التفعيل.') }}
            </p>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('student.dashboard') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #1d4ed8; color: white; text-decoration: none; padding: 8px 20px; border-radius: 8px; font-weight: 700; font-size: 0.84rem;">
                    <span>{{ __('العودة إلى لوحة التحكم') }}</span>
                    <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                </a>
                @php 
                    $waDirect = '970597694385'; 
                    $waDisplay = '00970597694385';
                    $waText = urlencode(app()->getLocale() === 'ar' 
                        ? ('مرحباً إدارة منارة التوجيهي، قمت بسداد الرسوم ورفع إشعار العملية برقم: ' . $payment->transaction_number . ' بمبلغ: ' . $payment->amount . ' ₪، يرجى التكرم بالاعتماد وتفعيل المواد.')
                        : ('Hello Menaret Al-Tawjihi Management, I submitted payment receipt for TX: ' . $payment->transaction_number . ' with amount: ' . $payment->amount . ' ILS, please verify and activate.'));
                @endphp
                <a href="https://wa.me/{{ $waDirect }}?text={{ $waText }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: #16a34a; color: white; text-decoration: none; padding: 8px 20px; border-radius: 8px; font-weight: 700; font-size: 0.84rem;">
                    <i class="fa-brands fa-whatsapp" style="font-size: 1rem;"></i>
                    <span>{{ __('متابعة فورية مع المشرف عبر واتساب (:phone)', ['phone' => $waDisplay]) }}</span>
                </a>
            </div>
        </div>
    @else
        <div class="no-print mt-4 text-center p-3" style="background: #fef2f2; border-radius: 12px; border: 1px solid #fecaca;">
            <h3 style="color: #b91c1c; font-weight: 800; font-size: 1.05rem; margin-bottom: 4px;">
                {{ __('❌ لم يتم اعتماد عملية السداد أو تم إلغاؤها') }}
            </h3>
            <p style="color: #7f1d1d; margin-bottom: 1rem; font-size: 0.85rem;">
                {{ __('إذا كنت قد قمت بالتحويل بالفعل، يرجى التواصل مع إدارة المنصة عبر واتساب للتحقق وإعادة التدقيق.') }}
            </p>
            <a href="{{ route('student.courses.catalog') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #1d4ed8; color: white; text-decoration: none; padding: 8px 20px; border-radius: 8px; font-weight: 700; font-size: 0.84rem;">
                <span>{{ __('إعادة المحاولة عبر الكتالوج') }}</span>
            </a>
        </div>
    @endif
</div>

<style>
/* تحسينات الطباعة الرسمية للوثائق والفواتير */
@media print {
    .no-print, aside, header, nav, footer, .top-bar, .sidebar {
        display: none !important;
    }
    body {
        background: white !important;
        color: black !important;
        font-family: 'Alexandria', 'Tajawal', sans-serif !important;
    }
    .receipt-container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .receipt-card {
        border: none !important;
        box-shadow: none !important;
        padding: 1rem !important;
    }
}
</style>
@endsection
