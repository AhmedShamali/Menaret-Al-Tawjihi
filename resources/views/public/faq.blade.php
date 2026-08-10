@extends('layouts.app')
@section('title', 'الأسئلة الشائعة')
@section('content')
<div style="max-width: 1000px; margin: 50px auto;">
    <div style="text-align: center; margin-bottom: 60px;">
        <h1 style="font-size: 3rem; font-weight: 900; color: var(--primary);">مركز المساعدة 💡</h1>
        <p style="color: #64748b;">كل ما تحتاج لمعرفته حول تجربة التعلم في جسر.</p>
    </div>

    <div style="display: grid; gap: 20px;">
        @php
            $faqs = [
                ['q' => 'كيف أبدأ الدراسة؟', 'a' => 'بعد تفعيل حسابك، اختر مرحلتك الدراسية ثم المادة، وستجد كل الفيديوهات مرتبة.'],
                ['q' => 'ماذا أفعل إذا نسيت كلمة المرور؟', 'a' => 'استخدم خاصية "نسيت كلمة المرور" من صفحة الدخول، وادخل رقم هويتك لتصلك رسالة عبر واتساب.'],
                ['q' => 'كيف يتم تقييم مستواي؟', 'a' => 'عن طريق "اختبار المستوى" المتاح لكل مادة، والذي يعطيك توصية بالدروس التي تحتاجها.'],
            ];
        @endphp

        @foreach($faqs as $f)
        <details class="glass-card" style="padding: 25px; cursor: pointer;">
            <summary style="font-weight: 800; color: var(--primary); font-size: 1.2rem;">{{ $f['q'] }}</summary>
            <p style="margin-top: 15px; color: #64748b; line-height: 1.8;">{{ $f['a'] }}</p>
        </details>
        @endforeach
    </div>
</div>
@endsection
