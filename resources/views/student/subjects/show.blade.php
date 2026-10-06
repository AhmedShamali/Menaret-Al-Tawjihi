@extends('layouts.app')

@section('title', ($subject->name_ar ?? $subject->name) . ' | ' . __('Step by Step'))

@section('content')
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

.badge-timing-schedule {
    font-size: 0.72rem;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    line-height: 1.2;
}
.badge-timing-schedule.upcoming {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.badge-timing-schedule.active-limited {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-timing-schedule.always-open {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
}
.badge-timing-schedule.expired {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
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
.tab-text-mobile {
    display: none;
}
.tab-text-desktop {
    display: inline;
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
.ed-player-frame:fullscreen, .ed-player-frame:-webkit-full-screen {
    width: 100vw !important;
    height: 100vh !important;
    padding-top: 0 !important;
    background: #000 !important;
}

/* مشغل الفيديو الأكاديمي الصافي والمحمي - عزل كامل عن يوتيوب */
.ed-yt-shield-container {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    background: #090d16;
    overflow: hidden;
    user-select: none;
    -webkit-user-select: none;
}

.ed-yt-shield-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
    pointer-events: none !important;
}

/* درع الشاشة التفاعلي */
.ed-student-screen-shield {
    position: absolute;
    inset: 0;
    z-index: 20;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
}

.ed-center-play-circle {
    width: 66px;
    height: 66px;
    border-radius: 50%;
    background: rgba(30, 58, 138, 0.9);
    border: 3px solid rgba(255, 255, 255, 0.85);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.6);
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease, background 0.2s;
    pointer-events: none;
}

.ed-student-screen-shield:hover .ed-center-play-circle {
    transform: scale(1.12);
    background: #2563eb;
}

/* دروع حماية إضافية للأركان والعنوان لمنع أي تسريب لأزرار يوتيوب */
.ed-shield-corner-bl {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 140px;
    height: 100px;
    z-index: 25;
    background: transparent;
    cursor: pointer;
}

.ed-shield-corner-br {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 140px;
    height: 70px;
    z-index: 25;
    background: transparent;
    cursor: pointer;
}

.ed-shield-top-band {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 80px;
    z-index: 25;
    background: transparent;
    cursor: pointer;
}

/* شريط التحكم الداخلي الذكي للمنصة */
.ed-student-player-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 46px;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0) 0%, rgba(15, 23, 42, 0.95) 100%);
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 12px;
    z-index: 30;
    user-select: none;
    direction: ltr;
}

.ed-student-player-bar .btn-ctrl-action {
    background: none;
    border: none;
    color: #ffffff;
    font-size: 0.92rem;
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: background 0.15s, transform 0.1s;
    flex-shrink: 0;
}

.ed-student-player-bar .btn-ctrl-action:hover {
    background: rgba(255, 255, 255, 0.2);
}

.ed-student-player-bar .btn-ctrl-action:active {
    transform: scale(0.92);
}

.ed-student-player-bar .ctrl-time-text {
    font-family: monospace;
    font-size: 0.76rem;
    font-weight: 700;
    color: #cbd5e1;
    white-space: nowrap;
    flex-shrink: 0;
}

.ed-student-player-bar .ctrl-seek-range {
    flex: 1;
    accent-color: #38bdf8;
    cursor: pointer;
    height: 5px;
    border-radius: 3px;
    outline: none;
}

.ed-player-frame:fullscreen .ed-student-player-bar,
.ed-player-frame:-webkit-full-screen .ed-student-player-bar {
    position: fixed;
    bottom: 24px;
    left: 36px;
    right: 36px;
    height: 54px;
    padding: 0 20px;
    border-radius: 14px;
    background: rgba(15, 23, 42, 0.88);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    z-index: 999999;
}

.ed-yt-shield-container .pause-play-btn {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.5);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ed-yt-shield-container .yt-pause-overlay:hover .pause-play-btn {
    transform: scale(1.08);
    box-shadow: 0 14px 30px rgba(37, 99, 235, 0.7);
}

.ed-yt-shield-container .pause-text {
    color: #f8fafc;
    font-size: 0.95rem;
    font-weight: 800;
    letter-spacing: 0.3px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
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

.ed-stream-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 800;
}

.ed-btn-lecture-pdf {
    color: #b91c1c;
    font-size: 0.82rem;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #fef2f2;
    padding: 7px 16px;
    border-radius: 8px;
    border: 1px solid #fecaca;
    transition: all 0.2s ease;
}
.ed-btn-lecture-pdf:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #991b1b;
}

