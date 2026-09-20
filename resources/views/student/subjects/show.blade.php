@extends('layouts.app')

@section('title', ($subject->name_ar ?? $subject->name) . ' | ' . __('منارة التوجيهي'))

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="/js/offline-video-manager.js"></script>

<style>
/* ==========================================================================
   CLASSIC ROYAL ACADEMIC DESIGN SYSTEM - STUDENT SUBJECT VIEW
   ========================================================================== */
.ed-sub-wrap {
    width: 100%;
    margin: 0 auto;
    padding: 0 0 50px;
    box-sizing: border-box;
}

/* 1. شريط التنقل العلوي الكلاسيكي */
.ed-top-nav-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}

.ed-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    color: #1e3a8a;
    border: 1px solid #cbd5e1;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
.ed-back-btn:hover {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
    transform: translateX(3px);
}

.ed-academic-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #475569;
}

/* 2. هيدر المساق الأكاديمي الملكي */
.ed-subject-hero {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
    padding: 28px 32px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}

.ed-subject-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(90deg, #1e3a8a 0%, #3b82f6 50%, #d97706 100%);
}

.ed-hero-main {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 24px;
}

.ed-hero-info {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    max-width: 720px;
}

.ed-sub-icon-box {
    width: 72px;
    height: 72px;
    border-radius: 16px;
    background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
    color: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    border: 2px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15);
    flex-shrink: 0;
}

.ed-hero-title-area h1 {
    margin: 0 0 6px;
    font-size: 1.85rem;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.25;
}

.ed-hero-stage-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 800;
    margin-bottom: 8px;
}

.ed-hero-desc {
    margin: 0 0 16px;
    color: #64748b;
    font-size: 0.92rem;
    line-height: 1.6;
}

.ed-hero-badges-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}

.ed-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 800;
}
.ed-status-badge.active {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.ed-status-badge.custom {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}
.ed-status-badge.pending {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
}
.ed-status-badge.free-trial {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #cbd5e1;
}

/* إحصائيات الهيدر السريعة */
.ed-hero-stats-panel {
    display: flex;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 20px;
    align-self: center;
}

.ed-hstat-box {
    text-align: center;
    padding: 0 10px;
}
.ed-hstat-box:not(:last-child) {
    border-left: 1px solid #e2e8f0;
}
.ed-hstat-num {
    display: block;
    font-size: 1.35rem;
    font-weight: 900;
    color: #1e3a8a;
}
.ed-hstat-lbl {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
}

.ed-hero-actions-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.ed-btn-royal {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}
.ed-btn-royal.primary {
    background: #1e3a8a;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
}
.ed-btn-royal.primary:hover {
    background: #172554;
}
.ed-btn-royal.secondary {
    background: #f8fafc;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.ed-btn-royal.secondary:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}
.ed-btn-royal.emerald {
    background: #059669;
    color: #ffffff;
}
.ed-btn-royal.emerald:hover {
    background: #047857;
}

/* 3. شبكة المحتوى الكلاسيكية (Main + Sidebar) */
.ed-layout-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 28px;
    align-items: start;
}

@media (max-width: 1040px) {
    .ed-layout-grid {
        grid-template-columns: 1fr;
    }
}

/* 4. أزرار التبويبات الكلاسيكية (Segmented Control) */
.ed-classic-tabs {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 6px;
    display: flex;
    gap: 6px;
    margin-bottom: 22px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}

.ed-tab-btn {
    flex: 1;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 800;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
    background: transparent;
    color: #64748b;
}
.ed-tab-btn:hover {
    color: #0f172a;
    background: #f8fafc;
}
.ed-tab-btn.active {
    background: #1e3a8a;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.25);
}

.ed-tab-count {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 800;
}
.ed-tab-btn.active .ed-tab-count {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}
.ed-tab-btn:not(.active) .ed-tab-count {
    background: #e2e8f0;
    color: #475569;
}

/* 5. بطاقات الفيديو الكلاسيكية الفاخرة */
.ed-video-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
    margin-bottom: 24px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.ed-video-card:hover {
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}

.ed-player-frame {
    position: relative;
    padding-top: 56.25%;
    background: #090d16;
}
.ed-player-frame video, .ed-player-frame iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

/* شريط تحكم الفيديو الذكي */
.ed-smart-player-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding: 10px 18px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}
.speed-buttons-group {
    display: flex;
    align-items: center;
    gap: 4px;
}
.speed-btn {
    padding: 4px 10px;
    font-size: 0.78rem;
    font-weight: 800;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    transition: 0.15s;
}
.speed-btn:hover, .speed-btn.active {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
}
.btn-toggle-notes {
    padding: 5px 12px;
    font-size: 0.8rem;
    font-weight: 800;
    border-radius: 8px;
    border: 1px solid #bfdbfe;
    background: #eff6ff;
    color: #1e40af;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: 0.2s;
}
.btn-toggle-notes:hover {
    background: #dbeafe;
}

