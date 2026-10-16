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

/* 5. بطاقات الفيديو الأكاديمية الاحترافية */
.ed-video-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    margin-bottom: 28px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.ed-video-card:hover {
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}

.ed-player-frame {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #020617;
    overflow: hidden !important;
    border-radius: 20px 20px 0 0;
    user-select: none;
    -webkit-user-select: none;
}

.ed-player-frame:fullscreen, 
.ed-player-frame:-webkit-full-screen {
    width: 100vw !important;
    height: 100vh !important;
    aspect-ratio: auto !important;
    background: #000 !important;
}

/* حاوية مشغل يوتيوب بالتكبير الذكي لحجب العناوين وأزرار يوتيوب بالكامل دون تشويه */
.ed-yt-frame-container {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    transform: translate(-50%, -50%) scale(1.36);
    transform-origin: center center;
    background: #020617;
    overflow: hidden;
    pointer-events: none;
    z-index: 1;
}

.ed-video-iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
    pointer-events: none !important;
}

.ed-native-video {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #020617;
    z-index: 1;
}

/* درع النقر الشفاف التفاعلي */
.ed-clean-click-shield {
    position: absolute;
    inset: 0;
    z-index: 18;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
}

.ed-pulse-icon {
    width: 66px;
    height: 66px;
    border-radius: 50%;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.4);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    opacity: 0;
    transform: scale(0.7);
    transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    pointer-events: none;
}

.ed-clean-click-shield.show-pulse .ed-pulse-icon {
    opacity: 1;
    transform: scale(1);
}

/* غلاف البدء الملكي لمنع ظهور زر يوتيوب الأحمر */
.ed-cover-start-banner {
    position: absolute;
    inset: 0;
    z-index: 28;
    background: radial-gradient(circle at center, rgba(15, 23, 42, 0.92) 0%, rgba(2, 6, 23, 0.98) 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 24px;
    text-align: center;
    transition: opacity 0.35s ease, visibility 0.35s ease;
}

.ed-cover-start-banner.is-hidden {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
}

.ed-cover-play-glow {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.8rem;
    box-shadow: 0 0 35px rgba(37, 99, 235, 0.65);
    margin-bottom: 14px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border: 2px solid rgba(255, 255, 255, 0.35);
}

.ed-cover-start-banner:hover .ed-cover-play-glow {
    transform: scale(1.12);
    box-shadow: 0 0 50px rgba(37, 99, 235, 0.95);
}

.ed-cover-title {
    color: #ffffff;
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0 0 6px;
    max-width: 520px;
    line-height: 1.4;
}

.ed-cover-sub {
    color: #94a3b8;
    font-size: 0.82rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* شريط تحكم المنصة المدمج والفخم */
.ed-platform-controls-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(2, 6, 23, 0.75) 25%, rgba(2, 6, 23, 0.95) 100%);
    padding: 14px 18px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 25;
    direction: ltr;
    opacity: 1;
    visibility: visible;
    transition: opacity 0.35s ease, visibility 0.35s ease, transform 0.35s ease;
}

.ed-player-frame.is-playing.user-idle .ed-platform-controls-bar {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transform: translateY(8px);
}

.ed-player-frame.is-playing.user-idle {
    cursor: none;
}

.ed-ctrl-btn {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 0.95rem;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.15s, color 0.15s, transform 0.1s;
}

.ed-ctrl-btn:hover {
    background: rgba(255, 255, 255, 0.18);
    color: #38bdf8;
}

.ed-ctrl-btn:active {
    transform: scale(0.92);
}

.ed-ctrl-time {
    font-family: monospace;
    font-size: 0.78rem;
    font-weight: 700;
    color: #cbd5e1;
    white-space: nowrap;
    flex-shrink: 0;
    user-select: none;
}

.ed-ctrl-seek-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    position: relative;
    padding: 4px 0;
}

.ed-ctrl-seeker {
    width: 100%;
    height: 5px;
    -webkit-appearance: none;
    appearance: none;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 4px;
    outline: none;
    cursor: pointer;
    transition: height 0.15s ease;
}

.ed-ctrl-seeker:hover {
    height: 7px;
}

.ed-ctrl-seeker::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: #38bdf8;
    box-shadow: 0 0 8px rgba(56, 189, 248, 0.8);
    cursor: pointer;
    transition: transform 0.1s;
}

.ed-volume-cluster {
    display: flex;
    align-items: center;
    gap: 4px;
}

.ed-volume-range {
    width: 55px;
    height: 4px;
    -webkit-appearance: none;
    appearance: none;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
    outline: none;
    cursor: pointer;
}

