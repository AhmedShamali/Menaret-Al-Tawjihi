@extends('layouts.app')
@section('content')
<div style="max-width: 1100px; margin: 0 auto; animation: fadeIn 0.6s ease;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px;">
        <div>
            <h1 style="font-weight: 900; color: #0f172a;">✏️ تعديل الاختبار</h1>
            <p style="color: #64748b;">أنت تقوم بتعديل: <strong>{{ $exam->title }}</strong></p>
        </div>
        <button onclick="updateExam({{ $exam->id }})" class="btn btn-primary" style="padding: 15px 50px; border-radius: 15px;">حفظ التغييرات ✅</button>
    </div>

    <form id="editExamForm">
        @csrf
        @method('PUT')
        <div style="display: grid; grid-template-columns: 320px 1fr; gap: 30px;">
            <aside class="glass-card" style="padding: 30px; height: fit-content; position: sticky; top: 100px;">
                <label class="f-label">عنوان الاختبار</label>
                <input type="text" name="title" value="{{ $exam->title }}" class="f-input">

                <label class="f-label" style="margin-top: 20px;">المدة الزمنية (دقائق)</label>
                <input type="number" name="duration_minutes" value="{{ $exam->duration_minutes }}" class="f-input">
            </aside>

            <main id="q_list" style="display: flex; flex-direction: column; gap: 20px;">
                {{-- تحميل الأسئلة الحالية من قاعدة البيانات --}}
                @foreach($exam->questions as $index => $q)
                <div class="glass-card q-card" style="padding: 35px; position: relative;">
                    <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $q->id }}">
                    <input type="hidden" name="questions[{{ $index }}][type]" value="{{ $q->type }}">

                    <label class="f-label">نص السؤال</label>
                    <textarea name="questions[{{ $index }}][question_text]" class="f-input" rows="2">{{ $q->question_text }}</textarea>

                    @if($q->type == 'mcq')
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                        <input type="text" name="questions[{{ $index }}][a]" value="{{ $q->a }}" class="f-input" placeholder="خيار A">
                        <input type="text" name="questions[{{ $index }}][b]" value="{{ $q->b }}" class="f-input" placeholder="خيار B">
                        <select name="questions[{{ $index }}][correct_answer]" class="f-input" style="background:#ecfdf5;">
                            <option value="a" {{ $q->correct_answer == 'a' ? 'selected' : '' }}>A</option>
                            <option value="b" {{ $q->correct_answer == 'b' ? 'selected' : '' }}>B</option>
                        </select>
                    </div>
                    @endif
                    <button type="button" onclick="this.parentElement.remove()" style="color:red; background:none; border:none; margin-top:15px; cursor:pointer; font-weight:700;">🗑️ حذف السؤال</button>
                </div>
                @endforeach
            </main>
        </div>
    </form>
</div>

<script>
    function updateExam(id) {
        const data = new FormData(document.getElementById('editExamForm'));
        axios.post(`/admin/exams/${id}`, data).then(() => {
            Swal.fire('تم!', 'تم تحديث الاختبار بنجاح', 'success').then(() => location.href="/admin/exams");
        });
    }
</script>
@endsection
