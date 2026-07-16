@extends('layouts.app')

@section('title', 'بوابة المحاضر | بناء تقييم')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; animation: fadeIn 0.5s ease;">

    <!-- هيدر البوابة الأكاديمية -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; border-bottom: 2px solid #e2e8f0; padding-bottom: 25px;">
        <div>
            <nav style="display: flex; gap: 8px; font-size: 0.8rem; color: #64748b; margin-bottom: 10px; font-weight: 600;">
                <span>لوحة التحكم</span> / <span>إدارة الاختبارات</span> / <span style="color: var(--accent);">بناء اختبار جديد</span>
            </nav>
            <h1 style="font-size: 2.4rem; font-weight: 800; color: #0f172a; letter-spacing: -1px;">مركز إعداد التقييمات الأكاديمية 🖋️</h1>
        </div>
        <div style="display: flex; gap: 15px;">
            <button type="button" onclick="publishExamNow()" class="btn btn-primary" style="padding: 15px 45px; border-radius: 12px; font-weight: 800; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);">نشر الاختبار للمساق ✅</button>
        </div>
    </div>

    <form id="mainExamForm">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 40px; align-items: start;">

            <!-- العمود الأيمن (الرئيسي): منشئ الأسئلة -->
            <div style="display: flex; flex-direction: column; gap: 30px;">

                <div id="questions_placeholder" style="text-align: center; padding: 80px; background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 30px;">
                    <div style="font-size: 4rem; margin-bottom: 20px;">📥</div>
                    <h3 style="color: #64748b; font-weight: 800;">لم يتم إضافة أسئلة بعد</h3>
                    <p style="color: #94a3b8; font-size: 1rem;">ابدأ بصياغة أسئلة الاختبار من خلال الأدوات في الأسفل</p>
                </div>

                <div id="questions_list"></div>

                <!-- أدوات إضافة الأسئلة (Sticky Toolbar) -->
                <div style="display: flex; gap: 20px; padding: 15px; background: #0f172a; border-radius: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); position: sticky; bottom: 30px; z-index: 100;">
                    <button type="button" onclick="addNewQuestion('mcq')" class="tool-btn">
                        <span class="icon">🎯</span>
                        <div style="text-align: right;">
                            <strong>سؤال موضوعي</strong>
                            <p>اختيار من متعدد</p>
                        </div>
                    </button>
                    <div style="width: 1px; background: rgba(255,255,255,0.15);"></div>
                    <button type="button" onclick="addNewQuestion('essay')" class="tool-btn" style="color: #3b82f6;">
                        <span class="icon">📝</span>
                        <div style="text-align: right;">
                            <strong>سؤال مقالي</strong>
                            <p>حل كتابي أو رفع ملف</p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- العمود الأيسر (الجانبي): إعدادات المساق + العداد -->
            <aside style="display: flex; flex-direction: column; gap: 25px; position: sticky; top: 110px;">

                <!-- بطاقة العداد (التي طلبت إصلاحها) -->
                <div class="portal-card" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-color: #bbf7d0;">
                    <h3 class="portal-card-title" style="color: #166534;">📊 ملخص التقييم الحظي</h3>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px; border-radius: 15px; border: 1px solid #e2e8f0;">
                            <span style="color: #64748b; font-weight: 700; font-size: 0.85rem;">عدد الأسئلة:</span>
                            <strong id="q_stat_count" style="font-size: 1.2rem; color: #166534;">0</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px; border-radius: 15px; border: 1px solid #e2e8f0;">
                            <span style="color: #64748b; font-weight: 700; font-size: 0.85rem;">إجمالي النقاط:</span>
                            <strong id="q_stat_points" style="font-size: 1.2rem; color: #166534;">0</strong>
                        </div>
                    </div>
                </div>

                <div class="portal-card">
                    <h3 class="portal-card-title">⚙️ الإعدادات الأكاديمية</h3>

                    <div class="portal-field">
                        <label>عنوان التقييم</label>
                        <input type="text" name="title" placeholder="مثلاً: اختبار نصفي - كيمياء" required>
                    </div>

                    <div class="portal-field">
                        <label>المرحلة الدراسية</label>
                        <select id="stage_picker" required>
                            <option value="">اختر الصف...</option>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->label_ar }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="portal-field">
                        <label>المادة العلمية</label>
                        <select name="subject_id" id="subject_picker" disabled required>
                            <option value="">اختر المرحلة أولاً...</option>
                        </select>
                    </div>

                    <div class="portal-field">
                        <label>المدة الزمنية (بالدقائق)</label>
                        <input type="number" name="duration_minutes" value="60">
                    </div>
                </div>
            </aside>

        </div>
    </form>
