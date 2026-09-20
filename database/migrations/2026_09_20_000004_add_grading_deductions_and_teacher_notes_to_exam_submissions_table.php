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
        Schema::table('exam_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_submissions', 'deduction_amount')) {
                $table->float('deduction_amount')->default(0)->nullable()->after('total_earned_grade');
            }
            if (!Schema::hasColumn('exam_submissions', 'deduction_reason')) {
                $table->text('deduction_reason')->nullable()->after('deduction_amount');
            }
            if (!Schema::hasColumn('exam_submissions', 'teacher_notes')) {
                $table->text('teacher_notes')->nullable()->after('deduction_reason');
            }
        });

        // مزامنة فورية: أي اختبار سابق بدون مرحلة محددة، يتم ربطه بمرحلة المادة الخاصة به
        try {
            DB::statement("
                UPDATE exams 
                SET stage_id = subjects.stage_id 
                FROM subjects 
                WHERE exams.stage_id IS NULL 
                  AND exams.subject_id = subjects.id 
                  AND subjects.stage_id IS NOT NULL
            ");
        } catch (\Throwable $e) {
            // للتوافق مع SQLite في بيئة الاختبارات
            try {
                $exams = DB::table('exams')->whereNull('stage_id')->whereNotNull('subject_id')->get();
                foreach ($exams as $ex) {
                    $sub = DB::table('subjects')->where('id', $ex->subject_id)->first();
                    if ($sub && !empty($sub->stage_id)) {
                        DB::table('exams')->where('id', $ex->id)->update(['stage_id' => $sub->stage_id]);
                    }
                }
            } catch (\Throwable $ex) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_submissions', function (Blueprint $table) {
            $cols = ['deduction_amount', 'deduction_reason', 'teacher_notes'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('exam_submissions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
