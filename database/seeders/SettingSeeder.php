<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            'site_name'             => 'منارة التوجيهي',
            'site_tagline'          => 'المنصة الوطنية الرائدة لطلبة الثانوية العامة في فلسطين',
            'site_description'      => 'منصة تعليمية متكاملة لطلبة التوجيهي تشمل امتحانات وزارية، شروحات مرئية، بنك أسئلة، بطاقات ذكية، وحاسبة دقيقة لمعدل الثانوية العامة.',
            'contact_email'         => 'ahmed.shamali@tawjihi.ps',
            'contact_whatsapp'      => '00970597694385',
            'contact_phone'         => '0567897212',
            'registration_status'   => 'open',
            'payment_account_name'  => 'أحمد حسين شمالي',
            'payment_phone'         => '0567897212',
            'payment_bank_name'     => 'بنك فلسطين',
            'payment_account_no'    => '2275913',
            'palpay_account'        => '0567897212',
            'palpay_service_code'   => '99420',
            'jawwal_pay_account'    => '0567897212',
            'bop_account'           => '2275913',
            'site_logo'             => '',
            'site_favicon'          => '',
            'academic_year'         => '2026',
            'announcement_banner'   => 'أهلاً بكم في منارة التوجيهي - تم تحديث حساب المعدل الوزاري وفق سلم الدرجات المعتمد 2026.',
        ];

        foreach ($defaultSettings as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
