@extends('layouts.app')

@section('title', 'إدارة بيانات الطالب')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; animation: slideUp 0.6s ease;">

    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">
            {{ isset($student) ? '✏️ تعديل بيانات الطالب' : '🚀 تسجيل طالب جديد' }}
        </h1>
        <p style="color: var(--text-light);">قم بتعبئة البيانات الـ 10 المطلوبة بدقة لضمان تفعيل الحساب.</p>
    </div>

    <form id="studentForm">
        @csrf
        @if(isset($student)) @method('PUT') @endif

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">

            <div style="display: flex; flex-direction: column; gap: 25px;">
                <!-- بطاقة المعلومات -->
                <div class="glass-card" style="padding: 40px; background: white; border: none;">
                    <h3 style="margin-bottom: 25px; color: var(--accent); border-right: 4px solid var(--accent); padding-right: 15px;">• البيانات الشخصية</h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="f-label">الاسم الرباعي (بالعربية)</label>
                            <input type="text" name="name_ar" value="{{ $student->name_ar ?? '' }}" class="f-input" required>
                        </div>
                        <div>
                            <label class="f-label">Full Name (English)</label>
                            <input type="text" name="name_en" value="{{ $student->name_en ?? '' }}" class="f-input" required>
                        </div>
                        <div>
                            <label class="f-label">رقم الهوية</label>
                            <input type="text" name="nid" value="{{ $student->nid ?? '' }}" maxlength="9" class="f-input" required>
                        </div>
                        <div>
                            <label class="f-label">العمر</label>
                            <input type="number" name="age" value="{{ $student->age ?? '' }}" class="f-input" required>
                        </div>
                    </div>

                    <h3 style="margin: 35px 0 25px; color: var(--accent); border-right: 4px solid var(--accent); padding-right: 15px;">• التواصل والدخول</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div style="grid-column: span 2;">
                            <label class="f-label">البريد الإلكتروني</label>
                            <input type="email" name="email" value="{{ $student->email ?? '' }}" class="f-input" placeholder="name@student.ps" required>
                        </div>
                        <div>
                            <label class="f-label">رقم الجوال</label>
                            <input type="tel" name="phone" value="{{ $student->phone ?? '' }}" class="f-input" required>
                        </div>
                        <div>
                            <label class="f-label">رقم الواتساب</label>
                            <input type="tel" name="whatsapp" value="{{ $student->whatsapp ?? '' }}" class="f-input" required>
                        </div>
                        <div style="grid-column: span 2;">
                            <label class="f-label">كلمة المرور {{ isset($student) ? '(اتركها فارغة إذا لم ترد التغيير)' : '' }}</label>
                            <input type="password" name="password" class="f-input">
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 25px;">
                <!-- المسار التعليمي -->
                <div class="glass-card" style="padding: 30px;">
                    <h3 style="margin-bottom: 20px; font-size: 1.1rem;">المسار الدراسي</h3>
                    <label class="f-label">الصف الدراسي</label>
                    <select name="stage_id" class="f-input" required>
                        <option value="">اختر صفك...</option>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->id }}" {{ (isset($student) && $student->stage_id == $stage->id) ? 'selected' : '' }}>
                                {{ $stage->label_ar }}
                            </option>
                        @endforeach
                    </select>
                    <label class="f-label" style="margin-top: 15px;">الجنس</label>
                    <select name="gender" class="f-input">
                        <option {{ (isset($student) && $student->gender == 'ذكر') ? 'selected' : '' }}>ذكر</option>
                        <option {{ (isset($student) && $student->gender == 'أنثى') ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>

                <!-- المرفقات -->
                <div class="glass-card" style="padding: 30px;">
                    <h3 style="margin-bottom: 20px; font-size: 1.1rem;">الصور والوثائق</h3>
                    @if(isset($student))
                        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                            <img src="{{ asset('storage/'.$student->photo) }}" style="width: 50px; height: 50px; border-radius: 10px;">
                            <img src="{{ asset('storage/'.$student->id_photo) }}" style="width: 50px; height: 50px; border-radius: 10px;">
                        </div>
                    @endif
                    <div class="up-zone">
                        <input type="file" name="photo" id="p" hidden>
                        <label for="p">📸 الصورة الشخصية</label>
                    </div>
                    <div class="up-zone" style="margin-top: 10px;">
                        <input type="file" name="id_photo" id="i" hidden>
                        <label for="i">🪪 صورة الهوية</label>
                    </div>
                </div>

                <button type="button" onclick="saveStudent()" id="saveBtn" class="btn btn-primary" style="width: 100%; padding: 20px; font-size: 1.2rem; border-radius: 20px;">
                    {{ isset($student) ? 'تحديث البيانات ✅' : 'نشر الطلب الآن 🚀' }}
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .f-label { display: block; font-weight: 700; font-size: 0.85rem; color: var(--primary); margin-bottom: 8px; }
    .f-input { width: 100%; padding: 15px; border-radius: 15px; border: 2px solid #f1f5f9; font-family: inherit; transition: 0.3s; background: #f8fafc; }
    .f-input:focus { border-color: var(--accent); background: white; outline: none; }
    .up-zone { border: 2px dashed #e2e8f0; border-radius: 15px; text-align: center; padding: 15px; cursor: pointer; }
    .up-zone:hover { border-color: var(--accent); background: #ecfdf5; }
    .up-zone label { cursor: pointer; font-weight: 700; color: var(--text-light); font-size: 0.85rem; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function saveStudent() {
        const btn = document.getElementById('saveBtn');
        const formData = new FormData(document.getElementById('studentForm'));
        @if(isset($student)) formData.append('_method', 'PUT'); @endif

        btn.disabled = true;
        axios.post("{{ isset($student) ? route('students.update', $student->id) : route('students.store') }}", formData)
        .then(res => {
            Swal.fire({ icon: 'success', title: res.data.title }).then(() => location.href = "{{ route('students.index') }}");
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: err.response.data.title });
            btn.disabled = false;
        });
    }
</script>
@endsection