.ed-video-info-box {
    padding: 18px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.ed-vtitle {
    margin: 0 0 4px;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
}
.ed-vmeta {
    font-size: 0.78rem;
    color: #64748b;
    font-weight: 600;
}

.ed-video-actions-box {
    padding: 12px 22px;
    background: #fcfdfe;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.btn-offline-save {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.2s;
}
.btn-offline-save:hover {
    background: #dcfce7;
}

.btn-offline-downloaded {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-direct-download {
    background: #0284c7;
    color: #ffffff;
    border: 1px solid #0284c7;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
}
.btn-direct-download:hover {
    background: #0369a1;
    color: #ffffff;
    border-color: #0369a1;
}

.note-item-card {
    transition: all 0.2s ease;
}
.note-item-card:hover {
    border-color: #93c5fd !important;
    background: #f8fafc !important;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.05);
}
.note-time-btn:hover {
    background: #dbeafe !important;
    border-color: #60a5fa !important;
}

/* بطاقة المحتوى المقفل */
.ed-locked-card {
    background: #ffffff;
    border: 1.5px dashed #cbd5e1;
    border-radius: 16px;
    padding: 36px 24px;
    text-align: center;
    margin-bottom: 24px;
}
.ed-lock-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: #fef2f2;
    color: #ef4444;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 16px;
    border: 1px solid #fee2e2;
}

/* 6. حالة الفراغ الأكاديمية الكلاسيكية الفاخرة (Empty State) */
.ed-classic-empty-state {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 48px 30px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    margin-bottom: 24px;
}
.ed-empty-icon-shield {
    width: 76px;
    height: 76px;
    border-radius: 20px;
    background: linear-gradient(135deg, #eff6ff 0%, #f1f5f9 100%);
    color: #1e3a8a;
    border: 1.5px solid #dbeafe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    margin: 0 auto 20px;
    box-shadow: 0 4px 14px rgba(30, 58, 138, 0.08);
}
.ed-empty-title {
    margin: 0 0 8px;
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
}
.ed-empty-desc {
    max-width: 540px;
    margin: 0 auto 22px;
    font-size: 0.9rem;
    color: #64748b;
    line-height: 1.6;
}
.ed-empty-cta {
    display: inline-flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

/* 7. بطاقات الاختبارات الكلاسيكية */
.ed-exam-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    transition: all 0.2s ease;
}
.ed-exam-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
}
.ed-exam-card.solved {
    border-color: #bbf7d0;
    background: #fcfffd;
}
.ed-exam-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.ed-exam-icon.pending {
    background: #eff6ff;
    color: #1e3a8a;
}
.ed-exam-icon.completed {
    background: #ecfdf5;
    color: #059669;
}

/* 8. عناصر السايدبار الكلاسيكية */
.ed-side-widget {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
    padding: 24px;
    margin-bottom: 24px;
}

.ed-widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.ed-widget-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* بطاقة المعلم الكلاسيكية */
.ed-teacher-box {
    text-align: center;
}
.ed-teacher-avatar-ring {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.9rem;
    font-weight: 900;
    border: 3px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}
.ed-teacher-avatar-ring.active {
    background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
    color: #ffffff;
    border-color: #bfdbfe;
}
.ed-teacher-avatar-ring.placeholder {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    color: #1e3a8a;
    border-color: #cbd5e1;
}

.ed-teacher-name {
    margin: 0 0 4px;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
}
.ed-teacher-role {
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    display: block;
    margin-bottom: 16px;
}

/* عناصر تنزيل الملفات */
.ed-file-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 10px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}
.ed-file-item:hover {
    background: #ffffff;
    border-color: #1e3a8a;
    transform: translateX(-3px);
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
}
.ed-file-badge {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #fef2f2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

/* أوفلاين والمودال */
.offline-drawer-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    z-index: 9999;
}
.offline-drawer {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 420px;
    max-width: 90vw;
    background: #ffffff;
    box-shadow: 20px 0 50px rgba(0,0,0,0.2);
    z-index: 10000;
    display: flex;
    flex-direction: column;
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.offline-drawer.open {
    transform: translateX(0);
}
</style>

<div class="ed-sub-wrap">

    {{-- 1. شريط التنقل العلوي الكلاسيكي --}}
    <div class="ed-top-nav-bar">
        <a href="{{ route('student.subjects.index') }}" class="ed-back-btn">
            <i class="fa-solid fa-arrow-right"></i>
            <span>{{ __('العودة لموادي ومقرراتي الدراسية') }}</span>
        </a>

        <div class="ed-academic-tag">
            <i class="fa-solid fa-graduation-cap" style="color: #1e3a8a;"></i>
            <span>{{ optional($subject->stage)->label_ar ?? optional($subject->stage)->name ?? __('الثانوية العامة - فلسطين') }}</span>
        </div>
    </div>

    {{-- 2. كرت الهيدر الأكاديمي الملكي --}}
    <header class="ed-subject-hero">
        <div class="ed-hero-main">
            <div class="ed-hero-info">
                <div class="ed-sub-icon-box">
                    @if($subject->icon && (str_contains($subject->icon, 'fa-') || str_contains($subject->icon, 'fas ')))
                        <i class="{{ $subject->icon }}"></i>
                    @else
                        {{ $subject->icon ?: '📖' }}
                    @endif
                </div>

                <div class="ed-hero-title-area">
                    <div class="ed-hero-stage-badge">
                        <i class="fa-solid fa-bookmark"></i>
                        <span>{{ optional($subject->stage)->label_ar ?? __('الثانوية العامة (توجيهي)') }}</span>
                    </div>

                    <h1>{{ $subject->name_ar ?? $subject->name }}</h1>

                    <p class="ed-hero-desc">
                        {{ $subject->description ?: __('منهاج التوجيهي الوزاري المعتمد في فلسطين — شروحات تفصيلية للمنهج، تدريبات وزارية، نماذج امتحانات، وإمكانية المشاهدة بدون إنترنت.') }}
                    </p>

                    <div class="ed-hero-badges-row">
                        @if($enrollment && $enrollment->status === 'active' && $enrollment->access_mode === 'all')
                            <span class="ed-status-badge active">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>{{ __('كامل المنهج مفعّل في حسابك') }}</span>
                            </span>
                        @elseif($enrollment && $enrollment->status === 'active' && $enrollment->access_mode === 'custom')
                            <span class="ed-status-badge custom">
                                <i class="fa-solid fa-layer-group"></i>
                                <span>{{ __('باقة مخصصة معتمدة') }}</span>
                            </span>
                        @elseif($enrollment && $enrollment->status === 'pending')
                            <span class="ed-status-badge pending">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>{{ __('إشعار السداد قيد التدقيق والاعتماد') }}</span>
                            </span>
                        @else
                            <span class="ed-status-badge free-trial">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>{{ __('نسخة استعراضية تجريبية') }}</span>
                            </span>
                        @endif

                        @if($subject->hasAssignedTeacher())
                            <span style="font-size: 0.8rem; color: #475569; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-chalkboard-user" style="color: #1e3a8a;"></i>
                                {{ $subject->teacher_display_name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- إحصائيات سريعة للمقرر --}}
            <div class="ed-hero-stats-panel">
                <div class="ed-hstat-box">
                    <span class="ed-hstat-num">{{ $videos->count() }}</span>
                    <span class="ed-hstat-lbl">{{ __('محاضرة') }}</span>
                </div>
                <div class="ed-hstat-box">
                    <span class="ed-hstat-num">{{ $exams->count() }}</span>
                    <span class="ed-hstat-lbl">{{ __('اختبار') }}</span>
                </div>
                <div class="ed-hstat-box">
                    <span class="ed-hstat-num">{{ $files->count() }}</span>
                    <span class="ed-hstat-lbl">{{ __('ملزمة') }}</span>
                </div>
            </div>
        </div>

        {{-- أزرار الإجراءات السريعة --}}
        <div class="ed-hero-actions-bar">
            @if(!$enrollment || $enrollment->status !== 'active')
                <button type="button" onclick="openRedeemModal()" class="ed-btn-royal emerald">
                    <i class="fa-solid fa-ticket"></i>
                    <span>{{ __('تفعيل كود المادة / بطاقة شحن') }}</span>
                </button>
            @endif

            <button type="button" onclick="openOfflineDrawer()" class="ed-btn-royal secondary">
                <i class="fa-solid fa-download" style="color: #1e3a8a;"></i>
                <span>{{ __('فيديوهاتي بدون إنترنت') }}</span>
                <span id="heroOfflineBadge" style="background: #1e3a8a; color: #ffffff; padding: 1px 7px; border-radius: 8px; font-size: 0.72rem; font-weight: 800;">0</span>
            </button>

            @if($subject->hasAssignedTeacher() && $subject->teacher)
                <a href="{{ route('student.chat.teacher', $subject->teacher->id) }}" class="ed-btn-royal secondary">
                    <i class="fa-solid fa-comment-dots" style="color: #1e3a8a;"></i>
                    <span>{{ __('استفسار من أستاذ المادة') }}</span>
                </a>
            @endif
        </div>
    </header>

    {{-- 3. شبكة المحتوى (قسم الدروس والامتحانات + السايدبار) --}}
    <div class="ed-layout-grid">

        {{-- العمود الرئيسي: التبويبات والمحتوى --}}
        <main>
            {{-- تبويبات التنقل الكلاسيكية --}}
            <div class="ed-classic-tabs">
                <button type="button" id="tabBtnVideos" class="ed-tab-btn active" onclick="switchSubjectTab('videos')">
                    <i class="fa-solid fa-circle-play"></i>
                    <span>{{ __('الدروس والحصص المرئية') }}</span>
                    <span class="ed-tab-count">{{ $videos->count() }}</span>
                </button>
                <button type="button" id="tabBtnExams" class="ed-tab-btn" onclick="switchSubjectTab('exams')">
                    <i class="fa-solid fa-file-signature"></i>
                    <span>{{ __('بنك الامتحانات الإلكترونية') }}</span>
                    <span class="ed-tab-count">{{ $exams->count() }}</span>
                </button>
            </div>

            {{-- 1. تبويب الدروس والحصص المرئية --}}
            <div id="tabContentVideos">
                @if($videos->count() > 0)
                    @foreach($videos as $video)
                        @if($video->is_unlocked)
                            <article class="ed-video-card" id="card_video_{{ $video->id }}">
                                <div class="ed-player-frame">
                                    @php
                                        $rawUrl = trim($video->url_path ?? '');
                                        $ytEmbed = $video->youtube_embed_url;
                                        $isYt = !empty($ytEmbed) || str_contains($rawUrl, 'youtube.com') || str_contains($rawUrl, 'youtu.be');
                                        if ($isYt) {
                                            if (empty($ytEmbed) && !empty($rawUrl)) {
                                                if (preg_match('/(?:v=|youtu\.be\/|embed\/|shorts\/|live\/)([a-zA-Z0-9_\-]{11})/', $rawUrl, $ym)) {
                                                    $ytEmbed = 'https://www.youtube.com/embed/' . $ym[1] . '?enablejsapi=1&rel=0&modestbranding=1';
                                                } else {
                                                    $ytEmbed = str_replace('watch?v=', 'embed/', $rawUrl);
                                                }
                                            }
                                            if (!empty($ytEmbed) && !str_contains($ytEmbed, 'enablejsapi=1')) {
                                                $ytEmbed .= (str_contains($ytEmbed, '?') ? '&' : '?') . 'enablejsapi=1&rel=0&modestbranding=1';
                                            }
                                        }
                                        $isDirectVideo = (bool) preg_match('/\.(mp4|webm|ogg|mov|m4v)($|\?)/i', $rawUrl);
                                        $directVideoUrl = $isDirectVideo 
                                            ? (filter_var($rawUrl, FILTER_VALIDATE_URL) ? $rawUrl : asset('storage/' . $rawUrl))
                                            : null;
                                    @endphp

                                    @if(!empty($ytEmbed))
                                        <iframe id="player_yt_{{ $video->id }}" src="{{ $ytEmbed }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none;"></iframe>
                                    @elseif($isDirectVideo && $directVideoUrl)
                                        <video id="player_{{ $video->id }}" controls preload="metadata" playsinline controlsList="nodownload" style="position: absolute; inset: 0; width: 100%; height: 100%;">
                                            <source src="{{ $directVideoUrl }}" type="video/mp4">{{ __('متصفحك لا يدعم مشغل الفيديو.') }}
                                        </video>
                                    @elseif(!empty($rawUrl) && filter_var($rawUrl, FILTER_VALIDATE_URL))
                                        <iframe id="player_ext_{{ $video->id }}" src="{{ $rawUrl }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none;"></iframe>
                                    @else
                                        {{-- في حال كان الدرس مرفقاً بملف أو دوسية بدون فيديو --}}
                                        <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #0f172a; color: #f8fafc; padding: 24px; text-align: center;">
                                            <i class="fa-solid fa-file-pdf" style="font-size: 3.2rem; color: #ef4444; margin-bottom: 12px;"></i>
                                            <h4 style="margin: 0 0 8px; font-size: 1.15rem; font-weight: 800; color: #ffffff;">{{ $video->title }}</h4>
                                            <p style="margin: 0 0 16px; font-size: 0.88rem; color: #94a3b8; max-width: 450px;">
                                                {{ __('هذا الدرس مخصص كملزمة / دوسية دراسية معتمدة قابلة للتحميل والدراسة المباشرة.') }}
                                            </p>
                                            @if(!empty($video->pdf_path))
                                                <a href="{{ route('content.download', $video->id) }}" class="ed-btn-royal primary" style="font-size: 0.86rem; padding: 10px 22px; text-decoration: none;">
                                                    <i class="fa-solid fa-cloud-arrow-down"></i> {{ __('تحميل ملزمة الدرس الآن') }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- شريط التحكم بالسرعة والملاحظات --}}
                                <div class="ed-smart-player-bar">
                                    <div class="speed-buttons-group">
                                        <span style="font-size: 0.78rem; font-weight: 800; color: #64748b; margin-left: 4px;">
                                            <i class="fa-solid fa-gauge-high"></i> {{ __('سرعة العرض:') }}
                                        </span>
                                        <button type="button" class="speed-btn" onclick="setVideoSpeed('{{ $video->id }}', 0.75, this)">0.75x</button>
                                        <button type="button" class="speed-btn active" onclick="setVideoSpeed('{{ $video->id }}', 1, this)">1x</button>
                                        <button type="button" class="speed-btn" onclick="setVideoSpeed('{{ $video->id }}', 1.25, this)">1.25x</button>
                                        <button type="button" class="speed-btn" onclick="setVideoSpeed('{{ $video->id }}', 1.5, this)">1.5x</button>
                                        <button type="button" class="speed-btn" onclick="setVideoSpeed('{{ $video->id }}', 2, this)">2x</button>
                                    </div>

                                    <div>
                                        <button type="button" class="btn-toggle-notes" onclick="toggleNotesSection('{{ $video->id }}')">
                                            <i class="fa-solid fa-bookmark"></i>
                                            <span>{{ __('ملاحظاتي على الدرس') }}</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- قسم تدوين الملاحظات بالتوقيت --}}
                                <div class="video-notes-panel" id="notes_panel_{{ $video->id }}" style="display: none; padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <input type="text" id="note_input_{{ $video->id }}" placeholder="{{ __('اكتب ملاحظتك عند التوقيت الحالي واضغط حفظ...') }}" style="flex: 1; padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; outline: none;" onkeydown="if(event.key==='Enter'){event.preventDefault(); submitVideoNote('{{ $video->id }}');}">
                                        <button type="button" id="btn_submit_note_{{ $video->id }}" onclick="submitVideoNote('{{ $video->id }}')" class="ed-btn-royal primary" style="padding: 9px 16px; font-size: 0.82rem; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-bookmark"></i> <span>{{ __('حفظ بالتوقيت') }}</span>
                                        </button>
                                    </div>
                                    <div class="notes-list-box" id="notes_list_{{ $video->id }}" style="max-height: 220px; overflow-y: auto; margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                                        <span style="font-size: 0.8rem; color: #94a3b8; text-align: center; padding: 8px;">
                                            <i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري تحميل الملاحظات...') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- بيانات المحاضرة --}}
                                <div class="ed-video-info-box">
                                    <div>
                                        <span style="background: #eff6ff; color: #1e3a8a; padding: 3px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-block; margin-bottom: 6px;">
                                            {{ $video->channel_name ?? __('الدرس') . ' #' . $video->order }}
                                        </span>
                                        <h3 class="ed-vtitle">{{ $video->title }}</h3>
                                    </div>
                                    <span class="ed-vmeta">
                                        <i class="fa-regular fa-clock"></i> #{{ $video->order }}
                                    </span>
                                </div>

                                {{-- إجراءات التنزيل والملفات المرفقة --}}
                                <div class="ed-video-actions-box">
                                    <div id="offline_action_box_{{ $video->id }}" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        @if($isYt)
                                            <span style="font-size: 0.8rem; color: #94a3b8; display: inline-flex; align-items: center; gap: 6px;">
                                                <i class="fa-brands fa-youtube" style="color: #ef4444; font-size: 1.1rem;"></i> {{ __('بث يوتيوب مباشر') }}
                                            </span>
                                        @elseif($isDirectVideo && $directVideoUrl)
                                            <a href="{{ route('content.downloadVideo', $video->id) }}" class="btn-direct-download" title="{{ __('تحميل ملف الفيديو الأصلي إلى جهازك') }}">
                                                <i class="fa-solid fa-cloud-arrow-down"></i> {{ __('تحميل الفيديو (MP4)') }}
                                            </a>
                                            <button type="button" class="btn-offline-save" id="btn_save_offline_{{ $video->id }}" onclick="downloadVideoOffline('{{ $video->id }}', '{{ $directVideoUrl }}', '{{ addslashes($video->title) }}', '{{ addslashes($subject->name_ar ?? $subject->name) }}', '{{ $subject->id }}')">
                                                <i class="fa-solid fa-download"></i> {{ __('حفظ بدون إنترنت') }}
                                            </button>
                                        @else
                                            <span style="font-size: 0.8rem; color: #64748b;"><i class="fa-solid fa-file-circle-check"></i> {{ __('ملف ومرفق دراسي') }}</span>
                                        @endif
                                    </div>


                                    @if(!empty($video->pdf_path))
                                        <a href="{{ route('content.download', $video->id) }}" style="color: #1e3a8a; font-size: 0.82rem; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; padding: 6px 12px; border-radius: 8px; border: 1px solid #bfdbfe;">
                                            <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i> {{ __('تحميل ملزمة المحاضرة') }}
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @else
                            {{-- درس مقفل --}}
                            <div class="ed-locked-card">
                                <div class="ed-lock-icon">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <h4 style="margin: 0 0 6px; font-size: 1.1rem; font-weight: 800; color: #0f172a;">
                                    {{ $video->title }} ({{ __('محتوى مقيد') }})
                                </h4>
                                <p style="color: #64748b; font-size: 0.86rem; max-width: 480px; margin: 0 auto 16px; line-height: 1.5;">
                                    {{ __('هذا الدرس متاح لطلاب باقة المنهج المعتمدة. يمكنك إدخال كود التفعيل أو التواصل مع الإدارة للتمكين.') }}
                                </p>
                                <button type="button" onclick="openRedeemModal()" class="ed-btn-royal primary">
                                    <i class="fa-solid fa-key"></i> {{ __('إدخال كود الشحن والتفعيل') }}
                                </button>
                            </div>
                        @endif
                    @endforeach
                @else
                    {{-- تصميم كلاسيكي راقٍ لحالة عدم توفر دروس حالياً --}}
                    <div class="ed-classic-empty-state">
                        <div class="ed-empty-icon-shield">
                            <i class="fa-solid fa-film"></i>
                        </div>
                        <h3 class="ed-empty-title">{{ __('المحاضرات والشروحات قيد الإعداد والتسجيل') }}</h3>
                        <p class="ed-empty-desc">
                            {{ __('يجري حالياً تجهيز ورفع المحاضرات المصورة والملازم التوضيحية لمبحث') }} ({{ $subject->name_ar ?? $subject->name }}) {{ __('وفق الخطة الوزارية المعتمدة. ستتاح الحصص في لوحتك فور اكتمال مراجعتها الأكاديمية.') }}
                        </p>
                        <div class="ed-empty-cta">
                            <button type="button" onclick="switchSubjectTab('exams')" class="ed-btn-royal primary">
                                <i class="fa-solid fa-file-pen"></i>
                                <span>{{ __('تصفح بنك الامتحانات لهذا المقرر') }}</span>
                            </button>
                            <a href="{{ route('student.planner.index') }}" class="ed-btn-royal secondary">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>{{ __('جدول المراجعة الذكي') }}</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 2. تبويب بنك الامتحانات المعتمدة للمادة --}}
            <div id="tabContentExams" style="display: none;">
                @if($exams->count() > 0)
                    @foreach($exams as $exam)
                        @php
                            $subm = $submissions[$exam->id] ?? null;
                            $isSolved = !is_null($subm);
                        @endphp
                        <div class="ed-exam-card {{ $isSolved ? 'solved' : '' }}">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div class="ed-exam-icon {{ $isSolved ? 'completed' : 'pending' }}">
                                    <i class="fa-solid {{ $isSolved ? 'fa-circle-check' : 'fa-file-signature' }}"></i>
                                </div>
                                <div>
                                    <h3 style="margin: 0 0 6px; font-size: 1.05rem; font-weight: 800; color: #0f172a;">{{ $exam->title }}</h3>
                                    <div style="display: flex; gap: 12px; align-items: center; font-size: 0.78rem; color: #64748b; font-weight: 700;">
                                        <span><i class="fa-regular fa-clock"></i> {{ $exam->duration_minutes }} {{ __('دقيقة') }}</span>
                                        <span>•</span>
                                        <span><i class="fa-solid fa-list-check"></i> {{ $exam->questions_count }} {{ __('أسئلة') }}</span>
                                        <span>•</span>
                                        <span style="color: #b45309;"><i class="fa-solid fa-star"></i> {{ $exam->total_grade ?? 100 }} {{ __('علامة') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                @if($isSolved)
                                    <div style="text-align: left; margin-bottom: 6px;">
                                        <span style="font-size: 0.72rem; color: #64748b; font-weight: 700; display: block;">{{ __('درجتك المحققة:') }}</span>
                                        <span style="font-size: 1.15rem; font-weight: 900; color: #059669;">
                                            {{ $subm->total_earned_grade ?? 0 }} / {{ $exam->total_grade ?? 100 }}
                                        </span>
                                    </div>
                                    <a href="{{ route('student.exams.results', $subm->id) }}" class="ed-btn-royal secondary" style="font-size: 0.8rem; padding: 7px 14px;">
                                        <i class="fa-solid fa-square-poll-vertical"></i> {{ __('مراجعة الإجابات') }}
                                    </a>
                                @else
                                    <a href="{{ route('student.exams.take', $exam->id) }}" class="ed-btn-royal primary">
                                        <i class="fa-solid fa-play"></i>
                                        <span>{{ __('بدء الاختبار الآن') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="ed-classic-empty-state">
                        <div class="ed-empty-icon-shield">
                            <i class="fa-solid fa-clipboard-question"></i>
                        </div>
                        <h3 class="ed-empty-title">{{ __('لا توجد اختبارات إلكترونية مضافة حالياً') }}</h3>
                        <p class="ed-empty-desc">
                            {{ __('سيتم نشر نماذج الامتحانات الوزارية والتجريبية الخاصة بهذا المقرر فور بدء الفصل الدراسي.') }}
                        </p>
                    </div>
                @endif
            </div>

        </main>

        {{-- السايدبار الجانبي الكلاسيكي --}}
        <aside>
            {{-- بطاقة الهيئة التدريسية للمساق --}}
            <div class="ed-side-widget">
                <div class="ed-widget-header">
                    <h4 class="ed-widget-title">
                        <i class="fa-solid fa-chalkboard-user" style="color: #1e3a8a;"></i>
                        <span>{{ __('معلّم المساق المعتمد') }}</span>
                    </h4>
                    <span style="font-size: 0.72rem; font-weight: 800; background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 6px;">
                        {{ __('فلسطين 🇵🇸') }}
                    </span>
                </div>

                <div class="ed-teacher-box">
                    @if($subject->hasAssignedTeacher())
                        <div class="ed-teacher-avatar-ring active">
                            {{ mb_substr($subject->teacher_display_name, 0, 1) }}
                        </div>
                        <h3 class="ed-teacher-name">{{ $subject->teacher_display_name }}</h3>
                        <span class="ed-teacher-role">{{ __('معلّم مسار الثانوية العامة المعتمد') }}</span>

                        @if($subject->teacher)
                            <a href="{{ route('student.chat.teacher', $subject->teacher->id) }}" class="ed-btn-royal secondary" style="width: 100%; justify-content: center; box-sizing: border-box;">
                                <i class="fa-solid fa-paper-plane" style="color: #1e3a8a;"></i>
                                <span>{{ __('مراسلة المعلم عبر المنصة') }}</span>
                            </a>
                        @endif
                    @else
                        <div class="ed-teacher-avatar-ring placeholder">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <h3 class="ed-teacher-name" style="color: #1e3a8a;">{{ __('نخبة معلمي التوجيهي') }}</h3>
                        <span class="ed-teacher-role">{{ __('طاقم أكاديمي متخصص معتمد') }}</span>
                        <p style="font-size: 0.8rem; color: #64748b; line-height: 1.5; margin: 0 0 14px;">
                            {{ __('يتم تدريس هذا المبحث بواسطة كادر تربوي متميز من ذوي الخبرة في امتحانات الثانوية العامة الفلسطينية.') }}
                        </p>
                        <a href="{{ route('student.support') }}" class="ed-btn-royal secondary" style="width: 100%; justify-content: center; box-sizing: border-box;">
                            <i class="fa-solid fa-headset" style="color: #1e3a8a;"></i>
                            <span>{{ __('التواصل مع الدعم الأكاديمي') }}</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- بطاقة الملازم وأوراق العمل الرسمية --}}
            <div class="ed-side-widget">
                <div class="ed-widget-header">
                    <h4 class="ed-widget-title">
                        <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i>
                        <span>{{ __('الملازم وأوراق العمل') }}</span>
                    </h4>
                    <span style="font-size: 0.75rem; font-weight: 800; background: #eff6ff; color: #1e3a8a; padding: 2px 8px; border-radius: 6px;">
                        {{ $files->count() }} {{ __('ملف') }}
                    </span>
                </div>

                @if($files->count() > 0)
                    @foreach($files as $file)
                        <a href="{{ route('content.download', $file->id) }}" class="ed-file-item">
                            <div class="ed-file-badge">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $file->title }}
                                </div>
                                <span style="font-size: 0.72rem; color: #059669; font-weight: 700; display: block; margin-top: 2px;">
                                    <i class="fa-solid fa-circle-down"></i> {{ __('تحميل مباشر') }}
                                </span>
                            </div>
                            <i class="fa-solid fa-arrow-down" style="color: #94a3b8; font-size: 0.85rem;"></i>
                        </a>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 20px 10px; color: #94a3b8;">
                        <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 8px; display: block; color: #cbd5e1;"></i>
                        <p style="font-size: 0.82rem; font-weight: 700; margin: 0; color: #64748b;">
                            {{ __('لا توجد ملازم مرفقة حالياً') }}
                        </p>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 4px;">
                            {{ __('تُضاف أوراق العمل والمذكرات بالتزامن مع نشر الحصص.') }}
                        </span>
                    </div>
                @endif
            </div>
        </aside>

    </div>
