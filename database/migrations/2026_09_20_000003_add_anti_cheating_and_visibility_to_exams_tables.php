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
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'show_result_immediately')) {
                $table->boolean('show_result_immediately')->default(false)->after('duration_minutes');
            }
        });

        Schema::table('exam_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_submissions', 'tab_switches_count')) {
                $table->integer('tab_switches_count')->default(0)->after('total_earned_grade');
            }
            if (!Schema::hasColumn('exam_submissions', 'screenshots_count')) {
                $table->integer('screenshots_count')->default(0)->after('tab_switches_count');
            }
            if (!Schema::hasColumn('exam_submissions', 'cheating_flags')) {
                $table->json('cheating_flags')->nullable()->after('screenshots_count');
            }
            if (!Schema::hasColumn('exam_submissions', 'has_cheating_risk')) {
                $table->boolean('has_cheating_risk')->default(false)->after('cheating_flags');
            }
            if (!Schema::hasColumn('exam_submissions', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('has_cheating_risk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'show_result_immediately')) {
                $table->dropColumn('show_result_immediately');
            }
        });

        Schema::table('exam_submissions', function (Blueprint $table) {
            $cols = ['tab_switches_count', 'screenshots_count', 'cheating_flags', 'has_cheating_risk', 'is_published'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('exam_submissions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
