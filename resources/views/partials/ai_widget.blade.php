<!-- ========================================================================= -->
<!-- المعلم الأكاديمي الذكي العائم (Classic Light Luxury AI Tutor Floating Widget) -->
<!-- متاح حصرياً للمستخدمين والطلبة المسجلين | يدعم الرموز بالعربية والإنجليزية وجميع الأسئلة -->
<!-- ========================================================================= -->

@php
    $currentActorStudent = \App\Support\CurrentActor::student() ?? auth('student')->user();
    $currentActorStaff = \App\Support\CurrentActor::staff() ?? auth()->user();

    // منع ظهور الزر العائم في الواجهات الإدارية، ولوحات المعلمين والمصورين،
    // وصفحة المعلم الذكي الرئيسية، وشاشات تأدية الاختبارات للطلبة، منعاً لأي تداخل مع الحقول والأزرار
    $isExcludedRoute = request()->is('admin/*')
        || request()->is('teacher/*')
        || request()->is('videographer/*')
        || request()->is('ai-hub*')
        || request()->is('*exam*take*')
        || request()->is('*exam*start*')
        || request()->is('*exam*attempt*')
        || request()->routeIs('student.exams.take')
        || request()->routeIs('student.exams.start');

    $activeActor = null;
    if (!$isExcludedRoute) {
        $activeActor = $currentActorStudent ? [
            'name' => (app()->getLocale() === 'en' && !empty($currentActorStudent->name_en)) ? $currentActorStudent->name_en : ($currentActorStudent->name ?? 'طالب توجيهي'),
            'role' => 'student',
            'id'   => $currentActorStudent->id,
        ] : ($currentActorStaff ? [
            'name' => (app()->getLocale() === 'en' && !empty($currentActorStaff->name_en)) ? $currentActorStaff->name_en : ($currentActorStaff->name ?? 'مستخدم المنصة'),
            'role' => $currentActorStaff->role ?? 'user',
            'id'   => $currentActorStaff->id,
        ] : [
            'name' => __('طالب / زائر المنصة'),
            'role' => 'guest',
            'id'   => 0,
        ]);
    }
@endphp

