@extends('layouts.app')

@section('title', 'تعديل الاختبار - ' . $exam->title)

@section('content')
<div class="exam-edit-wrapper">

    {{-- هيدر الصفحة --}}
    <div class="page-header">
        <div class="header-info">
            <nav class="breadcrumb-nav">
                <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> الرئيسية</a>
                <span class="sep"><i class="fa-solid fa-chevron-left"></i></span>
                <a href="{{ route('admin.exams.index') }}">إدارة الاختبارات</a>
                <span class="sep"><i class="fa-solid fa-chevron-left"></i></span>
                <span class="current">تعديل الاختبار</span>
            </nav>
            <h1 class="page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i>
                تعديل الاختبار
                <span class="exam-badge">{{ $exam->title }}</span>
            </h1>
            <p class="page-subtitle">يمكنك تعديل معلومات الاختبار، إضافة أسئلة جديدة أو تعديل وحذف الأسئلة الحالية.</p>
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.exams.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-right"></i> إلغاء
            </a>
            <button type="button" onclick="updateExam({{ $exam->id }})" id="saveBtn" class="btn-primary">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>حفظ التغييرات</span>
            </button>
        </div>
    </div>

    <form id="editExamForm" onsubmit="event.preventDefault();">
        @csrf
        @method('PUT')

        <div class="edit-layout-grid">

            {{-- الجانب الأيمن: البيانات الأساسية للاختبار --}}
            <aside class="sidebar-config">
                <div class="glass-card sticky-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <h3>إعدادات الاختبار</h3>
                    </div>

                    <div class="card-body">
                        <div class="f-group mb-20">
                            <label class="f-label">عنوان الاختبار <span class="req">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-heading icon"></i>
                                <input type="text" name="title" value="{{ $exam->title }}" class="f-input" required placeholder="أدخل عنوان الاختبار">
                            </div>
                        </div>

                        <div class="f-group mb-20">
                            <label class="f-label">المدة الزمنية (بالدقائق) <span class="req">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-regular fa-clock icon"></i>
                                <input type="number" name="duration_minutes" value="{{ $exam->duration_minutes }}" class="f-input" required min="1">
                            </div>
                        </div>

                        <div class="f-group mb-20">
                            <label class="f-label">درجة النجاح</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-award icon"></i>
                                <input type="number" name="pass_marks" value="{{ $exam->pass_marks ?? 50 }}" class="f-input">
                            </div>
                        </div>

                        <div class="exam-stats-info">
                            <div class="stat-item">
                                <span class="stat-label">إجمالي الأسئلة</span>
                                <span class="stat-val" id="questionsCount">{{ count($exam->questions) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- الجانب الأيسر: قائمة الأسئلة والأسئلة الجديدة --}}
            <main class="questions-container">
                <div class="section-title-bar">
                    <h3><i class="fa-solid fa-list-check"></i> أسئلة الاختبار</h3>

                    {{-- أزرار إضافة الأسئلة --}}
                    <div class="add-q-btns">
                        <button type="button" onclick="addQuestion('mcq')" class="btn-add-q mcq">
                            <i class="fa-solid fa-plus"></i> سؤال اختيار من متعدد
                        </button>
                        <button type="button" onclick="addQuestion('text')" class="btn-add-q text">
                            <i class="fa-solid fa-plus"></i> سؤال مقالي/نصي
                        </button>
                    </div>
                </div>

                <div id="q_list">
                    @forelse($exam->questions as $index => $q)
                    <div class="glass-card q-card mb-20">
                        <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $q->id }}">
                        <input type="hidden" name="questions[{{ $index }}][type]" value="{{ $q->type }}">

                        <div class="q-card-header">
                            <span class="q-number"><i class="fa-solid fa-circle-question"></i> سؤال <span class="q-idx">{{ $index + 1 }}</span></span>
                            <div class="q-actions">
                                <span class="type-badge">{{ strtoupper($q->type) }}</span>
                                <button type="button" onclick="removeQuestionCard(this)" class="btn-delete-q" title="حذف السؤال">
                                    <i class="fa-solid fa-trash-can"></i> حذف
                                </button>
                            </div>
                        </div>

                        <div class="q-card-body">
                            <div class="f-group mb-20">
                                <label class="f-label">نص السؤال</label>
                                <textarea name="questions[{{ $index }}][question_text]" class="f-input f-textarea" rows="2" placeholder="اكتب نص السؤال هنا...">{{ $q->question_text }}</textarea>
                            </div>

                            @if($q->type == 'mcq')
                            <div class="mcq-options-grid">
                                <div class="f-group">
                                    <label class="f-label-sm">الخيار (A)</label>
                                    <div class="input-icon-wrapper">
                                        <span class="option-prefix">A</span>
                                        <input type="text" name="questions[{{ $index }}][a]" value="{{ $q->a }}" class="f-input" placeholder="نص الخيار الأول">
                                    </div>
                                </div>

                                <div class="f-group">
                                    <label class="f-label-sm">الخيار (B)</label>
                                    <div class="input-icon-wrapper">
                                        <span class="option-prefix">B</span>
                                        <input type="text" name="questions[{{ $index }}][b]" value="{{ $q->b }}" class="f-input" placeholder="نص الخيار الثاني">
                                    </div>
                                </div>

                                <div class="f-group full-width">
                                    <label class="f-label-sm text-success"><i class="fa-solid fa-circle-check"></i> الإجابة الصحيحة</label>
                                    <div class="select-wrapper">
                                        <select name="questions[{{ $index }}][correct_answer]" class="f-select success-select">
                                            <option value="a" {{ $q->correct_answer == 'a' ? 'selected' : '' }}>الخيار (A)</option>
                                            <option value="b" {{ $q->correct_answer == 'b' ? 'selected' : '' }}>الخيار (B)</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div id="emptyState" class="empty-state glass-card text-center p-40">
                        <i class="fa-solid fa-folder-open empty-icon"></i>
                        <h4>لا توجد أسئلة مضافة لهذا الاختبار حالياً</h4>
                        <p class="text-muted">استخدم الأزرار بالأعلى لإضافة أسئلة جديدة.</p>
                    </div>
                    @endforelse
                </div>
            </main>

        </div>
    </form>
