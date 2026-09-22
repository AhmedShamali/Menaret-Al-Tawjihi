@extends('layouts.app')

@section('title', __('بناء اختبار جديد') . ' - ' . config('app.name', 'منارة التوجيهي'))

@section('content')
<div class="exam-edit-wrapper">

    {{-- هيدر الصفحة الكلاسيكي الأكاديمي --}}
    <div class="page-header">
        <div class="header-info">
            <nav class="breadcrumb-nav">
                <a href="{{ route((auth()->check() ? auth()->user()->role : 'admin') . '.dashboard') }}"><i class="fa-solid fa-house"></i>{{ __('الرئيسية') }}</a>
                <span class="sep"><i class="fa-solid fa-chevron-left"></i></span>
                <a href="{{ route((auth()->check() ? auth()->user()->role : 'admin') . '.exams.index') }}">{{ __('إدارة الاختبارات') }}</a>
                <span class="sep"><i class="fa-solid fa-chevron-left"></i></span>
                <span class="current">{{ __('بناء اختبار جديد') }}</span>
            </nav>
            <h1 class="page-title">
                <i class="fa-solid fa-file-circle-plus text-primary"></i>{{ __('بناء واختيار تقييم جديد') }}<span class="exam-badge" style="background: #e0f2fe; color: #0284c7;">{{ __('جديد 🌟') }}</span>
            </h1>
            <p class="page-subtitle">{{ __('صمم أسئلة الاختبار الموضوعية والمقالية، أرفق الصور التوضيحية واضبط الجدولة ومعايير التقييم بدقة وسهولة.') }}</p>
        </div>

        <div class="header-actions">
            <a href="{{ route((auth()->check() ? auth()->user()->role : 'admin') . '.exams.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-right"></i>{{ __('إلغاء') }}
            </a>
            <button type="button" onclick="submitCreateExam()" id="saveBtn" class="btn-primary">
                <i class="fa-solid fa-paper-plane"></i>
                <span>{{ __('حفظ ونشر الاختبار') }}</span>
            </button>
        </div>
    </div>

    <form id="createExamForm" onsubmit="event.preventDefault(); submitCreateExam();" enctype="multipart/form-data">
        @csrf

        <div class="edit-layout-grid">

            {{-- الجانب الأيمن: البيانات الأساسية والجدولة --}}
            <aside class="sidebar-config">
                <div class="glass-card sticky-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <h3>{{ __('إعدادات الاختبار الأكاديمي') }}</h3>
                    </div>

                    <div class="card-body">
                        <div class="f-group mb-20">
                            <label class="f-label">{{ __('عنوان الاختبار') }}<span class="req">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-heading icon"></i>
                                <input type="text" name="title" id="exam_title" class="f-input" required placeholder="{{ __('مثال: الاختبار النصفي - الفصل الأول 2026') }}">
                            </div>
                        </div>

                        <div class="f-group mb-20">
                            <label class="f-label">{{ __('المرحلة / الصف الدراسي') }}<span class="req">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-layer-group icon"></i>
                                <select name="stage_id" id="stage_select" class="f-input" style="padding-inline-start: 40px;" onchange="filterSubjectsByStage(this.value)" required>
                                    <option value="">{{ __('اختر المرحلة الدراسية...') }}</option>
                                    @foreach($stages as $stg)
                                        <option value="{{ $stg->id }}">{{ $stg->label_ar ?? $stg->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="f-group mb-20">
                            <label class="f-label">{{ __('المادة التعليمية') }}<span class="req">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-book-open icon"></i>
                                <select name="subject_id" id="subject_select" class="f-input" style="padding-inline-start: 40px;" required>
                                    <option value="">{{ __('اختر المادة التعليمية...') }}</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}" data-stage-id="{{ $sub->stage_id }}">{{ $sub->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="f-group mb-20">
                            <label class="f-label">{{ __('المدة الزمنية (بالدقائق)') }}<span class="req">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-regular fa-clock icon"></i>
                                <input type="number" name="duration_minutes" value="60" class="f-input" required min="1" max="600">
                            </div>
                        </div>

                        <div class="f-group mb-20">
                            <label class="f-label">{{ __('درجة النجاح') }}</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-award icon"></i>
                                <input type="number" name="pass_marks" value="50" class="f-input" min="1">
                            </div>
                        </div>

                        {{-- جدولة وتوقيت الاختبار --}}
                        <div class="f-group mb-20" style="padding-top: 14px; border-top: 1px solid var(--border-color);">
                            <label class="f-label" style="display: flex; align-items: center; gap: 6px; font-weight: 800; color: #059669;">
                                <i class="fa-solid fa-calendar-check"></i>
                                {{ __('جدولة وتوقيت الاختبار') }}
                            </label>
                            <div style="margin-bottom: 12px;">
                                <label class="f-label-sm" style="display: block; font-size: 0.78rem; font-weight: 700; margin-bottom: 4px;">{{ __('تاريخ ووقت بدء الاختبار') }}</label>
                                <input type="datetime-local" name="starts_at" id="exam_starts_at" class="f-input">
                                <small style="color: var(--text-muted); font-size: 0.72rem; display: block; margin-top: 2px;">{{ __('لن يتمكن الطالب من دخول الاختبار قبل هذا الموعد.') }}</small>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <label class="f-label-sm" style="display: block; font-size: 0.78rem; font-weight: 700; margin-bottom: 4px;">{{ __('تاريخ ووقت إغلاق الاختبار') }}</label>
                                <input type="datetime-local" name="ends_at" id="exam_ends_at" class="f-input">
                                <small style="color: var(--text-muted); font-size: 0.72rem; display: block; margin-top: 2px;">{{ __('يُقفل الاختبار ويمنع الدخول بعد هذا الموعد.') }}</small>
                            </div>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <button type="button" class="btn-preset-time" onclick="setSchedulePreset('now_24h')">
                                    <i class="fa-solid fa-bolt"></i> {{ __('متاح 24 ساعة') }}
                                </button>
                                <button type="button" class="btn-preset-time" onclick="setSchedulePreset('now_3d')">
                                    <i class="fa-solid fa-calendar-day"></i> {{ __('متاح 3 أيام') }}
                                </button>
                                <button type="button" class="btn-preset-time" onclick="setSchedulePreset('clear')">
                                    <i class="fa-solid fa-rotate-left"></i> {{ __('متاح دائماً') }}
                                </button>
                            </div>
                        </div>

                        {{-- سياسة إعلان النتيجة --}}
                        <div class="f-group mb-20" style="padding-top: 14px; border-top: 1px solid var(--border-color);">
                            <label class="f-label" style="display: flex; align-items: center; gap: 6px; font-weight: 800;">
                                <i class="fa-solid fa-eye-slash text-primary"></i>
                                {{ __('سياسة إعلان نتائج الاختبار') }}
                            </label>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 0.82rem; color: var(--text-main); cursor: pointer; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color);">
                                    <input type="radio" name="show_result_immediately" value="0" checked style="margin-top: 3px;">
                                    <div>
                                        <strong style="display: block;">{{ __('حجب النتيجة حتى اعتماد المعلم (موصى به)') }}</strong>
                                        <small style="color: var(--text-muted); line-height: 1.4; display: block; margin-top: 2px;">{{ __('لا تظهر العلامة أو الإجابات للطالب إلا بعد تصحيحك ومراجعتك للاختبار.') }}</small>
                                    </div>
                                </label>
                                <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 0.82rem; color: var(--text-main); cursor: pointer; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color);">
                                    <input type="radio" name="show_result_immediately" value="1" style="margin-top: 3px;">
                                    <div>
                                        <strong style="display: block;">{{ __('إظهار النتيجة فورياً بعد التسليم') }}</strong>
                                        <small style="color: var(--text-muted); line-height: 1.4; display: block; margin-top: 2px;">{{ __('تظهر النتيجة للطالب تلقائياً إذا كانت جميع الأسئلة موضوعية.') }}</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- إحصائيات الأسئلة اللحظية --}}
                        <div class="exam-stats-info" style="display: flex; gap: 12px; background: #f1f5f9; padding: 14px; border-radius: 10px;">
                            <div class="stat-item" style="flex: 1; text-align: center;">
                                <span class="stat-label" style="display: block; font-size: 0.76rem; color: var(--text-muted); margin-bottom: 2px;">{{ __('عدد الأسئلة') }}</span>
                                <strong class="stat-val" id="questionsCount" style="font-size: 1.3rem; color: #0f172a;">1</strong>
                            </div>
                            <div class="stat-item" style="flex: 1; text-align: center; border-inline-start: 1px solid #cbd5e1;">
                                <span class="stat-label" style="display: block; font-size: 0.76rem; color: var(--text-muted); margin-bottom: 2px;">{{ __('مجموع النقاط') }}</span>
                                <strong class="stat-val" id="totalPointsCount" style="font-size: 1.3rem; color: #0284c7;">5</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- الجانب الأيسر: قائمة الأسئلة وإدارتها --}}
            <main class="questions-container">
                <div class="section-title-bar">
                    <h3><i class="fa-solid fa-list-check"></i>{{ __('أسئلة الاختبار الأكاديمية') }}</h3>

                    {{-- أزرار إضافة الأسئلة العلوية المطابقة تماماً لشاشة التعديل --}}
                    <div class="add-q-btns">
                        <button type="button" onclick="addQuestion('mcq')" class="btn-add-q mcq">
                            <i class="fa-solid fa-plus"></i>{{ __('سؤال اختيار من متعدد') }}
                        </button>
                        <button type="button" onclick="addQuestion('essay')" class="btn-add-q text">
                            <i class="fa-solid fa-plus"></i>{{ __('سؤال مقالي/نصي') }}
                        </button>
                    </div>
                </div>

                <div id="q_list">
                    {{-- يبدأ الاختبار بسؤال موضوعي افتراضي جاهز للكتابة فوراً --}}
                    <div class="glass-card q-card mb-20" id="q_card_0">
                        <input type="hidden" name="questions[0][type]" value="mcq">
                        <input type="hidden" name="questions[0][remove_image]" id="remove_img_0" value="0">

                        <div class="q-card-header">
                            <span class="q-number"><i class="fa-solid fa-circle-question"></i>{{ __('سؤال') }} <span class="q-idx">1</span></span>
                            <div class="q-actions">
                                <span class="type-badge">MCQ</span>
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <label style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted);">{{ __('الدرجة:') }}</label>
                                    <input type="number" name="questions[0][points]" value="5" min="1" oninput="updateQuestionsUI()" style="width: 55px; padding: 3px 6px; border-radius: 6px; border: 1px solid var(--border-color); font-weight: 700; text-align: center;">
                                </div>
                                <button type="button" onclick="removeQuestionCard(this)" class="btn-delete-q" title="{{ __('حذف السؤال') }}">
                                    <i class="fa-solid fa-trash-can"></i>{{ __('حذف') }}
                                </button>
                            </div>
                        </div>

                        <div class="q-card-body">
                            <div class="f-group mb-20">
                                <label class="f-label">{{ __('نص السؤال') }}<span class="req" style="color: #dc2626;">*</span></label>
                                <textarea name="questions[0][question_text]" class="f-input f-textarea" rows="2" placeholder="{{ __('اكتب نص السؤال هنا...') }}" required></textarea>
                            </div>

                            {{-- إرفاق صورة السؤال --}}
                            <div class="q-image-upload-wrapper mb-20">
                                <input type="file" name="questions[0][image]" id="q_img_input_0" accept="image/*" hidden onchange="handleQuestionImage(this, 0)">
                                <label for="q_img_input_0" class="q-img-label">
                                    <i class="fa-solid fa-image"></i>
                                    <span>{{ __('إرفاق صورة مع السؤال (اختياري)') }}</span>
                                </label>
                                <div class="q-image-preview" id="q_img_preview_0" style="display: none;">
                                    <img id="q_img_target_0" src="" alt="{{ __('صورة السؤال') }}">
                                    <button type="button" class="btn-remove-img" onclick="removeQuestionImage(0)" title="{{ __('حذف الصورة') }}">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- خيارات الاختيار من متعدد مع صور الخيارات --}}
                            <div class="mcq-options-grid">
                                @foreach(['a', 'b', 'c', 'd'] as $opt)
                                <div class="f-group opt-item-box">
                                    <label class="f-label-sm">{{ __('الخيار') }} ({{ strtoupper($opt) }})</label>
                                    <div class="input-icon-wrapper opt-input-row">
                                        <span class="option-prefix">{{ strtoupper($opt) }}</span>
                                        <input type="text" name="questions[0][{{ $opt }}]" class="f-input" placeholder="{{ __('نص الخيار') }} ({{ strtoupper($opt) }})">
                                        <label for="q_opt_file_0_{{ $opt }}" class="btn-opt-img-trigger" title="{{ __('إرفاق صورة لهذا الخيار') }}">
                                            <i class="fa-solid fa-image"></i>
                                            <span>{{ __('صورة') }}</span>
                                        </label>
                                        <input type="file" name="questions[0][{{ $opt }}_image]" id="q_opt_file_0_{{ $opt }}" accept="image/*" hidden onchange="handleOptionImage(this, 0, '{{ $opt }}')">
                                        <input type="hidden" name="questions[0][remove_{{ $opt }}_image]" id="remove_opt_img_0_{{ $opt }}" value="0">
                                    </div>
                                    <div class="opt-image-preview-box" id="q_opt_preview_0_{{ $opt }}" style="display: none;">
                                        <img id="q_opt_img_target_0_{{ $opt }}" src="" alt="صورة الخيار {{ strtoupper($opt) }}">
                                        <button type="button" class="btn-remove-opt-img" onclick="removeOptionImage(0, '{{ $opt }}')" title="{{ __('حذف صورة الخيار') }}">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach

                                <div class="f-group full-width">
                                    <label class="f-label-sm text-success"><i class="fa-solid fa-circle-check"></i>{{ __('الإجابة الصحيحة المعتمدة') }}</label>
                                    <div class="select-wrapper">
                                        <select name="questions[0][correct_answer]" class="f-select success-select">
                                            <option value="a">الخيار (A)</option>
                                            <option value="b">الخيار (B)</option>
                                            <option value="c">الخيار (C)</option>
                                            <option value="d">الخيار (D)</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="emptyState" class="empty-state glass-card text-center p-40" style="display: none;">
                    <i class="fa-solid fa-folder-open empty-icon"></i>
                    <h4>{{ __('قائمة الأسئلة فارغة حالياً') }}</h4>
                    <p class="text-muted">{{ __('استخدم الأزرار في الأعلى لإضافة سؤال موضوعي أو مقالي.') }}</p>
                </div>
            </main>

        </div>
    </form>