@if($activeActor)
<div id="aiFloatingWidgetContainer" class="ai-widget-wrapper" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

    <!-- زر الإطلاق العائم الكلاسيكي الفاخر (Launcher Button) -->
    <div id="aiLauncherGroup" class="ai-launcher-group">
        <button type="button" id="aiWidgetLauncher" class="ai-launcher-btn" aria-label="{{ __('المعلم الذكي AI') }}" title="{{ __('المعلم الذكي - اسأل عن أي شيء') }}">
            <span class="ai-launcher-pulse"></span>
            <div class="ai-launcher-icon-wrap">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <span class="ai-launcher-label">{{ __('Step by Step Ai') }}</span>
            <span class="ai-launcher-chip">AI</span>
        </button>
        <button type="button" id="aiWidgetDismissBtn" class="ai-launcher-dismiss-btn" title="{{ __('إخفاء مؤقت للزر العائم') }}" aria-label="{{ __('إخفاء مؤقت') }}">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- نافذة المحادثة الذكية الفاخرة ذات التصميم الفاتح (Chat Modal/Window) -->
    <div id="aiWidgetPanel" class="ai-widget-panel" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="aiWidgetTitle">
        
        <!-- ترويسة النافذة الكلاسيكية الفاخرة -->
        <div class="ai-panel-header">
            <div class="ai-header-brand">
                <div class="ai-avatar-badge">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <div class="ai-header-text">
                    <div class="ai-header-title-row">
                        <h3 id="aiWidgetTitle" class="ai-header-title">Step by Step Ai</h3>
                        <span class="ai-badge-pro">Smart AI</span>
                    </div>
                    <p class="ai-header-subtitle">{{ __('المساعد الأكاديمي الذكي لمنصة Step by Step') }}</p>
                </div>
            </div>

            <div class="ai-header-controls">
                <!-- زر تبديل الرموز (عربي / إنجليزي) -->
                <button type="button" id="aiSymbolToggleBtn" class="ai-symbol-btn" title="{{ __('تبديل لغة الرموز الرياضية والعلمية') }}">
                    <i class="fa-solid fa-square-root-variable"></i>
                    <span id="aiSymbolLabel">الرموز: عربي (ق=ك×ت)</span>
                </button>

                <!-- رابط المركز الموسع -->
                <a href="{{ route('ai.hub') }}" class="ai-icon-btn" title="{{ __('الانتقال للمركز الأكاديمي الشامل') }}">
                    <i class="fa-solid fa-up-right-from-square"></i>
                </a>

                <!-- مسح المحادثة -->
                <button type="button" id="aiWidgetClearBtn" class="ai-icon-btn" title="{{ __('بدء محادثة جديدة') }}">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>

                <!-- إغلاق -->
                <button type="button" id="aiWidgetCloseBtn" class="ai-icon-btn ai-close-btn" title="{{ __('إغلاق') }}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- شريط الرقاقات والتوجيهات السريعة (Quick Action Pills) -->
        <div class="ai-quick-pills-bar">
            <button type="button" class="ai-quick-pill" data-prompt="اشرح لي قانون نيوتن الثاني وحل مسألة عليه بالرموز">
                <i class="fa-solid fa-atom"></i> نيوتن الثاني
            </button>
            <button type="button" class="ai-quick-pill" data-prompt="قواعد الاشتقاق والتكامل الأساسية مع أمثلة">
                <i class="fa-solid fa-infinity"></i> التفاضل والتكامل
            </button>
            <button type="button" class="ai-quick-pill" data-prompt="كيف أحسب معدلي التوجيهي بطريقة صحيحة؟">
                <i class="fa-solid fa-calculator"></i> حساب المعدل
            </button>
            <button type="button" class="ai-quick-pill" data-prompt="كيف أنظم جدول مراجعة مكثف للأسبوع القادم؟">
                <i class="fa-solid fa-calendar-check"></i> جدول مراجعة
            </button>
            <button type="button" class="ai-quick-pill" data-prompt="قواعد الإعراب لأدوات الشرط وأمثلة عليها">
                <i class="fa-solid fa-feather-pointed"></i> إعراب وقواعد
            </button>
        </div>

        <!-- منطقة الرسائل والمحادثة -->
        <div id="aiWidgetMessages" class="ai-messages-area">
            
            <!-- رسالة الترحيب الافتراضية -->
            <div class="ai-message ai-message-system">
                <div class="ai-msg-avatar">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="ai-msg-content">
                    <div class="ai-msg-author">
                        <span>Step by Step Ai</span>
                        <span class="ai-msg-time">{{ __('الآن') }}</span>
                    </div>
                    <div class="ai-msg-body">
                        أهلاً بك يا <strong>{{ $activeActor['name'] }}</strong> في منارة التوجيهي! 🎓<br>
                        أنا رفيقك ومعلمك الذكي، جاهز للإجابة عن <strong>أي استفسار</strong> علمي، رياضي، أدبي، أو عام بالخطوات والرموز المفضلة لديك.
                        <div class="ai-welcome-hint">
                            <i class="fa-solid fa-lightbulb"></i> يمكنك كتابة سؤالك، استخدام المايكروفون الصوتي، أو إرفاق صورة مسألة لحلها خطوة بخطوة.
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- معاينة الصورة المرفقة إن وجدت -->
        <div id="aiImagePreviewBar" class="ai-image-preview-bar" style="display: none;">
            <div class="ai-preview-thumb">
                <img id="aiAttachedImgPreview" src="" alt="{{ __('معاينة الصورة') }}">
            </div>
            <div class="ai-preview-info">
                <span class="ai-preview-title">{{ __('مسألة أو معادلة مرفقة للتحليل') }}</span>
                <span class="ai-preview-hint">{{ __('سيتم فك الرموز وحل المسألة فوراً') }}</span>
            </div>
            <button type="button" id="aiRemoveImgBtn" class="ai-remove-img-btn" title="{{ __('إلغاء إرفاق الصورة') }}">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- مؤشر جاري التفكير / الكتابة -->
        <div id="aiTypingIndicator" class="ai-typing-bar" style="display: none;">
            <div class="ai-typing-avatar">
                <i class="fa-solid fa-brain fa-fade"></i>
            </div>
            <div class="ai-typing-text">
                <span>المعلم الذكي يقوم بالتحليل والصياغة...</span>
                <span class="ai-typing-dots">
                    <span></span><span></span><span></span>
                </span>
            </div>
        </div>

        <!-- شريط الإدخال الفاخر -->
        <div class="ai-input-wrapper">
            <form id="aiWidgetChatForm" class="ai-input-form" onsubmit="return false;">
                
                <input type="file" id="aiImageFileInput" accept="image/*" style="display: none;">
                
                <!-- زر رفع صورة / كاميرا -->
                <button type="button" id="aiAttachImageBtn" class="ai-action-btn" title="{{ __('إرفاق صورة مسألة من الكتاب أو الدفتر') }}">
                    <i class="fa-solid fa-camera"></i>
                </button>

                <!-- زر الإدخال الصوتي (Speech to Text) -->
                <button type="button" id="aiVoiceInputBtn" class="ai-action-btn" title="{{ __('التحدث بالصوت (إملاء صوتي)') }}">
                    <i class="fa-solid fa-microphone"></i>
                </button>

                <!-- حقل الإدخال النصي المرن -->
                <textarea id="aiPromptInput" class="ai-textarea" rows="1" placeholder="{{ __('اسأل عن أي شيء، مسألة، قانون، أو موضوع...') }}" maxlength="5000"></textarea>

                <!-- زر الإرسال -->
                <button type="submit" id="aiSubmitBtn" class="ai-send-btn" title="{{ __('إرسال (Enter)') }}">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
            <div class="ai-footer-note">
                <span>منصة منارة التوجيهي &bull; نموذج ذكي معتمد &bull; مفتاح Shift+Enter لسطر جديد</span>
            </div>
        </div>

    </div>

</div>

<!-- تضمين مكتبات KaTeX للرموز الرياضية والعلمية و marked.js لتنسيق النصوص -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
/* =========================================================================
   التصميم الكلاسيكي الفاخر للمساعد الذكي العائم (Classic Light Luxury AI Widget)
   ========================================================================= */
