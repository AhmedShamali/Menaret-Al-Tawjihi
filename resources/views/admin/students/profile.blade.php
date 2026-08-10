@extends('layouts.app')

@section('title', 'ملفي الشخصي')

@section('content')
<div class="profile-wrapper">
    <!-- رأس الصفحة -->
    <div class="profile-header">
        <div class="welcome-text">
            <h1>مرحباً بك، {{ explode(' ', $student->name_ar)[0] ?? 'طالبنا' }} 👋</h1>
            <p>إليك نظرة عامة على بياناتك المسجلة في النظام</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="back-link">
            <span>&larr;</span> العودة للرئيسية
        </a>
    </div>

    <div class="profile-grid">
        <!-- الكارت الجانبي: الصورة والمعلومات الأساسية -->
        <div class="side-card">
            <div class="avatar-container">
                {{-- فحص: إذا كانت الصورة موجودة ولا تحتوي على مسار الويندوز المؤقت القديم --}}
                @if($student->photo && !str_contains($student->photo, 'C:'))
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="صورة الطالب" class="main-avatar">
                @else
                    <div class="avatar-placeholder">
                        {{ $student ? mb_substr($student->name_ar, 0, 1) : 'S' }}
                    </div>
                @endif
                <div class="status-indicator {{ $student->status == 'active' ? 'active' : 'pending' }}"></div>
            </div>

            <h2 class="name-display">{{ $student->name_ar ?? 'غير متوفر' }}</h2>
            <p class="email-display">{{ $student->email ?? 'لا يوجد بريد إلكتروني' }}</p>

            <div class="badge-group">
                <span class="badge badge-primary">حساب طالب</span>
                <span class="badge {{ $student->status == 'active' ? 'badge-success' : 'badge-warning' }}">
                    {{ $student->status == 'active' ? 'حساب نشط' : 'قيد المراجعة' }}
                </span>
            </div>

            <!-- صورة الهوية -->
            <div class="id-card-preview">
                <p>صورة الهوية الوطنية</p>
                @if($student->id_photo && !str_contains($student->id_photo, 'C:'))
                    <a href="{{ asset('storage/' . $student->id_photo) }}" target="_blank" title="اضغط للتكبير">
                        <img src="{{ asset('storage/' . $student->id_photo) }}" alt="الهوية">
                    </a>
                @else
                    <div class="no-id">
                        <i>🪪</i>
                        <span>لا توجد صورة هوية صالحة</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- الكارت الرئيسي: التفاصيل -->
        <div class="details-content">
            <!-- قسم المعلومات الشخصية -->
            <div class="info-section">
                <div class="section-title">
                    <span class="icon">👤</span>
                    <h3>المعلومات الشخصية</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>الاسم بالكامل (عربي)</label>
                        <p>{{ $student->name_ar ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Full Name (English)</label>
                        <p>{{ $student->name_en ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>الرقم الوطني / الهوية</label>
                        <p>{{ $student->nid ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>رقم الهاتف</label>
                        <p class="ltr-text">{{ $student->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- قسم المعلومات الأكاديمية -->
            <div class="info-section">
                <div class="section-title">
                    <span class="icon">🎓</span>
                    <h3>المسار الأكاديمي</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>المرحلة الدراسية</label>
                        <p>{{ $student->stage->label_ar ?? 'غير محدد' }}</p>
                    </div>
                    <div class="info-item">
                        <label>تاريخ التسجيل في النظام</label>
                        <p>{{ $student->created_at ? $student->created_at->format('Y/m/d') : '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>الجنس</label>
                        <p>{{ $student->gender ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>العمر</label>
                        <p>{{ $student->age ?? '—' }} سنة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* المتغيرات التصميمية */
    :root {
        --primary: #4361ee;
        --secondary: #3f37c9;
        --success: #10b981;
        --warning: #f59e0b;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --bg-body: #f8fafc;
        --white: #ffffff;
        --border: #e2e8f0;
    }

    .profile-wrapper {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 20px;
        direction: rtl;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .welcome-text h1 { color: var(--primary); margin: 0; font-size: 1.8rem; font-weight: 800; }
    .welcome-text p { color: var(--text-light); margin-top: 5px; }

    .back-link {
        background: var(--white);
        padding: 10px 20px;
        border-radius: 12px;
        text-decoration: none;
        color: var(--text-dark);
        border: 1px solid var(--border);
        font-weight: 600;
        transition: 0.3s;
    }
    .back-link:hover { background: var(--primary); color: white; border-color: var(--primary); }

    /* شبكة العرض */
    .profile-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 25px;
    }

    /* الكارت الجانبي */
    .side-card {
        background: var(--white);
        border-radius: 24px;
        padding: 40px 25px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        height: fit-content;
    }

    .avatar-container {
        position: relative;
        width: 130px;
        height: 130px;
        margin: 0 auto 20px;
    }
    .main-avatar {
        width: 100%; height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--white);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .avatar-placeholder {
        width: 100%; height: 100%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
    }
    .status-indicator {
        position: absolute; bottom: 8px; right: 8px;
        width: 20px; height: 20px;
        border-radius: 50%;
        border: 3px solid var(--white);
    }
    .status-indicator.active { background: var(--success); }
    .status-indicator.pending { background: var(--warning); }

    .name-display { font-size: 1.4rem; color: var(--text-dark); margin: 10px 0 5px; }
    .email-display { color: var(--text-light); font-size: 0.9rem; margin-bottom: 20px; }

    .badge-group { display: flex; gap: 8px; justify-content: center; margin-bottom: 25px; }
    .badge { padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; }
    .badge-primary { background: #eef2ff; color: var(--primary); }
    .badge-success { background: #ecfdf5; color: var(--success); }
    .badge-warning { background: #fffbeb; color: var(--warning); }

    .id-card-preview {
        padding-top: 20px;
        border-top: 1px dashed var(--border);
    }
    .id-card-preview p { font-size: 0.9rem; color: var(--text-light); margin-bottom: 12px; font-weight: 600; }
    .id-card-preview img { width: 100%; border-radius: 12px; cursor: pointer; transition: 0.3s; border: 1px solid var(--border); }
    .id-card-preview img:hover { transform: scale(1.02); }
    .no-id { background: #f1f5f9; padding: 20px; border-radius: 12px; color: var(--text-light); display: flex; flex-direction: column; gap: 8px; }
    .no-id i { font-style: normal; font-size: 1.5rem; }

    /* كروت التفاصيل */
    .info-section {
        background: var(--white);
        border-radius: 24px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
    }
    .section-title { display: flex; align-items: center; gap: 12px; margin-bottom: 25px; }
    .section-title .icon {
        background: #f1f5f9; width: 40px; height: 40px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px; font-size: 1.2rem;
    }
    .section-title h3 { margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--text-dark); }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
    .info-item label { display: block; color: var(--text-light); font-size: 0.85rem; margin-bottom: 8px; font-weight: 600; }
    .info-item p { margin: 0; font-weight: 700; color: var(--text-dark); font-size: 1.05rem; }
    .ltr-text { direction: ltr; text-align: right; display: block; }

    /* التجاوب مع الجوال */
    @media (max-width: 850px) {
        .profile-grid { grid-template-columns: 1fr; }
        .side-card { order: -1; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection
