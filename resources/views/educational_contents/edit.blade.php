@extends('layouts.app')

@section('title', 'تعديل المحتوى التعليمي')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; animation: fadeIn 0.6s ease;">

    <div style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">✏️ تعديل المحتوى</h1>
            <p style="color: var(--text-light);">أنت الآن تقوم بتعديل: <strong style="color: var(--accent);">{{ $content->title }}</strong></p>
        </div>
        <a href="{{ route('educational_contents.index') }}" class="btn" style="background: white; border: 1px solid #e2e8f0; color: var(--text-dark);">إلغاء والعودة</a>
    </div>

    <!-- فورم التعديل الذكي -->
    <form id="editForm">
        @csrf
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;">

            <div style="display: flex; flex-direction: column; gap: 25px;">
                <div class="glass-card" style="padding: 35px;">
                    <h3 style="margin-bottom: 25px; font-size: 1.2rem;">📦 البيانات الأساسية</h3>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div>
                            <label class="f-label">عنوان الدرس</label>
                            <input type="text" name="title" value="{{ $content->title }}" class="f-input" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label class="f-label">المرحلة الدراسية</label>
                                <select id="stage_select" class="f-input">
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}" {{ $content->subject->stage_id == $stage->id ? 'selected' : '' }}>
                                            {{ $stage->label_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="f-label">المادة الدراسية</label>
                                <select name="subject_id" id="subject_select" class="f-input" required>
                                    @foreach($stages->find($content->subject->stage_id)->subjects as $sub)
                                        <option value="{{ $sub->id }}" {{ $content->subject_id == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div style="background: #f8fafc; padding: 20px; border-radius: 20px; border: 2px dashed #e2e8f0;">
                            <label class="f-label">الرابط أو الرفع الجديد</label>
                            <input type="text" name="url_path" value="{{ $content->url_path }}" class="f-input" style="margin-bottom: 10px;">

                            @if($content->type == 'video')
                                <input type="file" name="file_upload_video" class="f-input" style="background: white;">
                            @else
                                <input type="file" name="file_upload_pdf" class="f-input" style="background: white;">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="glass-card" style="padding: 30px;">
                    <label class="f-label">نوع المحتوى</label>
                    <select name="type" class="f-input">
                        <option value="video" {{ $content->type == 'video' ? 'selected' : '' }}>🎥 فيديو</option>
                        <option value="file" {{ $content->type == 'file' ? 'selected' : '' }}>📄 ملف PDF</option>
                    </select>
                </div>

                <div class="glass-card" style="padding: 30px;">
                    <div style="margin-bottom: 15px;">
                        <label class="f-label">اسم القناة</label>
                        <input type="text" name="channel_name" value="{{ $content->channel_name }}" class="f-input">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label class="f-label">حجم الملف</label>
                        <input type="text" name="file_size" value="{{ $content->file_size }}" class="f-input">
                    </div>
                    <div>
                        <label class="f-label">الترتيب</label>
                        <input type="number" name="order" value="{{ $content->order }}" class="f-input">
                    </div>
                </div>

                <button type="button" onclick="performUpdate({{ $content->id }})" id="saveBtn" class="btn btn-primary" style="width: 100%; padding: 20px; font-size: 1.1rem;">
                    حفظ التعديلات الآن ✅
                </button>
            </div>
        </div>
    </form>
</div>

{{-- المكتبات المطلوبة --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. منطق تبديل المواد بناء على المرحلة
    const stages = @json($stages);
    document.getElementById('stage_select').addEventListener('change', function() {
        const subSel = document.getElementById('subject_select');
        subSel.innerHTML = '';
        const stage = stages.find(s => s.id == this.value);
        stage.subjects.forEach(sub => {
            subSel.innerHTML += `<option value="${sub.id}">${sub.name_ar}</option>`;
        });
    });

    // 2. وظيفة التحديث باستخدام AJAX (تمنع الصفحة البيضاء تماماً)
    function performUpdate(id) {
        const btn = document.getElementById('saveBtn');
        const form = document.getElementById('editForm');
        const formData = new FormData(form);

        // لمحاكاة طلب PUT في لارافيل مع رفع الملفات
        formData.append('_method', 'PUT');

        btn.disabled = true;
        btn.textContent = 'جاري التحديث...';

        axios.post(`/educational_contents/${id}`, formData)
        .then(function (response) {
            Swal.fire({
                icon: 'success',
                title: response.data.title,
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "{{ route('educational_contents.index') }}";
            });
        })
        .catch(function (error) {
            Swal.fire({
                icon: 'error',
                title: error.response.data.title || 'حدث خطأ ما',
            });
            btn.disabled = false;
            btn.textContent = 'حفظ التعديلات الآن ✅';
        });
    }
</script>

<style>
    .f-label { display: block; font-weight: 700; font-size: 0.85rem; color: var(--primary); margin-bottom: 8px; }
    .f-input { width: 100%; padding: 14px; border-radius: 15px; border: 2px solid #f1f5f9; font-family: inherit; transition: 0.3s; }
    .f-input:focus { border-color: var(--accent); outline: none; background: #fff; }
</style>
@endsection
