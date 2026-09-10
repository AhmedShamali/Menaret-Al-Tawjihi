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
            'email.required'   => 'البريد الإلكتروني مطلوب.',
            'email.unique'     => 'البريد الإلكتروني مستخدم بالفعل، يرجى تسجيل الدخول أو استخدام بريد آخر.',
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
            'stage_id'           => $stageId,
            'gender'             => $gender,
            'photo'              => $photoPath,
            'id_photo'           => $idPhotoPath,
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
            unset($studentData['city'], $studentData['school_name'], $studentData['guardian_phone']);
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
        // تنبيه: لا يتم تسجيل الطالب تلقائياً في كامل مواد الفرع! يبقى الحساب والمواد بانتظار موافقة الإدارة والاشتراك

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

        // تسجيل دخول الطالب وتوجيهه لصفحة انتظار موافقة واعتماد المدير
        Auth::guard('student')->login($student);
        $request->session()->regenerate();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'icon'     => 'info',
                'title'    => 'تم استلام طلبك بنجاح! ⏳',
                'text'     => 'حسابك واشتراكك قيد مراجعة واعتماد المشرف العام.',
                'redirect' => route('student.pending-approval')
            ]);
        }

        return redirect()->route('student.pending-approval')->with('info', 'تم استلام طلبك بنجاح! حسابك قيد مراجعة الإدارة.');
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
     * صفحة انتظار موافقة واعتماد المدير على تسجيل الدخول والاشتراك للطالب
     */
    public function pendingApproval()
    {
        $student = Auth::guard('student')->user();
        if (!$student) {
            return redirect()->route('login');
        }

        // إذا وافق المدير وأصبح الحساب مفعلاً، يتم تحويله للوحة التحكم فوراً
        if ($student->status === 'active') {
            return redirect()->route('student.dashboard');
        }

        $pendingEnrollments = \App\Models\Enrollment::with('subject')
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->get();

        return view('student.pending_approval', compact('student', 'pendingEnrollments'));
    }

    /**
     * موافقة المدير على تسجيل دخول الطالب وتفعيل اشتراكه في المواد
     */
    public function approveStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->status = 'active';
        $student->save();

        // تفعيل جميع اشتراكات الطالب المعلقة
        \App\Models\Enrollment::where('student_id', $student->id)
            ->update([
                'status'         => 'active',
                'activated_at'   => now(),
            ]);

        // إرسال إشعار فوري للطالب
        try {
            \App\Services\NotificationService::notifyStudent(
                $student->id,
                'تمت موافقة الإدارة وتفعيل حسابك واشتراكك! 🎉',
                'مبارك يا بطل! وافق المشرف العام على تسجيل دخولك واشتراكك في المنصة. يمكنك الآن بدء دراستك وتصفح مساقاتك كاملة.',
                'approval',
                route('student.dashboard')
            );
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'icon'    => 'success',
            'title'   => 'تم اعتماد الطالب بنجاح! 🚀',
            'message' => "تمت الموافقة وتفعيل دخول واشتراك الطالب ({$student->name_ar}) بنجاح."
        ]);
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
                route('student.courses.catalog')
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

    public function toggleStatus($id) {
        $student = Student::findOrFail($id);
        $newStatus = ($student->status === 'active') ? 'pending' : 'active';
        $student->status = $newStatus;
        $student->save();

        if ($newStatus === 'active') {
            \App\Models\Enrollment::where('student_id', $student->id)
                ->where('status', 'pending')
                ->update([
                    'status'       => 'active',
                    'activated_at' => now()
                ]);
        }

        return response()->json([
            'icon'  => 'success',
            'title' => $newStatus === 'active' ? 'تمت الموافقة وتفعيل الحساب والاشتراك بنجاح! 🎉' : 'تم تحويل الحساب لقيد المراجعة ⏳'
        ]);
    }

    public function destroy($id) {
        $student = Student::findOrFail($id);
        // حذف الصور عند حذف الطالب
        if($student->photo) Storage::disk('public')->delete($student->photo);
        if($student->id_photo) Storage::disk('public')->delete($student->id_photo);

        $student->delete();
        return response()->json(['success' => true]);
    }

    public function show($id) {
        $student = Student::with([
            'stage.subjects.teacher',
            'enrollments.subject.teacher',
            'enrolledSubjects.teacher'
        ])->findOrFail($id);

        $allStages = Stage::with(['subjects.teacher'])->get();

        return view('admin.students.show', compact('student', 'allStages'));
    }

    /**
     * مزامنة وتحديث المواد المشترك بها الطالب فورياً (من ملف الطالب الشخصي والمودال)
     */
    public function syncSubjects(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $selectedSubjectIds = $request->input('subject_ids', []);
        if (!is_array($selectedSubjectIds)) {
            $selectedSubjectIds = [];
        }

        // حذف الاشتراكات للمواد التي تم إلغاء تحديدها
        \App\Models\Enrollment::where('student_id', $student->id)
            ->whereNotIn('subject_id', $selectedSubjectIds)
            ->delete();

        // تفعيل أو إضافة المواد المختارة
        $addedCount = 0;
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
            $addedCount++;
        }

        return response()->json([
            'success' => true,
            'icon'    => 'success',
            'title'   => 'تم حفظ وتحديث مواد الطالب بنجاح! 📚',
            'message' => "تم اعتماد {$addedCount} مادة دراسية للطالب ({$student->name_ar}).",
            'count'   => $addedCount
        ]);
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

