@extends('layouts.app')

@section('title', __('لوحة الشرف وتحدي الأوائل') . ' | ' . __('منارة التوجيهي'))

@section('content')
<div class="ed-leaderboard-container">

    <!-- Header -->
    <header class="ed-lb-header">
        <div class="ed-lb-title-box">
            <div class="ed-lb-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('student.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('لوحة الطالب') }}</a>
                <i class="fas fa-chevron-left divider"></i>
                <span class="active">{{ __('لوحة الشرف') }}</span>
            </div>
            <h1>{{ __('لوحة الشرف وتحدي أوائل الطلبة') }}</h1>
            <p>{{ __('الترتيب الأكاديمي للطلبة الأكثر التزاماً وحضوراً وحلاً للاختبارات على المنصة لهذا العام.') }}</p>
        </div>

        <div class="ed-lb-badge">
            <i class="fas fa-medal"></i>
            <span>{{ __('لوحة المتفوقين في فلسطين 🇵🇸') }}</span>
        </div>
    </header>

    @if($topStudents->count() >= 3)
        <!-- منصة التتويج الأكاديمية (Top 3 Podium) -->
        <div class="ed-podium-grid">
            
            <!-- المركز الثاني -->
            <div class="ed-podium-card rank-2">
                <div class="ed-rank-tag silver">
                    <i class="fas fa-award"></i> {{ __('المركز الثاني') }}
                </div>
                <div class="ed-podium-avatar silver">
                    @if($currentStudent && $currentStudent->id === ($topStudents[1]->id ?? null))
                        {{ mb_substr($topStudents[1]->name_ar, 0, 1) }}
                    @else
                        🥈
                    @endif
                </div>
                <h3 class="ed-podium-name">
                    @if($currentStudent && $currentStudent->id === ($topStudents[1]->id ?? null))
                        {{ $topStudents[1]->name_ar }} <span style="font-size: 0.75rem; color: #1d4ed8;">({{ __('أنت') }})</span>
                    @else
                        {{ __('طالب متفوق') }} #2
                    @endif
                </h3>
                <span class="ed-podium-stage">{{ optional($topStudents[1]->stage)->label_ar ?? __('الثانوية العامة - فلسطين') }}</span>
                <div class="ed-podium-stats">
                    <span><i class="fas fa-bolt"></i> {{ $topStudents[1]->streak_count ?? 1 }} {{ __('يوم التزام') }}</span>
                    <span class="ed-stat-divider">•</span>
                    <strong>{{ $topStudents[1]->total_points ?? 100 }} {{ __('درجة') }}</strong>
                </div>
            </div>

            <!-- المركز الأول (بطل التوجيهي المعلن) -->
            <div class="ed-podium-card rank-1">
                <div class="ed-rank-tag gold">
                    <i class="fas fa-crown"></i> {{ __('المركز الأول (بطل المنصة)') }}
                </div>
                <div class="ed-podium-avatar gold">
                    {{ mb_substr($topStudents[0]->name_ar ?? 'طالب', 0, 1) }}
                </div>
                <h3 class="ed-podium-name">{{ $topStudents[0]->name_ar }}</h3>
                <span class="ed-podium-stage">{{ optional($topStudents[0]->stage)->label_ar ?? __('الثانوية العامة - فلسطين') }}</span>
                <div class="ed-podium-stats gold">
                    <span><i class="fas fa-star"></i> {{ $topStudents[0]->streak_count ?? 1 }} {{ __('يوم متواصل') }}</span>
                    <span class="ed-stat-divider">•</span>
                    <strong>{{ $topStudents[0]->total_points ?? 150 }} {{ __('درجة') }}</strong>
                </div>
            </div>

            <!-- المركز الثالث -->
            <div class="ed-podium-card rank-3">
                <div class="ed-rank-tag bronze">
                    <i class="fas fa-award"></i> {{ __('المركز الثالث') }}
                </div>
                <div class="ed-podium-avatar bronze">
                    @if($currentStudent && $currentStudent->id === ($topStudents[2]->id ?? null))
                        {{ mb_substr($topStudents[2]->name_ar, 0, 1) }}
                    @else
                        🥉
                    @endif
                </div>
                <h3 class="ed-podium-name">
                    @if($currentStudent && $currentStudent->id === ($topStudents[2]->id ?? null))
                        {{ $topStudents[2]->name_ar }} <span style="font-size: 0.75rem; color: #1d4ed8;">({{ __('أنت') }})</span>
                    @else
                        {{ __('طالب متفوق') }} #3
                    @endif
                </h3>
                <span class="ed-podium-stage">{{ optional($topStudents[2]->stage)->label_ar ?? __('الثانوية العامة - فلسطين') }}</span>
                <div class="ed-podium-stats">
                    <span><i class="fas fa-bolt"></i> {{ $topStudents[2]->streak_count ?? 1 }} {{ __('يوم التزام') }}</span>
                    <span class="ed-stat-divider">•</span>
                    <strong>{{ $topStudents[2]->total_points ?? 80 }} {{ __('درجة') }}</strong>
                </div>
            </div>

        </div>
    @endif

    <!-- جدول قائمة المتفوقين -->
    <div class="ed-card" style="padding: 0; overflow: hidden;">
        <div class="ed-lb-table-header">
            <div>
                <h2><i class="fas fa-list-ol"></i> {{ __('قائمة ترتيب الطلبة المتفوقين') }}</h2>
                <p>{{ __('يظهر اسم صاحب المركز الأول فقط، وتُحجب أسماء باقي المتفوقين حفاظاً على الخصوصية الأكاديمية.') }}</p>
            </div>
            <div class="ed-lb-counter">
                {{ __('إجمالي الطلبة بالقائمة:') }} <strong>{{ $topStudents->count() }}</strong>
            </div>
        </div>

        <div class="ed-ranks-list">
            @forelse($topStudents as $index => $stu)
                @php 
                    $isCurrent = $currentStudent && $currentStudent->id === $stu->id; 
                    $displayName = ($index === 0 || $isCurrent) ? $stu->name_ar : __('طالب متميز') . ' #' . ($index + 1);
                @endphp
                <div class="ed-rank-row {{ $isCurrent ? 'current-student' : '' }}">
                    <div class="ed-rank-left">
                        <div class="ed-rank-num-box {{ $index < 3 ? 'top-rank' : '' }}">
                            #{{ $index + 1 }}
                        </div>
                        <div class="ed-rank-user-info">
                            <div class="name-line">
                                <strong>{{ $displayName }}</strong>
                                @if($index === 0)
                                    <span class="ed-badge ed-badge-amber" style="font-size: 0.72rem; padding: 2px 8px;"><i class="fas fa-crown"></i> {{ __('بطل المنصة') }}</span>
                                @elseif($isCurrent)
                                    <span class="ed-badge ed-badge-blue" style="font-size: 0.72rem; padding: 2px 8px;">{{ __('أنت') }}</span>
                                @else
                                    <span class="ed-badge" style="font-size: 0.68rem; padding: 2px 6px; background: #f8fafc; color: #94a3b8; border: 1px dashed #cbd5e1;"><i class="fas fa-user-shield"></i> {{ __('محجوب للخصوصية') }}</span>
                                @endif
                            </div>
                            <span class="ed-rank-stage">{{ optional($stu->stage)->label_ar ?? __('توجيهي فلسطين') }}</span>
                        </div>
                    </div>

                    <div class="ed-rank-right">
                        <div class="ed-badge ed-badge-amber">
                            <i class="fas fa-bolt"></i> {{ $stu->streak_count ?? 1 }} {{ __('يوم') }}
                        </div>
                        <div class="ed-points-badge">
                            <strong>{{ $stu->total_points ?? 50 }}</strong> {{ __('درجة') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="ed-empty-cell">
                    <i class="fas fa-trophy"></i>
                    <p>{{ __('لا توجد بيانات لوحة شرف حالياً، ابدأ بحل الاختبارات لتكون الأول!') }}</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

<style>
    .ed-leaderboard-container {
        padding: 0 0 60px;
        width: 100%;
        box-sizing: border-box;
    }

    /* Header */
    .ed-lb-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .ed-lb-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 8px;
    }

    .ed-lb-breadcrumbs .divider {
        font-size: 0.65rem;
        color: #cbd5e1;
    }

    .ed-lb-breadcrumbs .active {
        color: #1d4ed8;
        font-weight: 600;
    }

    .ed-lb-title-box h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .ed-lb-title-box p {
        font-size: 0.9rem;
        color: #64748b;
        margin: 0;
    }

    .ed-lb-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        color: #1d4ed8;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 700;
    }

    /* Top 3 Podium */
    .ed-podium-grid {
        display: grid;
        grid-template-columns: 1fr 1.15fr 1fr;
        gap: 20px;
        align-items: end;
        margin-bottom: 36px;
    }

    .ed-podium-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 26px 18px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease;
    }

    .ed-podium-card:hover {
        transform: translateY(-4px);
    }

    .ed-podium-card.rank-1 {
        order: 2;
        border-color: #fde68a;
        background: linear-gradient(180deg, #fffdfa 0%, #ffffff 100%);
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.08);
        padding: 34px 20px;
    }

    .ed-podium-card.rank-2 { order: 1; }
    .ed-podium-card.rank-3 { order: 3; }

    .ed-rank-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 999px;
        margin-bottom: 14px;
    }

    .ed-rank-tag.gold {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .ed-rank-tag.silver {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .ed-rank-tag.bronze {
        background: #ffedd5;
        color: #9a3412;
        border: 1px solid #fed7aa;
    }

    .ed-podium-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 800;
        color: #ffffff;
    }

    .ed-podium-avatar.gold {
        width: 84px;
        height: 84px;
        font-size: 1.8rem;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.25);
    }

    .ed-podium-avatar.silver {
        background: linear-gradient(135deg, #64748b, #475569);
    }

    .ed-podium-avatar.bronze {
        background: linear-gradient(135deg, #d97706, #b45309);
    }

    .ed-podium-name {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
    }

    .ed-podium-stage {
        font-size: 0.8rem;
        color: #64748b;
        display: block;
        margin-bottom: 14px;
    }

    .ed-podium-stats {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        color: #334155;
    }

    .ed-podium-stats.gold {
        background: #fffbeb;
        border-color: #fef3c7;
        color: #92400e;
    }

    .ed-stat-divider {
        color: #cbd5e1;
    }

    /* Table Bar */
    .ed-lb-table-header {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ed-lb-table-header h2 {
        margin: 0 0 3px;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-lb-table-header h2 i {
        color: #1d4ed8;
        margin-left: 6px;
    }

    .ed-lb-table-header p {
        margin: 0;
        font-size: 0.82rem;
        color: #64748b;
    }

    .ed-lb-counter {
        font-size: 0.82rem;
        color: #64748b;
    }

    .ed-lb-counter strong {
        color: #1d4ed8;
        background: #eff6ff;
        padding: 2px 8px;
        border-radius: 6px;
    }

    .ed-ranks-list {
        display: flex;
        flex-direction: column;
    }

    .ed-rank-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s ease;
    }

    .ed-rank-row:last-child {
        border-bottom: none;
    }

    .ed-rank-row:hover {
        background: #fbfcfe;
    }

    .ed-rank-row.current-student {
        background: #eff6ff;
        border-right: 4px solid #1d4ed8;
    }

    .ed-rank-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .ed-rank-num-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.88rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ed-rank-num-box.top-rank {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .ed-rank-user-info .name-line {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 2px;
    }

    .ed-rank-user-info strong {
        font-size: 0.95rem;
        color: #0f172a;
    }

    .ed-rank-stage {
        font-size: 0.78rem;
        color: #64748b;
    }

    .ed-rank-right {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .ed-points-badge {
        font-size: 0.88rem;
        color: #64748b;
    }

    .ed-points-badge strong {
        color: #1d4ed8;
        font-size: 1rem;
    }

    .ed-empty-cell {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }

    .ed-empty-cell i {
        font-size: 2rem;
        margin-bottom: 8px;
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ed-leaderboard-container {
            padding: 18px 16px 60px;
        }
        .ed-podium-grid {
            grid-template-columns: 1fr;
        }
        .ed-podium-card.rank-1 { order: 1; }
        .ed-podium-card.rank-2 { order: 2; }
        .ed-podium-card.rank-3 { order: 3; }
    }
</style>
@endsection
