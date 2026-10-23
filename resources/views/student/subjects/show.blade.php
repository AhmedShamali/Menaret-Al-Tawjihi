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

/* وضع المسرح الأكاديمي العريض للتركيز والدقة العالية */
.ed-video-card.is-theater-mode {
    position: relative;
    z-index: 40;
    margin-left: calc(-50vw + 50%) !important;
    margin-right: calc(-50vw + 50%) !important;
    max-width: 100vw !important;
    width: 100vw !important;
    border-radius: 0 !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.25) !important;
    transition: all 0.3s ease;
}

.youtube-player-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    background: #000;
    border-radius: 14px;
}

.youtube-player-wrapper iframe {
    display: block;
    width: 100%;
    height: 100%;
    border: 0;
}

.youtube-player-wrapper video {
    display: block;
    width: 100%;
    height: 100%;
    border: 0;
    object-fit: contain;
    background: #000;
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
            @if(empty($enrollment) && empty($isFullAccess))
                <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe; border-radius: 14px; padding: 16px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.05);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="width: 42px; height: 42px; border-radius: 10px; background: #1e3a8a; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                            <i class="fa-solid fa-gift"></i>
                        </span>
                        <div>
                            <h4 style="margin: 0 0 3px; font-size: 0.95rem; font-weight: 800; color: #1e3a8a;">
                                {{ __('أنت في وضع المعاينة المجانية | الحصة الأولى مفتوحة ومتاحة للتقييم 🎬') }}
                            </h4>
                            <p style="margin: 0; font-size: 0.8rem; color: #475569;">
                                {{ __('يمكنك مشاهدة الحصة التأسيسية الأولى مجاناً، وتواصل مع الإدارة لتفعيل المساق كاملاً وبنك الامتحانات.') }}
                            </p>
                        </div>
                    </div>
                    @php
                        $waDirect = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('contact_whatsapp', '970597694385')) ?: '970597694385';
                    @endphp
                    <a href="https://wa.me/{{ $waDirect }}?text={{ urlencode('مرحباً إدارة Step by Step، أود الاشتراك في مساق: ' . ($subject->name_ar ?? $subject->name)) }}" target="_blank" class="ed-btn-royal primary" style="padding: 9px 18px; font-size: 0.84rem; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>{{ __('طلب تفعيل المساق كاملاً') }}</span>
                    </a>
                </div>
            @endif

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
                                {{-- إطار مشغل الفيديو --}}
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
                                            $ytEmbed = $ytBase . "?enablejsapi=1&controls=1&rel=0&fs=1&playsinline=1&iv_load_policy=3";
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
                                @endphp

                                @if($hasPlayableVideo)
                                    @if(!empty($ytEmbed))
                                        <div class="youtube-player-wrapper" id="yt_wrapper_{{ $video->id }}">
                                            <iframe id="player_yt_{{ $video->id }}" 
                                                    data-yt-id="{{ $ytId ?? '' }}"
                                                    width="100%"
                                                    height="100%"
                                                    src="{{ $ytEmbed }}" 
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" 
                                                    allowfullscreen
                                                    referrerpolicy="strict-origin-when-cross-origin">
                                            </iframe>
                                        </div>
                                    @else
                                        <div class="youtube-player-wrapper direct-player-wrapper" id="direct_wrapper_{{ $video->id }}">
                                            <video id="player_vid_{{ $video->id }}" 
                                                   class="ed-native-video" 
                                                   playsinline 
                                                   controls
                                                   preload="metadata" 
                                                   controlsList="nodownload" 
                                                   oncontextmenu="return false;"
                                                   style="width: 100%; height: 100%; object-fit: contain; background: #000;"
                                                   onerror="handleStudentVideoError(this, '{{ $video->id }}')">
                                                <source src="{{ $directVideoUrl }}" type="video/mp4">
                                                @if($directVideoFallback && $directVideoFallback !== $directVideoUrl)
                                                    <source src="{{ $directVideoFallback }}" type="video/mp4">
                                                @endif
                                                {{ __('متصفحك لا يدعم مشغل الفيديو.') }}
                                            </video>
                                        </div>
                                    @endif
                                @elseif(!empty($rawUrl) && filter_var($rawUrl, FILTER_VALIDATE_URL))
                                    <div class="youtube-player-wrapper">
                                        <iframe id="player_ext_{{ $video->id }}" src="{{ $rawUrl }}" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
                                    </div>
                                @else
                                    <div style="position: relative; width: 100%; aspect-ratio: 16/9; display: flex; flex-direction: column; align-items: center; justify-content: center; background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); color: #f8fafc; padding: 24px; text-align: center; border-radius: 14px; overflow: hidden;">
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

                                {{-- جسم البطاقة: البيانات والملاحظات --}}
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
                                        </div>
                                    </div>

                                    {{-- عنوان الحصة المعتمد --}}
                                    <h3 class="ed-card-lesson-title">{{ $video->title }}</h3>
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
// YOUTUBE OFFICIAL IFRAME PLAYER ENGINE & DIRECT VIDEO STREAM ENGINE
// ==========================================================================
let ytPlayers = {};
let progressTimers = {};

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

