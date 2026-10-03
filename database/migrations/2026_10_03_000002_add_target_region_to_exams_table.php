<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                if (!Schema::hasColumn('exams', 'target_region')) {
                    $table->string('target_region', 20)->default('all')->after('status')->index();
                }
            });

            // تعيين القيمة الافتراضية للاختبارات الحالية
            try {
                DB::table('exams')
                    ->whereNull('target_region')
                    ->orWhere('target_region', '')
                    ->update(['target_region' => 'all']);
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('exams') && Schema::hasColumn('exams', 'target_region')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropColumn('target_region');
            });
        }
    }
};
