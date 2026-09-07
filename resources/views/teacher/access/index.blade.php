@extends('layouts.app')

@section('title', 'تحكم صلاحيات واشتراكات الطلاب')

@section('content')
<style>
    :root {
        --primary: #4361ee;
        --primary-dark: #3a0ca3;
        --secondary: #4cc9f0;
        --card-bg: #ffffff;
        --border: #e2e8f0;
    }

    .access-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 20px 0;
    }

    .page-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .subject-pill {
        padding: 10px 20px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid var(--border);
        background: white;
        color: #475569;
    }

    .subject-pill.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .students-table-card {
        background: white;
        border-radius: 20px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }

    .table-modern th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        padding: 16px 20px;
        text-align: right;
        border-bottom: 1px solid var(--border);
    }

    .table-modern td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .table-modern tr:hover {
        background: #f8fafc;
    }

    .badge-mode-all {
        background: #dcfce7;
        color: #166534;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-mode-custom {
        background: #ede9fe;
        color: #5b21b6;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action-edit {
        background: #f1f5f9;
        color: #1e293b;
        border: 1px solid var(--border);
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-action-edit:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Modal Styles */
    .modal-backdrop-custom {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(6px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-content-custom {
        background: white;
        border-radius: 24px;
        max-width: 750px;
        width: 100%;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        animation: modalIn 0.3s ease-out;
    }

    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .content-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        margin-bottom: 8px;
        background: #f8fafc;
        transition: 0.2s;
    }

    .content-item-row:hover {
        background: white;
        border-color: var(--primary);
    }
</style>

<div class="access-container">
    <!-- رأس الصفحة -->
    <div class="page-header-card">
        <div>
            <span style="background: rgba(67, 97, 238, 0.2); color: #93c5fd; padding: 4px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">
                <i class="fa-solid fa-shield-halved"></i> إدارة اشتراكات التوجيهي
            </span>
            <h2 style="font-size: 1.6rem; font-weight: 800; margin: 10px 0 6px;">التحكم بصلاحيات وظهور الفيديوهات للطلاب</h2>
            <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">
                حدد ما يظهر لكل طالب: المنهج كاملاً أو جزئيات ووحدات مخصصة بحسب اشتراكه.
            </p>
        </div>

        <button onclick="openQuickEnrollModal()" style="background: var(--primary); color: white; border: none; padding: 12px 22px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-user-plus"></i> تفعيل اشتراك طالب جديد
        </button>
    </div>

    <!-- قائمة المواد الدراسية للمعلم -->
    <div style="display: flex; gap: 12px; margin-bottom: 25px; overflow-x: auto; padding-bottom: 8px;">
        @foreach($subjects as $sub)
            <a href="{{ route('teacher.access.index', ['subject_id' => $sub->id]) }}" 
               class="subject-pill {{ ($selectedSubject && $selectedSubject->id == $sub->id) ? 'active' : '' }}">
                <i class="fa-solid fa-book-bookmark"></i>
                {{ $sub->name_ar ?? $sub->name }}
            </a>
        @endforeach
    </div>

    <!-- جدول الطلاب المشتركين -->
    <div class="students-table-card">
        <div style="padding: 20px 25px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;">
                الطلاب المسجلون في: {{ $selectedSubject ? ($selectedSubject->name_ar ?? $selectedSubject->name) : 'المادة' }}
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 500; margin-right: 8px;">(إجمالي الدروس: {{ $totalContentsCount }})</span>
            </h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>اسم الطالب</th>
                        <th>رقم الهوية / البريد</th>
                        <th>المرحلة والفرع</th>
                        <th>نوع الاشتراك</th>
                        <th>الدروس المصرح بها</th>
                        <th style="text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                        @php
                            $student = $enrollment->student;
                            $assignedCount = $enrollment->access_mode === 'all' 
                                ? $totalContentsCount 
                                : $enrollment->contentAssignments->where('is_visible', true)->count();
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;">{{ $student->name_ar ?? 'طالب' }}</div>
                                <span style="font-size: 0.78rem; color: #94a3b8;">{{ $student->phone ?? 'بدون هاتف' }}</span>
                            </td>
                            <td>
                                <code style="background: #f1f5f9; padding: 2px 8px; border-radius: 6px; font-size: 0.85rem; color: #0f172a;">{{ $student->nid ?? $student->email }}</code>
                            </td>
                            <td>
                                <span style="color: #475569; font-weight: 600;">{{ optional($student->stage)->label_ar ?? optional($student->stage)->name ?? 'ثانوية عامة' }}</span>
                            </td>
                            <td>
                                @if($enrollment->access_mode === 'all')
                                    <span class="badge-mode-all">
                                        <i class="fa-solid fa-circle-check"></i> كامل المنهج
                                    </span>
                                @else
                                    <span class="badge-mode-custom">
                                        <i class="fa-solid fa-list-check"></i> جزئية مخصصة
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--primary);">{{ $assignedCount }}</span> من أصل {{ $totalContentsCount }} درس
                            </td>
                            <td style="text-align: center;">
                                <button class="btn-action-edit" onclick="openEditAccessModal({{ $enrollment->id }})">
                                    <i class="fa-solid fa-sliders"></i> تعديل الصلاحيات
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 50px; color: #94a3b8;">
                                <i class="fa-solid fa-users-slash" style="font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
                                <p style="font-size: 0.95rem; font-weight: 600; margin: 0;">لا يوجد طلاب مشتركون في هذه المادة حالياً.</p>
                                <button onclick="openQuickEnrollModal()" style="margin-top: 15px; background: none; border: 1px dashed var(--primary); color: var(--primary); padding: 8px 18px; border-radius: 10px; font-weight: 700; cursor: pointer;">
                                    + إضافة وتفعيل اشتراك طالب
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($enrollments, 'hasPages') && $enrollments->hasPages())
            <div style="padding: 20px; display: flex; justify-content: center;">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal تعديل صلاحيات الطالب -->
