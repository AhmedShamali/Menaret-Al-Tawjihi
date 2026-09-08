@extends('layouts.app')

@section('title', 'إدارة أسعار المواد والعروض الترويجية')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; animation: fadeIn 0.5s ease;">

    <!-- الترويسة -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
                <i class="fa-solid fa-tags" style="color: #0284c7;"></i> إدارة تسعير المواد وباقات الاشتراك 🇵🇸
            </h1>
            <p style="color: #64748b; font-size: 0.88rem; margin: 0;">
                تحديد أسعار المواد بالشيكل (₪)، ضبط الخصومات الترويجية، وتفعيل المواد المجانية لطلبة توجيهي فلسطين.
            </p>
        </div>

        <button onclick="openSeasonalModal()" style="background: linear-gradient(135deg, #059669, #10b981); color: white; border: none; padding: 11px 22px; border-radius: 12px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);">
            <i class="fa-solid fa-percent"></i> تطبيق خصم موسمي شامل
        </button>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 20px; border-radius: 14px; margin-bottom: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- كروت الإحصائيات السريعة -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px;">
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: grid; place-items: center; font-size: 1.3rem;">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <div>
                <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">إجمالي المواد</span>
                <div style="font-size: 1.5rem; font-weight: 900; color: #0f172a;">{{ $pricingStats['total_subjects'] }}</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: grid; place-items: center; font-size: 1.3rem;">
                <i class="fa-solid fa-fire"></i>
            </div>
            <div>
                <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">مواد عليها عروض مخفضة</span>
                <div style="font-size: 1.5rem; font-weight: 900; color: #d97706;">{{ $pricingStats['discounted'] }}</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #dcfce7; color: #16a34a; display: grid; place-items: center; font-size: 1.3rem;">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div>
                <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">مواد مجانية / تجريبية</span>
                <div style="font-size: 1.5rem; font-weight: 900; color: #16a34a;">{{ $pricingStats['free_subjects'] }}</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #ede9fe; color: #7c3aed; display: grid; place-items: center; font-size: 1.3rem;">
                <i class="fa-solid fa-shekel-sign"></i>
            </div>
            <div>
                <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">متوسط سعر المادة</span>
                <div style="font-size: 1.5rem; font-weight: 900; color: #7c3aed;">{{ round($pricingStats['avg_price']) }} ₪</div>
            </div>
        </div>
    </div>

    <!-- فلتر المرحلة -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 18px; padding: 18px 24px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-filter" style="color: #64748b;"></i>
            <span style="font-weight: 700; font-size: 0.88rem; color: #1e293b;">تصفية حسب المرحلة والفرع:</span>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('admin.subjects.pricing') }}" style="text-decoration: none; padding: 6px 14px; border-radius: 10px; font-size: 0.82rem; font-weight: 700; {{ empty($stageId) ? 'background: #0284c7; color: white;' : 'background: #f1f5f9; color: #475569;' }}">
                جميع الفروع
            </a>
            @foreach($stages as $stg)
                <a href="{{ route('admin.subjects.pricing', ['stage_id' => $stg->id]) }}" style="text-decoration: none; padding: 6px 14px; border-radius: 10px; font-size: 0.82rem; font-weight: 700; {{ $stageId == $stg->id ? 'background: #0284c7; color: white;' : 'background: #f1f5f9; color: #475569;' }}">
                    {{ $stg->name ?? $stg->name_ar ?? 'المرحلة' }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- جدول الأسعار -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.82rem; font-weight: 700;">
                    <th style="padding: 16px 20px;">المادة الدراسية</th>
                    <th style="padding: 16px 20px;">الفرع / المرحلة</th>
                    <th style="padding: 16px 20px;">السعر الأساسي (₪)</th>
                    <th style="padding: 16px 20px;">سعر العرض (₪)</th>
                    <th style="padding: 16px 20px;">الحالة والتسعير الفعلي</th>
                    <th style="padding: 16px 20px; text-align: center;">إجراءات التعديل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $sub)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 18px 20px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 12px; background: {{ $sub->color ?? '#0284c7' }}15; color: {{ $sub->color ?? '#0284c7' }}; display: grid; place-items: center; font-size: 1.1rem; flex-shrink: 0;">
                                    <i class="fa-solid {{ $sub->icon ?? 'fa-book' }}"></i>
                                </div>
                                <div>
                                    <strong style="color: #0f172a; font-size: 0.95rem;">{{ $sub->name_ar }}</strong>
                                    <small style="color: #94a3b8; display: block; font-size: 0.75rem;">{{ $sub->contents_count ?? 0 }} درس • {{ $sub->exams_count ?? 0 }} اختبار</small>
                                </div>
                            </div>
                        </td>

                        <td style="padding: 18px 20px;">
                            <span style="background: #eff6ff; color: #0284c7; padding: 4px 10px; border-radius: 8px; font-size: 0.78rem; font-weight: 700;">
                                {{ optional($sub->stage)->name ?? optional($sub->stage)->name_ar ?? 'توجيهي' }}
                            </span>
                        </td>

                        <td style="padding: 18px 20px; font-family: monospace; font-size: 1rem; font-weight: 700; color: #334155;">
                            {{ number_format($sub->price_ils ?? 150, 2) }} ₪
                        </td>

                        <td style="padding: 18px 20px; font-family: monospace; font-size: 1rem; font-weight: 700;">
                            @if($sub->discount_price_ils > 0)
                                <span style="color: #ea580c;">{{ number_format($sub->discount_price_ils, 2) }} ₪</span>
                            @else
                                <span style="color: #94a3b8; font-size: 0.85rem;">لا يوجد خصم</span>
                            @endif
                        </td>

                        <td style="padding: 18px 20px;">
                            @if($sub->is_free)
                                <span style="background: #dcfce7; color: #166534; padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 800;">
                                    مجانية تجريبية 🎁
                                </span>
                            @elseif($sub->discount_price_ils > 0)
                                <span style="background: #fef3c7; color: #b45309; padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 800;">
                                    عرض خاص: {{ number_format($sub->discount_price_ils, 0) }} ₪
                                </span>
                            @else
                                <span style="background: #f1f5f9; color: #334155; padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                                    سعر عادي: {{ number_format($sub->price_ils, 0) }} ₪
                                </span>
                            @endif
                        </td>

                        <td style="padding: 18px 20px; text-align: center;">
                            <button onclick="editPricing({{ json_encode($sub) }})" style="background: #f1f5f9; color: #0284c7; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 0.82rem; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e0f2fe'" onmouseout="this.style.background='#f1f5f9'">
                                <i class="fa-solid fa-pen-to-square"></i> تعديل السعر
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: #94a3b8;">
                            لا توجد مواد مسجلة مطابقة للبحث.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- نافذة تعديل تسعيرة مادة (Modal) -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 22px; max-width: 500px; width: 100%; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); animation: zoomIn 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 id="modalSubjectTitle" style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">تعديل تسعيرة المادة</h3>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form id="editPricingForm" onsubmit="submitPricing(event)">
            @csrf
            <input type="hidden" id="editSubjectId" name="subject_id">

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px;">السعر الأساسي بالشيكل (₪) *</label>
                <input type="number" step="0.5" id="modalPrice" name="price_ils" required style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem; font-weight: 700;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px;">سعر الخصم / العرض الترويجي بالشيكل (اختياري)</label>
                <input type="number" step="0.5" id="modalDiscount" name="discount_price_ils" placeholder="اتركه فارغاً إذا لم يكن هناك تخفيض" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;">
            </div>

            <div style="margin-bottom: 18px; display: flex; align-items: center; gap: 10px; background: #f8fafc; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <input type="checkbox" id="modalIsFree" name="is_free" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="modalIsFree" style="font-size: 0.85rem; font-weight: 700; color: #1e293b; cursor: pointer;">
                    تعيين المادة كمجانية بالكامل (Free Access) 🎁
                </label>
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px;">وصف باقة المادة ومميزاتها للطلاب</label>
                <textarea id="modalDescription" name="description" rows="3" placeholder="مثال: تشمل شرح كامل المنهاج الوزاري، حلول أسئلة السنوات السابقة، وبطاقات المذاكرة السريعة" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #cbd5e1; outline: none; font-size: 0.85rem; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeEditModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer;">إلغاء</button>
                <button type="submit" id="btnSavePrice" style="background: #0284c7; color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 700; cursor: pointer;">حفظ التغييرات</button>
            </div>
        </form>
    </div>
