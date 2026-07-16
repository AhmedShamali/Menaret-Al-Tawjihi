@extends('layouts.app')

@section('title', 'إدارة الطلاب')

@section('content')
<div style="display: flex; flex-direction: column; gap: 35px; animation: fadeIn 0.6s ease;">

    {{-- رأس الصفحة --}}
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--primary);">سجل الطلاب والطلبات 👥</h1>
            <p style="color: var(--text-light);">إدارة وتفعيل حسابات طلاب منصة جسر والمراجعة الأكاديمية.</p>
        </div>
        <a href="{{ route('students.create') }}" class="btn btn-primary" style="border-radius: 15px; padding: 15px 30px;">
            ➕ إضافة طالب جديد
        </a>
    </div>

    {{-- جدول الطلاب المصمم بأسلوب الجامعات --}}
    <div class="glass-card" style="padding: 0; overflow: hidden; border: none; border-radius: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.03);">
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="background: var(--primary); color: white;">
                    <th style="padding: 25px;">الطالب</th>
                    <th style="padding: 25px;">المرحلة الدراسية</th>
                    <th style="padding: 25px;">رقم الهوية</th>
                    <th style="padding: 25px;">الحالة</th>
                    <th style="padding: 25px; text-align: center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr id="row_{{ $student->id }}" class="student-row" style="border-bottom: 1px solid #f1f5f9; transition: 0.3s;">
                    <td style="padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="{{ asset('storage/'.$student->photo) }}" style="width: 50px; height: 50px; border-radius: 14px; object-fit: cover; border: 2px solid #f1f5f9;">
                            <div>
                                <div style="font-weight: 700; color: var(--primary);">{{ $student->name_ar }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-light);">{{ $student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 20px; font-weight: 600; color: var(--primary);">
                        {{ $student->stage->label_ar ?? 'غير محدد' }}
                    </td>
                    <td style="padding: 20px; font-family: monospace; font-weight: 700; color: var(--text-light);">
                        {{ $student->nid }}
                    </td>
                    <td style="padding: 20px;">
                        <span class="status-chip {{ $student->status == 'active' ? 'active' : 'pending' }}">
                            {{ $student->status == 'active' ? 'مفعّل' : 'قيد المراجعة' }}
                        </span>
                    </td>
                    <td style="padding: 20px;">
                        <div style="display: flex; justify-content: center; gap: 10px;">

                            {{-- زر التفعيل والتعطيل الذكي بالأيقونات الملونة --}}
                            <button onclick="performToggle({{ $student->id }})"
                                    class="act-icon {{ $student->status == 'active' ? 'status-active' : 'status-inactive' }}"
                                    title="{{ $student->status == 'active' ? 'تعطيل الحساب' : 'تفعيل الحساب' }}">
                                @if($student->status == 'active')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                @else
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                @endif
                            </button>

                            {{-- زر التعديل --}}
                            <a href="{{ route('students.edit', $student->id) }}" class="act-icon edit-btn" title="تعديل البيانات">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </a>

                            {{-- زر الحذف --}}
                            <button onclick="deleteStudent({{ $student->id }})" class="act-icon delete-btn" title="حذف الطالب">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    /* التنسيقات الفخمة للحالات */
    .status-chip { padding: 6px 15px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; }
    .status-chip.active { background: #ecfdf5; color: #059669; }
    .status-chip.pending { background: #fff7ed; color: #c2410c; }

    /* تنسيق الأيقونات */
    .act-icon { width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; display: grid; place-items: center; transition: 0.3s; cursor: pointer; text-decoration: none; }

    .status-active { color: #059669; background: #ecfdf5; border-color: #10b981; }
    .status-active:hover { background: #d1fae5; transform: scale(1.1); }

    .status-inactive { color: #64748b; background: #f1f5f9; }
    .status-inactive:hover { color: #ef4444; background: #fef2f2; transform: scale(1.1) rotate(10deg); }

    .edit-btn { color: var(--accent); }
    .edit-btn:hover { background: #ecfdf5; border-color: var(--accent); transform: translateY(-3px); }

    .delete-btn { color: #94a3b8; }
    .delete-btn:hover { color: #ef4444; background: #fef2f2; border-color: #fca5a5; transform: translateY(-3px); }

    .student-row:hover { background: #fcfcfd; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. وظيفة التفعيل والتعطيل (Toggle)
  function performToggle(id) {
    // السلاش / قبل كلمة students هي اللي بتحل مشكلة الراوت
    axios.post(`/students/toggle-status/${id}`)
    .then(function (res) {
        Swal.fire({
            icon: res.data.icon,
            title: res.data.title,
            showConfirmButton: false,
            timer: 1500
        }).then(() => location.reload()); // تحديث الصفحة لرؤية النتيجة
    })
    .catch(function (err) {
        console.error(err); // اطبع الخطأ في الكونسول عشان لو في مشكلة تانية تبين
        Swal.fire('خطأ في الراوت', 'تأكد من وجود المسار في ملف web.php', 'error');
    });
}

    // 2. وظيفة الحذف (Delete)
    function deleteStudent(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف بيانات الطالب نهائياً من النظام",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'نعم، احذف الآن',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/students/${id}`)
                .then(res => {
                    if(res.data.success) {
                        document.getElementById(`row_${id}`).remove();
                        Swal.fire('تم الحذف!', '', 'success');
                    }
                });
            }
        });
    }
</script>
@endsection