</div>

<style>
    :root {
        --primary-color: #0284c7;
        --primary-hover: #0369a1;
        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --radius-lg: 18px;
        --radius-md: 12px;
        --shadow-sm: 0 4px 15px -3px rgba(0, 0, 0, 0.04);
        --shadow-hover: 0 10px 25px -5px rgba(2, 132, 199, 0.12);
    }

    .exam-edit-wrapper {
        padding-bottom: 60px;
        animation: fadeIn 0.4s ease;
    }

    /* هيدر الصفحة */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
        flex-wrap: wrap;
        gap: 15px;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 10px;
        font-weight: 500;
    }

    .breadcrumb-nav a { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
    .breadcrumb-nav a:hover { color: var(--primary-color); }
    .breadcrumb-nav .sep { font-size: 0.65rem; color: #cbd5e1; }
    .breadcrumb-nav .current { color: var(--primary-color); font-weight: 700; }

    .page-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 6px 0;
    }

    .exam-badge {
        font-size: 0.85rem;
        background: #e0f2fe;
        color: #0284c7;
        padding: 4px 14px;
        border-radius: 20px;
        font-weight: 700;
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-primary, .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        text-decoration: none;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #091a2e 0%, #1e3a8a 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.3);
    }

    .btn-secondary {
        background: #ffffff;
        color: var(--text-main);
        border: 1px solid var(--border-color);
    }
    .btn-secondary:hover {
        background: #f1f5f9;
        color: #000;
    }

    /* Grid Layout */
    .edit-layout-grid {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 30px;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .edit-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Cards */
    .glass-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .sticky-card {
        position: sticky;
        top: 20px;
    }

    .card-header {
        padding: 18px 24px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header h3 {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .header-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #eff6ff;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .card-body {
        padding: 24px;
    }

    /* Inputs & Form Groups */
    .f-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .f-label {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .f-label-sm {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-muted);
    }

    .f-label .req {
        color: #ef4444;
        margin-right: 4px;
    }

    .input-icon-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon-wrapper .icon {
        position: absolute;
        right: 14px;
        color: #94a3b8;
        font-size: 0.95rem;
    }

    .f-input, .f-select {
        width: 100%;
        padding: 11px 40px 11px 14px;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        background: #f8fafc;
        font-size: 0.9rem;
        color: var(--text-main);
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .f-input:focus, .f-select:focus {
        background: #ffffff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
    }

    .f-textarea {
        resize: vertical;
        min-height: 80px;
        padding: 12px 14px;
    }

    /* Questions Container */
    .section-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .section-title-bar h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .add-q-btns {
        display: flex;
        gap: 10px;
    }

    .btn-add-q {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: var(--radius-md);
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-add-q.mcq {
        background: #0284c7;
        color: #ffffff;
    }
    .btn-add-q.mcq:hover {
        background: #0369a1;
        transform: translateY(-1px);
    }

    .btn-add-q.text {
        background: #475569;
        color: #ffffff;
    }
    .btn-add-q.text:hover {
        background: #334155;
        transform: translateY(-1px);
    }

    /* Question Cards */
    .q-card {
        border-right: 4px solid var(--primary-color);
        transition: all 0.25s ease;
    }

    .q-card:hover {
        box-shadow: var(--shadow-hover);
    }

    .q-card-header {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .q-number {
        font-weight: 800;
        color: var(--text-main);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .q-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .type-badge {
        font-size: 0.72rem;
        font-weight: 800;
        background: #e2e8f0;
        color: #475569;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
    }

    .btn-delete-q {
        background: #fee2e2;
        color: #ef4444;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }

    .btn-delete-q:hover {
        background: #ef4444;
        color: #fff;
    }

    .q-card-body {
        padding: 20px;
    }

    /* Question Image Upload */
    .q-image-upload-wrapper {
        margin-top: 10px;
    }

    .q-img-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        background: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: var(--radius-md);
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
    }

    .q-img-label:hover {
        background: #eff6ff;
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .q-image-preview {
        margin-top: 10px;
        position: relative;
        display: inline-block;
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 1px solid var(--border-color);
        max-width: 250px;
    }

    .q-image-preview img {
        max-width: 100%;
        max-height: 140px;
        display: block;
        object-fit: contain;
        background: #0000000a;
    }

    .btn-remove-img {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(239, 68, 68, 0.9);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.75rem;
        transition: all 0.2s;
    }

    .btn-remove-img:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    /* MCQ Grid */
    .mcq-options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px dashed var(--border-color);
    }

    .full-width {
        grid-column: span 2;
    }

    .opt-item-box {
        position: relative;
    }

    .opt-input-row {
        display: flex;
        align-items: stretch;
    }

    .option-prefix {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        background: #e2e8f0;
        font-weight: 800;
        color: #475569;
        font-size: 0.85rem;
        border-top-right-radius: var(--radius-md);
        border-bottom-right-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        border-left: none;
    }

    .opt-input-row .f-input {
        padding-right: 12px;
        border-radius: 0;
    }

    .btn-opt-img-trigger {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 12px;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid var(--border-color);
        border-right: none;
        border-top-left-radius: var(--radius-md);
        border-bottom-left-radius: var(--radius-md);
        transition: all 0.2s ease;
        white-space: nowrap;
        user-select: none;
    }
    .btn-opt-img-trigger:hover {
        background: #e0f2fe;
        color: var(--primary-color);
    }

    .opt-image-preview-box {
        position: relative;
        display: inline-block;
        max-width: 140px;
        padding: 4px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        margin-top: 6px;
    }

    .opt-image-preview-box img {
        max-height: 80px;
        max-width: 100%;
        border-radius: 6px;
        object-fit: contain;
        display: block;
    }

    .btn-remove-opt-img {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .select-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .select-arrow {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        pointer-events: none;
    }

    .success-select {
        border-color: #86efac;
        background: #f0fdf4;
        color: #166534;
        font-weight: 700;
    }

    .btn-preset-time {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-preset-time:hover { background: #e2e8f0; color: #0f172a; border-color: #94a3b8; }

    .empty-state { padding: 40px; }
    .empty-icon { font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; }
    .text-success { color: #059669; }
    .mb-20 { margin-bottom: 20px; }
    .p-40 { padding: 40px; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
        }
        .header-actions {
            width: 100%;
            display: flex;
            gap: 10px;
        }
        .header-actions .btn-secondary,
        .header-actions .btn-primary,
        #saveBtn {
            flex: 1;
            justify-content: center;
        }
        .exam-edit-wrapper {
            padding: 12px 10px 60px;
        }
        .mcq-options-grid {
            grid-template-columns: 1fr;
        }
        .full-width {
            grid-column: span 1;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let qIndex = 1;

    function filterSubjectsByStage(stageId) {
        const subjectSelect = document.getElementById('subject_select');
        const options = subjectSelect.querySelectorAll('option');
        let hasSelected = false;

        options.forEach(opt => {
            if (!opt.value) return; // Keep placeholder
            const optStage = opt.getAttribute('data-stage-id');
            if (!stageId || optStage === stageId) {
                opt.style.display = '';
                if (!hasSelected) {
                    opt.selected = true;
                    hasSelected = true;
                }
            } else {
                opt.style.display = 'none';
                if (opt.selected) opt.selected = false;
            }
        });
    }

    function updateQuestionsUI() {
        const qCards = document.querySelectorAll('.q-card');
        const countElem = document.getElementById('questionsCount');
        const pointsElem = document.getElementById('totalPointsCount');
        const emptyState = document.getElementById('emptyState');

        if (countElem) countElem.innerText = qCards.length;
        if (emptyState) emptyState.style.display = qCards.length === 0 ? 'block' : 'none';

        let totalPts = 0;
        qCards.forEach((card, idx) => {
            const idxSpan = card.querySelector('.q-idx');
            if (idxSpan) idxSpan.innerText = idx + 1;

            const ptsInput = card.querySelector('input[type="number"][name*="[points]"]');
            if (ptsInput) {
                totalPts += parseInt(ptsInput.value) || 0;
            }
        });

        if (pointsElem) pointsElem.innerText = totalPts;
    }

    function handleQuestionImage(input, index) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(`q_img_preview_${index}`);
                const target = document.getElementById(`q_img_target_${index}`);
                if (target && preview) {
                    target.src = e.target.result;
                    preview.style.display = 'inline-block';
                }
                const removeInput = document.getElementById(`remove_img_${index}`);
                if (removeInput) removeInput.value = '0';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeQuestionImage(index) {
        const input = document.getElementById(`q_img_input_${index}`);
        const preview = document.getElementById(`q_img_preview_${index}`);
        const target = document.getElementById(`q_img_target_${index}`);
        const removeInput = document.getElementById(`remove_img_${index}`);
        if (input) input.value = '';
        if (target) target.src = '';
        if (preview) preview.style.display = 'none';
        if (removeInput) removeInput.value = '1';
    }

    function handleOptionImage(input, index, opt) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(`q_opt_preview_${index}_${opt}`);
                const target = document.getElementById(`q_opt_img_target_${index}_${opt}`);
                if (target && preview) {
                    target.src = e.target.result;
                    preview.style.display = 'inline-block';
                }
                const removeInput = document.getElementById(`remove_opt_img_${index}_${opt}`);
                if (removeInput) removeInput.value = '0';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeOptionImage(index, opt) {
        const input = document.getElementById(`q_opt_file_${index}_${opt}`);
        const preview = document.getElementById(`q_opt_preview_${index}_${opt}`);
        const target = document.getElementById(`q_opt_img_target_${index}_${opt}`);
        const removeInput = document.getElementById(`remove_opt_img_${index}_${opt}`);
        if (input) input.value = '';
        if (target) target.src = '';
        if (preview) preview.style.display = 'none';
        if (removeInput) removeInput.value = '1';
    }

    function setSchedulePreset(type) {
        const startInput = document.getElementById('exam_starts_at');
        const endInput = document.getElementById('exam_ends_at');
        const now = new Date();
        const formatLocal = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            const hours = String(d.getHours()).padStart(2, '0');
            const minutes = String(d.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        };

        if (type === 'now_24h') {
            startInput.value = formatLocal(now);
            const in24 = new Date(now.getTime() + 24 * 60 * 60 * 1000);
            endInput.value = formatLocal(in24);
        } else if (type === 'now_3d') {
            startInput.value = formatLocal(now);
            const in3d = new Date(now.getTime() + 3 * 24 * 60 * 60 * 1000);
            endInput.value = formatLocal(in3d);
        } else if (type === 'clear') {
            startInput.value = '';
            endInput.value = '';
        }
    }

    function addQuestion(type) {
        const container = document.getElementById('q_list');
        const emptyState = document.getElementById('emptyState');
        if (emptyState) emptyState.style.display = 'none';

        let newCard = document.createElement('div');
        newCard.className = 'glass-card q-card mb-20';
        newCard.id = `q_card_${qIndex}`;

        let mcqHtml = '';
        if (type === 'mcq') {
            mcqHtml = `
            <div class="mcq-options-grid">
                ${['a', 'b', 'c', 'd'].map(opt => `
                    <div class="f-group opt-item-box">
                        <label class="f-label-sm">{{ __('الخيار') }} (${opt.toUpperCase()})</label>
                        <div class="input-icon-wrapper opt-input-row">
                            <span class="option-prefix">${opt.toUpperCase()}</span>
                            <input type="text" name="questions[${qIndex}][${opt}]" class="f-input" placeholder="نص الخيار (${opt.toUpperCase()})">
                            <label for="q_opt_file_${qIndex}_${opt}" class="btn-opt-img-trigger" title="{{ __('إرفاق صورة لهذا الخيار') }}">
                                <i class="fa-solid fa-image"></i>
                                <span>{{ __('صورة') }}</span>
                            </label>
                            <input type="file" name="questions[${qIndex}][${opt}_image]" id="q_opt_file_${qIndex}_${opt}" accept="image/*" hidden onchange="handleOptionImage(this, ${qIndex}, '${opt}')">
                            <input type="hidden" name="questions[${qIndex}][remove_${opt}_image]" id="remove_opt_img_${qIndex}_${opt}" value="0">
                        </div>
                        <div class="opt-image-preview-box" id="q_opt_preview_${qIndex}_${opt}" style="display: none;">
                            <img id="q_opt_img_target_${qIndex}_${opt}" src="" alt="صورة الخيار ${opt.toUpperCase()}">
                            <button type="button" class="btn-remove-opt-img" onclick="removeOptionImage(${qIndex}, '${opt}')" title="{{ __('حذف صورة الخيار') }}">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                `).join('')}
                <div class="f-group full-width">
                    <label class="f-label-sm text-success"><i class="fa-solid fa-circle-check"></i>{{ __('الإجابة الصحيحة المعتمدة') }}</label>
                    <div class="select-wrapper">
                        <select name="questions[${qIndex}][correct_answer]" class="f-select success-select">
                            <option value="a">الخيار (A)</option>
                            <option value="b">الخيار (B)</option>
                            <option value="c">الخيار (C)</option>
                            <option value="d">الخيار (D)</option>
                        </select>
                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                    </div>
                </div>
            </div>`;
        } else {
            mcqHtml = `
            <div class="f-group mb-20" style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0;">
                    <input type="checkbox" name="questions[${qIndex}][require_file]" value="1">
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-main);">{{ __('السماح للطالب بإرفاق ملفات/صور مع الإجابة') }}</span>
                </label>
            </div>`;
        }

        newCard.innerHTML = `
            <input type="hidden" name="questions[${qIndex}][type]" value="${type}">
            <input type="hidden" name="questions[${qIndex}][remove_image]" id="remove_img_${qIndex}" value="0">
            <div class="q-card-header">
                <span class="q-number"><i class="fa-solid fa-circle-question"></i>{{ __('سؤال') }} <span class="q-idx"></span></span>
                <div class="q-actions">
                    <span class="type-badge">${type.toUpperCase()}</span>
                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                        <label style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted);">{{ __('الدرجة:') }}</label>
                        <input type="number" name="questions[${qIndex}][points]" value="5" min="1" oninput="updateQuestionsUI()" style="width: 55px; padding: 3px 6px; border-radius: 6px; border: 1px solid var(--border-color); font-weight: 700; text-align: center;">
                    </div>
                    <button type="button" onclick="removeQuestionCard(this)" class="btn-delete-q" title="{{ __('حذف السؤال') }}">
                        <i class="fa-solid fa-trash-can"></i>{{ __('حذف') }}</button>
                </div>
            </div>
            <div class="q-card-body">
                <div class="f-group mb-20">
                    <label class="f-label">{{ __('نص السؤال') }}<span class="req" style="color: #dc2626;">*</span></label>
                    <textarea name="questions[${qIndex}][question_text]" class="f-input f-textarea" rows="2" placeholder="{{ __('اكتب نص السؤال الجديد هنا...') }}" required></textarea>
                </div>
                <div class="q-image-upload-wrapper mb-20">
                    <input type="file" name="questions[${qIndex}][image]" id="q_img_input_${qIndex}" accept="image/*" hidden onchange="handleQuestionImage(this, ${qIndex})">
                    <label for="q_img_input_${qIndex}" class="q-img-label">
                        <i class="fa-solid fa-image"></i>
                        <span>{{ __('إرفاق صورة مع السؤال (اختياري)') }}</span>
                    </label>
                    <div class="q-image-preview" id="q_img_preview_${qIndex}" style="display: none;">
                        <img id="q_img_target_${qIndex}" src="" alt="{{ __('صورة السؤال') }}">
                        <button type="button" class="btn-remove-img" onclick="removeQuestionImage(${qIndex})" title="{{ __('حذف الصورة') }}">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                ${mcqHtml}
            </div>
        `;

        container.appendChild(newCard);
        qIndex++;
        updateQuestionsUI();
        newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function removeQuestionCard(btn) {
        const qCards = document.querySelectorAll('.q-card');
        if (qCards.length <= 1) {
            Swal.fire('{{ __("تنبيه") }}', '{{ __("يجب أن يحتوي الاختبار على سؤال واحد على الأقل!") }}', 'warning');
            return;
        }

        Swal.fire({
            title: '{{ __("هل أنت متأكد؟") }}',
            text: '{{ __("سيتم حذف هذا السؤال من النموذج.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '{{ __("نعم، احذفه") }}',
            cancelButtonText: '{{ __("تراجع") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                const card = btn.closest('.q-card');
                card.remove();
                updateQuestionsUI();
            }
        });
    }

    function submitCreateExam() {
        const form = document.getElementById('createExamForm');
        const btn = document.getElementById('saveBtn');

        const title = document.getElementById('exam_title').value.trim();
        const stageId = document.getElementById('stage_select').value;
        const subjectId = document.getElementById('subject_select').value;

        if (!title) {
            Swal.fire('{{ __("تنبيه") }}', '{{ __("يرجى إدخال عنوان الاختبار أولاً.") }}', 'warning');
            return;
        }
        if (!subjectId) {
            Swal.fire('{{ __("تنبيه") }}', '{{ __("يرجى اختيار المادة التعليمية للاختبار.") }}', 'warning');
            return;
        }

        const qCards = document.querySelectorAll('.q-card');
        if (qCards.length === 0) {
            Swal.fire('{{ __("تنبيه") }}', '{{ __("يرجى إضافة سؤال واحد على الأقل للاختبار.") }}', 'warning');
            return;
        }

        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>{{ __("جاري النشر والاعتماد...") }}</span>';

        const role = "{{ auth()->check() ? auth()->user()->role : 'admin' }}";
        const postUrl = `/${role}/exams`;
        const indexRoute = "{{ route((auth()->check() ? auth()->user()->role : 'admin') . '.exams.index') }}";

        axios.post(postUrl, formData)
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: '{{ __("تم النشر بنجاح! 🚀") }}',
                    text: res.data.message || '{{ __("تم بناء ونشر الاختبار للطلاب بنجاح.") }}',
                    timer: 2200,
                    showConfirmButton: false
                }).then(() => {
                    location.href = indexRoute;
                });
            })
            .catch(err => {
                const msg = err.response?.data?.message || '{{ __("يرجى التأكد من ملء نص جميع الأسئلة والخيارات.") }}';
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("خطأ في العملية!") }}',
                    text: msg,
                });
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>{{ __("حفظ ونشر الاختبار") }}</span>';
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateQuestionsUI();
    });
</script>
@endsection