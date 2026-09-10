@extends('layouts.app')

@section('title', 'مولّد جدول المراجعة الذكي للامتحانات | منارة التوجيهي')

@section('content')
<div class="ed-planner-container">

    <!-- Header -->
    <header class="ed-pl-header">
        <div class="ed-pl-title-box">
            <div class="ed-pl-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('student.dashboard') }}" style="color: inherit; text-decoration: none;">لوحة الطالب</a>
                <i class="fas fa-chevron-left divider"></i>
                <span class="active">مولّد جدول المراجعة الذكي</span>
            </div>
            <h1>مولّد خطة وجدول المراجعة الذكي للتوجيهي</h1>
            <p>حدد تاريخ انطلاق الامتحانات الوزارية وساعات دراستك اليومية، وسيقوم النظام بتوزيع الوقت بدقة بحسب درجة صعوبة كل مادة.</p>
        </div>

        <div class="ed-pl-badge">
            <i class="fas fa-calendar-alt"></i>
            <span>تنظيم الوقت قبل الامتحانات الوزارية 🇵🇸</span>
        </div>
    </header>

    <div class="ed-pl-grid">
        
        <!-- بطاقة ضبط معايير الخطة -->
        <div class="ed-card ed-pl-form-card">
            <div class="ed-pl-card-title">
                <i class="fas fa-sliders-h"></i>
                <span>إعدادات وتفضيلات الخطة</span>
            </div>

            <div class="ed-pl-field">
                <label for="examDate">موعد أول امتحان وزاري:</label>
                <input type="date" id="examDate" class="ed-pl-input" value="{{ now()->addDays(45)->format('Y-m-d') }}">
            </div>

            <div class="ed-pl-field">
                <div class="ed-pl-field-flex">
                    <label for="dailyHours">ساعات الدراسة الفعلية المتاحة يومياً:</label>
                    <strong id="hoursVal" class="ed-pl-highlight">6 ساعات</strong>
                </div>
                <input 
                    type="range" 
                    id="dailyHours" 
                    min="3" 
                    max="12" 
                    value="6" 
                    step="0.5" 
                    class="ed-pl-range"
                    oninput="document.getElementById('hoursVal').textContent = this.value + ' ساعات'"
                >
            </div>

            <div class="ed-pl-field">
                <label>مواد الخطة ودرجة صعوبتها بالنسبة لك:</label>
                <div class="ed-pl-subjects-list" id="subjectsList">
                    
                    <div class="ed-pl-sub-row">
                        <label class="ed-pl-check-label">
                            <input type="checkbox" checked class="sub-check" value="الرياضيات">
                            <span>الرياضيات</span>
                        </label>
                        <select class="ed-pl-select sub-diff">
                            <option value="hard" selected>صعبة (تركيز مكثف)</option>
                            <option value="medium">متوسطة</option>
                            <option value="easy">سهلة / مراجعة سريعة</option>
                        </select>
                    </div>

                    <div class="ed-pl-sub-row">
                        <label class="ed-pl-check-label">
                            <input type="checkbox" checked class="sub-check" value="الفيزياء">
                            <span>الفيزياء</span>
                        </label>
                        <select class="ed-pl-select sub-diff">
                            <option value="hard" selected>صعبة (تركيز مكثف)</option>
                            <option value="medium">متوسطة</option>
                            <option value="easy">سهلة</option>
                        </select>
                    </div>

                    <div class="ed-pl-sub-row">
                        <label class="ed-pl-check-label">
                            <input type="checkbox" checked class="sub-check" value="اللغة العربية">
                            <span>اللغة العربية</span>
                        </label>
                        <select class="ed-pl-select sub-diff">
                            <option value="hard">صعبة</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="easy">سهلة</option>
                        </select>
                    </div>

                    <div class="ed-pl-sub-row">
                        <label class="ed-pl-check-label">
                            <input type="checkbox" checked class="sub-check" value="اللغة الإنجليزية">
                            <span>اللغة الإنجليزية</span>
                        </label>
                        <select class="ed-pl-select sub-diff">
                            <option value="hard">صعبة</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="easy">سهلة</option>
                        </select>
                    </div>

                    <div class="ed-pl-sub-row">
                        <label class="ed-pl-check-label">
                            <input type="checkbox" checked class="sub-check" value="الكيمياء / العلوم الحياتية">
                            <span>الكيمياء / العلوم الحياتية</span>
                        </label>
                        <select class="ed-pl-select sub-diff">
                            <option value="hard">صعبة</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="easy">سهلة</option>
                        </select>
                    </div>

                    <div class="ed-pl-sub-row">
                        <label class="ed-pl-check-label">
                            <input type="checkbox" checked class="sub-check" value="التربية الإسلامية">
                            <span>التربية الإسلامية</span>
                        </label>
                        <select class="ed-pl-select sub-diff">
                            <option value="hard">صعبة</option>
                            <option value="medium">متوسطة</option>
                            <option value="easy" selected>سهلة (مراجعة سريعة)</option>
                        </select>
                    </div>

                </div>
            </div>

            <button type="button" class="ed-btn ed-btn-primary" style="width: 100%; justify-content: center; padding: 13px;" onclick="generatePlan()">
                <i class="fas fa-magic"></i>
                <span>توليد وتحديث جدول المراجعة فورياً</span>
            </button>
        </div>

        <!-- بطاقة نتيجة الخطة والجدول الزمني -->
        <div class="ed-card ed-pl-result-card" id="planPrintCard">
            <div class="ed-pl-card-title">
                <i class="fas fa-calendar-check"></i>
                <span>الجدول الزمني المقترح للمراجعة</span>
            </div>

            <div class="ed-pl-stats-row">
                <div class="ed-pl-mini-stat">
                    <span class="lbl">الأيام المتبقية</span>
                    <strong class="val" id="resDays">0</strong>
                </div>
                <div class="ed-pl-mini-stat">
                    <span class="lbl">إجمالي ساعات الدراسة</span>
                    <strong class="val" id="resTotalHours" style="color: #1d4ed8;">0</strong>
                </div>
                <div class="ed-pl-mini-stat">
                    <span class="lbl">عدد المواد المجدولة</span>
                    <strong class="val" id="resSubCount">0</strong>
                </div>
            </div>

            <div class="ed-pl-schedule-container" id="scheduleContainer">
                <!-- سيتم ملؤه بواسطة JavaScript -->
            </div>

            <button type="button" class="ed-btn ed-btn-outline" style="width: 100%; justify-content: center; margin-top: 20px;" onclick="window.print()">
                <i class="fas fa-print"></i>
                <span>طباعة أو حفظ الجدول كملف PDF</span>
            </button>
        </div>

    </div>

