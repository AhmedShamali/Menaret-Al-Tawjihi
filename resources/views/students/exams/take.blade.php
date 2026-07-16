@extends('layouts.app')
@section('content')
<div style="max-width: 950px; margin: 0 auto;">
    {{-- هيدر المؤقت الاحترافي --}}
    <div style="background: #0f172a; color: white; padding: 35px 50px; border-radius: 30px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 20px; z-index: 1000; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
        <div>
            <h2 style="font-size: 1.4rem; font-weight: 800;">{{ $exam->title }}</h2>
            <p style="font-size: 0.85rem; opacity: 0.6; margin-top: 5px;">{{ $exam->subject->name_ar }} • نظام التقييم الرقمي</p>
        </div>
        <div style="background: rgba(255,255,255,0.08); padding: 15px 35px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); text-align: center;">
            <div style="font-size: 0.7rem; opacity: 0.7; text-transform: uppercase; letter-spacing: 2px;">المتبقي</div>
            <div id="timer" style="font-size: 2.2rem; font-weight: 900; font-family: monospace; color: #10b981;">{{ $exam->duration_minutes }}:00</div>
        </div>
    </div>

    <form id="examForm" style="margin-top: 50px; display: flex; flex-direction: column; gap: 35px;">
        @csrf
        @foreach($exam->questions as $idx => $q)
        <div class="glass-card" style="padding: 55px; border-radius: 40px; border: none; background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
                <span style="font-weight: 900; color: #10b981; font-size: 1.2rem; background: #ecfdf5; padding: 5px 20px; border-radius: 12px;">سؤال #{{ $idx + 1 }}</span>
                <span style="font-weight: 700; color: #94a3b8;">{{ $q->points }} نقاط</span>
            </div>

            <h3 style="font-size: 1.7rem; line-height: 1.7; color: #1e293b; margin-bottom: 45px; font-weight: 600;">{{ $q->question_text }}</h3>

            @if($q->type == 'mcq')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    @foreach(['a','b','c','d'] as $o)
                    <label class="q-opt">
                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $o }}" hidden>
                        <div class="q-box">
                            <span class="q-bullet">{{ strtoupper($o) }}</span>
                            <span style="font-weight: 500;">{{ $q->$o }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            @else
                <textarea name="answers[{{ $q->id }}]" class="u-input" rows="7" placeholder="اكتب إجابتك هنا بوضوح..." style="background: #f8fafc; font-size: 1.1rem;"></textarea>
                @if($q->require_file)
                    <div style="margin-top: 25px; border: 3px dashed #cbd5e1; padding: 45px; border-radius: 25px; text-align: center; background: #fdfdfd; transition: 0.3s;" onmouseover="this.style.borderColor='#10b981'">
                        <input type="file" name="files[{{ $q->id }}]" id="f_{{ $q->id }}" hidden>
                        <label for="f_{{ $q->id }}" style="cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 15px;">
                            <span style="font-size: 3rem;">📁</span>
                            <strong style="font-size: 1.1rem; color: #0f172a;">ارفع ملف الحل (PDF / صور)</strong>
                            <span style="color: #64748b;">سيتم إرفاق الملف مع إجابتك المكتوبة</span>
                        </label>
                    </div>
                @endif
            @endif
        </div>
        @endforeach

        <button type="button" onclick="submitExam()" class="btn btn-primary" style="padding: 25px; font-size: 1.5rem; border-radius: 30px; box-shadow: 0 20px 40px rgba(16, 185, 129, 0.25); margin-bottom: 50px;">إنهاء وتسليم الاختبار 🚩</button>
    </form>
</div>

<style>
    .q-opt input:checked + .q-box { border-color: #10b981; background: #f0fdf4; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08); }
    .q-box { display: flex; align-items: center; gap: 15px; padding: 25px; border: 2px solid #f1f5f9; border-radius: 20px; cursor: pointer; transition: 0.3s; background: white; }
    .q-bullet { width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: grid; place-items: center; font-weight: 900; font-size: 0.9rem; color: #475569; }
    .q-opt input:checked + .q-box .q-bullet { background: #10b981; color: white; }
</style>
@endsection
