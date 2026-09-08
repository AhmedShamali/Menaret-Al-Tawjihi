<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'name_ar'  => 'required|string|max:255',
            'nid'      => 'required|digits:9|unique:students,nid',
            'email'    => 'required|email|unique:students,email',
            'password' => 'required|min:6',
            'stage_id' => 'required',
            'phone'    => 'nullable|string|max:20',
            'photo'    => 'nullable|image|max:3072',
            'id_photo' => 'nullable|image|max:3072',
        ], [
            'name_ar.required' => 'يرجى كتابة الاسم الرباعي كاملاً.',
            'nid.required'     => 'يرجى إدخال رقم الهوية الفلسطينية.',
            'nid.digits'       => 'رقم الهوية يجب أن يتكون من 9 أرقام.',
            'nid.unique'       => 'رقم الهوية هذا مسجل مسبقاً في المنصة.',
            'email.required'   => 'البريد الإلكتروني مطلوب.',
            'email.unique'     => 'البريد الإلكتروني مستخدم بالفعل، يرجى تسجيل الدخول أو استخدام بريد آخر.',
            'password.min'     => 'كلمة المرور يجب أن لا تقل عن 6 خانات.',
            'stage_id.required'=> 'يرجى اختيار الفرع أو المرحلة الدراسية.',
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

        // إنشاء حساب الطالب مع تفعيل فوري ونقاط ترحيبية
        $student = Student::create([
            'name_ar'            => $request->name_ar,
            'name_en'            => $request->name_en ?? $request->name_ar,
            'nid'                => $request->nid,
            'age'                => $request->age ?? 18,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'whatsapp'           => $request->whatsapp ?? $request->phone,
            'password'           => Hash::make($request->password),
            'stage_id'           => $stageId,
            'gender'             => $gender,
            'photo'              => $photoPath,
            'id_photo'           => $idPhotoPath,
            'status'             => 'active',
            'streak_count'       => 1,
            'total_points'       => 50,
            'last_activity_date' => now()->toDateString(),
        ]);

        // تفعيل اشتراك الطالب في المواد المختارة (أو جميع مواد فرع التوجيهي)
        if ($request->has('subject_ids') && is_array($request->subject_ids) && count($request->subject_ids) > 0) {
            foreach ($request->subject_ids as $subId) {
                \App\Models\Enrollment::firstOrCreate(
                    ['student_id' => $student->id, 'subject_id' => $subId],
                    ['status' => 'active', 'access_mode' => 'all', 'payment_status' => 'registered', 'activated_at' => now()]
                );
            }
        } else {
            // تسجيل الطالب تلقائياً في مواد مرحلته الدراسية لتمكينه من البدء الفوري
            $stageSubjects = \App\Models\Subject::where('stage_id', $stageId)->get();
            foreach ($stageSubjects as $sub) {
                \App\Models\Enrollment::firstOrCreate(
                    ['student_id' => $student->id, 'subject_id' => $sub->id],
                    ['status' => 'active', 'access_mode' => 'all', 'payment_status' => 'free', 'activated_at' => now()]
                );
            }
        }

        // إذا كان تسجيلاً ذاتياً (ليس مديراً مسجلاً يضيف طالباً)، يتم تسجيل دخول الطالب فوراً
        if (!Auth::guard('web')->check()) {
            Auth::guard('student')->login($student);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'icon'     => 'success',
                    'title'    => 'أهلاً بك يا بطل! تم إنشاء حسابك وتفعيل موادك بنجاح 🚀',
                    'redirect' => route('student.dashboard')
                ]);
            }

            return redirect()->route('student.dashboard')->with('success', 'أهلاً بك في منصة منارة التوجيهي! تم إنشاء حسابك بنجاح 🎉');
        }

        return response()->json(['icon' => 'success', 'title' => 'تم تسجيل الطالب بنجاح في النظام! 🎉']);
    }

    public function edit($id) {
        $student = Student::findOrFail($id);
        $stages = Stage::all();
        return view('students.edit', compact('student', 'stages'));
    }

    // 5. دالة التحديث (Update) - معدلة
    public function update(Request $request, $id) {
        $student = Student::findOrFail($id);

        // استبعاد الصور وكلمة المرور من التحديث التلقائي لمعالجتها يدوياً
        $data = $request->except(['password', 'photo', 'id_photo']);

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
        return response()->json(['icon' => 'success', 'title' => 'تم تحديث البيانات بنجاح 🚀']);
    }

    public function toggleStatus($id) {
        $student = Student::findOrFail($id);
        $student->status = ($student->status == 'active') ? 'pending' : 'active';
        $student->save();
        return response()->json(['icon' => 'success', 'title' => 'تم تغيير الحالة بنجاح']);
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
        $student = Student::with('stage')->findOrFail($id);
        return view('admin.students.show', compact('student'));
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

