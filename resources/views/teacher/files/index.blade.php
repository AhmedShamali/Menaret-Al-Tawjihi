@extends('layouts.app')

@section('title', 'إدارة ورفع الملازم والملفات | بوابة المعلم')

@section('content')
<div class="ed-teacher-files-container" style="max-width: 1300px; margin: 0 auto; padding: 20px 16px 60px; direction: rtl; font-family: 'Alexandria', sans-serif;">

    <!-- الهيدر -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #64748b; margin-bottom: 6px;">
                <a href="{{ route('teacher.dashboard') }}" style="color: inherit; text-decoration: none;">بوابة المعلم</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 0.7rem;"></i>
                <span style="color: #dc2626; font-weight: 700;">الملازم والملفات الدراسية</span>
            </div>
            <h1 style="margin: 0 0 6px; font-size: 1.8rem; font-weight: 900; color: #0f172a;">
                <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i> إدارة ورفع الملازم وأوراق العمل
            </h1>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                رفع ملازم التوجيهي، أوراق العمل، الملخصات الوزارية بصيغ PDF المعتمدة مع إمكانية التنزيل الفوري للطلبة.
            </p>
        </div>

        <button type="button" onclick="openUploadFileModal()" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; border: none; padding: 12px 24px; border-radius: 14px; font-weight: 800; font-size: 0.92rem; display: flex; align-items: center; gap: 10px; cursor: pointer; box-shadow: 0 8px 20px rgba(220,38,38,0.3); transition: 0.2s;">
            <i class="fa-solid fa-file-arrow-up"></i>
            <span>رفع ملزمة / ملف جديد</span>
        </button>
    </div>

    <!-- شبكة الملفات -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
        @forelse($files as $file)
            <div style="background: white; border-radius: 18px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between; gap: 16px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: #fee2e2; color: #dc2626; display: grid; place-items: center; font-size: 1.5rem; flex-shrink: 0;">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div style="overflow: hidden;">
                            <span style="background: #eff6ff; color: #1d4ed8; padding: 2px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 700; display: inline-block; margin-bottom: 4px;">
                                {{ $file->subject?->name_ar ?? 'عام' }}
                            </span>
                            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $file->title }}">
                                {{ $file->title }}
                            </h3>
                        </div>
                    </div>

                    <div style="font-size: 0.8rem; color: #64748b; line-height: 1.6; background: #f8fafc; padding: 10px 14px; border-radius: 10px; border: 1px solid #f1f5f9;">
                        <div><i class="fa-solid fa-folder" style="color: #d97706;"></i> <strong>القسم:</strong> {{ $file->channel_name ?? 'ملزمة عامة' }}</div>
                        <div><i class="fa-solid fa-weight-hanging" style="color: #6366f1;"></i> <strong>الحجم:</strong> {{ $file->file_size ?? 'PDF وثيقة' }}</div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; pt: 12px; border-top: 1px solid #f1f5f9; margin-top: 8px;">
                    <a href="{{ route('content.download', $file->id) }}" target="_blank" style="padding: 8px 16px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 10px; text-decoration: none; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-download"></i> معاينة / تنزيل
                    </a>

                    <div style="display: flex; gap: 6px;">
                        <a href="{{ route('teacher.educational_contents.edit', $file->id) }}" style="padding: 8px 12px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <button type="button" onclick="deleteFileItem({{ $file->id }})" style="padding: 8px 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; color: #dc2626; cursor: pointer; font-size: 0.8rem;">
                            <i class="fa-solid fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: white; border-radius: 20px; border: 2px dashed #cbd5e1; color: #94a3b8;">
                <i class="fa-solid fa-folder-open" style="font-size: 3rem; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
                <h3 style="margin: 0 0 6px; font-weight: 800; color: #475569;">لا توجد ملازم أو ملفات مرفوعة حالياً</h3>
                <p style="margin: 0 0 20px; font-size: 0.88rem;">اضغط على زر "رفع ملزمة جديدة" لمشاركة أوراق العمل مع طلبتك.</p>
                <button type="button" onclick="openUploadFileModal()" style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer;">
                    رفع أول ملزمة الآن
                </button>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $files->links() }}
    </div>