.ed-volume-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #ffffff;
    cursor: pointer;
}

/* قائمة الجودات المتعددة */
.ed-quality-dropdown-wrap {
    position: relative;
    flex-shrink: 0;
}

.ed-quality-btn {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 7px;
    font-size: 0.74rem;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.18s;
}

.ed-quality-btn:hover {
    background: rgba(37, 99, 235, 0.35);
    border-color: #38bdf8;
    color: #38bdf8;
}

.ed-quality-menu {
    position: absolute;
    bottom: calc(100% + 10px);
    right: 0;
    min-width: 180px;
    background: rgba(15, 23, 42, 0.96);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 12px;
    padding: 6px;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.65);
    z-index: 50;
    direction: rtl;
    animation: edFadeMenu 0.18s ease;
}

@keyframes edFadeMenu {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.ed-quality-title {
    font-size: 0.72rem;
    font-weight: 800;
    color: #94a3b8;
    padding: 6px 10px 4px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    margin-bottom: 4px;
}

.ed-quality-item {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 10px;
    border: none;
    background: transparent;
    color: #e2e8f0;
    font-size: 0.78rem;
    font-weight: 700;
    border-radius: 7px;
    cursor: pointer;
    transition: 0.15s;
    text-align: right;
}

.ed-quality-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

.ed-quality-item.active {
    background: rgba(37, 99, 235, 0.28);
    color: #38bdf8;
    font-weight: 800;
}

.ed-quality-item i {
    font-size: 0.75rem;
    opacity: 0;
}

.ed-quality-item.active i {
    opacity: 1;
}

/* Fullscreen Controls Adjustments */
.ed-player-frame:fullscreen .ed-platform-controls-bar,
.ed-player-frame:-webkit-full-screen .ed-platform-controls-bar {
    position: fixed;
    bottom: 24px;
    left: 40px;
    right: 40px;
    height: 56px;
    padding: 0 22px;
    border-radius: 16px;
    background: rgba(15, 23, 42, 0.92);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    z-index: 999999;
}

/* علامة مائية ذكية متحركة بالاسم ورقم الهاتف لحماية الملكية الأكاديمية */
.ed-floating-security-watermark {
    position: absolute;
    pointer-events: none !important;
    user-select: none;
    -webkit-user-select: none;
    z-index: 15;
    padding: 4px 10px;
    border-radius: 6px;
    color: rgba(255, 255, 255, 0.24);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.9);
    background: rgba(0, 0, 0, 0.08);
    animation: edDriftingWatermark 36s ease-in-out infinite alternate;
}

@keyframes edDriftingWatermark {
    0%   { top: 8%; left: 8%; opacity: 0.2; }
    25%  { top: 76%; left: 12%; opacity: 0.28; }
    50%  { top: 12%; left: 74%; opacity: 0.22; }
    75%  { top: 80%; left: 66%; opacity: 0.28; }
    100% { top: 46%; left: 42%; opacity: 0.24; }
}

/* جسم البطاقة السفلي الأنيق */
.ed-card-content-wrap {
    padding: 22px 24px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
}

/* شريط الرأس والبادجات */
.ed-card-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 12px;
}

.ed-badges-cluster {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.ed-lesson-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #dbeafe;
    padding: 5px 12px;
    border-radius: 9999px;
    font-size: 0.78rem;
    font-weight: 800;
}

.ed-subject-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
    padding: 5px 12px;
    border-radius: 9999px;
    font-size: 0.78rem;
    font-weight: 700;
}

.ed-hd-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 0.72rem;
    font-weight: 800;
}

.ed-actions-cluster {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.ed-btn-action-pdf,
.ed-btn-lecture-pdf {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 7px 14px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 800;
    text-decoration: none;
    transition: all 0.2s ease;
}

.ed-btn-action-pdf:hover,
.ed-btn-lecture-pdf:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
}

.ed-btn-action-notes,
.btn-toggle-notes {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f8fafc;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 7px 13px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.ed-btn-action-notes:hover,
.btn-toggle-notes:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

.ed-btn-action-fs {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.ed-btn-action-fs:hover {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}

/* عنوان الحصة المعتمد */
.ed-card-lesson-title {
    margin: 6px 0 16px;
    font-size: 1.22rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.45;
}

/* شريط التحكم بالسرعة التفاعلي Segmented */
.ed-speed-control-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 14px;
    border-top: 1px dashed #e2e8f0;
}

.ed-speed-label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #64748b;
}

.ed-speed-segmented {
    display: inline-flex;
    align-items: center;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 3px;
    gap: 3px;
}

