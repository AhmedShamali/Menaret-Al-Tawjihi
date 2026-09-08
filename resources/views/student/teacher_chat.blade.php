@extends('layouts.app')

@section('title', 'محادثة الأستاذ ' . $teacher->name)

@section('content')
<div class="chat-page-wrapper" dir="rtl">
    <div class="chat-app-container">

        <!-- ترويسة المحادثة التفاعلية (Telegram / WhatsApp Style Header) -->
        <div class="chat-top-header">
            <div class="user-meta-group">
                <a href="{{ route('student.teachers.index') }}" class="back-action-btn" title="العودة لقائمة المعلمين">
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <div class="avatar-wrapper">
                    <div class="teacher-avatar-circle">
                        {{ mb_substr($teacher->name, 0, 1) }}
                    </div>
                    <span class="online-indicator-dot" title="متاح للتواصل التعليمي"></span>
                </div>
                <div class="user-details-text">
                    <div class="name-badge-row">
                        <h2 class="teacher-display-name">{{ $teacher->name }}</h2>
                        <span class="pal-tutor-badge">
                            <i class="fa-solid fa-graduation-cap"></i> معتمد توجيهي 🇵🇸
                        </span>
                    </div>
                    <div class="connection-status-text">
                        <span class="pulse-circle"></span>
                        <span id="status_text">متصل الآن • جاهز للإجابة عن أسئلة المنهاج</span>
                    </div>
                </div>
            </div>

            <!-- خيارات الهيدر السريعة -->
            <div class="header-action-tools">
                <button type="button" class="tool-btn" id="soundToggleBtn" onclick="toggleAudioChime()" title="كتم/تفعيل صوت الرسائل">
                    <i class="fa-solid fa-volume-high" id="soundIcon"></i>
                </button>
                <button type="button" class="tool-btn" onclick="scrollToBottomSmooth()" title="الانتقال لآخر رسالة">
                    <i class="fa-solid fa-angles-down"></i>
                </button>
            </div>
        </div>

        <!-- شريط كبسولات الأسئلة الوزارية السريعة (Quick Tawjihi Prompts) -->
        <div class="quick-prompts-bar">
            <span class="prompts-label"><i class="fa-solid fa-bolt text-warning"></i> أسئلة سريعة:</span>
            <div class="prompts-scroll-lane">
                <button type="button" class="prompt-pill" onclick="insertPrompt('أستاذ ممكن توضيح هذه النقطة في الدرس؟ ✍️')">
                    توضيح نقطة في الدرس ✍️
                </button>
                <button type="button" class="prompt-pill" onclick="insertPrompt('هل هذه الفكرة متوقعة في الامتحان الوزاري؟ 🎯')">
                    فكرة وزارية متوقعة 🎯
                </button>
                <button type="button" class="prompt-pill" onclick="insertPrompt('متى موعد الحصة التفاعلية المباشرة القادمة؟ ⏰')">
                    موعد الحصة القادمة ⏰
                </button>
                <button type="button" class="prompt-pill" onclick="insertPrompt('أرجو مراجعة حلي والتأكد من خطوات الحل 📝')">
                    مراجعة حل مسألة 📝
                </button>
                <button type="button" class="prompt-pill" onclick="insertPrompt('شكراً جزيلاً أستاذ وبارك الله في جهودك 🌹')">
                    شكراً وتقدير 🌹
                </button>
            </div>
        </div>

        <!-- شريط الإيموجي السريع -->
        <div class="quick-emoji-lane">
            <button type="button" class="emoji-tap" onclick="appendEmoji('🇵🇸')">🇵🇸</button>
            <button type="button" class="emoji-tap" onclick="appendEmoji('✍️')">✍️</button>
            <button type="button" class="emoji-tap" onclick="appendEmoji('💡')">💡</button>
            <button type="button" class="emoji-tap" onclick="appendEmoji('🎯')">🎯</button>
            <button type="button" class="emoji-tap" onclick="appendEmoji('❓')">❓</button>
            <button type="button" class="emoji-tap" onclick="appendEmoji('📚')">📚</button>
            <button type="button" class="emoji-tap" onclick="appendEmoji('👏')">👏</button>
            <button type="button" class="emoji-tap" onclick="appendEmoji('🔥')">🔥</button>
        </div>

        <!-- ساحة الرسائل التفاعلية (Telegram / WhatsApp Canvas) -->
        <div class="chat-viewport" id="chat_feed">
            <div class="date-badge-separator">
                <span>محادثة دراسية مباشرة • منهاج الثانوية العامة الفلسطيني</span>
            </div>

            <div id="loading_messages_state" class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;"></div>
                <p class="text-muted mt-2" style="font-size: 0.85rem;">جاري مزامنة المحادثة مع المعلم...</p>
            </div>

            <!-- حاوية الرسائل الديناميكية -->
            <div id="messages_list" class="messages-stack"></div>

            <!-- مؤشر الكتابة (Typing indicator) -->
            <div id="typing_indicator" class="typing-bubble-container" style="display: none;">
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
                <span class="typing-label">المعلم يكتب الآن...</span>
            </div>
        </div>

        <!-- زر الانتقال السريع لأسفل في حال التمرير لأعلى -->
        <button type="button" id="scrollToBottomFab" class="scroll-bottom-fab" onclick="scrollToBottomSmooth()" style="display: none;">
            <i class="fa-solid fa-arrow-down"></i>
            <span id="unread_floating_badge" class="unread-fab-count" style="display: none;">1</span>
        </button>

        <!-- شريط الإدخال المتطور (WhatsApp/Telegram Style Input Box) -->
        <div class="chat-composer-bar">
            <div class="composer-container">
                <div class="input-actions-before">
                    <span class="input-tag-icon"><i class="fa-regular fa-comment-dots"></i></span>
                </div>
                
                <textarea 
                    id="message_input" 
                    class="composer-textarea" 
                    placeholder="اكتب استفسارك لمعلمك بالتفصيل... (اضغط Enter للإرسال)" 
                    rows="1"
                    onkeydown="handleTextareaKeydown(event)"></textarea>

                <div class="input-actions-after">
                    <button type="button" id="sendBtn" class="composer-send-btn" onclick="sendChatMessage()" title="إرسال الرسالة">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
            <div class="composer-hint-row">
                <span>تلميح: اضغط Enter للإرسال، أو Shift + Enter لسطر جديد</span>
                <span id="char_counter" class="text-muted">0 / 1000</span>
            </div>
        </div>

    </div>
