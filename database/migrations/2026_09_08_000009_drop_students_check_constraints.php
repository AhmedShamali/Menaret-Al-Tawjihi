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
        // 1. إسقاط قيد التحقق القديم students_status_check في PostgreSQL لتمكين حفظ 'active'
        try {
            DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_status_check');
        } catch (\Throwable $e) {
            //
        }

        // 2. إسقاط قيد التحقق القديم students_gender_check في PostgreSQL إذا وجد
        try {
            DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_gender_check');
        } catch (\Throwable $e) {
            //
        }

        // 3. تحويل الأعمدة إلى نصوص قياسية مرنة
        try {
            Schema::table('students', function (Blueprint $table) {
                $table->string('status', 50)->default('active')->change();
                $table->string('gender', 20)->default('ذكر')->change();
            });
        } catch (\Throwable $e) {
            //
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