</div>

{{-- أوفلاين دراور --}}
<div class="offline-drawer-backdrop" id="drawerBackdrop" onclick="closeOfflineDrawer()"></div>
<div class="offline-drawer" id="offlineDrawer">
    <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
        <div>
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 900; color: #0f172a;">{{ __('فيديوهاتي بدون إنترنت') }}</h3>
            <span style="font-size: 0.78rem; color: #64748b;">{{ __('محفوظة بأمان محلياً في جهازك') }}</span>
        </div>
        <button onclick="closeOfflineDrawer()" style="background: none; border: none; font-size: 1.4rem; color: #94a3b8; cursor: pointer;">&times;</button>
    </div>

    <div style="padding: 20px; overflow-y: auto; flex: 1;" id="offlineVideosList"></div>
</div>

{{-- مودال تفعيل الكود --}}
<div class="offline-drawer-backdrop" id="redeemModalBackdrop" style="display: none; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 18px; max-width: 460px; width: 100%; padding: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.25);">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: #eff6ff; color: #1e3a8a; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 12px; border: 1px solid #bfdbfe;">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <h3 style="margin: 0 0 6px; font-weight: 900; color: #0f172a;">{{ __('تفعيل كود شحن المادة') }}</h3>
            <p style="margin: 0; font-size: 0.85rem; color: #64748b;">
                {{ __('أدخل كود الشحن من بطاقتك المعتمدة لتفعيل كامل الحصص والامتحانات فوراً.') }}
            </p>
        </div>

        <form onsubmit="handleRedeemCode(event)">
            <input type="hidden" id="redeemSubjectId" value="{{ $subject->id }}">
            <div style="margin-bottom: 20px;">
                <input type="text" id="voucherCodeInput" required placeholder="TAW-XXXX-XXXX" style="width: 100%; box-sizing: border-box; padding: 14px; border: 2px solid #cbd5e1; border-radius: 10px; font-size: 1.1rem; font-weight: 800; text-align: center; letter-spacing: 2px; text-transform: uppercase;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeRedeemModal()" class="ed-btn-royal secondary" style="flex: 1; justify-content: center;">{{ __('إلغاء') }}</button>
                <button type="submit" id="btnSubmitRedeem" class="ed-btn-royal emerald" style="flex: 2; justify-content: center;">{{ __('تفعيل الآن 🚀') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
// تحديث التبويبات الكلاسيكية
function switchSubjectTab(tab) {
    const vTab = document.getElementById('tabContentVideos');
    const eTab = document.getElementById('tabContentExams');
    const btnV = document.getElementById('tabBtnVideos');
    const btnE = document.getElementById('tabBtnExams');

    if (tab === 'exams') {
        if (vTab) vTab.style.display = 'none';
        if (eTab) eTab.style.display = 'block';
        if (btnV) btnV.classList.remove('active');
        if (btnE) btnE.classList.add('active');
    } else {
        if (vTab) vTab.style.display = 'block';
        if (eTab) eTab.style.display = 'none';
        if (btnV) btnV.classList.add('active');
        if (btnE) btnE.classList.remove('active');
    }
}

// أوفلاين مانيجر
document.addEventListener('DOMContentLoaded', async () => {
    refreshOfflineBadges();
});

async function refreshOfflineBadges() {
    if (!window.offlineVideoManager) return;
    const allDownloaded = await window.offlineVideoManager.getAllDownloaded();
    const heroBadge = document.getElementById('heroOfflineBadge');
    if (heroBadge) heroBadge.textContent = allDownloaded.length;

    allDownloaded.forEach(item => {
        markVideoAsDownloadedUI(item.id);
    });
}

function markVideoAsDownloadedUI(videoId) {
    const box = document.getElementById(`offline_action_box_${videoId}`);
    if (!box) return;

    box.innerHTML = `
        <span class="btn-offline-downloaded">
            <i class="fa-solid fa-circle-check" style="color: #059669;"></i> {{ __('محفوظ للمشاهدة بدون إنترنت') }}
        </span>
        <button type="button" onclick="playLocalOfflineVideo('${videoId}')" class="ed-btn-royal primary" style="padding: 6px 12px; font-size: 0.78rem;">
            <i class="fa-solid fa-play"></i> {{ __('تشغيل محلي') }}
        </button>
        <button type="button" onclick="deleteLocalVideo('${videoId}')" title="{{ __('حذف من الذاكرة المحلية') }}" style="background: none; border: none; color: #ef4444; font-size: 0.9rem; cursor: pointer; padding: 4px;">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    `;
}

async function downloadVideoOffline(videoId, videoUrl, title, subjectName, subjectId) {
    const btn = document.getElementById(`btn_save_offline_${videoId}`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري التحميل محلياً...') }}';
    }

    try {
        await window.offlineVideoManager.downloadVideo(videoId, videoUrl, title, subjectName, subjectId);
        markVideoAsDownloadedUI(videoId);
        refreshOfflineBadges();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '{{ __('تم التنزيل بنجاح!') }}',
                text: '{{ __('تم حفظ الفيديو داخل المنصة، يمكنك مشاهدته في أي وقت بدون إنترنت.') }}',
                confirmButtonColor: '#1e3a8a',
                confirmButtonText: '{{ __('حسناً') }}'
            });
        }
    } catch (err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> {{ __('فشل التحميل، أعد المحاولة') }}';
        }
    }
}

