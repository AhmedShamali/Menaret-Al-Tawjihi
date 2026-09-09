@extends('layouts.app')

@section('title', 'إدارة واعتماد شهادات ونتائج نهاية العام | منارة التوجيهي')

@section('content')
<!-- استدعاء خطوط وأيقونات أكاديمية -->
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Amiri:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #4f46e5;
        --primary-dark: #3730a3;
        --gold-cert: #d97706;
        --success: #10b981;
        --danger: #ef4444;
        --bg-slate: #f8fafc;
        --card-border: #e2e8f0;
    }

    body { font-family: 'Cairo', sans-serif; background-color: #f1f5f9; color: #1e293b; }

    .cert-admin-wrapper {
        max-width: 1350px;
        margin: 0 auto;
        padding: 25px 20px 60px;
        animation: fadeIn 0.4s ease-out;
    }

    /* هيدر الصفحة الرسمي */
    .official-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 24px;
        padding: 35px 40px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .official-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(217, 119, 6, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .header-info h1 {
        font-size: 1.8rem;
        font-weight: 900;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .header-info p {
        color: #94a3b8;
        font-size: 0.95rem;
        margin: 0;
    }

    .status-badge-header {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 800;
    }

    /* شبكة مفاتيح التحكم الكبرى */
    .master-controls-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .control-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 25px;
        border: 1.5px solid var(--card-border);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .control-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .card-title-box {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .btn-toggle-master {
        width: 100%;
        padding: 14px 20px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 0.92rem;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: 0.25s;
    }

    /* جدول الطلاب والشهادات */
    .main-table-card {
        background: #ffffff;
        border-radius: 22px;
        border: 1.5px solid var(--card-border);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .table-top-bar {
        padding: 22px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 15px;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .custom-table th {
        background: #f8fafc;
        padding: 16px 20px;
        font-size: 0.82rem;
        font-weight: 800;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }

    .custom-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .custom-table tr:hover td {
        background-color: #fafbfc;
    }

    .grade-badge-active {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1px solid #fcd34d;
        padding: 6px 14px;
        border-radius: 10px;
        font-weight: 900;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action-small {
        padding: 7px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.78rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        text-decoration: none;
        transition: 0.2s;
    }

    /* المودال */
    .modal-backdrop-custom {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(4px);
        z-index: 99999;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .modal-box-custom {
        background: white;
        width: 100%;
        max-width: 580px;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: scaleUp 0.25s ease-out;
    }

    @keyframes scaleUp { from { transform: scale(0.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="cert-admin-wrapper" dir="rtl">

    <!-- هيدر الإدارة الأكاديمي -->
    <div class="official-header">
        <div class="header-info">
            <span class="status-badge-header" style="background: rgba(217, 119, 6, 0.2); color: #fbbf24; margin-bottom: 10px;">
                <i class="fa-solid fa-graduation-cap"></i> الشؤون الأكاديمية والامتحانات الوزارية
            </span>
            <h1>
                <i class="fa-solid fa-award" style="color: #f59e0b;"></i>
                إدارة واعتماد شهادات ونتائج نهاية العام
            </h1>
            <p>لوحة التحكم الشاملة للتحكم في إعلان نتائج التخرج للطلاب، واعتماد الدرجات، وإصدار الشهادات الرسمية</p>
        </div>

        <div>
            <button type="button" onclick="openIssueModal()" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 12px 24px; border-radius: 14px; font-weight: 800; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);">
                <i class="fa-solid fa-plus-circle"></i> اعتماد ورصد شهادة جديدة
            </button>
        </div>
    </div>

    <!-- شبكة مفاتيح التحكم الكبرى (Master Toggles) -->
    <div class="master-controls-grid">

        <!-- بطاقة 1: إعلان ونشر شهادات نهاية العام -->
        <div class="control-card">
            <div>
                <div class="card-title-box">
                    <div class="card-icon" style="background: {{ $yearEndPublished ? '#ecfdf5' : '#fef2f2' }}; color: {{ $yearEndPublished ? '#059669' : '#dc2626' }};">
                        <i class="fa-solid {{ $yearEndPublished ? 'fa-unlock' : 'fa-lock' }}"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0 0 4px 0; font-size: 1.1rem; font-weight: 800;">إعلان شهادات نهاية العام</h3>
                        <span style="font-size: 0.8rem; font-weight: 700; color: {{ $yearEndPublished ? '#059669' : '#dc2626' }};">
                            الحالة الحالية: {{ $yearEndPublished ? 'معلنة ومنشورة رسمياً للطلاب 🎓' : 'محجوبة بقرار الإدارة (مغلقة) 🔒' }}
                        </span>
                    </div>
                </div>
                <p style="font-size: 0.84rem; color: #64748b; margin-top: 14px; line-height: 1.6;">
                    الشهادات محجوبة افتراضياً طوال العام الدراسي. عند تفعيل هذا المفتاح بنهاية العام، ستظهر الشهادات المعتمدة في حسابات الطلاب.
                </p>
            </div>

            <button type="button" onclick="togglePublishState()" id="btnTogglePublish" class="btn-toggle-master" style="background: {{ $yearEndPublished ? '#fee2e2' : '#ecfdf5' }}; color: {{ $yearEndPublished ? '#dc2626' : '#059669' }}; border: 1.5px solid {{ $yearEndPublished ? '#fca5a5' : '#a7f3d0' }};">
                <i class="fa-solid {{ $yearEndPublished ? 'fa-eye-slash' : 'fa-bullhorn' }}"></i>
                <span>{{ $yearEndPublished ? 'حجب الشهادات وإغلاق الإعلان 🔒' : 'إعلان ونشر الشهادات للطلاب الآن 🎓' }}</span>
            </button>
        </div>

        <!-- بطاقة 2: حاسبة المعدل النهائي للطلاب -->
        <div class="control-card">
            <div>
                <div class="card-title-box">
                    <div class="card-icon" style="background: {{ $allowStudentGpa ? '#eff6ff' : '#f8fafc' }}; color: {{ $allowStudentGpa ? '#2563eb' : '#64748b' }};">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0 0 4px 0; font-size: 1.1rem; font-weight: 800;">حساب المعدل النهائي للطلاب</h3>
                        <span style="font-size: 0.8rem; font-weight: 700; color: {{ $allowStudentGpa ? '#2563eb' : '#64748b' }};">
                            الحالة: {{ $allowStudentGpa ? 'متاح للطلاب حساب المعدل ✅' : 'مقيد ومحجوب بقرار الإدارة 🔒' }}
                        </span>
                    </div>
                </div>
                <p style="font-size: 0.84rem; color: #64748b; margin-top: 14px; line-height: 1.6;">
                    التحكم في إمكانية استخدام الطلاب لحاسبة المعدل واستخراج درجات التخرج، بحيث لا يتاح إلا عندما تسمح الإدارة بذلك.
                </p>
            </div>

            <button type="button" onclick="toggleGpaState()" id="btnToggleGpa" class="btn-toggle-master" style="background: {{ $allowStudentGpa ? '#f1f5f9' : '#eff6ff' }}; color: {{ $allowStudentGpa ? '#475569' : '#1d4ed8' }}; border: 1.5px solid {{ $allowStudentGpa ? '#cbd5e1' : '#bfdbfe' }};">
                <i class="fa-solid {{ $allowStudentGpa ? 'fa-lock' : 'fa-unlock' }}"></i>
                <span>{{ $allowStudentGpa ? 'قفل حاسبة المعدل عن الطلاب 🔒' : 'السماح للطلاب بحساب المعدل ✅' }}</span>
            </button>
        </div>

        <!-- بطاقة 3: إحصائيات الاعتماد الأكاديمي -->
        <div class="control-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
            <div>
                <div class="card-title-box">
                    <div class="card-icon" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0 0 4px 0; font-size: 1.1rem; font-weight: 800;">إحصائيات التخرج والاعتماد</h3>
                        <span style="font-size: 0.8rem; color: #64748b;">العام الأكاديمي: 2025 / 2026</span>
                    </div>
                </div>
                <div style="display: flex; gap: 20px; margin-top: 20px;">
                    <div style="flex: 1; background: white; padding: 14px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center;">
                        <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; display: block;">الشهادات الصادرة</span>
                        <span style="font-size: 1.5rem; font-weight: 900; color: #d97706;" id="statCertCount">{{ $stats['total_certificates'] }}</span>
                    </div>
                    <div style="flex: 1; background: white; padding: 14px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center;">
                        <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; display: block;">إجمالي الطلاب</span>
                        <span style="font-size: 1.5rem; font-weight: 900; color: #1e293b;">{{ $stats['total_students'] }}</span>
                    </div>
                </div>
            </div>
            <span style="font-size: 0.78rem; color: #64748b; text-align: center;">
                <i class="fa-solid fa-shield-halved" style="color: #10b981;"></i> وثائق مؤمنة بختم وتوقيع وكود تحقق رسمي
            </span>
        </div>

    </div>

    <!-- جدول الطلاب ورصد الدرجات والشهادات -->
    <div class="main-table-card">
        <div class="table-top-bar">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 900; color: #0f172a; margin: 0 0 4px 0;">سجل درجات وشهادات الطلاب</h2>
                <span style="font-size: 0.82rem; color: #64748b;">قائمة طلاب الثانوية العامة مع رصد المعدلات الحقيقية وحالة الاعتماد</span>
            </div>
            <div style="font-size: 0.85rem; font-weight: 700; color: #475569;">
                إجمالي الطلاب المسجلين: <mark style="background: #eef2ff; color: #4f46e5; padding: 2px 8px; border-radius: 6px;">{{ $students->total() }}</mark>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>معلومات الطالب</th>
                        <th>الفرع الأكاديمي</th>
                        <th>الهوية الوطنية</th>
                        <th>حالة الاعتماد والمعدل المرصود</th>
                        <th style="text-align: center;">التحكم والشهادة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        @php
                            $latestCert = $st->certificates->first();
                        @endphp
                        <tr id="row_student_{{ $st->id }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem;">
                                        {{ mb_substr($st->name_ar, 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 800; color: #0f172a;">{{ $st->name_ar }}</div>
                                        <div style="font-size: 0.78rem; color: #94a3b8;">{{ $st->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 8px; font-size: 0.82rem; font-weight: 700;">
                                    {{ $st->stage?->label_ar ?? 'غير محدد' }}
                                </span>
                            </td>
                            <td>
                                <code style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 6px; border-radius: 6px; font-size: 0.85rem;">
                                    {{ $st->nid }}
                                </code>
                            </td>
                            <td>
                                @if($latestCert)
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <span class="grade-badge-active">
                                            <i class="fa-solid fa-star" style="color: #d97706;"></i>
                                            المعدل: {{ $latestCert->final_grade }}%
                                        </span>
                                        <span style="font-size: 0.78rem; color: #64748b;">
                                            ({{ $latestCert->subject?->name_ar ?? 'توجيهي عام' }})
                                        </span>
                                    </div>
                                @else
                                    <span style="background: #f8fafc; color: #94a3b8; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; border: 1px dashed #cbd5e1;">
                                        <i class="fa-solid fa-hourglass-start"></i> قيد التقييم (لم تُرصد شهادة)
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 8px; align-items: center;">
                                    @if($latestCert)
                                        <a href="{{ route('certificates.show', $latestCert->id) }}" target="_blank" class="btn-action-small" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;" title="معاينة وطباعة الشهادة الملكية">
                                            <i class="fa-solid fa-eye"></i> معاينة الشهادة
                                        </a>

                                        <button type="button" onclick="openIssueModal({{ $st->id }}, '{{ addslashes($st->name_ar) }}', {{ $latestCert->final_grade }}, {{ $latestCert->subject_id ?? 'null' }})" class="btn-action-small" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;" title="تعديل المعدل">
                                            <i class="fa-solid fa-pen"></i> تعديل
                                        </button>

                                        <button type="button" onclick="deleteCert({{ $latestCert->id }}, '{{ addslashes($st->name_ar) }}')" class="btn-action-small" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;" title="حذف الشهادة">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button type="button" onclick="openIssueModal({{ $st->id }}, '{{ addslashes($st->name_ar) }}')" class="btn-action-small" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;" title="رصد الدرجة وإصدار الشهادة">
                                            <i class="fa-solid fa-award"></i> اعتماد وإصدار الشهادة
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                                لا يوجد طلاب مسجلون حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div style="padding: 20px; border-top: 1px solid #f1f5f9;">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>

<!-- نافذة اعتماد ورصد الشهادة للمدير (Modal) -->
<div id="issueModalOverlay" class="modal-backdrop-custom">
    <div class="modal-box-custom">
        <div style="padding: 22px 28px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 1.6rem;">🎓</span>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">اعتماد ورصد الشهادة الأكاديمية</h3>
                    <span style="font-size: 0.8rem; color: #94a3b8;">إصدار شهادة تفوق واجتياز رسمية معتمدة من الإدارة</span>
                </div>
            </div>
            <button type="button" onclick="closeIssueModal()" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">✕</button>
        </div>

        <form id="issueCertForm" style="padding: 25px 28px;">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 18px;">

                <!-- اختيار الطالب -->
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #475569; display: block; margin-bottom: 6px;">الطالب المعني بالشهادة *</label>
                    <select name="student_id" id="modalStudentSelect" required style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-family: inherit; font-size: 0.9rem; outline: none;">
                        <option value="" disabled selected>اختر الطالب...</option>
                        @foreach($students as $stu)
                            <option value="{{ $stu->id }}">{{ $stu->name_ar }} ({{ $stu->stage?->label_ar ?? 'توجيهي' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- اختيار المادة -->
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #475569; display: block; margin-bottom: 6px;">المادة / المساق الأكاديمي</label>
                    <select name="subject_id" id="modalSubjectSelect" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-family: inherit; font-size: 0.9rem; outline: none;">
                        <option value="">شهادة إتمام وتفوق عامة في الثانوية العامة</option>
                        @foreach($subjects as $sb)
                            <option value="{{ $sb->id }}">{{ $sb->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- رصد المعدل الحقيقي -->
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #475569; display: block; margin-bottom: 6px;">
                        المعدل أو النسبة المئوية المعتمدة (من 50 إلى 100) *
                    </label>
                    <input type="number" name="final_grade" id="modalFinalGrade" min="50" max="100" step="0.1" required placeholder="مثال: 94.5" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-family: inherit; font-size: 1.05rem; font-weight: 800; color: #d97706; outline: none;">
                    <span style="font-size: 0.75rem; color: #64748b; margin-top: 4px; display: block;">
                        * أدخل المعدل الحقيقي الفعلي؛ لن يتم وضع أي درجات وهمية أو تلقائية إطلاقاً.
                    </span>
                </div>

            </div>

            <!-- أزرار الإجراءات -->
            <div style="margin-top: 25px; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 18px;">
                <button type="button" onclick="closeIssueModal()" style="background: #f8fafc; color: #64748b; border: 1.5px solid #cbd5e1; padding: 10px 20px; border-radius: 12px; font-weight: 700; cursor: pointer;">إلغاء</button>
                <button type="button" onclick="submitIssueCert()" id="btnSubmitIssue" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 10px 24px; border-radius: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                    <i class="fa-solid fa-check"></i> اعتماد وإصدار الشهادة
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    function openIssueModal(studentId = null, studentName = null, grade = null, subjectId = null) {
        const modal = document.getElementById('issueModalOverlay');
        const stSelect = document.getElementById('modalStudentSelect');
        const subSelect = document.getElementById('modalSubjectSelect');
        const gradeInput = document.getElementById('modalFinalGrade');

        if (studentId) {
            stSelect.value = studentId;
        }
        if (grade) {
            gradeInput.value = grade;
        } else {
            gradeInput.value = '';
        }
        if (subjectId) {
            subSelect.value = subjectId;
        } else {
            subSelect.value = '';
        }

        modal.style.display = 'flex';
    }

    function closeIssueModal() {
        document.getElementById('issueModalOverlay').style.display = 'none';
    }

    document.getElementById('issueModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeIssueModal();
    });

    // تبديل حالة إعلان ونشر الشهادات
    async function togglePublishState() {
        const btn = document.getElementById('btnTogglePublish');
        btn.disabled = true;

        try {
            const res = await axios.post("{{ route('admin.certificates.togglePublish') }}", {
                _token: '{{ csrf_token() }}'
            });

            Swal.fire({
                icon: res.data.icon,
                title: res.data.title,
                text: res.data.message,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } catch (e) {
            btn.disabled = false;
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر تحديث حالة إعلان الشهادات.' });
        }
    }

    // تبديل إمكانية حساب المعدل
    async function toggleGpaState() {
        const btn = document.getElementById('btnToggleGpa');
        btn.disabled = true;

        try {
            const res = await axios.post("{{ route('admin.certificates.toggleGpa') }}", {
                _token: '{{ csrf_token() }}'
            });

            Swal.fire({
                icon: res.data.icon,
                title: res.data.title,
                text: res.data.message,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } catch (e) {
            btn.disabled = false;
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر تحديث إعدادات حاسبة المعدل.' });
        }
    }

    // إرسال اعتماد الشهادة
    async function submitIssueCert() {
        const form = document.getElementById('issueCertForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const btn = document.getElementById('btnSubmitIssue');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الاعتماد...';

        const formData = new FormData(form);

        try {
            const res = await axios.post("{{ route('admin.certificates.issue') }}", formData);
            Swal.fire({
                icon: 'success',
                title: res.data.title,
                text: res.data.message,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'عرض وتحديث'
            }).then(() => {
                location.reload();
            });
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> اعتماد وإصدار الشهادة';

            let msg = 'حدث خطأ أثناء رصد الشهادة.';
            if (error.response && error.response.data && error.response.data.errors) {
                msg = Object.values(error.response.data.errors).flat().join('<br>');
            }
            Swal.fire({ icon: 'error', title: 'فشل الاعتماد', html: msg });
        }
    }

    // حذف شهادة
    function deleteCert(certId, studentName) {
        Swal.fire({
            title: 'حذف وإلغاء الشهادة',
            text: `هل أنت متأكد من رغبتك في سحب شهادة الطالب (${studentName})؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، حذف الشهادة',
            cancelButtonText: 'تراجع'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    await axios.delete(`{{ url('admin/certificates') }}/${certId}`, {
                        data: { _token: '{{ csrf_token() }}' }
                    });
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف بنجاح',
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر حذف الشهادة.' });
                }
            }
        });
    }
</script>
@endsection
