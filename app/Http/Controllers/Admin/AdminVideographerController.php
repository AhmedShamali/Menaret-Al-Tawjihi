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

        $cleanEmail = strtolower(trim((string)$request->email));
        $cleanPass = trim((string)$request->password);

        $userData = [
            'name'     => trim((string)$request->name),
            'email'    => $cleanEmail,
            'password' => Hash::make($cleanPass),
            'phone'    => $request->phone ? trim((string)$request->phone) : null,
            'bio'      => $request->bio ? trim((string)$request->bio) : null,
            'role'     => 'videographer',
        ];

        if (Schema::hasColumn('users', 'plain_password')) {
            $userData['plain_password'] = $cleanPass;
        }

        User::create($userData);

        return redirect()->route('admin.videographers.index')
            ->with('success', 'تم إنشاء وتفعيل حساب المصور (' . trim((string)$request->name) . ') بنجاح! يمكنه الآن تسجيل الدخول ورفع المحاضرات فوراً 🎉');
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

        $cleanPass = trim((string)$request->new_password);
        $updateData = [
            'password' => Hash::make($cleanPass),
        ];

        if (Schema::hasColumn('users', 'plain_password')) {
            $updateData['plain_password'] = $cleanPass;
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
