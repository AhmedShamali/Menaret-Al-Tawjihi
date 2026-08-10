@extends('layouts.app')

@section('title', 'رصد درجات الطالب')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; animation: fadeIn 0.6s ease;">

    <!-- هيدر الصفحة -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; border-bottom: 2px solid #e2e8f0; padding-bottom: 25px;">
        <div>
            <nav style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px; font-weight: 600;">بوابة التصحيح / سجل التسليمات / رصد الدرجات</nav>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a; letter-spacing: -1px;">مراجعة الحلول الأكاديمية 🖋️</h1>
            <p style="margin-top: 5px; color: #64748b;">رصد درجات الطالب: <strong style="color: #4f46e5;">{{ $submission->student->name_ar ?? $submission->student->name }}</strong></p>
        </div>
        <div style="text-align: left; background: #fff; padding: 15px 30px; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 5px;">الدرجة الحالية</div>
            <div style="font-size: 2.5rem; font-weight: 900; color: #4f46e5; font-family: monospace;">{{ $submission->total_earned_grade ?? 0 }}</div>
        </div>
    </div>

    <form id="gradingForm">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 30px;">
            @foreach($submission->answers as $index => $ans)
            <div class="glass-card" style="padding: 40px; border-right: 6px solid {{ $ans->question->type == 'mcq' ? '#10b981' : '#3b82f6' }}; border-radius: 25px; background: white; border: 1px solid #f1f5f9;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <span style="font-weight: 800; color: #64748b; font-size: 0.9rem;">سؤال #{{ $index + 1 }} ({{ $ans->question->type == 'mcq' ? 'موضوعي' : 'مقالي' }})</span>
                    <span class="chip" style="background: #f1f5f9; font-weight: 800; color: #1e293b; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">الدرجة المستحقة للسؤال: {{ $ans->question->points }}</span>
                </div>

                <h3 style="font-size: 1.3rem; color: #1e293b; margin-bottom: 25px; line-height: 1.6; font-weight: 700;">{{ $ans->question->question_text }}</h3>

                <div style="background: #f8fafc; padding: 25px; border-radius: 20px; border: 1px solid #e2e8f0;">
                    @if($ans->question->type == 'mcq')
                        {{-- إجابة الموضوعي --}}
                        <div style="font-weight: 700; font-size: 1.1rem;">
                            إجابة الطالب:
                            <span style="color: {{ $ans->answer_text == $ans->question->correct_answer ? '#059669' : '#ef4444' }};">
                                ({{ strtoupper($ans->answer_text) }}) {{ $ans->question->{$ans->answer_text} ?? $ans->answer_text }}
                            </span>
                            {!! $ans->answer_text == $ans->question->correct_answer ? ' <span style="margin-right:10px;">✅</span>' : ' <span style="margin-right:10px;">❌</span>' !!}
                        </div>
                        {{-- حقل مخفي للحفاظ على درجة الموضوعي --}}
                        <input type="hidden" name="grades[{{ $ans->id }}]" value="{{ $ans->points_awarded }}">
                    @else
                        {{-- إجابة المقالي والملفات --}}
                        <div style="margin-bottom: 20px;">
                            <strong style="display: block; margin-bottom: 10px; color: #64748b; font-size: 0.9rem;">نص إجابة الطالب:</strong>
                            <p style="line-height: 1.8; font-size: 1.1rem; color: #1e293b; background: white; padding: 15px; border-radius: 12px; border: 1px solid #eee;">
                                {{ $ans->answer_text ?? 'لا يوجد نص مقدم' }}
                            </p>

                            @if($ans->file_path)
                                <a href="{{ asset('storage/'.$ans->file_path) }}" target="_blank" class="btn" style="margin-top: 15px; background: white; border: 1px solid #cbd5e1; color: #0f172a; font-weight: 700; display: inline-flex; align-items: center; gap: 10px; padding: 10px 20px; border-radius: 12px; text-decoration: none;">
                                    <span>📂</span> فتح ملف الحل المرفق (PDF/صور)
                                </a>
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; gap: 15px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                            <label style="font-weight: 800; color: #0f172a;">رصد الدرجة المستحقة:</label>
                            <input type="number" name="grades[{{ $ans->id }}]"
                                   value="{{ $ans->points_awarded ?? 0 }}"
                                   max="{{ $ans->question->points }}"
                                   min="0"
                                   class="f-input"
                                   style="width: 120px; text-align: center; font-weight: 900; font-size: 1.4rem; color: #2563eb; border-color: #3b82f6;"
                                   placeholder="0">
                            <span style="color: #94a3b8; font-weight: 600;">من أصل {{ $ans->question->points }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top: 60px; text-align: center; padding-bottom: 80px;">
            <button type="button" onclick="submitGradesToDB()" id="submitBtn" class="btn btn-primary" style="padding: 18px 80px; font-size: 1.2rem; border-radius: 30px; background: #10b981; color: white; border: none; cursor: pointer; font-weight: 800; box-shadow: 0 15px 35px rgba(16, 185, 129, 0.25);">
                اعتماد الدرجات النهائية ورصدها ✅
            </button>
        </div>
    </form>
</div>

<style>
    .f-input { padding: 12px; border-radius: 12px; border: 2px solid #f1f5f9; font-family: inherit; outline: none; transition: 0.3s; }
    .f-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function submitGradesToDB() {
        const btn = document.getElementById('submitBtn');
        const form = document.getElementById('gradingForm');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.textContent = 'جاري الحفظ والرصد...';

        // رابط حفظ ديناميكي يتعرف تلقائياً على دور المستخدِم (teacher أو admin)
        const saveRouteUrl = "{{ route(auth()->user()->role . '.submissions.saveGrade', $submission->id) }}";
        const redirectIndexUrl = "{{ route(auth()->user()->role . '.submissions.index') }}";

        axios.post(saveRouteUrl, formData)
        .then(res => {
            if(res.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: res.data.title || 'تم الحفظ بنجاح',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = redirectIndexUrl;
                });
            } else {
                Swal.fire('تنبيه!', res.data.message || 'حدث خطأ غير متوقع', 'warning');
                btn.disabled = false;
                btn.textContent = 'اعتماد الدرجات النهائية ✅';
            }
        })
        .catch(err => {
            console.error(err);
            const msg = err.response && err.response.data && err.response.data.message
                ? err.response.data.message
                : 'حدثت مشكلة أثناء الحفظ، يرجى المحاولة لاحقاً';
            Swal.fire('خطأ!', msg, 'error');
            btn.disabled = false;
            btn.textContent = 'اعتماد الدرجات النهائية ✅';
        });
    }
</script>
@endsection
