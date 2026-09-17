@extends('layouts.app')

@section('title', __('Smart Revision Schedule Generator') . ' | ' . config('app.name'))

@section('content')
<div class="ed-planner-container">

    <!-- Header -->
    <header class="ed-pl-header">
        <div class="ed-pl-title-box">
            <div class="ed-pl-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('student.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Student Portal') }}</a>
                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} divider"></i>
                <span class="active">{{ __('Smart Revision Schedule Generator') }}</span>
            </div>
            <h1>{{ __('Tawjihi Smart Revision Schedule & Plan Generator') }}</h1>
            <p>{{ __('Set your ministerial exam start date and daily study capacity, and the system will intelligently distribute revision hours based on subject complexity.') }}</p>
        </div>

        <div class="ed-pl-badge">
            <i class="fas fa-calendar-alt"></i>
            <span>{{ __('Time Management & Exam Readiness') }} 🇵🇸</span>
        </div>
    </header>

    <div class="ed-pl-grid">
        
        <!-- بطاقة ضبط معايير الخطة -->
        <div class="ed-card ed-pl-form-card">
            <div class="ed-pl-card-title">
                <i class="fas fa-sliders-h"></i>
                <span>{{ __('Plan Parameters & Preferences') }}</span>
            </div>

            <div class="ed-pl-field">
                <label for="examDate">{{ __('First Ministerial Exam Date:') }}</label>
                <input type="date" id="examDate" class="ed-pl-input" value="{{ now()->addDays(45)->format('Y-m-d') }}">
            </div>

            <div class="ed-pl-field">
                <div class="ed-pl-field-flex">
                    <label for="dailyHours">{{ __('Available Daily Study Hours:') }}</label>
                    <strong id="hoursVal" class="ed-pl-highlight">6 {{ __('hours') }}</strong>
                </div>
                <input 
                    type="range" 
                    id="dailyHours" 
                    min="3" 
                    max="12" 
                    value="6" 
                    step="0.5" 
                    class="ed-pl-range"
                    oninput="document.getElementById('hoursVal').textContent = this.value + ' ' + '{{ __('hours') }}'"
                >
            </div>

            <div class="ed-pl-field">
                <label>{{ __('Plan Subjects & Perceived Difficulty:') }}</label>
                <div class="ed-pl-subjects-list" id="subjectsList">
                    
                    @php
                        $sampleSubjects = [
                            ['name' => __('Mathematics'), 'diff' => 'hard'],
                            ['name' => __('Physics'), 'diff' => 'hard'],
                            ['name' => __('Arabic Language'), 'diff' => 'medium'],
                            ['name' => __('English Language'), 'diff' => 'medium'],
                            ['name' => __('Chemistry / Biology'), 'diff' => 'medium'],
                            ['name' => __('Islamic Studies'), 'diff' => 'easy'],
                        ];
                    @endphp

                    @foreach($sampleSubjects as $sub)
                    <div class="ed-pl-sub-row">
                        <label class="ed-pl-check-label">
                            <input type="checkbox" checked class="sub-check" value="{{ $sub['name'] }}">
                            <span>{{ $sub['name'] }}</span>
                        </label>
                        <select class="ed-pl-select sub-diff">
                            <option value="hard" {{ $sub['diff'] === 'hard' ? 'selected' : '' }}>{{ __('Difficult (Intensive Focus)') }}</option>
                            <option value="medium" {{ $sub['diff'] === 'medium' ? 'selected' : '' }}>{{ __('Moderate') }}</option>
                            <option value="easy" {{ $sub['diff'] === 'easy' ? 'selected' : '' }}>{{ __('Easy / Quick Review') }}</option>
                        </select>
                    </div>
                    @endforeach

                </div>
            </div>

            <button type="button" class="ed-btn ed-btn-primary" style="width: 100%; justify-content: center; padding: 13px;" onclick="generatePlan()">
                <i class="fas fa-magic"></i>
                <span>{{ __('Generate & Update Revision Plan') }}</span>
            </button>
        </div>

        <!-- بطاقة نتيجة الخطة والجدول الزمني -->
        <div class="ed-card ed-pl-result-card" id="planPrintCard">
            <div class="ed-pl-card-title">
                <i class="fas fa-calendar-check"></i>
                <span>{{ __('Suggested Revision Schedule') }}</span>
            </div>

            <div class="ed-pl-stats-row">
                <div class="ed-pl-mini-stat">
                    <span class="lbl">{{ __('Remaining Days') }}</span>
                    <strong class="val" id="resDays">0</strong>
                </div>
                <div class="ed-pl-mini-stat">
                    <span class="lbl">{{ __('Total Study Hours') }}</span>
                    <strong class="val" id="resTotalHours" style="color: #1e3a8a;">0</strong>
                </div>
                <div class="ed-pl-mini-stat">
                    <span class="lbl">{{ __('Scheduled Subjects') }}</span>
                    <strong class="val" id="resSubCount">0</strong>
                </div>
            </div>

            <div class="ed-pl-schedule-container" id="scheduleContainer">
                <!-- سيتم ملؤه بواسطة JavaScript -->
            </div>

            <button type="button" class="ed-btn ed-btn-outline" style="width: 100%; justify-content: center; margin-top: 20px;" onclick="window.print()">
                <i class="fas fa-print"></i>
                <span>{{ __('Print or Save as PDF') }}</span>
            </button>
        </div>

    </div>

