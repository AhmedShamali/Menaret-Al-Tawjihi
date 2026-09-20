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
        // 1. إسقاط قيود التحقق (Check Constraints) القديمة الموروثة من تعريف الـ ENUM في PostgreSQL
        $constraints = [
            ['table' => 'educational_contents', 'constraint' => 'educational_contents_type_check'],
            ['table' => 'messages',             'constraint' => 'messages_sender_type_check'],
            ['table' => 'support_tickets',      'constraint' => 'support_tickets_status_check'],
            ['table' => 'exams',                'constraint' => 'exams_status_check'],
            ['table' => 'exam_submissions',     'constraint' => 'exam_submissions_status_check'],
            ['table' => 'questions',            'constraint' => 'questions_type_check'],
            ['table' => 'students',             'constraint' => 'students_status_check'],
            ['table' => 'students',             'constraint' => 'students_gender_check'],
        ];

        foreach ($constraints as $c) {
            try {
                DB::statement("ALTER TABLE {$c['table']} DROP CONSTRAINT IF EXISTS {$c['constraint']}");
            } catch (\Throwable $e) {
                // تجاوز إذا لم تكن قاعدة البيانات pgsql أو كان القيد غير موجود
            }
        }

        // 2. إسقاط قيد NOT NULL عن url_path إن وجد في PostgreSQL لتمكين رفع الدوسيات والملفات وحدها
        try {
            DB::statement("ALTER TABLE educational_contents ALTER COLUMN url_path DROP NOT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE educational_contents ALTER COLUMN pdf_path DROP NOT NULL");
        } catch (\Throwable $e) {}

        // 3. ضمان تعديل أعمدة الجداول لتكون نصوصاً قياسية مرنة تدعم كافة القيم المدخلة
        try {
            if (Schema::hasTable('educational_contents')) {
                Schema::table('educational_contents', function (Blueprint $table) {
                    $table->string('type', 50)->default('video')->change();
                    $table->text('url_path')->nullable()->change();
                    $table->text('pdf_path')->nullable()->change();
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('messages')) {
                Schema::table('messages', function (Blueprint $table) {
                    $table->string('sender_type', 50)->default('student')->change();
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('support_tickets')) {
                Schema::table('support_tickets', function (Blueprint $table) {
                    $table->string('status', 50)->default('open')->change();
                });
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا حاجة لإعادة تقييد الأعمدة
    }
};
