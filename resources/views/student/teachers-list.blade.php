@extends('layouts.app')

@section('content')
<!-- CSS مخصص لحل مشاكل التداخل وضمان جمالية التصميم -->
<style>
    .teachers-section {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }

    /* الهيدر العلوي */
    .custom-header {
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        border-right: 5px solid #4361ee;
    }

    /* شبكة المعلمين - استخدام Flexbox لضمان عدم التداخل */
    .teachers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        padding-bottom: 40px;
    }

    /* كرت المعلم */
    .teacher-item {
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    .teacher-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #4361ee;
    }

    /* الصورة الرمزية */
    .avatar-box {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4361ee, #4cc9f0);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 15px;
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        position: relative;
    }

    /* نقطة الحالة (متصل) */
    .status-indicator {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 15px;
        height: 15px;
        background: #2ecc71;
        border: 3px solid #fff;
        border-radius: 50%;
    }

    .teacher-name {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #2b2d42;
    }

    .teacher-info {
        font-size: 0.85rem;
        color: #8d99ae;
        margin-bottom: 20px;
    }

    /* زر المراسلة */
    .chat-link {
        text-decoration: none;
        background: #f8f9fa;
        color: #4361ee;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        width: 100%;
        transition: 0.3s;
        border: 1px solid #eef2ff;
    }

    .chat-link:hover {
        background: #4361ee;
        color: #fff;
    }

    /* المرحلة الدراسية - Badge */
    .stage-pill {
        background: #eef2ff;
        color: #4361ee;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .custom-header { text-align: center; border-right: none; border-top: 5px solid #4361ee; }
        .teachers-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="container py-5 teachers-section" dir="rtl">

    <!-- الهيدر -->
    <div class="custom-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">طاقم التدريس للمرحلة</h2>
            <p class="text-muted mb-0">تواصل مباشرة مع المعلمين المعتمدين لمساعدتك في دروسك.</p>
        </div>
        <div class="stage-pill">
            🏫 {{ $studentStage ?? 'المرحلة الدراسية' }}
        </div>
    </div>

    <!-- شبكة المعلمين -->
    <div class="teachers-grid">
        @forelse($teachers as $teacher)
            <div class="teacher-item">
                <div class="avatar-box">
                    {{ mb_substr($teacher->name, 0, 1) }}
                    <div class="status-indicator"></div>
                </div>

                <div class="teacher-name">{{ $teacher->name }}</div>
                <div class="teacher-info">معلم متخصص • {{ $studentStage }}</div>

                <a href="{{ route('student.chat.teacher', $teacher->id) }}" class="chat-link">
                    <span>💬</span> بدء محادثة
                </a>
            </div>
        @empty
            <!-- حالة عدم وجود بيانات -->
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" style="opacity: 0.2">
                <h5 class="mt-3 text-muted">لا يوجد معلمون متاحون لهذه المرحلة حالياً</h5>
            </div>
        @endforelse
    </div>

</div>
@endsection