.ed-btn-lecture-video-download {
    color: #15803d;
    font-size: 0.82rem;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f0fdf4;
    padding: 7px 16px;
    border-radius: 8px;
    border: 1px solid #bbf7d0;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(22, 101, 52, 0.08);
}
.ed-btn-lecture-video-download:hover {
    background: #dcfce7;
    border-color: #86efac;
    color: #166534;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(22, 101, 52, 0.15);
}

.btn-download-quick {
    border-color: #bbf7d0 !important;
    background: #f0fdf4 !important;
    color: #15803d !important;
    text-decoration: none !important;
}
.btn-download-quick:hover {
    background: #dcfce7 !important;
    color: #166534 !important;
    transform: translateY(-1px);
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

/* المودال الأكاديمي */
.offline-drawer-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    z-index: 9999;
}

/* ==========================================================
   STUDENT SUBJECT VIEW - COMPREHENSIVE MOBILE RESPONSIVENESS
   ========================================================== */
@media (max-width: 768px) {
    .ed-sub-wrap {
        padding-bottom: 30px;
    }
    .ed-top-nav-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
        margin-bottom: 14px;
    }
    .ed-back-btn {
        width: 100%;
        justify-content: center;
        box-sizing: border-box;
    }
    .ed-academic-tag {
        width: 100%;
        justify-content: center;
        box-sizing: border-box;
    }
    .ed-subject-hero {
        padding: 18px 14px;
        border-radius: 14px;
        margin-bottom: 16px;
    }
    .ed-hero-main {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }
    .ed-hero-info {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 12px;
        width: 100%;
    }
    .ed-sub-icon-box {
        width: 58px;
        height: 58px;
        font-size: 1.8rem;
        border-radius: 14px;
    }
    .ed-hero-title-area h1 {
        font-size: 1.35rem;
    }
    .ed-hero-desc {
        font-size: 0.85rem;
        margin-bottom: 12px;
    }
    .ed-hero-badges-row {
        justify-content: center;
        gap: 6px;
    }
    .ed-hero-stats-panel {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        width: 100%;
        box-sizing: border-box;
        padding: 10px 4px;
        text-align: center;
        gap: 0;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }
    .ed-hstat-box {
        padding: 0 4px;
        border-left: 1px solid #e2e8f0;
    }
    .ed-hstat-box:last-child {
        border-left: none;
    }
    html[dir="ltr"] .ed-hstat-box {
        border-left: none;
        border-right: 1px solid #e2e8f0;
    }
    html[dir="ltr"] .ed-hstat-box:last-child {
        border-right: none;
    }
    .ed-hstat-num {
        font-size: 1.15rem;
    }
    .ed-hstat-lbl {
        font-size: 0.7rem;
    }
    .ed-hero-actions-bar {
        margin-top: 14px;
        padding-top: 14px;
        flex-direction: column;
    }
    .ed-btn-royal {
        width: 100%;
        justify-content: center;
        box-sizing: border-box;
    }

    /* التبويبات الكلاسيكية للهواتف - ثنائية الأعمدة بنظام Segmented Control متناسق 100% بدون أي خروج عن الشاشة */
    .ed-classic-tabs {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        padding: 4px !important;
        gap: 6px !important;
        margin-bottom: 16px !important;
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
    }
    .ed-classic-tabs::-webkit-scrollbar {
        display: none !important;
    }
    .ed-tab-btn {
        width: 100% !important;
        min-width: 0 !important;
        flex: none !important;
        padding: 9px 4px !important;
        font-size: 0.82rem !important;
        font-weight: 800 !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
        box-sizing: border-box !important;
    }
    .ed-tab-btn .tab-text-desktop {
        display: none !important;
    }
    .ed-tab-btn .tab-text-mobile {
        display: inline !important;
    }
    .ed-tab-btn i {
        font-size: 0.88rem !important;
        flex-shrink: 0 !important;
    }
    .ed-tab-count {
        padding: 1px 6px !important;
        font-size: 0.72rem !important;
        border-radius: 10px !important;
        flex-shrink: 0 !important;
    }

    /* مشغل الفيديو وشريط التحكم */
    .ed-smart-player-bar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
        padding: 10px 12px !important;
        box-sizing: border-box !important;
    }
    .speed-buttons-group {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        gap: 6px !important;
        flex-wrap: nowrap !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }
    .speed-control-lbl {
        font-size: 0.74rem !important;
        font-weight: 800 !important;
        color: #64748b !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }
    .speed-buttons-cluster {
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
        flex: 1 !important;
        justify-content: flex-end !important;
    }
    .speed-btn {
        padding: 5px 6px !important;
        font-size: 0.72rem !important;
        flex: 1 1 auto !important;
        max-width: 48px !important;
        text-align: center !important;
        box-sizing: border-box !important;
        border-radius: 6px !important;
    }
    .ed-player-action-pills {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 6px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .ed-player-action-pills .btn-toggle-notes {
        width: 100% !important;
        justify-content: center !important;
        text-align: center !important;
        box-sizing: border-box !important;
        padding: 7px 4px !important;
        font-size: 0.74rem !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* لوحة تدوين الملاحظات بالتوقيت على الموبايل */
    .video-notes-panel > div:first-child {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
    }
    .video-notes-panel input {
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .video-notes-panel button {
        width: 100% !important;
        justify-content: center !important;
    }

    /* معلومات الدرس وزر أوفلاين والملزمة */
    .ed-video-info-box {
        padding: 14px 12px;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    .ed-vtitle {
        font-size: 1.05rem;
    }
    .ed-video-info-box > div:last-child {
        width: 100%;
        flex-direction: column;
        align-items: stretch !important;
        gap: 8px !important;
    }
    .ed-offline-action-wrapper {
        width: 100%;
    }
    .ed-btn-offline-card {
        width: 100%;
        justify-content: center;
        padding: 10px 14px;
        box-sizing: border-box;
    }
    .ed-btn-lecture-pdf {
        width: 100%;
        justify-content: center;
        padding: 9px 14px;
        box-sizing: border-box;
    }
    .ed-btn-lecture-video-download {
        width: 100%;
        justify-content: center;
        box-sizing: border-box;
    }

    /* بطاقات الاختبارات */
    .ed-exam-card {
        padding: 14px 12px;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    .ed-exam-card > div:first-child {
        width: 100%;
    }
    .ed-exam-card .ed-btn-royal {
        width: 100%;
        justify-content: center;
    }

    /* حالة الفراغ */
    .ed-classic-empty-state {
        padding: 32px 16px;
    }
    .ed-empty-title {
        font-size: 1.15rem;
    }
    .ed-empty-desc {
        font-size: 0.82rem;
    }
    .ed-empty-cta {
        flex-direction: column;
        width: 100%;
    }
    .ed-empty-cta .ed-btn-royal {
        width: 100%;
        justify-content: center;
    }

    @media (max-width: 420px) {
        .ed-tab-btn {
            font-size: 0.76rem !important;
            padding: 8px 2px !important;
            gap: 3px !important;
        }
        .ed-tab-btn i {
            font-size: 0.8rem !important;
        }
        .ed-tab-count {
            padding: 1px 4px !important;
            font-size: 0.68rem !important;
        }
        .speed-control-lbl {
            font-size: 0.7rem !important;
        }
        .speed-btn {
            padding: 4px 2px !important;
            font-size: 0.68rem !important;
        }
        .ed-player-action-pills .btn-toggle-notes {
            font-size: 0.7rem !important;
            padding: 6px 2px !important;
            gap: 2px !important;
        }
        .ed-player-action-pills .btn-toggle-notes i {
            font-size: 0.72rem !important;
        }
    }
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
                    <span class="tab-text-desktop">{{ __('الدروس والحصص المرئية') }}</span>
                    <span class="tab-text-mobile">{{ __('الدروس والحصص') }}</span>
                    <span class="ed-tab-count">{{ $videos->count() }}</span>
                </button>
                <button type="button" id="tabBtnExams" class="ed-tab-btn" onclick="switchSubjectTab('exams')">
                    <i class="fa-solid fa-file-signature"></i>
                    <span class="tab-text-desktop">{{ __('بنك الامتحانات الإلكترونية') }}</span>
                    <span class="tab-text-mobile">{{ __('بنك الامتحانات') }}</span>
                    <span class="ed-tab-count">{{ $exams->count() }}</span>
                </button>
            </div>

            {{-- 1. تبويب الدروس والحصص المرئية --}}
            <div id="tabContentVideos">
                @if($videos->count() > 0)
                    @foreach($videos as $video)
                        @if($video->is_unlocked)
                            <article class="ed-video-card" id="card_video_{{ $video->id }}">
                                <div class="ed-player-frame" id="player_frame_{{ $video->id }}">
                                    @php
                                        $rawUrl = trim($video->url_path ?? '');
                                        $ytEmbed = $video->youtube_embed_url;
                                        $isYt = !empty($ytEmbed) || str_contains($rawUrl, 'youtube.com') || str_contains($rawUrl, 'youtu.be');
                                        if ($isYt) {
                                            if (empty($ytEmbed) && !empty($rawUrl)) {
                                                if (preg_match('/(?:v=|youtu\.be\/|embed\/|shorts\/|live\/)([a-zA-Z0-9_\-]{11})/', $rawUrl, $ym)) {
                                                    $ytEmbed = 'https://www.youtube-nocookie.com/embed/' . $ym[1] . '?enablejsapi=1&rel=0&modestbranding=1&iv_load_policy=3&controls=0&showinfo=0&fs=0&disablekb=1&playsinline=1';
                                                } else {
                                                    $ytEmbed = str_replace(['watch?v=', 'youtube.com/embed/'], ['embed/', 'youtube-nocookie.com/embed/'], $rawUrl);
                                                }
                                            }
                                            if (!empty($ytEmbed)) {
                                                if (!str_contains($ytEmbed, 'enablejsapi=1')) {
                                                    $ytEmbed .= (str_contains($ytEmbed, '?') ? '&' : '?') . 'enablejsapi=1&rel=0&modestbranding=1&iv_load_policy=3&controls=0&showinfo=0&fs=0&disablekb=1&playsinline=1';
                                                }
                                                $ytEmbed = str_replace(['https://www.youtube.com/embed/', 'http://www.youtube.com/embed/'], 'https://www.youtube-nocookie.com/embed/', $ytEmbed);
                                                $ytEmbed = str_replace(['controls=1', 'fs=1'], ['controls=0', 'fs=0'], $ytEmbed);
                                            }
                                        }
                                        $isDirectVideo = (bool) preg_match('/\.(mp4|webm|ogg|mov|m4v|mkv)($|\?)/i', $rawUrl) || str_contains($rawUrl, 'educational/videos') || (!empty($rawUrl) && !str_contains($rawUrl, 'youtube') && !str_contains($rawUrl, 'youtu.be'));
                                        $directVideoUrl = $isDirectVideo 
                                            ? \App\Support\MediaHelper::videoStreamUrl($rawUrl)
                                            : null;
                                        $directVideoFallback = $isDirectVideo
                                            ? \App\Support\MediaHelper::url($rawUrl)
                                            : null;
                                    @endphp

                                    @if(!empty($ytEmbed))
                                        <div class="ed-yt-shield-container" id="shield_wrap_{{ $video->id }}" oncontextmenu="event.preventDefault(); return false;">
                                            <iframe id="player_yt_{{ $video->id }}" 
                                                    src="{{ $ytEmbed }}" 
                                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                                                    sandbox="allow-scripts allow-same-origin allow-presentation allow-forms"
                                                    loading="lazy"
                                                    style="position: absolute; inset: 0; width: 100%; height: 100%; border: none; pointer-events: none !important; z-index: 1;">
                                            </iframe>

                                            {{-- درع الشاشة التفاعلي: النقر على الفيديو يشغل ويوقف بسلاسة تامة دون أي وصول ليوتيوب --}}
                                            <div class="ed-student-screen-shield" onclick="toggleStudentPlayback('{{ $video->id }}')" title="{{ __('انقر للتشغيل / الإيقاف المؤقت') }}" style="z-index: 20;">
                                                <div class="ed-center-play-circle" id="center_play_{{ $video->id }}">
                                                    <i class="fa-solid fa-play"></i>
                                                </div>
                                            </div>

                                            {{-- دروع حماية إضافية تمنع النقر أو استخراج الروابط من الأركان والعنوان --}}
                                            <div class="ed-shield-corner-bl" onclick="toggleStudentPlayback('{{ $video->id }}')"></div>
                                            <div class="ed-shield-corner-br" onclick="toggleStudentPlayback('{{ $video->id }}')"></div>
                                            <div class="ed-shield-top-band" onclick="toggleStudentPlayback('{{ $video->id }}')"></div>

                                            {{-- شريط تحكم داخلي أنيق خاص بالمنصة مدمج في مشغل الفيديو --}}
                                            <div class="ed-student-player-bar" id="bar_wrap_{{ $video->id }}" oncontextmenu="event.preventDefault(); return false;">
                                                <button type="button" class="btn-ctrl-action" id="bar_btn_{{ $video->id }}" onclick="toggleStudentPlayback('{{ $video->id }}')" title="{{ __('تشغيل / إيقاف مؤقت') }}">
                                                    <i class="fa-solid fa-play"></i>
                                                </button>
                                                <button type="button" class="btn-ctrl-action" onclick="seekStudentRelative('{{ $video->id }}', -10)" title="{{ __('تأخير 10 ثوانٍ') }}">
                                                    <i class="fa-solid fa-rotate-left"></i>
                                                </button>
                                                <button type="button" class="btn-ctrl-action" onclick="seekStudentRelative('{{ $video->id }}', 10)" title="{{ __('تقديم 10 ثوانٍ') }}">
                                                    <i class="fa-solid fa-rotate-right"></i>
                                                </button>
                                                <span class="ctrl-time-text" id="time_txt_{{ $video->id }}">00:00 / 00:00</span>
                                                <input type="range" class="ctrl-seek-range" id="seek_range_{{ $video->id }}" min="0" max="100" step="0.1" value="0" oninput="seekStudentAbsolute('{{ $video->id }}', this.value)">
                                                <button type="button" class="btn-ctrl-action" id="mute_btn_{{ $video->id }}" onclick="toggleStudentMute('{{ $video->id }}')" title="{{ __('كتم / تشغيل الصوت') }}">
                                                    <i class="fa-solid fa-volume-high"></i>
                                                </button>
                                                <button type="button" class="btn-ctrl-action" onclick="togglePlatformFullscreen('{{ $video->id }}')" title="{{ __('تكبير العرض بملء الشاشة') }}">
                                                    <i class="fa-solid fa-expand"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @elseif($isDirectVideo && $directVideoUrl)
                                        <video id="player_{{ $video->id }}" controls preload="metadata" playsinline controlsList="nodownload noplaybackrate" oncontextmenu="return false;" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; background: #090d16;">
                                            <source src="{{ $directVideoUrl }}" type="video/mp4">
                                            @if($directVideoFallback && $directVideoFallback !== $directVideoUrl)
                                                <source src="{{ $directVideoFallback }}" type="video/mp4">
                                            @endif
                                            {{ __('متصفحك لا يدعم مشغل الفيديو.') }}
                                        </video>
                                    @elseif(!empty($rawUrl) && filter_var($rawUrl, FILTER_VALIDATE_URL))
                                        <iframe id="player_ext_{{ $video->id }}" src="{{ $rawUrl }}" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" loading="lazy" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none; pointer-events: none !important;"></iframe>
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

                                @php
                                    $hasOfflineMp4 = \App\Services\OfflineVideoManager::hasLocalMp4($video);
                                    $isYt = !empty($video->youtube_id);
                                    $canDownloadOffline = ($isDirectVideo && ($directVideoUrl || !empty($video->url_path))) || $isYt || $hasOfflineMp4;
                                @endphp

                                {{-- شريط التحكم بالسرعة والملاحظات والحفظ أوفلاين --}}
                                <div class="ed-smart-player-bar">
                                    <div class="speed-buttons-group">
                                        <span class="speed-control-lbl">
                                            <i class="fa-solid fa-gauge-high"></i> {{ __('سرعة العرض:') }}
                                        </span>
                                        <div class="speed-buttons-cluster">
                                            <button type="button" class="speed-btn" onclick="setVideoSpeed('{{ $video->id }}', 0.75, this)">0.75x</button>
                                            <button type="button" class="speed-btn active" onclick="setVideoSpeed('{{ $video->id }}', 1, this)">1x</button>
                                            <button type="button" class="speed-btn" onclick="setVideoSpeed('{{ $video->id }}', 1.25, this)">1.25x</button>
                                            <button type="button" class="speed-btn" onclick="setVideoSpeed('{{ $video->id }}', 1.5, this)">1.5x</button>
                                            <button type="button" class="speed-btn" onclick="setVideoSpeed('{{ $video->id }}', 2, this)">2x</button>
                                        </div>
                                    </div>

                                    <div class="ed-player-action-pills">
                                        @if($canDownloadOffline)
                                            <button type="button" 
                                                    class="btn-toggle-notes btn-download-quick btn-offline-quick-save" 
                                                    id="quick_offline_btn_{{ $video->id }}"
                                                    onclick="(window.StepvoroVideoDownloader || StepvoroVideoDownloader).handleAction('{{ $video->id }}')" 
                                                    title="{{ __('حفظ الدرس في ذاكرة المنصة لمشاهدته بدون إنترنت') }}">
                                                <i class="fa-solid fa-cloud-arrow-down"></i>
                                                <span id="quick_offline_lbl_{{ $video->id }}">{{ __('حفظ أوفلاين ⚡') }}</span>
                                            </button>
                                        @endif
                                        <button type="button" class="btn-toggle-notes" onclick="togglePlatformFullscreen('{{ $video->id }}')" title="{{ __('تكبير العرض بملء الشاشة') }}">
                                            <i class="fa-solid fa-expand"></i>
                                            <span>{{ __('ملء الشاشة') }}</span>
                                        </button>
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

                                {{-- بيانات المحاضرة والمرفقات الدراسية وزر التحميل أوفلاين الفاخر داخل المنصة --}}
                                <div class="ed-video-info-box">
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 6px;">
                                            <span style="background: #eff6ff; color: #1e3a8a; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                                                <i class="fa-solid fa-circle-play" style="color: #2563eb;"></i>
                                                <span>{{ __('الدرس') . ' #' . $video->order }}</span>
                                            </span>
                                            <span class="ed-stream-tag">
                                                <i class="fa-solid fa-circle-play"></i> {{ __('مشاهدة مباشرة فائقة الدقة') }}
                                            </span>
                                        </div>
                                        <h3 class="ed-vtitle">{{ $video->title }}</h3>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                        <div class="ed-offline-action-wrapper" id="offline_wrap_{{ $video->id }}">
                                            @if($canDownloadOffline)
                                                <button type="button" 
                                                        class="ed-btn-offline-card" 
                                                        id="btn_offline_{{ $video->id }}" 
                                                        data-video-id="{{ $video->id }}"
                                                        data-video-title="{{ $video->title }}"
                                                        data-subject-title="{{ $subject->name_ar ?? ($subject->name ?? 'المنهاج') }}"
                                                        data-video-url="{{ route('content.downloadVideo', $video->id) }}"
                                                        data-is-direct="1"
                                                        @if($isYt && !$hasOfflineMp4)
                                                            data-is-youtube="1"
                                                            data-prepare-url="{{ route('content.prepareOfflineVideo', $video->id) }}"
                                                        @endif
                                                        data-yt-embed="{{ $ytEmbed ?? '' }}"
                                                        data-pdf-url="{{ !empty($video->pdf_path) ? route('content.download', $video->id) : '' }}"
                                                        onclick="(window.StepvoroVideoDownloader || StepvoroVideoDownloader).handleAction('{{ $video->id }}', this)" 
                                                        title="{{ __('حفظ وتشغيل الدرس بدون إنترنت داخل المنصة') }}">
                                                    <div class="ed-offline-btn-inner">
                                                        <span class="ed-offline-btn-icon"><i class="fa-solid fa-cloud-arrow-down"></i></span>
                                                        <span class="offline-btn-label">{{ __('حفظ وتشغيل أوفلاين (داخل المنصة ⚡)') }}</span>
                                                    </div>
                                                    <div class="ed-offline-progress-track">
                                                        <div class="ed-offline-progress-fill" id="progress_fill_{{ $video->id }}"></div>
                                                    </div>
                                                </button>
                                            @elseif(!empty($video->pdf_path))
                                                <button type="button" 
                                                        class="ed-btn-offline-card" 
                                                        id="btn_offline_{{ $video->id }}" 
                                                        data-video-id="{{ $video->id }}"
                                                        data-video-title="{{ $video->title }}"
                                                        data-subject-title="{{ $subject->name_ar ?? ($subject->name ?? 'المنهاج') }}"
                                                        data-video-url=""
                                                        data-is-direct="0"
                                                        data-yt-embed="{{ $ytEmbed ?? '' }}"
                                                        data-pdf-url="{{ route('content.download', $video->id) }}"
                                                        onclick="(window.StepvoroVideoDownloader || StepvoroVideoDownloader).handleAction('{{ $video->id }}', this)" 
                                                        title="{{ __('حفظ ملزمة وأوراق عمل الدرس بدون إنترنت') }}"
                                                        style="background: #fef2f2; border-color: #fecaca; color: #b91c1c;">
                                                    <div class="ed-offline-btn-inner">
                                                        <span class="ed-offline-btn-icon"><i class="fa-solid fa-file-pdf"></i></span>
                                                        <span class="offline-btn-label">{{ __('حفظ ملزمة الدرس أوفلاين (PDF ⚡)') }}</span>
                                                    </div>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- ملزمة وملازم الدرس PDF المعتمدة للدراسة والطباعة --}}
                                        @if(!empty($video->pdf_path))
                                            <a href="{{ route('content.download', $video->id) }}" class="ed-btn-lecture-pdf" title="{{ __('تحميل ملزمة / أوراق عمل المحاضرة (PDF)') }}">
                                                <i class="fa-solid fa-file-pdf"></i>
                                                <span>{{ __('تحميل ملزمة المحاضرة (PDF)') }}</span>
                                            </a>
                                        @endif
                                    </div>
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
                            $isUpcoming = $exam->isUpcoming();
                            $isExpired = $exam->isExpired();
                            $tb = $exam->timing_badge_data;
                        @endphp
                        <div class="ed-exam-card {{ $isSolved ? 'solved' : '' }}">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div class="ed-exam-icon {{ $isSolved ? 'completed' : ($isExpired ? 'expired' : ($isUpcoming ? 'upcoming' : 'pending')) }}">
                                    <i class="fa-solid {{ $isSolved ? 'fa-circle-check' : ($isExpired ? 'fa-lock' : ($isUpcoming ? 'fa-clock' : 'fa-file-signature')) }}"></i>
                                </div>
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 6px;">
                                        <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a;">{{ $exam->title }}</h3>
                                        <span class="badge-timing-schedule {{ $tb['status'] === 'upcoming' ? 'upcoming' : ($tb['status'] === 'expired' ? 'expired' : ($tb['status'] === 'active_limited' ? 'active-limited' : 'always-open')) }}" style="font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 6px;">
                                            <i class="{{ $tb['icon'] }}"></i> {{ $tb['label'] }}
                                        </span>
                                    </div>
                                    <div style="display: flex; gap: 12px; align-items: center; font-size: 0.78rem; color: #64748b; font-weight: 700; flex-wrap: wrap;">
                                        <span><i class="fa-regular fa-clock"></i> {{ $exam->duration_minutes }} {{ __('دقيقة') }}</span>
                                        <span>•</span>
                                        <span><i class="fa-solid fa-list-check"></i> {{ $exam->questions_count }} {{ __('أسئلة') }}</span>
                                        <span>•</span>
                                        <span style="color: #b45309;"><i class="fa-solid fa-star"></i> {{ $exam->total_grade ?? 100 }} {{ __('علامة') }}</span>
                                        <span>•</span>
                                        <span style="color: #1e3a8a; font-weight: 800; background: #eff6ff; padding: 2px 8px; border-radius: 6px; border: 1px solid #bfdbfe;">
                                            <i class="fa-regular fa-calendar-check"></i> {{ __('ساعات وموعد الفتح:') }} {{ $exam->formatted_timing_text }}
                                        </span>
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
                                @elseif($isUpcoming)
                                    <button type="button" class="ed-btn-royal secondary" disabled style="opacity: 0.7; cursor: not-allowed; font-size: 0.82rem;">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>{{ __('لم يبدأ بعد') }}</span>
                                    </button>
                                @elseif($isExpired)
                                    <button type="button" class="ed-btn-royal secondary" disabled style="opacity: 0.6; cursor: not-allowed; font-size: 0.82rem;">
                                        <i class="fa-solid fa-lock"></i>
                                        <span>{{ __('انتهى موعد الاختبار') }}</span>
                                    </button>
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
                            {{ __('سيتم نشر الاختبارات والتقييمات الخاصة بهذا المقرر فور اعتمادها من مدرس المساق.') }}
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

function togglePlatformFullscreen(videoId) {
    const frame = document.getElementById(`player_frame_${videoId}`);
    if (!frame) return;
    if (!document.fullscreenElement && !document.webkitFullscreenElement) {
        if (frame.requestFullscreen) {
            frame.requestFullscreen();
        } else if (frame.webkitRequestFullscreen) {
            frame.webkitRequestFullscreen();
        } else if (frame.msRequestFullscreen) {
            frame.msRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
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

// YouTube API & Video Management (مشغل المنصة الآمن كلياً)
let ytPlayers = {};
let studentProgressIntervals = {};

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
                        updateStudentTimeDisplay(videoId);
                    },
                    'onStateChange': function(event) {
                        handleYtStateChange(videoId, event.data);
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

function handleYtStateChange(videoId, state) {
    const centerBtn = document.getElementById(`center_play_${videoId}`);
    const barBtn = document.getElementById(`bar_btn_${videoId}`);
    
    // 1 = PLAYING
    if (state === 1) {
        if (centerBtn) centerBtn.style.opacity = '0';
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
        startStudentProgressTracking(videoId);
    } else { // 2 = PAUSED, 0 = ENDED, etc.
        if (centerBtn) {
            centerBtn.style.opacity = '1';
            centerBtn.innerHTML = (state === 0) 
                ? '<i class="fa-solid fa-rotate-right"></i>' 
                : '<i class="fa-solid fa-play"></i>';
        }
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
        stopStudentProgressTracking(videoId);
    }
}

function toggleStudentPlayback(videoId) {
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.getPlayerState === 'function') {
        try {
            const state = yt.getPlayerState();
            if (state === 1) {
                yt.pauseVideo();
            } else {
                yt.playVideo();
            }
            return;
        } catch(e) {}
    }
    
    const iframe = document.getElementById(`player_yt_${videoId}`);
    if (iframe && iframe.contentWindow) {
        const centerBtn = document.getElementById(`center_play_${videoId}`);
        const isPlaying = centerBtn && centerBtn.style.opacity === '0';
        iframe.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: isPlaying ? 'pauseVideo' : 'playVideo',
            args: []
        }), '*');
        if (centerBtn) centerBtn.style.opacity = isPlaying ? '1' : '0';
        const barBtn = document.getElementById(`bar_btn_${videoId}`);
        if (barBtn) barBtn.innerHTML = isPlaying ? '<i class="fa-solid fa-play"></i>' : '<i class="fa-solid fa-pause"></i>';
        if (!isPlaying) startStudentProgressTracking(videoId); else stopStudentProgressTracking(videoId);
    }
}

// التوافقية مع أي استدعاء قديم
function toggleYtPlayback(videoId) { toggleStudentPlayback(videoId); }
function resumeYtPlayback(videoId) { toggleStudentPlayback(videoId); }

function seekStudentRelative(videoId, secondsOffset) {
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.getCurrentTime === 'function' && typeof yt.getDuration === 'function') {
        try {
            const cur = yt.getCurrentTime() || 0;
            const dur = yt.getDuration() || 0;
            const target = Math.max(0, Math.min(dur, cur + secondsOffset));
            yt.seekTo(target, true);
            yt.playVideo();
            return;
        } catch(e) {}
    }
    const cur = getVideoCurrentTime(videoId);
    seekVideoTo(videoId, Math.max(0, cur + secondsOffset));
}

function seekStudentAbsolute(videoId, percentage) {
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.getDuration === 'function') {
        try {
            const dur = yt.getDuration() || 0;
            const targetSec = (percentage / 100) * dur;
            yt.seekTo(targetSec, true);
            return;
        } catch(e) {}
    }
    const iframe = document.getElementById(`player_yt_${videoId}`);
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: 'seekTo',
            args: [parseFloat(percentage), true]
        }), '*');
    }
}