.ed-speed-opt,
.speed-btn {
    background: transparent;
    border: none;
    border-radius: 7px;
    padding: 5px 11px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
}

.ed-speed-opt:hover,
.speed-btn:hover {
    color: #0f172a;
    background: rgba(255, 255, 255, 0.6);
}

.ed-speed-opt.active,
.speed-btn.active {
    background: #2563eb;
    color: #ffffff;
    font-weight: 800;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
}

.ed-btn-lecture-video-download,
[data-download-video-external] {
    display: none !important;
}

video::-internal-media-controls-download-button {
    display: none !important;
}

video::-webkit-media-controls-download-button {
    display: none !important;
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
                                {{-- إطار مشغل الفيديو الفاخر المحمي --}}
                                <div class="ed-player-frame" id="player_frame_{{ $video->id }}" onmousemove="registerPlayerActivity('{{ $video->id }}')" ontouchstart="registerPlayerActivity('{{ $video->id }}')">
                                    @php
                                        $rawUrl = trim($video->url_path ?? '');
                                        if (in_array(strtolower($rawUrl), ['undefined', 'null', 'none', 'false', '0', 'undefined.mp4', 'null.mp4'])) {
                                            $rawUrl = '';
                                        }
                                        $ytEmbed = $video->youtube_embed_url;
                                        $isYt = !empty($ytEmbed) || (!empty($rawUrl) && (str_contains($rawUrl, 'youtube.com') || str_contains($rawUrl, 'youtu.be')));
                                        if ($isYt) {
                                            if (empty($ytEmbed) && !empty($rawUrl)) {
                                                if (preg_match('/(?:v=|youtu\.be\/|embed\/|shorts\/|live\/)([a-zA-Z0-9_\-]{11})/', $rawUrl, $ym)) {
                                                    $ytEmbed = 'https://www.youtube-nocookie.com/embed/' . $ym[1] . '?enablejsapi=1&rel=0&modestbranding=1&iv_load_policy=3&controls=0&showinfo=0&fs=0&disablekb=1&playsinline=1';
                                                } else {
                                                    $ytEmbed = str_replace(['watch?v=', 'youtube.com/embed/'], ['embed/', 'youtube-nocookie.com/embed/'], $rawUrl);
                                                }
                                            }
                                            if (!empty($ytEmbed)) {
                                                $ytEmbed = str_replace(['controls=1', 'fs=1'], ['controls=0', 'fs=0'], $ytEmbed);
                                                if (!str_contains($ytEmbed, 'enablejsapi=1')) {
                                                    $ytEmbed .= (str_contains($ytEmbed, '?') ? '&' : '?') . 'enablejsapi=1&rel=0&modestbranding=1&iv_load_policy=3&controls=0&showinfo=0&fs=0&disablekb=1&playsinline=1';
                                                }
                                                $ytEmbed = str_replace(['https://www.youtube.com/embed/', 'http://www.youtube.com/embed/'], 'https://www.youtube-nocookie.com/embed/', $ytEmbed);
                                            }
                                        }
                                        $isDirectVideo = !empty($rawUrl) && (
                                            (bool) preg_match('/\.(mp4|webm|ogg|mov|m4v|mkv)($|\?)/i', $rawUrl)
                                            || str_contains($rawUrl, 'educational/videos')
                                            || (!str_contains($rawUrl, 'youtube') && !str_contains($rawUrl, 'youtu.be'))
                                        );
                                        $directVideoUrl = $isDirectVideo 
                                            ? \App\Support\MediaHelper::videoStreamUrl($rawUrl)
                                            : null;
                                        $directVideoFallback = $isDirectVideo
                                            ? \App\Support\MediaHelper::url($rawUrl)
                                            : null;
                                    @endphp

                                    @if(!empty($ytEmbed))
                                        <div class="ed-yt-frame-container" id="shield_wrap_{{ $video->id }}">
                                            @if(auth('student')->check() || auth()->check())
                                                @php
                                                    $wmUser = auth('student')->user() ?? auth()->user();
                                                    $wmText = ($wmUser->name ?? 'طالب') . ' • ' . ($wmUser->phone ?: ($wmUser->email ?: ('ID: ' . $wmUser->id)));
                                                @endphp
                                                <div class="ed-floating-security-watermark" aria-hidden="true">
                                                    <span>{{ $wmText }}</span>
                                                </div>
                                            @endif
                                            <iframe id="player_yt_{{ $video->id }}" 
                                                    src="{{ $ytEmbed }}" 
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                                    referrerpolicy="strict-origin-when-cross-origin"
                                                    loading="lazy"
                                                    class="ed-video-iframe">
                                            </iframe>
                                        </div>

                                        {{-- درع النقر التفاعلي الصافي --}}
                                        <div class="ed-clean-click-shield" id="click_shield_{{ $video->id }}" onclick="toggleStudentPlayback('{{ $video->id }}')">
                                            <div class="ed-pulse-icon" id="pulse_{{ $video->id }}">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>

                                        {{-- غلاف البداية الراقي الذي يحجب أي أزرار أو عناصر خارجية قبل التشغيل --}}
                                        <div class="ed-cover-start-banner" id="cover_{{ $video->id }}" onclick="startStudentPlaybackFirstTime('{{ $video->id }}')">
                                            <div class="ed-cover-play-glow">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                            <h4 class="ed-cover-title">{{ $video->title }}</h4>
                                            <span class="ed-cover-sub">
                                                <i class="fa-solid fa-wand-magic-sparkles" style="color: #60a5fa;"></i>
                                                {{ __('انقر للمشاهدة بأعلى جودة فائقة الدقة 1080p') }}
                                            </span>
                                        </div>

                                        {{-- شريط تحكم المنصة المدمج والفخم بنظام الاستوديو --}}
                                        <div class="ed-platform-controls-bar" id="controls_bar_{{ $video->id }}">
                                            <button type="button" class="ed-ctrl-btn" id="ctrl_play_btn_{{ $video->id }}" onclick="toggleStudentPlayback('{{ $video->id }}')" title="{{ __('تشغيل / إيقاف') }}">
                                                <i class="fa-solid fa-play"></i>
                                            </button>
                                            <button type="button" class="ed-ctrl-btn" onclick="seekStudentRelative('{{ $video->id }}', -10)" title="{{ __('تأخير 10 ثوانٍ') }}">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </button>
                                            <button type="button" class="ed-ctrl-btn" onclick="seekStudentRelative('{{ $video->id }}', 10)" title="{{ __('تقديم 10 ثوانٍ') }}">
                                                <i class="fa-solid fa-rotate-right"></i>
                                            </button>
                                            <span class="ed-ctrl-time" id="time_txt_{{ $video->id }}">00:00 / 00:00</span>
                                            <div class="ed-ctrl-seek-wrap">
                                                <input type="range" class="ed-ctrl-seeker" id="seek_range_{{ $video->id }}" min="0" max="100" step="0.1" value="0" oninput="seekStudentAbsolute('{{ $video->id }}', this.value)">
                                            </div>
                                            <div class="ed-volume-cluster">
                                                <button type="button" class="ed-ctrl-btn" id="mute_btn_{{ $video->id }}" onclick="toggleStudentMute('{{ $video->id }}')" title="{{ __('كتم / تشغيل الصوت') }}">
                                                    <i class="fa-solid fa-volume-high"></i>
                                                </button>
                                                <input type="range" class="ed-volume-range" id="vol_range_{{ $video->id }}" min="0" max="100" value="100" oninput="setStudentVolume('{{ $video->id }}', this.value)" title="{{ __('مستوى الصوت') }}">
                                            </div>

                                            {{-- قائمة الجودات المتعددة --}}
                                            <div class="ed-quality-dropdown-wrap">
                                                <button type="button" class="ed-quality-btn" onclick="toggleQualityMenu('{{ $video->id }}')" title="{{ __('تحديد دقة وجودة العرض') }}">
                                                    <i class="fa-solid fa-gear"></i>
                                                    <span id="active_quality_lbl_{{ $video->id }}">1080p HD</span>
                                                </button>
                                                <div class="ed-quality-menu" id="quality_menu_{{ $video->id }}" style="display: none;">
                                                    <div class="ed-quality-title">{{ __('دقة وجودة العرض:') }}</div>
                                                    <button type="button" class="ed-quality-item active" onclick="selectVideoQuality('{{ $video->id }}', 'hd1080', '1080p HD', this)">
                                                        <span>1080p Full HD</span>
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                    <button type="button" class="ed-quality-item" onclick="selectVideoQuality('{{ $video->id }}', 'hd720', '720p HD', this)">
                                                        <span>720p HD</span>
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                    <button type="button" class="ed-quality-item" onclick="selectVideoQuality('{{ $video->id }}', 'large', '480p SD', this)">
                                                        <span>480p SD</span>
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                    <button type="button" class="ed-quality-item" onclick="selectVideoQuality('{{ $video->id }}', 'medium', '360p', this)">
                                                        <span>360p (توفير البيانات)</span>
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                    <button type="button" class="ed-quality-item" onclick="selectVideoQuality('{{ $video->id }}', 'default', 'تلقائي', this)">
                                                        <span>تلقائي (حسب الاتصال)</span>
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <button type="button" class="ed-ctrl-btn" onclick="togglePlatformFullscreen('{{ $video->id }}')" title="{{ __('تكبير العرض بملء الشاشة') }}">
                                                <i class="fa-solid fa-expand"></i>
                                            </button>
                                        </div>
                                    @elseif($isDirectVideo && $directVideoUrl)
                                        <video id="player_{{ $video->id }}" controls preload="metadata" playsinline controlsList="nodownload noplaybackrate" disableRemotePlayback oncontextmenu="return false;" onerror="handleStudentVideoError(this, '{{ $video->id }}')" class="ed-native-video">
                                            <source src="{{ $directVideoUrl }}" type="video/mp4">
                                            @if($directVideoFallback && $directVideoFallback !== $directVideoUrl)
                                                <source src="{{ $directVideoFallback }}" type="video/mp4">
                                            @endif
                                            {{ __('متصفحك لا يدعم مشغل الفيديو.') }}
                                        </video>
                                        <div id="player_err_{{ $video->id }}" style="display: none; position: absolute; inset: 0; background: rgba(15, 23, 42, 0.95); flex-direction: column; align-items: center; justify-content: center; gap: 12px; padding: 20px; text-align: center; color: #fff; z-index: 25;">
                                            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); color: #f87171; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                                                <i class="fa-solid fa-triangle-exclamation"></i>
                                            </div>
                                            <div>
                                                <h4 id="player_err_h4_{{ $video->id }}" style="margin: 0 0 6px; font-size: 0.98rem; font-weight: 700; color: #f8fafc;">{{ __('تعذر بث الفيديو في المشغل المباشر') }}</h4>
                                                <p id="player_err_p_{{ $video->id }}" style="margin: 0; font-size: 0.82rem; color: #94a3b8; max-width: 420px; line-height: 1.5;">{{ __('يمكنك إعادة المحاولة أو فتح الفيديو عبر الرابط المباشر.') }}</p>
                                            </div>
                                            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap; justify-content: center;">
                                                <button type="button" onclick="retryStudentVideo('{{ $video->id }}')" style="background: #2563eb; color: #fff; border: none; padding: 7px 16px; border-radius: 8px; font-size: 0.82rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                                    <i class="fa-solid fa-rotate-right"></i> {{ __('إعادة المحاولة') }}
                                                </button>
                                                @if(!empty($video->pdf_path))
                                                    <a href="{{ route('content.download', $video->id) }}" style="background: #059669; color: #fff; padding: 7px 14px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                                        <i class="fa-solid fa-file-pdf"></i> {{ __('تحميل ملزمة الدرس PDF') }}
                                                    </a>
                                                @endif
                                                @if($directVideoFallback)
                                                    <a href="{{ $directVideoFallback }}" target="_blank" style="background: rgba(255,255,255,0.1); color: #cbd5e1; padding: 7px 14px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; text-decoration: none;">
                                                        {{ __('فتح الرابط المباشر') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif(!empty($rawUrl) && filter_var($rawUrl, FILTER_VALIDATE_URL))
                                        <iframe id="player_ext_{{ $video->id }}" src="{{ $rawUrl }}" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy" class="ed-video-iframe"></iframe>
                                    @else
                                        {{-- في حال لم يتم رفع فيديو بعد أو كان الدرس مخصصاً كملزمة ودوسية --}}
                                        <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); color: #f8fafc; padding: 24px; text-align: center;">
                                            @if(!empty($video->pdf_path))
                                                <i class="fa-solid fa-file-pdf" style="font-size: 3rem; color: #ef4444; margin-bottom: 12px;"></i>
                                                <h4 style="margin: 0 0 8px; font-size: 1.15rem; font-weight: 800; color: #ffffff;">{{ $video->title }}</h4>
                                                <p style="margin: 0 0 16px; font-size: 0.88rem; color: #94a3b8; max-width: 450px;">
                                                    {{ __('هذا الدرس متاح حالياً كملزمة / دوسية دراسية معتمدة قابلة للتحميل والمطالعة المباشرة.') }}
                                                </p>
                                                <a href="{{ route('content.download', $video->id) }}" class="ed-btn-royal primary" style="font-size: 0.86rem; padding: 10px 22px; text-decoration: none;">
                                                    <i class="fa-solid fa-cloud-arrow-down"></i> {{ __('تحميل ملزمة الدرس الآن') }}
                                                </a>
                                            @else
                                                <div style="width: 58px; height: 58px; border-radius: 50%; background: rgba(59, 130, 246, 0.15); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 12px; border: 1px solid rgba(96, 165, 250, 0.3);">
                                                    <i class="fa-solid fa-video-slash"></i>
                                                </div>
                                                <h4 style="margin: 0 0 8px; font-size: 1.15rem; font-weight: 800; color: #ffffff;">{{ $video->title }}</h4>
                                                <p style="margin: 0 0 14px; font-size: 0.88rem; color: #94a3b8; max-width: 460px; line-height: 1.6;">
                                                    {{ __('يجري حالياً رفع وتجهيز الفيديو المرئي عالي الدقة لهذه الحصة الدراسية من قِبل الأستاذ المعتمد للمادة.') }}
                                                </p>
                                                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'teacher', 'videographer']))
                                                    <a href="{{ route('educational_contents.edit', $video->id) }}" class="ed-btn-royal secondary" style="font-size: 0.82rem; padding: 7px 16px; text-decoration: none;">
                                                        <i class="fa-solid fa-pen-to-square"></i> {{ __('إضافة أو تعديل رابط الفيديو (لوحة الإدارة)') }}
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- جسم البطاقة: البيانات والبادجات وأزرار التحكم الفاخرة --}}
                                <div class="ed-card-content-wrap">
                                    <div class="ed-card-top-row">
                                        <div class="ed-badges-cluster">
                                            <span class="ed-lesson-pill">
                                                <i class="fa-solid fa-play"></i>
                                                <span>{{ __('الدرس') }} #{{ $video->order }}</span>
                                            </span>
                                            <span class="ed-subject-pill">
                                                <i class="fa-solid fa-graduation-cap"></i>
                                                <span>{{ $video->channel_name ?? $subject->name_ar ?? __('منهاج التوجيهي المعتمد') }}</span>
                                            </span>
                                            <span class="ed-hd-pill" id="card_hd_pill_{{ $video->id }}">
                                                <i class="fa-solid fa-circle-check"></i>
                                                <span>1080p Full HD</span>
                                            </span>
                                        </div>

                                        <div class="ed-actions-cluster">
                                            @if(!empty($video->pdf_path))
                                                <a href="{{ route('content.download', $video->id) }}" class="ed-btn-action-pdf" title="{{ __('تحميل ملزمة وأوراق عمل المحاضرة (PDF)') }}">
                                                    <i class="fa-solid fa-file-pdf"></i>
                                                    <span>{{ __('الملزمة الدراسية') }}</span>
                                                </a>
                                            @endif
                                            <button type="button" class="ed-btn-action-notes" onclick="toggleNotesSection('{{ $video->id }}')" title="{{ __('تدوين ومراجعة ملاحظاتي على الدرس') }}">
                                                <i class="fa-solid fa-bookmark"></i>
                                                <span>{{ __('ملاحظاتي') }}</span>
                                            </button>
                                            <button type="button" class="ed-btn-action-fs" onclick="togglePlatformFullscreen('{{ $video->id }}')" title="{{ __('تكبير العرض بملء الشاشة') }}">
                                                <i class="fa-solid fa-expand"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- عنوان الحصة المعتمد --}}
                                    <h3 class="ed-card-lesson-title">{{ $video->title }}</h3>

                                    {{-- شريط التحكم بالسرعة التفاعلي العصري --}}
                                    <div class="ed-speed-control-bar">
                                        <span class="ed-speed-label">
                                            <i class="fa-solid fa-gauge-high"></i>
                                            <span>{{ __('سرعة العرض:') }}</span>
                                        </span>
                                        <div class="ed-speed-segmented">
                                            <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 0.75, this)">0.75x</button>
                                            <button type="button" class="ed-speed-opt active" onclick="setVideoSpeed('{{ $video->id }}', 1, this)">1x</button>
                                            <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 1.25, this)">1.25x</button>
                                            <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 1.5, this)">1.5x</button>
                                            <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 2, this)">2x</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- قسم تدوين الملاحظات بالتوقيت --}}
                                <div class="video-notes-panel" id="notes_panel_{{ $video->id }}" style="display: none; padding: 18px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
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

