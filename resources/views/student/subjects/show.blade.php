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
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 28px;
    align-items: start;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
}

.ed-layout-grid > main {
    min-width: 0;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.ed-layout-grid > aside {
    min-width: 0;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

@media (max-width: 1040px) {
    .ed-layout-grid {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }
    .ed-layout-grid > main {
        order: 1 !important;
        width: 100% !important;
    }
    .ed-layout-grid > aside {
        order: 2 !important;
        width: 100% !important;
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
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
    background: #020617;
    overflow: hidden;
}

.ed-player-frame video, 
.ed-player-frame iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

.ed-player-frame:fullscreen, 
.ed-player-frame:-webkit-full-screen,
.ed-player-frame:-moz-full-screen,
.ed-player-frame:-ms-fullscreen,
.ed-player-frame.is-platform-fullscreen {
    position: fixed !important;
    inset: 0 !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    max-width: 100vw !important;
    max-height: 100vh !important;
    padding-top: 0 !important;
    margin: 0 !important;
    border-radius: 0 !important;
    z-index: 2147483647 !important;
    background: #000000 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.ed-player-frame:fullscreen .ed-yt-frame-container,
.ed-player-frame:-webkit-full-screen .ed-yt-frame-container,
.ed-player-frame.is-platform-fullscreen .ed-yt-frame-container {
    position: absolute !important;
    inset: 0 !important;
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
    max-height: 100% !important;
}

.ed-video-card.has-fullscreen-child {
    overflow: visible !important;
    transform: none !important;
    position: static !important;
    z-index: 2147483647 !important;
}

body.has-fullscreen-player {
    overflow: hidden !important;
}

.ed-fs-exit-floating-btn {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 99999999;
    background: rgba(15, 23, 42, 0.88);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 8px 18px;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 800;
    cursor: pointer;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.6);
    transition: all 0.2s ease;
}
.ed-fs-exit-floating-btn:hover {
    background: #dc2626;
    border-color: #ef4444;
}

.ed-yt-frame-container {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    background: #000000;
    overflow: hidden;
}

.ed-yt-viewport {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background: #000000;
}

/* عرض الفيديو بالكامل مع حجب كامل لكافة شعارات وبيانات يوتيوب */
.ed-video-iframe {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: 0;
    object-fit: cover;
    background: #000000;
    z-index: 2;
}

/* فيوبورت الفيديو: عرض كامل بدون قص مع إغلاق منافذ التفاعل المباشر مع يوتيوب */
.ed-yt-viewport {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    overflow: hidden !important;
    background: #000000;
}

.ed-yt-viewport .ed-video-iframe {
    position: absolute;
    inset: 0;
    width: 100% !important;
    height: 100% !important;
    top: 0 !important;
    left: 0 !important;
    border: 0 !important;
    background: #000000 !important;
    pointer-events: none !important;
    object-fit: contain;
    z-index: 2;
    image-rendering: -webkit-optimize-contrast;
    transform: translateZ(0);
}

/* درع حماية زاوية يوتيوب لمنع أي نقرات أو تفاعل مع أي علامة مائية */
.ed-yt-corner-shield {
    display: none !important;
}

/* طبقة المشغل الأكاديمي الصافي المتقدم */
.ed-pure-player-layer {
    position: absolute;
    inset: 0;
    z-index: 15;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: opacity 0.3s ease;
    user-select: none;
    pointer-events: none;
}

.ed-player-frame.controls-hidden .ed-player-top-header,
.ed-player-frame.controls-hidden .ed-player-bottom-panel {
    opacity: 0;
    pointer-events: none;
}

.ed-player-frame.controls-hidden {
    cursor: none;
}

/* سطح النقر للتشغيل والإيقاف والتكبير: يغطي وسط الفيديو فقط دون ملامسة الشريط السفلي أو الهيدر */
.ed-click-surface {
    position: absolute;
    top: 56px;
    left: 0;
    right: 0;
    bottom: 96px;
    z-index: 10;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: auto !important;
    background: transparent;
}

.ed-center-pulse-btn {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    font-size: 1.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    pointer-events: none;
}

.ed-center-pulse-btn.show-pulse {
    opacity: 1;
    transform: scale(1);
}

.ed-player-top-header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 50;
    padding: 12px 20px;
    background: linear-gradient(to bottom, rgba(2, 6, 23, 0.96) 0%, rgba(2, 6, 23, 0.7) 65%, transparent 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    pointer-events: auto !important;
}

.ed-pth-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #ffffff;
    font-weight: 800;
    font-size: 0.95rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
}

.ed-pth-badge {
    background: #2563eb;
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 800;
}

.ed-pth-quality-tag {
    background: rgba(16, 185, 129, 0.2);
    border: 1px solid rgba(16, 185, 129, 0.4);
    color: #34d399;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 800;
    pointer-events: auto !important;
    cursor: pointer;
    transition: all 0.2s ease;
}
.ed-pth-quality-tag:hover {
    background: rgba(16, 185, 129, 0.35);
    color: #ffffff;
}

/* شريط التحكم السفلي الكامل: يعلو كافة الطبقات مع استجابة فورية ونقرات مباشرة */
.ed-player-bottom-panel {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 70;
    padding: 12px 20px 14px;
    background: linear-gradient(to top, rgba(2, 6, 23, 0.98) 0%, rgba(2, 6, 23, 0.85) 60%, transparent 100%);
    display: flex;
    flex-direction: column;
    gap: 8px;
    pointer-events: auto !important;
}

.ed-scrubber-wrapper {
    position: relative;
    width: 100%;
    height: 20px;
    display: flex;
    align-items: center;
    cursor: pointer;
    direction: ltr !important;
    touch-action: none;
    user-select: none;
    pointer-events: auto !important;
}

.ed-scrub-track {
    position: relative;
    width: 100%;
    height: 5px;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 4px;
    overflow: visible;
    transition: height 0.15s ease;
    direction: ltr !important;
}

.ed-scrubber-wrapper:hover .ed-scrub-track,
.ed-scrubber-wrapper.is-scrubbing .ed-scrub-track {
    height: 8px;
}

.ed-scrub-buffer {
    position: absolute;
    top: 0;
    left: 0;
    right: auto;
    height: 100%;
    background: rgba(255, 255, 255, 0.35);
    border-radius: 4px;
    width: 0%;
    transition: width 0.3s ease;
}

.ed-scrub-fill {
    position: absolute;
    top: 0;
    left: 0;
    right: auto;
    height: 100%;
    background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 4px;
    width: 0%;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.ed-scrub-thumb {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #ffffff;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.7);
    transform: scale(0);
    transition: transform 0.15s ease;
    margin-right: -7px;
}

.ed-scrubber-wrapper:hover .ed-scrub-thumb,
.ed-scrubber-wrapper.is-scrubbing .ed-scrub-thumb {
    transform: scale(1);
}

.ed-scrub-tooltip {
    position: absolute;
    bottom: 24px;
    background: rgba(15, 23, 42, 0.95);
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    pointer-events: none;
    display: none;
    transform: translateX(-50%);
    white-space: nowrap;
    direction: ltr !important;
    font-family: 'Outfit', sans-serif, system-ui;
    z-index: 45;
}

.ed-controls-main-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.ed-ctrl-cluster {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ed-ctrl-btn {
    background: none;
    border: none;
    color: #ffffff;
    font-size: 1.15rem;
    cursor: pointer;
    padding: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: color 0.2s ease, transform 0.15s ease, background 0.2s ease;
    line-height: 1;
}

.ed-ctrl-btn:hover {
    color: #60a5fa;
    background: rgba(255, 255, 255, 0.1);
    transform: scale(1.08);
}

.ed-ctrl-btn.play {
    font-size: 1.25rem;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
}

.ed-ctrl-btn.play:hover {
    background: #2563eb;
    color: #ffffff;
}

.ed-ctrl-btn.skip {
    position: relative;
    font-size: 1.15rem;
}

.ed-skip-num {
    font-size: 0.55rem;
    font-weight: 900;
    position: absolute;
    top: 52%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-family: sans-serif;
}

.ed-time-counter {
    color: #cbd5e1;
    font-size: 0.82rem;
    font-weight: 700;
    font-family: 'Outfit', sans-serif, system-ui;
    direction: ltr;
    letter-spacing: 0.5px;
    min-width: 95px;
}

.ed-volume-group {
    display: flex;
    align-items: center;
    gap: 6px;
}

.ed-volume-slider {
    width: 55px;
    height: 4px;
    -webkit-appearance: none;
    appearance: none;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
    outline: none;
    cursor: pointer;
}

.ed-volume-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    background: #ffffff;
    cursor: pointer;
}

.ed-speed-dropdown,
.ed-quality-dropdown {
    position: relative;
}

.ed-ctrl-pill {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
}

.ed-ctrl-pill:hover,
.ed-ctrl-pill.active {
    background: #2563eb;
    border-color: #3b82f6;
    color: #ffffff;
}

.ed-ctrl-pill.hd {
    color: #34d399;
    border-color: rgba(52, 211, 153, 0.4);
}

.ed-ctrl-pill.hd:hover {
    background: #059669;
    color: #ffffff;
}

.ed-popover-menu {
    position: absolute;
    bottom: calc(100% + 10px);
    left: 50%;
    transform: translateX(-50%);
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    padding: 8px;
    display: none;
    flex-direction: column;
    gap: 4px;
    min-width: 140px;
    box-shadow: 0 14px 35px rgba(0, 0, 0, 0.6);
    z-index: 120 !important;
    pointer-events: auto !important;
}

.ed-popover-menu.show {
    display: flex;
}

.ed-pop-header {
    font-size: 0.72rem;
    font-weight: 800;
    color: #94a3b8;
    padding: 4px 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 4px;
}

.ed-pop-opt {
    background: none;
    border: none;
    color: #e2e8f0;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    text-align: right;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ed-pop-opt:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
}

.ed-pop-opt.active {
    background: #2563eb;
    color: #ffffff;
    font-weight: 800;
}

.ed-native-video {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #020617;
    z-index: 1;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    transform: translateZ(0);
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
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
    gap: 7px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 7px 15px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.82rem;
    font-weight: 800;
    transition: all 0.2s ease;
}

.ed-btn-action-fs:hover {
    background: #1d4ed8;
    color: #ffffff;
    border-color: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(29, 78, 216, 0.25);
}

/* عنوان الحصة المعتمد */
.ed-card-lesson-title {
    margin: 6px 0 16px;
    font-size: 1.22rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.45;
}

/* بوستر الفيديو الأكاديمي الحصري (يخفي شاشات يوتيوب المبدئية وأسماء القنوات) */
.ed-video-poster {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    z-index: 25 !important;
    cursor: pointer;
    overflow: hidden;
    user-select: none;
    transition: opacity 0.35s ease, visibility 0.35s ease;
}

.ed-poster-bg-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.8);
    transition: transform 0.4s ease, filter 0.4s ease;
}

.ed-video-poster:hover .ed-poster-bg-img {
    transform: scale(1.03);
    filter: brightness(0.9);
}

.ed-poster-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, rgba(15, 23, 42, 0.4) 0%, rgba(15, 23, 42, 0.8) 100%);
}

.ed-poster-center-content {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    z-index: 2;
}

