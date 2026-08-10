@extends('layouts.app')
@section('title', 'سياسة الخصوصية')
@section('content')
<div style="max-width: 900px; margin: 50px auto; animation: slideUp 0.8s ease;">
    <div class="glass-card" style="padding: 60px; border: none; background: white; border-top: 10px solid var(--accent);">
        <h1 style="font-size: 2.5rem; font-weight: 900; color: var(--primary); margin-bottom: 30px;">سياسة الخصوصية 🛡️</h1>
        <div style="line-height: 2; color: #475569; font-size: 1.1rem;">
            <p style="margin-bottom: 25px; font-weight: 600;">في {{ \App\Models\Setting::get('site_name') }}، نعتبر خصوصية الطالب أولوية قصوى. يوضح هذا المستند كيف نعالج بياناتك:</p>

            <h3 style="color: var(--primary); margin-bottom: 15px;">1. البيانات التي نجمعها</h3>
            <p>نجمع بيانات الهوية الرسمية فقط لضمان أمان المنصة ومنع الحسابات الوهمية. يتم تشفير هذه الصور ولا تظهر لأي مستخدم آخر.</p>

            <h3 style="color: var(--primary); margin: 30px 0 15px;">2. محادثات الذكاء الاصطناعي</h3>
            <p>كافة استفساراتك التعليمية مع المساعد الذكي يتم تحليلها برمجياً لتحسين جودة الإجابات، ولا يتم مشاركتها مع أي جهة خارجية.</p>

            <div style="margin-top: 50px; padding: 25px; background: #f0fdf4; border-radius: 20px; color: #166534; font-weight: 700; display: flex; align-items: center; gap: 15px;">
                <span>✅</span> نحن نلتزم بالمعايير العالمية لحماية البيانات الأكاديمية.
            </div>
        </div>
    </div>
</div>
@endsection
