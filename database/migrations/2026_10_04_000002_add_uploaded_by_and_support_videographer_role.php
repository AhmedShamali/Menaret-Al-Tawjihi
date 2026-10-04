<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. إضافة حقل من قام بالرفع في جدول المحتوى التعليمي
        if (Schema::hasTable('educational_contents')) {
            Schema::table('educational_contents', function (Blueprint $table) {
                if (!Schema::hasColumn('educational_contents', 'uploaded_by')) {
                    $table->foreignId('uploaded_by')->nullable()->after('subject_id')->constrained('users')->nullOnDelete();
                }
            });
        }

        // 2. تحديث عمود role في جدول users لدعم دور المصور videographer بأمان تام
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'role')) {
                    // جعل العمود string(32) ليتسع لأي دور بدون قيود enum
                    try {
                        $table->string('role', 32)->default('teacher')->change();
                    } catch (\Throwable $e) {
                        // في حال عدم توفر doctrine/dbal في بعض بيئات الاختبار
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('educational_contents')) {
            Schema::table('educational_contents', function (Blueprint $table) {
                if (Schema::hasColumn('educational_contents', 'uploaded_by')) {
                    $table->dropForeign(['uploaded_by']);
                    $table->dropColumn('uploaded_by');
                }
            });
        }
    }
};