let playerHideTimers = {};

let playerActivityTimers = {};

function startStudentPlaybackFirstTime(videoId) {
    const cover = document.getElementById(`cover_${videoId}`);
    if (cover) {
        cover.classList.add('is-hidden');
    }
    toggleStudentPlayback(videoId);
}

function registerPlayerActivity(videoId) {
    const frame = document.getElementById(`player_frame_${videoId}`);
    if (!frame) return;
    
    frame.classList.remove('user-idle');
    clearTimeout(playerActivityTimers[videoId]);
    
    if (frame.classList.contains('is-playing')) {
        playerActivityTimers[videoId] = setTimeout(() => {
            frame.classList.add('user-idle');
        }, 3000);
    }
}

function handlePlayerMouseLeave(videoId) {
    const frame = document.getElementById(`player_frame_${videoId}`);
    if (frame && frame.classList.contains('is-playing')) {
        frame.classList.add('user-idle');
    }
}

function handleYtStateChange(videoId, state) {
    const frame = document.getElementById(`player_frame_${videoId}`);
    const playBtn = document.getElementById(`ctrl_play_btn_${videoId}`);
    const pulse = document.getElementById(`pulse_${videoId}`);
    const shield = document.getElementById(`click_shield_${videoId}`);
    
    // 1 = PLAYING
    if (state === 1) {
        if (frame) {
            frame.classList.add('is-playing');
            frame.classList.remove('user-idle');
        }
        if (playBtn) playBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
        if (pulse) {
            pulse.innerHTML = '<i class="fa-solid fa-play"></i>';
            if (shield) {
                shield.classList.add('show-pulse');
                setTimeout(() => shield.classList.remove('show-pulse'), 500);
            }
        }
        startStudentProgressTracking(videoId);
        registerPlayerActivity(videoId);
    } else { // 2 = PAUSED, 0 = ENDED
        if (frame) {
            frame.classList.remove('is-playing', 'user-idle');
        }
        if (playBtn) playBtn.innerHTML = (state === 0) ? '<i class="fa-solid fa-rotate-right"></i>' : '<i class="fa-solid fa-play"></i>';
        if (pulse) {
            pulse.innerHTML = (state === 0) ? '<i class="fa-solid fa-rotate-right"></i>' : '<i class="fa-solid fa-pause"></i>';
            if (shield) {
                shield.classList.add('show-pulse');
                setTimeout(() => shield.classList.remove('show-pulse'), 500);
            }
        }
        stopStudentProgressTracking(videoId);
        clearTimeout(playerActivityTimers[videoId]);
    }
}

