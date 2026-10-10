<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // رقم الواتس الخاص بالمدير
            Setting::updateOrCreate(
                ['key' => 'contact_whatsapp'],
                ['value' => '00970597694385']
            );

            Setting::updateOrCreate(
                ['key' => 'supervisor_whatsapp'],
                ['value' => '+970597694385']
            );

            // رقم التحويل والسداد المعتمد (جوال باي، بنك فلسطين، بال باي)
            Setting::updateOrCreate(
                ['key' => 'payment_phone'],
                ['value' => '0567897212']
            );

            Setting::updateOrCreate(
                ['key' => 'palpay_account'],
                ['value' => '0567897212']
            );

            Setting::updateOrCreate(
                ['key' => 'jawwal_pay_account'],
                ['value' => '0567897212']
            );

            Setting::updateOrCreate(
                ['key' => 'payment_account_name'],
                ['value' => 'م. أحمد شمالي']
            );

            Setting::updateOrCreate(
                ['key' => 'supervisor_name'],
                ['value' => 'م. أحمد شمالي']
            );
        } catch (\Throwable $e) {
            // Ignore if settings table does not exist yet during fresh installation
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