function toggleStudentMute(videoId) {
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    const muteBtn = document.getElementById(`mute_btn_${videoId}`);
    if (yt && typeof yt.isMuted === 'function') {
        try {
            if (yt.isMuted()) {
                yt.unMute();
                if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
            } else {
                yt.mute();
                if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
            }
        } catch(e) {}
    }
}

function formatDurationSec(sec) {
    if (!sec || isNaN(sec)) return '00:00';
    sec = Math.floor(sec);
    const m = Math.floor(sec / 60);
    const s = sec % 60;
    return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
}

function updateStudentTimeDisplay(videoId) {
    const yt = ytPlayers[videoId];
    if (yt && typeof yt.getCurrentTime === 'function' && typeof yt.getDuration === 'function') {
        try {
            const cur = yt.getCurrentTime() || 0;
            const dur = yt.getDuration() || 0;
            const timeTxt = document.getElementById(`time_txt_${videoId}`);
            const seekRange = document.getElementById(`seek_range_${videoId}`);
            if (timeTxt) timeTxt.textContent = `${formatDurationSec(cur)} / ${formatDurationSec(dur)}`;
            if (seekRange && dur > 0) {
                seekRange.value = (cur / dur) * 100;
            }
        } catch(e) {}
    }
}

function startStudentProgressTracking(videoId) {
    stopStudentProgressTracking(videoId);
    studentProgressIntervals[videoId] = setInterval(() => {
        updateStudentTimeDisplay(videoId);
    }, 500);
}