.ed-poster-play-btn {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border: none;
    color: #ffffff;
    font-size: 1.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.55), 0 0 0 10px rgba(255, 255, 255, 0.18);
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
}

.ed-video-poster:hover .ed-poster-play-btn {
    transform: scale(1.12);
    box-shadow: 0 14px 40px rgba(37, 99, 235, 0.75), 0 0 0 14px rgba(255, 255, 255, 0.28);
}

.ed-poster-play-label {
    color: #ffffff;
    font-size: 0.92rem;
    font-weight: 800;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.8);
    letter-spacing: 0.3px;
    background: rgba(15, 23, 42, 0.6);
    padding: 5px 14px;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.ed-poster-top-bar {
    position: absolute;
    top: 14px;
    right: 14px;
    left: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 3;
}

.ed-poster-badge {
    background: rgba(15, 23, 42, 0.78);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    padding: 5px 12px;
    border-radius: 9999px;
    font-size: 0.78rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.ed-poster-badge.hd {
    color: #4ade80;
    border-color: rgba(74, 222, 128, 0.35);
}

/* شاشة الإيقاف المؤقت النظيفة الخاصة بالمنصة: تغطي الفيديو وتبقي شريط التحكم متاحاً */
.ed-video-pause-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 65px;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(10, 15, 30, 0.94);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    cursor: pointer;
    user-select: none;
    transition: opacity 0.25s ease;
    pointer-events: auto;
}

.ed-pause-content {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 26px;
    background: rgba(30, 41, 59, 0.88);
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 16px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);
    max-width: 90%;
}

.ed-pause-play-btn {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #2563eb;
    color: #ffffff;
    font-size: 1.35rem;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
    cursor: pointer;
    transition: transform 0.2s ease;
}

.ed-video-pause-overlay:hover .ed-pause-play-btn {
    transform: scale(1.1);
    background: #1d4ed8;
}

.ed-pause-meta {
    display: flex;
    flex-direction: column;
    text-align: right;
}

.ed-pause-title {
    margin: 0 0 4px;
    font-size: 0.95rem;
    font-weight: 800;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 320px;
}

.ed-pause-sub {
    font-size: 0.78rem;
    color: #94a3b8;
    font-weight: 600;
}

/* شريط التحكم بالسرعة والجودة المتعددة */
.ed-video-controls-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 12px 18px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    margin-top: 14px;
}

.ed-ctrl-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.ed-ctrl-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.82rem;
    font-weight: 800;
    color: #334155;
    white-space: nowrap;
}

