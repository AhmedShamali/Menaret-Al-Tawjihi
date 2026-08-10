@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 40px; animation: fadeIn 0.8s ease;">

    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;">

        {{-- قسم الشهادات --}}
        <div>
            <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 25px;">أوسمة التميز والشهادات 🏆</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                @foreach($certificates as $cert)
                <div class="glass-card certificate-mini" onclick="window.print()">
                    <div class="cert-icon">🎓</div>
                    <h4>شهادة إتمام: {{ $cert->subject->name_ar }}</h4>
                    <span class="code">{{ $cert->certificate_code }}</span>
                    <div class="grade">الدرجة: {{ $cert->final_grade }}%</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- قسم التوصيات الذكية (هاد اللي بهر المشرفين) --}}
        <div class="glass-card" style="padding: 35px; background: #0f172a; color: white;">
            <h3 style="color: var(--accent); margin-bottom: 20px;">💡 خطة تقوية مقترحة</h3>
            <p style="font-size: 0.85rem; opacity: 0.7; margin-bottom: 25px;">بناءً على نتائج اختباراتك الأخيرة، ننصحك بمراجعة هذه الدروس:</p>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @foreach($recommendations as $rec)
                <div style="padding: 15px; background: rgba(255,255,255,0.05); border-radius: 15px; border-right: 4px solid var(--accent);">
                    <div style="font-weight: 700; font-size: 0.9rem;">{{ $rec->content->title }}</div>
                    <span style="font-size: 0.7rem; color: var(--accent);">{{ $rec->reason }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .certificate-mini { padding: 30px; text-align: center; border: 2px solid #f1f5f9; cursor: pointer; transition: 0.3s; }
    .certificate-mini:hover { border-color: var(--accent); transform: translateY(-5px); }
    .cert-icon { font-size: 3rem; margin-bottom: 15px; }
    .certificate-mini h4 { font-size: 1rem; margin-bottom: 10px; }
    .code { font-family: monospace; font-size: 0.7rem; color: #94a3b8; }
    .grade { margin-top: 15px; font-weight: 800; color: var(--accent); }
</style>
@endsection
