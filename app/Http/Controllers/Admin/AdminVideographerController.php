<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminVideographerController extends Controller
{
    /**
     * قائمة المصورين المعتمدين وسجل أعمالهم
     */
    public function index()
    {
        $videographers = User::where('role', 'videographer')
            ->withCount('uploadedContents')
            ->latest()
            ->paginate(15);

        return view('admin.videographers.index', compact('videographers'));
    }

    /**
     * نموذج إضافة مصور جديد
     */
    public function create()
    {
        return view('admin.videographers.create');
    }

    /**
     * حفظ وتفعيل حساب المصور الجديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:30',
            'bio'      => 'nullable|string|max:500',
        ], [
            'name.required'     => 'يرجى إدخال اسم المصور أو وحدة التصوير.',
            'email.required'    => 'يرجى إدخال البريد الإلكتروني للمصور.',
            'email.email'       => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique'      => 'البريد الإلكتروني مسجل مسبقاً في النظام.',
            'password.required' => 'يرجى تعيين كلمة مرور للحساب.',
            'password.min'      => 'كلمة المرور يجب ألا تقل عن 6 خانات.',
        ]);

        $userData = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'bio'      => $request->bio,
            'role'     => 'videographer',
        ];

        if (Schema::hasColumn('users', 'plain_password')) {
            $userData['plain_password'] = $request->password;
        }

        User::create($userData);

        return redirect()->route('admin.videographers.index')
            ->with('success', 'تم إنشاء وتفعيل حساب المصور (' . $request->name . ') بنجاح! يمكنه الآن تسجيل الدخول ورفع المحاضرات فوراً 🎉');
    }

    /**
     * إعادة تعيين كلمة مرور المصور
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ], [
            'new_password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'new_password.min'      => 'كلمة المرور يجب ألا تقل عن 6 أحرف.',
        ]);

        $videographer = User::where('role', 'videographer')->findOrFail($id);

        $updateData = [
            'password' => Hash::make($request->new_password),
        ];

        if (Schema::hasColumn('users', 'plain_password')) {
            $updateData['plain_password'] = $request->new_password;
        }

        $videographer->update($updateData);

        return redirect()->back()->with('success', 'تم تحديث كلمة المرور للمصور (' . $videographer->name . ') بنجاح.');
    }

    /**
     * حذف حساب المصور
     */
    public function destroy($id)
    {
        $videographer = User::where('role', 'videographer')->findOrFail($id);
        $name = $videographer->name;
        $videographer->delete();

        return redirect()->route('admin.videographers.index')
            ->with('success', 'تم حذف حساب المصور (' . $name . ') بنجاح.');
    }
}
