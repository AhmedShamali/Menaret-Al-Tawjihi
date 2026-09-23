<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // 1. واجهة الإدارة (الجدول والتحكم)
    public function index() {
        $students = Student::with('stage')->latest()->get();
        return view('admin.students.index', compact('students'));
    }

    // 2. ملف الطالب الشخصي (يدعم استعراض الأدمن عبر المعرف، أو استعراض الطالب لصفحته الشخصية)
    public function profile($id = null)
    {
        if ($id && Auth::guard('web')->check() && Auth::user()->role === 'admin') {
            $student = Student::with('stage')->findOrFail($id);
            return view('admin.students.profile', compact('student'));
        }

        $student = Auth::guard('student')->user() ?? Auth::user();
        if (!$student) {
            return redirect()->route('login');
        }

        return view('student.profile', compact('student'));
    }

    // 3. عرض كافة البروفايلات (للأدمن)
    public function profile_all()
    {
        $students = Student::latest()->paginate(10);
        return view('admin.students.profile-all', compact('students'));
    }

    public function create() {
        if (\App\Models\Stage::where('grade_level', '>=', 120)->count() < 3 || \App\Models\Subject::count() === 0) {
            (new \Database\Seeders\StageSeeder())->run();
            (new \Database\Seeders\SubjectSeeder())->run();
        }

        $stages = Stage::with('subjects')
            ->where('grade_level', '>=', 120)
            ->orderBy('grade_level', 'desc')
            ->get();

        if ($stages->isEmpty()) {
            $stages = Stage::all();
        }

        // إذا كان المستخدم الحالي مديراً، يتم إظهار واجهة تسجيل الطالب الإدارية
        if (Auth::guard('web')->check() && Auth::user()->role === 'admin') {
            return view('admin.management.students_create', compact('stages'));
        }

        return view('students.create', compact('stages'));
    }

    public function showSubject($id)
    {
        $subject = \App\Models\Subject::with(['stage', 'teacher', 'contents'])->findOrFail($id);

        // جلب الفيديوهات
        $videos = $subject->contents->filter(function ($item) {
            return !empty($item->url_path);
        })->sortBy('order');

        // جلب الملفات
        $files = $subject->contents->filter(function ($item) {
            return !empty($item->pdf_path);
        })->sortBy('order');

        return view('student.subjects.show', compact('subject', 'videos', 'files'));
    }

    // 4. دالة حفظ وتسجيل الطالب (تدعم التسجيل الذاتي للطلاب وإضافة الأدمن)
    public function store(Request $request) {
        // تنظيف البريد وتثبيت النطاق المعتمد @tawjihi.ps بشكل صارم
        $rawEmail = trim($request->input('email', ''));
        $username = preg_replace('/[^a-zA-Z0-9._-]/', '', strtolower(explode('@', $rawEmail)[0]));
        if (empty($username)) {
            $username = 'std' . rand(1000, 9999);
        }
        $officialEmail = $username . '@tawjihi.ps';
        $request->merge(['email' => $officialEmail]);

        $validator = Validator::make($request->all(), [
            'name_ar'       => 'required|string|max:255',
            'nid'           => 'required|digits:9|unique:students,nid',
            'email'         => 'required|email|unique:students,email',
            'password'      => 'required|min:6',
            'stage_id'      => 'required',
            'phone'         => 'nullable|string|max:20',
            'whatsapp'      => 'nullable|string|max:20',
            'guardian_phone'=> 'nullable|string|max:20',
            'city'          => 'nullable|string|max:100',
            'school_name'   => 'nullable|string|max:255',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'id_photo'      => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:6144',
        ], [
            'name_ar.required' => 'يرجى كتابة الاسم الرباعي كاملاً.',
            'nid.required'     => 'يرجى إدخال رقم الهوية الفلسطينية.',
            'nid.digits'       => 'رقم الهوية يجب أن يتكون من 9 أرقام.',
            'nid.unique'       => 'رقم الهوية هذا مسجل مسبقاً في المنصة.',
            'email.required'   => 'اسم المستخدم للبريد الأكاديمي مطلوب.',
            'email.unique'     => 'اسم المستخدم هذا مسجل مسبقاً، يرجى اختيار اسم مستخدم آخر.',
            'password.min'     => 'كلمة المرور يجب أن لا تقل عن 6 خانات.',
            'stage_id.required'=> 'يرجى اختيار الفرع أو المرحلة الدراسية.',
            'photo.image'      => 'الصورة الشخصية يجب أن تكون ملف صورة صالح (JPG, PNG, WEBP).',
            'photo.max'        => 'حجم الصورة الشخصية يجب ألا يتجاوز 5 ميغابايت.',
            'id_photo.max'     => 'حجم صورة الهوية يجب ألا يتجاوز 6 ميغابايت.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['icon' => 'error', 'title' => $validator->errors()->first()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // رفع وتخزين الصورة الشخصية اختيارياً
        $photoPath = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        // رفع وتخزين صورة الهوية اختيارياً
        $idPhotoPath = null;
        if ($request->hasFile('id_photo') && $request->file('id_photo')->isValid()) {
            $idPhotoPath = $request->file('id_photo')->store('students/ids', 'public');
        }

        // مطابقة الجنس بدقة مع قيود قاعدة البيانات PostgreSQL (ذكر / أنثى)
        $rawGender = $request->input('gender', 'ذكر');
        $gender = ($rawGender === 'أنثى' || $rawGender === 'female') ? 'أنثى' : 'ذكر';

        // مطابقة معرف فرع التوجيهي (سواء تم إرسال الـ ID أو رقم المرحلة 121, 122, 123)
        $stage = Stage::where('id', $request->stage_id)
            ->orWhere('grade_level', $request->stage_id)
            ->first();
        $stageId = $stage ? $stage->id : (Stage::where('grade_level', 122)->value('id') ?? 1);

        $phone = $request->phone ?: '0590000000';
        $age = $request->age ? (int)$request->age : 18;
        $nameEn = $request->name_en ?: $request->name_ar;
        $city = $request->input('city', 'رام الله والبيرة');
        $schoolName = $request->input('school_name');
        $guardianPhone = $request->input('guardian_phone', $request->input('whatsapp'));

        $isAdmin = Auth::guard('web')->check();
        $accountStatus = $isAdmin ? 'active' : 'pending';

        // إعداد مصفوفة بيانات الطالب
        $studentData = [
            'name_ar'            => $request->name_ar,
            'name_en'            => $nameEn,
            'nid'                => $request->nid,
            'age'                => $age,
            'email'              => $request->email,
            'phone'              => $phone,
            'whatsapp'           => $request->whatsapp ?? $guardianPhone ?? $phone,
            'guardian_phone'     => $guardianPhone,
            'city'               => $city,
            'school_name'        => $schoolName,
            'password'           => Hash::make($request->password),
            'plain_password'     => $request->password,
            'stage_id'           => $stageId,
            'gender'             => $gender,
            'photo'              => $photoPath,
            'id_photo'           => $idPhotoPath,
            'avatar_url'         => null,
            'google_id'          => null,
            'provider'           => null,
            'status'             => $accountStatus,
            'streak_count'       => 1,
            'total_points'       => 50,
            'last_activity_date' => now()->toDateString(),
        ];

        // إنشاء حساب الطالب مع تعيين الحالة: معلق (pending) بانتظار موافقة المدير إن كان تسجيلاً ذاتياً
        try {
            $student = Student::create($studentData);
        } catch (\Illuminate\Database\QueryException $e) {
            // استبعاد الأعمدة الإضافية في حال عدم اكتمال هجرة قاعدة البيانات الخارجية
            unset(
                $studentData['city'], $studentData['school_name'], $studentData['guardian_phone'],
                $studentData['google_id'], $studentData['provider'], $studentData['avatar_url']
            );
            try {
                $student = Student::create($studentData);
            } catch (\Illuminate\Database\QueryException $e2) {
                unset($studentData['plain_password']);
                $student = Student::create($studentData);
            }
        }


        // إذا اختار الطالب مواد محددة عند التسجيل، تسجل بحالة معلقة pending بانتظار موافقة وسداد الإدارة
        if ($request->has('subject_ids') && is_array($request->subject_ids) && count($request->subject_ids) > 0) {
            foreach ($request->subject_ids as $subId) {
                \App\Models\Enrollment::firstOrCreate(
                    ['student_id' => $student->id, 'subject_id' => $subId],
                    [
                        'status'         => $isAdmin ? 'active' : 'pending',
                        'access_mode'    => 'all',
                        'payment_status' => $isAdmin ? 'admin_grant' : 'pending',
                        'activated_at'   => $isAdmin ? now() : null
                    ]
                );
            }
        }

        // احتساب القسط الشهري الدقيق بناءً على المواد الدراسية المختارة وحزم المنهاج
        $feeBreakdown = $student->getFeeBreakdown();
        if ($feeBreakdown['base_after_bundle'] > 0) {
            $student->monthly_fee = $feeBreakdown['base_after_bundle'];
            $student->save();
        }

        // إذا كان تسجيلاً من قبل مدير مسجل، يتم توجيهه للوحة إدارة الطلاب
        if ($isAdmin) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'icon'     => 'success',
                    'title'    => 'تم تسجيل الطالب بنجاح! 🎉',
                    'redirect' => route('admin.students.index')
                ]);
            }

            return redirect()->route('admin.students.index')->with('success', 'تم تسجيل الطالب بنجاح! 🎉');
        }

        // إشعار إدارة المنصة فوراً بتسجيل طالب جديد للمراجعة والاعتماد
        if (!$isAdmin) {
            try {
                $stageObj = Stage::find($stageId);
                $stageName = $stageObj ? ($stageObj->label_ar ?? $stageObj->name_ar ?? 'الثانوية العامة') : 'الثانوية العامة';
                \App\Services\NotificationService::notifyAdmin(
                    'تسجيل طالب جديد 🎓',
                    "قام الطالب ({$student->name_ar}) بإنشاء حساب جديد في {$stageName}، وحسابه بانتظار الاعتماد والموافقة وسداد الرسوم.",
                    'student',
                    route('admin.students.show', $student->id),
                    'fa-user-plus'
                );
            } catch (\Throwable $e) {
                \Log::error('Admin registration notification error: ' . $e->getMessage());
            }
        }

        // تسجيل دخول الطالب وتوجيهه لصفحة انتظار موافقة واعتماد المدير وبوابة الدفع
        Auth::guard('student')->login($student);
        $request->session()->regenerate();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'icon'     => 'info',
                'title'    => 'تم استلام طلبك بنجاح! ⏳',
                'text'     => 'يرجى مراجعة إشعار سداد الرسوم لإتمام تفعيل اشتراكك.',
                'redirect' => route('student.pending-approval')
            ]);
        }

        return redirect()->route('student.pending-approval')->with('info', 'تم استلام طلبك بنجاح! يرجى إتمام سداد الرسوم لمراجعة واعتماد اشتراكك.');
    }

    /**
     * صفحة انتظار الموافقة وبوابة سداد رسوم الاشتراك للطالب الجديد
     */
    public function pendingApproval()
    {
        $student = \App\Support\CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return redirect()->route('login');
        }

        if ($student->status === 'active' && !$student->isMonthlyFeeDue()) {
            return redirect()->route('student.dashboard');
        }

        $pendingEnrollments = \App\Models\Enrollment::with(['subject.stage'])
            ->where('student_id', $student->id)
            ->get();

        // احتساب التفصيل المالي الدقيق والشفاف للمواد المسجلة
        $feeBreakdown = $student->getFeeBreakdown();

        // تحديث رسوم الطالب إذا كانت مسجلة بالافتراضي القديم (150) ولديه مواد مسجلة حقيقية
        if ($pendingEnrollments->isNotEmpty() && $feeBreakdown['base_after_bundle'] > 0 && abs((float)$student->monthly_fee - 150.00) < 0.01) {
            $student->monthly_fee = $feeBreakdown['base_after_bundle'];
            $student->save();
        }

        // مزامنة وتحديث سجل الاشتراكات الشهرية للعام الأكاديمي
        try {
            \App\Models\StudentMonthlySubscription::syncWithStudentPayments($student);
        } catch (\Throwable $e) {}

        $financialSummary = $student->getFinancialSummary('2026-2027');
        $subscriptions = $financialSummary['subscriptions'];
        $dueMonthIndex = $financialSummary['active_due_month'];
        $dueMonthName = $financialSummary['active_due_month_name'];
        $monthlyFee = (float) $feeBreakdown['base_after_bundle'];
        $discountAmount = (float) $feeBreakdown['student_discount'];
        $requestedAmount = request()->filled('amount') ? (float) request('amount') : null;
        $requestedMonth = request()->filled('month') ? (int) request('month') : null;
        $requestedType = request()->input('type', 'due');

        $finalAmount = (float) ($requestedAmount ?: ($financialSummary['total_due_now'] > 0 ? $financialSummary['total_due_now'] : $feeBreakdown['final_amount']));
        $totalAmount = (float) $feeBreakdown['subtotal'];
        $bundleDiscount = (float) $feeBreakdown['bundle_discount'];
        $isFeeDue = $financialSummary['total_due_now'] > 0;

        $latestPayment = \App\Models\Payment::where('student_id', $student->id)->latest()->first();

        return view('student.pending_approval', compact(
            'student', 'pendingEnrollments', 'latestPayment', 
            'totalAmount', 'discountAmount', 'finalAmount', 'bundleDiscount', 'feeBreakdown',
            'subscriptions', 'dueMonthIndex', 'dueMonthName', 'monthlyFee', 'isFeeDue',
            'financialSummary', 'requestedAmount', 'requestedMonth', 'requestedType'
        ));
    }

    /**
     * رفع وإرسال إشعار السداد من قبل الطالب أثناء انتظار الموافقة
     */
    public function submitPendingPayment(Request $request)
    {
        $student = \App\Support\CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 401);
        }

        $request->validate([
            'payment_method' => 'required|string',
            'receipt_photo'  => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:8192',
            'receipt_file'   => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:8192',
        ], [
            'payment_method.required' => 'يرجى تحديد وسيلة الدفع المستخدمة.',
        ]);

        $receiptFile = $request->file('receipt_photo') ?? $request->file('receipt_file');
        $receiptPath = null;
        if ($receiptFile && $receiptFile->isValid()) {
            $receiptPath = $receiptFile->store('payments/receipts', 'public');
        }

        $txNo = $request->input('reference_no') ?: ($request->input('transaction_number') ?: 'TXN-' . time());
        $financialSummary = $student->getFinancialSummary('2026-2027');
        $suggestedAmount = $financialSummary['total_due_now'] > 0 ? $financialSummary['total_due_now'] : $student->monthlyAmountDue();
        $amount = (float)($request->input('amount') ?: ($suggestedAmount > 0 ? $suggestedAmount : 150));

        // تحويل اسم وسيلة الدفع إلى رمز البوابة المتوافق مع جدول payments
        $rawMethod = strtolower($request->payment_method);
        $gateway = 'jawwal_pay';
        if (str_contains($rawMethod, 'palpay') || str_contains($rawMethod, 'بال باي')) {
            $gateway = 'palpay';
        } elseif (str_contains($rawMethod, 'bank') || str_contains($rawMethod, 'فلسطين') || str_contains($rawMethod, 'bop')) {
            $gateway = 'bop';
        } elseif (str_contains($rawMethod, 'reflect') || str_contains($rawMethod, 'ريفلكت')) {
            $gateway = 'reflect';
        } elseif (str_contains($rawMethod, 'cash') || str_contains($rawMethod, 'نقد')) {
            $gateway = 'cash';
        } elseif (str_contains($rawMethod, 'voucher') || str_contains($rawMethod, 'كارت')) {
            $gateway = 'voucher';
        }

        $dueMonthName = $student->currentDueMonthName();
        $paymentDetails = json_encode([
            'payment_method_label' => $request->payment_method,
            'reference_no'         => $request->input('reference_no'),
            'month_target'         => $dueMonthName,
            'month_index'          => $student->currentDueMonth(),
            'notes'                => $request->input('notes', "إشعار سداد رسوم {$dueMonthName}"),
            'submitted_at'         => now()->toDateTimeString(),
        ], JSON_UNESCAPED_UNICODE);

        $payment = \App\Models\Payment::create([
            'student_id'         => $student->id,
            'transaction_number' => $txNo,
            'gateway'            => $gateway,
            'amount'             => $amount,
            'currency'           => 'ILS',
            'status'             => 'pending',
            'payment_details'    => $paymentDetails,
            'receipt_path'       => $receiptPath,
        ]);

        // تحديث حالة الشهر المستحق في جدول الاشتراكات إلى pending
        try {
            $dueMonth = $student->currentDueMonth();
            \App\Models\StudentMonthlySubscription::updateOrCreate(
                ['student_id' => $student->id, 'academic_year' => '2026-2027', 'month' => $dueMonth],
                ['status' => 'pending', 'amount' => $amount, 'notes' => "إشعار سداد رقم {$txNo}"]
            );
        } catch (\Throwable $e) {}

        // إشعار إدارة المنصة فوراً لوصول إشعار سداد من الطالب
        try {
            $gwLabel = $payment->gateway_name_ar ?? $request->payment_method;
            \App\Services\NotificationService::notifyAdmin(
                'إشعار سداد رسوم جديد 💳',
                "قام الطالب ({$student->name_ar}) برفع إشعار دفع جديد لرسوم ({$dueMonthName}) عبر ({$gwLabel}) بمبلغ ({$payment->amount} ₪).",
                'payment',
                route('admin.payments.index'),
                'fa-receipt'
            );
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "تم استلام إشعار سداد ({$dueMonthName}) بنجاح! سيقوم المشرف العام بمطابقته واعتماد حسابك فورياً. 🎉"
            ]);
        }

        return back()->with('payment_success', "تم استلام إشعار سداد ({$dueMonthName}) بنجاح! سيقوم المشرف العام بمراجعته واعتماد حسابك واشتراكك فورياً.");
    }

    /**
     * اعتماد وتفعيل حساب الطالب واشتراكه من قبل المدير
     */
    public function approveStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->status = 'active';

        try {
            if (!$student->approved_at) {
                $student->approved_at = now();
            }
            $student->freeze_reason = null;
            $student->save();
        } catch (\Throwable $e) {
            \DB::table('students')->where('id', $student->id)->update([
                'status' => 'active',
                'approved_at' => now(),
                'freeze_reason' => null
            ]);
        }

        // تفعيل كافة المواد المقيد بها الطالب أو تسجيل مواد مرحلته تلقائياً
        try {
            $affected = \App\Models\Enrollment::where('student_id', $student->id)->update([
                'status'         => 'active',
                'payment_status' => 'paid',
                'activated_at'   => now(),
            ]);

            if ($affected === 0 && $student->stage_id) {
                $stageSubjects = \App\Models\Subject::where('stage_id', $student->stage_id)->pluck('id');
                foreach ($stageSubjects as $subId) {
                    \App\Models\Enrollment::firstOrCreate(
                        ['student_id' => $student->id, 'subject_id' => $subId],
                        [
                            'status'         => 'active',
                            'access_mode'    => 'all',
                            'payment_status' => 'admin_grant',
                            'activated_at'   => now(),
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Approve student enrollment error: ' . $e->getMessage());
        }

        // تحديث أي مدفوعات معلقة بأمان
        try {
            \App\Models\Payment::where('student_id', $student->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'completed',
                ]);
        } catch (\Throwable $e) {
            \Log::error('Approve student payment update error: ' . $e->getMessage());
        }

        // تأكيد سداد الشهر الأول للطالب وتثبيته في سجل الاشتراكات
        try {
            $month1 = \App\Models\StudentMonthlySubscription::firstOrCreate(
                ['student_id' => $student->id, 'academic_year' => '2026-2027', 'month' => 1],
                ['amount' => $student->monthlyAmountDue(), 'status' => 'paid', 'paid_at' => now()]
            );
            $month1->update(['status' => 'paid', 'paid_at' => now()]);

            // مزامنة باقي الشهور
            \App\Models\StudentMonthlySubscription::syncWithStudentPayments($student, '2026-2027');
        } catch (\Throwable $e) {}

        // إشعار الطالب بالاعتماد والتفعيل
        try {
            \App\Services\NotificationService::notifyStudent(
                $student->id,
                'تم اعتماد وتفعيل حسابك بنجاح! 🎉',
                "أهلاً بك يا {$student->name_ar}! قامت إدارة المنصة بالموافقة على حسابك واشتراكك، وبإمكانك الآن الوصول لكافة الدروس والاختبارات.",
                'account',
                route('student.dashboard'),
                'fa-circle-check'
            );
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "تم اعتماد حساب الطالب ({$student->name_ar}) وتفعيل اشتراكه في المنصة بنجاح!"
            ]);
        }

        return back()->with('success', "تم اعتماد حساب الطالب ({$student->name_ar}) وتفعيل اشتراكه في المنصة بنجاح!");
    }

    /**
     * تحديد وتعديل الرسوم الشهرية المقررة للطالب من قبل المدير
     */
    public function updateMonthlyFee(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $request->validate([
            'monthly_fee' => 'required|numeric|min:0',
        ]);

        $student->monthly_fee = (float) $request->monthly_fee;
        $student->save();

        // تحديث رسوم الشهور غير المسددة للعام الحالي
        try {
            \App\Models\StudentMonthlySubscription::where('student_id', $student->id)
                ->where('academic_year', '2026-2027')
                ->where('status', 'unpaid')
                ->update(['amount' => $student->monthlyAmountDue()]);
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'            => true,
                'message'            => 'تم تحديث الرسوم الشهرية بنجاح (' . number_format($student->monthly_fee, 2) . ' ₪)',
                'monthly_fee'        => $student->monthly_fee,
                'monthly_amount_due' => $student->monthlyAmountDue(),
            ]);
        }

        return back()->with('success', 'تم تحديث الرسوم الشهرية للطالب بنجاح.');
    }

    /**
     * تجميد أو فك تجميد حساب الطالب مع تخزين سبب التجميد
     */
    public function toggleStatus(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $isCurrentlyActive = ($student->status === 'active');
        $newStatus = $isCurrentlyActive ? 'suspended' : 'active';
        $reason = $request->input('freeze_reason');

        $student->status = $newStatus;
        if ($newStatus === 'suspended') {
            $student->freeze_reason = $reason ?: 'عدم سداد الرسوم الدراسية أو مراجعة النشاط الأكاديمي والالتزام.';
        } else {
            $student->freeze_reason = null;
        }

        try {
            $student->save();
        } catch (\Throwable $e) {
            // استبعاد حقل freeze_reason إذا لم تكتمل الهجرة
            \DB::table('students')->where('id', $student->id)->update([
                'status' => $newStatus,
                'freeze_reason' => $newStatus === 'suspended' ? ($reason ?: 'عدم سداد الرسوم الدراسية') : null
            ]);
        }

        // إرسال إشعار فوري للطالب بحالة حسابه
        try {
            if ($newStatus === 'suspended') {
                \App\Services\NotificationService::notifyStudent(
                    $student->id,
                    'تنبيه رسمي: تم تجميد الحساب مؤقتاً 🔒',
                    "نحيطك علماً بأنه قد تم تجميد حسابك الدراسي. سبب التجميد: " . ($student->freeze_reason),
                    'system',
                    route('student.pending-approval'),
                    'fa-lock'
                );
            } else {
                \App\Services\NotificationService::notifyStudent(
                    $student->id,
                    'تم إلغاء تجميد الحساب وتفعيله بنجاح! 🎉',
                    "أهلاً بك يا {$student->name_ar}! قامت إدارة المنصة برفع التجميد وتفعيل حسابك، وبإمكانك استئناف دراستك الآن.",
                    'account',
                    route('student.dashboard'),
                    'fa-circle-check'
                );
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'status'  => $newStatus,
            'message' => $newStatus === 'active' ? 'تم إعادة تفعيل حساب الطالب بنجاح.' : 'تم تجميد حساب الطالب وحفظ سبب التجميد بنجاح.'
        ]);
    }

    /**
     * إرسال رمز التحقق الحي عند تسجيل حساب جديد (Live OTP)
     */
    public function sendRegistrationOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@tawjihi-gaza\.ps$/i',
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.regex'    => 'البريد يجب أن يكون بصيغة [username@tawjihi-gaza.ps].',
        ]);

        $otp = (string) rand(100000, 999999);
        session([
            'register_otp' => $otp,
            'register_otp_email' => strtolower(trim($request->email)),
            'register_otp_time' => now()->timestamp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء رمز التحقق الحي: (' . $otp . ') للتجربة الفورية والمصادقة.',
            'demo_otp' => $otp
        ]);
    }

    /**
     * مطابقة رمز التحقق الحي
     */
    public function verifyRegistrationOtp(Request $request)
    {
        $code = trim($request->input('otp', ''));
        $savedOtp = session('register_otp');

        if (!empty($savedOtp) && $code === (string)$savedOtp) {
            session(['register_otp_verified' => true]);
            return response()->json([
                'success' => true,
                'message' => 'تم تأكيد البريد الإلكتروني بنجاح! ✅'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'رمز التحقق غير صحيح، يرجى إعادة المحاولة.'
        ], 422);
    }


    public function edit($id) {
        $student = Student::with(['enrolledSubjects', 'enrollments.subject'])->findOrFail($id);
        $stages = Stage::with(['subjects.teacher'])->get();
        return view('students.edit', compact('student', 'stages'));
    }

    // 5. دالة التحديث (Update) - معدلة لدعم مزامنة المواد للطلاب الحاليين والقدامى
    public function update(Request $request, $id) {
        $student = Student::findOrFail($id);

        // استبعاد الصور وكلمة المرور من التحديث التلقائي لمعالجتها يدوياً
        $data = $request->except(['password', 'photo', 'id_photo', 'manage_subjects', 'subject_ids']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['plain_password'] = $request->password;
        }

        if ($request->hasFile('photo')) {
            // اختياري: حذف الصورة القديمة من السيرفر لتوفير المساحة
            if($student->photo) Storage::disk('public')->delete($student->photo);

            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        if ($request->hasFile('id_photo')) {
            if($student->id_photo) Storage::disk('public')->delete($student->id_photo);

            $data['id_photo'] = $request->file('id_photo')->store('students/ids', 'public');
        }

        $student->update($data);

        // مزامنة المواد الدراسية إذا أُرسلت من صفحة التعديل
        if ($request->has('manage_subjects')) {
            $selectedSubjectIds = $request->input('subject_ids', []);
            if (!is_array($selectedSubjectIds)) {
                $selectedSubjectIds = [];
            }

            // حذف اشتراكات المواد التي أُلغي تحديدها
            \App\Models\Enrollment::where('student_id', $student->id)
                ->whereNotIn('subject_id', $selectedSubjectIds)
                ->delete();

            // تفعيل أو إضافة المواد المحددة
            foreach ($selectedSubjectIds as $subId) {
                \App\Models\Enrollment::updateOrCreate(
                    ['student_id' => $student->id, 'subject_id' => $subId],
                    [
                        'status'         => 'active',
                        'access_mode'    => 'all',
                        'payment_status' => 'admin_grant',
                        'activated_at'   => now(),
                    ]
                );
            }
        }

        return response()->json(['icon' => 'success', 'title' => 'تم تحديث البيانات والمواد بنجاح 🚀']);
    }





    /**
     * تحديد وتحديث الخصم أو المنحة المخصصة للطالب من قِبل المدير
     */
    public function updateDiscount(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'discount_type'    => 'required|in:percent,fixed,none',
            'discount_value'   => 'nullable|numeric|min:0',
            'discount_notes'   => 'nullable|string|max:255',
        ], [
            'discount_type.required' => 'نوع الخصم مطلوب.',
            'discount_value.numeric' => 'قيمة الخصم يجب أن تكون رقماً صحيحاً.',
            'discount_value.min'     => 'قيمة الخصم يجب ألا تقل عن صفر.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'icon'    => 'error',
                'title'   => $validator->errors()->first()
            ], 422);
        }

        $type = $request->input('discount_type');
        $val = (float) $request->input('discount_value', 0);
        $notes = $request->input('discount_notes');

        if ($type === 'none' || $val <= 0) {
            $student->custom_discount_percent = 0;
            $student->custom_discount_fixed = 0;
            $student->discount_notes = null;
            $student->save();

            return response()->json([
                'success'        => true,
                'icon'           => 'info',
                'title'          => 'تم إلغاء الخصم بنجاح',
                'message'        => "تمت إزالة الخصم عن الطالب {$student->name_ar}.",
                'discount_label' => 'بدون خصم',
                'has_discount'   => false,
                'percent'        => 0,
                'fixed'          => 0,
                'notes'          => null
            ]);
        }

        if ($type === 'percent') {
            if ($val > 100) {
                $val = 100;
            }
            $student->custom_discount_percent = $val;
            $student->custom_discount_fixed = 0;
        } else {
            $student->custom_discount_percent = 0;
            $student->custom_discount_fixed = $val;
        }

        $student->discount_notes = $notes;
        $student->save();

        // إرسال إشعار فوري للطالب في حسابه
        try {
            $discountText = $type === 'percent' 
                ? "خصم بقيمة " . round($val) . "%" 
                : "خصم بقيمة " . round($val) . " ₪";
            
            if ($val >= 100 && $type === 'percent') {
                $discountText = "إعفاء كامل بنسبة 100% (منحة مجانية شاملة)";
            }

            $reasonText = $notes ? " - السبب: {$notes}" : "";

            \App\Services\NotificationService::notifyStudent(
                $student->id,
                'مبارك! تم منحك خصماً خاصاً من إدارة المنصة 🏷️🎉',
                "قررت إدارة المنصة منحك {$discountText}{$reasonText} على اشتراكات المواد الدراسية. يمكنك الآن الاستفادة من الخصم مباشرة عند الاشتراك.",
                'discount',
                url('/student/dashboard')
            );
        } catch (\Throwable $e) {}

        return response()->json([
            'success'        => true,
            'icon'           => 'success',
            'title'          => 'تم حفظ الخصم بنجاح! 🏷️',
            'message'        => "تم تحديث الخصم للطالب ({$student->name_ar}) بنجاح وإرسال إشعار له.",
            'discount_label' => $student->discount_label,
            'has_discount'   => $student->hasDiscount(),
            'percent'        => (float)$student->custom_discount_percent,
            'fixed'          => (float)$student->custom_discount_fixed,
            'notes'          => $student->discount_notes
        ]);
    }



    public function destroy($id) {
        $student = Student::findOrFail($id);
        
        // حذف الصور عند حذف الطالب
        if($student->photo) Storage::disk('public')->delete($student->photo);
        if($student->id_photo) Storage::disk('public')->delete($student->id_photo);

        $studentId = $student->id;

        \DB::beginTransaction();
        try {
            // 1. حذف التكليفات والامتحانات التابعة للتسجيلات
            if (\Illuminate\Support\Facades\Schema::hasTable('enrollments') && \Illuminate\Support\Facades\Schema::hasTable('exam_assignments')) {
                $enrIds = \DB::table('enrollments')->where('student_id', $studentId)->pluck('id')->toArray();
                if (!empty($enrIds)) {
                    \DB::table('exam_assignments')->whereIn('enrollment_id', $enrIds)->delete();
                }
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('exam_assignments')) {
                \DB::table('exam_assignments')->where('student_id', $studentId)->delete();
            }

            // 2. حذف إجابات الامتحانات عبر معرفات التقديم الصحيحة (exam_submission_id)
            if (\Illuminate\Support\Facades\Schema::hasTable('exam_submissions') && \Illuminate\Support\Facades\Schema::hasTable('submission_answers')) {
                $subIds = \DB::table('exam_submissions')->where('student_id', $studentId)->pluck('id')->toArray();
                if (!empty($subIds)) {
                    \DB::table('submission_answers')->whereIn('exam_submission_id', $subIds)->delete();
                }
            }

            // 3. حذف تسليمات الامتحانات
            if (\Illuminate\Support\Facades\Schema::hasTable('exam_submissions')) {
                \DB::table('exam_submissions')->where('student_id', $studentId)->delete();
            }

            // 4. حذف الاشتراكات الشهرية أولاً قبل المدفوعات لفك القيد الخارجي
            if (\Illuminate\Support\Facades\Schema::hasTable('student_monthly_subscriptions')) {
                \DB::table('student_monthly_subscriptions')->where('student_id', $studentId)->delete();
            }

            // 5. حذف المدفوعات والإيصالات
            if (\Illuminate\Support\Facades\Schema::hasTable('payments')) {
                \DB::table('payments')->where('student_id', $studentId)->delete();
            }

            // 6. حذف التسجيلات بالمواد
            if (\Illuminate\Support\Facades\Schema::hasTable('enrollments')) {
                \DB::table('enrollments')->where('student_id', $studentId)->delete();
            }

            // 7. حذف الجداول التابعة الأخرى
            $simpleStudentTables = [
                'certificates',
                'recommendations',
                'activities',
                'channel_requests',
                'placement_results',
                'support_tickets',
                'flashcards',
                'video_notes',
                'student_progress',
            ];

            foreach ($simpleStudentTables as $tbl) {
                if (\Illuminate\Support\Facades\Schema::hasTable($tbl)) {
                    \DB::table($tbl)->where('student_id', $studentId)->delete();
                }
            }

            // 8. حذف الرسائل
            if (\Illuminate\Support\Facades\Schema::hasTable('messages')) {
                \DB::table('messages')
                    ->where('student_id', $studentId)
                    ->orWhere(function($q) use ($studentId) {
                        $q->where('sender_type', 'student')->where('sender_id', $studentId);
                    })
                    ->delete();
            }

            // 9. تفريغ كوبونات الدخول إن استخدمت
            if (\Illuminate\Support\Facades\Schema::hasTable('access_vouchers')) {
                \DB::table('access_vouchers')->where('used_by_student_id', $studentId)->update([
                    'used_by_student_id' => null,
                    'is_used'            => false,
                    'used_at'            => null,
                ]);
            }

            // 10. حذف الإشعارات التابعة للطالب
            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \DB::table('notifications')
                    ->where('notifiable_type', 'like', '%Student%')
                    ->where('notifiable_id', $studentId)
                    ->delete();
            }

            // 11. حذف سجل الطالب النهائي
            $student->delete();
            \DB::commit();

            return response()->json(['success' => true, 'message' => 'تم حذف حساب الطالب وكافة سجلاته واشتراكاته بنجاح']);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'تعذر حذف الطالب: ' . $e->getMessage()], 500);
        }
    }

    public function show($id) {
        $studentId = $id instanceof Student ? $id->id : $id;
        $student = Student::with([
            'stage.subjects.teacher',
            'enrollments.subject.teacher',
            'enrolledSubjects.teacher'
        ])->findOrFail($studentId);

        $allStages = Stage::with(['subjects.teacher'])->get();

        return view('admin.students.show', compact('student', 'allStages'));
    }

    /**
     * مزامنة وتحديث المواد المشترك بها الطالب فورياً (من ملف الطالب الشخصي والمودال)
     */
    public function syncSubjects(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);
            $selectedSubjectIds = $request->input('subject_ids', []);
            if (!is_array($selectedSubjectIds)) {
                $selectedSubjectIds = [];
            }
            $selectedSubjectIds = array_values(array_filter(array_map('intval', $selectedSubjectIds)));

            $now = now();
            $syncData = [];
            foreach ($selectedSubjectIds as $subId) {
                $syncData[$subId] = [
                    'status'         => 'active',
                    'access_mode'    => 'all',
                    'payment_status' => 'admin_grant',
                    'activated_at'   => $now,
                ];
            }

            // مزامنة فورية فائقة السرعة بحد أدنى من الاستعلامات (Sync)
            $student->enrolledSubjects()->sync($syncData);

            $addedCount = count($selectedSubjectIds);

            if (!$request->ajax() && !$request->wantsJson()) {
                return redirect()->back()->with('success', "تم اعتماد {$addedCount} مادة دراسية للطالب ({$student->name_ar}).");
            }

            return response()->json([
                'success' => true,
                'icon'    => 'success',
                'title'   => 'تم حفظ وتحديث مواد الطالب بنجاح! 📚',
                'message' => "تم اعتماد {$addedCount} مادة دراسية للطالب ({$student->name_ar}).",
                'count'   => $addedCount
            ]);
        } catch (\Throwable $e) {
            \Log::error("syncSubjects error for student {$id}: " . $e->getMessage());

            if (!$request->ajax() && !$request->wantsJson()) {
                return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ المواد: ' . $e->getMessage());
            }

            return response()->json([
                'success' => false,
                'icon'    => 'error',
                'title'   => 'خطأ في الحفظ',
                'message' => 'حدث خطأ أثناء حفظ المواد: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تبديل أو إلغاء اشتراك مادة محددة للطالب بنقرة واحدة
     */
    public function toggleSubjectEnrollment(Request $request, $id, $subject_id)
    {
        $student = Student::findOrFail($id);
        $enrollment = \App\Models\Enrollment::where('student_id', $student->id)
            ->where('subject_id', $subject_id)
            ->first();

        if ($enrollment) {
            $enrollment->delete();
            return response()->json([
                'success' => true,
                'status'  => 'removed',
                'icon'    => 'info',
                'title'   => 'تم إلغاء الاشتراك في المادة ❌',
                'message' => 'تمت إزالة المادة من قائمة مواد الطالب بنجاح.'
            ]);
        } else {
            \App\Models\Enrollment::create([
                'student_id'     => $student->id,
                'subject_id'     => $subject_id,
                'status'         => 'active',
                'access_mode'    => 'all',
                'payment_status' => 'admin_grant',
                'activated_at'   => now(),
            ]);
            return response()->json([
                'success' => true,
                'status'  => 'added',
                'icon'    => 'success',
                'title'   => 'تم تفعيل المادة للطالب بنجاح! ✅',
                'message' => 'أصبحت المادة متاحة للطالب فوراً.'
            ]);
        }
    }

    /**
     * تفعيل اشتراك مادة بواسطة كود شحن أو بطاقة تفعيل
     */
    public function redeemCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|min:4',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $student = \App\Support\CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'يجب تسجيل الدخول كطالب أولاً.'], 401);
        }

        $codeStr = strtoupper(trim($request->code));
        $voucher = \App\Models\ActivationCode::where('code', $codeStr)->first();

        if (!$voucher) {
            return response()->json([
                'status' => 'error',
                'message' => 'كود التفعيل المدخل غير صحيح أو غير موجود.'
            ], 404);
        }

        if ($voucher->is_used) {
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، هذا الكود تم استخدامه مسبقاً.'
            ], 422);
        }

        // تفعيل الاشتراك في المادة
        $enrollment = \App\Models\Enrollment::updateOrCreate(
            [
                'student_id' => $student->id,
                'subject_id' => $voucher->subject_id,
            ],
            [
                'status' => 'active',
                'access_mode' => $voucher->access_mode ?? 'all',
                'payment_status' => 'voucher',
                'activated_at' => now(),
                'expires_at' => now()->addDays($voucher->duration_days ?? 365),
            ]
        );

        // وضع علامة مستخدم على الكود
        $voucher->update([
            'is_used' => true,
            'used_by_student_id' => $student->id,
            'used_at' => now(),
        ]);

        $subjectName = optional($voucher->subject)->name_ar ?? optional($voucher->subject)->name ?? 'المادة الدراسية';

        return response()->json([
            'status' => 'success',
            'message' => "تم تفعيل اشتراكك بنجاح في ({$subjectName}) مبارك! 🎉",
            'subject_id' => $voucher->subject_id
        ]);
    }

    /**
     * تحديث كلمة المرور الخاصة بالطالب
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6',
        ], [
            'old_password.required' => 'يرجى إدخال كلمة المرور القديمة.',
            'new_password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'new_password.min' => 'كلمة المرور الجديدة يجب أن لا تقل عن 6 خانات.',
        ]);

        $student = \App\Support\CurrentActor::student() ?? Auth::guard('student')->user();
        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح'], 401);
        }

        if (!Hash::check($request->old_password, $student->password)) {
            return response()->json(['status' => 'error', 'message' => 'كلمة المرور القديمة غير صحيحة.'], 422);
        }

        $updateData = [
            'password' => Hash::make($request->new_password)
        ];
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('students', 'plain_password')) {
                $updateData['plain_password'] = $request->new_password;
            }
        } catch (\Throwable $e) {}

        $student->update($updateData);

        return response()->json(['status' => 'success', 'message' => 'تم تحديث كلمة المرور بنجاح! 🔒']);
    }
}

