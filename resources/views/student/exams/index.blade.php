@extends('layouts.app')

@section('title', 'قاعة الاختبارات الرقمية')

@section('content')
<div style="display: flex; flex-direction: column; gap: 40px; animation: fadeIn 0.8s ease;">

    <!-- رأس الصفحة الفخم -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; background: white; padding: 40px; border-radius: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.02);">
        <div>
            <div class="hero-badge">بوابة التقييم الأكاديمي</div>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--primary); margin-top: 15px; letter-spacing: -1px;">اختباراتي المتاحة 📝</h1>
            <p style="color: var(--text-light); font-size: 1.1rem; margin-top: 10px;">هنا تجد كافة الاختبارات والتقييمات المقررة لك في مساقاتك الحالية.</p>
        </div>
        <div style="text-align: left;">
            <div style="font-size: 0.8rem; color: var(--text-light); font-weight: 700; margin-bottom: 5px;">إجمالي الاختبارات</div>
            <div style="font-size: 2.2rem; font-weight: 900; color: var(--accent);">{{ $exams->count() }}</div>
        </div>
    </div>

    <!-- شبكة الاختبارات (Exams Grid) -->
    @if($exams->isEmpty())
        <div class="glass-card" style="padding: 100px; text-align: center; border: none; background: white;">
            <div style="font-size: 5rem; margin-bottom: 20px; opacity: 0.3;">🍃</div>
            <h2 style="color: var(--primary); font-weight: 700;">لا توجد اختبارات متاحة حالياً</h2>
            <p style="color: var(--text-light);">سيقوم المدرسون بنشر الاختبارات فور جهوزيتها، تابعنا دائماً.</p>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
            @foreach($exams as $exam)
            <div class="glass-card exam-card" style="padding: 0; overflow: hidden; border: none; background: white; transition: 0.4s;">

                {{-- شريط ملون علوي يطابق لون المادة --}}
                <div style="height: 8px; background: {{ $exam->subject->color ?? 'var(--accent)' }};"></div>

                <div style="padding: 35px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
                        <span class="subject-chip" style="background: {{ $exam->subject->color ?? 'var(--accent)' }}15; color: {{ $exam->subject->color ?? 'var(--accent)' }};">
                            {{ $exam->subject->name_ar }}
                        </span>
                        <div class="duration-tag">
                            ⏱ {{ $exam->duration_minutes }} دقيقة
                        </div>
                    </div>

                    <h3 style="font-size: 1.4rem; font-weight: 800; color: var(--primary); margin-bottom: 12px; line-height: 1.4;">
                        {{ $exam->title }}
                    </h3>

                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px; font-size: 0.85rem; color: var(--text-light);">
                        <span>📄 {{ $exam->questions_count ?? $exam->questions->count() }} أسئلة</span>
                        <span style="opacity: 0.3;">|</span>
                        <span>🎓 درجة الاختبار: {{ $exam->questions->sum('points') }}</span>
                    </div>

                    <div style="padding-top: 20px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
                        <a href="{{ route('student.exams.take', $exam->id) }}" class="start-exam-btn">
                            دخول الاختبار
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 5px;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        </a>
                        <span style="font-size: 0.7rem; font-weight: 700; color: #10b981; display: flex; align-items: center; gap: 5px;">
                            <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%;"></span>
                            متاح حالياً
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>

<style>
    .hero-badge {
        display: inline-block; padding: 6px 16px; background: var(--accent); color: white;
        border-radius: 10px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
    }

    .exam-card:hover { transform: translateY(-10px); box-shadow: 0 25px 60px rgba(0,0,0,0.06); }

    .subject-chip { padding: 6px 15px; border-radius: 10px; font-size: 0.8rem; font-weight: 800; }

    .duration-tag { font-size: 0.8rem; font-weight: 700; color: var(--text-light); background: #f8fafc; padding: 6px 12px; border-radius: 10px; }

    .start-exam-btn {
        display: flex; align-items: center; text-decoration: none;
        color: var(--accent); font-weight: 800; font-size: 0.95rem; transition: 0.3s;
    }
    .start-exam-btn:hover { color: var(--primary); transform: translateX(-5px); }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
