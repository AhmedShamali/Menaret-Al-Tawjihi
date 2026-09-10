<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Setting::updateOrCreate(
                ['key' => 'contact_whatsapp'],
                ['value' => '00970597694385']
            );

            Setting::updateOrCreate(
                ['key' => 'supervisor_whatsapp'],
                ['value' => '+970597694385']
            );

            Setting::updateOrCreate(
                ['key' => 'supervisor_name'],
                ['value' => 'أ. أحمد حسين شمالي']
            );
        } catch (\Throwable $e) {
            // Ignore if settings table does not exist yet
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
