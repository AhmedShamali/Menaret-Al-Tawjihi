@extends('layouts.app')

@section('title', 'بث النشاطات الحي')

@section('content')
<div class="live-activity-container">

    <!-- Header Section -->
    <div class="header-flex">
        <div class="title-side">
            <h1 class="main-title">نبض المنصة المباشر 📡</h1>
            <p class="sub-title">مراقبة حية لكل العمليات التي تتم عبر {{ \App\Models\Setting::get('site_name') }}.</p>
        </div>
        <div class="status-side">
            <div class="status-badge">
                <span class="pulse-dot"></span>
                متصل الآن
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card table-wrapper">
        <table class="responsive-table">
            <thead>
                <tr>
                    <th>المستخدم</th>
                    <th>العملية</th>
                    <th class="hide-mobile">الوقت</th>
                    <th style="text-align: center;">الجهاز</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $act)
                <tr>
                    <td data-label="المستخدم">
                        <div class="user-info">
                            <div class="user-avatar">👤</div>
                            <div class="user-details">
                                <span class="user-name">{{ $act->student->name_ar ?? 'مدير النظام' }}</span>
                                <span class="user-ip">{{ $act->ip_address }}</span>
                            </div>
                        </div>
                    </td>
                    <td data-label="العملية">
                        <div class="activity-info">
                            <span class="chip">{{ $act->type }}</span>
                            <span class="activity-desc">{{ $act->description }}</span>
                        </div>
                    </td>
                    <td data-label="الوقت" class="time-cell hide-mobile">
                        {{ $act->created_at->diffForHumans() }}
                    </td>
                    <td data-label="الجهاز" style="text-align: center; font-size: 1.2rem;">
                        {{ str_contains($act->user_agent, 'Mobile') ? '📱' : '💻' }}
                        <div class="mobile-only-time">{{ $act->created_at->diffForHumans() }}</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    /* المتغيرات الأساسية */
    :root {
        --primary-color: var(--primary, #1e293b);
        --accent-color: var(--accent, #10b981);
        --text-main: #1e293b;
        --text-muted: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.9);
    }

    .live-activity-container {
        display: flex;
        flex-direction: column;
        gap: 30px;
        animation: fadeIn 0.6s ease;
        padding: 10px;
    }

    /* الهيدر المتجاوب */
    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .main-title {
        font-size: clamp(1.5rem, 5vw, 2.2rem);
        font-weight: 900;
        color: var(--primary-color);
        margin: 0;
    }

    .sub-title {
        color: var(--text-muted);
        margin-top: 5px;
        font-size: 0.95rem;
    }

    /* بادج الحالة */
    .status-badge {
        background: white;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--accent-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    /* تصميم الجدول */
    .table-wrapper {
        background: var(--glass-bg);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid rgba(255,255,255,0.3);
    }

    .responsive-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .responsive-table thead tr {
        background: var(--primary-color);
        color: white;
    }

    .responsive-table th {
        padding: 20px;
        font-weight: 600;
    }

    .responsive-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        transition: 0.3s;
    }

    .responsive-table tbody tr:hover {
        background: #f8fafc;
    }

    /* تفاصيل المستخدم داخل الجدول */
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #f1f5f9;
        display: grid;
        place-items: center;
        font-size: 1.2rem;
    }

    .user-name {
        display: block;
        font-weight: 800;
        color: var(--primary-color);
        font-size: 0.9rem;
    }

    .user-ip {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    /* الأنشطة */
    .activity-info {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .chip {
        background: #eff6ff;
        color: #2563eb;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
    }

    .activity-desc {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
    }

    .time-cell {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .mobile-only-time {
        display: none;
        font-size: 0.65rem;
        color: var(--text-muted);
        margin-top: 5px;
    }

    /* الأنميشن */
    @keyframes pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.5); opacity: 0.4; } 100% { transform: scale(1); opacity: 1; } }
    .pulse-dot { width: 8px; height: 8px; background: var(--accent-color); border-radius: 50%; animation: pulse 1.5s infinite; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Media Queries للموبايل */
    @media (max-width: 768px) {
        .header-flex {
            flex-direction: column;
            align-items: flex-start;
        }

        .status-side {
            width: 100%;
        }

        .hide-mobile {
            display: none;
        }

        .mobile-only-time {
            display: block;
        }

        .responsive-table td {
            padding: 12px 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }

        .activity-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
    }

    /* للأجهزة الصغيرة جداً (أقل من 480px) */
    @media (max-width: 480px) {
        .responsive-table thead {
            display: none; /* إخفاء الهيدر في الموبايل الصغير جداً */
        }
        
        .responsive-table tr {
            display: block;
            border-bottom: 2px solid #f1f5f9;
            padding: 10px 0;
        }

        .responsive-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            padding: 8px 15px;
        }

        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 800;
            color: var(--text-muted);
            font-size: 0.75rem;
        }
        
        .user-info, .activity-info {
            text-align: left;
            align-items: flex-end;
        }
    }
</style>
@endsection