async function playLocalOfflineVideo(videoId) {
    const item = await window.offlineVideoManager.getVideo(videoId);
    if (!item || !item.blob) return;

    const player = document.getElementById(`player_${videoId}`);
    if (player) {
        player.src = URL.createObjectURL(item.blob);
        player.scrollIntoView({ behavior: 'smooth', block: 'center' });
        player.play();
    }
}

async function deleteLocalVideo(videoId) {
    if (confirm('{{ __('هل ترغب في حذف الفيديو من المشاهدة بدون إنترنت لتوفير المساحة؟') }}')) {
        await window.offlineVideoManager.deleteVideo(videoId);
        window.location.reload();
    }
}

function openOfflineDrawer() {
    document.getElementById('offlineDrawer').classList.add('open');
    document.getElementById('drawerBackdrop').style.display = 'block';
}

function closeOfflineDrawer() {
    document.getElementById('offlineDrawer').classList.remove('open');
    document.getElementById('drawerBackdrop').style.display = 'none';
}

function openRedeemModal() {
    const modal = document.getElementById('redeemModalBackdrop');
    if (modal) modal.style.display = 'flex';
}

function closeRedeemModal() {
    const modal = document.getElementById('redeemModalBackdrop');
    if (modal) modal.style.display = 'none';
}

