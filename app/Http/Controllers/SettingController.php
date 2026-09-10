<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // تجهيز مصفوفة الإعدادات الحالية لعرضها في الفورم
        $settings = [
            'site_name' => Setting::get('site_name', 'منارة التوجيهي'),
            'contact_email' => Setting::get('contact_email', 'info@jesr.ps'),
            'contact_whatsapp' => Setting::get('contact_whatsapp', '00970597694385'),
            'registration_status' => Setting::get('registration_status', 'open'),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // تحديث أو إنشاء الإعداد لكل حقل مرسل
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['success' => true, 'title' => 'تم تحديث إعدادات النظام بنجاح ⚙️']);
    }
}
