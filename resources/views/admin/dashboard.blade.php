@extends('layouts.app')

@section('title', __('Central Admin Dashboard') . ' | ' . config('app.name'))

@section('content')
<div class="ed-admin-container">

    <!-- 1. ترويسة الصرح الأكاديمي المركزي (Classic Royal Banner) -->
    <header class="classic-header-banner">
        <div class="classic-banner-main">
            <div class="classic-breadcrumbs">
                <i class="fa-solid fa-building-columns text-amber"></i>
                <span>{{ config('app.name', 'منارة التوجيهي') }}</span>
                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} divider"></i>
                <span class="active">{{ __('لوحة الإدارة المركزية والرقابة الأكاديمية') }}</span>
            </div>
            
            <div class="classic-title-row">
                <div class="academic-crest-box">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h1 class="classic-main-heading">{{ __('لوحة الإدارة المركزية والتحكم الأكاديمي') }}</h1>
                    <p class="classic-desc-text">{{ __('غرفة العمليات الإدارية الرئيسية لمنظومة منارة التوجيهي. ملخص مؤشرات الأداء، رقابة الكادر التعليمي، ومتابعة العمليات المباشرة.') }}</p>
                </div>
            </div>
        </div>

        <div class="classic-banner-badges">
            <div class="classic-status-pill online">
                <span class="live-dot-pulse"></span>
                <span>{{ __('حالة المنصة: متصل ومستقر') }}</span>
            </div>
            <div class="classic-date-pill">
                <i class="fa-regular fa-calendar-check text-amber"></i>
                <span>{{ now()->translatedFormat('l, j F Y') }} م</span>
            </div>
        </div>
    </header>

    <!-- 2. بطاقات المؤشرات الأكاديمية الكلاسيكية الفاخرة (Royal KPI Cards) -->
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
            <span class="stat-label">{{ __('كادر المعلمين المعتمد') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ number_format($data['total_teachers'] ?? 0) }}</span>
                <span class="stat-icon text-navy"><i class="fa-solid fa-chalkboard-user"></i></span>
            </div>
            <small>{{ __('أعضاء الهيئة التدريسية المعتمدة') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #059669;">
            <span class="stat-label">{{ __('الطلبة المسجلون بالمنصة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ number_format($data['total_students'] ?? 0) }}</span>
                <span class="stat-icon text-emerald"><i class="fa-solid fa-user-graduate"></i></span>
            </div>
            <small>{{ __('طلبة الثانوية في كافة الفروع') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;">
            <span class="stat-label">{{ __('المقررات والمحتوى الأكاديمي') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-amber">{{ number_format($data['total_files'] ?? 0) }}</span>
                <span class="stat-icon text-amber"><i class="fa-solid fa-book-open-reader"></i></span>
            </div>
            <small>{{ __('ملفات، ملازم ومصادر دراسية نشطة') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #4f46e5;">
            <span class="stat-label">{{ __('الجاهزية والأمان التقني') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo">99.9%</span>
                <span class="stat-icon text-indigo"><i class="fa-solid fa-shield-halved"></i></span>
            </div>
            <small>{{ __('حماية وتوافرية كاملة للبيانات') }}</small>
        </div>
    </div>

    <!-- 3. شبكة المحتوى الرئيسي الكلاسيكية -->
    <div class="ed-admin-grid">

        <!-- العمود الأيمن: أدوات الوصول السريع وإدارة الكادر والطلاب -->
        <div class="ed-admin-main-col">

            <!-- بطاقة عمليات الإدارة السريعة -->
            <div class="ed-card">
                <div class="ed-card-header">
                    <div class="ed-card-title">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>{{ __('مركز العمليات والوصول السريع') }}</span>
                    </div>
                    <span class="classic-card-tag">{{ __('إجراءات فورية') }}</span>
                </div>
                <div class="ed-quick-actions-grid">
                    <a href="{{ route('admin.subjects.pricing') }}" class="classic-action-card">
                        <div class="classic-action-icon-box" style="background: #eff6ff; color: #1e3a8a; border: 1.5px solid #bfdbfe;">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <div class="classic-action-text">
                            <span class="classic-action-title">{{ __('تسعير وباقات المواد') }}</span>
                            <small class="classic-action-desc">{{ __('إدارة خطط الاشتراك والعروض') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-left classic-action-arrow"></i>
                    </a>

                    <a href="{{ route('admin.payments.index') }}" class="classic-action-card">
                        <div class="classic-action-icon-box" style="background: #ecfdf5; color: #059669; border: 1.5px solid #a7f3d0;">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div class="classic-action-text">
                            <span class="classic-action-title">{{ __('الاشتراكات والمالية') }}</span>
                            <small class="classic-action-desc">{{ __('التحويلات وتدقيق المدفوعات') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-left classic-action-arrow"></i>
                    </a>

                    <a href="{{ route('admin.certificates.index') }}" class="classic-action-card">
                        <div class="classic-action-icon-box" style="background: #fffbeb; color: #d97706; border: 1.5px solid #fde68a;">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div class="classic-action-text">
                            <span class="classic-action-title">{{ __('إصدار الشهادات والنتائج') }}</span>
                            <small class="classic-action-desc">{{ __('شهادات التميز والإتمام الوزارية') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-left classic-action-arrow"></i>
                    </a>

                    <a href="{{ route('admin.teachers.create') }}" class="classic-action-card">
                        <div class="classic-action-icon-box" style="background: #f5f3ff; color: #7c3aed; border: 1.5px solid #ddd6fe;">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div class="classic-action-text">
                            <span class="classic-action-title">{{ __('إضافة معلم جديد') }}</span>
                            <small class="classic-action-desc">{{ __('تعيين الصلاحيات والمواد') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-left classic-action-arrow"></i>
                    </a>

                    <a href="{{ route('admin.students.create') }}" class="classic-action-card">
                        <div class="classic-action-icon-box" style="background: #e0f2fe; color: #0284c7; border: 1.5px solid #bae6fd;">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div class="classic-action-text">
                            <span class="classic-action-title">{{ __('تسجيل طالب جديد') }}</span>
                            <small class="classic-action-desc">{{ __('إدراج حساب طالب في الفروع') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-left classic-action-arrow"></i>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" class="classic-action-card">
                        <div class="classic-action-icon-box" style="background: #f1f5f9; color: #334155; border: 1.5px solid #cbd5e1;">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <div class="classic-action-text">
                            <span class="classic-action-title">{{ __('إعدادات المنصة') }}</span>
                            <small class="classic-action-desc">{{ __('هوية النظام، السيرفر والتحكم') }}</small>
                        </div>
                        <i class="fa-solid fa-chevron-left classic-action-arrow"></i>
                    </a>
                </div>
            </div>

            <!-- بطاقة إدارة الكادر والطلبة الكلاسيكية -->
            <div class="ed-card">
                <div class="ed-card-header">
                    <div class="ed-card-title">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>{{ __('إدارة القوى البشرية والأكاديمية') }}</span>
                    </div>
                </div>

                <div class="classic-groups-container">
                    <!-- مجموعة المعلمين -->
                    <div class="classic-group-item">
                        <div class="classic-group-info">
                            <div class="classic-group-avatar teacher">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <div class="classic-group-texts">
                                <strong>{{ __('الهيئة التدريسية والكادر التعليمي') }}</strong>
                                <p>{{ __('إدارة حسابات المدرسين، صلاحيات المواد، وقنوات التواصل المباشر') }}</p>
                            </div>
                        </div>
                        <div class="classic-group-actions">
                            <a href="{{ route('admin.teachers.create') }}" class="classic-btn outline">
                                <i class="fa-solid fa-plus"></i> {{ __('إضافة معلم') }}
                            </a>
                            <a href="{{ route('admin.teachers.index') }}" class="classic-btn primary">
                                <i class="fa-solid fa-list-check"></i> {{ __('عرض الكل') }}
                            </a>
                        </div>
                    </div>

                    <!-- مجموعة الطلبة -->
                    <div class="classic-group-item">
                        <div class="classic-group-info">
                            <div class="classic-group-avatar student">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <div class="classic-group-texts">
                                <strong>{{ __('قاعدة بيانات الطلبة والقبول') }}</strong>
                                <p>{{ __('متابعة الفروع الأكاديمية، سجلات الاشتراكات الشهرية، والنتائج الوزارية') }}</p>
                            </div>
                        </div>
                        <div class="classic-group-actions">
                            <a href="{{ route('admin.students.create') }}" class="classic-btn outline">
                                <i class="fa-solid fa-plus"></i> {{ __('تسجيل طالب') }}
                            </a>
                            <a href="{{ route('admin.students.index') }}" class="classic-btn primary">
                                <i class="fa-solid fa-list-check"></i> {{ __('عرض الكل') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- العمود الأيسر: حالة المنصة وسعة السيرفر -->
        <div class="ed-admin-side-col">

            <!-- بطاقة بوابة القبول والتسجيل -->
            <div class="ed-card">
                <div class="ed-card-header">
                    <div class="ed-card-title">
                        <i class="fa-solid fa-door-open"></i>
                        <span>{{ __('بوابة القبول والتسجيل') }}</span>
                    </div>
                    <span class="classic-badge-royal">{{ __('إشراف عام') }}</span>
                </div>

                <div class="classic-side-box">
                    <label class="classic-side-label">{{ __('حالة استقبال طلبات التسجيل الجديدة') }}</label>
                    @if(class_exists(\App\Models\Setting::class) && \App\Models\Setting::get('registration_status') == 'open')
                        <div class="classic-status-box open">
                            <div class="status-emblem-circle">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div>
                                <strong>{{ __('التسجيل الذاتي متاح حالياً') }}</strong>
                                <p>{{ __('يمكن للطلبة الجدد إنشاء حساباتهم ذاتياً') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="classic-status-box closed">
                            <div class="status-emblem-circle">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <div>
                                <strong>{{ __('التسجيل مغلق مؤقتاً') }}</strong>
                                <p>{{ __('إضافة الطلبة متاحة عبر لوحة الإدارة فقط') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="classic-side-box">
                    <div class="classic-box-flex">
                        <label class="classic-side-label">{{ __('سعة التخزين وحالة السيرفر') }}</label>
                        <span class="classic-pct-tag">82%</span>
                    </div>
                    <div class="classic-progress-track">
                        <div class="classic-progress-bar" style="width: 82%;"></div>
                    </div>
                    <span class="classic-usage-sub">{{ __('تم استهلاك 164 جيجابايت من إجمالي 200 جيجابايت') }}</span>
                </div>

                <div class="classic-audit-seal">
                    <i class="fa-solid fa-stamp"></i>
                    <p>{{ __('أنت تعمل بصلاحيات المشرف العام الكاملة. كافة العمليات الإدارية موثقة بسجل النظام.') }}</p>
                </div>
            </div>

            <!-- بطاقة قنوات الدعم والتواصل الأكاديمي -->
            <div class="ed-card">
                <div class="ed-card-header">
                    <div class="ed-card-title">
                        <i class="fa-solid fa-headset"></i>
                        <span>{{ __('قنوات التواصل والدعم الأكاديمي') }}</span>
                    </div>
                </div>
                <div class="classic-side-box" style="padding-bottom: 6px;">
                    <p style="font-size: 0.84rem; color: #475569; line-height: 1.6; margin-bottom: 16px;">{{ __('يتيح لك مركز المراسلات متابعة تذاكر واستفسارات الطلبة ورسائل الكادر التدريسي بشكل مباشر ولحظي.') }}</p>
                    <a href="{{ route('admin.settings.index') }}" class="classic-btn outline" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-sliders"></i> {{ __('تهيئة إعدادات المنصة') }}
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

<style>
    /* الحاوية الكلاسيكية المركزية */
    .ed-admin-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 0 0 60px;
        box-sizing: border-box;
    }

    /* 1. الترويسة الأكاديمية الكلاسيكية الملكية */
    .classic-header-banner {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #cbd5e1;
        border-top: 4px solid #1e3a8a;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .classic-banner-main {
        flex: 1;
        min-width: 280px;
    }

    .classic-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 10px;
    }

    .classic-breadcrumbs .divider {
        font-size: 0.65rem;
        color: #94a3b8;
    }

    .classic-breadcrumbs .active {
        color: #1e3a8a;
        font-weight: 700;
    }

    .classic-title-row {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .academic-crest-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        border: 2px solid #f59e0b;
        color: #ffffff;
        display: grid;
        place-items: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
    }

    .classic-main-heading {
        font-size: 1.45rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 4px;
        line-height: 1.2;
    }

    .classic-desc-text {
        font-size: 0.84rem;
        color: #64748b;
        margin: 0;
        line-height: 1.5;
        max-width: 780px;
    }

    .classic-banner-badges {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .classic-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .classic-status-pill.online {
        background: #ecfdf5;
        border: 1.5px solid #a7f3d0;
        color: #065f46;
    }

    .live-dot-pulse {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    }

    .classic-date-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #334155;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    /* 2. شبكة المخطط الأساسية */
    .ed-admin-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 22px;
        align-items: start;
    }

    .ed-admin-main-col,
    .ed-admin-side-col {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .classic-card-tag {
        font-size: 0.72rem;
        font-weight: 700;
        background: #eff6ff;
        color: #1e3a8a;
        padding: 3px 10px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
    }

    .classic-badge-royal {
        font-size: 0.72rem;
        font-weight: 700;
        background: #fffbeb;
        color: #b45309;
        padding: 3px 10px;
        border-radius: 6px;
        border: 1px solid #fde68a;
    }

    /* 3. شبكة إجراءات الوصول السريع الكلاسيكية */
    .ed-quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        padding: 20px;
    }

    .classic-action-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 14px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .classic-action-card:hover {
        transform: translateY(-2.5px);
        border-color: #1e3a8a;
        box-shadow: 0 8px 18px -3px rgba(30, 58, 138, 0.12);
        background: #ffffff;
    }

    .classic-action-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
        transition: transform 0.2s ease;
    }

    .classic-action-card:hover .classic-action-icon-box {
        transform: scale(1.08);
    }

    .classic-action-text {
        flex: 1;
        min-width: 0;
    }

    .classic-action-title {
        display: block;
        font-size: 0.88rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .classic-action-desc {
        display: block;
        font-size: 0.74rem;
        color: #64748b;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .classic-action-arrow {
        font-size: 0.75rem;
        color: #94a3b8;
        transition: transform 0.2s ease, color 0.2s ease;
    }

    .classic-action-card:hover .classic-action-arrow {
        color: #1e3a8a;
        transform: translateX(-3px);
    }

    html[dir="ltr"] .classic-action-card:hover .classic-action-arrow {
        transform: translateX(3px);
    }

    /* 4. حاوية مجموعات الكادر والطلبة */
    .classic-groups-container {
        padding: 16px 20px 20px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .classic-group-item {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .classic-group-info {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
        min-width: 240px;
    }

    .classic-group-avatar {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .classic-group-avatar.teacher {
        background: #eff6ff;
        color: #1e3a8a;
        border: 1.5px solid #bfdbfe;
    }

    .classic-group-avatar.student {
        background: #ecfdf5;
        color: #059669;
        border: 1.5px solid #a7f3d0;
    }

    .classic-group-texts strong {
        display: block;
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .classic-group-texts p {
        font-size: 0.78rem;
        color: #64748b;
        margin: 0;
        line-height: 1.4;
    }

    .classic-group-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* أزرار كلاسيكية مؤسسية */
    .classic-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 7px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .classic-btn.primary {
        background: #1e3a8a;
        color: #ffffff;
        border-color: #1e3a8a;
    }

    .classic-btn.primary:hover {
        background: #1e40af;
        box-shadow: 0 4px 10px rgba(30, 58, 138, 0.25);
    }

    .classic-btn.outline {
        background: #ffffff;
        color: #1e293b;
        border-color: #cbd5e1;
    }

    .classic-btn.outline:hover {
        background: #f8fafc;
        border-color: #1e3a8a;
        color: #1e3a8a;
    }

    /* 5. العمود الجانبي وتفاصيل القبول */
    .classic-side-box {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .classic-side-box:last-child {
        border-bottom: none;
    }

    .classic-side-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        display: block;
        margin-bottom: 10px;
    }

    .classic-status-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 9px;
    }

    .classic-status-box.open {
        background: #ecfdf5;
        border: 1.5px solid #a7f3d0;
        color: #065f46;
    }

    .classic-status-box.closed {
        background: #fef2f2;
        border: 1.5px solid #fecaca;
        color: #991b1b;
    }

    .status-emblem-circle {
        font-size: 1.35rem;
    }

    .classic-status-box strong {
        display: block;
        font-size: 0.88rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .classic-status-box p {
        margin: 2px 0 0;
        font-size: 0.76rem;
        opacity: 0.9;
    }

    .classic-box-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .classic-pct-tag {
        font-size: 0.78rem;
        font-weight: 800;
        background: #eff6ff;
        color: #1e3a8a;
        padding: 2px 8px;
        border-radius: 5px;
        border: 1px solid #bfdbfe;
    }

    .classic-progress-track {
        height: 9px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 8px;
    }

    .classic-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 999px;
    }

    .classic-usage-sub {
        font-size: 0.74rem;
        color: #64748b;
        font-weight: 600;
        display: block;
    }

    .classic-audit-seal {
        margin: 16px 20px 20px;
        padding: 12px 14px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: #475569;
        font-size: 0.78rem;
        line-height: 1.5;
    }

    .classic-audit-seal i {
        color: #d97706;
        font-size: 1.1rem;
        margin-top: 1px;
    }

    .classic-audit-seal p {
        margin: 0;
    }

    /* التجاوب (Responsive) */
    @media (max-width: 1100px) {
        .ed-quick-actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 960px) {
        .ed-admin-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .ed-quick-actions-grid {
            grid-template-columns: 1fr;
        }
        .classic-header-banner {
            padding: 16px;
        }
        .classic-main-heading {
            font-size: 1.25rem;
        }
    }
</style>
@endsection
