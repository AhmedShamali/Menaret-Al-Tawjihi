@extends('layouts.app')

@section('title', 'إيصال سداد وتفعيل اشتراك رقمي')

@section('content')
<div class="receipt-container" style="max-width: 820px; margin: 2rem auto; padding: 0 1rem;">
    <!-- أزرار الإجراءات السريعة في الأعلى (تختفي في الطباعة) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <a href="{{ route('student.dashboard') }}" class="btn-return" style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-weight: 600; padding: 10px 16px; border-radius: 12px; background: rgba(0,0,0,0.03); transition: var(--transition);">
            <i class="fa-solid fa-arrow-right"></i>
            <span>العودة للوحة المواد</span>
        </a>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-print" style="display: inline-flex; align-items: center; gap: 8px; background: #0284c7; color: white; border: none; padding: 10px 22px; border-radius: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35); transition: var(--transition);">
                <i class="fa-solid fa-print"></i>
                <span>طباعة الإيصال / حفظ PDF</span>
            </button>
            <a href="{{ route('student.courses.catalog') }}" class="btn-catalog" style="display: inline-flex; align-items: center; gap: 8px; background: #10b981; color: white; text-decoration: none; padding: 10px 18px; border-radius: 12px; font-weight: 700; transition: var(--transition);">
                <i class="fa-solid fa-book-open"></i>
                <span>تصفح مواد إضافية</span>
            </a>
        </div>
    </div>

    <!-- بطاقة الإيصال الرسمية المعتمَدة -->
    <div class="receipt-card" style="background: #ffffff; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; position: relative; overflow: hidden; padding: 3rem 2.5rem;">
        
        <!-- الشريط العلوي الملون بنمط الهوية الفلسطينية -->
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 8px; background: linear-gradient(90deg, #000000 25%, #e11d48 25%, #e11d48 50%, #ffffff 50%, #ffffff 75%, #059669 75%);"></div>

        <!-- شارة الحالة: مدفوع ومعتمد أو قيد المراجعة -->
        @if($payment->status === 'completed')
            <div class="paid-badge" style="position: absolute; top: 28px; left: 28px; background: #ecfdf5; border: 2px solid #10b981; color: #047857; font-size: 0.95rem; font-weight: 800; padding: 6px 18px; border-radius: 50px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-check" style="font-size: 1.1rem;"></i>
                <span>مدفوع ومفعل رسمياً</span>
            </div>
        @elseif($payment->status === 'pending')
            <div class="paid-badge" style="position: absolute; top: 28px; left: 28px; background: #fffbeb; border: 2px solid #f59e0b; color: #b45309; font-size: 0.95rem; font-weight: 800; padding: 6px 18px; border-radius: 50px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 0 15px rgba(245, 158, 11, 0.25);">
                <i class="fa-solid fa-clock" style="font-size: 1.1rem;"></i>
                <span>قيد المراجعة والتدقيق</span>
            </div>
        @else
            <div class="paid-badge" style="position: absolute; top: 28px; left: 28px; background: #fef2f2; border: 2px solid #ef4444; color: #b91c1c; font-size: 0.95rem; font-weight: 800; padding: 6px 18px; border-radius: 50px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-xmark" style="font-size: 1.1rem;"></i>
                <span>ملغي أو لم يُعتمد</span>
            </div>
        @endif

        <!-- ترويسة الإيصال الرسمية -->
        <div class="receipt-header" style="text-align: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 2rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 14px; margin-bottom: 0.75rem;">
                @if(\App\Models\Setting::get('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}" style="max-height: 55px; max-width: 110px; object-fit: contain;">
                @else
                    <span style="font-size: 2.2rem;">🇵🇸</span>
                @endif
                <h1 style="margin: 0; font-size: 1.65rem; font-weight: 800; color: #0f172a; font-family: 'Alexandria', sans-serif;">{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</h1>
            </div>
            <p style="margin: 0 0 0.5rem; color: #64748b; font-size: 0.95rem; font-weight: 500;">
                {{ \App\Models\Setting::get('site_slogan', 'المنصة الوطنية الرائدة لطلبة الثانوية العامة في فلسطين') }}
            </p>
            <div style="display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 4px 14px; font-size: 0.85rem; color: #334155; font-weight: 700;">
                إيصال استلام مالي وتفعيل اشتراك دراسي معتمد • Official Verified E-Receipt
            </div>
        </div>

        <!-- معلومات الفاتورة الأساسية في شبكة -->
        <div class="receipt-meta-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #f1f5f9;">
            <div>
                <div style="color: #64748b; font-size: 0.8rem; font-weight: 600; margin-bottom: 4px;">رقم الإيصال المرجعي</div>
                <div style="color: #0f172a; font-weight: 800; font-size: 0.95rem; font-family: monospace; direction: ltr; text-align: right;">{{ $payment->transaction_number }}</div>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.8rem; font-weight: 600; margin-bottom: 4px;">تاريخ ووقت المعاملة</div>
                <div style="color: #0f172a; font-weight: 700; font-size: 0.9rem;">{{ $payment->created_at ? $payment->created_at->format('Y-m-d - h:i A') : now()->format('Y-m-d') }}</div>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.8rem; font-weight: 600; margin-bottom: 4px;">طريقة السداد / المزود</div>
                <div style="color: #0284c7; font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-wallet"></i>
                    <span>{{ $payment->gateway_name_ar }}</span>
                </div>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.8rem; font-weight: 600; margin-bottom: 4px;">حالة المعاملة</div>
                @if($payment->status === 'completed')
                    <div style="color: #059669; font-weight: 800; font-size: 0.95rem;">مكتمل ومؤكد بنجاح ✅</div>
                @elseif($payment->status === 'pending')
                    <div style="color: #d97706; font-weight: 800; font-size: 0.95rem;">بانتظار تدقيق الإشعار ⏳</div>
                @else
                    <div style="color: #dc2626; font-weight: 800; font-size: 0.95rem;">ملغي ❌</div>
                @endif
            </div>
        </div>

        <!-- بيانات الطالب -->
        <div class="student-info-section" style="margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0 0 1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-graduation-cap" style="color: #0284c7;"></i>
                <span>بيانات الطالب المشترك:</span>
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px;">
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">الاسم الثلاثي:</span>
                    <strong style="color: #0f172a; font-size: 0.95rem;">{{ $payment->student->name_ar ?? 'طالب توجيهي' }}</strong>
                </div>
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px;">
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">الفرع الأكاديمي / الصف:</span>
                    <strong style="color: #0f172a; font-size: 0.95rem;">{{ $payment->student->stage->name_ar ?? 'الثانوية العامة (التوجيهي)' }}</strong>
                </div>
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px;">
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">البريد الإلكتروني / الحساب:</span>
                    <strong style="color: #0f172a; font-size: 0.9rem; direction: ltr; display: inline-block;">{{ $payment->student->email ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- جدول المواد المفعّلة -->
        <div class="items-table-section" style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0 0 1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: #10b981;"></i>
                <span>المواد والمقررات المفعلة بموجب هذا الإيصال:</span>
            </h3>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right;">
                    <thead>
                        <tr style="background: #f1f5f9; color: #334155; font-size: 0.85rem; font-weight: 700;">
                            <th style="padding: 12px 16px; border-radius: 0 10px 10px 0;">#</th>
                            <th style="padding: 12px 16px;">المادة التعليمية</th>
                            <th style="padding: 12px 16px;">نوع الاشتراك وصلاحيته</th>
                            <th style="padding: 12px 16px;">حالة الصلاحية</th>
                            <th style="padding: 12px 16px; border-radius: 10px 0 0 10px; text-align: left;">القيمة الفردية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $items = is_array($payment->items) ? $payment->items : json_decode($payment->items, true);
                            $counter = 1;
                        @endphp
                        @if(!empty($items))
                            @foreach($items as $item)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 14px 16px; font-weight: 700; color: #64748b;">{{ $counter++ }}</td>
                                    <td style="padding: 14px 16px;">
                                        <strong style="color: #0f172a; font-size: 0.95rem;">{{ $item['name_ar'] ?? 'مادة دراسية' }}</strong>
                                        <div style="color: #64748b; font-size: 0.75rem;">شاملة الشروحات، بنوك الأسئلة، والامتحانات الوزارية التجريبية</div>
                                    </td>
                                    <td style="padding: 14px 16px; color: #334155; font-size: 0.85rem;">
                                        عام دراسي كامل (حتى امتحانات 2026/2027)
                                    </td>
                                    <td style="padding: 14px 16px;">
                                        @if($payment->status === 'completed')
                                            <span style="background: #ecfdf5; color: #047857; font-weight: 700; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-check"></i>
                                                <span>مفعّل الآن</span>
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span style="background: #fffbeb; color: #b45309; font-weight: 700; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-hourglass-half"></i>
                                                <span>بانتظار اعتماد الإدارة</span>
                                            </span>
                                        @else
                                            <span style="background: #fef2f2; color: #b91c1c; font-weight: 700; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-ban"></i>
                                                <span>غير مفعّل</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 16px; text-align: left; font-weight: 800; color: #0f172a;">
                                        {{ number_format($item['price'] ?? 0, 2) }} ₪
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 1.5rem; color: #64748b;">لا توجد تفاصيل للمواد</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if($payment->receipt_path)
        <!-- قسم إشعار التحويل المرفوع للمراجعة -->
        <div class="receipt-proof-section" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 16px; padding: 1.25rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                <span style="font-weight: 800; font-size: 0.92rem; color: #1e293b; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-file-circle-check" style="color: #0284c7;"></i>
                    <span>صورة إشعار السداد البنكي المرفوعة:</span>
                </span>
                <span style="font-size: 0.8rem; color: #64748b;">مُرفقة مع المعاملة {{ $payment->transaction_number }}</span>
            </div>
            <div style="text-align: center; background: #ffffff; border-radius: 12px; padding: 12px; border: 1px solid #e2e8f0;">
                @php
                    $isPdf = \Illuminate\Support\Str::endsWith(strtolower($payment->receipt_path), '.pdf');
                @endphp
                @if($isPdf)
                    <div style="padding: 24px;">
                        <i class="fa-solid fa-file-pdf" style="font-size: 3.5rem; color: #ef4444; margin-bottom: 8px; display: block;"></i>
                        <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">تم إرفاق مستند الإشعار بصيغة PDF</div>
                        <a href="{{ asset('storage/' . $payment->receipt_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; color: #0284c7; text-decoration: none; font-weight: 700; font-size: 0.85rem;">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> عرض المستند
                        </a>
                    </div>
                @else
                    <img src="{{ asset('storage/' . $payment->receipt_path) }}" alt="إشعار التحويل البنكي" style="max-height: 280px; max-width: 100%; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                @endif
            </div>
        </div>
        @endif

        <!-- ملخص الحساب والإجمالي المالي -->
        <div class="financial-summary" style="display: flex; justify-content: flex-end; margin-bottom: 2.5rem;">
            <div style="width: 100%; max-width: 360px; background: #f8fafc; border-radius: 16px; padding: 1.25rem; border: 1px solid #e2e8f0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #64748b; font-size: 0.9rem;">
                    <span>العملة الرسمية:</span>
                    <strong style="color: #0f172a;">الشيكل الفلسطيني الجديد (ILS ₪)</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #64748b; font-size: 0.9rem;">
                    <span>رسوم المعاملة الإلكترونية:</span>
                    <strong style="color: #10b981;">0.00 ₪ (معفية مجاناً)</strong>
                </div>
                <div style="border-top: 2px dashed #cbd5e1; margin: 10px 0; padding-top: 10px; display: flex; justify-content: space-between; align-items: baseline;">
                    <span style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">المبلغ المطلوب سداده:</span>
                    <span style="font-size: 1.5rem; font-weight: 900; color: #0284c7;">{{ number_format($payment->amount, 2) }} ₪</span>
                </div>
            </div>
        </div>

        <!-- الختم الرقمي الفلسطيني والـ QR Code والمعلومات القانونية -->
        <div class="receipt-footer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
            
            <!-- الختم الدائري المعتمد -->
            <div class="digital-stamp" style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 90px; height: 90px; border-radius: 50%; border: 3px double {{ $payment->status === 'completed' ? '#059669' : '#f59e0b' }}; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: {{ $payment->status === 'completed' ? '#059669' : '#f59e0b' }}; padding: 6px; transform: rotate(-5deg); background: {{ $payment->status === 'completed' ? 'rgba(5, 150, 105, 0.04)' : 'rgba(245, 158, 11, 0.04)' }};">
                    <i class="fa-solid {{ $payment->status === 'completed' ? 'fa-stamp' : 'fa-hourglass-half' }}" style="font-size: 1.2rem; margin-bottom: 2px;"></i>
                    <span style="font-size: 0.62rem; font-weight: 800; line-height: 1.1;">{{ $payment->status === 'completed' ? 'معتمد رسمياً' : 'قيد التدقيق' }}<br>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}<br>2026/2027</span>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 0.85rem; color: #0f172a;">توثيق المعاملة والاعتماد الإلكتروني</div>
                    <div style="font-size: 0.75rem; color: #64748b;">هذا الإيصال مُصدر ومسجل في النظام المالي للمنصة</div>
                    <div style="font-size: 0.75rem; color: #0284c7; font-weight: 600;">الدعم الفني: واتساب {{ \App\Models\Setting::get('contact_whatsapp', \App\Models\Setting::get('payment_phone', '0567897212')) }} • القدس - غزة - الضفة</div>
                </div>
            </div>

            <!-- الباركود / كود التحقق الرقمي -->
            <div style="text-align: center;">
                <div style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 8px 14px; display: inline-block;">
                    <i class="fa-solid fa-qrcode" style="font-size: 3rem; color: #0f172a;"></i>
                    <div style="font-family: monospace; font-size: 0.7rem; color: #64748b; margin-top: 4px;">TX-{{ substr($payment->transaction_number, -8) }}</div>
                </div>
            </div>
        </div>

    </div>

    @if($payment->status === 'completed')
        <!-- رسالة تحفيزية عند اكتمال التفعيل -->
        <div class="no-print mt-4 text-center p-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%); border-radius: 20px; border: 1px solid #bae6fd;">
            <h3 style="color: #0369a1; font-weight: 800; font-size: 1.2rem; margin-bottom: 6px;">
                🎓 انطلاقة موفقة نحو التفوق والـ 99% بإذن الله!
            </h3>
            <p style="color: #334155; margin-bottom: 1.25rem; font-size: 0.95rem;">
                تم اعتماد تفعيل المواد بنجاح، بإمكانك الآن حضور الدروس والشروحات وتقديم الامتحانات التجريبية فوراً.
            </p>
            <a href="{{ route('student.dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #0284c7; color: white; text-decoration: none; padding: 12px 28px; border-radius: 50px; font-weight: 800; font-size: 1rem; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);">
                <span>ابدأ دراسة المواد الآن</span>
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    @elseif($payment->status === 'pending')
        <!-- رسالة تنبيهية عند الانتظار للمراجعة من الإدارة -->
        <div class="no-print mt-4 text-center p-4" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-radius: 20px; border: 1px solid #fde68a;">
            <h3 style="color: #b45309; font-weight: 800; font-size: 1.25rem; margin-bottom: 8px;">
                ⏳ إشعار الدفع قيد التدقيق والمراجعة من الإدارة
            </h3>
            <p style="color: #78350f; margin-bottom: 1.25rem; font-size: 0.95rem; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                تم رفع صورة الإشعار بنجاح إلى الإدارة. يقوم المسؤول الآن بمطابقة عملية التحويل وتفعيل المواد لحسابك رسمياً. ستصلك رسالة وإشعار فوري عند اكتمال التفعيل.
            </p>
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('student.dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #0284c7; color: white; text-decoration: none; padding: 11px 24px; border-radius: 50px; font-weight: 700; font-size: 0.92rem;">
                    <span>العودة إلى لوحة التحكم</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                @php $adminWhatsapp = \App\Models\Setting::get('contact_whatsapp', \App\Models\Setting::get('payment_phone', '0567897212')); @endphp
                <a href="https://wa.me/972{{ ltrim($adminWhatsapp, '0') }}?text={{ urlencode('مرحباً إدارة منارة التوجيهي، قمت برفع إشعار دفع جديد برقم المعاملة: ' . $payment->transaction_number . ' للتفعيل.') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: #25d366; color: white; text-decoration: none; padding: 11px 24px; border-radius: 50px; font-weight: 700; font-size: 0.92rem; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);">
                    <i class="fa-brands fa-whatsapp" style="font-size: 1.2rem;"></i>
                    <span>متابعة فورية مع المشرف عبر واتساب</span>
                </a>
            </div>
        </div>
    @else
        <div class="no-print mt-4 text-center p-4" style="background: #fef2f2; border-radius: 20px; border: 1px solid #fecaca;">
            <h3 style="color: #b91c1c; font-weight: 800; font-size: 1.2rem; margin-bottom: 6px;">
                ❌ لم يتم اعتماد عملية السداد أو تم إلغاؤها
            </h3>
            <p style="color: #7f1d1d; margin-bottom: 1.25rem; font-size: 0.95rem;">
                إذا كنت قد قمت بالتحويل بالفعل، يرجى التواصل مع إدارة المنصة عبر واتساب للتحقق وإعادة التدقيق.
            </p>
            <a href="{{ route('student.courses.catalog') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #0284c7; color: white; text-decoration: none; padding: 10px 22px; border-radius: 50px; font-weight: 700;">
                <span>إعادة المحاولة عبر الكتالوج</span>
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
        font-family: 'Alexandria', sans-serif !important;
    }
    .receipt-container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .receipt-card {
        border: none !important;
        box-shadow: none !important;
        padding: 1.5rem !important;
    }
}
</style>
@endsection