.ed-speed-segmented,
.ed-quality-segmented {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 9px;
    padding: 3px;
    gap: 2px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

.ed-speed-opt,
.ed-quality-opt,
.speed-btn {
    background: transparent;
    border: none;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
}

.ed-speed-opt:hover,
.ed-quality-opt:hover,
.speed-btn:hover {
    color: #1e3a8a;
    background: #f1f5f9;
}

.ed-speed-opt.active,
.ed-quality-opt.active,
.speed-btn.active {
    background: #1e3a8a !important;
    color: #ffffff !important;
    font-weight: 800;
    box-shadow: 0 2px 6px rgba(30, 58, 138, 0.25) !important;
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
@media (max-width: 1040px) {
    .ed-layout-grid {
        display: flex !important;
        flex-direction: column !important;
        grid-template-columns: 1fr !important;
        gap: 20px !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .ed-layout-grid > main {
        order: 1 !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
    }
    .ed-layout-grid > aside {
        order: 2 !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
    }
    .ed-side-widget {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
}

@media (max-width: 768px) {
    .ed-sub-wrap {
        padding-bottom: 30px !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
    }
    .ed-top-nav-bar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
        margin-bottom: 14px !important;
        width: 100% !important;
    }
    .ed-back-btn {
        width: 100% !important;
        justify-content: center !important;
        box-sizing: border-box !important;
        font-size: 0.84rem !important;
        padding: 9px 12px !important;
    }
    .ed-academic-tag {
        width: 100% !important;
        justify-content: center !important;
        box-sizing: border-box !important;
        font-size: 0.8rem !important;
        padding: 7px 12px !important;
    }
    .ed-subject-hero {
        padding: 18px 14px !important;
        border-radius: 14px !important;
        margin-bottom: 16px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .ed-hero-main {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 16px !important;
    }
    .ed-hero-info {
        flex-direction: column !important;
        align-items: center !important;
        text-align: center !important;
        gap: 12px !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .ed-sub-icon-box {
        width: 58px !important;
        height: 58px !important;
        font-size: 1.8rem !important;
        border-radius: 14px !important;
        margin: 0 auto !important;
    }
    .ed-hero-title-area {
        width: 100% !important;
        text-align: center !important;
    }
    .ed-hero-title-area h1 {
        font-size: 1.35rem !important;
        text-align: center !important;
        line-height: 1.3 !important;
    }
    .ed-hero-desc {
        font-size: 0.84rem !important;
        text-align: center !important;
        line-height: 1.55 !important;
        margin-bottom: 12px !important;
    }
    .ed-hero-badges-row {
        justify-content: center !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
    }
    .ed-hero-stats-panel {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        width: 100% !important;
        box-sizing: border-box !important;
        padding: 10px 4px !important;
        text-align: center !important;
        gap: 0 !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
    }
    .ed-hstat-box {
        padding: 0 4px !important;
        border-left: 1px solid #e2e8f0 !important;
    }
    .ed-hstat-box:last-child {
        border-left: none !important;
    }
    html[dir="ltr"] .ed-hstat-box {
        border-left: none !important;
        border-right: 1px solid #e2e8f0 !important;
    }
    html[dir="ltr"] .ed-hstat-box:last-child {
        border-right: none !important;
    }
    .ed-hstat-num {
        font-size: 1.15rem !important;
    }
    .ed-hstat-lbl {
        font-size: 0.7rem !important;
    }
    .ed-hero-actions-bar {
        margin-top: 14px !important;
        padding-top: 14px !important;
        flex-direction: column !important;
        gap: 8px !important;
    }
    .ed-hero-actions-bar .ed-btn-royal {
        width: 100% !important;
        justify-content: center !important;
        box-sizing: border-box !important;
    }

    /* التبويبات الكلاسيكية للهواتف */
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

    /* بطاقة المحاضرة والفيديو المتجاوبة */
    .ed-video-card {
        border-radius: 14px !important;
        margin-bottom: 20px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }
    .ed-card-content-wrap {
        padding: 14px 12px !important;
    }
    .ed-card-top-row {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
        margin-bottom: 10px !important;
        width: 100% !important;
    }
    .ed-badges-cluster {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 5px !important;
        width: 100% !important;
    }
    .ed-actions-cluster {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(90px, 1fr)) !important;
        gap: 6px !important;
        width: 100% !important;
    }
    .ed-actions-cluster a,
    .ed-actions-cluster button {
        width: 100% !important;
        justify-content: center !important;
        font-size: 0.75rem !important;
        padding: 7px 6px !important;
        box-sizing: border-box !important;
    }
    .ed-card-lesson-title {
        font-size: 1.05rem !important;
        line-height: 1.4 !important;
        margin: 6px 0 12px !important;
    }

    /* شريط التحكم بالسرعة والجودة على الجوال */
    .ed-video-controls-row {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
        padding-top: 10px !important;
        border-top: 1px dashed #e2e8f0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .ed-ctrl-group {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
        width: 100% !important;
    }
    .ed-ctrl-label {
        font-size: 0.76rem !important;
        font-weight: 800 !important;
        color: #64748b !important;
    }
    .ed-speed-segmented,
    .ed-quality-segmented {
        display: flex !important;
        width: 100% !important;
        box-sizing: border-box !important;
        justify-content: space-between !important;
        gap: 2px !important;
        padding: 3px !important;
        overflow-x: auto !important;
        scrollbar-width: none !important;
    }
    .ed-speed-segmented::-webkit-scrollbar,
    .ed-quality-segmented::-webkit-scrollbar {
        display: none !important;
    }
    .ed-speed-opt,
    .ed-quality-opt {
        flex: 1 1 auto !important;
        padding: 6px 4px !important;
        font-size: 0.74rem !important;
        text-align: center !important;
        min-width: 0 !important;
        white-space: nowrap !important;
        border-radius: 6px !important;
    }

    /* لوحة تدوين الملاحظات */
    .video-notes-panel {
        padding: 12px 10px !important;
    }
    .video-notes-panel > div:first-child {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
    }
    .video-notes-panel input {
        width: 100% !important;
        box-sizing: border-box !important;
        font-size: 0.84rem !important;
    }
    .video-notes-panel button {
        width: 100% !important;
        justify-content: center !important;
    }

    /* مشغل الفيديو الأكاديمي للهواتف والشاشات الصغيرة */
    .ed-controls-main-row {
        gap: 6px !important;
        flex-wrap: wrap !important;
    }
    .ed-ctrl-cluster {
        gap: 6px !important;
    }
    .ed-ctrl-cluster.left {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }
    .ed-ctrl-cluster.right {
        flex-shrink: 0 !important;
    }
    .ed-volume-slider {
        display: none !important; /* الهواتف تحتوي على أزرار صوت فيزيائية ولا داعي لشغل مساحة الشريط */
    }
    .ed-time-counter {
        min-width: auto !important;
        font-size: 0.72rem !important;
        letter-spacing: 0 !important;
        white-space: nowrap !important;
    }
    .ed-ctrl-btn {
        padding: 4px !important;
        font-size: 1.05rem !important;
    }
    .ed-ctrl-btn.play {
        width: 34px !important;
        height: 34px !important;
        font-size: 1.1rem !important;
    }
    .ed-ctrl-btn.skip {
        font-size: 1.05rem !important;
    }
    .ed-skip-num {
        font-size: 0.5rem !important;
    }
    .ed-ctrl-pill {
        padding: 3px 7px !important;
        font-size: 0.72rem !important;
    }
    .ed-popover-menu {
        max-height: 190px !important;
        overflow-y: auto !important;
        z-index: 100 !important;
    }

    /* السايدبار وبطاقات المعلم والملازم */
    .ed-side-widget {
        padding: 16px 14px !important;
        border-radius: 14px !important;
        margin-bottom: 16px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .ed-teacher-avatar-ring {
        width: 64px !important;
        height: 64px !important;
        font-size: 1.5rem !important;
        margin: 0 auto 10px !important;
    }
    .ed-teacher-name {
        font-size: 1.05rem !important;
    }
    .ed-file-item {
        padding: 10px !important;
        gap: 10px !important;
    }

    /* بطاقات الاختبارات */
    .ed-exam-card {
        padding: 14px 12px !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 12px !important;
    }
    .ed-exam-card > div:first-child {
        width: 100% !important;
    }
    .ed-exam-card .ed-btn-royal {
        width: 100% !important;
        justify-content: center !important;
        box-sizing: border-box !important;
    }

    /* حالة الفراغ */
    .ed-classic-empty-state {
        padding: 28px 14px !important;
    }
    .ed-empty-title {
        font-size: 1.1rem !important;
    }
    .ed-empty-desc {
        font-size: 0.82rem !important;
    }
    .ed-empty-cta {
        flex-direction: column !important;
        width: 100% !important;
        gap: 8px !important;
    }
    .ed-empty-cta .ed-btn-royal {
        width: 100% !important;
        justify-content: center !important;
    }
}

/* الشاشات الضيقة للغاية (<= 420px) */
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
    .ed-actions-cluster {
        grid-template-columns: 1fr 1fr !important;
    }
    .ed-speed-opt,
    .ed-quality-opt {
        padding: 5px 2px !important;
        font-size: 0.7rem !important;
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
                                {{-- إطار مشغل الفيديو الفاخر --}}
                                <div class="ed-player-frame" id="player_frame_{{ $video->id }}" oncontextmenu="return false;" onmousemove="registerPlayerActivity('{{ $video->id }}')" ontouchstart="registerPlayerActivity('{{ $video->id }}')">
                                    @php
                                        $rawUrl = trim($video->url_path ?? '');
                                        if (in_array(strtolower($rawUrl), ['undefined', 'null', 'none', 'false', '0', 'undefined.mp4', 'null.mp4'])) {
                                            $rawUrl = '';
                                        }
                                        $ytEmbed = $video->youtube_embed_url;
                                        $ytId = null;
                                        $isYt = !empty($ytEmbed) || (!empty($rawUrl) && (str_contains($rawUrl, 'youtube.com') || str_contains($rawUrl, 'youtu.be')));
                                        if ($isYt) {
                                            if (preg_match('/(?:v=|youtu\.be\/|embed\/|shorts\/|live\/)([a-zA-Z0-9_\-]{11})/', $rawUrl ?: $ytEmbed, $ym)) {
                                                $ytId = $ym[1];
                                                $ytEmbed = 'https://www.youtube.com/embed/' . $ytId;
                                            } elseif (!empty($ytEmbed)) {
                                                $ytEmbed = str_replace(['watch?v=', 'youtube-nocookie.com/embed/'], ['embed/', 'youtube.com/embed/'], $ytEmbed);
                                            }
                                            if (!empty($ytEmbed)) {
                                                $ytBase = explode('?', $ytEmbed)[0];
                                                $ytBase = str_replace(['https://www.youtube-nocookie.com/embed/', 'http://www.youtube-nocookie.com/embed/'], 'https://www.youtube.com/embed/', $ytBase);
                                                $ytEmbed = $ytBase . '?enablejsapi=1&controls=0&fs=0&rel=0&iv_load_policy=3&playsinline=1&modestbranding=1&disablekb=1&showinfo=0&autohide=1&vq=hd1080&highres=1&origin=' . urlencode(request()->getSchemeAndHttpHost());
                                            }
                                        }
                                        $isDirectVideo = !empty($rawUrl) && (
                                            (bool) preg_match('/\.(mp4|webm|ogg|mov|m4v|mkv)($|\?)/i', $rawUrl)
                                            || str_contains($rawUrl, 'educational/videos')
                                            || (!str_contains($rawUrl, 'youtube') && !str_contains($rawUrl, 'youtu.be'))
                                        );
                                        $directVideoUrl = $isDirectVideo 
                                            ? \App\Support\MediaHelper::videoStreamUrl($rawUrl, null, $video->id)
                                            : null;
                                        $directVideoFallback = $isDirectVideo
                                            ? \App\Support\MediaHelper::url($rawUrl)
                                            : null;
                                        $hasPlayableVideo = !empty($ytEmbed) || ($isDirectVideo && $directVideoUrl);

                                        $posterThumb = null;
                                        if (!empty($ytId)) {
                                            $posterThumb = "https://i.ytimg.com/vi/{$ytId}/hqdefault.jpg";
                                        }
                                        if (empty($posterThumb)) {
                                            $posterThumb = asset('images/video-placeholder.jpg');
                                        }
                                    @endphp

                                    @if($hasPlayableVideo)
                                        <div class="ed-yt-frame-container" id="shield_wrap_{{ $video->id }}">
                                            {{-- منطقة الفيديو: يوتيوب بدقة نظيفة كاملة بدون قص أو فيديو مباشر --}}
                                            <div class="ed-yt-viewport">
                                                @if(!empty($ytEmbed))
                                                    <iframe id="player_yt_{{ $video->id }}" 
                                                            width="1920"
                                                            height="1080"
                                                            src="{{ $ytEmbed }}" 
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen" 
                                                            allowfullscreen
                                                            referrerpolicy="strict-origin-when-cross-origin"
                                                            tabindex="-1"
                                                            aria-hidden="true"
                                                            loading="lazy"
                                                            class="ed-video-iframe">
                                                    </iframe>
                                                @else
                                                    <video id="player_vid_{{ $video->id }}" 
                                                           class="ed-native-video" 
                                                           playsinline 
                                                           preload="metadata" 
                                                           controlsList="nodownload noplaybackrate" 
                                                           disableRemotePlayback 
                                                           oncontextmenu="return false;"
                                                           onerror="handleStudentVideoError(this, '{{ $video->id }}')">
                                                        <source src="{{ $directVideoUrl }}" type="video/mp4">
                                                        @if($directVideoFallback && $directVideoFallback !== $directVideoUrl)
                                                            <source src="{{ $directVideoFallback }}" type="video/mp4">
                                                        @endif
                                                        {{ __('متصفحك لا يدعم مشغل الفيديو.') }}
                                                    </video>
                                                @endif
                                            </div>

                                            {{-- علامة مائية ذكية متحركة لحماية الملكية الأكاديمية للمنصة --}}
                                            @if(auth('student')->check() || auth()->check())
                                                @php
                                                    $wmUser = auth('student')->user() ?? auth()->user();
                                                    $wmText = ($wmUser->name ?? 'طالب') . ' • ' . ($wmUser->phone ?: ($wmUser->email ?: ('ID: ' . $wmUser->id)));
                                                @endphp
                                                <div class="ed-floating-security-watermark" aria-hidden="true">
                                                    <span>{{ $wmText }}</span>
                                                </div>
                                            @endif

                                            {{-- بوستر المشغل الأكاديمي الموحد: يغطي المشغل تماماً ويمنع ظهور شاشات البداية وقنوات يوتيوب --}}
                                            <div class="ed-video-poster" id="poster_{{ $video->id }}" onclick="startStudentVideo('{{ $video->id }}')">
                                                <img src="{{ $posterThumb }}" alt="{{ $video->title }}" class="ed-poster-bg-img" loading="lazy" onerror="this.src='{{ asset('images/video-placeholder.jpg') }}'">
                                                <div class="ed-poster-overlay"></div>
                                                <div class="ed-poster-top-bar">
                                                    <span class="ed-poster-badge">
                                                        <i class="fa-solid fa-graduation-cap"></i> {{ __('الدرس') }} #{{ $video->order }}
                                                    </span>
                                                    <span class="ed-poster-badge hd">
                                                        <i class="fa-solid fa-sparkles"></i> 1080p Full HD
                                                    </span>
                                                </div>
                                                <div class="ed-poster-center-content">
                                                    <button type="button" class="ed-poster-play-btn" aria-label="{{ __('تشغيل الدرس') }}">
                                                        <i class="fa-solid fa-play"></i>
                                                    </button>
                                                    <span class="ed-poster-play-label">{{ __('انقر لبدء مشاهدة الحصة') }}</span>
                                                </div>
                                            </div>

                                            {{-- طبقة المشغل الخاص الفاخر: مُفعَّلة لكافة الفيديوهات التعليمية (يوتيوب ومباشر) --}}
                                            <div class="ed-pure-player-layer" id="player_layer_{{ $video->id }}" onmousemove="registerPlayerActivity('{{ $video->id }}')" onmouseleave="handlePlayerMouseLeave('{{ $video->id }}')">
                                                    {{-- سطح النقر للتشغيل والإيقاف والتكبير المزدوج --}}
                                                    <div class="ed-click-surface" onclick="handleClickSurface('{{ $video->id }}', event)" onmousemove="registerPlayerActivity('{{ $video->id }}')" ontouchstart="registerPlayerActivity('{{ $video->id }}')">
                                                        <div class="ed-center-pulse-btn" id="center_pulse_{{ $video->id }}">
                                                            <i class="fa-solid fa-play"></i>
                                                        </div>
                                                    </div>

                                                    {{-- هيدر المشغل المباشر الفاخر --}}
                                                    <div class="ed-player-top-header" onclick="event.stopPropagation()">
                                                        <div class="ed-pth-title">
                                                            <span class="ed-pth-badge"><i class="fa-solid fa-graduation-cap"></i> {{ __('الدرس') }} #{{ $video->order }}</span>
                                                            <span class="ed-pth-name">{{ $video->title }}</span>
                                                        </div>
                                                        <div class="ed-pth-quality-tag" id="inner_top_quality_{{ $video->id }}" onclick="toggleDropdownMenu('quality_menu_{{ $video->id }}', event); event.stopPropagation();" style="cursor: pointer;">
                                                            <i class="fa-solid fa-circle-check"></i> <span id="inner_quality_text_{{ $video->id }}">1080p Full HD</span>
                                                        </div>
                                                    </div>

                                                    {{-- شريط التحكم السفلي الكامل للمشغل المباشر --}}
                                                    <div class="ed-player-bottom-panel" onclick="event.stopPropagation()" onmousedown="event.stopPropagation()" ontouchstart="event.stopPropagation()">
                                                        {{-- شريط التقدم الزمني الدقيق --}}
                                                        <div class="ed-scrubber-wrapper" id="scrubber_wrap_{{ $video->id }}" onclick="handleScrubberClick(event, '{{ $video->id }}'); event.stopPropagation();" onmousemove="handleScrubberHover(event, '{{ $video->id }}')" onmouseleave="hideScrubberTooltip('{{ $video->id }}')">
                                                            <div class="ed-scrub-track">
                                                                <div class="ed-scrub-buffer" id="buffer_bar_{{ $video->id }}"></div>
                                                                <div class="ed-scrub-fill" id="progress_bar_{{ $video->id }}">
                                                                    <div class="ed-scrub-thumb"></div>
                                                                </div>
                                                            </div>
                                                            <div class="ed-scrub-tooltip" id="scrub_tip_{{ $video->id }}">00:00</div>
                                                        </div>

                                                        {{-- أزرار وعناصر التحكم --}}
                                                        <div class="ed-controls-main-row" onclick="event.stopPropagation()">
                                                            <div class="ed-ctrl-cluster left" onclick="event.stopPropagation()">
                                                                <button type="button" class="ed-ctrl-btn play" id="bar_btn_{{ $video->id }}" onclick="toggleStudentPlayback('{{ $video->id }}', event); event.stopPropagation();" title="{{ __('تشغيل / إيقاف (مسافة)') }}">
                                                                    <i class="fa-solid fa-play"></i>
                                                                </button>
                                                                <button type="button" class="ed-ctrl-btn skip" onclick="seekStudentRelative('{{ $video->id }}', -10, event); event.stopPropagation();" title="{{ __('تراجع 10 ثوانٍ') }}">
                                                                    <i class="fa-solid fa-rotate-left"></i>
                                                                    <span class="ed-skip-num">10</span>
                                                                </button>
                                                                <button type="button" class="ed-ctrl-btn skip" onclick="seekStudentRelative('{{ $video->id }}', 10, event); event.stopPropagation();" title="{{ __('تقديم 10 ثوانٍ') }}">
                                                                    <i class="fa-solid fa-rotate-right"></i>
                                                                    <span class="ed-skip-num">10</span>
                                                                </button>
                                                                <div class="ed-time-counter" id="time_txt_{{ $video->id }}">
                                                                    00:00 / 00:00
                                                                </div>
                                                            </div>

                                                            <div class="ed-ctrl-cluster right" onclick="event.stopPropagation()">
                                                                <div class="ed-volume-group" onclick="event.stopPropagation()" onmousedown="event.stopPropagation()">
                                                                    <button type="button" class="ed-ctrl-btn vol" id="mute_btn_{{ $video->id }}" onclick="toggleStudentMute('{{ $video->id }}', event); event.stopPropagation();" title="{{ __('كتم / تشغيل الصوت (M)') }}">
                                                                        <i class="fa-solid fa-volume-high"></i>
                                                                    </button>
                                                                    <input type="range" class="ed-volume-slider" id="vol_slider_{{ $video->id }}" min="0" max="100" value="100" onclick="event.stopPropagation()" oninput="setStudentVolume('{{ $video->id }}', this.value, event)">
                                                                </div>

                                                                <div class="ed-speed-dropdown" onclick="event.stopPropagation()" onmousedown="event.stopPropagation()">
                                                                    <button type="button" class="ed-ctrl-pill" onclick="toggleDropdownMenu('speed_menu_{{ $video->id }}', event); event.stopPropagation();">
                                                                        <span id="active_speed_{{ $video->id }}">1x</span>
                                                                    </button>
                                                                    <div class="ed-popover-menu" id="speed_menu_{{ $video->id }}" onclick="event.stopPropagation()">
                                                                        <div class="ed-pop-header"><i class="fa-solid fa-gauge-high"></i> {{ __('سرعة التشغيل') }}</div>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoSpeed('{{ $video->id }}', 0.5, this, event); event.stopPropagation();">0.5x</button>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoSpeed('{{ $video->id }}', 0.75, this, event); event.stopPropagation();">0.75x</button>
                                                                        <button type="button" class="ed-pop-opt active" onclick="setVideoSpeed('{{ $video->id }}', 1, this, event); event.stopPropagation();">1x ({{ __('عادي') }})</button>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoSpeed('{{ $video->id }}', 1.25, this, event); event.stopPropagation();">1.25x</button>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoSpeed('{{ $video->id }}', 1.5, this, event); event.stopPropagation();">1.5x</button>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoSpeed('{{ $video->id }}', 1.75, this, event); event.stopPropagation();">1.75x</button>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoSpeed('{{ $video->id }}', 2, this, event); event.stopPropagation();">2x</button>
                                                                    </div>
                                                                </div>

                                                                <div class="ed-quality-dropdown" onclick="event.stopPropagation()" onmousedown="event.stopPropagation()">
                                                                    <button type="button" class="ed-ctrl-pill hd" onclick="toggleDropdownMenu('quality_menu_{{ $video->id }}', event); event.stopPropagation();" title="{{ __('دقة وجودة الفيديو') }}">
                                                                        <i class="fa-solid fa-sliders"></i>
                                                                        <span id="active_quality_{{ $video->id }}">1080p</span>
                                                                    </button>
                                                                    <div class="ed-popover-menu" id="quality_menu_{{ $video->id }}" onclick="event.stopPropagation()">
                                                                        <div class="ed-pop-header"><i class="fa-solid fa-sparkles"></i> {{ __('دقة الفيديو') }}</div>
                                                                        <button type="button" class="ed-pop-opt active" onclick="setVideoQuality('{{ $video->id }}', '1080p', this, '1080p Full HD', event); event.stopPropagation();">
                                                                            <span>1080p Full HD</span>
                                                                            <span style="font-size: 0.65rem; color: #4ade80;">الأعلى</span>
                                                                        </button>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoQuality('{{ $video->id }}', '720p', this, '720p HD', event); event.stopPropagation();">
                                                                            <span>720p HD</span>
                                                                            <span style="font-size: 0.65rem; color: #60a5fa;">متوازنة</span>
                                                                        </button>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoQuality('{{ $video->id }}', '480p', this, '480p SD', event); event.stopPropagation();">
                                                                            <span>480p SD</span>
                                                                            <span style="font-size: 0.65rem; color: #f59e0b;">توفير</span>
                                                                        </button>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoQuality('{{ $video->id }}', '360p', this, '360p', event); event.stopPropagation();">
                                                                            <span>360p</span>
                                                                            <span style="font-size: 0.65rem; color: #94a3b8;">نت ضعيف</span>
                                                                        </button>
                                                                        <button type="button" class="ed-pop-opt" onclick="setVideoQuality('{{ $video->id }}', 'auto', this, 'تلقائي (Auto)', event); event.stopPropagation();">
                                                                            <span>{{ __('تلقائي (Auto)') }}</span>
                                                                            <span style="font-size: 0.65rem; color: #a855f7;">تكيف</span>
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                                <button type="button" class="ed-ctrl-btn fs" onclick="togglePlatformFullscreen('{{ $video->id }}', event); event.stopPropagation();" title="{{ __('ملء الشاشة (F)') }}">
                                                                    <i class="fa-solid fa-expand"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>

                                            @if(!$isYt)
                                                {{-- شاشة الإيقاف المؤقت للفيديوهات المباشرة --}}
                                                <div class="ed-video-pause-overlay" id="pause_overlay_{{ $video->id }}" onclick="resumeStudentPlayback('{{ $video->id }}')" style="display: none;">
                                                    <div class="ed-pause-content">
                                                        <button type="button" class="ed-pause-play-btn">
                                                            <i class="fa-solid fa-play"></i>
                                                        </button>
                                                        <div class="ed-pause-meta">
                                                            <h4 class="ed-pause-title">{{ $video->title }}</h4>
                                                            <span class="ed-pause-sub">{{ __('الحصة متوقفة مؤقتاً • انقر للاستئناف والمتابعة') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                                {{-- نافذة الخطأ الذكية في حال تعثر البث المباشر --}}
                                                <div id="player_err_{{ $video->id }}" style="display: none; position: absolute; inset: 0; background: rgba(15, 23, 42, 0.95); flex-direction: column; align-items: center; justify-content: center; gap: 12px; padding: 20px; text-align: center; color: #fff; z-index: 25;">
                                                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); color: #f87171; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                                    </div>
                                                    <div>
                                                        <h4 id="player_err_h4_{{ $video->id }}" style="margin: 0 0 6px; font-size: 0.98rem; font-weight: 700; color: #f8fafc;">{{ __('تعذر بث الفيديو في المشغل المباشر') }}</h4>
                                                        <p id="player_err_p_{{ $video->id }}" style="margin: 0; font-size: 0.82rem; color: #94a3b8; max-width: 420px; line-height: 1.5;">{{ __('يمكنك إعادة المحاولة أو تنزيل ملزمة الدرس PDF.') }}</p>
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
                                                    </div>
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
                                            <span class="ed-hd-pill">
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
                                                <span>{{ __('تكبير الشاشة') }}</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- عنوان الحصة المعتمد --}}
                                    <h3 class="ed-card-lesson-title">{{ $video->title }}</h3>

                                    {{-- شريط التحكم بالسرعة والجودة المتعددة --}}
                                    <div class="ed-video-controls-row">
                                        <div class="ed-ctrl-group">
                                            <span class="ed-ctrl-label">
                                                <i class="fa-solid fa-gauge-high"></i>
                                                <span>{{ __('سرعة العرض:') }}</span>
                                            </span>
                                            <div class="ed-speed-segmented">
                                                <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 0.5, this)">0.5x</button>
                                                <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 0.75, this)">0.75x</button>
                                                <button type="button" class="ed-speed-opt active" onclick="setVideoSpeed('{{ $video->id }}', 1, this)">1x</button>
                                                <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 1.25, this)">1.25x</button>
                                                <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 1.5, this)">1.5x</button>
                                                <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 1.75, this)">1.75x</button>
                                                <button type="button" class="ed-speed-opt" onclick="setVideoSpeed('{{ $video->id }}', 2, this)">2x</button>
                                            </div>
                                        </div>

                                        <div class="ed-ctrl-group">
                                            <span class="ed-ctrl-label">
                                                <i class="fa-solid fa-sliders"></i>
                                                <span>{{ __('جودة العرض:') }}</span>
                                            </span>
                                            <div class="ed-quality-segmented">
                                                <button type="button" class="ed-quality-opt active" onclick="setVideoQuality('{{ $video->id }}', '1080p', this, '1080p Full HD')">1080p</button>
                                                <button type="button" class="ed-quality-opt" onclick="setVideoQuality('{{ $video->id }}', '720p', this, '720p HD')">720p</button>
                                                <button type="button" class="ed-quality-opt" onclick="setVideoQuality('{{ $video->id }}', '480p', this, '480p SD')">480p</button>
                                                <button type="button" class="ed-quality-opt" onclick="setVideoQuality('{{ $video->id }}', '360p', this, '360p')">360p</button>
                                                <button type="button" class="ed-quality-opt" onclick="setVideoQuality('{{ $video->id }}', 'auto', this, '{{ __('تلقائي') }}')">{{ __('تلقائي') }}</button>
                                            </div>
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

