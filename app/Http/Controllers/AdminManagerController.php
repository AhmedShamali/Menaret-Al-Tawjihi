<?php
namespace App\Http\Controllers;

use App\Models\{Setting, Activity, Message, Student, User, Stage};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminManagerController extends Controller {

    public function settings()
    {
        $settings = [
            'site_name'         => Setting::get('site_name', 'منصة جسر'),
            'contact_email'       => Setting::get('contact_email', 'info@jesr.ps'),
            'contact_whatsapp'    => Setting::get('contact_whatsapp', '0590000000'),
            'registration_status' => Setting::get('registration_status', 'open'),
        ];
        return view('admin.management.settings', compact('settings'));
    }

    public function inbox() {
        $chats = Student::withCount(['messages' => function($q) {
            $q->where('is_read', \Illuminate\Support\Facades\DB::raw('false'))->where('sender_type', 'student');
        }])->has('messages')->latest()->get();

        return view('admin.management.inbox', compact('chats'));
    }

    public function sendMessage(Request $request) {
        $msg = Message::create([
            'student_id' => $request->student_id,
            'sender_type' => 'admin',
            'message' => $request->message
        ]);
        return response()->json(['success' => true, 'message' => $msg->message, 'time' => $msg->created_at->format('H:i')]);
    }

    public function teacherCreate() {
        $stages = Stage::with('subjects')->get();
        return view('admin.management.teachers_create', compact('stages'));
    }

    public function teacherEdit($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $stages = Stage::with('subjects')->get();
        return view('admin.management.teachers_edit', compact('teacher', 'stages'));
    }

    public function teacherUpdate(Request $request, $id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'name' => 'required',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $teacher->photo = $request->file('photo')->store('teachers/photos', 'public');
        }

        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'major' => $request->major,
            'bio' => $request->bio,
            'subject_id' => $request->subject_id,
            'password' => $request->password ? Hash::make($request->password) : $teacher->password,
        ]);

        return redirect()->route('admin.teachers.info')->with('success', 'تم تحديث بيانات المعلم بنجاح');
    }

    public function teacherDestroy($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        $teacher->delete();

        return redirect()->route('admin.teachers.info')->with('success', 'تم حذف المعلم بنجاح');
    }

    public function teachersInfo()
    {
        // جلب المعلمين فقط مع ترقيم الصفحات
        $teachers = User::where('role', 'teacher')->latest()->paginate(10);

        // إرجاع الواجهة مع تمرير البيانات
        return view('admin.teachers.information', compact('teachers'));
    }

    // دالة حفظ المدرس
    public function teacherStore(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'photo' => 'nullable|image|max:2048',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['icon' => 'error', 'title' => $validator->errors()->first()], 400);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('teachers/photos', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'major' => $request->major,
            'bio' => $request->bio,
            'subject_id' => $request->subject_id,
            'photo' => $photoPath,
            'role' => 'teacher',
        ]);

        return response()->json(['success' => true, 'title' => 'تم إنشاء ملف المدرس بنجاح ✅']);
    }

    public function studentStore(Request $request) {
        $validator = Validator::make($request->all(), [
            'name_ar'  => 'required|string|max:255',
            'email'    => 'required|email|unique:students,email',
            'password' => 'required|min:6',
            'nid'      => 'required|digits:9|unique:students,nid',
            'stage_id' => 'required',
            'phone'    => 'nullable|string|max:20',
            'photo'    => 'nullable|image|max:3072',
            'id_photo' => 'nullable|image|max:3072'
        ], [
            'name_ar.required'  => 'يرجى إدخال الاسم الرباعي للطالب.',
            'email.required'    => 'يرجى إدخال البريد الإلكتروني.',
            'email.unique'      => 'البريد الإلكتروني مستخدم بالفعل لطالب آخر.',
            'password.required' => 'كلمة المرور مطلوبة ولا تقل عن 6 خانات.',
            'password.min'      => 'كلمة المرور يجب أن تتكون من 6 خانات على الأقل.',
            'nid.required'      => 'رقم الهوية مطلوب.',
            'nid.digits'        => 'رقم الهوية يجب أن يتكون من 9 أرقام بدقة.',
            'nid.unique'        => 'رقم الهوية مسجل مسبقاً في النظام.',
            'stage_id.required' => 'يرجى اختيار المرحلة أو الفرع الدراسي.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'icon'    => 'error',
                'title'   => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        $photoPath = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        $idPhotoPath = null;
        if ($request->hasFile('id_photo') && $request->file('id_photo')->isValid()) {
            $idPhotoPath = $request->file('id_photo')->store('students/ids', 'public');
        }

        $rawGender = $request->input('gender', 'ذكر');
        $gender = ($rawGender === 'أنثى' || $rawGender === 'female') ? 'أنثى' : 'ذكر';

        $stage = Stage::where('id', $request->stage_id)
            ->orWhere('grade_level', $request->stage_id)
            ->first();
        $stageId = $stage ? $stage->id : (Stage::where('grade_level', 122)->value('id') ?? Stage::first()?->id ?? 1);

        try {
            DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_status_check');
            DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_gender_check');
        } catch (\Throwable $e) {}

        $phone = $request->phone ?: '0590000000';
        $age = $request->age ? (int)$request->age : 18;
        $nameEn = $request->name_en ?: $request->name_ar;
        $city = $request->input('city', 'رام الله والبيرة');
        $schoolName = $request->input('school_name');
        $guardianPhone = $request->input('guardian_phone', $request->input('whatsapp'));

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
            'status'             => 'active',
            'streak_count'       => 1,
            'total_points'       => 50,
            'last_activity_date' => now()->toDateString(),
        ];

        try {
            $student = Student::create($studentData);
        } catch (\Illuminate\Database\QueryException $e) {
            unset($studentData['city'], $studentData['school_name'], $studentData['guardian_phone']);
            $student = Student::create($studentData);
        }

        // تسجيل الطالب في المواد التي حددها المدير حصراً دون إجباره على كل المواد تلقائياً
        $enrolledCount = 0;
        if ($request->has('subject_ids') && is_array($request->subject_ids) && count($request->subject_ids) > 0) {
            foreach ($request->subject_ids as $subId) {
                try {
                    \App\Models\Enrollment::firstOrCreate(
                        ['student_id' => $student->id, 'subject_id' => $subId],
                        [
                            'status'         => 'active',
                            'access_mode'    => 'all',
                            'payment_status' => 'admin_grant',
                            'activated_at'   => now(),
                        ]
                    );
                    $enrolledCount++;
                } catch (\Throwable $e) {}
            }
        }

        try {
            \App\Services\NotificationService::notifyStudent(
                $student->id,
                'أهلاً بك في منصة منارة التوجيهي! 🎓',
                "تم إنشاء وتفعيل حسابك الأكاديمي رسمياً من قِبل إدارة المنصة. نتمنى لك رحلة تعليمية موفقة ومتميزة!",
                'system',
                route('student.dashboard'),
                'fa-sparkles'
            );
        } catch (\Throwable $e) {}

        $titleMsg = $enrolledCount > 0 
            ? "تم إنشاء حساب الطالب وتفعيل ({$enrolledCount}) مادة مختارة بنجاح ✅" 
            : 'تم إنشاء حساب الطالب بنجاح (يمكنك تخصيص مواده لاحقاً) ✅';

        return response()->json([
            'success'  => true,
            'icon'     => 'success',
            'title'    => $titleMsg,
            'redirect' => route('admin.students.index')
        ]);
    }

    public function studentCreate() {
        if (\App\Models\Stage::where('grade_level', '>=', 120)->count() < 3 || \App\Models\Subject::count() === 0) {
            (new \Database\Seeders\StageSeeder())->run();
            (new \Database\Seeders\SubjectSeeder())->run();
        }

        $stages = Stage::with(['subjects.teacher'])
            ->where('grade_level', '>=', 120)
            ->orderBy('grade_level', 'desc')
            ->get();

        if ($stages->isEmpty()) {
            $stages = Stage::with(['subjects.teacher'])->get();
        }

        return view('admin.management.students_create', compact('stages'));
    }

    public function settingsUpdate(Request $request)
    {
        try {
            // معالجة رفع شعار المنصة الرسمي
            if ($request->hasFile('site_logo')) {
                $request->validate([
                    'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                ]);
                $file = $request->file('site_logo');
                $filename = 'site_logo_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/logos');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                Setting::updateOrCreate(['key' => 'site_logo'], ['value' => 'uploads/logos/' . $filename]);
            }

            // معالجة رفع أيقونة المتصفح Favicon
            if ($request->hasFile('site_favicon')) {
                $fav = $request->file('site_favicon');
                $favName = 'site_favicon_' . time() . '.' . $fav->getClientOriginalExtension();
                $destinationPath = public_path('uploads/logos');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $fav->move($destinationPath, $favName);
                Setting::updateOrCreate(['key' => 'site_favicon'], ['value' => 'uploads/logos/' . $favName]);
            }

            // خيار إزالة الشعار واستعادة الشعار الافتراضي
            if ($request->input('remove_logo') === '1') {
                Setting::updateOrCreate(['key' => 'site_logo'], ['value' => '']);
            }

            // حفظ باقي إعدادات النصوص والأرقام
            foreach ($request->except(['_token', 'site_logo', 'site_favicon', 'remove_logo']) as $key => $value) {
                if ($value !== null) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                }
            }

            $currentLogo = Setting::get('site_logo') ? asset(Setting::get('site_logo')) : null;

            return response()->json([
                'success' => true,
                'title' => 'تم حفظ الشعار وهوية المنصة بنجاح ✅',
                'logo_url' => $currentLogo
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()], 500);
        }
    }

    public function pulse()
    {
        $activities = Activity::with('student')->latest()->take(50)->get();
        $chartData = collect();
        
        try {
            $chartData = Activity::select(
                DB::raw("date_part('hour', created_at) as hour"),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>', now()->subDay())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        } catch (\Exception $e) {
            // fallback if hour function or date_part differs
            $chartData = collect();
        }

        if (view()->exists('admin.activities.pulse')) {
            return view('admin.activities.pulse', compact('activities', 'chartData'));
        }

        return view('admin.activities.index', compact('activities'));
    }

    public function studentProfile()
    {
        $student = Student::with('stage')->first();
        if (!$student) {
            return "تنبيه: لا يوجد طلاب في القاعدة، يرجى تسجيل طالب أولاً.";
        }
        $activities = Activity::where('student_id', $student->id)->latest()->take(5)->get();
        return view('student.profile', compact('student', 'activities'));
    }
}
