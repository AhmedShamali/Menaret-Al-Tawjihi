@extends('layouts.app')

@section('title', 'تعديل ملف الطالب')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; animation: fadeIn 0.6s ease;">

    <!-- رأس الصفحة مع مسار التنقل -->
    <div style="margin-bottom: 35px;">
        <nav style="display: flex; gap: 10px; font-size: 0.85rem; color: var(--text-light); margin-bottom: 10px;">
            <a href="{{ route('students.index') }}" style="color: inherit; text-decoration: none;">إدارة الطلاب</a> /
            <span style="color: var(--accent); font-weight: 600;">تعديل البيانات</span>
        </nav>
        <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--primary);">✏️ تحديث بيانات الطالب</h1>
        <p style="color: var(--text-light);">أنت الآن تقوم بتعديل ملف: <strong style="color: var(--primary);">{{ $student->name_ar }}</strong></p>
    </div>

    <form id="editStudentForm">
        @csrf
        @method('PUT') {{-- ضروري جداً للتحديث --}}

        <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 30px;">

            <!-- العمود الأيمن: البيانات المكتوبة -->
            <div style="display: flex; flex-direction: column; gap: 25px;">

                <!-- القسم الأول: الهوية -->
                <div class="glass-card" style="padding: 35px;">
                    <h3 style="font-size: 1.1rem; color: var(--accent); margin-bottom: 25px; border-right: 4px solid var(--accent); padding-right: 15px;">
                        البيانات الشخصية
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="f-group">
                            <label class="f-label">الاسم الرباعي (عربي)</label>
                            <input type="text" name="name_ar" value="{{ $student->name_ar }}" class="f-input" required>
                        </div>
                        <div class="f-group">
                            <label class="f-label">Full Name (English)</label>
                            <input type="text" name="name_en" value="{{ $student->name_en }}" class="f-input" required>
                        </div>
                        <div class="f-group">
                            <label class="f-label">رقم الهوية</label>
                            <input type="text" name="nid" value="{{ $student->nid }}" maxlength="9" class="f-input" required>
                        </div>
                        <div class="f-group">
                            <label class="f-label">العمر</label>
                            <input type="number" name="age" value="{{ $student->age }}" class="f-input" required>
                        </div>
                    </div>
                </div>

                <!-- القسم الثاني: التواصل -->
                <div class="glass-card" style="padding: 35px;">
                    <h3 style="font-size: 1.1rem; color: var(--accent); margin-bottom: 25px; border-right: 4px solid var(--accent); padding-right: 15px;">
                        التواصل والدخول
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="f-group" style="grid-column: span 2;">
                            <label class="f-label">البريد الإلكتروني</label>
                            <input type="email" name="email" value="{{ $student->email }}" class="f-input" required>
                        </div>
                        <div class="f-group">
                            <label class="f-label">رقم الجوال</label>
                            <input type="tel" name="phone" value="{{ $student->phone }}" class="f-input" required>
                        </div>
                        <div class="f-group">
                            <label class="f-label">رقم الواتساب</label>
                            <input type="tel" name="whatsapp" value="{{ $student->whatsapp }}" class="f-input" required>
                        </div>
                        <div class="f-group" style="grid-column: span 2;">
                            <label class="f-label">كلمة المرور (اتركها فارغة إذا لم ترد التغيير)</label>
                            <input type="password" name="password" class="f-input" placeholder="••••••••">
                        </div>
                    </div>
                </div>
            </div>

            <!-- العمود الأيسر: المرفقات والمسار -->
            <div style="display: flex; flex-direction: column; gap: 25px;">

                <!-- الحالة والمسار -->
                <div class="glass-card" style="padding: 30px;">
                    <h3 style="margin-bottom: 20px; font-size: 1rem;">المسار الدراسي</h3>
                    <div class="f-group" style="margin-bottom: 15px;">
                        <label class="f-label">الصف الحالي</label>
                        <select name="stage_id" class="f-input">
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id }}" {{ $student->stage_id == $stage->id ? 'selected' : '' }}>
                                    {{ $stage->label_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">الجنس</label>
                        <select name="gender" class="f-input">
                            <option {{ $student->gender == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                            <option {{ $student->gender == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                        </select>
                    </div>
                </div>

                <!-- المرفقات الحالية والجديدة -->
                <div class="glass-card" style="padding: 30px;">
                    <h3 style="margin-bottom: 20px; font-size: 1rem;">الصور والوثائق</h3>

                    {{-- عرض الصور الحالية --}}
                    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                        <div class="preview-box">
                            <img src="{{ asset('storage/'.$student->photo) }}" title="الصورة الشخصية">
                            <span>الشخصية</span>
                        </div>
                        <div class="preview-box">
                            <img src="{{ asset('storage/'.$student->id_photo) }}" title="صورة الهوية">
                            <span>الهوية</span>
                        </div>
                    </div>

                    <div class="up-zone">
                        <input type="file" name="photo" id="p_file" hidden>
                        <label for="p_file">🔄 تغيير الشخصية</label>
                    </div>
                    <div class="up-zone" style="margin-top: 10px;">
                        <input type="file" name="id_photo" id="i_file" hidden>
                        <label for="i_file">🔄 تغيير الهوية</label>
                    </div>
                </div>

                <!-- أزرار التحكم -->
                <button type="button" onclick="performUpdate({{ $student->id }})" id="saveBtn" class="btn btn-primary" style="width: 100%; padding: 20px; font-size: 1.1rem; border-radius: 20px;">
                    حفظ التغييرات ✅
                </button>
            </div>

        </div>
    </form>
</div>

<style>
    .f-label { display: block; font-weight: 700; font-size: 0.85rem; color: var(--primary); margin-bottom: 8px; margin-right: 5px; }
    .f-input { width: 100%; padding: 14px; border-radius: 15px; border: 2px solid #f1f5f9; font-family: inherit; transition: 0.3s; background: #f8fafc; outline: none; }
    .f-input:focus { border-color: var(--accent); background: white; box-shadow: 0 10px 20px rgba(0,0,0,0.02); }

    .preview-box { flex: 1; text-align: center; background: #f8fafc; padding: 10px; border-radius: 15px; border: 1px solid #e2e8f0; }
    .preview-box img { width: 100%; height: 60px; object-fit: cover; border-radius: 10px; margin-bottom: 5px; }
    .preview-box span { font-size: 0.65rem; font-weight: 700; color: var(--text-light); }

    .up-zone { border: 2px dashed #e2e8f0; border-radius: 15px; text-align: center; padding: 12px; cursor: pointer; transition: 0.3s; }
    .up-zone:hover { border-color: var(--accent); background: #ecfdf5; }
    .up-zone label { cursor: pointer; font-weight: 700; color: var(--text-light); font-size: 0.8rem; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

{{-- الجافاسكربت الخاص بالتحديث --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function performUpdate(id) {
        const btn = document.getElementById('saveBtn');
        const form = document.getElementById('editStudentForm');
        const formData = new FormData(form);

        // لمحاكاة طلب PUT في لارافيل عند رفع الملفات
        formData.append('_method', 'PUT');

        btn.disabled = true;
        btn.textContent = 'جاري الحفظ...';

        axios.post(`/students/${id}`, formData)
        .then(function (res) {
            Swal.fire({
                icon: 'success',
                title: 'تم تحديث البيانات بنجاح 🚀',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "{{ route('students.index') }}";
            });
        })
        .catch(function (error) {
            Swal.fire({
                icon: 'error',
                title: error.response.data.title || 'حدث خطأ في التحديث',
            });
            btn.disabled = false;
            btn.textContent = 'حفظ التغييرات ✅';
        });
    }
</script>
@endsection