// ==========================================================================
// UNIFIED PLATFORM SECURE VIDEO PLAYER ENGINE (YOUTUBE & DIRECT STREAM)
// ==========================================================================
let ytPlayers = {};
let studentProgressIntervals = {};
let playerHideTimers = {};

// Toast الإشعارات الأكاديمي الفاخر
function showPlayerToast(msg) {
    let toast = document.getElementById('edPlayerGlobalToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'edPlayerGlobalToast';
        toast.style.cssText = 'position: fixed; bottom: calc(78px + env(safe-area-inset-bottom, 0px)); left: 50%; transform: translateX(-50%) translateY(20px); background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); color: #fff; padding: 10px 22px; border-radius: 9999px; font-size: 0.86rem; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.18); box-shadow: 0 12px 30px rgba(0,0,0,0.5); opacity: 0; pointer-events: none; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); z-index: 99999; display: flex; align-items: center; gap: 8px;';
        document.body.appendChild(toast);
    }
    toast.innerHTML = msg;
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(20px)';
    }, 2400);
}

// فحص نوع المشغل للدرس (مباشر أو يوتيوب)
function getPlayerContext(videoId) {
    const direct = document.getElementById(`player_vid_${videoId}`) || document.getElementById(`player_${videoId}`);
    const iframe = document.getElementById(`player_yt_${videoId}`);
    const yt = ytPlayers[videoId];
    return {
        isDirect: !!direct,
        direct: direct,
        isYt: !!iframe,
        iframe: iframe,
        yt: yt
    };
}

let ytPlayStates = {};

// تهيئة مشغلات يوتيوب بدقة وموثوقية عالية
function initAllYouTubePlayers() {
    if (window.YT && window.YT.Player) {
        document.querySelectorAll('iframe[id^="player_yt_"]').forEach(iframe => {
            initSingleYtPlayer(iframe.id);
        });
    }
}

