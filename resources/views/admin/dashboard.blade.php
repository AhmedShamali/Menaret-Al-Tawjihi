@extends('layouts.app')

@section('title', 'لوحة التحكم الاحترافية')

@section('content')
<!-- استيراد الخطوط والأيقونات -->
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

<div class="admin-wrapper">

    {{-- الجزء العلوي: الترحيب --}}
    <header class="main-header">
        <div class="greet-box">
            <div class="date-chip">
                <span class="material-icons-round">calendar_today</span>
                {{ now()->translatedFormat('l, j F Y') }}
            </div>
            <h1>مرحباً، <span class="gradient-text">سيادة المدير</span> <span class="wave-emoji">👋</span></h1>
            <p>إليك ملخص سريع لأداء المنصة وما يتطلب انتباهك اليوم.</p>
        </div>

        <div class="header-actions">
            <div class="server-status">
                <div class="pulse-indicator"></div>
                <span>حالة الخادم: متصل</span>
            </div>
        </div>
    </header>

    {{-- الإحصائيات الرئيسية --}}
    <div class="kpi-grid">
        @php
            $kpis = [
                ['label' => 'المدرسين', 'val' => $data['total_teachers'] ?? 0, 'icon' => 'person_4', 'color' => '#4f46e5', 'trend' => 'كادر متميز'],
                ['label' => 'الطلاب المسجلين', 'val' => $data['total_students'] ?? 0, 'icon' => 'school', 'color' => '#10b981', 'trend' => 'نمو مستمر'],
                ['label' => 'المحتوى الرقمي', 'val' => $data['total_files'] ?? 0, 'icon' => 'inventory_2', 'color' => '#f59e0b', 'trend' => 'ملف تعليمي'],
                ['label' => 'استقرار النظام', 'val' => '99.9%', 'icon' => 'security', 'color' => '#6366f1', 'trend' => 'آمن ومستقر'],
            ];
        @endphp

        @foreach($kpis as $item)
        <div class="kpi-card">
            <div class="kpi-icon" style="background-color: {{ $item['color'] }}15; color: {{ $item['color'] }};">
                <span class="material-icons-round">{{ $item['icon'] }}</span>
            </div>
            <div class="kpi-data">
                <span class="kpi-label">{{ $item['label'] }}</span>
                <h2 class="kpi-value">{{ is_numeric($item['val']) ? number_format($item['val']) : $item['val'] }}</h2>
                <span class="kpi-trend">{{ $item['trend'] }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="dashboard-layout">
        {{-- الجانب الأيمن: الإدارة السريعة --}}
        <div class="main-column">
            <div class="section-card shadow-sm">
                <div class="section-header">
                    <h3><span class="material-icons-round">bolt</span> وصول سريع</h3>
                </div>
                <div class="quick-grid">
                    <a href="{{ route('admin.teachers.create') }}" class="q-link">
                        <div class="q-icon purple"><span class="material-icons-round">add_reaction</span></div>
                        <span>إضافة مدرس</span>
                    </a>
                    {{-- تم تصحيح مسار إضافة الطالب ليتطابق مع المعيار (create) --}}
                    <a href="{{ route('admin.students.create') }}" class="q-link">
                        <div class="q-icon green"><span class="material-icons-round">group_add</span></div>
                        <span>إضافة طالب</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="q-link">
                        <div class="q-icon blue"><span class="material-icons-round">settings_suggest</span></div>
                        <span>الإعدادات</span>
                    </a>
                    <a href="#" class="q-link">
                        <div class="q-icon orange"><span class="material-icons-round">analytics</span></div>
                        <span>التقارير</span>
                    </a>
                </div>
            </div>

            <div class="section-card mt-4">
                <div class="section-header">
                    <h3><span class="material-icons-round">manage_accounts</span> إدارة القوى البشرية</h3>
                </div>
                <div class="user-list">
                    <div class="user-item">
                        <div class="u-info">
                            <div class="u-avatar blue">T</div>
                            <div>
                                <h4>المعلمين</h4>
                                <p>إدارة الحسابات، الصلاحيات، والتقارير</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.teachers.index') }}" class="btn-outline">عرض الكل</a>
                    </div>
                    <div class="user-item">
                        <div class="u-info">
                            <div class="u-avatar green">S</div>
                            <div>
                                <h4>الطلاب</h4>
                                <p>متابعة التسجيل والمستويات الدراسية</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.students.index') }}" class="btn-outline">عرض الكل</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- الجانب الأيسر: حالة النظام --}}
        <div class="side-column">
            <div class="monitor-card">
                <div class="m-header">
                    <h4>بوابة التحكم</h4>
                    <span class="live-tag">LIVE</span>
                </div>

                <div class="status-box">
                    <p>حالة التسجيل</p>
                    @if(class_exists(\App\Models\Setting::class) && \App\Models\Setting::get('registration_status') == 'open')
                        <div class="status-badge success">مفتوح للمنتسبين</div>
                    @else
                        <div class="status-badge danger">مغلق مؤقتاً</div>
                    @endif
                </div>

                <div class="storage-box">
                    <div class="storage-info">
                        <span>سعة التخزين</span>
                        <span>82%</span>
                    </div>
                    <div class="progress-container">
                        <div class="progress-fill" style="width: 82%"></div>
                    </div>
                    <small>تم استهلاك 164GB من أصل 200GB</small>
                </div>

                <div class="system-footer">
                    <p><span class="material-icons-round">info</span> أنت في لوحة الإدارة العليا. جميع العمليات يتم تسجيلها في سجل النظام.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #6366f1;
        --bg-body: #f1f5f9;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --white: #ffffff;
        --radius: 16px;
    }

    .admin-wrapper {
        font-family: 'Tajawal', sans-serif;
        background: var(--bg-body);
        padding: 2rem;
        direction: rtl;
        min-height: 100vh;
    }

    /* Header Design */
    .main-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 2.5rem;
    }
    .date-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #e2e8f0;
        padding: 6px 14px;
        border-radius: 100px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }
    .main-header h1 {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }
    .gradient-text {
        background: linear-gradient(135deg, #4f46e5, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .server-status {
        background: white;
        padding: 10px 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .pulse-indicator {
        width: 10px; height: 10px; background: #10b981; border-radius: 50%;
        animation: pulse-animation 2s infinite;
    }

    /* KPI Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    .kpi-card {
        background: var(--white);
        padding: 1.5rem;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        gap: 1.2rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.3s ease;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
    }
    .kpi-icon {
        width: 56px; height: 56px; border-radius: 14px;
        display: grid; place-items: center; font-size: 1.8rem;
    }
    .kpi-label { color: var(--text-muted); font-size: 0.9rem; font-weight: 600; }
    .kpi-value { font-size: 1.6rem; font-weight: 800; margin: 2px 0; color: var(--text-main); }
    .kpi-trend { font-size: 0.75rem; color: var(--text-muted); }

    /* Layout Columns */
    .dashboard-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    .section-card {
        background: var(--white);
        border-radius: var(--radius);
        padding: 1.8rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .section-header h3 {
        display: flex; align-items: center; gap: 10px;
        font-size: 1.2rem; font-weight: 700; color: var(--text-main);
        margin-bottom: 1.5rem;
    }

    /* Quick Links */
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
    }
    .q-link {
        text-decoration: none; text-align: center;
        padding: 1rem; border-radius: 12px;
        background: #f8fafc; transition: 0.2s;
    }
    .q-link:hover { background: #f1f5f9; transform: scale(1.02); }
    .q-icon {
        width: 45px; height: 45px; margin: 0 auto 10px;
        border-radius: 10px; display: grid; place-items: center; color: white;
    }
    .q-icon.purple { background: #8b5cf6; }
    .q-icon.green { background: #10b981; }
    .q-icon.blue { background: #3b82f6; }
    .q-icon.orange { background: #f59e0b; }
    .q-link span { font-size: 0.85rem; font-weight: 700; color: var(--text-main); }

    /* User List */
    .user-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 1rem; border-radius: 12px; border: 1px solid #f1f5f9;
        margin-bottom: 10px;
    }
    .u-info { display: flex; align-items: center; gap: 12px; }
    .u-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        display: grid; place-items: center; color: white; font-weight: bold;
    }
    .u-avatar.blue { background: var(--primary); }
    .u-avatar.green { background: #10b981; }
    .u-info h4 { margin: 0; font-size: 0.95rem; font-weight: 700; }
    .u-info p { margin: 0; font-size: 0.8rem; color: var(--text-muted); }
    .btn-outline {
        padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 8px;
        text-decoration: none; color: var(--text-main); font-size: 0.8rem; font-weight: 600;
        transition: 0.2s;
    }
    .btn-outline:hover { background: var(--text-main); color: white; }

    /* Side Column / Monitor */
    .monitor-card {
        background: #1e293b;
        color: white;
        padding: 1.8rem;
        border-radius: var(--radius);
        position: sticky; top: 20px;
    }
    .m-header { display: flex; justify-content: space-between; margin-bottom: 2rem; }
    .live-tag {
        background: rgba(239, 68, 68, 0.2); color: #f87171;
        padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 900;
        border: 1px solid #f87171;
    }
    .status-badge {
        padding: 10px; border-radius: 10px; text-align: center; font-weight: 700; margin-top: 8px;
    }
    .status-badge.success { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .status-badge.danger { background: rgba(239, 68, 68, 0.15); color: #f87171; }

    .storage-box { margin: 2rem 0; }
    .storage-info { display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 8px; }
    .progress-container { height: 8px; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, #4f46e5, #818cf8); border-radius: 10px; }
    .storage-box small { font-size: 0.7rem; color: #94a3b8; display: block; margin-top: 8px; }

    .system-footer { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); }
    .system-footer p { font-size: 0.75rem; color: #94a3b8; line-height: 1.6; display: flex; gap: 8px; }

    /* Animations */
    @keyframes pulse-animation {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .wave-emoji { display: inline-block; animation: wave 2s infinite; transform-origin: 70% 70%; }
    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        20% { transform: rotate(-10deg); }
        40% { transform: rotate(10deg); }
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .dashboard-layout { grid-template-columns: 1fr; }
        .main-header { flex-direction: column; align-items: flex-start; }
    }
</style>
@endsection