</div>

<style>
    :root {
        --primary-color: #6366f1;
        --primary-hover: #4f46e5;
        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --radius-lg: 18px;
        --radius-md: 12px;
        --shadow-sm: 0 4px 15px -3px rgba(0, 0, 0, 0.04);
        --shadow-hover: 0 10px 25px -5px rgba(99, 102, 241, 0.12);
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
        background: #e0e7ff;
        color: var(--primary-hover);
        padding: 4px 14px;
        border-radius: 20px;
        font-weight: 700;
    }

    .page-subtitle { color: var(--text-muted); font-size: 0.95rem; margin: 0; }

    .header-actions { display: flex; align-items: center; gap: 12px; }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.4);
        transition: all 0.2s ease;
    }

    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -4px rgba(99, 102, 241, 0.5); }

    .btn-secondary {
        background: #ffffff;
        color: var(--text-muted);
        border: 1.5px solid var(--border-color);
        padding: 11px 22px;
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-secondary:hover { background: #f8fafc; color: var(--text-main); border-color: #cbd5e1; }

    /* شبكة الصفحة */
    .edit-layout-grid {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 28px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .edit-layout-grid { grid-template-columns: 1fr; }
    }

    /* الكروت */
    .glass-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: all 0.25s ease;
    }

    .glass-card:hover { box-shadow: var(--shadow-hover); }

    .sticky-card { position: sticky; top: 90px; }

    .card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-icon {
        width: 38px;
        height: 38px;
        background: #e0e7ff;
        color: var(--primary-hover);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .card-header h3 { font-size: 1rem; font-weight: 700; color: var(--text-main); margin: 0; }
    .card-body { padding: 22px; }

    /* الحقول والمدخلات */
    .f-group { display: flex; flex-direction: column; }
    .f-group.mb-20 { margin-bottom: 20px; }
    .f-label { font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 8px; }
    .f-label-sm { font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 6px; }
    .req { color: #ef4444; }

    .input-icon-wrapper { position: relative; display: flex; align-items: center; }
    .input-icon-wrapper .icon {
        position: absolute; right: 14px; color: #94a3b8; font-size: 0.9rem; pointer-events: none;
    }

    .option-prefix {
        position: absolute; right: 14px; font-weight: 800; color: var(--primary-color); font-size: 0.9rem; pointer-events: none;
    }

    .f-input {
        width: 100%;
        padding: 12px 42px 12px 16px;
        border-radius: var(--radius-md);
        border: 1.5px solid var(--border-color);
        background: #f8fafc;
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.9rem;
        font-weight: 500;
        outline: none;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .f-textarea { padding: 12px 16px; resize: vertical; min-height: 80px; }

    .f-input:focus {
        border-color: var(--primary-color);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    /* حالة نجاح الإجابة الصحيحة */
    .select-wrapper { position: relative; display: flex; align-items: center; }
    .f-select {
        width: 100%;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        border: 1.5px solid var(--border-color);
        background: #f8fafc;
        color: var(--text-main);
        font-size: 0.9rem;
        font-weight: 600;
        outline: none;
        appearance: none;
        cursor: pointer;
    }

    .success-select {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }

    .success-select:focus { border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12); }
    .select-arrow { position: absolute; left: 14px; font-size: 0.8rem; color: #94a3b8; pointer-events: none; }

    /* إحصائية الجانب */
    .exam-stats-info {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px dashed var(--border-color);
    }

    .stat-item { display: flex; justify-content: space-between; align-items: center; font-size: 0.88rem; }
    .stat-label { color: var(--text-muted); font-weight: 500; }
    .stat-val { font-weight: 800; color: var(--primary-color); background: #e0e7ff; padding: 2px 10px; border-radius: 12px; }

    /* شريط عنوان قسم الأسئلة وأزرار الإضافة */
    .section-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .section-title-bar h3 {
        font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0;
        display: flex; align-items: center; gap: 8px;
    }

    .add-q-btns { display: flex; gap: 10px; }

    .btn-add-q {
        border: none;
        padding: 9px 16px;
        border-radius: var(--radius-md);
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-add-q.mcq { background: #e0e7ff; color: var(--primary-hover); }
    .btn-add-q.mcq:hover { background: #c7d2fe; }

    .btn-add-q.text { background: #f1f5f9; color: #475569; }
    .btn-add-q.text:hover { background: #e2e8f0; color: var(--text-main); }

    /* بطاقة السؤال */
    .q-card-header {
        padding: 16px 22px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top-left-radius: var(--radius-lg);
        border-top-right-radius: var(--radius-lg);
    }

    .q-number { font-weight: 700; color: var(--text-main); font-size: 0.95rem; }
    .q-actions { display: flex; align-items: center; gap: 12px; }

    .type-badge {
        font-size: 0.72rem; font-weight: 800; background: #cbd5e1; color: #334155; padding: 2px 8px; border-radius: 6px;
    }

    .btn-delete-q {
        background: none; border: none; color: #ef4444; font-weight: 700; font-size: 0.82rem; cursor: pointer; display: flex; align-items: center; gap: 5px; padding: 4px 8px; border-radius: 6px; transition: background 0.2s;
    }

    .btn-delete-q:hover { background: #fee2e2; }

    .q-card-body { padding: 22px; }

    .mcq-options-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 15px; background: #f8fafc; padding: 18px; border-radius: var(--radius-md); border: 1px solid #f1f5f9;
    }

    .full-width { grid-column: span 2; }

    @media (max-width: 640px) {
        .mcq-options-grid { grid-template-columns: 1fr; }
        .full-width { grid-column: span 1; }
    }

    .empty-state { padding: 40px; }
    .empty-icon { font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; }

    .text-success { color: #059669; }
    .mb-20 { margin-bottom: 20px; }
    .p-40 { padding: 40px; }
    .text-center { text-align: center; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    // الحصول على أحدث فهارس الأسئلة
    let qIndex = {{ count($exam->questions) }};

    function updateQuestionsUI() {
        const qCards = document.querySelectorAll('.q-card');
        const countElem = document.getElementById('questionsCount');
        const emptyState = document.getElementById('emptyState');

        if (countElem) {
            countElem.innerText = qCards.length;
        }

        if (emptyState) {
            emptyState.style.display = qCards.length === 0 ? 'block' : 'none';
        }

        // إكاد أرقام الترتيب
        qCards.forEach((card, idx) => {
            const idxSpan = card.querySelector('.q-idx');
            if(idxSpan) idxSpan.innerText = idx + 1;
        });
    }

    function addQuestion(type) {
        const container = document.getElementById('q_list');
        const emptyState = document.getElementById('emptyState');
        if (emptyState) emptyState.style.display = 'none';

        let newCard = document.createElement('div');
        newCard.className = 'glass-card q-card mb-20';

        let mcqHtml = '';
        if (type === 'mcq') {
            mcqHtml = `
            <div class="mcq-options-grid">
                <div class="f-group">
                    <label class="f-label-sm">الخيار (A)</label>
                    <div class="input-icon-wrapper">
                        <span class="option-prefix">A</span>
                        <input type="text" name="questions[${qIndex}][a]" class="f-input" placeholder="نص الخيار الأول">
                    </div>
                </div>
                <div class="f-group">
                    <label class="f-label-sm">الخيار (B)</label>
                    <div class="input-icon-wrapper">
                        <span class="option-prefix">B</span>
                        <input type="text" name="questions[${qIndex}][b]" class="f-input" placeholder="نص الخيار الثاني">
                    </div>
                </div>
                <div class="f-group full-width">
                    <label class="f-label-sm text-success"><i class="fa-solid fa-circle-check"></i> الإجابة الصحيحة</label>
                    <div class="select-wrapper">
                        <select name="questions[${qIndex}][correct_answer]" class="f-select success-select">
                            <option value="a">الخيار (A)</option>
                            <option value="b">الخيار (B)</option>
                        </select>
                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                    </div>
                </div>
            </div>`;
        }

        newCard.innerHTML = `
            <input type="hidden" name="questions[${qIndex}][type]" value="${type}">
            <div class="q-card-header">
                <span class="q-number"><i class="fa-solid fa-circle-question"></i> سؤال <span class="q-idx"></span></span>
                <div class="q-actions">
                    <span class="type-badge">${type.toUpperCase()}</span>
                    <button type="button" onclick="removeQuestionCard(this)" class="btn-delete-q" title="حذف السؤال">
                        <i class="fa-solid fa-trash-can"></i> حذف
                    </button>
                </div>
            </div>
            <div class="q-card-body">
                <div class="f-group mb-20">
                    <label class="f-label">نص السؤال</label>
                    <textarea name="questions[${qIndex}][question_text]" class="f-input f-textarea" rows="2" placeholder="اكتب نص السؤال الجديد هنا..."></textarea>
                </div>
                ${mcqHtml}
            </div>
        `;

        container.appendChild(newCard);
        qIndex++;
        updateQuestionsUI();

        // التمرير السلس نحو السؤال المضاف
        newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function removeQuestionCard(btn) {
        Swal.fire({
            title: 'هل أنت تأكد؟',
            text: "سيتم إزالة هذا السؤال من الواجهة عند الحفظ!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، احذفه',
            cancelButtonText: 'تراجع'
        }).then((result) => {
            if (result.isConfirmed) {
                const card = btn.closest('.q-card');
                card.remove();
                updateQuestionsUI();
            }
        });
    }

    function updateExam(id) {
        const btn = document.getElementById('saveBtn');
        const form = document.getElementById('editExamForm');

        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري الحفظ...</span>';

        axios.post(`/admin/exams/${id}`, formData)
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: 'تم تحديث بيانات الاختبار والأسئلة.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.href = "{{ route('admin.exams.index') }}";
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في العملية!',
                    text: err.response?.data?.message || 'يرجى التأكد من ملء كافة الحقول بشكل صحيح.',
                });
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> <span>حفظ التغييرات</span>';
            });
    }
</script>
@endsection