// تهيئة المشغل الرسمي لليوتيوب
function onYouTubeIframeAPIReady() {
    initAllYouTubePlayers();
}

function initAllYouTubePlayers() {
    if (window.YT && window.YT.Player) {
        document.querySelectorAll('iframe[id^="player_yt_"]').forEach(iframe => {
            initSingleYtPlayer(iframe.id);
        });
    }
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
                        onPlayerReady(videoId, event);
                    },
                    'onStateChange': function(event) {
                        onPlayerStateChange(videoId, event);
                    },
                    'onError': function(event) {
                        onPlayerError(videoId, event);
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

function onPlayerReady(videoId, event) {
    // استعادة مكان تقدم الطالب المحفوظ
    try {
        const saved = localStorage.getItem('lesson_progress_' + videoId);
        if (saved) {
            const savedTime = parseFloat(saved);
            if (savedTime && savedTime > 5) {
                event.target.seekTo(savedTime, true);
            }
        }
    } catch(e) {}
}

function onPlayerStateChange(videoId, event) {
    if (!window.YT) return;
    if (event.data === YT.PlayerState.PLAYING) {
        startProgressTracking(videoId);
    } else if (event.data === YT.PlayerState.PAUSED) {
        saveLessonProgress(videoId);
    } else if (event.data === YT.PlayerState.ENDED) {
        clearInterval(progressTimers[videoId]);
        saveLessonProgress(videoId, true);
        markLessonCompleted(videoId);
    }
}

function onPlayerError(videoId, event) {
    console.warn('YouTube Player error code:', event.data, 'for video:', videoId);
}

function startProgressTracking(videoId) {
    clearInterval(progressTimers[videoId]);
    progressTimers[videoId] = setInterval(() => {
        saveLessonProgress(videoId);
    }, 5000);
}

function saveLessonProgress(videoId, isEnded = false) {
    const player = ytPlayers[videoId];
    if (!player || typeof player.getCurrentTime !== 'function') return;
    try {
        const current = player.getCurrentTime();
        const duration = player.getDuration();
        if (!duration) return;
        localStorage.setItem('lesson_progress_' + videoId, isEnded ? 0 : current);
    } catch(e) {}
}

function markLessonCompleted(videoId) {
    try {
        localStorage.setItem('lesson_completed_' + videoId, 'true');
    } catch(e) {}
}

// دمج نظام الملاحظات مع المشغل الرسمي
function getVideoCurrentTime(videoId) {
    const ctx = getPlayerContext(videoId);
    if (ctx.isDirect && ctx.direct && !isNaN(ctx.direct.currentTime)) {
        return Math.floor(ctx.direct.currentTime);
    }
    let yt = ytPlayers[videoId] || (window.YT && window.YT.Player ? initSingleYtPlayer(`player_yt_${videoId}`) : null);
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
    } else {
        let yt = ytPlayers[videoId] || (window.YT && window.YT.Player ? initSingleYtPlayer(`player_yt_${videoId}`) : null);
        if (yt && typeof yt.seekTo === 'function') {
            try {
                yt.seekTo(seconds, true);
                yt.playVideo();
            } catch(e) {}
        }
    }
    const card = document.getElementById(`card_video_${videoId}`);
    if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function scrollToVideoLesson(videoId) {
    const card = document.getElementById(`card_video_${videoId}`);
    if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function handleStudentVideoError(videoEl, videoId) {
    if (!videoEl) return;
    console.warn('Direct video error:', videoId, videoEl.currentSrc || videoEl.src);
}

function retryStudentVideo(videoId) {
    const player = document.getElementById(`player_vid_${videoId}`);
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


// حظر فتح أي روابط لليوتيوب في نوافذ جديدة لضمان بقاء الطالب داخل المنصة
try {
    const _origWinOpen = window.open;
    window.open = function(url, target, features) {
        if (typeof url === 'string' && (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('youtube-nocookie.com'))) {
            console.warn('[Security] YouTube direct window blocked.');
            showPlayerToast('{{ __('التشغيل محمي ومتاح حصرياً داخل المنصة التعليمية') }}');
            return null;
        }
        return _origWinOpen.call(window, url, target, features);
    };
} catch(e) {}

// اعتراض أي نقرات على روابط يوتيوب داخل الصفحة
document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href*="youtube.com"], a[href*="youtu.be"], a[href*="youtube-nocookie.com"]');
    if (link) {
        e.preventDefault();
        e.stopPropagation();
        showPlayerToast('{{ __('المشاهدة متوفرة مباشرة داخل الدرس') }}');
        return false;
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

document.addEventListener('DOMContentLoaded', function() {
    initAllYouTubePlayers();
});

</script>
<script src="https://www.youtube.com/iframe_api"></script>
@endsection