<div class="modal-backdrop-custom" id="accessModal">
    <div class="modal-content-custom">
        <div style="padding: 22px 25px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <div>
                <h4 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;" id="modalStudentName">صلاحيات الطالب</h4>
                <p style="margin: 4px 0 0; font-size: 0.85rem; color: #64748b;" id="modalSubjectName">المادة</p>
            </div>
            <button onclick="closeAccessModal()" style="background: none; border: none; font-size: 1.4rem; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <div style="padding: 25px; overflow-y: auto; flex: 1;">
            <!-- اختيار نمط الوصول -->
            <label style="font-weight: 700; font-size: 0.9rem; color: #1e293b; display: block; margin-bottom: 10px;">نوع الاشتراك المصرح به:</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                <label style="border: 2px solid var(--border); border-radius: 14px; padding: 15px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: 0.2s;" id="labelModeAll">
                    <input type="radio" name="access_mode" value="all" id="radioModeAll" onchange="toggleModeView('all')">
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">كامل المنهج</div>
                        <div style="font-size: 0.8rem; color: #64748b;">تظهر كافة الفيديوهات والملازم الحالية والمستقبلية</div>
                    </div>
                </label>

                <label style="border: 2px solid var(--border); border-radius: 14px; padding: 15px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: 0.2s;" id="labelModeCustom">
                    <input type="radio" name="access_mode" value="custom" id="radioModeCustom" onchange="toggleModeView('custom')">
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">جزئية مخصصة</div>
                        <div style="font-size: 0.8rem; color: #64748b;">تحديد دروس أو وحدات معينة يدرسها الطالب</div>
                    </div>
                </label>
            </div>

            <!-- قائمة الدروس عند اختيار نمط جزئية مخصصة -->
            <div id="customContentsWrapper" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <span style="font-weight: 800; font-size: 0.9rem; color: #0f172a;">حدد الدروس والفيديوهات المفتوحة لهذا الطالب:</span>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" onclick="selectAllContents(true)" style="background: none; border: none; color: var(--primary); font-size: 0.8rem; font-weight: 700; cursor: pointer;">تحديد الكل</button>
                        <span style="color: #cbd5e1;">|</span>
                        <button type="button" onclick="selectAllContents(false)" style="background: none; border: none; color: #ef4444; font-size: 0.8rem; font-weight: 700; cursor: pointer;">إلغاء التحديد</button>
                    </div>
                </div>

                <div id="contentsListContainer" style="max-height: 320px; overflow-y: auto; padding-left: 5px;">
                    <!-- يتم تحميل الدروس ديناميكياً هنا -->
                </div>
            </div>
        </div>

        <div style="padding: 18px 25px; border-top: 1px solid var(--border); background: #f8fafc; display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" onclick="closeAccessModal()" style="background: white; border: 1px solid var(--border); padding: 10px 20px; border-radius: 12px; font-weight: 700; cursor: pointer; color: #64748b;">إلغاء</button>
            <button type="button" onclick="saveStudentAccess()" id="btnSaveAccess" style="background: var(--primary); color: white; border: none; padding: 10px 25px; border-radius: 12px; font-weight: 700; cursor: pointer;">
                حفظ التعديلات
            </button>
        </div>
    </div>