function onYouTubeIframeAPIReady() {
    initAllYouTubePlayers();
}

function initSingleYtPlayer(iframeId) {
    if (!iframeId) return null;
    const videoId = iframeId.replace('player_yt_', '');
    if (ytPlayers[videoId]) return ytPlayers[videoId];
    if (window.YT && window.YT.Player) {
        try {
            ytPlayers[videoId] = new YT.Player(iframeId, {
                playerVars: {
                    'controls': 0,
                    'fs': 0,
                    'rel': 0,
                    'playsinline': 1,
                    'modestbranding': 1,
                    'disablekb': 1,
                    'iv_load_policy': 3,
                    'showinfo': 0,
                    'autohide': 1,
                    'vq': 'hd1080',
                    'highres': 1
                },
                events: {
                    'onReady': function(event) {
                        try {
                            if (event.target && typeof event.target.setPlaybackQuality === 'function') {
                                event.target.setPlaybackQuality('hd1080');
                            }
                        } catch(e) {}
                        updateStudentTimeDisplay(videoId);
                        applyStoredQualityPreference(videoId);
                        applyStoredSpeedPreference(videoId);
                    },
                    'onStateChange': function(event) {
                        handleYtStateChange(videoId, event.data);
                    }
                }
            });
            return ytPlayers[videoId];
        } catch(e) {
            console.warn('YT Player init error:', e);
        }
    }
    return null;
}

// إدارة إخفاء عناصر التحكم تلقائياً بعد 2.5 ثانية من عدم النشاط أثناء التشغيل
function registerPlayerActivity(videoId) {
    const frame = document.getElementById(`player_frame_${videoId}`);
    if (!frame) return;
    frame.classList.remove('controls-hidden');
    clearTimeout(playerHideTimers[videoId]);
    
    const ctx = getPlayerContext(videoId);
    const isPlayingYt = ytPlayStates[videoId] || (ctx.yt && typeof ctx.yt.getPlayerState === 'function' && ctx.yt.getPlayerState() === 1);
    const isPlayingDirect = ctx.direct && !ctx.direct.paused && !ctx.direct.ended;
    
    if (isPlayingYt || isPlayingDirect) {
        playerHideTimers[videoId] = setTimeout(() => {
            frame.classList.add('controls-hidden');
        }, 2600);
    }
}

function handlePlayerMouseLeave(videoId) {
    const frame = document.getElementById(`player_frame_${videoId}`);
    if (!frame) return;
    const ctx = getPlayerContext(videoId);
    const isPlayingYt = ytPlayStates[videoId] || (ctx.yt && typeof ctx.yt.getPlayerState === 'function' && ctx.yt.getPlayerState() === 1);
    const isPlayingDirect = ctx.direct && !ctx.direct.paused && !ctx.direct.ended;
    
    if (isPlayingYt || isPlayingDirect) {
        frame.classList.add('controls-hidden');
    }
}

// تغيير حالة يوتيوب
function handleYtStateChange(videoId, state) {
    const poster = document.getElementById(`poster_${videoId}`);
    const pauseOverlay = document.getElementById(`pause_overlay_${videoId}`);
    const barBtn = document.getElementById(`bar_btn_${videoId}`);
    const frame = document.getElementById(`player_frame_${videoId}`);
    
    // 1 = PLAYING
    if (state === 1) {
        ytPlayStates[videoId] = true;
        if (poster) {
            poster.style.opacity = '0';
            poster.style.pointerEvents = 'none';
            setTimeout(() => { poster.style.display = 'none'; }, 250);
        }
        let yt = ytPlayers[videoId];
        if (yt && typeof yt.setPlaybackQuality === 'function') {
            try { yt.setPlaybackQuality('hd1080'); } catch(e) {}
        }
        if (pauseOverlay) pauseOverlay.style.display = 'none';
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
        startStudentProgressTracking(videoId);
        registerPlayerActivity(videoId);
    } else if (state === 2) { // 2 = PAUSED
        ytPlayStates[videoId] = false;
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
        if (frame) frame.classList.remove('controls-hidden');
        clearTimeout(playerHideTimers[videoId]);
        stopStudentProgressTracking(videoId);
    } else if (state === 0) { // 0 = ENDED
        ytPlayStates[videoId] = false;
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-rotate-right"></i>';
        if (frame) frame.classList.remove('controls-hidden');
        stopStudentProgressTracking(videoId);
    }
}

// بدء تشغيل الدرس من البوستر
function startStudentVideo(videoId) {
    const poster = document.getElementById(`poster_${videoId}`);
    if (poster) {
        const btn = poster.querySelector('.ed-poster-play-btn');
        if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    }
    const pauseOverlay = document.getElementById(`pause_overlay_${videoId}`);
    if (pauseOverlay) pauseOverlay.style.display = 'none';

    const ctx = getPlayerContext(videoId);
    if (ctx.isDirect && ctx.direct) {
        ctx.direct.play().then(() => {
            if (poster) {
                poster.style.opacity = '0';
                poster.style.pointerEvents = 'none';
                setTimeout(() => { poster.style.display = 'none'; }, 250);
            }
        }).catch(() => {
            if (poster) {
                poster.style.opacity = '0';
                poster.style.pointerEvents = 'none';
                setTimeout(() => { poster.style.display = 'none'; }, 250);
            }
        });
        const barBtn = document.getElementById(`bar_btn_${videoId}`);
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
        startStudentProgressTracking(videoId);
        initScrubberEvents(videoId);
        registerPlayerActivity(videoId);
        return;
    }

    ytPlayStates[videoId] = true;
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt) {
        try {
            if (typeof yt.setPlaybackQuality === 'function') {
                yt.setPlaybackQuality('hd1080');
            }
            if (typeof yt.playVideo === 'function') {
                yt.playVideo();
            }
        } catch(e) {}
    }
    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'setPlaybackQuality', args: ['hd1080'] }), '*');
        ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'playVideo', args: [] }), '*');
    }
    const barBtn = document.getElementById(`bar_btn_${videoId}`);
    if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
    startStudentProgressTracking(videoId);
    initScrubberEvents(videoId);
    registerPlayerActivity(videoId);

    // مهلة حماية لضمان إخفاء البوستر بعد بدء البث وعدم تسريب أي زر ليوتيوب
    setTimeout(() => {
        const p = document.getElementById(`poster_${videoId}`);
        if (p && p.style.display !== 'none') {
            p.style.opacity = '0';
            p.style.pointerEvents = 'none';
            setTimeout(() => { p.style.display = 'none'; }, 250);
        }
    }, 900);
}

// استئناف التشغيل من شاشة الإيقاف المؤقت
function resumeStudentPlayback(videoId) {
    const pauseOverlay = document.getElementById(`pause_overlay_${videoId}`);
    if (pauseOverlay) pauseOverlay.style.display = 'none';
    const ctx = getPlayerContext(videoId);
    if (ctx.isDirect && ctx.direct) {
        ctx.direct.play().catch(() => {});
        const barBtn = document.getElementById(`bar_btn_${videoId}`);
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
        initScrubberEvents(videoId);
        registerPlayerActivity(videoId);
        return;
    }

    ytPlayStates[videoId] = true;
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.playVideo === 'function') {
        try { yt.playVideo(); } catch(e) {}
    }
    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'playVideo', args: [] }), '*');
    }
    const barBtn = document.getElementById(`bar_btn_${videoId}`);
    if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
    initScrubberEvents(videoId);
    registerPlayerActivity(videoId);
}

// تبديل التشغيل والإيقاف (مسافة أو نقر على الشاشة)
function toggleStudentPlayback(videoId, e) {
    if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
    const pulseBtn = document.getElementById(`center_pulse_${videoId}`);
    const barBtn = document.getElementById(`bar_btn_${videoId}`);
    const pauseOverlay = document.getElementById(`pause_overlay_${videoId}`);
    const poster = document.getElementById(`poster_${videoId}`);
    if (poster && poster.style.display !== 'none') {
        startStudentVideo(videoId);
        return;
    }

    const ctx = getPlayerContext(videoId);
    if (ctx.isDirect && ctx.direct) {
        if (ctx.direct.paused) {
            ctx.direct.play().catch(() => {});
            if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
            if (pauseOverlay) pauseOverlay.style.display = 'none';
            if (pulseBtn) {
                pulseBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
                pulseBtn.classList.add('show-pulse');
                setTimeout(() => pulseBtn.classList.remove('show-pulse'), 500);
            }
            startStudentProgressTracking(videoId);
            registerPlayerActivity(videoId);
        } else {
            ctx.direct.pause();
            if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
            if (pauseOverlay) pauseOverlay.style.display = 'flex';
            if (pulseBtn) {
                pulseBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
                pulseBtn.classList.add('show-pulse');
                setTimeout(() => pulseBtn.classList.remove('show-pulse'), 500);
            }
            stopStudentProgressTracking(videoId);
        }
        return;
    }

    // لليوتيوب
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    let isPlaying = ytPlayStates[videoId];
    if (yt && typeof yt.getPlayerState === 'function') {
        try {
            const st = yt.getPlayerState();
            if (st === 1) isPlaying = true;
            else if (st === 2 || st === 0) isPlaying = false;
        } catch(e) {}
    }

    if (isPlaying) {
        // إيقاف مؤقت
        ytPlayStates[videoId] = false;
        if (yt && typeof yt.pauseVideo === 'function') {
            try { yt.pauseVideo(); } catch(e) {}
        }
        const iframe = document.getElementById(`player_yt_${videoId}`);
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'pauseVideo', args: [] }), '*');
        }
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
        if (pauseOverlay) pauseOverlay.style.display = 'flex';
        if (pulseBtn) {
            pulseBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
            pulseBtn.classList.add('show-pulse');
            setTimeout(() => pulseBtn.classList.remove('show-pulse'), 500);
        }
        stopStudentProgressTracking(videoId);
    } else {
        // تشغيل واستئناف
        ytPlayStates[videoId] = true;
        if (yt && typeof yt.playVideo === 'function') {
            try { yt.playVideo(); } catch(e) {}
        }
        const iframe = document.getElementById(`player_yt_${videoId}`);
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'playVideo', args: [] }), '*');
        }
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
        if (pauseOverlay) pauseOverlay.style.display = 'none';
        if (pulseBtn) {
            pulseBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
            pulseBtn.classList.add('show-pulse');
            setTimeout(() => pulseBtn.classList.remove('show-pulse'), 500);
        }
        startStudentProgressTracking(videoId);
        registerPlayerActivity(videoId);
    }
}