</div>

<style>
/* المتغيرات والتصميم العام بنمط تيليجرام / واتساب */
:root {
    --chat-max-width: 950px;
    --chat-height: calc(100vh - 120px);
    --bubble-me-bg: linear-gradient(135deg, #059669 0%, #10b981 100%);
    --bubble-me-color: #ffffff;
    --bubble-them-bg: #ffffff;
    --bubble-them-color: #0f172a;
    --chat-canvas-bg: #f0f2f5;
    --primary-teal: #0d9488;
}

body.dark-theme {
    --chat-canvas-bg: #0b141a;
    --bubble-me-bg: linear-gradient(135deg, #047857 0%, #059669 100%);
    --bubble-me-color: #f1f5f9;
    --bubble-them-bg: #1e293b;
    --bubble-them-color: #f8fafc;
}

.chat-page-wrapper {
    max-width: var(--chat-max-width);
    margin: 1rem auto;
    padding: 0 0.75rem;
}

.chat-app-container {
    height: var(--chat-height);
    min-height: 550px;
    background: var(--chat-canvas-bg);
    border-radius: 24px;
    box-shadow: 0 16px 40px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.07);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}

/* 1. الترويسة العلوية */
.chat-top-header {
    background: #ffffff;
    padding: 12px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    z-index: 10;
}

body.dark-theme .chat-top-header {
    background: #0f172a;
    border-bottom-color: #1e293b;
}

.user-meta-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.back-action-btn {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 1rem;
    transition: all 0.2s;
}

.back-action-btn:hover {
    background: #e2e8f0;
    color: #0284c7;
}

.avatar-wrapper {
    position: relative;
}

.teacher-avatar-circle {
    width: 46px;
    height: 46px;
    border-radius: 16px;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: white;
    font-size: 1.25rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
}

.online-indicator-dot {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 13px;
    height: 13px;
    background: #10b981;
    border: 2px solid #ffffff;
    border-radius: 50%;
}

.teacher-display-name {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    display: inline-block;
}

body.dark-theme .teacher-display-name {
    color: #f8fafc;
}

.pal-tutor-badge {
    background: #eff6ff;
    color: #0284c7;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    margin-right: 6px;
}

.connection-status-text {
    font-size: 0.78rem;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 2px;
    font-weight: 600;
}

.pulse-circle {
    width: 7px;
    height: 7px;
    background: #10b981;
    border-radius: 50%;
    animation: pulseGlow 1.8s infinite;
}

@keyframes pulseGlow {
    0% { transform: scale(0.9); opacity: 0.7; }
    50% { transform: scale(1.3); opacity: 1; box-shadow: 0 0 8px #10b981; }
    100% { transform: scale(0.9); opacity: 0.7; }
}

.header-action-tools {
    display: flex;
    gap: 8px;
}

.tool-btn {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.tool-btn:hover {
    color: #0284c7;
    background: #f0f9ff;
}

/* 2. شريط الكبسولات والإيموجي */
.quick-prompts-bar {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 8px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    overflow: hidden;
    flex-shrink: 0;
}

body.dark-theme .quick-prompts-bar {
    background: #0f172a;
    border-bottom-color: #1e293b;
}

.prompts-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    white-space: nowrap;
}

.prompts-scroll-lane {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 2px;
}

.prompts-scroll-lane::-webkit-scrollbar { display: none; }

.prompt-pill {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 0.78rem;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: 0.2s;
}

.prompt-pill:hover {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    transform: translateY(-1px);
}

.quick-emoji-lane {
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid rgba(0,0,0,0.04);
    padding: 4px 16px;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-shrink: 0;
}

body.dark-theme .quick-emoji-lane {
    background: rgba(15, 23, 42, 0.7);
    border-bottom-color: #1e293b;
}

.emoji-tap {
    background: none;
    border: none;
    font-size: 1.15rem;
    cursor: pointer;
    transition: transform 0.15s ease;
    padding: 2px;
}

.emoji-tap:hover {
    transform: scale(1.35);
}

/* 3. منطقة الرسائل الشبيهة بواتساب وتيليجرام */
.chat-viewport {
    flex: 1;
    overflow-y: auto;
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    position: relative;
    background-image: radial-gradient(rgba(0,0,0,0.03) 1px, transparent 0);
    background-size: 20px 20px;
}

.date-badge-separator {
    text-align: center;
    margin: 6px auto 12px;
}

.date-badge-separator span {
    background: rgba(0,0,0,0.06);
    backdrop-filter: blur(4px);
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
}

body.dark-theme .date-badge-separator span {
    background: rgba(255,255,255,0.08);
    color: #94a3b8;
}

.messages-stack {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* نمط الفقاعات (Bubbles) */
.bubble-row {
    display: flex;
    margin-bottom: 2px;
    animation: fadeInSlide 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeInSlide {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.bubble-row.me {
    justify-content: flex-end;
}

.bubble-row.them {
    justify-content: flex-start;
}

.bubble-box {
    max-width: 78%;
    padding: 10px 14px;
    border-radius: 18px;
    position: relative;
    font-size: 0.92rem;
    line-height: 1.55;
    word-break: break-word;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}

/* فقاعة الطالب (أنا - مرسل) */
.bubble-row.me .bubble-box {
    background: var(--bubble-me-bg);
    color: var(--bubble-me-color);
    border-bottom-left-radius: 4px;
}

/* فقاعة المعلم (مستلم) */
.bubble-row.them .bubble-box {
    background: var(--bubble-them-bg);
    color: var(--bubble-them-color);
    border: 1px solid rgba(0,0,0,0.06);
    border-bottom-right-radius: 4px;
}

.bubble-sender-label {
    font-size: 0.72rem;
    font-weight: 800;
    color: #0284c7;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.bubble-meta-info {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 5px;
    margin-top: 4px;
    font-size: 0.68rem;
    opacity: 0.85;
}

.bubble-row.me .bubble-meta-info {
    color: rgba(255,255,255,0.9);
}

.bubble-row.them .bubble-meta-info {
    color: #94a3b8;
}

.read-ticks-icon {
    font-size: 0.75rem;
    color: #7dd3fc;
}

/* مؤشر الكتابة */
.typing-bubble-container {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    padding: 8px 16px;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    width: fit-content;
    margin-top: 6px;
}

body.dark-theme .typing-bubble-container {
    background: #1e293b;
}

.typing-dots span {
    display: inline-block;
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
    margin: 0 1px;
    animation: typingBounce 1.4s infinite ease-in-out both;
}

.typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.typing-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes typingBounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1.0); }
}

.typing-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
}

/* زر العودة للأسفل FAB */
.scroll-bottom-fab {
    position: absolute;
    bottom: 90px;
    left: 24px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    z-index: 20;
}

.scroll-bottom-fab:hover {
    transform: translateY(-2px);
    background: #f0f9ff;
}

.unread-fab-count {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: white;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 10px;
}

/* 4. صندوق الإدخال (Composer) */
.chat-composer-bar {
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 12px 20px;
    flex-shrink: 0;
    z-index: 10;
}

body.dark-theme .chat-composer-bar {
    background: #0f172a;
    border-top-color: #1e293b;
}

.composer-container {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 18px;
    padding: 6px 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

body.dark-theme .composer-container {
    background: #1e293b;
    border-color: #334155;
}

.composer-container:focus-within {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
}

.input-tag-icon {
    color: #94a3b8;
    font-size: 1.1rem;
}

.composer-textarea {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 0.95rem;
    font-family: inherit;
    resize: none;
    max-height: 120px;
    line-height: 1.5;
    color: #0f172a;
}

body.dark-theme .composer-textarea {
    color: #f8fafc;
}

.composer-send-btn {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    border: none;
    color: white;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    transition: all 0.2s;
}

.composer-send-btn:hover {
    transform: scale(1.05);
}

.composer-send-btn:active {
    transform: scale(0.95);
}

.composer-hint-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.72rem;
    color: #94a3b8;
    margin-top: 6px;
    padding: 0 6px;
}

/* استعلامات الجوال المتجاوبة */
@media (max-width: 640px) {
    .chat-page-wrapper {
        margin: 0;
        padding: 0;
    }
    .chat-app-container {
        height: calc(100vh - 70px);
        border-radius: 0;
        border: none;
    }
    .bubble-box {
        max-width: 88%;
    }
    .prompt-pill {
        font-size: 0.72rem;
    }
    .composer-hint-row {
        display: none;
    }
}
</style>

<script>
    const teacherId = {{ $teacher->id }};
    const feed = document.getElementById('chat_feed');
    const messagesList = document.getElementById('messages_list');
    const msgInput = document.getElementById('message_input');
    const sendBtn = document.getElementById('sendBtn');
    const scrollFab = document.getElementById('scrollToBottomFab');
    const loadingState = document.getElementById('loading_messages_state');
    const charCounter = document.getElementById('char_counter');

    let lastMessagesJson = "";
    let isSoundEnabled = true;
    let audioCtx = null;
    let isPolling = false;

    // تشغيل نغمة تنبيه لطيفة باستخدام Web Audio API دون الحاجة لملفات خارجية
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

    // جلب الرسائل
    function fetchMessages(isInitial = false) {
        if (isPolling) return;
        isPolling = true;

        axios.get(`/student/teachers/${teacherId}/messages`)
            .then(res => {
                const messages = res.data.messages || [];
                const currentJson = JSON.stringify(messages);

                if (loadingState) {
                    loadingState.style.display = 'none';
                }

                if (currentJson === lastMessagesJson) {
                    isPolling = false;
                    return;
                }

                const hadMessagesBefore = lastMessagesJson !== "" && lastMessagesJson !== "[]";
                lastMessagesJson = currentJson;

                if (messages.length === 0) {
                    messagesList.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="fa-regular fa-comments fa-3x mb-3" style="opacity: 0.3;"></i>
                            <h5 style="font-weight: 700; color: #64748b;">لا توجد رسائل سابقة مع الأستاذ</h5>
                            <p style="font-size: 0.85rem;">اختر كبسولة من الأسئلة السريعة بالأعلى أو اكتب سؤالك لبدء المحادثة.</p>
                        </div>`;
                    isPolling = false;
                    return;
                }

                let html = '';
                messages.forEach(m => {
                    const isMe = (m.sender_type === 'student');
                    html += `
                        <div class="bubble-row ${isMe ? 'me' : 'them'}">
                            <div class="bubble-box">
                                ${!isMe ? `<div class="bubble-sender-label"><i class="fa-solid fa-chalkboard-user"></i> {{ $teacher->name }}</div>` : ''}
                                <div class="bubble-text-content">${escapeHtml(m.message)}</div>
                                <div class="bubble-meta-info">
                                    <span>${m.created_at_formatted || 'الآن'}</span>
                                    ${isMe ? `<i class="fa-solid fa-check-double read-ticks-icon" title="تم التسليم"></i>` : ''}
                                </div>
                            </div>
                        </div>`;
                });

                const isNearBottom = feed.scrollHeight - feed.scrollTop <= feed.clientHeight + 160;
                messagesList.innerHTML = html;

                if (isInitial || isNearBottom) {
                    scrollToBottomSmooth();
                } else {
                    // إظهار زر التمرير مع تنبيه بوجود رسائل جديدة
                    scrollFab.style.display = 'flex';
                }

                // إصدار صوت تنبيه عند وصول رسالة جديدة أثناء المحادثة
                if (hadMessagesBefore) {
                    playNotificationTone();
                }
            })
            .catch(err => {
                console.error("خطأ في مزامنة الرسائل", err);
            })
            .finally(() => {
                isPolling = false;
            });
    }

    // إرسال رسالة
    function sendChatMessage() {
        const text = msgInput.value.trim();
        if (!text) return;

        sendBtn.disabled = true;
        sendBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i>`;

        axios.post('/student/teachers/send', {
            teacher_id: teacherId,
            message: text,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            msgInput.value = '';
            msgInput.style.height = 'auto';
            updateCharCounter();
            fetchMessages();
            setTimeout(scrollToBottomSmooth, 100);
        })
        .catch(err => {
            alert('حدث خطأ أثناء إرسال الرسالة، يرجى المحاولة ثانية.');
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = `<i class="fa-solid fa-paper-plane"></i>`;
            msgInput.focus();
        });
    }

    function handleTextareaKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendChatMessage();
        }
    }

    function insertPrompt(text) {
        msgInput.value = text;
        updateCharCounter();
        msgInput.focus();
        // تأثير وميض خفيف للحقل
        msgInput.parentElement.style.borderColor = '#10b981';
        setTimeout(() => {
            msgInput.parentElement.style.borderColor = '';
        }, 600);
    }

    function appendEmoji(emoji) {
        msgInput.value += emoji;
        updateCharCounter();
        msgInput.focus();
    }

    function updateCharCounter() {
        const len = msgInput.value.length;
        charCounter.textContent = `${len} / 1000`;
    }

    msgInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        updateCharCounter();
    });

    function scrollToBottomSmooth() {
        feed.scrollTo({
            top: feed.scrollHeight,
            behavior: 'smooth'
        });
        scrollFab.style.display = 'none';
    }

    feed.addEventListener('scroll', function() {
        const isNearBottom = feed.scrollHeight - feed.scrollTop <= feed.clientHeight + 160;
        if (isNearBottom) {
            scrollFab.style.display = 'none';
        }
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/\n/g, '<br>');
    }

    // التهيئة والمؤقت الزمني
    document.addEventListener('DOMContentLoaded', () => {
        fetchMessages(true);
        // مزامنة حية كل 3 ثوانٍ
        setInterval(() => fetchMessages(false), 3000);
    });
</script>
@endsection