function toggleStudentPlayback(videoId) {
    const cover = document.getElementById(`cover_${videoId}`);
    if (cover && !cover.classList.contains('is-hidden')) {
        cover.classList.add('is-hidden');
    }
    
    const frame = document.getElementById(`player_frame_${videoId}`);
    
    // 1. YouTube Player
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.getPlayerState === 'function') {
        try {
            const state = yt.getPlayerState();
            if (state === 1) {
                yt.pauseVideo();
                handleYtStateChange(videoId, 2);
            } else {
                yt.playVideo();
                handleYtStateChange(videoId, 1);
            }
            return;
        } catch(e) {}
    }
    
    // 2. YouTube postMessage fallback
    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        const isPlaying = frame && frame.classList.contains('is-playing');
        ytIframe.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: isPlaying ? 'pauseVideo' : 'playVideo',
            args: []
        }), '*');
        handleYtStateChange(videoId, isPlaying ? 2 : 1);
        return;
    }
    
    // 3. HTML5 Video
    const vid = document.getElementById(`player_${videoId}`);
    if (vid) {
        if (vid.paused) {
            vid.play().catch(() => {});
            handleYtStateChange(videoId, 1);
        } else {
            vid.pause();
            handleYtStateChange(videoId, 2);
        }
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
            handleYtStateChange(videoId, 1);
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
    const volRange = document.getElementById(`vol_range_${videoId}`);
    
    if (yt && typeof yt.isMuted === 'function') {
        try {
            if (yt.isMuted()) {
                yt.unMute();
                if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
                if (volRange) volRange.value = yt.getVolume() || 100;
            } else {
                yt.mute();
                if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
                if (volRange) volRange.value = 0;
            }
            return;
        } catch(e) {}
    }
    
    const vid = document.getElementById(`player_${videoId}`);
    if (vid) {
        vid.muted = !vid.muted;
        if (muteBtn) muteBtn.innerHTML = vid.muted ? '<i class="fa-solid fa-volume-xmark"></i>' : '<i class="fa-solid fa-volume-high"></i>';
        if (volRange) volRange.value = vid.muted ? 0 : (vid.volume * 100);
    }
}