</div>

<style>
    .ed-planner-container {
        padding: 24px 32px 60px;
        direction: rtl;
        font-family: 'Alexandria', 'Tajawal', sans-serif;
    }

    /* Header */
    .ed-pl-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .ed-pl-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 8px;
    }

    .ed-pl-breadcrumbs .divider {
        font-size: 0.65rem;
        color: #cbd5e1;
    }

    .ed-pl-breadcrumbs .active {
        color: #1d4ed8;
        font-weight: 600;
    }

    .ed-pl-title-box h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .ed-pl-title-box p {
        font-size: 0.9rem;
        color: #64748b;
        margin: 0;
    }

    .ed-pl-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #ecfdf5;
        border: 1px solid #d1fae5;
        color: #065f46;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 700;
    }

    /* Grid */
    .ed-pl-grid {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 24px;
        align-items: start;
    }

    .ed-pl-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-pl-card-title i {
        color: #1d4ed8;
    }

    .ed-pl-field {
        margin-bottom: 20px;
    }

    .ed-pl-field label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .ed-pl-field-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .ed-pl-highlight {
        color: #1d4ed8;
        font-size: 0.92rem;
    }

    .ed-pl-input {
        width: 100%;
        padding: 11px 14px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-family: inherit;
        font-size: 0.9rem;
        color: #0f172a;
        outline: none;
    }

    .ed-pl-range {
        width: 100%;
        accent-color: #1d4ed8;
        cursor: pointer;
    }

    .ed-pl-subjects-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ed-pl-sub-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .ed-pl-check-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        cursor: pointer;
    }

    .ed-pl-check-label input {
        accent-color: #1d4ed8;
        width: 16px;
        height: 16px;
    }

    .ed-pl-select {
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        font-family: inherit;
        font-size: 0.8rem;
        font-weight: 600;
        color: #334155;
        outline: none;
    }

    /* Result Stats */
    .ed-pl-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 22px;
    }

    .ed-pl-mini-stat {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 14px 10px;
        text-align: center;
    }

    .ed-pl-mini-stat .lbl {
        display: block;
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .ed-pl-mini-stat .val {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-pl-schedule-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ed-schedule-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        gap: 12px;
    }

    .ed-sch-name {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-sch-meta {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ed-sch-hours {
        font-size: 0.85rem;
        color: #475569;
        font-weight: 600;
    }

    .ed-sch-hours strong {
        color: #1d4ed8;
    }

    /* Print styling */
    @media print {
        body * { visibility: hidden; }
        #planPrintCard, #planPrintCard * { visibility: visible; }
        #planPrintCard {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
            box-shadow: none;
        }
        .ed-btn { display: none !important; }
    }

    /* Responsive */
    @media (max-width: 860px) {
        .ed-pl-grid {
            grid-template-columns: 1fr;
        }
        .ed-planner-container {
            padding: 18px 16px 60px;
        }
    }
</style>

<script>
    function generatePlan() {
        const examDateVal = document.getElementById('examDate').value;
        if (!examDateVal) return;

        const targetDate = new Date(examDateVal);
        const today = new Date();
        const diffTime = targetDate - today;
        const diffDays = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));

        const dailyHours = parseFloat(document.getElementById('dailyHours').value) || 6;
        const totalHoursAvailable = Math.round(diffDays * dailyHours);

        // جمع المواد المختارة
        const rows = document.querySelectorAll('.ed-pl-sub-row');
        let selectedSubjects = [];
        let totalWeights = 0;

        rows.forEach(r => {
            const check = r.querySelector('.sub-check');
            if (check.checked) {
                const diff = r.querySelector('.sub-diff').value;
                let weight = 2; // medium
                if (diff === 'hard') weight = 3;
                if (diff === 'easy') weight = 1;

                selectedSubjects.push({
                    name: check.value,
                    diff: diff,
                    weight: weight
                });
                totalWeights += weight;
            }
        });

        document.getElementById('resDays').textContent = diffDays;
        document.getElementById('resTotalHours').textContent = totalHoursAvailable + ' س';
        document.getElementById('resSubCount').textContent = selectedSubjects.length;

        const container = document.getElementById('scheduleContainer');
        container.innerHTML = '';

        if (selectedSubjects.length === 0) {
            container.innerHTML = '<div style="text-align: center; color: #94a3b8; padding: 20px;">يرجى اختيار مادة واحدة على الأقل.</div>';
            return;
        }

        selectedSubjects.forEach(s => {
            const subHours = Math.round((s.weight / totalWeights) * totalHoursAvailable);
            const subDays = Math.max(1, Math.round(subHours / dailyHours));

            let diffBadge = '<span class="ed-badge ed-badge-amber">متوسطة</span>';
            if (s.diff === 'hard') diffBadge = '<span class="ed-badge ed-badge-red">مكثفة</span>';
            if (s.diff === 'easy') diffBadge = '<span class="ed-badge ed-badge-emerald">مراجعة سريعة</span>';

            const item = document.createElement('div');
            item.className = 'ed-schedule-item';
            item.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    ${diffBadge}
                    <span class="ed-sch-name">${s.name}</span>
                </div>
                <div class="ed-sch-meta">
                    <span class="ed-sch-hours">مخصص لها: <strong>${subHours} ساعة</strong> (${subDays} أيام)</span>
                </div>
            `;
            container.appendChild(item);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        generatePlan();
    });
</script>
@endsection