</div>

<style>
    .ed-planner-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 0 0 60px;
        box-sizing: border-box;
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
        color: #1e3a8a;
        font-weight: 600;
    }

    .ed-pl-title-box h1 {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .ed-pl-title-box p {
        font-size: 0.88rem;
        color: #64748b;
        margin: 0;
        max-width: 780px;
        line-height: 1.6;
    }

    .ed-pl-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #1e3a8a;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    /* Grid Layout */
    .ed-pl-grid {
        display: grid;
        grid-template-columns: 1fr 1.35fr;
        gap: 24px;
        align-items: start;
    }

    /* Cards */
    .ed-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    .ed-pl-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-pl-card-title i {
        color: #1e3a8a;
        font-size: 1.15rem;
    }

    /* Form Fields */
    .ed-pl-field {
        margin-bottom: 20px;
    }

    .ed-pl-field label {
        display: block;
        font-size: 0.84rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .ed-pl-field-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .ed-pl-field-flex label {
        margin-bottom: 0;
    }

    .ed-pl-highlight {
        color: #1e3a8a;
        font-size: 0.95rem;
        font-weight: 800;
    }

    .ed-pl-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-size: 0.88rem;
        color: #0f172a;
        background: #f8fafc;
        box-sizing: border-box;
    }

    .ed-pl-input:focus {
        outline: none;
        border-color: #1e3a8a;
        background: #ffffff;
    }

    .ed-pl-range {
        width: 100%;
        accent-color: #1e3a8a;
        cursor: pointer;
    }

    /* Subjects List */
    .ed-pl-subjects-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px;
        max-height: 280px;
        overflow-y: auto;
    }

    .ed-pl-sub-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        gap: 10px;
    }

    .ed-pl-check-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.84rem;
        font-weight: 700;
        color: #1e293b;
        cursor: pointer;
        margin: 0;
    }

    .ed-pl-select {
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.78rem;
        color: #475569;
        background: #f8fafc;
        cursor: pointer;
    }

    /* Buttons */
    .ed-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .ed-btn-primary {
        background: #1e3a8a;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25);
    }

    .ed-btn-primary:hover {
        background: #172554;
    }

    .ed-btn-outline {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 10px 16px;
    }

    .ed-btn-outline:hover {
        border-color: #1e3a8a;
        color: #1e3a8a;
    }

    /* Stats Row */
    .ed-pl-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .ed-pl-mini-stat {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
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
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
    }

    /* Schedule Items */
    .ed-pl-schedule-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ed-schedule-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        transition: all 0.2s;
    }

    .ed-schedule-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .ed-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .ed-badge-red { background: #fee2e2; color: #dc2626; }
    .ed-badge-amber { background: #fef3c7; color: #d97706; }
    .ed-badge-emerald { background: #d1fae5; color: #059669; }

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
        color: #1e3a8a;
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
        document.getElementById('resTotalHours').textContent = totalHoursAvailable + ' ' + '{{ __("hours") }}';
        document.getElementById('resSubCount').textContent = selectedSubjects.length;

        const container = document.getElementById('scheduleContainer');
        container.innerHTML = '';

        if (selectedSubjects.length === 0) {
            container.innerHTML = '<div style="text-align: center; color: #94a3b8; padding: 20px;">{{ __("Please select at least one subject.") }}</div>';
            return;
        }

        selectedSubjects.forEach(s => {
            const subHours = Math.round((s.weight / totalWeights) * totalHoursAvailable);
            const subDays = Math.max(1, Math.round(subHours / dailyHours));

            let diffBadge = '<span class="ed-badge ed-badge-amber">{{ __("Moderate") }}</span>';
            if (s.diff === 'hard') diffBadge = '<span class="ed-badge ed-badge-red">{{ __("Intensive") }}</span>';
            if (s.diff === 'easy') diffBadge = '<span class="ed-badge ed-badge-emerald">{{ __("Quick Review") }}</span>';

            const item = document.createElement('div');
            item.className = 'ed-schedule-item';
            item.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    ${diffBadge}
                    <span class="ed-sch-name">${s.name}</span>
                </div>
                <div class="ed-sch-meta">
                    <span class="ed-sch-hours">{{ __("Allocated:") }} <strong>${subHours} {{ __("hours") }}</strong> (${subDays} {{ __("days") }})</span>
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