async function handleRedeemCode(e) {
    e.preventDefault();
    const code = document.getElementById('voucherCodeInput').value.trim();
    const subjectId = document.getElementById('redeemSubjectId').value;
    const btn = document.getElementById('btnSubmitRedeem');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري التحقق...') }}';

    try {
        const res = await fetch('/student/redeem-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code, subject_id: subjectId, _token: '{{ csrf_token() }}' })
        });
        const data = await res.json();
        if (res.ok && data.success) {
            closeRedeemModal();
            location.reload();
        } else {
            alert(data.message || '{{ __('كود الشحن غير صالح، يرجى إعادة المحاولة.') }}');
            btn.disabled = false;
            btn.textContent = '{{ __('تفعيل الآن 🚀') }}';
        }
    } catch(err) {
        btn.disabled = false;
        btn.textContent = '{{ __('تفعيل الآن 🚀') }}';
    }
}

// YouTube API & Video Management
let ytPlayers = {};

function onYouTubeIframeAPIReady() {
    document.querySelectorAll('iframe[id^="player_yt_"]').forEach(iframe => {
        initSingleYtPlayer(iframe.id);
    });
}

function initSingleYtPlayer(iframeId) {
    if (!iframeId) return null;
    const videoId = iframeId.replace('player_yt_', '');
    if (ytPlayers[videoId]) return ytPlayers[videoId];
    if (window.YT && window.YT.Player) {
        try {
            ytPlayers[videoId] = new YT.Player(iframeId, {
                events: {
                    'onReady': function(event) {
                        // Ready
                    }
                }
            });
            return ytPlayers[videoId];
        } catch(e) {
            console.warn('YT Player init:', e);
        }
    }
    return null;
}