// ضبط جودة الفيديو متعدد الجودات
function setVideoQuality(videoId, quality, btnElement, label, e) {
    if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
    const displayLabel = label || quality;
    const ctx = getPlayerContext(videoId);
    
    // حفظ التفضيل للطالب
    try {
        localStorage.setItem('step_player_quality', quality);
    } catch(e) {}

    // تحديث الشارات في الواجهة
    const activeBadge = document.getElementById(`active_quality_${videoId}`);
    if (activeBadge) activeBadge.textContent = quality;
    
    const topTag = document.getElementById(`top_quality_text_${videoId}`);
    if (topTag) topTag.textContent = displayLabel;

    const innerTag = document.getElementById(`inner_quality_text_${videoId}`);
    if (innerTag) innerTag.textContent = displayLabel;

    // لليوتيوب
    const ytQualityMap = {
        '1080p': 'hd1080',
        '720p': 'hd720',
        '480p': 'large',
        '360p': 'medium',
        'auto': 'auto'
    };
    const mapped = ytQualityMap[quality] || quality;
    
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.setPlaybackQuality === 'function') {
        try {
            yt.setPlaybackQuality(mapped);
            if (typeof yt.setPlaybackQualityRange === 'function') {
                yt.setPlaybackQualityRange(mapped, mapped);
            }
        } catch(e) {}
    }

    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        ytIframe.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: 'setPlaybackQuality',
            args: [mapped]
        }), '*');
    }

    // للفيديو المباشر
    if (ctx.isDirect && ctx.direct) {
        const cur = ctx.direct.currentTime || 0;
        const wasPaused = ctx.direct.paused;
        const baseSrc = ctx.direct.getAttribute('data-base-src') || ctx.direct.currentSrc || ctx.direct.src;
        if (baseSrc && !ctx.direct.getAttribute('data-base-src')) {
            ctx.direct.setAttribute('data-base-src', baseSrc);
        }
        if (baseSrc) {
            const cleanUrl = baseSrc.split('?')[0];
            const newSrc = `${cleanUrl}?quality=${quality}`;
            ctx.direct.src = newSrc;
            ctx.direct.currentTime = cur;
            if (!wasPaused) {
                ctx.direct.play().catch(() => {});
            }
        }
    }

    // مزامنة أزرار الجودة عبر بطاقة الدرس وقائمة المشغل الداخلية
    document.querySelectorAll(`#quality_menu_${videoId} .ed-pop-opt, #card_video_${videoId} .ed-quality-opt`).forEach(b => {
        const oc = b.getAttribute('onclick') || '';
        if (oc.includes(`'${quality}'`)) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });
    document.querySelectorAll('.ed-popover-menu').forEach(m => m.classList.remove('show'));
    showPlayerToast(`{{ __("تم ضبط دقة العرض على") }} ${displayLabel} 🎬`);
}

function applyStoredQualityPreference(videoId) {
    try {
        const pref = localStorage.getItem('step_player_quality') || '1080p';
        if (pref) {
            const optBtn = document.querySelector(`#card_video_${videoId} .ed-quality-opt[onclick*="'${pref}'"]`) ||
                           document.querySelector(`#quality_menu_${videoId} button[onclick*="'${pref}'"]`);
            if (optBtn) {
                setVideoQuality(videoId, pref, optBtn, optBtn.innerText.trim());
            }
        }
    } catch(e) {}
}

// التحكم بالصوت والكتم
function setStudentVolume(videoId, value, e) {
    if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
    const val = parseInt(value, 10);
    const muteBtn = document.getElementById(`mute_btn_${videoId}`);
    const ctx = getPlayerContext(videoId);

    if (ctx.isDirect && ctx.direct) {
        ctx.direct.volume = val / 100;
        ctx.direct.muted = (val === 0);
    }

    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.setVolume === 'function') {
        try {
            if (val === 0) yt.mute();
            else { yt.unMute(); yt.setVolume(val); }
        } catch(e) {}
    }

    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        if (val === 0) {
            ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'mute', args: [] }), '*');
        } else {
            ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'unMute', args: [] }), '*');
            ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'setVolume', args: [val] }), '*');
        }
    }

    if (muteBtn) {
        if (val === 0) muteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
        else if (val < 50) muteBtn.innerHTML = '<i class="fa-solid fa-volume-low"></i>';
        else muteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
    }
}

function toggleStudentMute(videoId, e) {
    if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
    const ctx = getPlayerContext(videoId);
    const muteBtn = document.getElementById(`mute_btn_${videoId}`);
    const slider = document.getElementById(`vol_slider_${videoId}`);

    if (ctx.isDirect && ctx.direct) {
        ctx.direct.muted = !ctx.direct.muted;
        if (ctx.direct.muted) {
            if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
            if (slider) slider.value = 0;
        } else {
            if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
            if (slider) slider.value = (ctx.direct.volume || 1) * 100;
        }
        return;
    }

    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.isMuted === 'function') {
        try {
            if (yt.isMuted()) {
                yt.unMute();
                if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
                if (slider) slider.value = 100;
            } else {
                yt.mute();
                if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
                if (slider) slider.value = 0;
            }
            return;
        } catch(e) {}
    }

    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        const isMutedNow = slider && slider.value === '0';
        if (isMutedNow) {
            ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'unMute', args: [] }), '*');
            if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
            if (slider) slider.value = 100;
        } else {
            ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'mute', args: [] }), '*');
            if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
            if (slider) slider.value = 0;
        }
    }
}

// التحكم بالسرعة ومزامنة التفضيل
function setVideoSpeed(videoId, speed, btnElement, e) {
    if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
    speed = parseFloat(speed) || 1;
    const ctx = getPlayerContext(videoId);
    if (ctx.isDirect && ctx.direct) {
        ctx.direct.playbackRate = speed;
        ctx.direct.defaultPlaybackRate = speed;
    }
    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.setPlaybackRate === 'function') {
        try { yt.setPlaybackRate(speed); } catch(e) {}
    }
    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'setPlaybackRate', args: [speed] }), '*');
    }

    try {
        localStorage.setItem('step_player_speed', speed);
    } catch(e) {}

    const pill = document.getElementById(`active_speed_${videoId}`);
    if (pill) pill.textContent = `${speed}x`;

    // مزامنة أزرار السرعة عبر بطاقة الدرس وقائمة المشغل الداخلية
    document.querySelectorAll(`#speed_menu_${videoId} .ed-pop-opt, #card_video_${videoId} .ed-speed-opt`).forEach(b => {
        const oc = b.getAttribute('onclick') || '';
        if (oc.includes(`, ${speed},`) || oc.includes(`, ${speed})`)) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });
    document.querySelectorAll('.ed-popover-menu').forEach(m => m.classList.remove('show'));
    showPlayerToast(`{{ __("تم ضبط السرعة على") }} ${speed}x ⚡`);
}

function applyStoredSpeedPreference(videoId) {
    try {
        const pref = localStorage.getItem('step_player_speed');
        if (pref && parseFloat(pref) > 0) {
            const speed = parseFloat(pref);
            const pill = document.getElementById(`active_speed_${videoId}`);
            if (pill) pill.textContent = `${speed}x`;
            const ctx = getPlayerContext(videoId);
            if (ctx.isDirect && ctx.direct) {
                ctx.direct.playbackRate = speed;
            }
            let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
            if (yt && typeof yt.setPlaybackRate === 'function') {
                try { yt.setPlaybackRate(speed); } catch(e) {}
            }
            document.querySelectorAll(`#speed_menu_${videoId} .ed-pop-opt, #card_video_${videoId} .ed-speed-opt`).forEach(b => {
                const oc = b.getAttribute('onclick') || '';
                if (oc.includes(`, ${speed},`) || oc.includes(`, ${speed})`)) {
                    b.classList.add('active');
                } else {
                    b.classList.remove('active');
                }
            });
        }
    } catch(e) {}
}

// التقديم والتأخير بنسبة مئوية دقيقة (يدعم السحب والنقر)
let activeScrubVideoId = null;

function seekToPercentage(videoId, percentage) {
    percentage = Math.max(0, Math.min(1, percentage));
    const ctx = getPlayerContext(videoId);
    if (ctx.isDirect && ctx.direct) {
        const dur = ctx.direct.duration || 0;
        if (dur > 0) {
            ctx.direct.currentTime = percentage * dur;
            updateStudentTimeDisplay(videoId);
        }
        return;
    }

    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    let dur = 0;
    if (yt && typeof yt.getDuration === 'function') {
        dur = yt.getDuration() || 0;
    }
    if (dur > 0 && yt && typeof yt.seekTo === 'function') {
        try {
            yt.seekTo(percentage * dur, true);
        } catch(e) {}
    }
    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow && dur > 0) {
        ytIframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'seekTo', args: [percentage * dur, true] }), '*');
    }
    updateStudentTimeDisplay(videoId);
}

// شريط التقدم: النقر المباشر
function handleScrubberClick(e, videoId) {
    const wrap = document.getElementById(`scrubber_wrap_${videoId}`);
    if (!wrap) return;
    const rect = wrap.getBoundingClientRect();
    const clickX = e.clientX - rect.left;
    const pct = Math.max(0, Math.min(1, clickX / rect.width));
    seekToPercentage(videoId, pct);
}

// شريط التقدم: التلميح الزمني عند التحويم
function handleScrubberHover(e, videoId) {
    const wrap = document.getElementById(`scrubber_wrap_${videoId}`);
    const tip = document.getElementById(`scrub_tip_${videoId}`);
    if (!wrap || !tip) return;
    const rect = wrap.getBoundingClientRect();
    const clickX = e.clientX - rect.left;
    const pct = Math.max(0, Math.min(1, clickX / rect.width));
    
    const ctx = getPlayerContext(videoId);
    let dur = 0;
    if (ctx.isDirect && ctx.direct) dur = ctx.direct.duration || 0;
    else if (ctx.yt && typeof ctx.yt.getDuration === 'function') dur = ctx.yt.getDuration() || 0;

    if (dur > 0) {
        tip.textContent = formatDurationSec(pct * dur);
        tip.style.display = 'block';
        tip.style.left = `${Math.max(16, Math.min(rect.width - 16, clickX))}px`;
    }
}

function hideScrubberTooltip(videoId) {
    const tip = document.getElementById(`scrub_tip_${videoId}`);
    if (tip) tip.style.display = 'none';
}

// نظام السحب الذكي للشريط (Drag-to-Seek) للمس والفأرة
function initScrubberEvents(videoId) {
    const wrap = document.getElementById(`scrubber_wrap_${videoId}`);
    if (!wrap || wrap._hasScrubEvents) return;
    wrap._hasScrubEvents = true;

    function getPercentage(clientX) {
        const rect = wrap.getBoundingClientRect();
        return Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
    }

    function updateVisual(pct, clientX) {
        const progBar = document.getElementById(`progress_bar_${videoId}`);
        if (progBar) progBar.style.width = `${pct * 100}%`;
        const tip = document.getElementById(`scrub_tip_${videoId}`);
        if (tip) {
            const ctx = getPlayerContext(videoId);
            let dur = 0;
            if (ctx.isDirect && ctx.direct) dur = ctx.direct.duration || 0;
            else if (ctx.yt && typeof ctx.yt.getDuration === 'function') dur = ctx.yt.getDuration() || 0;
            if (dur > 0) {
                tip.textContent = formatDurationSec(pct * dur);
                tip.style.display = 'block';
                const rect = wrap.getBoundingClientRect();
                const x = clientX - rect.left;
                tip.style.left = `${Math.max(16, Math.min(rect.width - 16, x))}px`;
            }
        }
    }

    wrap.addEventListener('mousedown', function(e) {
        e.preventDefault();
        activeScrubVideoId = videoId;
        wrap.classList.add('is-scrubbing');
        const pct = getPercentage(e.clientX);
        updateVisual(pct, e.clientX);
        seekToPercentage(videoId, pct);
    });

    wrap.addEventListener('touchstart', function(e) {
        if (!e.touches || !e.touches[0]) return;
        activeScrubVideoId = videoId;
        wrap.classList.add('is-scrubbing');
        const pct = getPercentage(e.touches[0].clientX);
        updateVisual(pct, e.touches[0].clientX);
        seekToPercentage(videoId, pct);
    }, { passive: true });
}