</div>

<!-- نافذة الخصم الشامل (Seasonal Modal) -->
<div id="seasonalModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 22px; max-width: 480px; width: 100%; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">تطبيق خصم موسمي موحد</h3>
            <button onclick="closeSeasonalModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.subjects.pricing.seasonal') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px;">نسبة الخصم المئوية (%) *</label>
                <input type="number" name="discount_percentage" min="5" max="90" value="20" required style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #cbd5e1; outline: none; font-size: 1.1rem; font-weight: 800; color: #059669;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px;">تطبيق الخصم على فرع محدد (اختياري)</label>
                <select name="stage_id" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #cbd5e1; outline: none; font-size: 0.9rem;">
                    <option value="">جميع فروع الثانوية العامة</option>
                    @foreach($stages as $stg)
                        <option value="{{ $stg->id }}">{{ $stg->name ?? $stg->name_ar }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeSeasonalModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer;">إلغاء</button>
                <button type="submit" style="background: #059669; color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 700; cursor: pointer;">تطبيق الخصم فوراً</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentSubject = null;

function editPricing(sub) {
    currentSubject = sub;
    document.getElementById('editSubjectId').value = sub.id;
    document.getElementById('modalSubjectTitle').textContent = 'تعديل تسعيرة: ' + sub.name_ar;
    document.getElementById('modalPrice').value = sub.price_ils || 150;
    document.getElementById('modalDiscount').value = sub.discount_price_ils || '';
    document.getElementById('modalIsFree').checked = (sub.is_free == 1);
    document.getElementById('modalDescription').value = sub.description || '';

    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openSeasonalModal() {
    document.getElementById('seasonalModal').style.display = 'flex';
}

function closeSeasonalModal() {
    document.getElementById('seasonalModal').style.display = 'none';
}

function submitPricing(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSavePrice');
    const id = document.getElementById('editSubjectId').value;
    const price = document.getElementById('modalPrice').value;
    const discount = document.getElementById('modalDiscount').value;
    const isFree = document.getElementById('modalIsFree').checked ? 1 : 0;
    const desc = document.getElementById('modalDescription').value;

    btn.disabled = true;
    btn.textContent = 'جاري الحفظ...';

    axios.post(`/admin/subjects/pricing/${id}/update`, {
        price_ils: price,
        discount_price_ils: discount,
        is_free: isFree,
        description: desc
    })
    .then(res => {
        closeEditModal();
        Swal.fire({
            icon: 'success',
            title: 'تم التحديث بنجاح!',
            text: res.data.message || 'تم تحديث تسعيرة المادة بنجاح.',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.reload();
        });
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'حفظ التغييرات';
        const msg = err.response?.data?.message || 'تعذر تحديث التسعيرة.';
        Swal.fire({ icon: 'error', title: 'خطأ', text: msg });
    });
}
</script>
@endsection
