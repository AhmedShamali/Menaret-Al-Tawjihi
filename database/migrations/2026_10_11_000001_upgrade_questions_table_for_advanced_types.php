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
        // 1. توسيع حقل الإجابة الصحيحة وحقل النوع ليدعم النصوص الطويلة والـ JSON في PostgreSQL و MySQL
        try {
            DB::statement("ALTER TABLE questions ALTER COLUMN correct_answer TYPE TEXT");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE questions ALTER COLUMN type TYPE VARCHAR(50)");
        } catch (\Throwable $e) {}

        // 2. إضافة الأعمدة الداعمة للأسئلة المتقدمة (مطابقة وسحب مربعات، إجابات متعددة، بيانات إضافية)
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'matching_pairs')) {
                $table->text('matching_pairs')->nullable();
            }
            if (!Schema::hasColumn('questions', 'options_data')) {
                $table->text('options_data')->nullable();
            }
            if (!Schema::hasColumn('questions', 'is_multiple')) {
                $table->boolean('is_multiple')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'matching_pairs')) {
                $table->dropColumn('matching_pairs');
            }
            if (Schema::hasColumn('questions', 'options_data')) {
                $table->dropColumn('options_data');
            }
            if (Schema::hasColumn('questions', 'is_multiple')) {
                $table->dropColumn('is_multiple');
            }
        });
    }
};
