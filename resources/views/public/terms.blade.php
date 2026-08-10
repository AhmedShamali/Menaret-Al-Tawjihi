@extends('layouts.app')
@section('title', 'شروط الاستخدام')
@section('content')
<div style="max-width: 900px; margin: 50px auto; animation: slideUp 0.8s ease;">
    <div class="glass-card" style="padding: 60px; border: none; background: white; border-top: 10px solid var(--primary);">
        <h1 style="font-size: 2.5rem; font-weight: 900; color: var(--primary); margin-bottom: 30px;">اتفاقية الاستخدام ⚖️</h1>
        <div style="line-height: 2; color: #475569; font-size: 1.1rem;">
            <div class="term-box" style="margin-bottom: 25px; padding: 20px; background: #f8fafc; border-radius: 15px;">
                <strong>المحتوى التعليمي:</strong> جميع الفيديوهات والملفات ملكية خاصة للمنصة ويمنع نشرها أو تداولها خارج الموقع.
            </div>
            <div class="term-box" style="margin-bottom: 25px; padding: 20px; background: #f8fafc; border-radius: 15px;">
                <strong>السلوك الأكاديمي:</strong> يلتزم الطالب بالأمانة العلمية في الاختبارات، وأي محاولة غش تعرض الحساب للحظر النهائي.
            </div>
        </div>
    </div>
</div>
@endsection