</div>

<!-- Modal تفعيل اشتراك سريع لطالب -->
<div class="modal-backdrop-custom" id="quickEnrollModal">
    <div class="modal-content-custom" style="max-width: 500px;">
        <div style="padding: 20px 25px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;">تفعيل اشتراك طالب في المادة</h4>
            <button onclick="closeQuickEnrollModal()" style="background: none; border: none; font-size: 1.4rem; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>
        <form onsubmit="handleQuickEnroll(event)" style="padding: 25px;">
            <input type="hidden" id="enrollSubjectId" value="{{ $selectedSubject?->id }}">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 8px; color: #1e293b;">رقم هوية الطالب أو بريده الإلكتروني:</label>
                <input type="text" id="studentIdentifier" required placeholder="مثال: 401234567 أو student@mail.com" style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 8px; color: #1e293b;">نوع الاشتراك الابتدائي:</label>
                <select id="initialAccessMode" style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem;">
                    <option value="all">كامل المنهج (جميع الفيديوهات والملفات)</option>
                    <option value="custom">جزئية مخصصة (تحديد يدوي للدروس)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeQuickEnrollModal()" style="background: white; border: 1px solid var(--border); padding: 10px 18px; border-radius: 10px; font-weight: 700; cursor: pointer;">إلغاء</button>
                <button type="submit" style="background: var(--primary); color: white; border: none; padding: 10px 22px; border-radius: 10px; font-weight: 700; cursor: pointer;">تفعيل الاشتراك الآن</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentEnrollmentId = null;

function openEditAccessModal(enrollmentId) {
    currentEnrollmentId = enrollmentId;
    const modal = document.getElementById('accessModal');
    modal.style.display = 'flex';

    document.getElementById('contentsListContainer').innerHTML = '<div style="text-align:center; padding: 30px; color:#64748b;"><i class="fa-solid fa-spinner fa-spin"></i> جاري جلب الدروس...</div>';

    axios.get(`/teacher/access/${enrollmentId}/contents`)
        .then(res => {
            const data = res.data;
            document.getElementById('modalStudentName').textContent = `صلاحيات الطالب: ${data.enrollment.student_name}`;
            document.getElementById('modalSubjectName').textContent = `المادة: ${data.enrollment.subject_name}`;

            if (data.enrollment.access_mode === 'all') {
                document.getElementById('radioModeAll').checked = true;
                toggleModeView('all');
            } else {
                document.getElementById('radioModeCustom').checked = true;
                toggleModeView('custom');
            }

            renderContentsList(data.contents);
        })
        .catch(err => {
            Swal.fire('خطأ', 'تعذر تحميل بيانات دروس المادة', 'error');
        });
}

