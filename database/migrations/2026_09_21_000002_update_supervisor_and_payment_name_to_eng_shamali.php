<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
            Setting::where('key', 'payment_account_name')
                ->where(function($q) {
                    $q->where('value', 'أحمد حسين شمالي')
                      ->orWhere('value', 'أ. أحمد حسين شمالي');
                })
                ->update(['value' => 'م.أحمد شمالي']);

            Setting::where('key', 'supervisor_name')
                ->update(['value' => 'م.أحمد شمالي']);

            Setting::where('key', 'site_supervisor')
                ->update(['value' => 'م.أحمد شمالي']);

            // Update admin user name if it has old format
            User::where('role', 'admin')
                ->where('email', 'ahmad@admin.ps')
                ->update([
                    'name'    => 'م.أحمد شمالي (مدير النظام)',
                    'name_ar' => 'م.أحمد شمالي',
                ]);
        } catch (\Throwable $e) {
            // Ignore during early installation
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