document.addEventListener('mousemove', function(e) {
    if (!activeScrubVideoId) return;
    const wrap = document.getElementById(`scrubber_wrap_${activeScrubVideoId}`);
    if (!wrap) return;
    const rect = wrap.getBoundingClientRect();
    const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
    const progBar = document.getElementById(`progress_bar_${activeScrubVideoId}`);
    if (progBar) progBar.style.width = `${pct * 100}%`;
    const tip = document.getElementById(`scrub_tip_${activeScrubVideoId}`);
    if (tip) {
        const ctx = getPlayerContext(activeScrubVideoId);
        let dur = 0;
        if (ctx.isDirect && ctx.direct) dur = ctx.direct.duration || 0;
        else if (ctx.yt && typeof ctx.yt.getDuration === 'function') dur = ctx.yt.getDuration() || 0;
        if (dur > 0) {
            tip.textContent = formatDurationSec(pct * dur);
            tip.style.display = 'block';
            tip.style.left = `${Math.max(16, Math.min(rect.width - 16, e.clientX - rect.left))}px`;
        }
    }
});

document.addEventListener('mouseup', function(e) {
    if (activeScrubVideoId) {
        const wrap = document.getElementById(`scrubber_wrap_${activeScrubVideoId}`);
        if (wrap) {
            wrap.classList.remove('is-scrubbing');
            const rect = wrap.getBoundingClientRect();
            const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
            seekToPercentage(activeScrubVideoId, pct);
        }
        hideScrubberTooltip(activeScrubVideoId);
        activeScrubVideoId = null;
    }
});

document.addEventListener('touchmove', function(e) {
    if (!activeScrubVideoId || !e.touches || !e.touches[0]) return;
    const wrap = document.getElementById(`scrubber_wrap_${activeScrubVideoId}`);
    if (!wrap) return;
    const rect = wrap.getBoundingClientRect();
    const pct = Math.max(0, Math.min(1, (e.touches[0].clientX - rect.left) / rect.width));
    const progBar = document.getElementById(`progress_bar_${activeScrubVideoId}`);
    if (progBar) progBar.style.width = `${pct * 100}%`;
}, { passive: true });

document.addEventListener('touchend', function(e) {
    if (activeScrubVideoId) {
        const wrap = document.getElementById(`scrubber_wrap_${activeScrubVideoId}`);
        if (wrap) wrap.classList.remove('is-scrubbing');
        hideScrubberTooltip(activeScrubVideoId);
        activeScrubVideoId = null;
    }
});

// التعامل الذكي مع نقر سطح المشغل (نقرة واحدة للتشغيل/الإيقاف، نقرتان للتكبير دون تشويش)
let clickSurfaceTimers = {};

function handleClickSurface(videoId, e) {
    if (e && e.target && e.target.closest && e.target.closest('.ed-player-bottom-panel, .ed-player-top-header, .ed-popover-menu, .ed-ctrl-btn, .ed-ctrl-pill, .ed-volume-group, .ed-scrubber-wrapper, input, button')) {
        return;
    }
    if (clickSurfaceTimers[videoId]) {
        clearTimeout(clickSurfaceTimers[videoId]);
        delete clickSurfaceTimers[videoId];
        togglePlatformFullscreen(videoId, e);
        return;
    }
    clickSurfaceTimers[videoId] = setTimeout(() => {
        delete clickSurfaceTimers[videoId];
        toggleStudentPlayback(videoId);
    }, 240);
}

function seekStudentRelative(videoId, secondsOffset, e) {
    if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
    const ctx = getPlayerContext(videoId);
    if (ctx.isDirect && ctx.direct) {
        const cur = ctx.direct.currentTime || 0;
        const dur = ctx.direct.duration || 0;
        ctx.direct.currentTime = Math.max(0, Math.min(dur || 999999, cur + secondsOffset));
        ctx.direct.play().catch(() => {});
        updateStudentTimeDisplay(videoId);
        return;
    }

    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    let cur = 0;
    let dur = 0;
    if (yt && typeof yt.getCurrentTime === 'function' && typeof yt.getDuration === 'function') {
        try {
            cur = yt.getCurrentTime() || 0;
            dur = yt.getDuration() || 0;
            const target = Math.max(0, Math.min(dur, cur + secondsOffset));
            yt.seekTo(target, true);
            yt.playVideo();
            updateStudentTimeDisplay(videoId);
            return;
        } catch(e) {}
    }

    const ytIframe = document.getElementById(`player_yt_${videoId}`);
    if (ytIframe && ytIframe.contentWindow) {
        ytIframe.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: 'seekTo',
            args: [Math.max(0, cur + secondsOffset), true]
        }), '*');
        ytIframe.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: 'playVideo',
            args: []
        }), '*');
    }
}

function formatDurationSec(sec) {
    if (!sec || isNaN(sec)) return '00:00';
    sec = Math.floor(sec);
    const m = Math.floor(sec / 60);
    const s = sec % 60;
    return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
}

// تحديث العداد الزمني والبارات
function updateStudentTimeDisplay(videoId) {
    const ctx = getPlayerContext(videoId);
    let cur = 0;
    let dur = 0;
    let loaded = 0;

    if (ctx.isDirect && ctx.direct) {
        cur = ctx.direct.currentTime || 0;
        dur = ctx.direct.duration || 0;
        if (ctx.direct.buffered && ctx.direct.buffered.length > 0 && dur > 0) {
            loaded = ctx.direct.buffered.end(ctx.direct.buffered.length - 1) / dur;
        }
    } else if (ctx.yt && typeof ctx.yt.getCurrentTime === 'function' && typeof ctx.yt.getDuration === 'function') {
        try {
            cur = ctx.yt.getCurrentTime() || 0;
            dur = ctx.yt.getDuration() || 0;
            loaded = typeof ctx.yt.getVideoLoadedFraction === 'function' ? ctx.yt.getVideoLoadedFraction() : 0;
        } catch(e) {}
    }

    const timeTxt = document.getElementById(`time_txt_${videoId}`);
    if (timeTxt) timeTxt.textContent = `${formatDurationSec(cur)} / ${formatDurationSec(dur)}`;

    const progBar = document.getElementById(`progress_bar_${videoId}`);
    if (progBar && dur > 0 && activeScrubVideoId !== videoId) {
        progBar.style.width = `${(cur / dur) * 100}%`;
    }

    const bufBar = document.getElementById(`buffer_bar_${videoId}`);
    if (bufBar) {
        bufBar.style.width = `${Math.min(100, loaded * 100)}%`;
    }
}

function startStudentProgressTracking(videoId) {
    stopStudentProgressTracking(videoId);
    studentProgressIntervals[videoId] = setInterval(() => {
        updateStudentTimeDisplay(videoId);
    }, 400);
}

function stopStudentProgressTracking(videoId) {
    if (studentProgressIntervals[videoId]) {
        clearInterval(studentProgressIntervals[videoId]);
        delete studentProgressIntervals[videoId];
    }
}

function toggleDropdownMenu(menuId, e) {
    if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
    const menu = document.getElementById(menuId);
    if (!menu) return;
    const isShown = menu.classList.contains('show');
    document.querySelectorAll('.ed-popover-menu').forEach(m => m.classList.remove('show'));
    if (!isShown) menu.classList.add('show');
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.ed-speed-dropdown') && !e.target.closest('.ed-quality-dropdown')) {
        document.querySelectorAll('.ed-popover-menu').forEach(m => m.classList.remove('show'));
    }
});

// تحديث واجهة أزرار ملء الشاشة المتزامنة
function updateFullscreenButtonsUI(videoId, isFs) {
    const cardBtn = document.querySelector(`#card_video_${videoId} .ed-btn-action-fs`);
    if (cardBtn) {
        if (isFs) {
            cardBtn.innerHTML = '<i class="fa-solid fa-compress"></i> <span>' + '{{ __("إلغاء التكبير") }}' + '</span>';
            cardBtn.title = '{{ __("إلغاء ملء الشاشة (Esc)") }}';
        } else {
            cardBtn.innerHTML = '<i class="fa-solid fa-expand"></i> <span>' + '{{ __("تكبير الشاشة") }}' + '</span>';
            cardBtn.title = '{{ __("تكبير العرض بملء الشاشة") }}';
        }
    }
    const innerFsBtn = document.querySelector(`#player_frame_${videoId} .ed-ctrl-btn.fs i`);
    if (innerFsBtn) {
        innerFsBtn.className = isFs ? 'fa-solid fa-compress' : 'fa-solid fa-expand';
    }
}

