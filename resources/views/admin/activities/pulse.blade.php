@extends('layouts.app')

@section('content')
<div class="monitor-container">
    <!-- Header Section -->
    <div class="monitor-header">
        <div class="title-area">
            <h1 class="main-title">نبض المنصة 🛡️</h1>
            <p class="sub-title">مراقبة حية للعمليات الأمنية والأكاديمية.</p>
        </div>
        <div class="status-badge">
            <span class="dot"></span> 
            <span class="badge-text">البث المباشر مفعل</span>
        </div>
    </div>

    <!-- Activity Table/Cards -->
    <div class="glass-card main-wrapper">
        <table class="live-table">
            <thead>
                <tr>
                    <th>المستخدم</th>
                    <th>النشاط</th>
                    <th class="hide-mobile">الجهاز/IP</th>
                    <th>التوقيت</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $act)
                <tr class="pulse-row {{ $act->severity }}">
                    <td data-label="المستخدم">
                        <div class="user-block">
                            <div class="user-avatar">👤</div>
                            <div class="user-info-text">
                                <strong class="u-name">{{ $act->student->name_ar ?? 'النظام' }}</strong>
                                <span class="ip-mobile-only">{{ $act->ip_address }}</span>
                            </div>
                        </div>
                    </td>
                    <td data-label="النشاط">
                        <div class="activity-content">
                            <span class="type-tag">{{ $act->type }}</span>
                            <span class="activity-desc">{{ $act->description }}</span>
                        </div>
                    </td>
                    <td data-label="الجهاز" class="hide-mobile">
                        <div class="device-info">
                            <span class="ip-addr">{{ $act->ip_address }}</span>
                            <span class="device-type">
                                {!! str_contains($act->user_agent, 'Mobile') ? '📱 Mobile' : '💻 Desktop' !!}
                            </span>
                        </div>
                    </td>
                    <td data-label="التوقيت" class="time-cell">
                        <span class="time-text">{{ $act->created_at->diffForHumans() }}</span>
                        <div class="device-mobile-icon">
                             {{ str_contains($act->user_agent, 'Mobile') ? '📱' : '💻' }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    :root {
        --primary: #1e293b;
        --danger: #ef4444;
        --warning: #f59e0b;
        --success: #10b981;
        --bg-glass: rgba(255, 255, 255, 0.9);
    }

    .monitor-container {
        display: flex;
        flex-direction: column;
        gap: 25px;
        animation: fadeIn 0.8s ease;
        padding: 15px;
    }

    /* Header */
    .monitor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .main-title { font-size: clamp(1.6rem, 4vw, 2.2rem); font-weight: 900; color: var(--primary); margin: 0; }
    .sub-title { color: #64748b; margin: 5px 0 0 0; font-size: 0.95rem; }

    .status-badge {
        background: #ecfdf5;
        color: #059669;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 10px rgba(16, 185, 129, 0.1);
    }

    .dot { width: 10px; height: 10px; background: var(--success); border-radius: 50%; animation: blink 1s infinite; }

    /* Table Styles */
    .main-wrapper {
        border-radius: 16px;
        overflow: hidden;
        background: var(--bg-glass);
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
    }

    .live-table { width: 100%; border-collapse: collapse; text-align: right; }
    .live-table thead tr { background: var(--primary); color: white; }
    .live-table th { padding: 18px 20px; font-weight: 600; font-size: 0.9rem; }
    .live-table td { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    /* Rows Severity */
    .pulse-row { transition: 0.2s; }
    .pulse-row.danger { background: #fef2f2; border-right: 4px solid var(--danger); }
    .pulse-row.warning { background: #fffbeb; border-right: 4px solid var(--warning); }
    .pulse-row:hover { background: #f8fafc; }

    /* User Info */
    .user-block { display: flex; align-items: center; gap: 12px; }
    .user-avatar { width: 35px; height: 35px; background: #e2e8f0; border-radius: 50%; display: grid; place-items: center; }
    .u-name { color: var(--primary); font-size: 0.9rem; }
    .ip-mobile-only { display: none; font-size: 0.7rem; color: #94a3b8; }

    /* Tags */
    .type-tag { 
        padding: 4px 10px; 
        background: #f1f5f9; 
        border-radius: 6px; 
        font-size: 0.7rem; 
        font-weight: 800; 
        margin-left: 8px;
        color: #475569;
        display: inline-block;
    }
    .activity-desc { font-size: 0.9rem; color: #334155; }

    /* Device Info */
    .ip-addr { display: block; font-size: 0.75rem; color: #94a3b8; font-family: monospace; }
    .device-type { font-size: 0.8rem; color: #64748b; }
    .device-mobile-icon { display: none; }

    /* Animations */
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; } }

    /* Responsive Media Queries */
    @media (max-width: 992px) {
        .hide-mobile { display: none; }
        .ip-mobile-only { display: block; }
        .device-mobile-icon { display: block; font-size: 1rem; margin-top: 4px; }
    }

    @media (max-width: 600px) {
        .monitor-header { flex-direction: column; align-items: flex-start; }
        
        .live-table thead { display: none; } /* إخفاء رأس الجدول في الجوال */
        
        .live-table tr { 
            display: block; 
            margin-bottom: 15px; 
            border: 1px solid #e2e8f0; 
            border-radius: 12px;
            padding: 10px;
        }

        .live-table td { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border: none; 
            padding: 8px 10px;
            text-align: left;
        }

        .live-table td::before {
            content: attr(data-label);
            font-weight: 800;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .user-block, .activity-content { width: 100%; justify-content: flex-end; text-align: left; }
        .pulse-row.danger, .pulse-row.warning { border-right: none; border-left: 5px solid; }
        .pulse-row.danger { border-left-color: var(--danger); }
        .pulse-row.warning { border-left-color: var(--warning); }
    }
</style>
@endsection