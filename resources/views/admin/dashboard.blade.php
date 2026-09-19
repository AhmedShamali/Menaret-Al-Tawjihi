@extends('layouts.app')

@section('title', __('لوحة الإدارة المركزية') . ' | ' . __(config('app.name', 'منارة التوجيهي')))

@section('content')
<div class="dash-wrapper">

    <!-- 1. ترويسة الصفحة البسيطة والواضحة -->
    <div class="dash-header-bar">
        <div class="dash-header-info">
            <h1 class="dash-title">
                <i class="fa-solid fa-gauge-high text-primary"></i>
                {{ __('لوحة الإدارة المركزية') }}
            </h1>
            <div class="dash-breadcrumbs">
                <span>{{ __(config('app.name', 'منارة التوجيهي')) }}</span>
                <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} sep"></i>
                <span class="active">{{ __('المؤشرات العامة ومتابعة المنصة') }}</span>
            </div>
        </div>

        <div class="dash-header-meta">
            <div class="dash-pill date-pill">
                <i class="fa-regular fa-calendar-check text-muted"></i>
                <span>{{ now()->translatedFormat('l، j F Y') }}{{ app()->getLocale() === 'ar' ? ' م' : ' AD' }}</span>
            </div>
            <div class="dash-pill status-pill">
                <span class="live-status-dot"></span>
                <span>{{ __('النظام متصل ومستقر') }}</span>
            </div>
        </div>
    </div>

    <!-- 2. بطاقات المؤشرات الأكاديمية البسيطة والواضحة (Compact Metric Boxes) -->
    <div class="dash-kpi-row">
        <!-- كادر المعلمين -->
        <div class="kpi-box" style="--accent: #1d4ed8;">
            <div class="kpi-content">
                <span class="kpi-label">{{ __('كادر المعلمين المعتمد') }}</span>
                <div class="kpi-value">{{ number_format($data['total_teachers'] ?? 0) }}</div>
                <span class="kpi-subtext">{{ __('أعضاء الهيئة التدريسية المعتمدة') }}</span>
            </div>
            <div class="kpi-icon-wrap" style="color: #1d4ed8; background: #eff6ff;">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </div>

        <!-- الطلبة المسجلون -->
        <div class="kpi-box" style="--accent: #059669;">
            <div class="kpi-content">
                <span class="kpi-label">{{ __('الطلبة المسجلون بالمنصة') }}</span>
                <div class="kpi-value">{{ number_format($data['total_students'] ?? 0) }}</div>
                <span class="kpi-subtext">{{ __('طلبة الثانوية في كافة الفروع') }}</span>
            </div>
            <div class="kpi-icon-wrap" style="color: #059669; background: #ecfdf5;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <!-- المقررات والمحتوى -->
        <div class="kpi-box" style="--accent: #d97706;">
            <div class="kpi-content">
                <span class="kpi-label">{{ __('المحتوى الأكاديمي والملفات') }}</span>
                <div class="kpi-value">{{ number_format($data['total_files'] ?? 0) }}</div>
                <span class="kpi-subtext">{{ __('ملفات وملازم دراسية نشطة') }}</span>
            </div>
            <div class="kpi-icon-wrap" style="color: #d97706; background: #fffbeb;">
                <i class="fa-solid fa-book-open"></i>
            </div>
        </div>

        <!-- الجاهزية والأمان -->
        <div class="kpi-box" style="--accent: #4f46e5;">
            <div class="kpi-content">
                <span class="kpi-label">{{ __('الجاهزية والأمان التقني') }}</span>
                <div class="kpi-value">99.9%</div>
                <span class="kpi-subtext">{{ __('حماية وتوافرية كاملة للبيانات') }}</span>
            </div>
            <div class="kpi-icon-wrap" style="color: #4f46e5; background: #eef2ff;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>
    </div>

    <!-- 3. شبكة المحتوى البسيطة والعملية (Main Academic Grid) -->
    <div class="dash-main-grid">

        <!-- العمود الرئيسي (الأيمن) -->
        <div class="dash-col-main">

            <!-- جدول ملخص المنظومة الكلاسيكي (مثل جداول الجامعات النظيفة) -->
            <div class="simple-card">
                <div class="simple-card-header">
                    <div class="simple-card-title">
                        <i class="fa-solid fa-table-list text-primary"></i>
                        <span>{{ __('ملخص السجلات والبيانات الأكاديمية') }}</span>
                    </div>
                    <span class="simple-tag">{{ __('تحديث فوري') }}</span>
                </div>

                <div class="table-responsive">
                    <table class="academic-simple-table">
                        <thead>
                            <tr>
                                <th>{{ __('البيان والقطاع') }}</th>
                                <th style="text-align: center;">{{ __('العدد / الإحصائية') }}</th>
                                <th style="text-align: center;">{{ __('الحالة') }}</th>
                                <th style="text-align: center;">{{ __('الإجراء') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="cell-entity">
                                        <i class="fa-solid fa-chalkboard-user text-primary"></i>
                                        <div>
                                            <strong>{{ __('الهيئة التدريسية والكادر التعليمي') }}</strong>
                                            <small>{{ __('إدارة حسابات المدرسين وصلاحيات المواد') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="count-pill">{{ number_format($data['total_teachers'] ?? 0) }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-badge success"><i class="fa-solid fa-circle-check"></i> {{ __('معتمد') }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.teachers.index') }}" class="btn-action-view">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('عرض') }}
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="cell-entity">
                                        <i class="fa-solid fa-users text-emerald"></i>
                                        <div>
                                            <strong>{{ __('شؤون الطلبة والقبول الأكاديمي') }}</strong>
                                            <small>{{ __('بيانات الطلاب المسجلين والفروع الدراسية') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="count-pill">{{ number_format($data['total_students'] ?? 0) }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-badge success"><i class="fa-solid fa-circle-check"></i> {{ __('نشط') }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.students.index') }}" class="btn-action-view">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('عرض') }}
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="cell-entity">
                                        <i class="fa-solid fa-book-bookmark text-amber"></i>
                                        <div>
                                            <strong>{{ __('المقررات، الملازم والمحتوى التعليمي') }}</strong>
                                            <small>{{ __('المناهج، الأسئلة الوزارية، ومصادر الدراسة') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="count-pill">{{ number_format($data['total_files'] ?? 0) }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-badge info"><i class="fa-solid fa-circle-info"></i> {{ __('متاح') }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.subjects.pricing') }}" class="btn-action-view">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('عرض') }}
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="cell-entity">
                                        <i class="fa-solid fa-money-check-dollar text-indigo"></i>
                                        <div>
                                            <strong>{{ __('مصفوفة الاشتراكات والتحصيلات السنوية') }}</strong>
                                            <small>{{ __('متابعة دفعات الطلاب على مدار 12 شهراً') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="count-pill">12 {{ __('شهراً') }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-badge success"><i class="fa-solid fa-circle-check"></i> {{ __('مستقر') }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.subscriptions.monthly') }}" class="btn-action-view">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('عرض') }}
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="cell-entity">
                                        <i class="fa-solid fa-award text-rose"></i>
                                        <div>
                                            <strong>{{ __('الشهادات ولوائح التميز الوزارية') }}</strong>
                                            <small>{{ __('شهادات إتمام مرحلة التوجيهي ولوحة الشرف') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="count-pill">{{ __('معتمدة') }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-badge info"><i class="fa-solid fa-circle-info"></i> {{ __('جاهز') }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.certificates.index') }}" class="btn-action-view">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('عرض') }}
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- مركز الإجراءات السريعة (بسيط ونظيف بدون كتل ضخمة) -->
            <div class="simple-card">
                <div class="simple-card-header">
                    <div class="simple-card-title">
                        <i class="fa-solid fa-bolt text-primary"></i>
                        <span>{{ __('الوصول السريع والإجراءات الفورية') }}</span>
                    </div>
                </div>

                <div class="quick-actions-simple-grid">
                    <a href="{{ route('admin.teachers.create') }}" class="quick-action-link">
                        <div class="qa-icon" style="background: #eff6ff; color: #1d4ed8;">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div class="qa-text">
                            <strong>{{ __('إضافة معلم جديد') }}</strong>
                            <small>{{ __('تعيين الصلاحيات والمواد') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} qa-arrow"></i>
                    </a>

                    <a href="{{ route('admin.students.create') }}" class="quick-action-link">
                        <div class="qa-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div class="qa-text">
                            <strong>{{ __('تسجيل طالب جديد') }}</strong>
                            <small>{{ __('إدراج حساب طالب في الفروع') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} qa-arrow"></i>
                    </a>

                    <a href="{{ route('admin.subjects.pricing') }}" class="quick-action-link">
                        <div class="qa-icon" style="background: #fffbeb; color: #d97706;">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <div class="qa-text">
                            <strong>{{ __('تسعير وباقات المواد') }}</strong>
                            <small>{{ __('إدارة خطط الاشتراكات') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} qa-arrow"></i>
                    </a>

                    <a href="{{ route('admin.payments.index') }}" class="quick-action-link">
                        <div class="qa-icon" style="background: #f5f3ff; color: #7c3aed;">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div class="qa-text">
                            <strong>{{ __('الاشتراكات والمالية') }}</strong>
                            <small>{{ __('تدقيق ومراجعة المدفوعات') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} qa-arrow"></i>
                    </a>

                    <a href="{{ route('admin.certificates.index') }}" class="quick-action-link">
                        <div class="qa-icon" style="background: #fff1f2; color: #e11d48;">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div class="qa-text">
                            <strong>{{ __('إصدار الشهادات') }}</strong>
                            <small>{{ __('شهادات التميز والإتمام') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} qa-arrow"></i>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" class="quick-action-link">
                        <div class="qa-icon" style="background: #f1f5f9; color: #475569;">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <div class="qa-text">
                            <strong>{{ __('إعدادات المنصة') }}</strong>
                            <small>{{ __('هوية النظام والسيرفر') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} qa-arrow"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- العمود الجانبي (الأيسر) -->
        <div class="dash-col-side">

            <!-- بطاقة القبول والتسجيل -->
            <div class="simple-card">
                <div class="simple-card-header">
                    <div class="simple-card-title">
                        <i class="fa-solid fa-door-open text-primary"></i>
                        <span>{{ __('بوابة القبول والتسجيل') }}</span>
                    </div>
                </div>

                <div class="simple-card-body">
                    <div class="side-item">
                        <span class="side-item-label">{{ __('حالة استقبال التسجيل الجديد') }}</span>
                        @if(class_exists(\App\Models\Setting::class) && \App\Models\Setting::get('registration_status') == 'open')
                            <div class="status-tag-box open">
                                <i class="fa-solid fa-circle-check"></i>
                                <div>
                                    <strong>{{ __('التسجيل الذاتي متاح حالياً') }}</strong>
                                    <small>{{ __('يمكن للطلبة الجدد إنشاء حساباتهم') }}</small>
                                </div>
                            </div>
                        @else
                            <div class="status-tag-box closed">
                                <i class="fa-solid fa-lock"></i>
                                <div>
                                    <strong>{{ __('التسجيل الذاتي متوقف') }}</strong>
                                    <small>{{ __('القبول يتم عبر الإدارة فقط') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="side-item" style="border-top: 1px solid #f1f5f9; padding-top: 14px; margin-top: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: #334155;">{{ __('سعة وجاهزية السيرفر') }}</span>
                            <span style="font-size: 0.78rem; font-weight: 800; color: #1d4ed8;">82%</span>
                        </div>
                        <div class="simple-progress-track">
                            <div class="simple-progress-bar" style="width: 82%;"></div>
                        </div>
                        <small style="font-size: 0.72rem; color: #64748b; display: block; margin-top: 4px;">
                            {{ __('استجابة قواعد البيانات سريعة والأداء ممتاز') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- بطاقة المساعدة والدعم الفني -->
            <div class="simple-card">
                <div class="simple-card-header">
                    <div class="simple-card-title">
                        <i class="fa-solid fa-headset text-primary"></i>
                        <span>{{ __('الدعم والتواصل الإداري') }}</span>
                    </div>
                </div>

                <div class="simple-card-body">
                    <p style="font-size: 0.82rem; color: #64748b; line-height: 1.5; margin: 0 0 12px;">
                        {{ __('متابعة استفسارات ومراسلات الطلبة والمعلمين والإشعارات المركزية.') }}
                    </p>
                    <a href="{{ route('admin.inquiries.index') }}" class="btn-side-support">
                        <i class="fa-solid fa-envelope-open-text"></i>
                        <span>{{ __('صندوق الاستفسارات والدعم') }}</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

<style>
    /* ====================================================================
       تنسيقات لوحة الإدارة الكلاسيكية البسيطة والأنيقة (Simple & Clean Theme)
       خط مريح، بدون كتل ضخمة وبدون تكديس بصري
       ==================================================================== */

    .dash-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        box-sizing: border-box;
    }

    /* 1. ترويسة الصفحة البسيطة والواضحة */
    .dash-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        padding: 12px 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 18px;
    }

    .dash-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 3px;
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.3;
    }

    .dash-title i {
        font-size: 1.05rem;
    }

    .dash-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        color: #64748b;
    }

    .dash-breadcrumbs .sep {
        font-size: 0.65rem;
        color: #cbd5e1;
    }

    .dash-breadcrumbs .active {
        color: #1d4ed8;
        font-weight: 600;
    }

    .dash-header-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .dash-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #334155;
    }

    .dash-pill.status-pill {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }

    .live-status-dot {
        width: 7px;
        height: 7px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
    }

    /* 2. بطاقات المؤشرات الأكاديمية البسيطة والواضحة (Compact KPI Boxes) */
    .dash-kpi-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 18px;
        width: 100%;
    }

    @media (max-width: 1024px) {
        .dash-kpi-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 520px) {
        .dash-kpi-row {
            grid-template-columns: 1fr;
        }
    }

    .kpi-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-top: 3px solid var(--accent, #1d4ed8);
        border-radius: 8px;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .kpi-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    }

    .kpi-content {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .kpi-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 2px;
    }

    .kpi-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .kpi-subtext {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    .kpi-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    /* 3. شبكة المحتوى الرئيسية */
    .dash-main-grid {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: 18px;
        align-items: start;
        width: 100%;
    }

    @media (max-width: 960px) {
        .dash-main-grid {
            grid-template-columns: 1fr;
        }
    }

    .dash-col-main,
    .dash-col-side {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    /* البطاقات الموحدة البسيطة */
    .simple-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }

    .simple-card-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .simple-card-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .simple-tag {
        font-size: 0.72rem;
        font-weight: 600;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 2px 8px;
        border-radius: 4px;
        border: 1px solid #bfdbfe;
    }

    .simple-card-body {
        padding: 16px;
    }

    /* جدول السجلات الكلاسيكي (مثل جداول الجامعات) */
    .academic-simple-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
    }

    .academic-simple-table th {
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
        padding: 9px 14px;
        border-bottom: 1.5px solid #cbd5e1;
        font-size: 0.8rem;
        white-space: nowrap;
    }

    .academic-simple-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #1e293b;
    }

    .academic-simple-table tbody tr:hover {
        background: #f8fafc;
    }

    .cell-entity {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cell-entity i {
        font-size: 1rem;
        flex-shrink: 0;
    }

    .cell-entity strong {
        display: block;
        font-size: 0.84rem;
        font-weight: 700;
        color: #0f172a;
    }

    .cell-entity small {
        display: block;
        font-size: 0.72rem;
        color: #64748b;
    }

    .count-pill {
        display: inline-block;
        padding: 2px 8px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #0f172a;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .status-badge.success {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .status-badge.info {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .btn-action-view {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 4px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #1d4ed8;
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.15s ease;
    }

    .btn-action-view:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: #1d4ed8;
    }

    /* مركز الإجراءات السريعة البسيطة */
    .quick-actions-simple-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        padding: 14px;
    }

    @media (max-width: 600px) {
        .quick-actions-simple-grid {
            grid-template-columns: 1fr;
        }
    }

    .quick-action-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .quick-action-link:hover {
        background: #ffffff;
        border-color: #1d4ed8;
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.08);
        transform: translateY(-1px);
    }

    .qa-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: grid;
        place-items: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .qa-text {
        flex: 1;
        min-width: 0;
    }

    .qa-text strong {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .qa-text small {
        display: block;
        font-size: 0.72rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .qa-arrow {
        font-size: 0.7rem;
        color: #94a3b8;
    }

    /* العمود الجانبي */
    .status-tag-box {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
    }

    .status-tag-box.open {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .status-tag-box.closed {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .status-tag-box i {
        font-size: 1.2rem;
    }

    .status-tag-box strong {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .status-tag-box small {
        display: block;
        font-size: 0.72rem;
        opacity: 0.85;
    }

    .simple-progress-track {
        height: 7px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }

    .simple-progress-bar {
        height: 100%;
        background: #1d4ed8;
        border-radius: 999px;
    }

    .btn-side-support {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 8px 12px;
        border-radius: 6px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #1d4ed8;
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 700;
        transition: all 0.15s ease;
    }

    .btn-side-support:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: #1d4ed8;
    }

    /* الألوان المساعدة */
    .text-primary { color: #1d4ed8 !important; }
    .text-emerald { color: #059669 !important; }
    .text-amber { color: #d97706 !important; }
    .text-indigo { color: #4f46e5 !important; }
    .text-rose { color: #e11d48 !important; }
    .text-muted { color: #64748b !important; }
</style>
@endsection
