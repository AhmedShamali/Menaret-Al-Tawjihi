<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index() {
        $students = Student::with('stage')->latest()->get();
        return view('students.index', compact('students'));
    }

    public function create() {
        $stages = Stage::all();
        return view('students.create', compact('stages'));
    }

    public function store(Request $request) {
        $attributes = [
            'name_ar'       => 'الاسم بالعربي',
            'nid'           => 'رقم الهوية',
            'email'         => 'البريد الإلكتروني',
            'password'      => 'كلمة المرور',
            'photo'         => 'الصورة الشخصية',
            'id_photo'      => 'صورة الهوية',
            'stage_id'      => 'الصف الدراسي'
        ];

        $validator = Validator::make($request->all(), [
            'name_ar'       => 'required',
            'nid'           => 'required|digits:9|unique:students,nid',
            'email'         => 'required|email|unique:students,email',
            'password'      => 'required|min:8',
            'photo'         => 'required|image|max:2048',
            'id_photo'      => 'required|image|max:2048',
            'stage_id'      => 'required',
        ], ['required'      => 'حقل :attribute مطلوب.'], $attributes);

        if ($validator->fails()) {
            return response()->json([
                'icon'  => 'error',
                'title' => $validator->errors()->first()
            ], 400);
        }

        $student = new Student($request->except(['password', 'photo', 'id_photo']));
        $student->password = Hash::make($request->password);
        $student->photo = $request->file('photo')->store('students/photos', 'public');
        $student->id_photo = $request->file('id_photo')->store('students/ids', 'public');
        $student->status = 'pending';
        $student->save();

        return response()->json(['icon' => 'success', 'title' => 'تم تسجيل الطالب بنجاح! 🎉']);
    }

    // صفحة التعديل
    public function edit($id) {
        $student = Student::findOrFail($id);
        $stages = Stage::all();
        return view('students.edit', compact('student', 'stages'));
    }

    // تحديث البيانات (AJAX)
    public function update(Request $request, $id) {
        $student = Student::findOrFail($id);
        $data = $request->except(['password', 'photo', 'id_photo']);

        if ($request->filled('password')) $data['password'] = Hash::make($request->password);
        if ($request->hasFile('photo')) $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        if ($request->hasFile('id_photo')) $data['id_photo'] = $request->file('id_photo')->store('students/ids', 'public');

        $student->update($data);
        return response()->json(['icon' => 'success', 'title' => 'تم تحديث البيانات بنجاح 🚀']);
    }

    // حذف طالب
    public function destroy($id) {
        return response()->json(['success' => Student::destroy($id)]);
    }



    public function toggleStatus($id)
    {
        $student = \App\Models\Student::findOrFail($id);

        // تأكد أن الكلمات هنا (active, pending) مطابقة تماماً لما هو موجود في الـ Migration
        if ($student->status == 'active') {
            $student->status = 'pending';
            $title = 'تم تعطيل الحساب 🔒';
        } else {
            $student->status = 'active';
            $title = 'تم تفعيل الحساب ✅';
        }

        $student->save();

        return response()->json([
            'icon'  => 'success',
            'title' => $title
        ]);
    }
}

