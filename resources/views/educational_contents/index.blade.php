@extends('layouts.app')

@section('title', 'إدارة المحتوى التعليمي')

@section('content')
<div style="display: flex; flex-direction: column; gap: 30px; animation: fadeIn 0.5s ease;">

    <!-- رأس الصفحة -->
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--primary);">المكتبة التعليمية 📂</h1>
            <p style="color: var(--text-light);">تحكم كامل في دروس ومصادر المنصة التعليمية.</p>
        </div>
        <a href="{{ route('educational_contents.create') }}" class="btn btn-primary" style="padding: 12px 25px; border-radius: 14px;">
            ➕ إضافة محتوى جديد
        </a>
    </div>

    <!-- جدول المحتويات -->
    <div class="glass-card" style="padding: 0; overflow: hidden; border: none;">
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <th style="padding: 20px; font-size: 0.85rem; color: var(--text-light); width: 100px;">النوع</th>
                    <th style="padding: 20px; font-size: 0.85rem; color: var(--text-light);">عنوان الدرس</th>
                    <th style="padding: 20px; font-size: 0.85rem; color: var(--text-light);">المادة</th>
                    <th style="padding: 20px; font-size: 0.85rem; color: var(--text-light); text-align: center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contents as $content)
                <tr id="row_{{ $content->id }}" class="table-row">
                    <td style="padding: 20px;">
                        <span class="chip" style="background: {{ $content->type == 'video' ? '#ecfdf5' : '#eff6ff' }}; color: {{ $content->type == 'video' ? '#059669' : '#2563eb' }};">
                            {{ $content->type == 'video' ? '🎥 فيديو' : '📄 ملف' }}
                        </span>
                    </td>
                    <td style="padding: 20px;">
                        <div style="font-weight: 700; color: var(--primary);">{{ $content->title }}</div>
                        <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">الترتيب: #{{ $content->order }}</div>
                    </td>
                    <td style="padding: 20px;">
                        <span style="color: var(--text-light); font-weight: 500;">{{ $content->subject->name_ar }}</span>
                    </td>
                    <td style="padding: 20px;">
                        <div style="display: flex; justify-content: center; gap: 10px;">
                            <!-- أيقونة عرض -->
                            <a href="{{ route('subject.show', $content->subject_id) }}" class="action-icon view" title="عرض">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <!-- أيقونة تعديل -->
                            <a href="{{ route('educational_contents.edit', $content->id) }}" class="action-icon edit" title="تعديل">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <!-- أيقونة حذف -->
                            <button type="button" onclick="deleteContent({{ $content->id }})" class="action-icon delete" title="حذف">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- المكتبات --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function deleteContent(id) {
        Swal.fire({
            title: 'تأكيد الحذف؟',
            text: "لا يمكن التراجع عن هذه الخطوة!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1e3a2b',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إرسال طلب الحذف
                axios.delete(`/educational_contents/${id}`)
                .then(res => {
                    if(res.data.success) {
                        Swal.fire({ icon: 'success', title: 'تم الحذف!', showConfirmButton: false, timer: 1000 });
                        // إخفاء السطر من الجدول
                        const row = document.getElementById(`row_${id}`);
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 400);
                    }
                })
                .catch(err => Swal.fire('خطأ!', 'لا يمكن حذف هذا العنصر حالياً', 'error'));
            }
        });
    }
</script>

<style>
    .action-icon {
        width: 36px; height: 36px; display: grid; place-items: center;
        border-radius: 10px; transition: 0.3s; background: #f8fafc;
        border: 1px solid #e2e8f0; cursor: pointer; text-decoration: none;
    }
    .view { color: #64748b; }
    .edit { color: var(--accent); }
    .delete { color: #ef4444; }
    .action-icon:hover { transform: scale(1.1); background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .table-row { transition: 0.4s; border-bottom: 1px solid #f1f5f9; }
    .table-row:hover { background: #fcfcfd; }
</style>
@endsection