// تكبير المشغل الأكاديمي بملء الشاشة الفاخر مع دعم كافة الأجهزة والمتصفحات
function togglePlatformFullscreen(videoId, e) {
    if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
    let frame = null;
    if (typeof videoId === 'string' || typeof videoId === 'number') {
        frame = document.getElementById(`player_frame_${videoId}`) || document.getElementById(`shield_wrap_${videoId}`) || document.getElementById(`card_video_${videoId}`);
    } else if (videoId && videoId.closest) {
        frame = videoId.closest('.ed-player-frame') || videoId.closest('.ed-video-card');
    }
    if (!frame) {
        const activeHover = document.querySelector('.ed-player-frame:hover, .ed-player-frame:fullscreen, .ed-player-frame:-webkit-full-screen, .ed-player-frame.is-platform-fullscreen');
        if (activeHover) frame = activeHover;
    }
    if (!frame) return;

    const actualVidId = frame.id.replace('player_frame_', '').replace('shield_wrap_', '').replace('card_video_', '');

    const isFs = !!(
        document.fullscreenElement || 
        document.webkitFullscreenElement || 
        document.mozFullScreenElement || 
        document.msFullscreenElement || 
        frame.classList.contains('is-platform-fullscreen')
    );

    if (!isFs) {
        // Enter Fullscreen
        let req = frame.requestFullscreen || 
                  frame.webkitRequestFullscreen || 
                  frame.webkitRequestFullScreen || 
                  frame.mozRequestFullScreen || 
                  frame.msRequestFullscreen;

        let nativeAttempted = false;
        if (req) {
            try {
                let res = req.call(frame);
                nativeAttempted = true;
                if (res && typeof res.catch === 'function') {
                    res.catch(function() {
                        enterFallbackFullscreen(frame, actualVidId);
                    });
                }
            } catch (err) {
                enterFallbackFullscreen(frame, actualVidId);
            }
        }
        if (!nativeAttempted) {
            enterFallbackFullscreen(frame, actualVidId);
        }
    } else {
        // Exit Fullscreen
        try {
            if (document.exitFullscreen) {
                document.exitFullscreen().catch(() => {});
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        } catch(err) {}
        exitFallbackFullscreen(frame, actualVidId);
    }
}

function enterFallbackFullscreen(frame, videoId) {
    const vidEl = document.getElementById(`player_vid_${videoId}`);
    const isIosPhone = /iPhone|iPod/.test(navigator.userAgent);
    if (isIosPhone && vidEl && vidEl.webkitEnterFullscreen && !frame.classList.contains('has-youtube-shield')) {
        try {
            vidEl.webkitEnterFullscreen();
            return;
        } catch(err) {}
    }

    frame.classList.add('is-platform-fullscreen');
    document.body.classList.add('has-fullscreen-player');
    const card = document.getElementById(`card_video_${videoId}`) || frame.closest('.ed-video-card');
    if (card) {
        card.classList.add('has-fullscreen-child');
    }
    updateFullscreenButtonsUI(videoId, true);
    
    let exitBtn = document.getElementById(`fs_exit_btn_${videoId}`);
    if (!exitBtn) {
        exitBtn = document.createElement('button');
        exitBtn.id = `fs_exit_btn_${videoId}`;
        exitBtn.className = 'ed-fs-exit-floating-btn';
        exitBtn.innerHTML = '<i class="fa-solid fa-compress"></i> <span>' + '{{ __("إلغاء ملء الشاشة") }}' + '</span>';
        exitBtn.onclick = function(e) {
            e.stopPropagation();
            togglePlatformFullscreen(videoId);
        };
        frame.appendChild(exitBtn);
    }
    exitBtn.style.display = 'inline-flex';
}

function exitFallbackFullscreen(frame, videoId) {
    if (frame) frame.classList.remove('is-platform-fullscreen');
    document.body.classList.remove('has-fullscreen-player');
    const card = document.getElementById(`card_video_${videoId}`) || (frame ? frame.closest('.ed-video-card') : null);
    if (card) {
        card.classList.remove('has-fullscreen-child');
    }
    updateFullscreenButtonsUI(videoId, false);
    const exitBtn = document.getElementById(`fs_exit_btn_${videoId}`);
    if (exitBtn) exitBtn.style.display = 'none';
}

function syncFullscreenState() {
    const isFs = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
    if (!isFs) {
        document.querySelectorAll('.ed-player-frame.is-platform-fullscreen').forEach(f => {
            const vidId = f.id.replace('player_frame_', '');
            exitFallbackFullscreen(f, vidId);
        });
        document.querySelectorAll('.ed-video-card.has-fullscreen-child').forEach(c => {
            c.classList.remove('has-fullscreen-child');
        });
        document.body.classList.remove('has-fullscreen-player');
        document.querySelectorAll('.ed-btn-action-fs').forEach(btn => {
            btn.innerHTML = '<i class="fa-solid fa-expand"></i> <span>' + '{{ __("تكبير الشاشة") }}' + '</span>';
            btn.title = '{{ __("تكبير العرض بملء الشاشة") }}';
        });
        document.querySelectorAll('.ed-ctrl-btn.fs i').forEach(icon => {
            icon.className = 'fa-solid fa-expand';
        });
    } else {
        const curFs = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
        if (curFs) {
            const vidId = curFs.id ? curFs.id.replace('player_frame_', '') : '';
            if (vidId) {
                updateFullscreenButtonsUI(vidId, true);
            }
        }
    }
}

document.addEventListener('fullscreenchange', syncFullscreenState);
document.addEventListener('webkitfullscreenchange', syncFullscreenState);
document.addEventListener('mozfullscreenchange', syncFullscreenState);
document.addEventListener('MSFullscreenChange', syncFullscreenState);

// اختصارات لوحة المفاتيح الأكاديمية الذكية
document.addEventListener('keydown', function(e) {
    if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;
    
    // الخروج من التكبير عند الضغط على Escape
    if (e.key === 'Escape' || e.code === 'Escape') {
        document.querySelectorAll('.ed-player-frame.is-platform-fullscreen').forEach(f => {
            const vidId = f.id.replace('player_frame_', '');
            exitFallbackFullscreen(f, vidId);
        });
        return;
    }

    const activeFrame = document.querySelector('.ed-player-frame:hover, .ed-player-frame:fullscreen, .ed-player-frame.is-platform-fullscreen');
    if (!activeFrame) return;
    const videoId = activeFrame.id.replace('player_frame_', '');
    if (!videoId) return;

    if (e.code === 'Space') {
        e.preventDefault();
        toggleStudentPlayback(videoId);
    } else if (e.code === 'ArrowRight') {
        e.preventDefault();
        seekStudentRelative(videoId, 10);
    } else if (e.code === 'ArrowLeft') {
        e.preventDefault();
        seekStudentRelative(videoId, -10);
    } else if (e.code === 'KeyM') {
        e.preventDefault();
        toggleStudentMute(videoId);
    } else if (e.code === 'KeyF') {
        e.preventDefault();
        togglePlatformFullscreen(videoId);
    }
});

// ربط أحداث الفيديو المباشر تلقائياً فور تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('video[id^="player_vid_"]').forEach(vEl => {
        const vidId = vEl.id.replace('player_vid_', '');
        vEl.addEventListener('play', () => {
            const barBtn = document.getElementById(`bar_btn_${vidId}`);
            if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
            const pauseOverlay = document.getElementById(`pause_overlay_${vidId}`);
            if (pauseOverlay) pauseOverlay.style.display = 'none';
            const poster = document.getElementById(`poster_${vidId}`);
            if (poster) poster.style.display = 'none';
            startStudentProgressTracking(vidId);
            registerPlayerActivity(vidId);
        });
        vEl.addEventListener('pause', () => {
            const barBtn = document.getElementById(`bar_btn_${vidId}`);
            if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
            const pauseOverlay = document.getElementById(`pause_overlay_${vidId}`);
            if (pauseOverlay) pauseOverlay.style.display = 'flex';
            stopStudentProgressTracking(vidId);
        });
        vEl.addEventListener('ended', () => {
            const barBtn = document.getElementById(`bar_btn_${vidId}`);
            if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-rotate-right"></i>';
            const pauseOverlay = document.getElementById(`pause_overlay_${vidId}`);
            if (pauseOverlay) pauseOverlay.style.display = 'flex';
            stopStudentProgressTracking(vidId);
        });
        vEl.addEventListener('timeupdate', () => {
            updateStudentTimeDisplay(vidId);
        });
        applyStoredQualityPreference(vidId);
    });
});

function getVideoCurrentTime(videoId) {
    const ctx = getPlayerContext(videoId);
    if (ctx.isDirect && ctx.direct && !isNaN(ctx.direct.currentTime)) {
        return Math.floor(ctx.direct.currentTime);
    }
    const player = document.getElementById(`player_vid_${videoId}`) || document.getElementById(`player_${videoId}`);
    if (player && !isNaN(player.currentTime) && player.currentTime > 0) {
        return Math.floor(player.currentTime);
    }

    let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
    if (yt && typeof yt.getCurrentTime === 'function') {
        try {
            const t = yt.getCurrentTime();
            if (!isNaN(t)) return Math.floor(t);
        } catch(e) {}
    }

    return 0;
}

function seekVideoTo(videoId, seconds) {
    const ctx = getPlayerContext(videoId);
    if (ctx.isDirect && ctx.direct) {
        ctx.direct.currentTime = seconds;
        ctx.direct.play().catch(() => {});
        updateStudentTimeDisplay(videoId);
    } else {
        let yt = ytPlayers[videoId] || initSingleYtPlayer(`player_yt_${videoId}`);
        if (yt && typeof yt.seekTo === 'function') {
            try {
                yt.seekTo(seconds, true);
                yt.playVideo();
                updateStudentTimeDisplay(videoId);
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
    const player = document.getElementById(`player_vid_${videoId}`) || document.getElementById(`player_${videoId}`);
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


// منع القائمة المنبثقة بالزر الأيمن على مشغل الفيديو نهائياً لمنع نسخ الروابط
document.addEventListener('contextmenu', function(e) {
    if (e.target.closest('.ed-player-frame, .ed-yt-frame-container, .ed-yt-viewport, .ed-yt-top-shield, .ed-yt-corner-shield, .ed-pure-player-layer, .ed-video-card, iframe, video')) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
}, true);

// حظر فتح أي روابط لليوتيوب نهائياً لضمان بقاء الطالب داخل بيئة المنصة الأكاديمية
try {
    const _origWinOpen = window.open;
    window.open = function(url, target, features) {
        if (typeof url === 'string' && (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('youtube-nocookie.com'))) {
            console.warn('[Security] Direct YouTube access blocked.');
            showPlayerToast('{{ __('التشغيل محمي ومقيد حصرياً داخل المنصة التعليمية') }}');
            return null;
        }
        return _origWinOpen.call(window, url, target, features);
    };
} catch(e) {}

// مزامنة حالة مشغل يوتيوب تلقائياً عبر postMessage لضمان حجب أي فيديوهات مقترحة وشاشات خارجية
window.addEventListener('message', function(event) {
    if (!event.data) return;
    try {
        const payload = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
        if (payload && (payload.event === 'onStateChange' || payload.info !== undefined)) {
            const state = typeof payload.info === 'number' ? payload.info : payload.data;
            if (typeof state === 'number') {
                document.querySelectorAll('iframe[id^="player_yt_"]').forEach(iframe => {
                    if (iframe.contentWindow === event.source) {
                        const vidId = iframe.id.replace('player_yt_', '');
                        handleYtStateChange(vidId, state);
                    }
                });
            }
        }
    } catch(e) {}
});

// حظر اختصارات فحص العنصر لحماية روابط المحتوى الأكاديمي ومنع استخراج الرابط الأصلي
document.addEventListener('keydown', function(e) {
    if (
        e.key === 'F12' || 
        (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) || 
        (e.ctrlKey && (e.key === 'u' || e.key === 'U'))
    ) {
        const activePlayer = document.querySelector('.ed-player-frame:hover, .ed-player-frame.is-platform-fullscreen');
        if (activePlayer) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }
}, true);

// تهيئة المشغلات فوراً عند جاهزية واجهة يوتيوب
if (window.YT && window.YT.Player) {
    initAllYouTubePlayers();
} else {
    const prevYtReady = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = function() {
        if (typeof prevYtReady === 'function') prevYtReady();
        initAllYouTubePlayers();
    };
}

function initAllPlayerComponents() {
    initAllYouTubePlayers();
    document.querySelectorAll('.ed-scrubber-wrapper').forEach(wrap => {
        const vidId = wrap.id.replace('scrubber_wrap_', '');
        if (vidId) initScrubberEvents(vidId);
    });
    document.querySelectorAll('.ed-player-frame').forEach(frame => {
        const vidId = frame.id.replace('player_frame_', '');
        if (vidId) {
            applyStoredQualityPreference(vidId);
            applyStoredSpeedPreference(vidId);
        }
    });
}

document.addEventListener('DOMContentLoaded', initAllPlayerComponents);

// إزالة أي أزرار تحميل خارجي للملفات الخام لضمان الحفظ حصرياً داخل المنصة
function purgeExternalDownloadButtons() {
    document.querySelectorAll('.ed-btn-lecture-video-download, a[download][href*="downloadVideo"], a[href*="force_download=1"]').forEach(el => el.remove());
}
purgeExternalDownloadButtons();
document.addEventListener('DOMContentLoaded', purgeExternalDownloadButtons);

</script>
<script src="https://www.youtube.com/iframe_api"></script>
@endsection