.ai-widget-wrapper {
    position: fixed;
    bottom: 24px;
    {{ app()->getLocale() === 'ar' ? 'left: 78px;' : 'right: 78px;' }}
    z-index: 99998;
    font-family: inherit;
    --ai-primary: #1d4ed8;
    --ai-primary-hover: #1e40af;
    --ai-primary-soft: #eff6ff;
    --ai-gold: #d97706;
    --ai-gold-soft: #fffbeb;
    --ai-surface: #ffffff;
    --ai-bg: #f8fafc;
    --ai-border: #e2e8f0;
    --ai-border-hover: #cbd5e1;
    --ai-text-main: #0f172a;
    --ai-text-muted: #64748b;
    --ai-shadow: 0 20px 45px -12px rgba(15, 23, 42, 0.18), 0 8px 18px -6px rgba(15, 23, 42, 0.08);
}

html[dir="ltr"] .ai-widget-wrapper {
    left: auto !important;
    right: 24px !important;
}

@media (max-width: 1024px) {
    .ai-widget-wrapper {
        bottom: calc(122px + env(safe-area-inset-bottom, 0px));
        left: 14px;
        right: auto;
    }
    html[dir="ltr"] .ai-widget-wrapper {
        left: auto !important;
        right: 14px !important;
    }
}

@media (max-width: 768px) {
    .ai-widget-wrapper {
        bottom: calc(124px + env(safe-area-inset-bottom, 0px));
        left: 14px;
        right: auto;
    }
    html[dir="ltr"] .ai-widget-wrapper {
        left: auto !important;
        right: 14px !important;
    }
}

@media print {
    .ai-widget-wrapper,
    #aiFloatingWidgetContainer {
        display: none !important;
    }
}