function getVideoCurrentTime(videoId) {
    // 1. HTML5 video
    const player = document.getElementById(`player_${videoId}`);
    if (player && !isNaN(player.currentTime) && player.currentTime > 0) {
        return Math.floor(player.currentTime);
    }

    // 2. YouTube API
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.getCurrentTime === 'function') {
        try {
            const t = yt.getCurrentTime();
            if (!isNaN(t)) return Math.floor(t);
        } catch(e) {}
    }

    return 0;
}

function setVideoSpeed(videoId, speed, btnElement) {
    // 1. HTML5 Video
    const player = document.getElementById(`player_${videoId}`);
    if (player) {
        player.playbackRate = speed;
    }

    // 2. YouTube Iframe API
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.setPlaybackRate === 'function') {
        try {
            yt.setPlaybackRate(speed);
        } catch(e) {}
    }

    // 3. YouTube postMessage Fallback
    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        ytIframe.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: 'setPlaybackRate',
            args: [speed]
        }), '*');
    }

    // 4. Update UI Active State
    if (btnElement) {
        const parent = btnElement.closest('.speed-buttons-group');
        if (parent) {
            parent.querySelectorAll('.speed-btn').forEach(b => b.classList.remove('active'));
            btnElement.classList.add('active');
        }
    }

    showPlayerToast(`{{ __('تم ضبط سرعة العرض على') }} ${speed}x ⚡`);
}