function stopStudentProgressTracking(videoId) {
    if (studentProgressIntervals[videoId]) {
        clearInterval(studentProgressIntervals[videoId]);
        delete studentProgressIntervals[videoId];
    }
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

// منع القائمة المنبثقة بالزر الأيمن على مشغل الفيديو نهائياً
document.addEventListener('contextmenu', function(e) {
    if (e.target.closest('.ed-player-frame, .ed-yt-shield-container, .ed-student-player-bar, iframe')) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
}, true);

// فحص حالة كافة الفيديوهات في الذاكرة المحلية لتفعيل المشغل أوفلاين
function initSubjectOfflineCheck(attempt = 0) {
    const downloader = window.StepvoroVideoDownloader || (typeof StepvoroVideoDownloader !== 'undefined' ? StepvoroVideoDownloader : null);
    if (!downloader) {
        if (attempt < 10) setTimeout(() => initSubjectOfflineCheck(attempt + 1), 150);
        return;
    }
    document.querySelectorAll('[id^="btn_offline_"]').forEach(function(btn) {
        var vidId = btn.getAttribute('data-video-id') || btn.id.replace('btn_offline_', '');
        if (vidId) {
            downloader.checkAndInitLessonPlayer(vidId);
        }
    });
}
initSubjectOfflineCheck();
</script>
<script src="https://www.youtube.com/iframe_api"></script>
@endsection
