@extends('layouts.app')

@section('title', 'الاستفسارات والشكاوى الأكاديمية | إدارة المنصة')

@section('content')
<div style="max-width: 1350px; margin: 0 auto; padding: 10px 0 40px;" dir="rtl">

    {{-- رأس الصفحة --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <span style="background: #eff6ff; color: #1d4ed8; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 800;">
                    🇵🇸 الدعم الأكاديمي والوزاري
                </span>
            </div>
            <h1 style="font-size: 1.9rem; font-weight: 900; color: #0f172a; margin: 0 0 4px;">
                الاستفسارات الأكاديمية وشكاوى الطلبة 💬
            </h1>
            <p style="color: #64748b; font-size: 0.92rem; margin: 0;">
                متابعة تذاكر واستفسارات طلبة التوجيهي وأولياء الأمور والمعلمين، والرد عليها فورياً.
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.dashboard') }}" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 10px 18px; border-radius: 12px; font-weight: 700; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-right"></i> لوحة الإدارة
            </a>
        </div>
    </div>

    {{-- بطاقات الإحصائيات السريعة --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 25px;">
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: grid; place-items: center; font-size: 1.3rem;">
                <i class="fas fa-inbox"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; display: block;">إجمالي التذاكر</span>
                <strong style="font-size: 1.4rem; color: #0f172a;">{{ $stats['total'] }}</strong>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef2f2; color: #dc2626; display: grid; place-items: center; font-size: 1.3rem;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; display: block;">بانتظار المعالجة والرد</span>
                <strong style="font-size: 1.4rem; color: #dc2626;">{{ $stats['pending'] }}</strong>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #059669; display: grid; place-items: center; font-size: 1.3rem;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; display: block;">تم الرد عليها</span>
                <strong style="font-size: 1.4rem; color: #059669;">{{ $stats['replied'] }}</strong>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #faf5ff; color: #7c3aed; display: grid; place-items: center; font-size: 1.3rem;">
                <i class="fas fa-book-bookmark"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; display: block;">استفسارات المواد والدروس</span>
                <strong style="font-size: 1.4rem; color: #7c3aed;">{{ $stats['academics'] }}</strong>
            </div>
        </div>
    </div>

    {{-- شريط التصفية والبحث --}}
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <form method="GET" action="{{ route('admin.inquiries.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطالب، البريد، أو نص الرسالة..." style="padding: 9px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.88rem; width: 280px; font-family: inherit; outline: none;">
            
            <select name="category" onchange="this.form.submit()" style="padding: 9px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.88rem; font-family: inherit; outline: none; background: white;">
                <option value="all">كافة التصنيفات</option>
                <option value="استفسار أكاديمي عن المساقات" {{ request('category') == 'استفسار أكاديمي عن المساقات' ? 'selected' : '' }}>📚 استفسار أكاديمي عن المساقات</option>
                <option value="مشكلة فنية أو تقنية في المنصة" {{ request('category') == 'مشكلة فنية أو تقنية في المنصة' ? 'selected' : '' }}>⚙️ مشكلة تقنية</option>
                <option value="طلب تفعيل حساب أو اشتراك" {{ request('category') == 'طلب تفعيل حساب أو اشتراك' ? 'selected' : '' }}>💳 تفعيل حساب / اشتراك</option>
                <option value="اقتراح تطويري للمنصة" {{ request('category') == 'اقتراح تطويري للمنصة' ? 'selected' : '' }}>💡 اقتراح تطويري</option>
                <option value="شكوى خاصة أخرى" {{ request('category') == 'شكوى خاصة أخرى' ? 'selected' : '' }}>📌 أخرى</option>
            </select>

            <select name="status" onchange="this.form.submit()" style="padding: 9px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.88rem; font-family: inherit; outline: none; background: white;">
                <option value="all">كافة الحالات</option>
                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>🔴 جديدة (قيد الانتظار)</option>
                <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>🟢 تم الرد</option>
            </select>

            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer;">
                تصفية
            </button>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('admin.inquiries.index') }}" style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-decoration: underline; margin-right: 6px;">إلغاء الفلتر</a>
            @endif
        </form>
    </div>

    {{-- جدول التذاكر والاستفسارات --}}
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 18px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 0.9rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 0.82rem; text-transform: uppercase;">
                        <th style="padding: 14px 18px;">المُرسل</th>
                        <th style="padding: 14px 18px;">التصنيف والموضوع</th>
                        <th style="padding: 14px 18px;">مقتطف الرسالة</th>
                        <th style="padding: 14px 18px;">الحالة</th>
                        <th style="padding: 14px 18px;">التاريخ</th>
                        <th style="padding: 14px 18px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inq)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 18px;">
                                <div style="font-weight: 800; color: #0f172a;">{{ $inq->name }}</div>
                                <div style="font-size: 0.78rem; color: #64748b; direction: ltr; text-align: right;">
                                    {{ $inq->email }}
                                    @if($inq->phone)
                                        <br><span style="color: #10b981;">{{ $inq->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 14px 18px;">
                                <span style="background: #eff6ff; color: #1d4ed8; font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; display: inline-block; margin-bottom: 3px;">
                                    {{ $inq->category ?? 'استفسار عام' }}
                                </span>
                                <div style="font-size: 0.85rem; font-weight: 700; color: #1e293b;">{{ $inq->subject ?? 'بدون عنوان' }}</div>
                            </td>
                            <td style="padding: 14px 18px; max-width: 320px;">
                                <div style="font-size: 0.84rem; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $inq->message }}
                                </div>
                                @if($inq->reply)
                                    <small style="color: #059669; font-size: 0.75rem; display: block; margin-top: 2px;">
                                        <i class="fas fa-check-double"></i> تم إرسال رد الإدارة
                                    </small>
                                @endif
                            </td>
                            <td style="padding: 14px 18px;">
                                @if($inq->status === 'replied')
                                    <span style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981;"></span> تم الرد
                                    </span>
                                @else
                                    <span style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #ef4444;"></span> جديد (بانتظار الرد)
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px; font-size: 0.8rem; color: #64748b;">
                                {{ $inq->created_at ? $inq->created_at->diffForHumans() : '' }}
                            </td>
                            <td style="padding: 14px 18px; text-align: center;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button" onclick="openReplyModal({{ json_encode($inq) }})" style="background: #2563eb; color: white; border: none; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;" title="قراءة التذكرة والرد">
                                        <i class="fas fa-reply"></i> رد
                                    </button>

                                    @if($inq->phone)
                                        @php
                                            $wa = preg_replace('/[^0-9]/', '', $inq->phone);
                                            if (str_starts_with($wa, '05')) $wa = '970' . substr($wa, 1);
                                        @endphp
                                        <a href="https://wa.me/{{ $wa }}?text={{ urlencode('أهلاً بك أ. ' . $inq->name . '، بخصوص استفسارك في منارة التوجيهي:') }}" target="_blank" style="background: #22c55e; color: white; padding: 6px 10px; border-radius: 8px; font-size: 0.8rem; text-decoration: none;" title="مراسلة سريعة عبر واتساب">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    @endif

                                    <form action="{{ route('admin.inquiries.destroy', $inq->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه التذكرة؟');" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 10px; border-radius: 8px; font-size: 0.8rem; cursor: pointer;" title="حذف">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                                <i class="fas fa-inbox fa-3x" style="margin-bottom: 12px; display: block; opacity: 0.5;"></i>
                                <h4 style="font-size: 1.1rem; color: #475569; margin-bottom: 4px;">لا توجد استفسارات أو شكاوى مطابقة</h4>
                                <p style="font-size: 0.85rem; margin: 0;">كافة استفسارات الطلبة والزوار تم الرد عليها بنجاح.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inquiries->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9;">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>
</div>

{{-- نافذة استعراض التذكرة والرد --}}
<div id="replyModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 99999; justify-content: center; align-items: center; padding: 20px;" dir="rtl">
    <div style="background: white; border-radius: 20px; width: 100%; max-width: 600px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: fadeIn 0.2s ease;">
        <div style="padding: 18px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;">تفاصيل الاستفسار والرد الأكاديمي</h3>
                <span id="modalInqSender" style="font-size: 0.8rem; color: #64748b;">اسم الطالب</span>
            </div>
            <button type="button" onclick="closeReplyModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">✕</button>
        </div>

        <div style="padding: 24px; max-height: 75vh; overflow-y: auto;">
            <!-- نص رسالة الطالب -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span id="modalInqCategory" style="font-size: 0.78rem; font-weight: 700; color: #2563eb;"></span>
                    <span id="modalInqDate" style="font-size: 0.74rem; color: #94a3b8;"></span>
                </div>
                <h4 id="modalInqSubject" style="margin: 0 0 8px; font-size: 0.95rem; color: #0f172a; font-weight: 800;"></h4>
                <p id="modalInqMessage" style="margin: 0; font-size: 0.88rem; color: #334155; line-height: 1.6; white-space: pre-wrap;"></p>
            </div>

            <form id="replyForm" onsubmit="submitReply(event)">
                <input type="hidden" id="modalInqId" value="">

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-bottom: 6px;">
                        رد الإدارة / المشرف الأكاديمي <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea id="modalReplyText" rows="4" placeholder="اكتب ردك الوافي على استفسار الطالب هنا..." style="width: 100%; padding: 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-family: inherit; font-size: 0.9rem; outline: none; box-sizing: border-box;" required></textarea>
                </div>

                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 6px;">
                        ملاحظات إدارية داخلية (اختياري - للإدارة فقط):
                    </label>
                    <input type="text" id="modalAdminNotes" placeholder="مثال: تم التواصل هاتفياً وحل الإشكالية" style="width: 100%; padding: 10px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-family: inherit; font-size: 0.85rem; outline: none; box-sizing: border-box;">
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeReplyModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer;">
                        إغلاق
                    </button>
                    <button type="submit" id="btnSubmitReply" style="background: #2563eb; color: white; border: none; padding: 10px 22px; border-radius: 10px; font-weight: 800; font-size: 0.88rem; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <span>حفظ الرد واعتماده</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openReplyModal(inq) {
        document.getElementById('modalInqId').value = inq.id;
        document.getElementById('modalInqSender').innerText = `${inq.name} (${inq.email})`;
        document.getElementById('modalInqCategory').innerText = inq.category || 'استفسار عام';
        document.getElementById('modalInqDate').innerText = inq.created_at ? inq.created_at.substring(0, 10) : '';
        document.getElementById('modalInqSubject').innerText = inq.subject || 'بدون عنوان';
        document.getElementById('modalInqMessage').innerText = inq.message;
        document.getElementById('modalReplyText').value = inq.reply || '';
        document.getElementById('modalAdminNotes').value = inq.admin_notes || '';

        document.getElementById('replyModalOverlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeReplyModal() {
        document.getElementById('replyModalOverlay').style.display = 'none';
        document.body.style.overflow = '';
    }

    function submitReply(e) {
        e.preventDefault();
        const id = document.getElementById('modalInqId').value;
        const reply = document.getElementById('modalReplyText').value.trim();
        const notes = document.getElementById('modalAdminNotes').value.trim();

        const btn = document.getElementById('btnSubmitReply');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

        axios.post(`{{ url('admin/academic-inquiries') }}/${id}/reply`, {
            reply: reply,
            admin_notes: notes,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: res.data.title,
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span>حفظ الرد واعتماده</span> <i class="fas fa-paper-plane"></i>';
            Swal.fire('خطأ', err.response?.data?.message || 'فشل حفظ الرد.', 'error');
        });
    }
</script>
@endsection