function setStudentVolume(videoId, value) {
    const val = parseInt(value, 10);
    const muteBtn = document.getElementById(`mute_btn_${videoId}`);
    
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.setVolume === 'function') {
        try {
            if (val === 0) {
                yt.mute();
            } else {
                yt.unMute();
                yt.setVolume(val);
            }
        } catch(e) {}
    }
    
    const vid = document.getElementById(`player_${videoId}`);
    if (vid) {
        vid.muted = (val === 0);
        vid.volume = val / 100;
    }
    
    if (muteBtn) {
        muteBtn.innerHTML = (val === 0) ? '<i class="fa-solid fa-volume-xmark"></i>' : '<i class="fa-solid fa-volume-high"></i>';
    }
}

function toggleQualityMenu(videoId) {
    const menu = document.getElementById(`quality_menu_${videoId}`);
    if (!menu) return;
    const isShown = (menu.style.display === 'block');
    document.querySelectorAll('.ed-quality-menu').forEach(m => m.style.display = 'none');
    menu.style.display = isShown ? 'none' : 'block';
}

function selectVideoQuality(videoId, qCode, qLabel, btnEl) {
    const menu = document.getElementById(`quality_menu_${videoId}`);
    if (menu) {
        menu.querySelectorAll('.ed-quality-item').forEach(b => b.classList.remove('active'));
        if (btnEl) btnEl.classList.add('active');
        menu.style.display = 'none';
    }
    
    const lbl = document.getElementById(`active_quality_lbl_${videoId}`);
    if (lbl) lbl.textContent = qLabel;
    
    const cardBadge = document.getElementById(`card_hd_pill_${videoId}`);
    if (cardBadge) cardBadge.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>${qLabel}</span>`;
    
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.setPlaybackQuality === 'function') {
        try {
            yt.setPlaybackQuality(qCode);
        } catch(e) {}
    }
    
    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        ytIframe.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: 'setPlaybackQuality',
            args: [qCode]
        }), '*');
    }
    
    showPlayerToast(`{{ __('تم ضبط الجودة على') }} ${qLabel} ⚡`);
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.ed-quality-dropdown-wrap')) {
        document.querySelectorAll('.ed-quality-menu').forEach(m => m.style.display = 'none');
    }
});

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
        const parent = btnElement.closest('.ed-speed-segmented, .speed-buttons-group');
        if (parent) {
            parent.querySelectorAll('.ed-speed-opt, .speed-btn').forEach(b => b.classList.remove('active'));
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
    } else {
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
    }

    const card = document.getElementById(`card_video_${videoId}`);
    if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function handleStudentVideoError(videoEl, videoId) {
    if (!videoEl) return;
    console.warn('Student video error:', videoId, videoEl.currentSrc || videoEl.src);

    setTimeout(() => {
        if (!videoEl.error && videoEl.networkState !== 3) {
            return;
        }
        const curSrc = videoEl.currentSrc || videoEl.src || '';
        const errBox = document.getElementById(`player_err_${videoId}`);
        const errH4 = document.getElementById(`player_err_h4_${videoId}`);
        const errP = document.getElementById(`player_err_p_${videoId}`);

        if (errBox) {
            errBox.style.display = 'flex';
        }

        if (curSrc) {
            fetch(curSrc, { method: 'GET', headers: { 'Accept': 'application/json' } })
                .then(res => {
                    if (res.status === 422) {
                        return res.json().then(data => {
                            if (errH4) errH4.textContent = 'ملف هذا الدرس غير مكتمل على السيرفر (0 بايت)';
                            if (errP) errP.textContent = data.message || 'عذراً، ملف هذا الدرس غير مكتمل في التخزين (حجمه 0 بايت). بإمكانك تحميل ملزمة الدرس PDF ريثما يقوم المعلم بإعادة رفع الفيديو.';
                        });
                    }
                })
                .catch(() => {});
        }
    }, 400);
}

function retryStudentVideo(videoId) {
    const errBox = document.getElementById(`player_err_${videoId}`);
    if (errBox) errBox.style.display = 'none';
    const player = document.getElementById(`player_${videoId}`);
    if (player) {
        player.load();
        player.play().catch(() => {});
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
    })
    .catch(err => {
        alert('{{ __('حدث خطأ في الاتصال أثناء حذف الملاحظة.') }}');
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

// إزالة أي أزرار تحميل خارجي للملفات الخام لضمان الحفظ حصرياً داخل المنصة
function purgeExternalDownloadButtons() {
    document.querySelectorAll('.ed-btn-lecture-video-download, a[download][href*="downloadVideo"], a[href*="force_download=1"]').forEach(el => el.remove());
}
purgeExternalDownloadButtons();
document.addEventListener('DOMContentLoaded', purgeExternalDownloadButtons);


</script>
<script src="https://www.youtube.com/iframe_api"></script>
@endsection
