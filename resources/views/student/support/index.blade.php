@extends('layouts.app')

@section('title', __('مركز الدعم والمساعدة الأكاديمية') . ' | ' . config('app.name', 'منارة التوجيهي'))

@section('content')
<div class="support-classic-container">
    <div class="support-classic-card" id="support_container">

        <!-- القائمة الجانبية (المشرفون والدعم) -->
        <aside class="support-classic-sidebar" id="support_sidebar">
            <div class="support-sidebar-header">
                <div class="header-brand-row">
                    <div class="brand-icon-pill">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div>
                        <h3 class="brand-headline">{{ __('الدعم الأكاديمي') }}</h3>
                        <p class="brand-subtext">{{ __('تواصل مع المشرفين المعتمدين') }}</p>
                    </div>
                </div>

                <div class="support-search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-ico"></i>
                    <input type="text" id="admin_search" placeholder="{{ __('بحث عن مشرف أو دعم...') }}" autocomplete="off">
                </div>
            </div>

            <div class="support-admins-list" id="support-list">
                @isset($support)
                    @forelse($support as $index => $admin)
                    <div class="support-admin-card {{ $index === 0 ? 'active' : '' }}"
                         id="admin_card_{{ $admin->id }}"
                         onclick="loadSupportChat({{ $admin->id }}, '{{ addslashes($admin->name) }}')">
                        
                        <div class="admin-avatar-box">
                            {{ mb_substr($admin->name, 0, 1) }}
                            <span class="online-status-dot"></span>
                        </div>
                        
                        <div class="admin-meta-box">
                            <div class="admin-name-row">
                                <h4 class="admin-name-text">{{ $admin->name }}</h4>
                                <span class="admin-role-badge">{{ __('مشرف') }}</span>
                            </div>
                            <p class="admin-status-desc">{{ __('متواجد لخدمتك ومساعدتك') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="empty-support-state">
                        <i class="fa-solid fa-user-shield"></i>
                        <p>{{ __('لا يوجد مشرفين متاحين حالياً') }}</p>
                    </div>
                    @endforelse
                @endisset
            </div>
        </aside>

        <!-- منطقة الدردشة الرئيسية -->
        <main class="support-classic-main" id="support_main">
            <!-- Active Header -->
            <header class="support-chat-header">
                <div class="chat-header-profile">
                    <button type="button" class="mobile-back-btn" onclick="toggleSupportMobile('sidebar')" title="{{ __('العودة للقائمة') }}">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    
                    <div class="header-avatar-circle" id="active_avatar">
                        {{ isset($support) && $support->count() > 0 ? mb_substr($support->first()->name, 0, 1) : '🛡️' }}
                    </div>

                    <div class="header-info-details">
                        <div class="name-and-pill">
                            <h3 class="active-admin-title" id="active_name">
                                {{ isset($support) && $support->count() > 0 ? $support->first()->name : __('اختر مشرفاً') }}
                            </h3>
                            <span class="platform-support-badge">{{ __('فريق الدعم الفني والأكاديمي') }}</span>
                        </div>
                        <div class="online-state-line">
                            <span class="green-pulse"></span>
                            <span>{{ __('متصل ومتاح للرد على استفساراتك') }}</span>
                        </div>
                    </div>
                </div>

                <div class="header-student-tag" style="display: flex; align-items: center; gap: 8px;">
                    <button type="button" class="tool-btn" id="soundToggleBtn" onclick="toggleAudioChime()" title="{{ __('كتم/تفعيل صوت التنبيه') }}" style="width: 36px; height: 36px; border-radius: 8px; background: #f8fafc; border: 1px solid var(--sup-border); color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-volume-high" id="soundIcon"></i>
                    </button>
                    <span class="branch-pill">
                        <i class="fa-solid fa-graduation-cap"></i>
                        {{ $studentStage ?? __('طالب توجيهي') }}
                    </span>
                </div>
            </header>

            <!-- Messages Area -->
            <div class="support-messages-stream" id="chat_messages">
                <div class="support-welcome-placeholder">
                    <div class="welcome-icon-circle">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h4>{{ __('أهلاً بك في الدعم الأكاديمي المباشر') }}</h4>
                    <p>{{ __('اختر استفساراً من المقترحات السريعة بالأسفل أو اكتب رسالتك مباشرة للمشرف.') }}</p>
                </div>
            </div>

            <!-- Quick Student Chips -->
            <div class="support-quick-chips-bar">
                <span class="quick-title"><i class="fa-solid fa-bolt"></i> {{ __('استفسارات شائعة:') }}</span>
                <div class="chips-scroll-lane">
                    <button type="button" class="quick-chip" onclick="insertSupportPrompt('السلام عليكم، لدي استفسار بخصوص تفعيل اشتراك المواد الدراسية.')">
                        💳 تفعيل الاشتراك
                    </button>
                    <button type="button" class="quick-chip" onclick="insertSupportPrompt('لدي مشكلة في فتح أسئلة الاختبار أو تسليم الحل.')">
                        📝 مشكلة في الاختبار
                    </button>
                    <button type="button" class="quick-chip" onclick="insertSupportPrompt('أرجو المساعدة في تحديث بيانات حسابي والفرع الدراسي.')">
                        ⚙️ تحديث البيانات
                    </button>
                    <button type="button" class="quick-chip" onclick="insertSupportPrompt('كيف يمكنني التواصل مع معلم المادة لمراجعة سؤال؟')">
                        👨‍🏫 التواصل مع المعلم
                    </button>
                </div>
            </div>

            <!-- Composer Area -->
            <div class="support-composer-pane">
                <div class="composer-field-wrapper">
                    <input type="text" id="msg_input" placeholder="{{ __('اكتب رسالتك للمشرف هنا... (اضغط Enter للإرسال)') }}" autocomplete="off">
                    <button type="button" class="support-send-btn" id="btn_send" onclick="sendSupportMessage()" title="{{ __('إرسال الرسالة') }}">
                        <span>{{ __('إرسال') }}</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </main>

    </div>
</div>

<style>
    /* =========================================================
       CLASSIC SUPPORT STYLING (Prestige Academic Design)
       ========================================================= */
    :root {
        --sup-navy-dark: #0f172a;
        --sup-navy-main: #1e3a8a;
        --sup-navy-light: #2563eb;
        --sup-bg: #f8fafc;
        --sup-surface: #ffffff;
        --sup-border: #e2e8f0;
        --sup-text-dark: #0f172a;
        --sup-text-muted: #64748b;
        --sup-emerald: #10b981;
        --sup-radius: 16px;
    }

    .support-classic-container {
        padding: 16px 20px 30px;
        max-width: 1400px;
        margin: 0 auto;
        box-sizing: border-box;
    }

    .support-classic-card {
        display: grid;
        grid-template-columns: 340px 1fr;
        height: calc(100vh - 130px);
        min-height: 560px;
        background: var(--sup-surface);
        border: 1px solid var(--sup-border);
        border-radius: var(--sup-radius);
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.08);
    }

    /* Sidebar */
    .support-classic-sidebar {
        display: flex;
        flex-direction: column;
        background: var(--sup-surface);
        border-left: 1px solid var(--sup-border);
        height: 100%;
        min-width: 0;
    }

    .support-sidebar-header {
        padding: 18px 18px 14px;
        background: var(--sup-surface);
        border-bottom: 1px solid var(--sup-border);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .header-brand-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .brand-icon-pill {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #eff6ff;
        color: var(--sup-navy-main);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        border: 1px solid #dbeafe;
    }

    .brand-headline {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--sup-text-dark);
        margin: 0;
    }

    .brand-subtext {
        font-size: 0.74rem;
        color: var(--sup-text-muted);
        margin: 0;
    }

    .support-search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .support-search-wrapper .search-ico {
        position: absolute;
        right: 14px;
        color: #94a3b8;
        font-size: 0.85rem;
        pointer-events: none;
    }

    .support-search-wrapper input {
        width: 100%;
        padding: 9px 38px 9px 12px;
        background: #f8fafc;
        border: 1px solid var(--sup-border);
        border-radius: 10px;
        font-size: 0.84rem;
        color: var(--sup-text-dark);
        font-family: inherit;
        outline: none;
        transition: all 0.2s ease;
    }

    .support-search-wrapper input:focus {
        background: #ffffff;
        border-color: var(--sup-navy-light);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .support-admins-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .support-admin-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 12px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 4px;
        border: 1px solid transparent;
        background: #ffffff;
    }

    .support-admin-card:hover {
        background: #f8fafc;
        border-color: var(--sup-border);
    }

    .support-admin-card.active {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .admin-avatar-box {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        position: relative;
        flex-shrink: 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
    }

    .online-status-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: var(--sup-emerald);
        border: 2px solid #ffffff;
    }

    .admin-meta-box {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .admin-name-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
    }

    .admin-name-text {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--sup-text-dark);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .admin-role-badge {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
    }

    .support-admin-card.active .admin-role-badge {
        background: #dbeafe;
        color: #1e40af;
    }

    .admin-status-desc {
        font-size: 0.74rem;
        color: var(--sup-text-muted);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .empty-support-state {
        padding: 40px 16px;
        text-align: center;
        color: var(--sup-text-muted);
    }

    .empty-support-state i {
        font-size: 2rem;
        color: #cbd5e1;
        margin-bottom: 8px;
        display: block;
    }

    .empty-support-state p {
        font-size: 0.84rem;
        margin: 0;
    }

    /* Main Chat */
    .support-classic-main {
        display: flex;
        flex-direction: column;
        background: var(--sup-bg);
        height: 100%;
        min-width: 0;
        position: relative;
    }

    .support-chat-header {
        padding: 12px 20px;
        background: #ffffff;
        border-bottom: 1px solid var(--sup-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        z-index: 5;
    }

    .chat-header-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .mobile-back-btn {
        display: none;
        background: none;
        border: none;
        color: var(--sup-text-dark);
        font-size: 1.1rem;
        cursor: pointer;
        padding: 6px;
    }

    .header-avatar-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.15rem;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
    }

    .header-info-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .name-and-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .active-admin-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--sup-text-dark);
        margin: 0;
    }

    .platform-support-badge {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #dbeafe;
    }

    .online-state-line {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.74rem;
        color: var(--sup-text-muted);
    }

    .green-pulse {
        width: 7px;
        height: 7px;
        background: var(--sup-emerald);
        border-radius: 50%;
        display: inline-block;
    }

    .branch-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 8px;
        background: #f1f5f9;
        border: 1px solid var(--sup-border);
        color: #334155;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .branch-pill i {
        color: var(--sup-navy-main);
    }

    /* Messages stream (NO DOTS) */
    .support-messages-stream {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #f8fafc;
    }

    .support-welcome-placeholder {
        margin: auto;
        text-align: center;
        color: var(--sup-text-muted);
        padding: 30px;
        max-width: 380px;
    }

    .welcome-icon-circle {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: #eff6ff;
        color: var(--sup-navy-main);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 16px;
        border: 1px solid #dbeafe;
    }

    .support-welcome-placeholder h4 {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--sup-text-dark);
        margin: 0 0 6px;
    }

    .support-welcome-placeholder p {
        font-size: 0.84rem;
        margin: 0;
        line-height: 1.5;
    }

    /* Chat Bubbles */
    .sup-msg-row {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        width: 100%;
    }

    .sup-msg-row.is-me {
        justify-content: flex-end;
        flex-direction: row-reverse;
    }

    .sup-msg-row.is-admin {
        justify-content: flex-start;
        flex-direction: row;
    }

    .sup-msg-bubble {
        max-width: 68%;
        min-width: 120px;
        padding: 11px 16px;
        font-size: 0.9rem;
        line-height: 1.6;
        word-break: break-word;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        animation: bubblePop 0.2s ease-out;
    }

    @keyframes bubblePop {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .sup-msg-row.is-me .sup-msg-bubble {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: #ffffff;
        border-radius: 14px 14px 4px 14px;
    }

    .sup-msg-row.is-admin .sup-msg-bubble {
        background: #ffffff;
        color: var(--sup-text-dark);
        border: 1px solid var(--sup-border);
        border-radius: 14px 14px 14px 4px;
    }

    .sup-bubble-author {
        font-size: 0.7rem;
        font-weight: 800;
        margin-bottom: 4px;
        display: block;
    }

    .sup-msg-row.is-me .sup-bubble-author {
        color: #93c5fd;
    }

    .sup-msg-row.is-admin .sup-bubble-author {
        color: var(--sup-navy-main);
    }

    .sup-bubble-text {
        white-space: pre-wrap;
    }

    .sup-bubble-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;
        margin-top: 5px;
        font-size: 0.66rem;
    }

    .sup-msg-row.is-me .sup-bubble-meta {
        color: rgba(255, 255, 255, 0.8);
    }

    .sup-msg-row.is-admin .sup-bubble-meta {
        color: var(--sup-text-muted);
    }

    /* Quick Chips */
    .support-quick-chips-bar {
        background: #ffffff;
        border-top: 1px solid var(--sup-border);
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        overflow-x: auto;
        white-space: nowrap;
    }

    .quick-title {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--sup-text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .quick-title i {
        color: #d97706;
    }

    .chips-scroll-lane {
        display: flex;
        gap: 8px;
        overflow-x: auto;
    }

    .quick-chip {
        background: #f8fafc;
        border: 1px solid var(--sup-border);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.76rem;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        font-family: inherit;
        font-weight: 600;
    }

    .quick-chip:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: var(--sup-navy-main);
    }

    /* Composer */
    .support-composer-pane {
        padding: 12px 18px;
        background: #ffffff;
        border-top: 1px solid var(--sup-border);
    }

    .composer-field-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid var(--sup-border);
        border-radius: 12px;
        padding: 5px 8px 5px 14px;
        transition: all 0.2s ease;
    }

    .composer-field-wrapper:focus-within {
        background: #ffffff;
        border-color: var(--sup-navy-light);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .composer-field-wrapper input {
        flex: 1;
        border: none;
        background: transparent;
        outline: none;
        font-family: inherit;
        font-size: 0.9rem;
        padding: 8px 0;
        color: var(--sup-text-dark);
    }

    .support-send-btn {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: #ffffff;
        border: none;
        padding: 9px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.25);
    }

    .support-send-btn:hover {
        background: #172554;
        box-shadow: 0 4px 10px rgba(30, 58, 138, 0.35);
    }

    .support-send-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Scrollbars */
    .support-admins-list::-webkit-scrollbar,
    .support-messages-stream::-webkit-scrollbar,
    .chips-scroll-lane::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    .support-admins-list::-webkit-scrollbar-thumb,
    .support-messages-stream::-webkit-scrollbar-thumb,
    .chips-scroll-lane::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .support-classic-container {
            padding: 8px;
        }
        .support-classic-card {
            grid-template-columns: 1fr;
            height: calc(100vh - 100px);
            border-radius: 12px;
        }
        .support-classic-main {
            display: none;
        }
        .support-classic-card.mobile-active .support-classic-sidebar {
            display: none;
        }
        .support-classic-card.mobile-active .support-classic-main {
            display: flex;
        }
        .mobile-back-btn {
            display: inline-block;
        }
        .sup-msg-bubble {
            max-width: 85%;
        }
        .support-send-btn span {
            display: none;
        }
    }