</div>

<!-- Modal رفع ملزمة / ملف PDF -->
<div id="uploadFileModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; max-width: 520px; width: 100%; padding: 26px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i> رفع ملزمة / ورقة عمل وزارية
            </h3>
            <button type="button" onclick="closeUploadFileModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">✕</button>
        </div>

        <form id="uploadDocForm" onsubmit="submitDocForm(event)">
            @csrf
            <input type="hidden" name="type" value="pdf">

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">المادة الأكاديمية *</label>
                    <select name="subject_id" required style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ $subjectId == $sub->id ? 'selected' : '' }}>{{ $sub->name_ar }} ({{ $sub->stage?->label_ar ?? 'توجيهي' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">عنوان الملزمة / ورقة العمل *</label>
                    <input type="text" name="title" required placeholder="مثال: كراسة التميز الوزارية - حلول الامتحانات السابقة" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">رقم الترتيب *</label>
                        <input type="number" name="order" value="{{ $files->count() + 1 }}" required min="1" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">تصنيف الملزمة</label>
                        <input type="text" name="channel_name" placeholder="مثال: أوراق عمل الوحدة الأولى" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">ملف PDF المعتمد (رفع مباشر) *</label>
                    <input type="file" name="file_upload_pdf" accept="application/pdf,.doc,.docx" required style="width: 100%; padding: 10px; border: 1.5px dashed #cbd5e1; border-radius: 10px; font-family: inherit; font-size: 0.85rem; background: #f8fafc;">
                    <small style="color: #64748b; font-size: 0.72rem; display: block; margin-top: 4px;">يدعم ملفات PDF و Word بحد أقصى 50 ميجابايت.</small>
                </div>

                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">أو رابط PDF سحابي مباشر (اختياري)</label>
                    <input type="url" name="pdf_url" placeholder="https://..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none; direction: ltr; text-align: right;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="closeUploadFileModal()" style="padding: 10px 20px; border-radius: 10px; background: #f1f5f9; color: #64748b; border: none; font-weight: 700; cursor: pointer;">إلغاء</button>
                <button type="submit" id="btnSubmitDoc" style="padding: 10px 24px; border-radius: 10px; background: #dc2626; color: white; border: none; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>حفظ ونشر الملزمة</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
function openUploadFileModal() {
    document.getElementById('uploadFileModal').style.display = 'flex';
}
function closeUploadFileModal() {
    document.getElementById('uploadFileModal').style.display = 'none';
}

async function submitDocForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitDoc');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الرفع...';

    const form = document.getElementById('uploadDocForm');
    const formData = new FormData(form);

    try {
        const res = await axios.post("{{ route('teacher.educational_contents.store') }}", formData);
        Swal.fire({
            icon: 'success',
            title: res.data.title || 'تم الحفظ بنجاح',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#dc2626'
        }).then(() => location.reload());
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        const msg = err.response?.data?.title || err.response?.data?.message || 'حدث خطأ أثناء الرفع';
        Swal.fire({ icon: 'error', title: 'خطأ', text: msg });
    }
}

async function deleteFileItem(id) {
    Swal.fire({
        title: 'حذف الملزمة',
        text: 'هل أنت متأكد من رغبتك في حذف هذا الملف؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'نعم، حذف',
        cancelButtonText: 'تراجع'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(`{{ url('teacher/educational_contents') }}/${id}`, {
                    data: { _token: '{{ csrf_token() }}' }
                });
                Swal.fire({ icon: 'success', title: 'تم الحذف', timer: 1200, showConfirmButton: false })
                    .then(() => location.reload());
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر حذف الملف.' });
            }
        }
    });
}
</script>
@endsection
