@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 0 auto; animation: fadeIn 0.5s ease;">

    <div class="uni-card" style="border-top: 5px solid #2563eb;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <span class="chip" style="background: #f1f5f9; color: #1e293b; font-weight: 800;">الدرجة: {{ $question->points }} نقاط</span>
            <span style="font-size: 0.85rem; color: #64748b;">تاريخ النشر: {{ $question->created_at->format('Y/m/d') }}</span>
        </div>

        <h2 style="font-size: 1.5rem; line-height: 1.6; color: #1e293b; margin-bottom: 40px;">
            {{ $question->question_text }}
        </h2>

        <form id="submissionForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">

            @if($question->type == 'mcq')
                <!-- خيارات الطالب -->
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    @foreach(['a','b','c','d'] as $opt)
                        <label class="ans-opt">
                            <input type="radio" name="student_answer" value="{{ $opt }}">
                            <div class="ans-box">
                                <span class="bullet">{{ strtoupper($opt) }}</span>
                                <span class="text">{{ $question->$opt }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <!-- إجابة مقالية -->
                <div style="margin-bottom: 25px;">
                    <label class="uni-label">اكتب إجابتك هنا (اختياري)</label>
                    <textarea name="answer_text" rows="6" class="uni-input" placeholder="ابدأ الكتابة..."></textarea>
                </div>

                @if($question->require_file)
                    <div style="border: 2px dashed #cbd5e1; padding: 30px; border-radius: 12px; text-align: center;">
                        <input type="file" name="file_attachment" id="f_up" hidden>
                        <label for="f_up" style="cursor: pointer;">
                            <div style="font-size: 2.5rem; margin-bottom: 10px;">📤</div>
                            <strong style="display: block;">اضغط لرفع ملف الحل</strong>
                            <span style="font-size: 0.8rem; color: #64748b;">(PDF, JPG, PNG) - الحجم الأقصى 10MB</span>
                        </label>
                    </div>
                @endif
            @endif

            <button type="button" onclick="submitSolution()" id="subBtn" class="btn-uni-primary" style="margin-top: 40px; background: #059669;">
                تسليم الإجابة النهائية ✅
            </button>
        </form>
    </div>
</div>

<style>
    .ans-opt input { display: none; }
    .ans-box { display: flex; align-items: center; gap: 15px; padding: 18px; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: 0.2s; }
    .ans-box .bullet { width: 30px; height: 30px; border-radius: 50%; background: #f1f5f9; display: grid; place-items: center; font-weight: 800; font-size: 0.8rem; }
    .ans-opt input:checked + .ans-box { border-color: #2563eb; background: #eff6ff; }
    .ans-opt input:checked + .ans-box .bullet { background: #2563eb; color: white; }
</style>
@endsection