</style>

<script>
    let activeAdminId = {{ isset($support) && $support->count() > 0 ? $support->first()->id : 'null' }};
    let activeAdminName = "{{ isset($support) && $support->count() > 0 ? addslashes($support->first()->name) : 'مشرف' }}";

    function toggleSupportMobile(view) {
        const container = document.getElementById('support_container');
        if (view === 'chat') {
            container.classList.add('mobile-active');
        } else {
            container.classList.remove('mobile-active');
        }
    }

    function loadSupportChat(id, name) {
        activeAdminId = id;
        activeAdminName = name;
        
        toggleSupportMobile('chat');

        document.getElementById('active_name').innerText = name;
        document.getElementById('active_avatar').innerText = name.charAt(0);

        document.querySelectorAll('.support-admin-card').forEach(el => el.classList.remove('active'));
        const selectedCard = document.getElementById('admin_card_' + id);
        if (selectedCard) {
            selectedCard.classList.add('active');
        }

        fetchMessages();
    }

    let lastMessagesJson = "";
    let isSoundEnabled = true;
    let audioCtx = null;

    function playNotificationTone() {
        if (!isSoundEnabled) return;
        try {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
            osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.12); // A5
            gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.25);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.25);
        } catch(e) {
            console.log('Audio error:', e);
        }
    }

    function toggleAudioChime() {
        isSoundEnabled = !isSoundEnabled;
        const icon = document.getElementById('soundIcon');
        if (isSoundEnabled) {
            icon.className = 'fa-solid fa-volume-high';
            playNotificationTone();
        } else {
            icon.className = 'fa-solid fa-volume-xmark text-danger';
        }
    }

    function fetchMessages() {
        if (!activeAdminId) return;
        const container = document.getElementById('chat_messages');

        fetch("{{ url('student/support/fetch') }}/" + activeAdminId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.messages) {
                    const currentJson = JSON.stringify(data.messages);
                    if (currentJson === lastMessagesJson) return;

                    const hadPrevious = (lastMessagesJson !== "" && lastMessagesJson !== "[]");
                    lastMessagesJson = currentJson;

                    if (data.messages.length === 0) {
                        container.innerHTML = `
                            <div class="support-welcome-placeholder">
                                <div class="welcome-icon-circle">
                                    <i class="fa-regular fa-comment-dots"></i>
                                </div>
                                <h4>لا توجد رسائل سابقة</h4>
                                <p>ابدأ محادثتك مع المشرف الآن عبر كتابة استفسارك بالأسفل.</p>
                            </div>
                        `;
                    } else {
                        let html = '';
                        let hasNewAdminMsg = false;
                        data.messages.forEach(msg => {
                            const isMe = (msg.sender_type && msg.sender_type.toLowerCase() === 'student');
                            if (!isMe) hasNewAdminMsg = true;
                            const author = isMe ? 'أنت' : activeAdminName;
                            const time = msg.created_at_formatted || '';

                            html += `
                                <div class="sup-msg-row ${isMe ? 'is-me' : 'is-admin'}">
                                    <div class="sup-msg-bubble">
                                        <span class="sup-bubble-author">${escapeHtml(author)}</span>
                                        <div class="sup-bubble-text">${escapeHtml(msg.message)}</div>
                                        <div class="sup-bubble-meta">
                                            <span>${escapeHtml(time)}</span>
                                            ${isMe ? '<i class="fa-solid fa-check"></i>' : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        container.innerHTML = html;

                        if (hadPrevious && hasNewAdminMsg) {
                            playNotificationTone();
                        }
                    }
                    container.scrollTop = container.scrollHeight;
                }
            })
            .catch(err => console.error("Error fetching messages:", err));
    }

    function sendSupportMessage() {
        const input = document.getElementById('msg_input');
        const sendBtn = document.getElementById('btn_send');
        const text = input.value.trim();

        if (!text || !activeAdminId) return;

        sendBtn.disabled = true;
        const originalHtml = sendBtn.innerHTML;
        sendBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';

        fetch("{{ route('student.support.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text, admin_id: activeAdminId })
        })
        .then(res => res.json())
        .then(data => {
            input.value = '';
            fetchMessages();
        })
        .catch(err => {
            console.error("Error sending message:", err);
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = originalHtml;
            input.focus();
        });
    }

    function insertSupportPrompt(promptText) {
        const input = document.getElementById('msg_input');
        input.value = promptText;
        input.focus();
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Filter admins
    document.getElementById('admin_search').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.support-admin-card').forEach(item => {
            const name = item.querySelector('.admin-name-text')?.innerText.toLowerCase() || '';
            item.style.display = name.includes(term) ? 'flex' : 'none';
        });
    });

    // Enter key handler
    document.getElementById('msg_input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendSupportMessage();
        }
    });

    // Polling every 5 seconds
    setInterval(() => {
        if (activeAdminId) fetchMessages();
    }, 5000);

    document.addEventListener("DOMContentLoaded", function() {
        if (activeAdminId) {
            fetchMessages();
        }
    });
</script>
@endsection