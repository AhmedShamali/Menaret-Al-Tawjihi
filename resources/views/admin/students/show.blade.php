@extends('layouts.app')

@section('title', 'الملف الشخصي للطالب | ' . ($student->name_ar ?? $student->name ?? 'طالب'))

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding-bottom: 50px;" dir="rtl">

    <!-- شريط التنقل العلوي وزر العودة -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <a href="{{ route('admin.students.index') }}" style="background: #ffffff; color: #4f46e5; border: 1.5px solid #e0e7ff; padding: 10px 20px; border-radius: 12px; font-size: 0.9rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.08);" onmouseover="this.style.background='#4f46e5';this.style.color='#fff'" onmouseout="this.style.background='#ffffff';this.style.color='#4f46e5'">
            <i class="fa-solid fa-arrow-right"></i> عودة لسجل الطلاب
        </a>

        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="openSubjectModal()" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: #ffffff; border: none; padding: 10px 20px; border-radius: 12px; font-size: 0.9rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3); transition: 0.2s;">
                <i class="fa-solid fa-book-open"></i> إدارة وتعديل مواد الطالب
            </button>
            <a href="{{ route('admin.students.edit', $student->id) }}" style="background: #f8fafc; color: #334155; border: 1.5px solid #cbd5e1; padding: 10px 18px; border-radius: 12px; font-size: 0.9rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square"></i> تعديل الحساب
            </a>
        </div>
    </div>

    <!-- البطاقة الرئيسية لمعلومات الطالب -->
    <div style="background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.03); overflow: hidden; padding: 35px; text-align: right; margin-bottom: 30px;">

        <!-- الهيدر الشخصي -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 25px; margin-bottom: 25px; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <!-- صورة أو أيقونة الطالب -->
                <div style="width: 85px; height: 85px; border-radius: 22px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25); overflow: hidden;">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name_ar }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-user-graduate"></i>
                    @endif
                </div>

                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <span style="background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 800;">طالب مسجل</span>
                        @if($student->status === 'active')
                            <span style="background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 800;">
                                <i class="fa-solid fa-circle-check"></i> حساب مفعّل
                            </span>
                        @else
                            <span style="background: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 800;">
                                <i class="fa-solid fa-clock"></i> بانتظار الموافقة
                            </span>
                        @endif
                    </div>
                    <h1 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0;">{{ $student->name_ar ?? $student->name ?? 'غير متوفر' }}</h1>
                    @if($student->name_en && $student->name_en !== $student->name_ar)
                        <span style="font-size: 0.9rem; color: #64748b; font-weight: 600;">{{ $student->name_en }}</span>
                    @endif
                </div>
            </div>
            <div style="background: #f8fafc; padding: 12px 22px; border-radius: 14px; border: 1.5px solid #e2e8f0; text-align: center;">
                <span style="display: block; font-size: 0.75rem; color: #94a3b8; font-weight: 700;">الرقم التعريفي</span>
                <span style="font-size: 1.2rem; font-weight: 900; color: #4f46e5;">#{{ $student->id }}</span>
            </div>
        </div>

        <!-- شبكة تفاصيل المعلومات الشاملة -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">

            <!-- البريد الإلكتروني -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">البريد الإلكتروني</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px; direction: ltr; justify-content: flex-end;">
                    {{ $student->email ?? 'غير متوفر' }} <i class="fa-solid fa-envelope" style="color: #4f46e5;"></i>
                </span>
            </div>

            <!-- رقم الجوال -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">رقم الجوال</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px; direction: ltr; justify-content: flex-end;">
                    {{ $student->phone ?? 'غير متوفر' }} <i class="fa-solid fa-phone" style="color: #4f46e5;"></i>
                </span>
            </div>

            <!-- المرحلة والفرع الدراسي -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">الفرع والمرحلة الأكاديمية</span>
                <span style="font-size: 0.92rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-graduation-cap" style="color: #4f46e5;"></i> {{ $student?->stage?->label_ar ?? $student?->stage?->name ?? 'غير محددة' }}
                </span>
            </div>

            <!-- رقم الهوية الوطنية (NID) -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">رقم الهوية الوطنية (NID)</span>
                <span style="font-size: 0.92rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-id-card" style="color: #4f46e5;"></i> <code>{{ $student->nid ?? 'غير متوفر' }}</code>
                </span>
            </div>

            <!-- الجنس -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">الجنس</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-user" style="color: #4f46e5;"></i> {{ $student->gender ?? 'غير محدد' }}
                </span>
            </div>

            <!-- تاريخ الانضمام للمنصة -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">تاريخ الانضمام للمنصة</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calendar-days" style="color: #4f46e5;"></i> {{ $student->created_at ? $student->created_at->format('Y-m-d') : 'غير متوفر' }}
                </span>
            </div>

        </div>

    </div>

    <!-- قسم المواد الدراسية المقيد بها الطالب -->
    <div style="background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.03); overflow: hidden; padding: 35px; text-align: right;">

        <!-- هيدر قسم المواد -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.3rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
                        المواد الدراسية المقيد بها الطالب
                        <span style="background: #eef2ff; color: #4f46e5; font-size: 0.85rem; font-weight: 800; padding: 3px 10px; border-radius: 20px;" id="enrolledCountBadge">
                            {{ $student->enrolledSubjects->count() }} مواد
                        </span>
                    </h2>
                    <span style="font-size: 0.82rem; color: #64748b;">قائمة المواد المتاحة للطالب في حسابه الدراسي مع إمكانية التعديل والإلغاء</span>
                </div>
            </div>

            <!-- زر إضافة / تعديل المواد -->
            <button type="button" onclick="openSubjectModal()" style="background: #10b981; color: white; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 0.88rem; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                <i class="fa-solid fa-plus-circle"></i> تخصيص واختيار المواد
            </button>
        </div>

        <!-- قائمة كروت المواد المشترك بها -->
        @if($student->enrolledSubjects->count() > 0)
            <div id="enrolledSubjectsGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                @foreach($student->enrolledSubjects as $subject)
                    <div id="subject_card_{{ $subject->id }}" style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between; gap: 14px; transition: 0.2s;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="font-size: 2rem; width: 48px; height: 48px; border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                    {{ $subject->icon ?? '📖' }}
                                </div>
                                <div>
                                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">{{ $subject->name_ar }}</h3>
                                    <span style="font-size: 0.78rem; color: #64748b; display: block;">
                                        <i class="fa-solid fa-chalkboard-user" style="color: #4f46e5;"></i>
                                        {{ $subject->teacher?->name_ar ?? $subject->teacher?->name ?? 'مدرس المادة' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- تفاصيل الاشتراك وزر الإلغاء -->
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #edf2f7; padding-top: 12px; font-size: 0.78rem;">
                            <span style="background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-check"></i> مفعّل ونشط
                            </span>

                            <button type="button" onclick="confirmRemoveSubject({{ $student->id }}, {{ $subject->id }}, '{{ addslashes($subject->name_ar) }}')" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 5px 10px; border-radius: 8px; font-weight: 700; font-size: 0.75rem; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                <i class="fa-solid fa-trash-can"></i> إلغاء المادة
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- الحالة الفارغة: الطالب ليس لديه أي مواد مسجلة -->
            <div id="emptySubjectsState" style="text-align: center; padding: 45px 20px; background: #f8fafc; border-radius: 18px; border: 2px dashed #cbd5e1;">
                <div style="font-size: 3rem; margin-bottom: 12px;">📚</div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #334155; margin-bottom: 6px;">لا توجد أي مواد دراسية مقيدة لهذا الطالب حتى الآن</h3>
                <p style="font-size: 0.88rem; color: #64748b; max-width: 500px; margin: 0 auto 20px auto;">
                    يمكنك اختيار وتحديد المواد المناسبة للطالب لتفعيلها في لوحة دراسته على الفور.
                </p>
                <button type="button" onclick="openSubjectModal()" style="background: #4f46e5; color: white; border: none; padding: 11px 24px; border-radius: 12px; font-weight: 800; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.25);">
                    <i class="fa-solid fa-plus-circle"></i> اختيار وتفعيل مواد الطالب الآن
                </button>
            </div>
        @endif

    </div>

</div>

<!-- النافذة المنبثقة التفاعلية لإدارة وتعديل مواد الطالب (Modal) -->
<div id="subjectModalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;" dir="rtl">
    <div style="background: #ffffff; width: 100%; max-width: 680px; border-radius: 22px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column; animation: modalIn 0.25s ease-out;">

        <!-- هيدر المودال -->
        <div style="padding: 22px 28px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 1.6rem;">📚</span>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">تخصيص مواد الطالب</h3>
                    <span style="font-size: 0.8rem; color: #94a3b8;">الطالب: {{ $student->name_ar }} • {{ $student?->stage?->label_ar }}</span>
                </div>
            </div>
            <button type="button" onclick="closeSubjectModal()" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1rem; display: flex; align-items: center; justify-content: center;">✕</button>
        </div>

        <!-- شريط الإجراءات السريعة في المودال -->
        <div style="padding: 14px 28px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <span style="font-size: 0.85rem; font-weight: 700; color: #475569;">
                حدد المواد التي تريد تمكين الطالب من دراستها:
            </span>
            <div style="display: flex; gap: 8px;">
                <button type="button" onclick="toggleModalCheckboxes(true)" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">تحديد الكل</button>
                <button type="button" onclick="toggleModalCheckboxes(false)" style="background: #ffffff; color: #64748b; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">إلغاء التحديد</button>
            </div>
        </div>

        <!-- قائمة المواد بخانات الاختيار -->
        <form id="syncSubjectsForm" style="padding: 24px 28px; overflow-y: auto; flex: 1;">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px;">
                @php
                    $enrolledIds = $student->enrolledSubjects->pluck('id')->toArray();
                    $stageSubjects = $student->stage?->subjects ?? collect();
                @endphp

                @if($stageSubjects->count() > 0)
                    @foreach($stageSubjects as $sub)
                        @php $isChecked = in_array($sub->id, $enrolledIds); @endphp
                        <label class="modal-subject-card" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 2px solid {{ $isChecked ? '#4f46e5' : '#e2e8f0' }}; background: {{ $isChecked ? '#eef2ff' : '#ffffff' }}; border-radius: 14px; cursor: pointer; transition: 0.2s; user-select: none;">
                            <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" class="modal-subject-cb" {{ $isChecked ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #4f46e5; cursor: pointer;">
                            <span style="font-size: 1.5rem;">{{ $sub->icon ?? '📖' }}</span>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 800; font-size: 0.9rem; color: #1e293b;">{{ $sub->name_ar }}</div>
                                <div style="font-size: 0.75rem; color: #64748b;">
                                    {{ $sub->teacher?->name_ar ?? $sub->teacher?->name ?? 'مدرس المنصة' }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                @else
                    <div style="grid-column: 1/-1; text-align: center; padding: 25px; color: #94a3b8;">
                        لا توجد مواد مسجلة لهذا الفرع حالياً.
                    </div>
                @endif
            </div>
        </form>

        <!-- فوتر المودال -->
        <div style="padding: 16px 28px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" onclick="closeSubjectModal()" style="background: #ffffff; color: #64748b; border: 1.5px solid #cbd5e1; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer;">
                إلغاء
            </button>
            <button type="button" onclick="submitSyncSubjects()" id="btnSaveSubjects" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 800; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                <i class="fa-solid fa-floppy-disk"></i> حفظ وتفعيل المواد
            </button>
        </div>

    </div>
</div>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    const studentId = {{ $student->id }};

    function openSubjectModal() {
        const modal = document.getElementById('subjectModalOverlay');
        modal.style.display = 'flex';
    }

    function closeSubjectModal() {
        const modal = document.getElementById('subjectModalOverlay');
        modal.style.display = 'none';
    }

    // إغلاق المودال عند النقر في الخارج
    document.getElementById('subjectModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSubjectModal();
        }
    });

    // تبديل وتلوين كروت المواد في المودال
    document.querySelectorAll('.modal-subject-card').forEach(card => {
        const cb = card.querySelector('.modal-subject-cb');
        if (cb) {
            cb.addEventListener('change', () => {
                if (cb.checked) {
                    card.style.borderColor = '#4f46e5';
                    card.style.background = '#eef2ff';
                } else {
                    card.style.borderColor = '#e2e8f0';
                    card.style.background = '#ffffff';
                }
            });
        }
    });

    function toggleModalCheckboxes(selectAll) {
        document.querySelectorAll('.modal-subject-cb').forEach(cb => {
            cb.checked = selectAll;
            const card = cb.closest('.modal-subject-card');
            if (card) {
                card.style.borderColor = selectAll ? '#4f46e5' : '#e2e8f0';
                card.style.background = selectAll ? '#eef2ff' : '#ffffff';
            }
        });
    }

    // حفظ ومزامنة المواد عبر AJAX
    async function submitSyncSubjects() {
        const btn = document.getElementById('btnSaveSubjects');
        const form = document.getElementById('syncSubjectsForm');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';

        try {
            const response = await axios.post("{{ route('admin.students.syncSubjects', $student->id) }}", formData);
            Swal.fire({
                icon: 'success',
                title: 'تم الحفظ بنجاح! 🎉',
                text: response.data.message || 'تم تحديث قائمة مواد الطالب وتفعيلها.',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> حفظ وتفعيل المواد';
            Swal.fire({
                icon: 'error',
                title: 'خطأ أثناء الحفظ',
                text: 'تعذر حفظ مواد الطالب، يرجى المحاولة مرة أخرى.',
                confirmButtonText: 'حسناً'
            });
        }
    }

    // تأكيد وإلغاء مادة فردية فورياً
    function confirmRemoveSubject(sId, subId, subName) {
        Swal.fire({
            title: 'إلغاء اشتراك المادة',
            text: `هل أنت متأكد من رغبتك في إلغاء اشتراك الطالب في مادة (${subName})؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، إلغاء الاشتراك',
            cancelButtonText: 'تراجع'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await axios.post(`{{ url('admin/students') }}/${sId}/toggle-subject/${subId}`, {
                        _token: '{{ csrf_token() }}'
                    });

                    Swal.fire({
                        icon: 'success',
                        title: response.data.title || 'تم إلغاء المادة',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'فشلت العملية',
                        text: 'حدث خطأ أثناء إلغاء المادة.',
                        confirmButtonText: 'حسناً'
                    });
                }
            }
        });
    }
</script>
@endsection
