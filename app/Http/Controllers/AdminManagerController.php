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
            'site_name'         => Setting::get('site_name', 'منارة التوجيهي'),
            'contact_email'       => Setting::get('contact_email', 'info@jesr.ps'),
            'contact_whatsapp'    => Setting::get('contact_whatsapp', '00970597694385'),
            'registration_status' => Setting::get('registration_status', 'open'),
        ];
        return view('admin.management.settings', compact('settings'));
    }

    public function inbox() {
        $chats = Student::withCount(['messages' => function($q) {
            $q->where(function($query) {
                $query->where('is_read', false)->orWhereNull('is_read');
            })->where('sender_type', 'student');
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

        $oldSubjectId = $teacher->subject_id;

        $updateData = [
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'major'      => $request->major,
            'bio'        => $request->bio,
            'subject_id' => $request->subject_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            try {
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'plain_password')) {
                    $updateData['plain_password'] = $request->password;
                }
            } catch (\Throwable $e) {}
        }

        $teacher->update($updateData);

        // إلغاء إسناد المادة القديمة إذا تغيرت
        if ($oldSubjectId && $oldSubjectId != $request->subject_id) {
            \App\Models\Subject::where('id', $oldSubjectId)->update([
                'user_id' => null,
                'teacher_id' => null,
                'teacher_name' => null,
            ]);
        }

        // إسناد المادة الجديدة للمعلم المعتمد
        if ($request->filled('subject_id')) {
            \App\Models\Subject::where('id', $request->subject_id)->update([
                'user_id' => $teacher->id,
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->name,
            ]);
        }

        return redirect()->route('admin.teachers.info')->with('success', 'تم تحديث بيانات المعلم بنجاح');
    }

    public function teacherDestroy($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        // إخلاء أي مواد مسندة لهذا المعلم
        \App\Models\Subject::where('user_id', $teacher->id)
            ->orWhere('teacher_id', $teacher->id)
            ->orWhere('id', $teacher->subject_id)
            ->update([
                'user_id' => null,
                'teacher_id' => null,
                'teacher_name' => null,
            ]);

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

        $teacherData = [
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'plain_password' => $request->password,
            'phone'          => $request->phone,
            'major'          => $request->major,
            'bio'            => $request->bio,
            'subject_id'     => $request->subject_id,
            'photo'          => $photoPath,
            'role'           => 'teacher',
        ];

        try {
            $teacher = User::create($teacherData);
        } catch (\Illuminate\Database\QueryException $e) {
            unset($teacherData['plain_password']);
            $teacher = User::create($teacherData);
        }

        // إسناد المادة للمدرس إذا تم تحديدها
        if ($request->filled('subject_id')) {
            \App\Models\Subject::where('id', $request->subject_id)->update([
                'user_id' => $teacher->id,
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->name,
            ]);
        }

        return response()->json(['success' => true, 'title' => 'تم إنشاء ملف المدرس بنجاح ✅']);
    }

    /**
     * تصدير كامل جدول المعلمين إلى ملف CSV / Excel
     */
    public function exportTeachers()
    {
        $teachers = User::where('role', 'teacher')->latest()->get();

        $filename = 'teachers_export_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($teachers) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Arabic Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'المعرف',
                'اسم المعلم',
                'البريد الإلكتروني',
                'كلمة المرور المسجلة',
                'رقم الجوال',
                'التخصص الأكاديمي',
                'المادة المسندة',
                'تاريخ التسجيل'
            ]);

            foreach ($teachers as $t) {
                $subName = optional(\App\Models\Subject::find($t->subject_id))->name_ar ?? 'غير محدد';
                fputcsv($file, [
                    $t->id,
                    $t->name,
                    $t->email,
                    $t->plain_password ?? 'مشفرة',
                    $t->phone ?? '',
                    $t->major ?? '',
                    $subName,
                    $t->created_at ? $t->created_at->format('Y-m-d H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * تصدير بيانات الطلاب كملف CSV / Excel
     */
    public function exportStudents()
    {
        $students = Student::with('stage')->latest()->get();

        $filename = 'students_export_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel Arabic compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'المعرف',
                'اسم الطالب (عربي)',
                'الاسم (إنجليزي)',
                'البريد الإلكتروني',
                'كلمة المرور',
                'رقم الهوية',
                'الفرع / المرحلة',
                'رقم الهاتف',
                'رقم الواتساب',
                'المدينة',
                'المدرسة',
                'الحالة',
                'تاريخ التسجيل'
            ]);

            $statusMap = [
                'active'    => 'مفعّل ومعتمد',
                'pending'   => 'بانتظار الموافقة',
                'suspended' => 'مجمد',
                'frozen'    => 'مجمد',
                'inactive'  => 'معطل',
            ];

            foreach ($students as $s) {
                $stageName = $s->stage ? ($s->stage->label_ar ?? $s->stage->name_ar) : 'توجيهي';
                $statusText = $statusMap[$s->status] ?? $s->status;

                fputcsv($file, [
                    $s->id,
                    $s->name_ar,
                    $s->name_en ?? '',
                    $s->email,
                    $s->plain_password ?? 'مشفرة',
                    $s->nid ?? '',
                    $stageName,
                    $s->phone ?? '',
                    $s->whatsapp ?? '',
                    $s->city ?? '',
                    $s->school_name ?? '',
                    $statusText,
                    $s->created_at ? $s->created_at->format('Y-m-d H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    /**
     * استيراد المعلمين من ملف CSV
     */
    public function importTeachers(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'تعذر فتح ملف البيانات المرفق.');
        }

        // skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Header row
        fgetcsv($handle);

        $imported = 0;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 3) continue;
            $name = trim($row[1] ?? $row[0]);
            $email = trim($row[2] ?? $row[1]);
            $pwd = !empty($row[3]) ? trim($row[3]) : 'tawjihi2026';

            if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if (User::where('email', $email)->exists()) {
                continue;
            }

            $tData = [
                'name'           => $name,
                'email'          => $email,
                'password'       => Hash::make($pwd),
                'plain_password' => $pwd,
                'phone'          => $row[4] ?? null,
                'major'          => $row[5] ?? null,
                'role'           => 'teacher',
            ];

            try {
                User::create($tData);
                $imported++;
            } catch (\Illuminate\Database\QueryException $e) {
                unset($tData['plain_password']);
                User::create($tData);
                $imported++;
            }
        }

        fclose($handle);

        return back()->with('success', "تم استيراد ({$imported}) معلماً بنجاح إلى النظام ✅");
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

            // معالجة رفع ختم المنصة الرسمي
            if ($request->hasFile('official_stamp')) {
                $stamp = $request->file('official_stamp');
                $stampName = 'stamp_' . time() . '.' . $stamp->getClientOriginalExtension();
                $dest = public_path('uploads/branding');
                if (!file_exists($dest)) mkdir($dest, 0777, true);
                $stamp->move($dest, $stampName);
                Setting::updateOrCreate(['key' => 'official_stamp'], ['value' => 'uploads/branding/' . $stampName]);
            }

            // معالجة رفع شعار المدير العام
            if ($request->hasFile('director_logo')) {
                $dLogo = $request->file('director_logo');
                $dLogoName = 'director_logo_' . time() . '.' . $dLogo->getClientOriginalExtension();
                $dest = public_path('uploads/branding');
                if (!file_exists($dest)) mkdir($dest, 0777, true);
                $dLogo->move($dest, $dLogoName);
                Setting::updateOrCreate(['key' => 'director_logo'], ['value' => 'uploads/branding/' . $dLogoName]);
            }

            // معالجة رفع التوقيع الرقمي للمدير
            if ($request->hasFile('admin_signature')) {
                $sig = $request->file('admin_signature');
                $sigName = 'admin_sig_' . time() . '.' . $sig->getClientOriginalExtension();
                $dest = public_path('uploads/branding');
                if (!file_exists($dest)) mkdir($dest, 0777, true);
                $sig->move($dest, $sigName);
                Setting::updateOrCreate(['key' => 'admin_signature'], ['value' => 'uploads/branding/' . $sigName]);
            }

            // معالجة رفع التوقيع الرقمي للمعلم
            if ($request->hasFile('teacher_signature')) {
                $tSig = $request->file('teacher_signature');
                $tSigName = 'teacher_sig_' . time() . '.' . $tSig->getClientOriginalExtension();
                $dest = public_path('uploads/branding');
                if (!file_exists($dest)) mkdir($dest, 0777, true);
                $tSig->move($dest, $tSigName);
                Setting::updateOrCreate(['key' => 'teacher_signature'], ['value' => 'uploads/branding/' . $tSigName]);
            }

            // خيار إزالة الشعار واستعادة الشعار الافتراضي
            if ($request->input('remove_logo') === '1') {
                Setting::updateOrCreate(['key' => 'site_logo'], ['value' => '']);
            }

            // حفظ باقي إعدادات النصوص والأرقام
            foreach ($request->except(['_token', 'site_logo', 'site_favicon', 'official_stamp', 'director_logo', 'admin_signature', 'teacher_signature', 'remove_logo']) as $key => $value) {
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
                'title' => 'تم حفظ الشعار وهوية المنصة والأختام بنجاح ✅',
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
            $driver = DB::getDriverName();
            $hourExpr = match($driver) {
                'pgsql'  => "date_part('hour', created_at)::int as hour",
                'sqlite' => "cast(strftime('%H', created_at) as integer) as hour",
                default  => "HOUR(created_at) as hour",
            };

            $chartData = Activity::select(
                DB::raw($hourExpr),
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

    /**
     * واجهة الاستفسار الأكاديمي وتذاكر الشكاوى للإدارة
     */
    public function academicInquiries(Request $request)
    {
        $query = \App\Models\Complaint::query();

        if ($request->filled('open_id')) {
            $openId = (int) $request->open_id;
            $query->orderByRaw("CASE WHEN id = {$openId} THEN 0 ELSE 1 END");
        }

        $query->latest();

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%")
                  ->orWhere('message', 'like', "%{$s}%");
            });
        }

        $inquiries = $query->paginate(15)->withQueryString();

        $stats = [
            'total'     => \App\Models\Complaint::count(),
            'pending'   => \App\Models\Complaint::whereIn('status', ['new', 'pending'])->count(),
            'replied'   => \App\Models\Complaint::where('status', 'replied')->count(),
            'academics' => \App\Models\Complaint::where('category', 'like', '%أكاديمي%')->count(),
            'financial' => \App\Models\Complaint::where('category', 'like', '%مالي%')->count(),
        ];

        return view('admin.inquiries.index', compact('inquiries', 'stats'));
    }

    public function academicInquiryReply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|min:2',
        ]);

        $inquiry = \App\Models\Complaint::findOrFail($id);
        $inquiry->reply = trim($request->reply);
        $inquiry->status = 'replied';
        $inquiry->replied_at = now();
        if ($request->filled('admin_notes')) {
            $inquiry->admin_notes = $request->admin_notes;
        }
        $inquiry->save();

        // مزامنة الرد مع جدول استفسارات رواتب المعلمين وإرسال إشعار فوري للمعلم
        try {
            $teacherUser = null;
            if ($inquiry->email) {
                $teacherUser = \App\Models\User::where('email', $inquiry->email)->first();
            }
            if (!$teacherUser && $inquiry->name) {
                $teacherUser = \App\Models\User::where('role', 'teacher')->where('name', $inquiry->name)->first();
            }

            if ($teacherUser) {
                if (\Illuminate\Support\Facades\Schema::hasTable('teacher_salary_claims')) {
                    $claim = \App\Models\TeacherSalaryClaim::where('teacher_id', $teacherUser->id)
                        ->where(function($q) {
                            $q->where('status', 'pending')->orWhereNull('admin_reply');
                        })
                        ->latest()
                        ->first();

                    if ($claim) {
                        $claim->admin_reply = trim($request->reply);
                        $claim->replied_by = \Illuminate\Support\Facades\Auth::id();
                        $claim->replied_at = now();
                        $claim->status = 'replied';
                        $claim->save();
                    }
                }

                // إرسال إشعار مباشر في لوحة تحكم المعلم
                \App\Services\NotificationService::notifyUser(
                    $teacherUser->id,
                    "رد إداري رسمي على استفسارك المالي 📬",
                    "وردك رد من إدارة المنصة: " . \Illuminate\Support\Str::limit($request->reply, 80),
                    'support',
                    route('teacher.salaries.index'),
                    'fa-reply'
                );
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'title'   => 'تم حفظ الرد على الاستفسار وإشعار صاحب التذكرة بنجاح ✅',
            'status'  => 'replied'
        ]);
    }

    public function academicInquiryDestroy($id)
    {
        $inquiry = \App\Models\Complaint::findOrFail($id);
        $inquiry->delete();

        return back()->with('success', 'تم حذف تذكرة الاستفسار بنجاح.');
    }

    /**
     * حذف الطلاب المحددين (Bulk Delete Students)
     */
    public function bulkDeleteStudents(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'لم يتم تحديد أي طالب للحذف.'], 422);
        }

        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'معرفات الطلاب غير صالحة.'], 422);
        }

        \DB::beginTransaction();
        try {
            $students = Student::whereIn('id', $ids)->get();
            foreach ($students as $s) {
                if ($s->photo) Storage::disk('public')->delete($s->photo);
                if ($s->id_photo) Storage::disk('public')->delete($s->id_photo);
            }

            // 1. حذف التكليفات والامتحانات المرتبطة بالتسجيلات
            if (\Illuminate\Support\Facades\Schema::hasTable('enrollments') && \Illuminate\Support\Facades\Schema::hasTable('exam_assignments')) {
                $enrIds = \DB::table('enrollments')->whereIn('student_id', $ids)->pluck('id')->toArray();
                if (!empty($enrIds)) {
                    \DB::table('exam_assignments')->whereIn('enrollment_id', $enrIds)->delete();
                }
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('exam_assignments')) {
                \DB::table('exam_assignments')->whereIn('student_id', $ids)->delete();
            }

            // 2. حذف إجابات الامتحانات عبر معرفات التقديم الصحيحة (exam_submission_id)
            if (\Illuminate\Support\Facades\Schema::hasTable('exam_submissions') && \Illuminate\Support\Facades\Schema::hasTable('submission_answers')) {
                $subIds = \DB::table('exam_submissions')->whereIn('student_id', $ids)->pluck('id')->toArray();
                if (!empty($subIds)) {
                    \DB::table('submission_answers')->whereIn('exam_submission_id', $subIds)->delete();
                }
            }

            // 3. حذف تسليمات الامتحانات
            if (\Illuminate\Support\Facades\Schema::hasTable('exam_submissions')) {
                \DB::table('exam_submissions')->whereIn('student_id', $ids)->delete();
            }

            // 4. حذف الاشتراكات الشهرية أولاً قبل المدفوعات لفك القيد الخارجي
            if (\Illuminate\Support\Facades\Schema::hasTable('student_monthly_subscriptions')) {
                \DB::table('student_monthly_subscriptions')->whereIn('student_id', $ids)->delete();
            }

            // 5. حذف المدفوعات والإيصالات
            if (\Illuminate\Support\Facades\Schema::hasTable('payments')) {
                \DB::table('payments')->whereIn('student_id', $ids)->delete();
            }

            // 6. حذف التسجيلات بالمواد
            if (\Illuminate\Support\Facades\Schema::hasTable('enrollments')) {
                \DB::table('enrollments')->whereIn('student_id', $ids)->delete();
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
                    \DB::table($tbl)->whereIn('student_id', $ids)->delete();
                }
            }

            // 8. حذف الرسائل
            if (\Illuminate\Support\Facades\Schema::hasTable('messages')) {
                \DB::table('messages')
                    ->whereIn('student_id', $ids)
                    ->orWhere(function($q) use ($ids) {
                        $q->where('sender_type', 'student')->whereIn('sender_id', $ids);
                    })
                    ->delete();
            }

            // 9. تفريغ كوبونات الدخول إن استخدمت
            if (\Illuminate\Support\Facades\Schema::hasTable('access_vouchers')) {
                \DB::table('access_vouchers')->whereIn('used_by_student_id', $ids)->update([
                    'used_by_student_id' => null,
                    'is_used'            => false,
                    'used_at'            => null,
                ]);
            }

            // 10. حذف الإشعارات التابعة للطلاب
            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \DB::table('notifications')
                    ->where('notifiable_type', 'like', '%Student%')
                    ->whereIn('notifiable_id', $ids)
                    ->delete();
            }

            // 11. حذف الطلاب
            Student::whereIn('id', $ids)->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'count'   => count($ids),
                'message' => 'تم حذف (' . count($ids) . ') طالب بنجاح مع كافة سجلاتهم واشتراكاتهم 🗑️'
            ]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء حذف الطلاب: ' . $e->getMessage()], 500);
        }
    }

    /**
     * حذف جميع الطلاب دفعة واحدة (Purge All Students)
     */
    public function purgeAllStudents(Request $request)
    {
        $confirm = trim($request->input('confirm_text', ''));
        if ($confirm !== 'تأكيد الحذف' && $confirm !== 'DELETE' && $confirm !== 'CONFIRM') {
            return response()->json([
                'success' => false,
                'message' => 'يرجى كتابة عبارة التأكيد بشكل صحيح (تأكيد الحذف) لإتمام العملية.'
            ], 422);
        }

        \DB::beginTransaction();
        try {
            $students = Student::all();
            $count = $students->count();
            foreach ($students as $s) {
                if ($s->photo) Storage::disk('public')->delete($s->photo);
                if ($s->id_photo) Storage::disk('public')->delete($s->id_photo);
            }

            // الترتيب الصحيح للأسبقية لفك القيود الخارجية
            $tablesToClean = [
                'exam_assignments',
                'submission_answers',
                'exam_submissions',
                'student_monthly_subscriptions',
                'payments',
                'enrollments',
                'certificates',
                'recommendations',
                'activities',
                'channel_requests',
                'placement_results',
                'support_tickets',
                'video_notes',
                'student_progress',
            ];

            foreach ($tablesToClean as $tbl) {
                if (\Illuminate\Support\Facades\Schema::hasTable($tbl)) {
                    \DB::table($tbl)->delete();
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('messages')) {
                \DB::table('messages')->whereNotNull('student_id')->orWhere('sender_type', 'student')->delete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('flashcards')) {
                \DB::table('flashcards')->whereNotNull('student_id')->delete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \DB::table('notifications')->where('notifiable_type', 'like', '%Student%')->delete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('access_vouchers')) {
                \DB::table('access_vouchers')->update([
                    'used_by_student_id' => null,
                    'is_used'            => false,
                    'used_at'            => null,
                ]);
            }

            \DB::table('students')->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'count'   => $count,
                'message' => 'تم حذف جميع الطلاب بنجاح (' . $count . ' طالب) وتصفير كافة سجلاتهم واشتراكاتهم! 🗑️'
            ]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء حذف الطلاب: ' . $e->getMessage()], 500);
        }
    }

    /**
     * حذف المعلمين المحددين (Bulk Delete Teachers)
     */
    public function bulkDeleteTeachers(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'لم يتم تحديد أي معلم للحذف.'], 422);
        }

        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'معرفات المعلمين غير صالحة.'], 422);
        }

        \DB::beginTransaction();
        try {
            if (\DB::connection()->getDriverName() === 'mysql') {
                try { \DB::statement('SET FOREIGN_KEY_CHECKS=0;'); } catch (\Throwable $e) {}
            }

            // فلترة فقط المعلمين لمنع حذف أي حساب إداري نهائياً
            $teachers = User::where('role', 'teacher')->whereIn('id', $ids)->get();
            $realIds = $teachers->pluck('id')->toArray();

            if (empty($realIds)) {
                \DB::rollBack();
                return response()->json(['success' => false, 'message' => 'لم يتم العثور على معلمين متطابقين مع التحديد.'], 404);
            }

            foreach ($teachers as $t) {
                if ($t->photo) Storage::disk('public')->delete($t->photo);
            }

            // إخلاء إسناد المواد
            if (\Illuminate\Support\Facades\Schema::hasTable('subjects')) {
                \DB::table('subjects')->whereIn('user_id', $realIds)
                    ->orWhereIn('teacher_id', $realIds)
                    ->update([
                        'user_id'      => null,
                        'teacher_id'   => null,
                        'teacher_name' => null,
                    ]);
            }

            // حذف رسائل وتذاكر المعلم
            if (\Illuminate\Support\Facades\Schema::hasTable('messages')) {
                \DB::table('messages')->whereIn('teacher_id', $realIds)
                    ->orWhere(function($q) use ($realIds) {
                        $q->where('sender_type', 'teacher')->whereIn('sender_id', $realIds);
                    })->delete();
            }

            User::where('role', 'teacher')->whereIn('id', $realIds)->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'count'   => count($realIds),
                'message' => 'تم حذف (' . count($realIds) . ') معلم بنجاح وإخلاء إسناد المواد التابعة لهم 🗑️'
            ]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        } finally {
            if (\DB::connection()->getDriverName() === 'mysql') {
                try { \DB::statement('SET FOREIGN_KEY_CHECKS=1;'); } catch (\Throwable $e) {}
            }
        }
    }

    /**
     * حذف جميع المعلمين دفعة واحدة (Purge All Teachers)
     */
    public function purgeAllTeachers(Request $request)
    {
        $confirm = trim($request->input('confirm_text', ''));
        if ($confirm !== 'تأكيد الحذف' && $confirm !== 'DELETE' && $confirm !== 'CONFIRM') {
            return response()->json([
                'success' => false,
                'message' => 'يرجى كتابة عبارة التأكيد بشكل صحيح (تأكيد الحذف) لإتمام العملية.'
            ], 422);
        }

        \DB::beginTransaction();
        try {
            if (\DB::connection()->getDriverName() === 'mysql') {
                try { \DB::statement('SET FOREIGN_KEY_CHECKS=0;'); } catch (\Throwable $e) {}
            }

            $teachers = User::where('role', 'teacher')->get();
            $count = $teachers->count();
            foreach ($teachers as $t) {
                if ($t->photo) Storage::disk('public')->delete($t->photo);
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('subjects')) {
                \DB::table('subjects')->update([
                    'user_id'      => null,
                    'teacher_id'   => null,
                    'teacher_name' => null,
                ]);
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('messages')) {
                \DB::table('messages')->where('sender_type', 'teacher')->orWhereNotNull('teacher_id')->delete();
            }

            User::where('role', 'teacher')->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'count'   => $count,
                'message' => 'تم حذف جميع المعلمين بنجاح (' . $count . ' معلم) وإخلاء إسناد المواد دون المساس بحسابات الإدارة! 🗑️'
            ]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء حذف المعلمين: ' . $e->getMessage()], 500);
        } finally {
            if (\DB::connection()->getDriverName() === 'mysql') {
                try { \DB::statement('SET FOREIGN_KEY_CHECKS=1;'); } catch (\Throwable $e) {}
            }
        }
    }

    /**
     * الحذف الشامل لجميع الطلاب والمعلمين معاً دفعة واحدة (Purge All Students & Teachers)
     */
    public function purgeAllStudentsAndTeachers(Request $request)
    {
        $confirm = trim($request->input('confirm_text', ''));
        if ($confirm !== 'تأكيد الحذف الشامل' && $confirm !== 'DELETE ALL' && $confirm !== 'تأكيد الحذف') {
            return response()->json([
                'success' => false,
                'message' => 'يرجى كتابة عبارة التأكيد (تأكيد الحذف الشامل) بدقة لتأكيد العملية الكبرى.'
            ], 422);
        }

        \DB::beginTransaction();
        try {
            if (\DB::connection()->getDriverName() === 'mysql') {
                try { \DB::statement('SET FOREIGN_KEY_CHECKS=0;'); } catch (\Throwable $e) {}
            }

            // 1. حذف صور وسجلات الطلاب
            $students = Student::all();
            $studentCount = $students->count();
            foreach ($students as $s) {
                if ($s->photo) Storage::disk('public')->delete($s->photo);
                if ($s->id_photo) Storage::disk('public')->delete($s->id_photo);
            }

            // 2. حذف صور المعلمين
            $teachers = User::where('role', 'teacher')->get();
            $teacherCount = $teachers->count();
            foreach ($teachers as $t) {
                if ($t->photo) Storage::disk('public')->delete($t->photo);
            }

            // 3. تنظيف الجداول التابعة
            $tablesToClean = [
                'submission_answers',
                'exam_submissions',
                'enrollments',
                'certificates',
                'recommendations',
                'activities',
                'payments',
                'channel_requests',
                'placement_results',
                'support_tickets',
                'messages',
                'video_notes',
                'student_progress',
                'exam_assignments',
            ];

            foreach ($tablesToClean as $tbl) {
                if (\Illuminate\Support\Facades\Schema::hasTable($tbl)) {
                    \DB::table($tbl)->delete();
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('flashcards')) {
                \DB::table('flashcards')->whereNotNull('student_id')->delete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \DB::table('notifications')->where('notifiable_type', 'like', '%Student%')->delete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('subjects')) {
                \DB::table('subjects')->update([
                    'user_id'      => null,
                    'teacher_id'   => null,
                    'teacher_name' => null,
                ]);
            }

            // حذف جميع سجلات الطلاب
            \DB::table('students')->delete();

            // حذف المعلمين فقط والحفاظ على حسابات المدراء
            User::where('role', 'teacher')->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "تم الحذف الشامل بنجاح! تم حذف ({$studentCount}) طالباً و({$teacherCount}) معلماً، وتصفير المنصة بالكامل للعام الدراسي الجديد 🚀"
            ]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف الشامل: ' . $e->getMessage()], 500);
        } finally {
            if (\DB::connection()->getDriverName() === 'mysql') {
                try { \DB::statement('SET FOREIGN_KEY_CHECKS=1;'); } catch (\Throwable $e) {}
            }
        }
    }
}
