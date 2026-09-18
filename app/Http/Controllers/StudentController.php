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
                $studentData['plain_password'],
                $studentData['city'], $studentData['school_name'], $studentData['guardian_phone'],
                $studentData['google_id'], $studentData['provider'], $studentData['avatar_url']
            );
            $student = Student::create($studentData);
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

        if ($student->status === 'active') {
            return redirect()->route('student.dashboard');
        }

        $pendingEnrollments = \App\Models\Enrollment::with('subject')
            ->where('student_id', $student->id)
            ->get();

        // حساب إجمالي الرسوم الأكاديمية المطلوبة والخصم الممنوح
        $totalAmount = 0;
        foreach ($pendingEnrollments as $enr) {
            $totalAmount += (float)($enr->subject->price ?? 120);
        }
        if ($totalAmount === 0) {
            $totalAmount = 150; // باقة التوجيهي الأساسية الافتراضية
        }

        $discountAmount = $student->hasDiscount() ? $student->calculateDiscount($totalAmount) : 0;
        $finalAmount = max(0, $totalAmount - $discountAmount);

        $latestPayment = \App\Models\Payment::where('student_id', $student->id)->latest()->first();

        return view('student.pending_approval', compact('student', 'pendingEnrollments', 'latestPayment', 'totalAmount', 'discountAmount', 'finalAmount'));
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
        $amount = (float)($request->input('amount') ?: 150);

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

        $paymentDetails = json_encode([
            'payment_method_label' => $request->payment_method,
            'reference_no'         => $request->input('reference_no'),
            'notes'                => $request->input('notes', 'إشعار سداد اشتراك من منصة التوجيهي'),
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

        // إشعار إدارة المنصة فوراً لوصول إشعار سداد من الطالب
        try {
            $gwLabel = $payment->gateway_name_ar ?? $request->payment_method;
            \App\Services\NotificationService::notifyAdmin(
                'إشعار سداد رسوم جديد 💳',
                "قام الطالب ({$student->name_ar}) برفع إشعار دفع جديد عبر ({$gwLabel}) بمبلغ ({$payment->amount} ₪).",
                'payment',
                route('admin.payments.index'),
                'fa-receipt'
            );
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم استلام إشعار السداد والإيصال بنجاح! سيقوم المشرف العام بمطابقته واعتماد حسابك فورياً. 🎉'
            ]);
        }

        return back()->with('payment_success', 'تم استلام إشعار السداد والإيصال بنجاح! سيقوم المشرف العام بمراجعته واعتماد حسابك واشتراكك فورياً.');
    }

    /**
     * اعتماد وتفعيل حساب الطالب واشتراكه من قبل المدير
     */
    public function approveStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->status = 'active';
        $student->freeze_reason = null;
        $student->save();

        // تفعيل كافة المواد المقيد بها الطالب
        \App\Models\Enrollment::where('student_id', $student->id)->update([
            'status'         => 'active',
            'payment_status' => 'paid',
            'activated_at'   => now(),
        ]);

        // تحديث أي مدفوعات معلقة
        \App\Models\Payment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id() ?? 1,
                'reviewed_at' => now(),
            ]);

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

        return response()->json([
            'success' => true,
            'message' => 'تم اعتماد وتفعيل حساب الطالب واشتراكه بنجاح! 🎉'
        ]);
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
            $student->freeze_reason = $reason ?: 'تم تجميد الحساب من قبل الإدارة لمراجعة النشاط الدراسي والالتزام.';
        } else {
            $student->freeze_reason = null;
        }

        try {
            $student->save();
        } catch (\Throwable $e) {
            // استبعاد حقل freeze_reason إذا لم تكتمل الهجرة
            \DB::table('students')->where('id', $student->id)->update(['status' => $newStatus]);
        }

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
            $tables = [
                'submission_answers' => "submission_id IN (SELECT id FROM exam_submissions WHERE student_id = {$studentId})",
                'exam_submissions'   => "student_id = {$studentId}",
                'enrollments'        => "student_id = {$studentId}",
                'certificates'       => "student_id = {$studentId}",
                'recommendations'    => "student_id = {$studentId}",
                'activities'         => "student_id = {$studentId}",
                'payments'           => "student_id = {$studentId}",
                'channel_requests'   => "student_id = {$studentId}",
                'placement_results'  => "student_id = {$studentId}",
                'support_tickets'    => "student_id = {$studentId}",
                'messages'           => "student_id = {$studentId}",
                'flashcards'         => "student_id = {$studentId}",
                'video_notes'        => "student_id = {$studentId}",
                'student_progress'   => "student_id = {$studentId}",
                'exam_assignments'   => "student_id = {$studentId}",
            ];

            foreach ($tables as $tbl => $rawWhere) {
                if (\Illuminate\Support\Facades\Schema::hasTable($tbl)) {
                    try {
                        \DB::table($tbl)->whereRaw($rawWhere)->delete();
                    } catch (\Throwable $ex) {}
                }
            }

            $student->delete();
            \DB::commit();

            return response()->json(['success' => true, 'message' => 'تم حذف الطالب وكافة سجلاته بنجاح']);
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

            \DB::transaction(function () use ($student, $selectedSubjectIds) {
                // 1. حذف الاشتراكات للمواد التي تم إلغاء تحديدها
                \App\Models\Enrollment::where('student_id', $student->id)
                    ->whereNotIn('subject_id', $selectedSubjectIds)
                    ->delete();

                // 2. تحديث أو تفعيل المواد المحددة دفعة واحدة
                foreach ($selectedSubjectIds as $subId) {
                    \App\Models\Enrollment::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subId,
                        ],
                        [
                            'status'         => 'active',
                            'access_mode'    => 'all',
                            'payment_status' => 'admin_grant',
                            'activated_at'   => now(),
                        ]
                    );
                }
            });

            $addedCount = count($selectedSubjectIds);

            return response()->json([
                'success' => true,
                'icon'    => 'success',
                'title'   => 'تم حفظ وتحديث مواد الطالب بنجاح! 📚',
                'message' => "تم اعتماد {$addedCount} مادة دراسية للطالب ({$student->name_ar}).",
                'count'   => $addedCount
            ]);
        } catch (\Throwable $e) {
            \Log::error("syncSubjects error for student {$id}: " . $e->getMessage());
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

        $student->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['status' => 'success', 'message' => 'تم تحديث كلمة المرور بنجاح! 🔒']);
    }
}

