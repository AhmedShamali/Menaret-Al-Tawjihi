@extends('layouts.app')

@section('content')
<style>
    /* استيراد خط كاييرو للمظهر العصري */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');

    :root {
        --main-bg: #f0f2f5;
        --primary-grad: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --accent-color: #764ba2;
        --success-bg: #ecfdf5;
        --success-text: #065f46;
        --success-border: #10b981;
        --text-dark: #2d3436;
        --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    body {
        background-color: var(--main-bg);
        font-family: 'Cairo', sans-serif;
    }

    .wrapper {
        max-width: 900px;
        margin: 50px auto;
        padding: 0 20px;
    }

    /* تصميم رسالة النجاح */
    .alert-custom {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-right: 5px solid var(--success-border);
        background: var(--success-bg);
        color: var(--success-text);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* الرأس الأنيق */
    .header-section {
        background: var(--primary-grad);
        padding: 40px;
        border-radius: 20px 20px 0 0;
        color: white;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-section::before {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .header-title h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.8rem;
    }

    .header-title p {
        margin: 5px 0 0;
        opacity: 0.8;
        font-size: 0.9rem;
    }

    .btn-return {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
        transition: 0.3s;
        border: 1px solid rgba(255,255,255,0.3);
        font-size: 0.9rem;
    }

    .btn-return:hover {
        background: white;
        color: var(--accent-color);
    }

    /* جسم البطاقة */
    .main-card {
        background: white;
        border-radius: 0 0 20px 20px;
        box-shadow: var(--card-shadow);
        padding: 40px;
    }

    /* تنسيق الحقول */
    .input-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }

    .input-box {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .input-box.full {
        grid-column: span 2;
    }

    .input-box label {
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.95rem;
        margin-right: 5px;
    }

    .input-box input, .input-box textarea, .input-box select {
        border: 2px solid #edf2f7;
        padding: 12px 15px;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 1rem;
        transition: 0.3s;
        background: #f8fafc;
    }

    .input-box input:focus, .input-box textarea:focus, .input-box select:focus {
        border-color: var(--accent-color);
        background: white;
        outline: none;
        box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.1);
    }

    /* قسم الصورة */
    .photo-section {
        background: #fdfcfe;
        border: 2px dashed #d1d5db;
        padding: 20px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .preview-img {
        width: 80px;
        height: 80px;
        border-radius: 15px;
        object-fit: cover;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* أزرار التحكم */
    .action-area {
        margin-top: 40px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .btn-save {
        background: var(--primary-grad);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 15px;
        font-weight: 700;
        cursor: pointer;
        font-size: 1.1rem;
        transition: 0.3s;
        box-shadow: 0 10px 15px -3px rgba(118, 75, 162, 0.4);
    }

    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 20px -3px rgba(118, 75, 162, 0.5);
    }

    .btn-cancel {
        background: #fff;
        color: #64748b;
        border: 2px solid #e2e8f0;
        padding: 15px 40px;
        border-radius: 15px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-cancel:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    /* تنبيهات الخطأ */
    .error-tag {
        color: #e53e3e;
        font-size: 0.8rem;
        margin-top: 4px;
    }

    @media (max-width: 768px) {
        .input-grid { grid-template-columns: 1fr; }
        .input-box.full { grid-column: span 1; }
        .header-section { flex-direction: column; text-align: center; gap: 20px; }
    }
</style>

<div class="wrapper" dir="rtl">

    <!-- رسالة النجاح -->
    @if(session('success'))
    <div class="alert-custom">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- الرأس -->
    <div class="header-section">
        <div class="header-title">
            <h2>تعديل ملف المعلم</h2>
            <p>تحديث المعلومات الشخصية والمهنية للمستخدم: {{ $teacher->name }}</p>
        </div>
        <a href="{{ route('admin.teachers.info') }}" class="btn-return">
            رجوع للسجل &larr;
        </a>
    </div>

    <!-- البطاقة الرئيسية -->
    <div class="main-card">
        <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="input-grid">
                <div class="input-box">
                    <label>اسم المعلم</label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required>
                    @error('name') <span class="error-tag">{{ $message }}</span> @enderror
                </div>

                <div class="input-box">
                    <label>البريد الإلكتروني</label>
                    <input type="email" dir="ltr" style="text-align: right;" name="email" value="{{ old('email', $teacher->email) }}" required>
                    @error('email') <span class="error-tag">{{ $message }}</span> @enderror
                </div>

                <div class="input-box">
                    <label>رقم الهاتف</label>
                    <input type="text" dir="ltr" style="text-align: right;" name="phone" value="{{ old('phone', $teacher->phone) }}">
                    @error('phone') <span class="error-tag">{{ $message }}</span> @enderror
                </div>

                <div class="input-box">
                    <label>التخصص الأكاديمي</label>
                    <input type="text" name="major" value="{{ old('major', $teacher->major) }}">
                    @error('major') <span class="error-tag">{{ $message }}</span> @enderror
                </div>

                <!-- حقل المادة الدراسية المرتبطة -->
                <div class="input-box">
                    <label>المادة الدراسية</label>
                    <select name="subject_id">
                        <option value="">اختر المادة الدراسية</option>
                        @foreach($stages as $stage)
                            <optgroup label="{{ $stage->name }}">
                                @foreach($stage->subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $teacher->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('subject_id') <span class="error-tag">{{ $message }}</span> @enderror
                </div>

                <div class="input-box">
                    <label>كلمة المرور (اختياري)</label>
                    <input type="password" name="password" placeholder="اتركها فارغة للأمان">
                </div>

                <div class="input-box">
                    <label>الصورة الشخصية</label>
                    <div class="photo-section">
                        @if($teacher->photo)
                            <img src="{{ asset('storage/' . $teacher->photo) }}" class="preview-img">
                        @endif
                        <input type="file" name="photo">
                    </div>
                </div>

                <div class="input-box full">
                    <label>نبذة تعريفية قصيرة</label>
                    <textarea name="bio" rows="4">{{ old('bio', $teacher->bio) }}</textarea>
                </div>
            </div>

            <div class="action-area">
                <button type="submit" class="btn-save">حفظ التغييرات الآن</button>
                <a href="{{ route('admin.teachers.info') }}" class="btn-cancel">إلغاء الأمر</a>
            </div>
        </form>
    </div>
</div>
@endsection
