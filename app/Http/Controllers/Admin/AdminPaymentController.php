<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPaymentController extends Controller
{
    /**
     * قائمة كافة عمليات الدفع والاشتراكات لمدير المنصة
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $gateway = $request->query('gateway');
        $search = $request->query('search');

        $query = Payment::with('student')->latest();

        if ($status && in_array($status, ['completed', 'pending', 'cancelled'])) {
            $query->where('status', $status);
        }

        if ($gateway && in_array($gateway, ['jawwal_pay', 'palpay', 'bop', 'voucher'])) {
            $query->where('gateway', $gateway);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name_ar', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        // إحصائيات الدفع العامة للمدير
        $stats = [
            'total_revenue'      => Payment::where('status', 'completed')->sum('amount'),
            'total_count'        => Payment::count(),
            'pending_count'      => Payment::where('status', 'pending')->count(),
            'completed_count'    => Payment::where('status', 'completed')->count(),
            'cancelled_count'    => Payment::where('status', 'cancelled')->count(),
            'jawwal_pay_revenue' => Payment::where('status', 'completed')->where('gateway', 'jawwal_pay')->sum('amount'),
            'palpay_revenue'     => Payment::where('status', 'completed')->where('gateway', 'palpay')->sum('amount'),
            'bop_revenue'        => Payment::where('status', 'completed')->where('gateway', 'bop')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats', 'status', 'gateway', 'search'));
    }

    /**
     * استعراض أو تحميل صورة إشعار التحويل البنكي للمدير بأمان
     */
    public function viewReceipt($id)
    {
        $payment = Payment::findOrFail($id);
        if (!$payment->receipt_path) {
            abort(404, 'لا يوجد إشعار مرفق لهذه المعاملة.');
        }

        if (Storage::disk('public')->exists($payment->receipt_path)) {
            return Storage::disk('public')->response($payment->receipt_path);
        }

        $fullPath = storage_path('app/public/' . $payment->receipt_path);
        if (file_exists($fullPath)) {
            return response()->file($fullPath);
        }

        abort(404, 'ملف الإشعار غير موجود على السيرفر.');
    }

    /**
     * تحديث حالة الدفع وتفعيل أو تعطيل اشتراك الطالب
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:completed,cancelled,pending',
        ]);

        $payment = Payment::with('student')->findOrFail($id);
        $newStatus = $request->status;
        $oldStatus = $payment->status;

        $payment->status = $newStatus;
        $payment->save();

        // إذا تم تأكيد الدفع من المدير، تفعيل حساب الطالب واشتراكاته في المواد فوراً
        if ($newStatus === 'completed') {
            // تفعيل حساب الطالب إن كان معلقاً
            if ($payment->student && $payment->student->status !== 'active') {
                $payment->student->update(['status' => 'active']);
            }

            if (is_array($payment->items)) {
                foreach ($payment->items as $item) {
                    if (!empty($item['id'])) {
                        Enrollment::updateOrCreate(
                            [
                                'student_id' => $payment->student_id,
                                'subject_id' => $item['id']
                            ],
                            [
                                'status'         => 'active',
                                'access_mode'    => 'all',
                                'payment_status' => $payment->gateway,
                                'activated_at'   => now(),
                            ]
                        );
                    }
                }
            }

            // إشعار الطالب
            if ($payment->student_id) {
                NotificationService::notifyStudent(
                    $payment->student_id,
                    'تم اعتماد دفعتك وتفعيل موادك بنجاح! 🎉',
                    "قام مدير المنصة بفحص إشعار السداد واعتماد الدفعة رقم ({$payment->transaction_number}) بمبلغ {$payment->amount} ₪. تم تفعيل كامل دروس واختبارات موادك، نتمنى لك التوفيق والتميز!",
                    'payment',
                    route('student.dashboard')
                );
            }
        } elseif ($newStatus === 'cancelled' && is_array($payment->items)) {
            // في حال الإلغاء أو الرفض، يتم إلغاء تفعيل المواد
            foreach ($payment->items as $item) {
                if (!empty($item['id'])) {
                    Enrollment::where('student_id', $payment->student_id)
                        ->where('subject_id', $item['id'])
                        ->update(['status' => 'inactive']);
                }
            }

            if ($payment->student_id) {
                NotificationService::notifyStudent(
                    $payment->student_id,
                    'تنبيه بخصوص إشعار السداد رقم ' . $payment->transaction_number,
                    "تم رفض أو إلغاء الدفعة رقم ({$payment->transaction_number}). يرجى التحقق من إشعار التحويل البنكي أو التواصل مع إدارة المنصة للمساعدة.",
                    'payment',
                    route('student.checkout.receipt', $payment->id)
                );
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'تم تحديث حالة الدفع واعتماد الإجراء بنجاح! ✅',
            ]);
        }

        return back()->with('success', 'تم تحديث حالة المعاملة المالية وتعديل الصلاحيات بنجاح.');
    }
}
