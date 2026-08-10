@extends('layouts.app') {{-- تأكد من وجود الـ Layout الخاص بك --}}

@section('content')

<!-- استدعاء خط Cairo من Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
<!-- استدعاء FontAwesome للأيقونات -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* إعدادات المتصفح الأساسية */
    :root {
        --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        --glass-bg: rgba(255, 255, 255, 0.9);
        --text-color: #2d3436;
        --soft-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        --hover-shadow: 0 30px 60px rgba(0, 0, 0, 0.12);
        --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .premium-page {
        direction: rtl;
        font-family: 'Cairo', sans-serif;
        background: var(--bg-gradient);
        min-height: 100vh;
        padding: 60px 20px;
    }

    .container {
        max-width: 1250px;
        margin: 0 auto;
    }

    /* هيدر جذاب */
    .hero-section {
        text-align: center;
        margin-bottom: 70px;
    }

    .hero-section h1 {
        font-size: 3.2rem;
        font-weight: 900;
        color: #1e272e;
        margin-bottom: 15px;
        letter-spacing: -1px;
    }

    .hero-section p {
        font-size: 1.2rem;
        color: #57606f;
        max-width: 600px;
        margin: 0 auto;
    }

    /* شبكة المواد */
    .subjects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 40px;
    }

    /* تصميم الكارت الفاخر */
    .premium-card {
        background: var(--glass-bg);
        border-radius: 40px;
        padding: 15px;
        position: relative;
        transition: var(--transition);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: var(--soft-shadow);
        display: flex;
        flex-direction: column;
    }

    .premium-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: var(--hover-shadow);
    }

    /* منطقة الصورة / الأيقونة */
    .image-wrapper {
        width: 100%;
        height: 220px;
        border-radius: 30px;
        overflow: hidden;
        position: relative;
        background: #f1f2f6;
    }

    .subject-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
    }

    .premium-card:hover .subject-img {
        transform: scale(1.1);
    }

    /* في حال عدم وجود صورة، يظهر هذا التدرج مع الأيقونة */
    .icon-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--sub-color), #ffffff);
        position: relative;
    }

    .icon-placeholder i {
        font-size: 5rem;
        color: #fff;
        filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));
    }

    /* محتوى الكارت */
    .card-body {
        padding: 30px 15px 15px 15px;
        text-align: center;
    }

    .subject-key-badge {
        display: inline-block;
        padding: 4px 15px;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--sub-color);
        margin-bottom: 15px;
    }

    .subject-name {
        font-size: 1.8rem;
        font-weight: 800;
        color: #2d3436;
        margin-bottom: 25px;
    }

    /* الإحصائيات بشكل دائري */
    .stats-flex {
        display: flex;
        justify-content: center;
        gap: 25px;
        margin-bottom: 35px;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .stat-circle {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        color: var(--sub-color);
        font-size: 1.2rem;
        border: 1px solid #f1f2f6;
    }

    .stat-text {
        font-size: 0.8rem;
        font-weight: 600;
        color: #747d8c;
    }

    /* الزر "استكمال الدراسة" */
    .action-button {
        background: #1e272e;
        color: #fff;
        text-decoration: none;
        padding: 20px;
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: var(--transition);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .action-button:hover {
        background: var(--sub-color);
        box-shadow: 0 15px 30px rgba(var(--sub-color), 0.4);
    }

    .action-button i {
        font-size: 0.9rem;
        transition: var(--transition);
    }

    .action-button:hover i {
        transform: translateX(-5px);
    }

    /* حالة لا توجد بيانات */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 100px 0;
    }

    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 768px) {
        .hero-section h1 { font-size: 2.2rem; }
        .subjects-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="premium-page">
    <div class="container">

        <!-- الهيدر -->
        <header class="hero-section">
            <h1>موادي الدراسية</h1>
            <p>أهلاً بك في فضاء التعلم الخاص بك، اختر مادة وانطلق نحو النجاح</p>
        </header>

        <!-- شبكة المواد -->
        <div class="subjects-grid">
            @forelse($subjects as $subject)
                @php
                    $color = $subject->color ?? '#6c5ce7'; // لون افتراضي فخم
                @endphp

                <div class="premium-card" style="--sub-color: {{ $color }};">

                    <!-- صورة المادة -->
                    <div class="image-wrapper">
                        @if(isset($subject->image_url) && $subject->image_url)
                            <img src="{{ asset($subject->image_url) }}" class="subject-img" alt="{{ $subject->name_ar }}">
                        @else
                            <div class="icon-placeholder">
                                <i class="{{ $subject->icon ?? 'fas fa-graduation-cap' }}"></i>
                            </div>
                        @endif
                    </div>

                    <!-- تفاصيل المادة -->
                    <div class="card-body">
                        <span class="subject-key-badge">{{ $subject->subject_key }}</span>
                        <h3 class="subject-name">{{ $subject->name_ar }}</h3>

                        <!-- إحصائيات سريعة -->
                        <div class="stats-flex">
                            <div class="stat-item">
                                <div class="stat-circle">
                                    <i class="fas fa-play"></i>
                                </div>
                                <span class="stat-text">{{ $subject->educational_contents_count }} درس</span>
                            </div>
                            <div class="stat-item">
                                <div class="stat-circle">
                                    <i class="fas fa-file-pen"></i>
                                </div>
                                <span class="stat-text">{{ $subject->exams_count }} اختبار</span>
                            </div>
                        </div>

                        <!-- زر الدخول -->
                        <a href="{{ route('student.subjects.show', $subject->id) }}" class="action-button">
                            <span>استكمال التعلم</span>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-folder-open fa-5x" style="opacity: 0.1; margin-bottom: 20px;"></i>
                    <h3>لا توجد مواد مسجلة حالياً</h3>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
