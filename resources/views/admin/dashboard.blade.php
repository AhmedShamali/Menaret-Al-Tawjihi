@extends('layouts.app')

@section('title', __('Central Admin Dashboard') . ' | ' . config('app.name'))

@section('content')
<div class="ed-admin-container">

    <!-- رأس لوحة الإدارة -->
    <header class="ed-admin-header">
        <div class="ed-admin-title-box">
            <div class="ed-admin-breadcrumbs">
                <i class="fas fa-home"></i>
                <span>{{ __('Home') }}</span>
                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} divider"></i>
                <span class="active">{{ __('Academic Control Panel') }}</span>
            </div>
            <h1>{{ __('Central Admin Dashboard') }}</h1>
            <p>{{ __('Welcome, here is your summary of performance indicators, academic staff management, and active platform operations.') }}</p>
        </div>

        <div class="ed-admin-status-wrap">
            <div class="ed-status-chip">
                <span class="ed-live-dot"></span>
                <span>{{ __('System: Online & Stable') }}</span>
            </div>
            <div class="ed-date-chip">
                <i class="far fa-calendar-alt"></i>
                <span>{{ now()->translatedFormat('l, j F Y') }}</span>
            </div>
        </div>
    </header>

    <!-- بطاقات المؤشرات الأكاديمية (KPIs) -->
    @php
        $kpis = [
            [
                'label' => __('Total Teachers'),
                'val' => $data['total_teachers'] ?? 0,
                'icon' => 'fas fa-chalkboard-teacher',
                'color' => '#1e3a8a',
                'bg' => '#eff6ff',
                'desc' => __('Certified Teaching Staff')
            ],
            [
                'label' => __('Registered Students'),
                'val' => $data['total_students'] ?? 0,
                'icon' => 'fas fa-user-graduate',
                'color' => '#059669',
                'bg' => '#ecfdf5',
                'desc' => __('Students across all branches')
            ],
            [
                'label' => __('Content & Files'),
                'val' => $data['total_files'] ?? 0,
                'icon' => 'fas fa-folder-open',
                'color' => '#d97706',
                'bg' => '#fffbeb',
                'desc' => __('Academic files & resources')
            ],
            [
                'label' => __('Server & Security Status'),
                'val' => '99.9%',
                'icon' => 'fas fa-shield-alt',
                'color' => '#475569',
                'bg' => '#f1f5f9',
                'desc' => __('Full Protection & High Availability')
            ],
        ];
    @endphp

    <div class="ed-kpi-grid">
        @foreach($kpis as $item)
        <div class="ed-kpi-card">
            <div class="ed-kpi-icon-box" style="background-color: {{ $item['bg'] }}; color: {{ $item['color'] }};">
                <i class="{{ $item['icon'] }}"></i>
            </div>
            <div class="ed-kpi-info">
                <span class="ed-kpi-title">{{ $item['label'] }}</span>
                <div class="ed-kpi-number">{{ is_numeric($item['val']) ? number_format($item['val']) : $item['val'] }}</div>
                <span class="ed-kpi-desc">{{ $item['desc'] }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- شبكة المحتوى الرئيسي -->
    <div class="ed-admin-grid">

        <!-- العمود الأيمن: أدوات الوصول السريع وإدارة القوى البشرية -->
        <div class="ed-admin-main-col">

            <!-- بطاقة إجراءات الإدارة السريعة -->
            <div class="ed-card">
                <div class="ed-card-header">
                    <div class="ed-card-title">
                        <i class="fas fa-th-large"></i>
                        <span>{{ __('Quick Actions & Access') }}</span>
                    </div>
                </div>
                <div class="ed-quick-actions-grid">
                    <a href="{{ route('admin.subjects.pricing') }}" class="ed-quick-btn">
                        <div class="ed-qb-icon" style="background: #eff6ff; color: #1e3a8a;">
                            <i class="fas fa-tags"></i>
                        </div>
                        <span class="ed-qb-title">{{ __('Subject Pricing') }}</span>
                        <small class="ed-qb-desc">{{ __('Manage Subscription Plans') }}</small>
                    </a>

                    <a href="{{ route('admin.payments.index') }}" class="ed-quick-btn">
                        <div class="ed-qb-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <span class="ed-qb-title">{{ __('Subscriptions & Finance') }}</span>
                        <small class="ed-qb-desc">{{ __('Transactions & Payments') }}</small>
                    </a>

                    <a href="{{ route('admin.certificates.index') }}" class="ed-quick-btn">
                        <div class="ed-qb-icon" style="background: #fffbeb; color: #d97706;">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <span class="ed-qb-title">{{ __('Issue Certificates') }}</span>
                        <small class="ed-qb-desc">{{ __('Honors & Completion Diplomas') }}</small>
                    </a>

                    <a href="{{ route('admin.teachers.create') }}" class="ed-quick-btn">
                        <div class="ed-qb-icon" style="background: #f5f3ff; color: #7c3aed;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <span class="ed-qb-title">{{ __('Add Teacher') }}</span>
                        <small class="ed-qb-desc">{{ __('Create & Assign Permissions') }}</small>
                    </a>

                    <a href="{{ route('admin.students.create') }}" class="ed-quick-btn">
                        <div class="ed-qb-icon" style="background: #e0f2fe; color: #0284c7;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <span class="ed-qb-title">{{ __('Add Student') }}</span>
                        <small class="ed-qb-desc">{{ __('Register New Student') }}</small>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" class="ed-quick-btn">
                        <div class="ed-qb-icon" style="background: #f1f5f9; color: #334155;">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span class="ed-qb-title">{{ __('Platform Settings') }}</span>
                        <small class="ed-qb-desc">{{ __('System & Brand Configurations') }}</small>
                    </a>
                </div>
            </div>

            <!-- بطاقة إدارة الكادر والطلبة -->
            <div class="ed-card">
                <div class="ed-card-header">
                    <div class="ed-card-title">
                        <i class="fas fa-users-cog"></i>
                        <span>{{ __('Users & Faculty Management') }}</span>
                    </div>
                </div>

                <div class="ed-user-groups">
                    <!-- مجموعة المعلمين -->
                    <div class="ed-group-item">
                        <div class="ed-gi-content">
                            <div class="ed-gi-avatar teacher">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="ed-gi-text">
                                <strong>{{ __('Teachers Faculty') }}</strong>
                                <p>{{ __('Manage accounts, subject permissions, and classes') }}</p>
                            </div>
                        </div>
                        <div class="ed-gi-actions">
                            <a href="{{ route('admin.teachers.create') }}" class="ed-btn ed-btn-outline" style="font-size: 0.8rem; padding: 6px 12px;">
                                <i class="fas fa-plus"></i> {{ __('Add') }}
                            </a>
                            <a href="{{ route('admin.teachers.index') }}" class="ed-btn ed-btn-primary" style="font-size: 0.8rem; padding: 6px 12px;">
                                {{ __('View All') }}
                            </a>
                        </div>
                    </div>

                    <!-- مجموعة الطلبة -->
                    <div class="ed-group-item">
                        <div class="ed-gi-content">
                            <div class="ed-gi-avatar student">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="ed-gi-text">
                                <strong>{{ __('Students Database') }}</strong>
                                <p>{{ __('Track academic branches, enrollments, and study progress') }}</p>
                            </div>
                        </div>
                        <div class="ed-gi-actions">
                            <a href="{{ route('admin.students.create') }}" class="ed-btn ed-btn-outline" style="font-size: 0.8rem; padding: 6px 12px;">
                                <i class="fas fa-plus"></i> {{ __('Add') }}
                            </a>
                            <a href="{{ route('admin.students.index') }}" class="ed-btn ed-btn-primary" style="font-size: 0.8rem; padding: 6px 12px;">
                                {{ __('View All') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- العمود الأيسر: حالة المنصة وسعة التخزين -->
        <div class="ed-admin-side-col">

            <!-- بطاقة بوابة النظام -->
            <div class="ed-card">
                <div class="ed-card-header">
                    <div class="ed-card-title">
                        <i class="fas fa-sliders-h"></i>
                        <span>{{ __('System Control & Admissions') }}</span>
                    </div>
                    <span class="ed-badge ed-badge-blue">{{ __('Super Admin') }}</span>
                </div>

                <div class="ed-side-section">
                    <label class="ed-side-label">{{ __('New Student Registration Status') }}</label>
                    @if(class_exists(\App\Models\Setting::class) && \App\Models\Setting::get('registration_status') == 'open')
                        <div class="ed-status-indicator active">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>{{ __('Registration is Open') }}</strong>
                                <p>{{ __('New students can self-register online') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="ed-status-indicator inactive">
                            <i class="fas fa-lock"></i>
                            <div>
                                <strong>{{ __('Registration Temporarily Closed') }}</strong>
                                <p>{{ __('Registration managed via admin panel only') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="ed-side-section">
                    <div class="ed-side-flex-label">
                        <label class="ed-side-label">{{ __('Storage & Server Capacity') }}</label>
                        <span class="ed-usage-pct">82%</span>
                    </div>
                    <div class="ed-progress-track">
                        <div class="ed-progress-bar" style="width: 82%;"></div>
                    </div>
                    <span class="ed-usage-info">{{ __('164 GB used of 200 GB total') }}</span>
                </div>

                <div class="ed-side-note">
                    <i class="fas fa-info-circle"></i>
                    <p>{{ __('Operating with General Supervisor permissions. All changes are logged in system audit.') }}</p>
                </div>
            </div>

            <!-- بطاقة الدعم الفني والمراسلات -->
            <div class="ed-card">
                <div class="ed-card-header">
                    <div class="ed-card-title">
                        <i class="fas fa-headset"></i>
                        <span>{{ __('Support & Inquiries Channels') }}</span>
                    </div>
                </div>
                <div class="ed-support-summary">
                    <p>{{ __('Communications center allows you to track student and teacher inquiries directly.') }}</p>
                    <a href="{{ route('admin.settings.index') }}" class="ed-btn ed-btn-outline" style="width: 100%; justify-content: center;">
                        <i class="fas fa-sliders-h"></i> {{ __('Configure Platform Settings') }}
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

<style>
    .ed-admin-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 0 0 60px;
        box-sizing: border-box;
    }

    /* Header */
    .ed-admin-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .ed-admin-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 8px;
    }

    .ed-admin-breadcrumbs .divider {
        font-size: 0.65rem;
        color: #cbd5e1;
    }

    .ed-admin-breadcrumbs .active {
        color: #1e3a8a;
        font-weight: 600;
    }

    .ed-admin-title-box h1 {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .ed-admin-title-box p {
        font-size: 0.88rem;
        color: #64748b;
        margin: 0;
        line-height: 1.6;
        max-width: 780px;
    }

    .ed-admin-status-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ed-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ecfdf5;
        border: 1px solid #d1fae5;
        color: #065f46;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .ed-live-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    }

    .ed-date-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    /* KPI Grid */
    .ed-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 28px;
    }

    .ed-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .ed-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.05);
    }

    .ed-kpi-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .ed-kpi-info {
        flex: 1;
        min-width: 0;
    }

    .ed-kpi-title {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 4px;
    }

    .ed-kpi-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .ed-kpi-desc {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    /* Grid Layout */
    .ed-admin-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .ed-admin-main-col,
    .ed-admin-side-col {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Cards */
    .ed-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 22px 24px;
    }

    .ed-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    .ed-card-title i {
        color: #1e3a8a;
    }

    /* Quick Actions */
    .ed-quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .ed-quick-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 18px 12px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .ed-quick-btn:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.05);
    }

    .ed-qb-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 10px;
    }

    .ed-qb-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .ed-qb-desc {
        font-size: 0.75rem;
        color: #64748b;
    }

    /* User Groups */
    .ed-user-groups {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .ed-group-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .ed-gi-content {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .ed-gi-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .ed-gi-avatar.teacher {
        background: #eff6ff;
        color: #1e3a8a;
    }

    .ed-gi-avatar.student {
        background: #ecfdf5;
        color: #059669;
    }

    .ed-gi-text strong {
        display: block;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .ed-gi-text p {
        margin: 0;
        font-size: 0.8rem;
        color: #64748b;
    }

    .ed-gi-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ed-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 10px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .ed-btn-primary {
        background: #1e3a8a;
        color: #ffffff;
    }

    .ed-btn-primary:hover {
        background: #172554;
    }

    .ed-btn-outline {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    .ed-btn-outline:hover {
        border-color: #1e3a8a;
        color: #1e3a8a;
    }

    /* Side Column Items */
    .ed-side-section {
        margin-bottom: 20px;
    }

    .ed-side-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
    }

    .ed-side-flex-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .ed-usage-pct {
        font-size: 0.85rem;
        font-weight: 800;
        color: #1e3a8a;
    }

    .ed-progress-track {
        height: 7px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 6px;
    }

    .ed-progress-bar {
        height: 100%;
        background: #1e3a8a;
        border-radius: 999px;
    }

    .ed-usage-info {
        font-size: 0.75rem;
        color: #94a3b8;
        display: block;
    }

    .ed-status-indicator {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px;
        border-radius: 12px;
    }

    .ed-status-indicator.active {
        background: #ecfdf5;
        border: 1px solid #d1fae5;
        color: #065f46;
    }

    .ed-status-indicator.inactive {
        background: #fef2f2;
        border: 1px solid #fee2e2;
        color: #991b1b;
    }

    .ed-status-indicator i {
        font-size: 1.25rem;
        margin-top: 2px;
    }

    .ed-status-indicator strong {
        display: block;
        font-size: 0.88rem;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .ed-status-indicator p {
        margin: 0;
        font-size: 0.78rem;
        opacity: 0.9;
    }

    .ed-side-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 12px;
        margin-top: 20px;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.5;
    }

    .ed-side-note i {
        color: #94a3b8;
        font-size: 0.9rem;
        margin-top: 2px;
    }

    .ed-side-note p {
        margin: 0;
    }

    .ed-support-summary p {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .ed-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .ed-badge-blue { background: #eff6ff; color: #1e3a8a; }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .ed-kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .ed-admin-grid {
            grid-template-columns: 1fr;
        }
        .ed-quick-actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .ed-kpi-grid {
            grid-template-columns: 1fr;
        }
        .ed-quick-actions-grid {
            grid-template-columns: 1fr;
        }
        .ed-group-item {
            flex-direction: column;
            align-items: flex-start;
        }
        .ed-gi-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>
@endsection
