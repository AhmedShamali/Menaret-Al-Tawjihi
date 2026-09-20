<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // 1. Update Platform Settings
            Setting::updateOrCreate(
                ['key' => 'payment_account_name'],
                ['value' => 'م.أحمد شمالي']
            );

            Setting::updateOrCreate(
                ['key' => 'supervisor_name'],
                ['value' => 'م.أحمد شمالي']
            );

            Setting::updateOrCreate(
                ['key' => 'supervisor_name_en'],
                ['value' => 'Eng.Ahmed Shamali']
            );

            // 2. Update Admin Users in DB
            User::where('email', 'ahmad@admin.ps')
                ->orWhere('role', 'admin')
                ->update([
                    'name'    => 'م.أحمد شمالي (مدير النظام)',
                    'name_ar' => 'م.أحمد شمالي',
                    'name_en' => 'Eng.Ahmed Shamali',
                ]);

            // 3. Update any users with old supervisor names
            User::where('name', 'LIKE', '%أحمد حسين شمالي%')
                ->orWhere('name', 'LIKE', '%أ. أحمد حسين شمالي%')
                ->orWhere('name', 'LIKE', '%أ.أحمد حسين شمالي%')
                ->orWhere('name', 'LIKE', '%أ. أحمد شمالي%')
                ->update([
                    'name'    => 'م.أحمد شمالي (مدير النظام)',
                    'name_ar' => 'م.أحمد شمالي',
                    'name_en' => 'Eng.Ahmed Shamali',
                ]);

        } catch (\Throwable $e) {
            // Graceful fallback if tables not yet present
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};
