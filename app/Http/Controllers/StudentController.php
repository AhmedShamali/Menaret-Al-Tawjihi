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

    // 2. ملف الطالب الشخصي (للطالب المسجل دخوله)
    public function profile()
    {
        $student = auth()->user();
        return view('admin.students.profile', compact('student'));
    }

    // 3. عرض كافة البروفايلات (للأدمن)
    public function profile_all()
    {
        $students = Student::latest()->paginate(10);
        return view('admin.students.profile-all', compact('students'));
    }

    public function create() {
        $stages = Stage::all();
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

    // 4. دالة الحفظ (Store) - معدلة لضمان مسارات الصور
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name_ar'  => 'required|string|max:255',
            'nid'      => 'required|digits:9|unique:students,nid',
            'email'    => 'required|email|unique:students,email',
            'password' => 'required|min:8',
            'photo'    => 'required|image|max:2048',
            'id_photo' => 'required|image|max:2048',
            'stage_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['icon' => 'error', 'title' => $validator->errors()->first()], 400);
        }

        // رفع وتخزين الصورة الشخصية (ستخزن في storage/app/public/students/photos)
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        // رفع وتخزين صورة الهوية (ستخزن في storage/app/public/students/ids)
        $idPhotoPath = null;
        if ($request->hasFile('id_photo')) {
            $idPhotoPath = $request->file('id_photo')->store('students/ids', 'public');
        }

        // إنشاء السجل مع التأكد من إرسال المتغيرات التي تحمل "المسار" كـ String
        Student::create([
            'name_ar'  => $request->name_ar,
            'name_en'  => $request->name_en,
            'nid'      => $request->nid,
            'age'      => $request->age,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'stage_id' => $request->stage_id,
            'gender'   => $request->gender,
            'photo'    => $photoPath,    // سيتم حفظ نص مثل: students/photos/xyz.jpg
            'id_photo' => $idPhotoPath, // سيتم حفظ نص مثل: students/ids/abc.jpg
            'status'   => 'active',     // جعلته نشط مباشرة للإدارة
        ]);

        return response()->json(['icon' => 'success', 'title' => 'تم تسجيل الطالب بنجاح! 🎉']);
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
}
