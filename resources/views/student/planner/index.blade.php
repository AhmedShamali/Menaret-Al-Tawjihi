@extends('layouts.app')

@section('title', 'مولّد جدول المراجعة الذكي للامتحانات | توجيهي فلسطين')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #2563eb;
        --emerald: #10b981;
        --amber: #f59e0b;
        --rose: #f43f5e;
        --bg-main: #f8fafc;
        --card-bg: #ffffff;
        --border-card: #e2e8f0;
        --text-title: #0f172a;
        --text-body: #334155;
        --text-muted: #64748b;
    }

    * { font-family: 'Alexandria', sans-serif; }

    .planner-wrapper {
        direction: rtl; max-width: 1100px; margin: 0 auto; padding: 20px 20px 60px;
    }

    .header-banner { text-align: center; margin-bottom: 35px; }
    .badge-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 16px; background: #ecfdf5; border: 1px solid #a7f3d0;
        color: var(--emerald); border-radius: 50px; font-size: 0.82rem; font-weight: 700;
        margin-bottom: 12px;
    }
    .header-banner h1 { font-size: 2.1rem; font-weight: 800; color: var(--text-title); margin-bottom: 8px; }
    .header-banner p { color: var(--text-muted); font-size: 1rem; max-width: 650px; margin: 0 auto; }

    .planner-grid {
        display: grid; grid-template-columns: 1fr 1.1fr; gap: 30px; align-items: start;
    }

    .form-card {
        background: white; border: 1px solid var(--border-card); border-radius: 24px;
        padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }
    .form-card h3 {
        font-size: 1.15rem; font-weight: 800; color: var(--text-title); margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px;
    }

    .field-group { margin-bottom: 20px; }
    .field-group label {
        display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-title); margin-bottom: 8px;
    }
    .input-box {
        width: 100%; padding: 12px 14px; border: 1.5px solid var(--border-card);
        border-radius: 12px; font-size: 0.95rem; font-family: inherit; outline: none; transition: 0.2s;
    }
    .input-box:focus { border-color: var(--primary); }

    /* Subjects List in Form */
    .subjects-list {
        display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;
    }
    .subject-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; background: #f8fafc; border: 1px solid var(--border-card);
        border-radius: 12px;
    }
    .subject-name-col { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 0.9rem; }
    .diff-select {
        padding: 6px 10px; border-radius: 8px; border: 1px solid var(--border-card);
        font-size: 0.8rem; font-weight: 700; font-family: inherit; outline: none;
    }

    .btn-generate {
        width: 100%; padding: 14px; border: none; border-radius: 12px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white;
        font-size: 1rem; font-weight: 800; cursor: pointer; transition: 0.2s;
        box-shadow: 0 4px 15px rgba(37,99,235,0.3); display: flex; align-items: center;
        justify-content: center; gap: 8px;
    }
    .btn-generate:hover { filter: brightness(1.1); transform: translateY(-2px); }

    /* Result Plan Card */
    .plan-card {
        background: white; border: 1px solid var(--border-card); border-radius: 24px;
        padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }
    .plan-stats-bar {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 24px;
    }
    .stat-box {
        background: #f8fafc; border: 1px solid var(--border-card); border-radius: 14px;
        padding: 12px; text-align: center;
    }
    .stat-box h4 { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px; }
    .stat-box p { font-size: 1.25rem; font-weight: 800; color: var(--primary); margin: 0; }

    .schedule-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px; border: 1px solid var(--border-card); border-radius: 14px;
        margin-bottom: 10px; background: white; transition: 0.2s;
    }
    .schedule-item:hover { border-color: #93c5fd; }
    .sch-name { font-weight: 800; font-size: 0.95rem; color: var(--text-title); }
    .sch-tag {
        font-size: 0.75rem; padding: 2px 8px; border-radius: 6px; font-weight: 700;
    }
    .tag-hard { background: #fee2e2; color: #dc2626; }
    .tag-medium { background: #fef3c7; color: #b45309; }
    .tag-easy { background: #dcfce7; color: #15803d; }

    .btn-print-plan {
        margin-top: 20px; width: 100%; padding: 12px; border-radius: 12px;
        background: #f8fafc; border: 1.5px solid var(--border-card); color: var(--text-title);
        font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-print-plan:hover { background: #f1f5f9; }

    @media print {
        body * { visibility: hidden; }
        .plan-card, .plan-card * { visibility: visible; }
        .plan-card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; }
        .btn-print-plan { display: none; }
    }

    @media (max-width: 850px) {
        .planner-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="planner-wrapper">
    <!-- Header -->
    <div class="header-banner">
        <div class="badge-pill">
            <i class="fa-solid fa-calendar-check"></i>
            <span>تنظيم الوقت لليالي الامتحانات النهائية</span>
        </div>
        <h1>مولّد جدول المراجعة الذكي لطلبة التوجيهي</h1>
        <p>حدد موعد بدء امتحاناتك الوزارية وساعات دراستك اليومية، وسيقوم النظام بتوزيع الوقت بعدالة بحسب درجة صعوبة كل مادة.</p>
    </div>

    <div class="planner-grid">
        <!-- Settings Form -->
        <div class="form-card">
            <h3><i class="fa-solid fa-sliders" style="color: var(--primary);"></i> إعدادات خطة المراجعة</h3>

            <div class="field-group">
                <label for="examDate">موعد أول امتحان وزاري</label>
                <input type="date" id="examDate" class="input-box" value="{{ now()->addDays(45)->format('Y-m-d') }}">
            </div>

            <div class="field-group">
                <label for="dailyHours">ساعات الدراسة الفعلية المتاحة يومياً: <span id="hoursVal" style="color: var(--primary);">6 ساعات</span></label>
                <input type="range" id="dailyHours" min="3" max="12" value="6" step="0.5" style="width: 100%; accent-color: var(--primary);" oninput="document.getElementById('hoursVal').textContent = this.value + ' ساعات'">
            </div>

            <div class="field-group">
                <label>مواد الخطة ودرجة صعوبتها بالنسبة لك:</label>
                <div class="subjects-list" id="subjectsList">
                    <div class="subject-row">
                        <div class="subject-name-col">
                            <input type="checkbox" checked class="sub-check" value="الرياضيات">
                            <span>الرياضيات</span>
                        </div>
                        <select class="diff-select sub-diff">
                            <option value="hard" selected>صعبة (تركيز مكثف)</option>
                            <option value="medium">متوسطة</option>
                            <option value="easy">سهلة / مراجعة سريعة</option>
                        </select>
                    </div>

                    <div class="subject-row">
                        <div class="subject-name-col">
                            <input type="checkbox" checked class="sub-check" value="الفيزياء">
                            <span>الفيزياء</span>
                        </div>
                        <select class="diff-select sub-diff">
                            <option value="hard" selected>صعبة (تركيز مكثف)</option>
                            <option value="medium">متوسطة</option>
                            <option value="easy">سهلة</option>
                        </select>
                    </div>

                    <div class="subject-row">
                        <div class="subject-name-col">
                            <input type="checkbox" checked class="sub-check" value="اللغة العربية">
                            <span>اللغة العربية</span>
                        </div>
                        <select class="diff-select sub-diff">
                            <option value="hard">صعبة</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="easy">سهلة</option>
                        </select>
                    </div>

                    <div class="subject-row">
                        <div class="subject-name-col">
                            <input type="checkbox" checked class="sub-check" value="اللغة الإنجليزية">
                            <span>اللغة الإنجليزية</span>
                        </div>
                        <select class="diff-select sub-diff">
                            <option value="hard">صعبة</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="easy">سهلة</option>
                        </select>
                    </div>

                    <div class="subject-row">
                        <div class="subject-name-col">
                            <input type="checkbox" checked class="sub-check" value="الكيمياء / العلوم الحياتية">
                            <span>الكيمياء / العلوم الحياتية</span>
                        </div>
                        <select class="diff-select sub-diff">
                            <option value="hard">صعبة</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="easy">سهلة</option>
                        </select>
                    </div>

                    <div class="subject-row">
                        <div class="subject-name-col">
                            <input type="checkbox" checked class="sub-check" value="التربية الإسلامية">
                            <span>التربية الإسلامية</span>
                        </div>
                        <select class="diff-select sub-diff">
                            <option value="hard">صعبة</option>
                            <option value="medium">متوسطة</option>
                            <option value="easy" selected>سهلة (مراجعة سريعة)</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="button" class="btn-generate" onclick="generatePlan()">
                <i class="fa-solid fa-wand-magic-sparkles"></i> توليد جدول المراجعة فورياً
            </button>
        </div>

        <!-- Generated Plan Result -->
        <div class="plan-card">
            <h3><i class="fa-solid fa-chart-pie" style="color: var(--emerald);"></i> خطتك الزمنية المقترحة</h3>

            <div class="plan-stats-bar">
                <div class="stat-box">
                    <h4>الأيام المتبقية</h4>
                    <p id="resDays">45 يوم</p>
                </div>
                <div class="stat-box">
                    <h4>إجمالي الساعات</h4>
                    <p id="resTotalHours">270 ساعة</p>
                </div>
                <div class="stat-box">
                    <h4>جلسات بومودورو</h4>
                    <p id="resSessions">330 جلسة</p>
                </div>
            </div>

            <div id="scheduleResultsList">
                <!-- Dynamically Rendered -->
            </div>

            <button type="button" class="btn-print-plan" onclick="window.print()">
                <i class="fa-solid fa-print"></i> طباعة الجدول أو حفظه كـ PDF
            </button>
        </div>
    </div>
</div>

<script>
function generatePlan() {
    const examDate = new Date(document.getElementById('examDate').value);
    const today = new Date();
    const diffTime = Math.max(1, examDate - today);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    const dailyHours = parseFloat(document.getElementById('dailyHours').value);
    const totalHours = diffDays * dailyHours;

    const rows = document.querySelectorAll('.subject-row');
    let subjects = [];
    let totalWeight = 0;

    rows.forEach(row => {
        const check = row.querySelector('.sub-check');
        if (check.checked) {
            const diff = row.querySelector('.sub-diff').value;
            const weight = diff === 'hard' ? 3 : (diff === 'medium' ? 2 : 1);
            totalWeight += weight;
            subjects.push({
                name: check.value,
                diff: diff,
                weight: weight
            });
        }
    });

    if (subjects.length === 0) {
        alert('يرجى تحديد مادة واحدة على الأقل');
        return;
    }

    document.getElementById('resDays').textContent = diffDays + ' يوم';
    document.getElementById('resTotalHours').textContent = Math.round(totalHours) + ' ساعة';
    document.getElementById('resSessions').textContent = Math.round(totalHours / 0.8) + ' جلسة';

    let html = '';
    subjects.forEach(sub => {
        const subHours = Math.round((sub.weight / totalWeight) * totalHours);
        const dailyMins = Math.round((subHours / diffDays) * 60);
        const tagClass = sub.diff === 'hard' ? 'tag-hard' : (sub.diff === 'medium' ? 'tag-medium' : 'tag-easy');
        const tagText = sub.diff === 'hard' ? 'أولوية قصوى' : (sub.diff === 'medium' ? 'أولوية متوسطة' : 'مراجعة خفيفة');

        html += `
            <div class="schedule-item">
                <div>
                    <div class="sch-name">${sub.name}</div>
                    <span class="sch-tag ${tagClass}">${tagText}</span>
                </div>
                <div style="text-align: left;">
                    <div style="font-size: 1.05rem; font-weight: 800; color: var(--primary);">${subHours} ساعة</div>
                    <div style="font-size: 0.78rem; color: var(--text-muted);">${dailyMins} دقيقة / يومياً</div>
                </div>
            </div>
        `;
    });

    document.getElementById('scheduleResultsList').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', () => {
    generatePlan();
});
</script>
@endsection