function renderContentsList(contents) {
    const container = document.getElementById('contentsListContainer');
    if (!contents || contents.length === 0) {
        container.innerHTML = '<div style="text-align:center; padding: 20px; color: #94a3b8;">لا توجد دروس مضافة في هذه المادة بعد.</div>';
        return;
    }

    let html = '';
    contents.forEach(c => {
        const checked = c.is_unlocked ? 'checked' : '';
        const icon = c.has_video ? '<i class="fa-solid fa-play-circle" style="color: #3b82f6;"></i>' : '<i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i>';
        html += `
            <label class="content-item-row">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <input type="checkbox" class="content-checkbox" value="${c.id}" ${checked} style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">${icon} ${c.title}</div>
                        <span style="font-size: 0.75rem; color: #64748b;">القناة/الوحدة: ${c.channel_name} | الترتيب: ${c.order}</span>
                    </div>
                </div>
            </label>
        `;
    });
    container.innerHTML = html;
}

function toggleModeView(mode) {
    const customDiv = document.getElementById('customContentsWrapper');
    const labelAll = document.getElementById('labelModeAll');
    const labelCustom = document.getElementById('labelModeCustom');

    if (mode === 'all') {
        customDiv.style.display = 'none';
        labelAll.style.borderColor = 'var(--primary)';
        labelAll.style.background = '#f0f9ff';
        labelCustom.style.borderColor = 'var(--border)';
        labelCustom.style.background = 'white';
    } else {
        customDiv.style.display = 'block';
        labelCustom.style.borderColor = 'var(--primary)';
        labelCustom.style.background = '#f0f9ff';
        labelAll.style.borderColor = 'var(--border)';
        labelAll.style.background = 'white';
    }
}

function selectAllContents(check) {
    document.querySelectorAll('.content-checkbox').forEach(cb => cb.checked = check);
}

function closeAccessModal() {
    document.getElementById('accessModal').style.display = 'none';
}

function saveStudentAccess() {
    if (!currentEnrollmentId) return;

    const btn = document.getElementById('btnSaveAccess');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';

    const accessMode = document.querySelector('input[name="access_mode"]:checked').value;
    const allowedContents = [];

    if (accessMode === 'custom') {
        document.querySelectorAll('.content-checkbox:checked').forEach(cb => {
            allowedContents.push(parseInt(cb.value));
        });
    }

    axios.post(`/teacher/access/${currentEnrollmentId}/update`, {
        access_mode: accessMode,
        allowed_contents: allowedContents,
        _token: '{{ csrf_token() }}'
    }).then(res => {
        btn.disabled = false;
        btn.textContent = 'حفظ التعديلات';
        closeAccessModal();
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح!',
            text: res.data.message,
            confirmButtonText: 'حسناً'
        }).then(() => {
            window.location.reload();
        });
    }).catch(err => {
        btn.disabled = false;
        btn.textContent = 'حفظ التعديلات';
        Swal.fire('خطأ', 'فشلت عملية حفظ الصلاحيات', 'error');
    });
}

function openQuickEnrollModal() {
    document.getElementById('quickEnrollModal').style.display = 'flex';
}

function closeQuickEnrollModal() {
    document.getElementById('quickEnrollModal').style.display = 'none';
}

function handleQuickEnroll(e) {
    e.preventDefault();
    const subjectId = document.getElementById('enrollSubjectId').value;
    const identifier = document.getElementById('studentIdentifier').value.trim();
    const mode = document.getElementById('initialAccessMode').value;

    axios.post('/teacher/access/quick-enroll', {
        subject_id: subjectId,
        student_identifier: identifier,
        access_mode: mode,
        _token: '{{ csrf_token() }}'
    }).then(res => {
        closeQuickEnrollModal();
        Swal.fire('تم التفعيل', res.data.message, 'success').then(() => {
            window.location.reload();
        });
    }).catch(err => {
        const msg = err.response?.data?.message || 'حدث خطأ أثناء تفعيل الاشتراك';
        Swal.fire('تنبيه', msg, 'warning');
    });
}
</script>
@endsection