/* مجموعة زر الإطلاق وزر الإخفاء السريع */
.ai-launcher-group {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    position: relative;
}
.ai-launcher-dismiss-btn {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid var(--ai-border);
    color: var(--ai-text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    opacity: 0.65;
    transform: scale(0.9);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.ai-launcher-dismiss-btn:hover,
.ai-launcher-group:hover .ai-launcher-dismiss-btn {
    opacity: 1;
    transform: scale(1);
}
.ai-launcher-dismiss-btn:hover {
    color: #ef4444;
    border-color: #fca5a5;
    background: #fef2f2;
}

/* زر الإطلاق */
.ai-launcher-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px 10px 14px;
    background: #ffffff;
    border: 1.5px solid var(--ai-border);
    border-radius: 999px;
    color: var(--ai-text-main);
    cursor: pointer;
    box-shadow: 0 10px 25px -5px rgba(29, 78, 216, 0.18), 0 4px 10px -2px rgba(15, 23, 42, 0.06);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.ai-launcher-btn:hover {
    transform: translateY(-2px);
    border-color: #93c5fd;
    box-shadow: 0 16px 32px -6px rgba(29, 78, 216, 0.24);
}
.ai-launcher-pulse {
    position: absolute;
    inset: -3px;
    border-radius: 999px;
    border: 2px solid #3b82f6;
    opacity: 0.35;
    animation: aiPulseRing 2.4s infinite cubic-bezier(0.25, 0, 0, 1);
    pointer-events: none;
}
@keyframes aiPulseRing {
    0% { transform: scale(0.96); opacity: 0.6; }
    70% { transform: scale(1.08); opacity: 0; }
    100% { transform: scale(1.08); opacity: 0; }
}
.ai-launcher-icon-wrap {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 1.05rem;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
}
.ai-launcher-label {
    font-weight: 700;
    font-size: 0.92rem;
    color: #0f172a;
    letter-spacing: -0.2px;
}
.ai-launcher-chip {
    background: var(--ai-gold-soft);
    color: var(--ai-gold);
    border: 1px solid #fde68a;
    font-size: 0.68rem;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 6px;
}

/* نافذة المحادثة */
.ai-widget-panel {
    position: absolute;
    bottom: 58px;
    {{ app()->getLocale() === 'ar' ? 'left: 0;' : 'right: 0;' }}
    width: 440px;
    max-width: calc(100vw - 32px);
    height: 640px;
    max-height: calc(100vh - 150px);
    background: #ffffff;
    border: 1px solid var(--ai-border);
    border-radius: 20px;
    box-shadow: var(--ai-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: aiPanelSlideUp 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}

html[dir="ltr"] .ai-widget-panel {
    left: auto !important;
    right: 0 !important;
}
@keyframes aiPanelSlideUp {
    from { opacity: 0; transform: translateY(14px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* الترويسة */
.ai-panel-header {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 1px solid var(--ai-border);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.ai-header-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.ai-avatar-badge {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e40af, #2563eb);
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 1.05rem;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(30, 64, 175, 0.25);
}
.ai-header-title-row {
    display: flex;
    align-items: center;
    gap: 6px;
}
.ai-header-title {
    margin: 0;
    font-size: 0.98rem;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
}
.ai-badge-pro {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 999px;
}
.ai-header-subtitle {
    margin: 2px 0 0;
    font-size: 0.72rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ai-header-controls {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.ai-symbol-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #1e3a8a;
    padding: 5px 9px;
    border-radius: 8px;
    font-size: 0.73rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
}
.ai-symbol-btn:hover {
    background: #eff6ff;
    border-color: #93c5fd;
}
.ai-icon-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid var(--ai-border);
    color: #475569;
    display: grid;
    place-items: center;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.15s;
}
.ai-icon-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.ai-close-btn:hover {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fca5a5;
}

/* شريط الرقاقات السريعة */
.ai-quick-pills-bar {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: #fafaf9;
    border-bottom: 1px solid #f1f5f9;
    overflow-x: auto;
    scrollbar-width: none;
}
.ai-quick-pills-bar::-webkit-scrollbar {
    display: none;
}
.ai-quick-pill {
    white-space: nowrap;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 0.73rem;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
}
.ai-quick-pill i {
    color: var(--ai-primary);
    font-size: 0.75rem;
}
.ai-quick-pill:hover {
    background: var(--ai-primary-soft);
    color: var(--ai-primary);
    border-color: #bfdbfe;
    transform: translateY(-1px);
}

/* منطقة الرسائل */
.ai-messages-area {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 14px;
    scroll-behavior: smooth;
}
.ai-message {
    display: flex;
    gap: 10px;
    max-width: 90%;
    animation: aiMsgFadeIn 0.2s ease-out;
}
@keyframes aiMsgFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.ai-message-user {
    align-self: flex-end;
    flex-direction: row-reverse;
}
.ai-message-system,
.ai-message-assistant {
    align-self: flex-start;
}
.ai-msg-avatar {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    font-size: 0.85rem;
}
.ai-message-system .ai-msg-avatar,
.ai-message-assistant .ai-msg-avatar {
    background: linear-gradient(135deg, #1e40af, #2563eb);
    color: #ffffff;
    box-shadow: 0 3px 8px rgba(30, 64, 175, 0.2);
}
.ai-message-user .ai-msg-avatar {
    background: #e2e8f0;
    color: #1e293b;
}
.ai-msg-content {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}
.ai-msg-author {
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 0 4px;
}
.ai-message-user .ai-msg-author {
    justify-content: flex-end;
}
.ai-msg-time {
    font-size: 0.65rem;
    color: #94a3b8;
    font-weight: 500;
}
.ai-msg-body {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 14px;
    color: #1e293b;
    font-size: 0.87rem;
    line-height: 1.65;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
    word-break: break-word;
}
.ai-message-user .ai-msg-body {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #ffffff;
    border: none;
    box-shadow: 0 4px 12px rgba(29, 78, 216, 0.2);
}
.ai-msg-body p:last-child {
    margin-bottom: 0;
}
.ai-msg-body h1, .ai-msg-body h2, .ai-msg-body h3, .ai-msg-body h4 {
    margin-top: 10px;
    margin-bottom: 6px;
    font-size: 0.98rem;
    font-weight: 800;
    color: #0f172a;
}
.ai-msg-body ul, .ai-msg-body ol {
    margin: 6px 0;
    padding-inline-start: 20px;
}
.ai-msg-body code {
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 5px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.85rem;
    color: #0369a1;
    direction: ltr !important;
    unicode-bidi: isolate !important;
    display: inline-block;
    vertical-align: baseline;
}
.ai-message-user .ai-msg-body code {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    direction: ltr !important;
    unicode-bidi: isolate !important;
    display: inline-block;
}
.ai-msg-body pre {
    background: #0f172a;
    color: #f8fafc;
    padding: 12px 14px;
    border-radius: 12px;
    overflow-x: auto;
    font-size: 0.84rem;
    margin: 10px 0;
    direction: ltr !important;
    unicode-bidi: isolate !important;
    text-align: left !important;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}
.ai-msg-body .katex, .ai-msg-body .katex-display, .ai-msg-body .katex *, .ai-msg-body .katex-display * {
    direction: ltr !important;
    unicode-bidi: isolate !important;
}
.ai-msg-body .katex-display {
    display: block !important;
    margin: 16px auto !important;
    padding: 14px 18px !important;
    background: #f8fafc !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 12px !important;
    overflow-x: auto !important;
    text-align: center !important;
    direction: ltr !important;
    unicode-bidi: isolate !important;
    max-width: 100% !important;
    clear: both !important;
}
.ai-msg-body .katex-display > .katex {
    display: inline-block !important;
    font-size: 1.25rem !important;
    color: #0f172a !important;
    direction: ltr !important;
    unicode-bidi: isolate !important;
    text-align: center !important;
}
.ai-msg-body .katex {
    font-size: 1.12em !important;
    display: inline-block !important;
    padding: 0 3px !important;
    vertical-align: -0.12em !important;
    text-align: left !important;
    direction: ltr !important;
    unicode-bidi: isolate !important;
}
.ai-msg-body .katex .mord.text,
.ai-msg-body .katex .text,
.ai-msg-body .katex .text * {
    direction: rtl !important;
    unicode-bidi: isolate !important;
    font-family: 'Noto Sans Arabic', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}
.ai-msg-body .katex .base,
.ai-msg-body .katex .strut,
.ai-msg-body .katex .mord,
.ai-msg-body .katex .mbin,
.ai-msg-body .katex .mrel,
.ai-msg-body .katex .mopen,
.ai-msg-body .katex .mclose,
.ai-msg-body .katex .mpunct,
.ai-msg-body .katex .minner,
.ai-msg-body .katex .mfrac,
.ai-msg-body .katex .msupsub,
.ai-msg-body .katex .sqrt {
    direction: ltr !important;
    unicode-bidi: isolate !important;
}
.ai-msg-body pre code {
    background: transparent;
    color: inherit;
    padding: 0;
}
.ai-msg-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    padding: 0 4px;
}
.ai-msg-action-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 0.74rem;
    cursor: pointer;
    padding: 3px 7px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s;
}
.ai-msg-action-btn:hover {
    background: #e2e8f0;
    color: #334155;
}
.ai-welcome-hint {
    margin-top: 10px;
    padding: 8px 10px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 8px;
    font-size: 0.77rem;
    color: #92400e;
    line-height: 1.5;
}

/* شريط الصورة المرفقة */
.ai-image-preview-bar {
    padding: 8px 14px;
    background: #fffbeb;
    border-top: 1px solid #fde68a;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ai-preview-thumb img {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #f59e0b;
}
.ai-preview-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}
.ai-preview-title {
    font-size: 0.78rem;
    font-weight: 700;
    color: #78350f;
}
.ai-preview-hint {
    font-size: 0.68rem;
    color: #b45309;
}
.ai-remove-img-btn {
    background: transparent;
    border: none;
    color: #b45309;
    cursor: pointer;
    font-size: 0.95rem;
    padding: 4px;
}
.ai-remove-img-btn:hover {
    color: #78350f;
}

/* مؤشر التفكير */
.ai-typing-bar {
    padding: 8px 14px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ai-typing-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #eff6ff;
    color: var(--ai-primary);
    display: grid;
    place-items: center;
    font-size: 0.78rem;
}
.ai-typing-text {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ai-primary);
    display: flex;
    align-items: center;
    gap: 4px;
}
.ai-typing-dots span {
    display: inline-block;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--ai-primary);
    margin: 0 1px;
    animation: aiDots 1.4s infinite;
}
.ai-typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.ai-typing-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes aiDots {
    0%, 80%, 100% { transform: scale(0); opacity: 0.3; }
    40% { transform: scale(1); opacity: 1; }
}

/* شريط الإدخال */
.ai-input-wrapper {
    background: #ffffff;
    border-top: 1px solid var(--ai-border);
    padding: 12px 14px 10px;
}
.ai-input-form {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    background: #f8fafc;
    border: 1.5px solid var(--ai-border);
    border-radius: 14px;
    padding: 6px 10px;
    transition: all 0.2s;
}
.ai-input-form:focus-within {
    border-color: #93c5fd;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.ai-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: transparent;
    border: none;
    color: #64748b;
    display: grid;
    place-items: center;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.15s;
    flex-shrink: 0;
}
.ai-action-btn:hover {
    background: #e2e8f0;
    color: #1e293b;
}
.ai-action-btn.active-listening {
    color: #dc2626;
    background: #fee2e2;
    animation: aiMicPulse 1s infinite alternate;
}
@keyframes aiMicPulse {
    from { transform: scale(1); }
    to { transform: scale(1.1); }
}
.ai-textarea {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-family: inherit;
    font-size: 0.88rem;
    color: #0f172a;
    resize: none;
    max-height: 120px;
    line-height: 1.45;
    padding: 4px 2px;
}
.ai-textarea::placeholder {
    color: #94a3b8;
}
.ai-send-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    border: none;
    color: #ffffff;
    display: grid;
    place-items: center;
    cursor: pointer;
    font-size: 0.9rem;
    flex-shrink: 0;
    box-shadow: 0 3px 8px rgba(29, 78, 216, 0.25);
    transition: all 0.15s;
}
.ai-send-btn:hover {
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
    transform: translateY(-1px);
}
.ai-footer-note {
    text-align: center;
    font-size: 0.65rem;
    color: #94a3b8;
    margin-top: 6px;
}

/* شاشات الجوال */
@media (max-width: 768px) {
    .ai-widget-panel {
        position: fixed !important;
        top: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100% !important;
        max-height: 100% !important;
        border-radius: 0 !important;
        border: none !important;
        z-index: 999999 !important;
    }
}
</style>

<script>
(function() {
    // إدارة حالة المساعد الذكي
    let currentConversationId = null;
    let currentSymbolMode = 'ar'; // 'ar' أو 'en'
    let currentAttachedImageBase64 = null;
    let isWaitingResponse = false;
    let speechRecognition = null;

    function resolveApiUrl(path) {
        const cleanPath = path.startsWith('/') ? path : '/' + path;
        const subfolderMatch = window.location.pathname.match(/^(\/[^\/]+\/public|\/public)?/);
        const prefix = (subfolderMatch && subfolderMatch[0]) ? subfolderMatch[0] : '';
        return prefix + cleanPath;
    }

    // عناصر الواجهة
    const container = document.getElementById('aiFloatingWidgetContainer');
    if (!container) return;

    const launcher = document.getElementById('aiWidgetLauncher');
    const panel = document.getElementById('aiWidgetPanel');
    const closeBtn = document.getElementById('aiWidgetCloseBtn');
    const clearBtn = document.getElementById('aiWidgetClearBtn');
    const symbolToggleBtn = document.getElementById('aiSymbolToggleBtn');
    const symbolLabel = document.getElementById('aiSymbolLabel');
    const messagesArea = document.getElementById('aiWidgetMessages');
    const chatForm = document.getElementById('aiWidgetChatForm');
    const promptInput = document.getElementById('aiPromptInput');
    const submitBtn = document.getElementById('aiSubmitBtn');
    const attachImgBtn = document.getElementById('aiAttachImageBtn');
    const imgFileInput = document.getElementById('aiImageFileInput');
    const imgPreviewBar = document.getElementById('aiImagePreviewBar');
    const attachedImgPreview = document.getElementById('aiAttachedImgPreview');
    const removeImgBtn = document.getElementById('aiRemoveImgBtn');
    const typingIndicator = document.getElementById('aiTypingIndicator');
    const voiceInputBtn = document.getElementById('aiVoiceInputBtn');

    const launcherGroup = document.getElementById('aiLauncherGroup');
    const dismissBtn = document.getElementById('aiWidgetDismissBtn');

    // 1. فتح وإغلاق النافذة
    launcher.addEventListener('click', () => {
        panel.style.display = 'flex';
        if (launcherGroup) launcherGroup.style.display = 'none';
        else launcher.style.display = 'none';
        promptInput.focus();
        scrollToBottom();
    });

    closeBtn.addEventListener('click', () => {
        panel.style.display = 'none';
        if (launcherGroup) launcherGroup.style.display = 'inline-flex';
        else launcher.style.display = 'inline-flex';
    });

    if (dismissBtn) {
        dismissBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            container.style.display = 'none';
            try { sessionStorage.setItem('ai_floating_widget_hidden', '1'); } catch(err){}
        });
        try {
            if (sessionStorage.getItem('ai_floating_widget_hidden') === '1') {
                container.style.display = 'none';
            }
        } catch(err){}
    }

    // 2. تبديل وضع الرموز (عربي / إنجليزي)
    symbolToggleBtn.addEventListener('click', () => {
        if (currentSymbolMode === 'ar') {
            currentSymbolMode = 'en';
            symbolLabel.textContent = 'Symbols: English (F=ma)';
            symbolToggleBtn.style.color = '#1e40af';
            symbolToggleBtn.style.background = '#eff6ff';
        } else {
            currentSymbolMode = 'ar';
            symbolLabel.textContent = 'الرموز: عربي (ق=ك×ت)';
            symbolToggleBtn.style.color = '#1e3a8a';
            symbolToggleBtn.style.background = '#ffffff';
        }
    });

    // 3. النقر على الرقاقات السريعة
    document.querySelectorAll('.ai-quick-pill').forEach(pill => {
        pill.addEventListener('click', () => {
            const promptText = pill.getAttribute('data-prompt');
            if (promptText) {
                promptInput.value = promptText;
                handleSendMessage();
            }
        });
    });

    // 4. رفع صورة
    attachImgBtn.addEventListener('click', () => imgFileInput.click());

    imgFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 8 * 1024 * 1024) {
            alert('حجم الصورة كبير جداً، يرجى اختيار صورة أصغر من 8 ميجابايت.');
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            currentAttachedImageBase64 = event.target.result;
            attachedImgPreview.src = currentAttachedImageBase64;
            imgPreviewBar.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    });

    removeImgBtn.addEventListener('click', () => {
        currentAttachedImageBase64 = null;
        imgFileInput.value = '';
        imgPreviewBar.style.display = 'none';
    });

    // 5. الإدخال الصوتي بالمايكروفون (Web Speech API)
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        speechRecognition = new SpeechRec();
        speechRecognition.lang = 'ar-SA';
        speechRecognition.continuous = false;
        speechRecognition.interimResults = false;

        speechRecognition.onstart = () => {
            voiceInputBtn.classList.add('active-listening');
            promptInput.placeholder = 'جارٍ الاستماع إليك... تحدث الآن';
        };

        speechRecognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            promptInput.value = (promptInput.value ? promptInput.value + ' ' : '') + transcript;
            autoResizeTextarea();
        };

        speechRecognition.onerror = () => {
            voiceInputBtn.classList.remove('active-listening');
            promptInput.placeholder = 'اسأل عن أي شيء، مسألة، قانون، أو موضوع...';
        };

        speechRecognition.onend = () => {
            voiceInputBtn.classList.remove('active-listening');
            promptInput.placeholder = 'اسأل عن أي شيء، مسألة، قانون، أو موضوع...';
        };

        voiceInputBtn.addEventListener('click', () => {
            try {
                if (voiceInputBtn.classList.contains('active-listening')) {
                    speechRecognition.stop();
                } else {
                    speechRecognition.start();
                }
            } catch (err) {
                console.warn('Speech error:', err);
            }
        });
    } else {
        voiceInputBtn.title = 'خاصية الإملاء الصوتي غير مدعومة في هذا المتصفح';
        voiceInputBtn.style.opacity = '0.5';
    }

    // 6. ضبط تلقائي لارتفاع حقل الإدخال
    promptInput.addEventListener('input', autoResizeTextarea);
    function autoResizeTextarea() {
        promptInput.style.height = 'auto';
        promptInput.style.height = Math.min(promptInput.scrollHeight, 120) + 'px';
    }

    promptInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSendMessage();
        }
    });

    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        handleSendMessage();
    });

    // 7. بدء محادثة جديدة
    clearBtn.addEventListener('click', () => {
        if (confirm('هل تريد بدء محادثة جديدة وتصفير الشاشة الحالية؟')) {
            currentConversationId = null;
            currentAttachedImageBase64 = null;
            imgPreviewBar.style.display = 'none';
            // الإبقاء فقط على رسالة الترحيب الأولى
            const welcomeMsg = messagesArea.firstElementChild;
            messagesArea.innerHTML = '';
            if (welcomeMsg) messagesArea.appendChild(welcomeMsg);
        }
    });

    // 8. إرسال الرسالة إلى السيرفر
    async function handleSendMessage() {
        if (isWaitingResponse) return;

        const text = promptInput.value.trim();
        const image = currentAttachedImageBase64;

        if (!text && !image) return;

        // فحص هل هذه الرسالة الأولى في محادثة الويدجت وتجميع السجل
        const existingMessages = Array.from(messagesArea.querySelectorAll('.ai-message')).map(msg => {
            const isUser = msg.classList.contains('ai-message-user');
            const body = msg.querySelector('.ai-msg-body');
            return {
                role: isUser ? 'user' : 'assistant',
                content: body ? body.innerText.slice(0, 600) : ''
            };
        }).filter(m => m.content.length > 0);

        const isFirstMessage = (existingMessages.length === 0);

        // إضافة رسالة المستخدم إلى الواجهة
        appendUserMessage(text, image);

        promptInput.value = '';
        promptInput.style.height = 'auto';
        currentAttachedImageBase64 = null;
        imgFileInput.value = '';
        imgPreviewBar.style.display = 'none';

        isWaitingResponse = true;
        typingIndicator.style.display = 'flex';
        scrollToBottom();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(resolveApiUrl('/ai/chat'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    prompt: text,
                    conversation_id: currentConversationId,
                    symbol_mode: currentSymbolMode,
                    image_base64: image,
                    is_first_message: isFirstMessage,
                    history: existingMessages.slice(-6)
                })
            });

            const data = await res.json();

            if (res.status === 401 && data.require_login) {
                appendAssistantMessage('⚠️ ' + data.message);
                return;
            }

            if (data.success) {
                currentConversationId = data.conversation_id;
                appendAssistantMessage(data.reply, data.provider);
            } else {
                appendAssistantMessage('عذراً، حدث خطأ: ' + (data.message || 'يرجى المحاولة مجدداً'));
            }
        } catch (error) {
            console.error('AI chat error:', error);
            appendAssistantMessage('⚠️ تعذر الاتصال بمحرك الذكاء الاصطناعي حالياً. يرجى التحقق من اتصال الإنترنت والمحاولة مرة أخرى.');
        } finally {
            isWaitingResponse = false;
            typingIndicator.style.display = 'none';
            scrollToBottom();
        }
    }

    // 9. إضافة رسالة المستخدم
    function appendUserMessage(text, image) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'ai-message ai-message-user';

        let innerContent = '';
        if (image) {
            innerContent += `<div style="margin-bottom: 8px;"><img src="${image}" style="max-width: 180px; max-height: 140px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.4);"></div>`;
        }
        if (text) {
            innerContent += `<div>${escapeHtml(text).replace(/\n/g, '<br>')}</div>`;
        }

        msgDiv.innerHTML = `
            <div class="ai-msg-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="ai-msg-content">
                <div class="ai-msg-author">
                    <span>أنت</span>
                    <span class="ai-msg-time">${getTimeString()}</span>
                </div>
                <div class="ai-msg-body">
                    ${innerContent}
                </div>
            </div>
        `;
        messagesArea.appendChild(msgDiv);
        scrollToBottom();
    }

    // 10. إضافة رد الذكاء الاصطناعي مع دعم Markdown و KaTeX
    // 10. إضافة رد الذكاء الاصطناعي مع دعم Markdown و KaTeX وتنظيف أي وسوم ميتا
    function appendAssistantMessage(rawText, provider) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'ai-message ai-message-assistant';

        let cleanText = (rawText || '').trim();
        // إزالة أي وسوم تفكير داخلية مسربة
        cleanText = cleanText.replace(/<think>[\s\S]*?<\/think>/gi, '');
        cleanText = cleanText.replace(/<think>[\s\S]*$/gi, '');
        // إزالة أي مسودات ميتا في بداية النص
        cleanText = cleanText.replace(/^[-*•]?\s*المستخدم يسأل[^\n.!?]*[.!?]?\s*/gi, '');
        cleanText = cleanText.replace(/^[-*•]?\s*الرد يجب أن يكون[^\n.!?]*[.!?]?\s*/gi, '');
        cleanText = cleanText.replace(/^[-*•]?\s*الرد يجب[^\n.!?]*[.!?]?\s*/gi, '');

        // استخراج أي اقتراحات ذكية إن وجدت لعرضها كأزرار أنيقة، مع إزالة الوسم الخام من النص
        let suggestions = [];
        const suggMatch = cleanText.match(/---SUGGESTIONS---\s*(\[[\s\S]*?\])/i);
        if (suggMatch) {
            try {
                suggestions = JSON.parse(suggMatch[1]);
            } catch (e) {}
        }
        cleanText = cleanText.replace(/---SUGGESTIONS---[\s\S]*$/gi, '').trim();

        // تنسيق Markdown مع حماية صيغ LaTeX للرياضيات
        let renderedHtml = cleanText;
        if (typeof marked !== 'undefined') {
            try {
                const mathTokens = [];
                let processed = cleanText.replace(/\$\$([\s\S]*?)\$\$/g, (match) => {
                    const id = `@@MATH_BLOCK_${mathTokens.length}@@`;
                    mathTokens.push({ id, original: match });
                    return id;
                });
                processed = processed.replace(/\\\[([\s\S]*?)\\\]/g, (match) => {
                    const id = `@@MATH_BLOCK_${mathTokens.length}@@`;
                    mathTokens.push({ id, original: match });
                    return id;
                });
                processed = processed.replace(/\$([^\$\n]+?)\$/g, (match) => {
                    const id = `@@MATH_INLINE_${mathTokens.length}@@`;
                    mathTokens.push({ id, original: match });
                    return id;
                });
                processed = processed.replace(/\\\(([^\(\n]+?)\\\)/g, (match) => {
                    const id = `@@MATH_INLINE_${mathTokens.length}@@`;
                    mathTokens.push({ id, original: match });
                    return id;
                });

                let parsedHtml = marked.parse(processed);
                mathTokens.forEach(item => {
                    parsedHtml = parsedHtml.replace(item.id, item.original);
                });
                renderedHtml = parsedHtml;
            } catch (e) {
                renderedHtml = escapeHtml(cleanText).replace(/\n/g, '<br>');
            }
        } else {
            renderedHtml = escapeHtml(cleanText).replace(/\n/g, '<br>');
        }

        let suggestionsHtml = '';
        if (Array.isArray(suggestions) && suggestions.length > 0) {
            suggestionsHtml = `
                <div class="ai-widget-suggestions" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px;">
                    ${suggestions.map(s => `<button type="button" class="ai-quick-pill" onclick="window.sendAiWidgetSuggestion('${escapeHtml(s).replace(/'/g, "\\'")}')" style="font-size: 0.78rem; padding: 4px 10px;"><i class="fa-regular fa-lightbulb"></i> ${escapeHtml(s)}</button>`).join('')}
                </div>
            `;
        }

        const msgId = 'aiMsg_' + Date.now();

        msgDiv.innerHTML = `
            <div class="ai-msg-avatar">
                <i class="fa-solid fa-brain"></i>
            </div>
            <div class="ai-msg-content">
                <div class="ai-msg-author">
                    <span>Step by Step Ai</span>
                    <span class="ai-msg-time">${getTimeString()}</span>
                </div>
                <div class="ai-msg-body" id="${msgId}">
                    ${renderedHtml}
                    ${suggestionsHtml}
                </div>
                <div class="ai-msg-actions">
                    <button type="button" class="ai-msg-action-btn" title="نسخ الإجابة" onclick="copyAiText('${msgId}')">
                        <i class="fa-regular fa-copy"></i> نسخ
                    </button>
                    <button type="button" class="ai-msg-action-btn" title="قراءة صوتية" onclick="speakAiText('${msgId}')">
                        <i class="fa-solid fa-volume-high"></i> استماع
                    </button>
                </div>
            </div>
        `;

        messagesArea.appendChild(msgDiv);

        // تصيير معادلات KaTeX
        renderMathInElementSafe(msgDiv.querySelector('.ai-msg-body'));

        scrollToBottom();
    }

    window.sendAiWidgetSuggestion = function(text) {
        if (!promptInput) return;
        promptInput.value = text;
        promptInput.focus();
        handleSendMessage();
    };

    // تصيير KaTeX بأمان بنمط ChatGPT
    function renderMathInElementSafe(element) {
        if (!element) return;

        function runKaTeX() {
            if (typeof renderMathInElement !== 'undefined') {
                try {
                    renderMathInElement(element, {
                        delimiters: [
                            { left: '$$', right: '$$', display: true },
                            { left: '$', right: '$', display: false },
                            { left: '\\(', right: '\\)', display: false },
                            { left: '\\[', right: '\\]', display: true }
                        ],
                        throwOnError: false
                    });
                } catch (err) {
                    console.warn('KaTeX render note:', err);
                }
            }
        }

        if (typeof renderMathInElement !== 'undefined') {
            runKaTeX();
        } else {
            let tries = 0;
            const timer = setInterval(() => {
                tries++;
                if (typeof renderMathInElement !== 'undefined') {
                    clearInterval(timer);
                    runKaTeX();
                } else if (tries > 30) {
                    clearInterval(timer);
                }
            }, 100);
        }
    }

    function scrollToBottom() {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function getTimeString() {
        const d = new Date();
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // دوال عامة للنقر من الأزرار (نسخ واستماع)
    window.copyAiText = function(elementId) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const text = el.innerText || el.textContent;
        navigator.clipboard.writeText(text).then(() => {
            alert('تم نسخ الإجابة بنجاح ✅');
        }).catch(() => {
            alert('يرجى تحديد النص ونسخه يدوياً.');
        });
    };

    window.speakAiText = function(elementId) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const text = el.innerText || el.textContent;
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'ar-SA';
            utterance.rate = 0.95;
            window.speechSynthesis.speak(utterance);
        } else {
            alert('المتصفح لا يدعم ميزة النطق الصوتي.');
        }
    };

})();
</script>
@endif
