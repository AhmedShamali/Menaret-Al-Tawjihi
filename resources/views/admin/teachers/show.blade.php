@extends('layouts.app')

@section('title', 'ملف المدرس الأكاديمي')

@section('content')
<div class="teacher-profile-wrapper">

    {{-- رأس الصفحة والمسار --}}
    <div class="page-header">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> الرئيسية</a>
            <span class="sep"><i class="fa-solid fa-chevron-left"></i></span>
            <span>إدارة الكادر</span>
            <span class="sep"><i class="fa-solid fa-chevron-left"></i></span>
            <span class="current">الملف الأكاديمي للمعلم</span>
        </nav>
    </div>

    {{-- بطاقة رأس الملف الشخصي (Hero Card) --}}
    <div class="profile-hero-card">
        <div class="profile-id-badge">
            الرقم التعريفي <br>
            <span>#{{ $teacher->id }}</span>
        </div>

        <div class="profile-avatar-area">
            @if($teacher->photo)
                <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->name }}" class="profile-avatar-img">
            @else
                <div class="profile-avatar-placeholder">
                    {{ mb_substr($teacher->name, 0, 2) }}
                </div>
            @endif
        </div>

        <div class="profile-main-info">
            <span class="profile-role-tag">
                <i class="fa-solid fa-chalkboard-user"></i> عضو هيئة تدريس
            </span>
            <h1 class="profile-name">{{ $teacher->name }}</h1>
            @if($teacher->major)
                <p class="profile-major"><i class="fa-solid fa-graduation-cap"></i> {{ $teacher->major }}</p>
            @endif
        </div>
    </div>

    {{-- شبكة المعلومات التفصيلية --}}
    <div class="profile-info-grid">

        <!-- البريد الإلكتروني -->
        <div class="info-card">
            <div class="info-icon email-bg">
                <i class="fa-regular fa-envelope"></i>
            </div>
            <div class="info-content">
                <span class="info-label">البريد الإلكتروني</span>
                <span class="info-value">{{ $teacher->email }}</span>
            </div>
        </div>

        <!-- المرحلة الدراسية (مجلوبة تلقائياً من المادة المسندة) -->
        <div class="info-card">
            <div class="info-icon stage-bg">
                <i class="fa-solid fa-school"></i>
            </div>
            <div class="info-content">
                <span class="info-label">المرحلة الدراسية</span>
                <span class="info-value highlight">
                    {{ optional(optional($teacher->subject)->stage)->label_ar ?? 'غير محددة' }}
                </span>
            </div>
        </div>

        <!-- المادة المسندة -->
        <div class="info-card">
            <div class="info-icon subject-bg">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div class="info-content">
                <span class="info-label">المادة الدراسية المسندة</span>
                <span class="info-value highlight">
                    {{ optional($teacher->subject)->name_ar ?? optional($teacher->subject)->name ?? 'لم تُسند بعد' }}
                </span>
            </div>
        </div>

        <!-- تاريخ الانضمام -->
        <div class="info-card">
            <div class="info-icon date-bg">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="info-content">
                <span class="info-label">تاريخ انضمام المعلم</span>
                <span class="info-value">{{ $teacher->created_at->format('Y-m-d') }}</span>
            </div>
        </div>

    </div>

    {{-- نبذة المدرس (إذا وجدت) --}}
    @if($teacher->bio)
    <div class="uni-card bio-section-card">
        <div class="uni-card-header">
            <div class="header-icon warning-bg">
                <i class="fa-solid fa-pen-nib"></i>
            </div>
            <div>
                <h3>النبذة التعريفية (Bio)</h3>
            </div>
        </div>
        <div class="uni-card-body">
            <p class="bio-text">{{ $teacher->bio }}</p>
        </div>
    </div>
    @endif

</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --primary-color: #6366f1;
        --primary-hover: #4f46e5;
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --radius-lg: 18px;
        --radius-md: 12px;
        --shadow-card: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .teacher-profile-wrapper {
        padding-bottom: 60px;
        animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        max-width: 1100px;
        margin: 0 auto;
    }

    .page-header { margin-bottom: 24px; }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .breadcrumb-nav a {
        color: var(--text-muted);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .breadcrumb-nav a:hover { color: var(--primary-color); }
    .breadcrumb-nav .sep { font-size: 0.65rem; color: #cbd5e1; }
    .breadcrumb-nav .current { color: var(--primary-color); font-weight: 700; }

    /* بطاقة الـ Hero الرئيسية */
    .profile-hero-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-card);
        padding: 30px;
        display: flex;
        align-items: center;
        gap: 24px;
        position: relative;
        margin-bottom: 24px;
    }

    .profile-id-badge {
        position: absolute;
        top: 24px;
        left: 24px;
        background: #f1f5f9;
        color: var(--text-muted);
        padding: 6px 14px;
        border-radius: var(--radius-md);
        font-size: 0.75rem;
        font-weight: 700;
        text-align: center;
        border: 1px solid var(--border-color);
    }

    .profile-id-badge span {
        color: var(--primary-color);
        font-size: 0.95rem;
    }

    .profile-avatar-area {
        flex-shrink: 0;
    }

    .profile-avatar-img, .profile-avatar-placeholder {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
    }

    .profile-avatar-placeholder {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25);
    }

    .profile-main-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .profile-role-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        background: #fef3c7;
        color: #d97706;
        padding: 4px 12px;
        border-radius: 30px;
        font-weight: 700;
        width: fit-content;
    }

    .profile-name {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .profile-major {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* شبكة المعلومات */
    .profile-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .profile-info-grid { grid-template-columns: 1fr; }
    }

    .info-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--shadow-card);
    }

    .info-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .email-bg { background: #e0e7ff; color: #4f46e5; }
    .stage-bg { background: #cff4fc; color: #055160; }
    .subject-bg { background: #fef3c7; color: #d97706; }
    .date-bg { background: #f1f5f9; color: #475569; }

    .info-content {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .info-label {
        font-size: 0.78rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .info-value.highlight {
        color: var(--primary-color);
    }

    /* كرت النبذة */
    .uni-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .uni-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .header-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .warning-bg { background: #fef3c7; color: #d97706; }

    .uni-card-header h3 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }

    .header-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .uni-card-body { padding: 20px 24px; }
    .bio-text { color: #334155; font-size: 0.92rem; line-height: 1.6; margin: 0; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