</div>

<style>
    /* التنسيقات الفاخرة لـ Portal */
    .portal-card { background: white; border-radius: 28px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 4px 25px rgba(0,0,0,0.03); }
    .portal-card-title { font-size: 1rem; font-weight: 800; margin-bottom: 25px; color: #1e293b; display: flex; align-items: center; gap: 10px; }

    .portal-field { margin-bottom: 20px; }
    .portal-field label { display: block; font-size: 0.8rem; font-weight: 800; color: #64748b; margin-bottom: 10px; }
    .portal-field input, .portal-field select { width: 100%; padding: 15px; border-radius: 14px; border: 2px solid #f1f5f9; font-family: inherit; font-weight: 600; background: #f8fafc; transition: 0.3s; }
    .portal-field input:focus { border-color: var(--accent); background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.05); }

    .tool-btn { flex: 1; display: flex; align-items: center; gap: 15px; padding: 15px 25px; background: transparent; border: none; color: white; cursor: pointer; border-radius: 18px; transition: 0.3s; }
    .tool-btn:hover { background: rgba(255,255,255,0.08); transform: translateY(-2px); }
    .tool-btn .icon { width: 45px; height: 45px; background: rgba(255,255,255,0.1); border-radius: 12px; display: grid; place-items: center; font-size: 1.5rem; }
    .tool-btn strong { display: block; font-size: 0.95rem; }
    .tool-btn p { font-size: 0.7rem; opacity: 0.6; margin: 0; }

    .q-portal-card { background: white; padding: 45px; border-radius: 35px; border: 1px solid #e2e8f0; position: relative; animation: slideUp 0.5s ease; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.02); }
    .q-portal-card:hover { border-color: var(--accent); }

    .opt-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 25px; }
    .opt-input { display: flex; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 15px; overflow: hidden; transition: 0.3s; }
    .opt-input:focus-within { border-color: var(--accent); background: white; }
    .opt-input span { padding: 15px 20px; background: #f1f5f9; font-weight: 900; color: #64748b; font-size: 0.85rem; }
    .opt-input input { flex: 1; border: none; padding: 15px; background: transparent; outline: none; font-weight: 600; font-family: inherit; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    const stagesData = @json($stages);
    const stagePicker = document.getElementById('stage_picker');
    const subjectPicker = document.getElementById('subject_picker');
    let qIdx = 0;

    // 1. منطق الربط بين المرحلة والمادة
    stagePicker.addEventListener('change', function() {
        const id = this.value;
        subjectPicker.innerHTML = '<option value="">اختر المادة...</option>';
        if (id) {
            const stage = stagesData.find(s => s.id == id);
            stage.subjects.forEach(sub => {
                subjectPicker.innerHTML += `<option value="${sub.id}">${sub.name_ar}</option>`;
            });
            subjectPicker.disabled = false;
        } else {
            subjectPicker.disabled = true;
        }
    });

    // 2. تحديث العداد والنقاط (Fixed)
    function updateStats() {
        const cards = document.querySelectorAll('.q-portal-card');
        document.getElementById('q_stat_count').textContent = cards.length;

        let total = 0;
        const pointInputs = document.querySelectorAll('input[name*="[points]"]');
        pointInputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });
        document.getElementById('q_stat_points').textContent = total;
    }

    // 3. إضافة سؤال جديد
    function addNewQuestion(type) {
        document.getElementById('questions_placeholder').style.display = 'none';
        const list = document.getElementById('questions_list');

        let html = `
            <div class="q-portal-card">
                <input type="hidden" name="questions[${qIdx}][type]" value="${type}">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <span style="font-weight: 900; color: #cbd5e1; font-size: 1.8rem;">#${qIdx + 1}</span>
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <label style="font-size: 0.85rem; font-weight: 800; color: #64748b;">درجة السؤال:</label>
                        <input type="number" name="questions[${qIdx}][points]" value="5" oninput="updateStats()" class="f-input" style="width: 90px; text-align: center; background: #fff;">
                        <button type="button" onclick="this.closest('.q-portal-card').remove(); updateStats();" style="background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; width: 40px; height: 40px; border-radius: 12px; cursor: pointer; transition: 0.3s;">🗑️</button>
                    </div>
                </div>

                <label class="portal-field label" style="display:block; font-weight:800; color:#1e293b; margin-bottom:10px;">نص السؤال</label>
                <textarea name="questions[${qIdx}][question_text]" class="portal-field input" rows="2" placeholder="اكتب نص السؤال هنا..." style="width:100%; border:2px solid #f1f5f9; border-radius:15px; padding:15px; font-family:inherit;" required></textarea>
        `;

        if (type === 'mcq') {
            html += `
                <div class="opt-grid">
                    <div class="opt-input"><span>A</span><input type="text" name="questions[${qIdx}][a]" placeholder="الخيار الأول" required></div>
                    <div class="opt-input"><span>B</span><input type="text" name="questions[${qIdx}][b]" placeholder="الخيار الثاني" required></div>
                    <div class="opt-input"><span>C</span><input type="text" name="questions[${qIdx}][c]" placeholder="الخيار الثالث" required></div>
                    <div class="opt-input"><span>D</span><input type="text" name="questions[${qIdx}][d]" placeholder="الخيار الرابع" required></div>
                </div>
                <div style="margin-top: 25px;">
                    <label style="display:block; font-weight:800; font-size:0.8rem; color:#64748b; margin-bottom:10px;">الإجابة الصحيحة</label>
                    <select name="questions[${qIdx}][correct_answer]" style="width: 250px; padding:12px; border-radius:12px; border:2px solid #10b981; background:#ecfdf5; font-weight:800; font-family:inherit;">
                        <option value="a">الخيار A</option><option value="b">الخيار B</option><option value="c">الخيار C</option><option value="d">الخيار D</option>
                    </select>
                </div>
            `;
        } else {
            html += `
                <div style="margin-top: 30px; padding: 25px; background: #f8fafc; border-radius: 20px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                    <input type="checkbox" name="questions[${qIdx}][require_file]" value="1" style="width: 22px; height: 22px; accent-color: var(--accent);">
                    <div>
                        <strong style="display:block; color:#1e293b;">تفعيل رفع الملفات</strong>
                        <span style="font-size:0.8rem; color:#64748b;">سيتمكن الطالب من إرفاق صور أو ملفات PDF كإجابة لهذا التكليف.</span>
                    </div>
                </div>
            `;
        }

        html += `</div>`;
        list.insertAdjacentHTML('beforeend', html);
        qIdx++;
        updateStats(); // تحديث فوري عند الإضافة
    }

    // 4. النشر النهائي
    function publishExamNow() {
        const form = document.getElementById('mainExamForm');
        const formData = new FormData(form);

        if(document.querySelectorAll('.q-portal-card').length === 0) {
            Swal.fire('تنبيه', 'يجب إضافة سؤال واحد على الأقل للاختبار!', 'warning');
            return;
        }

        Swal.fire({ title: 'جاري النشر...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        axios.post("{{ route('admin.exams.store') }}", formData).then(res => {
            Swal.fire({ icon: 'success', title: 'تم النشر بنجاح ✅', showConfirmButton: false, timer: 1500 })
            .then(() => location.href = "{{ route('admin.exams.index') }}");
        }).catch(err => {
            Swal.fire('خطأ!', 'يرجى التأكد من ملء جميع الحقول والخيارات.', 'error');
        });
    }
</script>
@endsection
