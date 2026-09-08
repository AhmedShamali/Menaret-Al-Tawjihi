<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Support\CurrentActor;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentGatewayController extends Controller
{
    /**
     * عرض بوابة الدفع الإلكترونية الفلسطينية
     */
    public function showCheckout(Request $request)
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        $cart = session('checkout_cart');

        if (!$cart || empty($cart['items'])) {
            return redirect()->route('student.courses.catalog')
                ->with('error', 'سلة المواد فارغة، يرجى اختيار المواد أولاً.');
        }

        // بيانات الحسابات المعتمدة في فلسطين (مربوطة بإعدادات مدير النظام ديناميكياً)
        $palPhone = \App\Models\Setting::get('payment_phone', '0567897212');
        $palOwner = \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي');
        $palSiteName = \App\Models\Setting::get('site_name', 'منارة التوجيهي');

        $palGatewaysConfig = [
            'jawwal_pay' => [
                'name'         => 'محفظة جوال باي (Jawwal Pay) 📱',
                'merchant_no'  => $palPhone,
                'merchant_name'=> $palOwner . ' (' . $palSiteName . ')',
                'instructions' => 'قم بتحويل المبلغ إلى رقم المحفظة أعلاه لصاحب الحساب (' . $palOwner . ') ثم أدخل رقم محفظتك للتأكيد الفوري.'
            ],
            'palpay' => [
                'name'         => 'بال باي (PalPay - محفظتي) 💳',
                'service_code' => \App\Models\Setting::get('palpay_service_code', '99420'),
                'merchant_name'=> $palOwner . ' - كود الخدمة المعتمد',
                'instructions' => 'ادفع عبر تطبيق محفظتي أو أي نقطة بيع بال باي في كافة مدن وقرى الضفة وغزة باستخدام كود الخدمة أو رقم الحساب ' . $palPhone . '.'
            ],
            'bop' => [
                'name'         => 'بنك فلسطين (Bank of Palestine) 🏦',
                'bank_name'    => \App\Models\Setting::get('payment_bank_name', 'بنك فلسطين - الإدارة العامة'),
                'account_no'   => \App\Models\Setting::get('payment_account_no', '0458-123456-001'),
                'account_owner'=> $palOwner,
                'iban'         => \App\Models\Setting::get('payment_iban', 'PS91PALS0458000000123456001'),
                'swift'        => 'PALSPS22',
                'instructions' => 'حوالة بنكية عبر تطبيق بنكي (أونلاين) لحساب المستفيد: ' . $palOwner . ' ورقم الهاتف: ' . $palPhone . '.'
            ]
        ];

        return view('student.checkout.index', compact('cart', 'student', 'palGatewaysConfig'));
    }

    /**
     * معالجة الدفع والتفعيل الفوري للاشتراك
     */
    public function processPayment(Request $request)
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح'], 401);
        }

        $cart = session('checkout_cart');
        if (!$cart || empty($cart['items'])) {
            return response()->json(['status' => 'error', 'message' => 'سلة المواد فارغة أو منتهية الصلاحية.'], 422);
        }

        $request->validate([
            'gateway'      => 'required|in:jawwal_pay,palpay,bop,voucher',
            'wallet_phone' => 'required_if:gateway,jawwal_pay|nullable|string',
            'palpay_ref'   => 'required_if:gateway,palpay|nullable|string',
            'bop_ref'      => 'required_if:gateway,bop|nullable|string',
            'receipt_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:4096',
        ], [
            'gateway.required'          => 'يرجى اختيار طريقة الدفع الفلسطينية المناسبة.',
            'wallet_phone.required_if'  => 'يرجى إدخال رقم محفظة جوال باي الخاصة بك.',
            'palpay_ref.required_if'    => 'يرجى إدخال رقم العملية أو كود السداد في بال باي.',
            'bop_ref.required_if'       => 'يرجى إدخال رقم الحوالة أو المرجع البنكي.',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_file') && $request->file('receipt_file')->isValid()) {
            $receiptPath = $request->file('receipt_file')->store('receipts/payments', 'public');
        }

        // إنشاء رقم عملية مرجعي فلسطيني موحد
        $txNumber = 'PAL-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $details = [
            'gateway'      => $request->gateway,
            'wallet_phone' => $request->wallet_phone,
            'reference_no' => $request->palpay_ref ?? $request->bop_ref ?? $request->voucher_code,
            'ip'           => $request->ip(),
            'paid_at'      => now()->toDateTimeString(),
        ];

        // 1. تسجيل المعاملة في جدول المدفوعات
        $payment = Payment::create([
            'student_id'         => $student->id,
            'transaction_number' => $txNumber,
            'gateway'            => $request->gateway,
            'amount'             => $cart['total'],
            'currency'           => 'ILS',
            'status'             => 'completed',
            'payment_details'    => json_encode($details, JSON_UNESCAPED_UNICODE),
            'items'              => $cart['items'],
            'receipt_path'       => $receiptPath
        ]);

        // 2. تفعيل اشتراكات الطالب في المواد فورياً
        $subjectNames = [];
        foreach ($cart['items'] as $item) {
            Enrollment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $item['id']
                ],
                [
                    'status'         => 'active',
                    'access_mode'    => 'all',
                    'payment_status' => $request->gateway,
                    'activated_at'   => now(),
                    'expires_at'     => now()->addDays(365),
                ]
            );
            $subjectNames[] = $item['name_ar'];
        }

        // 3. إرسال إشعار فوري للطالب
        $namesStr = implode('، ', $subjectNames);
        NotificationService::notifyStudent(
            $student->id,
            'تم تفعيل اشتراكك بنجاح! 🎉',
            "مبارك! تم تفعيل اشتراكك في مواد ({$namesStr}) عبر {$payment->gateway_name_ar}. نتمنى لك أعلى الدرجات والتفوق الوزاري!",
            'payment',
            route('student.dashboard')
        );

        // 4. إرسال تنبيه لإدارة المنصة
        NotificationService::notifyAdmin(
            'عملية دفع جديدة عبر ' . $payment->gateway_name_ar,
            "قام الطالب {$student->name_ar} بسداد مبلغ {$payment->amount} ₪ للاشتراك في ({$namesStr}). رقم العملية: {$txNumber}",
            'payment'
        );

        // تفريغ السلة
        session()->forget('checkout_cart');

        return response()->json([
            'status'   => 'success',
            'message'  => 'تم تأكيد الدفع وتفعيل المواد بنجاح! 🎉',
            'redirect' => route('student.checkout.receipt', $payment->id)
        ]);
    }

    /**
     * استعراض الفاتورة الرقمية وإشعار التفعيل المعتمد
     */
    public function successReceipt($id)
    {
        $student = CurrentActor::student() ?? Auth::guard('student')->user();
        $payment = Payment::with('student')->findOrFail($id);

        if ($student && $payment->student_id !== $student->id && !Auth::guard('web')->check()) {
            abort(403, 'غير مصرح باستعراض هذه الفاتورة');
        }

        return view('student.checkout.receipt', compact('payment'));
    }
}