function seekVideoTo(videoId, seconds) {
    // 1. HTML5 Video
    const player = document.getElementById(`player_${videoId}`);
    if (player) {
        player.currentTime = seconds;
        player.play().catch(() => {});
    }

    // 2. YouTube API
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.seekTo === 'function') {
        try {
            yt.seekTo(seconds, true);
            yt.playVideo();
        } catch(e) {}
    } else {
        const ytIframe = document.getElementById(`player_yt_${videoId}`);
        if (ytIframe && ytIframe.contentWindow) {
            ytIframe.contentWindow.postMessage(JSON.stringify({
                event: 'command',
                func: 'seekTo',
                args: [seconds, true]
            }), '*');
            ytIframe.contentWindow.postMessage(JSON.stringify({
                event: 'command',
                func: 'playVideo',
                args: []
            }), '*');
        }
    }

    const card = document.getElementById(`card_video_${videoId}`);
    if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function toggleNotesSection(videoId) {
    const panel = document.getElementById(`notes_panel_${videoId}`);
    if (!panel) return;
    const isHidden = (panel.style.display === 'none' || !panel.style.display);
    if (isHidden) {
        panel.style.display = 'block';
        loadVideoNotes(videoId);
    } else {
        panel.style.display = 'none';
    }
}

function formatNoteTime(totalSeconds) {
    const s = parseInt(totalSeconds, 10) || 0;
    const m = Math.floor(s / 60);
    const sec = s % 60;
    return `${m.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
}

function loadVideoNotes(videoId) {
    const list = document.getElementById(`notes_list_${videoId}`);
    if (!list) return;

    fetch(`/student/video-notes/${videoId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        const notes = data.notes || [];
        if (notes.length === 0) {
            list.innerHTML = `
                <div style="text-align: center; padding: 14px; color: #94a3b8; font-size: 0.82rem; background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 8px;">
                    <i class="fa-regular fa-note-sticky" style="font-size: 1.2rem; margin-bottom: 4px; display: block; color: #cbd5e1;"></i>
                    {{ __('لا توجد ملاحظات مسجلة بعد لهذا الدرس. اكتب أول ملاحظة بالتوقيت أعلاه!') }}
                </div>
            `;
            return;
        }

        list.innerHTML = notes.map(n => renderNoteItemHtml(videoId, n)).join('');
    })
    .catch(err => {
        list.innerHTML = `<span style="font-size: 0.78rem; color: #ef4444; text-align: center; padding: 6px;">{{ __('تعذر جلب الملاحظات، أعد المحاولة.') }}</span>`;
    });
}

