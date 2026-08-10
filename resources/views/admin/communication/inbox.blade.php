@extends('layouts.app')

@section('title', 'بث النشاطات الحي')

@section('content')
<div class="live-stream-wrapper">

    <!-- Header Section -->
    <div class="header-container">
        <div class="text-side">
            <h1 class="main-title">نبض المنصة المباشر 📡</h1>
            <p class="description">مراقبة حية لكل العمليات التي تتم عبر {{ \App\Models\Setting::get('site_name') }}.</p>
        </div>
        <div class="status-side">
            <div class="live-badge">
                <span class="pulse-dot"></span>
                <span class="badge-text">متصل الآن</span>
            </div>
        </div>
    </div>

    <!-- table Section -->
    <div class="glass-card table-responsive-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>المستخدم</th>
                    <th>العملية</th>
                    <th class="hide-on-mobile">الوقت</th>
                    <th style="text-align: center;">الجهاز</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $act)
                <tr class="table-row">
                    <td data-label="المستخدم">
                        <div class="user-cell">
                            <div class="avatar-box">👤</div>
                            <div class="user-data">
                                <span class="name">{{ $act->student->name_ar ?? 'مدير النظام' }}</span>
                                <span class="ip">{{ $act->ip_address }}</span>
                            </div>
                        </div>
                    </td>
                    <td data-label="العملية">
                        <div class="process-cell">
                            <span class="type-badge">{{ $act->type }}</span>
                            <span class="desc">{{ $act->description }}</span>
                        </div>
                    </td>
                    <td data-label="الوقت" class="time-cell hide-on-mobile">
                        {{ $act->created_at->diffForHumans() }}
                    </td>
                    <td data-label="الجهاز" class="device-cell">
                        <span class="device-icon">{{ str_contains($act->user_agent, 'Mobile') ? '📱' : '💻' }}</span>
                        <span class="mobile-time">{{ $act->created_at->diffForHumans() }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    /* المتغيرات الأساسية لسهولة التعديل */
    :root {
        --primary-color: var(--primary, #1e293b);
        --accent-color: var(--accent, #6366f1);
        --glass-white: rgba(255, 255, 255, 0.95);
        --border-color: #f1f5f9;
    }

    .live-stream-wrapper {
        display: flex;
        flex-direction: column;
        gap: 30px;
        animation: fadeIn 0.6s ease;
        max-width: 100%;
        padding: 10px;
    }

    /* الهيدر */
    .header-container {
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

    .description {
        color: #64748b;
        font-size: 0.95rem;
        margin-top: 5px;
    }

    .live-badge {
        background: white;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--accent-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    /* الجدول والبطاقة */
    .table-responsive-wrapper {
        background: var(--glass-white);
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .custom-table thead tr {
        background: var(--primary-color);
        color: white;
    }

    .custom-table th {
        padding: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .custom-table td {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-row:hover { background: #fcfcfd; }

    /* تفاصيل الخلايا */
    .user-cell { display: flex; align-items: center; gap: 15px; }
    .avatar-box { width: 40px; height: 40px; border-radius: 12px; background: #f1f5f9; display: grid; place-items: center; font-size: 1.2rem; }
    .name { display: block; font-weight: 800; color: var(--primary-color); font-size: 0.9rem; }
    .ip { font-size: 0.7rem; color: #94a3b8; }

    .process-cell { display: flex; align-items: center; gap: 10px; }
    .type-badge { background: #eff6ff; color: #2563eb; font-weight: 800; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; }
    .desc { font-weight: 600; color: #475569; font-size: 0.85rem; }

    .time-cell { font-size: 0.8rem; color: #94a3b8; font-weight: 600; }
    
    .mobile-time { display: none; }

    /* الأنميشن */
    @keyframes pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.5); opacity: 0.4; } 100% { transform: scale(1); opacity: 1; } }
    .pulse-dot { width: 8px; height: 8px; background: var(--accent-color); border-radius: 50%; animation: pulse 1.5s infinite; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Media Queries لضمان التجاوب */

    /* التابلت */
    @media (max-width: 992px) {
        .custom-table th, .custom-table td { padding: 12px 15px; }
    }

    /* الموبايل */
    @media (max-width: 768px) {
        .header-container { flex-direction: column; align-items: flex-start; gap: 15px; }
        
        .hide-on-mobile { display: none; } /* إخفاء عمود الوقت المنفصل */
        .mobile-time { display: block; font-size: 0.7rem; color: #94a3b8; margin-top: 4px; }
        
        .device-cell { text-align: center; }
        .device-icon { font-size: 1.2rem; }
        
        .process-cell { flex-direction: column; align-items: flex-start; gap: 5px; }
    }

    /* الموبايل الصغير جداً (تحويل الجدول لبطاقات) */
    @media (max-width: 500px) {
        .custom-table thead { display: none; } /* إخفاء الرأس تماماً */
        
        .table-row {
            display: block;
            border-bottom: 8px solid #f8fafc;
            padding: 15px 5px;
        }
        
        .custom-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            padding: 8px 15px;
            width: 100%;
            box-sizing: border-box;
        }

        .custom-table td::before {
            content: attr(data-label); /* جلب اسم العمود */
            font-weight: 800;
            color: #94a3b8;
            font-size: 0.75rem;
        }
        
        .user-cell, .process-cell { justify-content: flex-end; width: 100%; text-align: left; }
    }
</style>
@endsection