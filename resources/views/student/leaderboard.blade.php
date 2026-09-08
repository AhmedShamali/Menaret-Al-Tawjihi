@extends('layouts.app')

@section('title', 'لوحة الشرف وتحدي الأوائل | توجيهي فلسطين')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #2563eb;
        --amber: #f59e0b;
        --emerald: #10b981;
        --bg-main: #f8fafc;
        --card-bg: #ffffff;
        --border-card: #e2e8f0;
        --text-title: #0f172a;
        --text-body: #334155;
        --text-muted: #64748b;
    }

    * { font-family: 'Alexandria', sans-serif; }

    .leaderboard-wrapper {
        direction: rtl; max-width: 950px; margin: 0 auto; padding: 20px 20px 60px;
    }

    .header-banner { text-align: center; margin-bottom: 40px; }
    .badge-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 18px; background: #fef3c7; border: 1px solid #fde68a;
        color: #b45309; border-radius: 50px; font-size: 0.85rem; font-weight: 700;
        margin-bottom: 12px;
    }
    .header-banner h1 { font-size: 2.2rem; font-weight: 800; color: var(--text-title); margin-bottom: 8px; }
    .header-banner p { color: var(--text-muted); font-size: 1rem; max-width: 600px; margin: 0 auto; }

    /* Top 3 Podium */
    .podium-container {
        display: grid; grid-template-columns: 1fr 1.15fr 1fr; gap: 20px;
        align-items: end; margin-bottom: 45px;
    }
    .podium-card {
        background: white; border: 1px solid var(--border-card); border-radius: 24px;
        padding: 24px 16px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        position: relative; transition: 0.3s ease;
    }
    .podium-card:hover { transform: translateY(-6px); }

    .podium-first {
        border-color: #fde68a; background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%);
        box-shadow: 0 15px 35px rgba(245, 158, 11, 0.15); order: 2;
    }
    .podium-second { order: 1; }
    .podium-third { order: 3; }

    .crown-badge {
        font-size: 1.8rem; margin-bottom: 8px; display: block;
    }
    .podium-avatar {
        width: 72px; height: 72px; border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #6366f1);
        color: white; display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; font-weight: 800; margin: 0 auto 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 3px solid white;
    }
    .podium-first .podium-avatar {
        width: 86px; height: 86px; font-size: 1.8rem;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-color: #fde68a;
    }

    .podium-name { font-size: 1.1rem; font-weight: 800; color: var(--text-title); margin-bottom: 4px; }
    .podium-stage { font-size: 0.78rem; color: var(--text-muted); margin-bottom: 12px; }
    .podium-score {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f1f5f9; padding: 4px 14px; border-radius: 50px;
        font-size: 0.85rem; font-weight: 700; color: var(--text-title);
    }
    .podium-first .podium-score {
        background: #fef3c7; color: #b45309;
    }

    /* Ranks List */
    .ranks-card {
        background: white; border: 1px solid var(--border-card); border-radius: 20px;
        padding: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .rank-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; border-radius: 14px; transition: 0.2s;
    }
    .rank-row:hover { background: #f8fafc; }
    .rank-row.current-user-row {
        background: #eff6ff; border: 1.5px solid #bfdbfe;
    }

    .rank-left { display: flex; align-items: center; gap: 16px; }
    .rank-number {
        width: 34px; height: 34px; border-radius: 10px; background: #f1f5f9;
        color: #475569; font-weight: 800; font-size: 0.9rem;
        display: flex; align-items: center; justify-content: center;
    }
    .rank-info h4 { font-size: 0.95rem; font-weight: 700; color: var(--text-title); margin-bottom: 2px; }
    .rank-info span { font-size: 0.78rem; color: var(--text-muted); }

    .rank-badges { display: flex; align-items: center; gap: 12px; }
    .streak-badge {
        font-size: 0.82rem; font-weight: 700; color: #ea580c;
        display: flex; align-items: center; gap: 4px;
        background: #fff7ed; border: 1px solid #ffedd5; padding: 4px 10px; border-radius: 8px;
    }
    .points-badge {
        font-size: 0.85rem; font-weight: 800; color: var(--primary);
    }

    @media (max-width: 700px) {
        .podium-container { grid-template-columns: 1fr; }
        .podium-first { order: 1; }
        .podium-second { order: 2; }
        .podium-third { order: 3; }
    }
</style>

<div class="leaderboard-wrapper">
    <!-- Header -->
    <div class="header-banner">
        <div class="badge-pill">
            <i class="fa-solid fa-trophy"></i>
            <span>لوحة الشرف وتحدي الالتزام اليومي</span>
        </div>
        <h1>أوائل طلبة فلسطين 🏆</h1>
        <p>الترتيب الأكاديمي للطلاب المتفوقين الأكثر التزاماً وحضوراً وحلاً للامتحانات على المنصة.</p>
    </div>

    @if($topStudents->count() >= 3)
        <!-- Top 3 Podium -->
        <div class="podium-container">
            <!-- المركز الثاني -->
            <div class="podium-card podium-second">
                <span class="crown-badge">🥈</span>
                <div class="podium-avatar">{{ mb_substr($topStudents[1]->name_ar ?? 'طالب', 0, 1) }}</div>
                <div class="podium-name">{{ $topStudents[1]->name_ar }}</div>
                <div class="podium-stage">{{ optional($topStudents[1]->stage)->label_ar ?? 'توجيهي فلسطين' }}</div>
                <div class="podium-score">
                    <i class="fa-solid fa-fire" style="color: #ea580c;"></i> {{ $topStudents[1]->streak_count ?? 1 }} يوم • {{ $topStudents[1]->total_points ?? 100 }} نقطة
                </div>
            </div>

            <!-- المركز الأول -->
            <div class="podium-card podium-first">
                <span class="crown-badge">👑</span>
                <div class="podium-avatar">{{ mb_substr($topStudents[0]->name_ar ?? 'طالب', 0, 1) }}</div>
                <div class="podium-name">{{ $topStudents[0]->name_ar }}</div>
                <div class="podium-stage">{{ optional($topStudents[0]->stage)->label_ar ?? 'توجيهي فلسطين' }}</div>
                <div class="podium-score">
                    <i class="fa-solid fa-star" style="color: #f59e0b;"></i> {{ $topStudents[0]->streak_count ?? 1 }} يوم التزام • {{ $topStudents[0]->total_points ?? 150 }} نقطة
                </div>
            </div>

            <!-- المركز الثالث -->
            <div class="podium-card podium-third">
                <span class="crown-badge">🥉</span>
                <div class="podium-avatar">{{ mb_substr($topStudents[2]->name_ar ?? 'طالب', 0, 1) }}</div>
                <div class="podium-name">{{ $topStudents[2]->name_ar }}</div>
                <div class="podium-stage">{{ optional($topStudents[2]->stage)->label_ar ?? 'توجيهي فلسطين' }}</div>
                <div class="podium-score">
                    <i class="fa-solid fa-fire" style="color: #ea580c;"></i> {{ $topStudents[2]->streak_count ?? 1 }} يوم • {{ $topStudents[2]->total_points ?? 80 }} نقطة
                </div>
            </div>
        </div>
    @endif

    <!-- Ranks Table -->
    <div class="ranks-card">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-title); margin-bottom: 16px; padding: 0 10px;">
            <i class="fa-solid fa-ranking-star" style="color: var(--primary);"></i> قائمة المتفوقين
        </h3>

        @foreach($topStudents as $index => $stu)
            @php $isCurrent = $currentStudent && $currentStudent->id === $stu->id; @endphp
            <div class="rank-row {{ $isCurrent ? 'current-user-row' : '' }}">
                <div class="rank-left">
                    <div class="rank-number">#{{ $index + 1 }}</div>
                    <div class="rank-info">
                        <h4>{{ $stu->name_ar }} @if($isCurrent) <span style="font-size: 0.75rem; background: var(--primary); color: white; padding: 2px 8px; border-radius: 10px;">أنت</span> @endif</h4>
                        <span>{{ optional($stu->stage)->label_ar ?? 'ثانوية عامة' }}</span>
                    </div>
                </div>

                <div class="rank-badges">
                    <div class="streak-badge">
                        <i class="fa-solid fa-fire"></i> {{ $stu->streak_count ?? 1 }} يوم
                    </div>
                    <div class="points-badge">
                        {{ $stu->total_points ?? 50 }} نقطة
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
