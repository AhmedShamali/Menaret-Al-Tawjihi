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
            $q->where('is_read', false)->where('sender_type', 'student');
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
            'name_ar' => 'required',
            'email' => 'required|email|unique:students,email',
            'password' => 'required|min:6',
            'nid' => 'required|unique:students,nid',
            'photo' => 'nullable|image|max:2048',
            'id_photo' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['icon' => 'error', 'title' => $validator->errors()->first()], 400);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        $idPhotoPath = null;
        if ($request->hasFile('id_photo')) {
            $idPhotoPath = $request->file('id_photo')->store('students/ids', 'public');
        }

        Student::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'nid' => $request->nid,
            'age' => $request->age,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'stage_id' => $request->stage_id,
            'gender' => $request->gender,
            'photo' => $photoPath,
            'id_photo' => $idPhotoPath,
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'title' => 'تم إنشاء حساب الطالب وتفعيله ✅']);
    }

    public function studentCreate() {
        $stages = Stage::all();
        return view('admin.management.students_create', compact('stages'));
    }

    public function settingsUpdate(Request $request)
    {
        try {
            foreach ($request->except('_token') as $key => $value) {
                if ($value !== null) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                }
            }
            return response()->json(['success' => true, 'title' => 'تم تحديث هوية المنصة بنجاح ✅']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
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
