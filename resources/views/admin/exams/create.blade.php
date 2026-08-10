@extends('layouts.app')

@section('title', 'بوابة المحاضر | بناء تقييم أكاديمي')

@section('content')
<div class="exam-builder-container">

    <!-- Header Section -->
    <header class="page-header">
        <div class="header-info">
            <nav class="breadcrumb-nav">
                <span>البوابة الإدارية</span>
                <i class="fa-solid fa-chevron-left sep"></i>
                <span>المساقات</span>
                <i class="fa-solid fa-chevron-left sep"></i>
                <span class="current">إعداد اختبار جديد</span>
            </nav>
            <h1 class="page-title">
                مركز إعداد التقييمات الأكاديمية
                <span class="badge-live">
                    <span class="pulse-dot"></span> مباشر
                </span>
            </h1>
            <p class="page-subtitle">صمم الأسئلة الموضوعية والمقالية واضبط معايير التقييم بدقة وسهولة</p>
        </div>
        <div class="header-actions">
            <button type="button" onclick="publishExamNow()" id="publishBtn" class="btn-publish">
                <i class="fa-solid fa-paper-plane"></i>
                <span>نشر الاختبار للمساق</span>
            </button>
        </div>
    </header>

    <form id="mainExamForm" enctype="multipart/form-data">
        @csrf
        <div class="builder-grid">

            <!-- Right Column: Question Construction Zone -->
            <main class="questions-column">

                {{-- Empty State Placeholder --}}
                <div id="questions_placeholder" class="empty-state-card">
                    <div class="empty-icon-wrapper">
                        <i class="fa-solid fa-file-circle-plus"></i>
                    </div>
                    <h3>قائمة الأسئلة فارغة حالياً</h3>
                    <p>ابدأ ببناء التقييم عبر اختيار نوع السؤال (موضوعي أو مقالي) من الشريط السفلي.</p>
                </div>

                <div id="questions_list" class="questions-list">
                    {{-- Dynamically inserted questions go here --}}
                </div>

                <!-- Sticky Floating Toolbar -->
                <div class="floating-toolbar">
                    <button type="button" onclick="addNewQuestion('mcq')" class="tool-btn btn-mcq">
                        <div class="tool-icon">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div class="tool-text">
                            <strong>سؤال موضوعي (MCQ)</strong>
                            <small>تصحيح تلقائي وإجابات متعددة</small>
                        </div>
                    </button>

                    <div class="toolbar-divider"></div>

                    <button type="button" onclick="addNewQuestion('essay')" class="tool-btn btn-essay">
                        <div class="tool-icon">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <div class="tool-text">
                            <strong>سؤال مقالي (Essay)</strong>
                            <small>إجابة كتابية أو رفع ملفات</small>
                        </div>
                    </button>
                </div>
            </main>

            <!-- Left Column: Settings & Live Stats -->
            <aside class="sidebar-column">

                <!-- Live Summary Card -->
                <div class="builder-card summary-card">
                    <div class="card-header">
                        <i class="fa-solid fa-chart-pie"></i>
                        <h3>ملخص التقييم اللحظي</h3>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-box">
                            <span class="stat-label">إجمالي الأسئلة</span>
                            <strong id="q_stat_count" class="stat-value">0</strong>
                        </div>
                        <div class="stat-box">
                            <span class="stat-label">مجموع النقاط</span>
                            <strong id="q_stat_points" class="stat-value highlight">0</strong>
                        </div>
                    </div>
                </div>

                <!-- Academic Criteria Card -->
                <div class="builder-card">
                    <div class="card-header">
                        <i class="fa-solid fa-sliders"></i>
                        <h3>المعايير الأكاديمية</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>عنوان الاختبار الأكاديمي <span class="req">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="مثلاً: الامتحان النهائي - 2026" required>
                        </div>

                        <div class="form-group">
                            <label>المرحلة / الصف الدراسي <span class="req">*</span></label>
                            <select id="stage_picker" class="form-control form-select" required>
                                <option value="">اختر الصف الدراسي...</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->label_ar }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>المادة التعليمية <span class="req">*</span></label>
                            <select name="subject_id" id="subject_picker" class="form-control form-select" disabled required>
                                <option value="">اختر المرحلة أولاً...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>مدة الاختبار</label>
                            <div class="input-addon-group">
                                <input type="number" name="duration_minutes" value="60" class="form-control" min="1">
                                <span class="addon-text">دقيقة</span>
                            </div>
                        </div>
                    </div>
                </div>

            </aside>

        </div>
    </form>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --primary: #6366f1;
        --primary-hover: #4f46e5;
        --primary-light: #eeef2f7;
        --success: #10b981;
        --success-hover: #059669;
        --danger: #f43f5e;
        --danger-light: #fff1f2;
        --bg-main: #f8fafc;
        --surface: #ffffff;
        --border: #e2e8f0;
        --border-hover: #cbd5e1;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --radius-xl: 20px;
        --radius-lg: 14px;
        --radius-md: 10px;
        --shadow-subtle: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.04);
        --shadow-floating: 0 20px 40px -10px rgba(15, 23, 42, 0.25);
    }

    .exam-builder-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 30px 20px;
        animation: fadeIn 0.4s ease-out;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 8px;
        font-weight: 600;
    }

    .breadcrumb-nav .sep {
        font-size: 0.65rem;
        color: #94a3b8;
    }

    .breadcrumb-nav .current {
        color: var(--primary);
    }

    .page-title {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 6px 0;
        letter-spacing: -0.02em;
    }

    .badge-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        background: #ecfdf5;
        color: var(--success);
        padding: 4px 12px;
        border-radius: 30px;
        font-weight: 700;
        border: 1px solid #a7f3d0;
    }

    .pulse-dot {
        width: 7px;
        height: 7px;
        background-color: var(--success);
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse 1.6s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin: 0;
    }

    .btn-publish {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        border: none;
        padding: 14px 28px;
        border-radius: var(--radius-lg);
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.35);
    }

    .btn-publish:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.45);
    }

    /* Grid Layout */
    .builder-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 30px;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .builder-grid {
            grid-template-columns: 1fr;
        }
    }

    .questions-column, .sidebar-column {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .sidebar-column {
        position: sticky;
        top: 25px;
    }

    /* Empty State Card */
    .empty-state-card {
        text-align: center;
        padding: 70px 20px;
        background: var(--surface);
        border: 2px dashed #cbd5e1;
        border-radius: var(--radius-xl);
        transition: border-color 0.3s ease;
    }

    .empty-icon-wrapper {
        width: 76px;
        height: 76px;
        background: #f1f5f9;
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin: 0 auto 20px auto;
    }

    .empty-state-card h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .empty-state-card p {
        color: var(--text-muted);
        max-width: 380px;
        margin: 0 auto;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* Question Cards */
    .questions-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .question-card {
        background: var(--surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        padding: 28px;
        box-shadow: var(--shadow-card);
        transition: all 0.25s ease;
        animation: slideUp 0.3s ease-out;
    }

    .question-card:hover {
        border-color: var(--border-hover);
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.07);
    }

    .q-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .q-badge {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--primary);
        background: #e0e7ff;
        padding: 6px 16px;
        border-radius: 30px;
    }

    .q-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .points-input-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        padding: 6px 14px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
    }

    .points-input-wrapper label {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-muted);
    }

    .points-input-wrapper input {
        width: 50px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 800;
        font-size: 1rem;
        color: var(--text-dark);
        outline: none;
    }

    .btn-delete-q {
        background: var(--danger-light);
        color: var(--danger);
        border: 1px solid #fecdd3;
        width: 38px;
        height: 38px;
        border-radius: var(--radius-md);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-delete-q:hover {
        background: var(--danger);
        color: #ffffff;
    }

    /* Floating Toolbar */
    .floating-toolbar {
        display: flex;
        gap: 12px;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(12px);
        padding: 10px;
        border-radius: var(--radius-xl);
        position: sticky;
        bottom: 25px;
        z-index: 90;
        box-shadow: var(--shadow-floating);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .tool-btn {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-lg);
        color: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: right;
    }

    .tool-btn:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateY(-2px);
    }

    .tool-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .btn-mcq .tool-icon {
        background: rgba(99, 102, 241, 0.25);
        color: #818cf8;
    }

    .btn-essay .tool-icon {
        background: rgba(20, 184, 166, 0.25);
        color: #2dd4bf;
    }

    .tool-text strong {
        display: block;
        font-size: 0.88rem;
    }

    .tool-text small {
        font-size: 0.72rem;
        color: #94a3b8;
    }

    .toolbar-divider {
        width: 1px;
        background: rgba(255, 255, 255, 0.12);
        margin: 4px 0;
    }

    /* MCQ Grid */
    .mcq-options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-top: 18px;
    }

    @media (max-width: 640px) {
        .mcq-options-grid {
            grid-template-columns: 1fr;
        }
    }

    .option-group {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: border-color 0.2s;
    }

    .option-group:focus-within {
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .option-badge {
        padding: 12px 16px;
        background: #e2e8f0;
        font-weight: 800;
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    .option-input {
        flex: 1;
        border: none;
        padding: 12px;
        background: transparent;
        outline: none;
        font-family: inherit;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Sidebar Cards */
    .builder-card {
        background: var(--surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-subtle);
        overflow: hidden;
    }

    .card-header {
        padding: 18px 22px;
        background: #ffffff;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .card-header i {
        color: var(--primary);
    }

    .card-body {
        padding: 22px;
    }

    .summary-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
    }

    .summary-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    .summary-card .card-header i {
        color: var(--success);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 20px;
    }

    .stat-box {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-lg);
        padding: 16px;
        text-align: center;
    }

    .stat-label {
        display: block;
        font-size: 0.75rem;
        color: #94a3b8;
        margin-bottom: 6px;
    }

    .stat-value {
        font-size: 1.7rem;
        font-weight: 800;
    }

    .stat-value.highlight {
        color: var(--success);
    }

    /* Forms */
    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .req {
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        padding: 12px 14px;
        border-radius: var(--radius-md);
        border: 1.5px solid var(--border);
        background: var(--bg-main);
        font-family: inherit;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-dark);
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
    }

    .input-addon-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .addon-text {
        font-weight: 700;
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    /* Image Attachment & Checkbox */
    .q-image-upload-wrapper {
        margin-top: 14px;
    }

    .q-img-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #f1f5f9;
        color: #475569;
        border: 1px dashed #cbd5e1;
        border-radius: var(--radius-md);
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .q-img-label:hover {
        background: #e0e7ff;
        color: var(--primary);
        border-color: var(--primary);
    }

    .q-image-preview {
        margin-top: 12px;
        position: relative;
        display: inline-block;
        max-width: 100%;
    }

    .q-image-preview img {
        max-height: 180px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        object-fit: contain;
    }

    .btn-remove-img {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--danger);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .essay-attach-box {
        margin-top: 18px;
        padding: 14px 18px;
        background: #f8fafc;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .custom-checkbox {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    const stagesData = @json($stages);
    const stagePicker = document.getElementById('stage_picker');
    const subjectPicker = document.getElementById('subject_picker');
    let qIdx = 0;

    // 1. الربط الديناميكي بين المرحلة والمادة
    stagePicker.addEventListener('change', function() {
        const id = this.value;
        subjectPicker.innerHTML = '<option value="">اختر المادة...</option>';
        if (id) {
            const stage = stagesData.find(s => s.id == id);
            if (stage && stage.subjects.length > 0) {
                stage.subjects.forEach(sub => {
                    subjectPicker.innerHTML += `<option value="${sub.id}">${sub.name_ar}</option>`;
                });
                subjectPicker.disabled = false;
            } else {
                subjectPicker.innerHTML = '<option value="">لا توجد مواد متاحة</option>';
                subjectPicker.disabled = true;
            }
        } else {
            subjectPicker.disabled = true;
        }
    });

    // 2. تحديث إحصائيات الأسئلة والدرجات
    function updateStats() {
        const cards = document.querySelectorAll('.question-card');
        document.getElementById('q_stat_count').textContent = cards.length;

        let total = 0;
        const pointInputs = document.querySelectorAll('input[name*="[points]"]');
        pointInputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });
        document.getElementById('q_stat_points').textContent = total;
    }

    // 3. معالجة معاينة صورة السؤال
    function handleQuestionImage(input, index) {
        const previewContainer = document.getElementById(`q_img_preview_${index}`);
        const imgElement = document.getElementById(`q_img_${index}`);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgElement.src = e.target.result;
                previewContainer.style.display = 'inline-block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeQuestionImage(index) {
        const input = document.getElementById(`q_img_input_${index}`);
        const previewContainer = document.getElementById(`q_img_preview_${index}`);
        const imgElement = document.getElementById(`q_img_${index}`);

        input.value = '';
        imgElement.src = '';
        previewContainer.style.display = 'none';
    }

    // 4. إضافة سؤال جديد
    function addNewQuestion(type) {
        document.getElementById('questions_placeholder').style.display = 'none';
        const list = document.getElementById('questions_list');

        let html = `
            <div class="question-card">
                <input type="hidden" name="questions[${qIdx}][type]" value="${type}">
                <div class="q-card-header">
                    <span class="q-badge">سؤال #${qIdx + 1}</span>
                    <div class="q-header-right">
                        <div class="points-input-wrapper">
                            <label>الدرجة:</label>
                            <input type="number" name="questions[${qIdx}][points]" value="5" min="1" oninput="updateStats()" required>
                        </div>
                        <button type="button" class="btn-delete-q" onclick="this.closest('.question-card').remove(); updateStats();" title="حذف السؤال">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>نص السؤال الأكاديمي <span class="req">*</span></label>
                    <textarea name="questions[${qIdx}][question_text]" class="form-control" rows="2" placeholder="اكتب نص السؤال بوضوح هنا..." required></textarea>

                    <div class="q-image-upload-wrapper">
                        <input type="file" name="questions[${qIdx}][image]" id="q_img_input_${qIdx}" accept="image/*" hidden onchange="handleQuestionImage(this, ${qIdx})">
                        <label for="q_img_input_${qIdx}" class="q-img-label">
                            <i class="fa-solid fa-image"></i>
                            <span>إرفاق صورة مساعدة</span>
                        </label>
                        <div class="q-image-preview" id="q_img_preview_${qIdx}" style="display: none;">
                            <img id="q_img_${qIdx}" src="" alt="صورة السؤال">
                            <button type="button" class="btn-remove-img" onclick="removeQuestionImage(${qIdx})" title="حذف الصورة">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
        `;

        if (type === 'mcq') {
            html += `
                <div class="mcq-options-grid">
                    <div class="option-group"><span class="option-badge">A</span><input type="text" name="questions[${qIdx}][a]" class="option-input" placeholder="الخيار الأول" required></div>
                    <div class="option-group"><span class="option-badge">B</span><input type="text" name="questions[${qIdx}][b]" class="option-input" placeholder="الخيار الثاني" required></div>
                    <div class="option-group"><span class="option-badge">C</span><input type="text" name="questions[${qIdx}][c]" class="option-input" placeholder="الخيار الثالث" required></div>
                    <div class="option-group"><span class="option-badge">D</span><input type="text" name="questions[${qIdx}][d]" class="option-input" placeholder="الخيار الرابع" required></div>
                </div>
                <div class="form-group" style="margin-top: 18px; margin-bottom: 0;">
                    <label>الإجابة الصحيحة (مفتاح التصحيح)</label>
                    <select name="questions[${qIdx}][correct_answer]" class="form-control" style="max-width: 200px;">
                        <option value="a">الخيار (A)</option>
                        <option value="b">الخيار (B)</option>
                        <option value="c">الخيار (C)</option>
                        <option value="d">الخيار (D)</option>
                    </select>
                </div>
            `;
        } else {
            html += `
                <div class="essay-attach-box">
                    <input type="checkbox" name="questions[${qIdx}][require_file]" value="1" id="file_check_${qIdx}" class="custom-checkbox">
                    <label for="file_check_${qIdx}" style="margin: 0; cursor: pointer;">
                        <strong style="display: block; font-size: 0.85rem; color: var(--text-dark);">السماح للطالب بإرفاق ملفات/صور مع الإجابة</strong>
                        <span style="display: block; font-size: 0.75rem; color: var(--text-muted);">تمكين الطالب من رفع صور الحل أو ملفات PDF عند أداء الامتحان.</span>
                    </label>
                </div>
            `;
        }

        html += `</div>`;
        list.insertAdjacentHTML('beforeend', html);
        qIdx++;
        updateStats();
    }

    // 5. النشر عبر Ajax
    function publishExamNow() {
        const btn = document.getElementById('publishBtn');
        const form = document.getElementById('mainExamForm');
        const formData = new FormData(form);

        if (document.querySelectorAll('.question-card').length === 0) {
            Swal.fire('تنبيه', 'يرجى إضافة سؤال واحد على الأقل قبل النشر.', 'warning');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري النشر...</span>';

        axios.post("{{ route('teacher.exams.store') }}", formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: 'تم نشر التقييم بنجاح',
                text: 'الاختبار أصبح متاحاً للطلاب الآن.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.href = "{{ route('teacher.exams.index') }}";
            });
        })
        .catch(err => {
            Swal.fire('خطأ!', err.response?.data?.message || 'تأكد من إدخال كافة البيانات المطلوبة.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>نشر الاختبار للمساق</span>';
        });
    }
</script>
@endsection