function renderNoteItemHtml(videoId, note) {
    const formatted = note.formatted_time || formatNoteTime(note.timestamp_seconds);
    return `
        <div class="note-item-card" id="note_item_${note.id}" style="display: flex; align-items: center; justify-content: space-between; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 12px; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                <button type="button" onclick="seekVideoTo('${videoId}', ${note.timestamp_seconds})" class="note-time-btn" title="{{ __('الانتقال إلى هذه الدقيقة بالدرس') }}" style="background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; font-size: 0.78rem; font-weight: 800; border-radius: 6px; padding: 3px 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; flex-shrink: 0;">
                    <i class="fa-regular fa-clock"></i>
                    <span>${formatted}</span>
                </button>
                <div style="font-size: 0.85rem; color: #1e293b; word-break: break-word; flex: 1;">
                    ${escapeHtml(note.note_text)}
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                <span style="font-size: 0.72rem; color: #94a3b8;">${note.created_at || ''}</span>
                <button type="button" onclick="deleteVideoNote('${note.id}', '${videoId}')" title="{{ __('حذف الملاحظة') }}" style="background: none; border: none; color: #94a3b8; font-size: 0.85rem; cursor: pointer; padding: 4px 6px; border-radius: 4px;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>
    `;
}

function submitVideoNote(videoId) {
    const input = document.getElementById(`note_input_${videoId}`);
    const text = input ? input.value.trim() : '';
    if (!text) {
        if (input) input.focus();
        return;
    }

    const btn = document.getElementById(`btn_submit_note_${videoId}`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    }

    const currentTime = getVideoCurrentTime(videoId);

    fetch('/student/video-notes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            educational_content_id: videoId,
            timestamp_seconds: currentTime,
            note_text: text,
            _token: '{{ csrf_token() }}'
        })
    })
    .then(res => res.json())
    .then(res => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-bookmark"></i> <span>{{ __('حفظ بالتوقيت') }}</span>';
        }

        if (res.success && res.note) {
            input.value = '';
            const list = document.getElementById(`notes_list_${videoId}`);
            if (list) {
                // Remove empty notice if present
                const emptyNotice = list.querySelector('.fa-note-sticky');
                if (emptyNotice) {
                    list.innerHTML = '';
                }
                const newNoteDiv = document.createElement('div');
                newNoteDiv.innerHTML = renderNoteItemHtml(videoId, res.note);
                list.prepend(newNoteDiv.firstElementChild);
            }
            showPlayerToast('{{ __('تم حفظ الملاحظة عند التوقيت بنجاح 📌') }}');
        } else {
            alert(res.message || '{{ __('فشل حفظ الملاحظة، يرجى المحاولة ثانية') }}');
        }
    })
    .catch(err => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-bookmark"></i> <span>{{ __('حفظ بالتوقيت') }}</span>';
        }
        alert('{{ __('حدث خطأ أثناء حفظ الملاحظة.') }}');
    });
}

function deleteVideoNote(noteId, videoId) {
    if (!confirm('{{ __('هل أنت متأكد من رغبتك في حذف هذه الملاحظة؟') }}')) return;

    fetch(`/student/video-notes/${noteId}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            const item = document.getElementById(`note_item_${noteId}`);
            if (item) {
                item.style.opacity = '0';
                item.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    item.remove();
                    const list = document.getElementById(`notes_list_${videoId}`);
                    if (list && list.children.length === 0) {
                        loadVideoNotes(videoId);
                    }
                }, 200);
            }
            showPlayerToast('{{ __('تم حذف الملاحظة بنجاح') }}');
        } else {
            alert(res.message || '{{ __('تعذر حذف الملاحظة') }}');
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showPlayerToast(message) {
    let toast = document.getElementById('player_speed_toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'player_speed_toast';
        toast.style.cssText = 'position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: #0f172a; color: #ffffff; padding: 10px 20px; border-radius: 30px; font-size: 0.85rem; font-weight: 700; z-index: 99999; box-shadow: 0 8px 24px rgba(0,0,0,0.25); display: flex; align-items: center; gap: 8px; transition: all 0.3s ease; opacity: 0; pointer-events: none;';
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color: #38bdf8;"></i> ${message}`;
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(10px)';
    }, 2400);
}
</script>
<script src="https://www.youtube.com/iframe_api"></script>
@endsection
