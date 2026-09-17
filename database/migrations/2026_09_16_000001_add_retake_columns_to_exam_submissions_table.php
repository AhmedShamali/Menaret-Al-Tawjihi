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
        Schema::table('exam_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_submissions', 'allow_retake')) {
                $table->boolean('allow_retake')->default(false)->after('status');
            }
            if (!Schema::hasColumn('exam_submissions', 'retake_requested')) {
                $table->boolean('retake_requested')->default(false)->after('allow_retake');
            }
            if (!Schema::hasColumn('exam_submissions', 'retake_request_notes')) {
                $table->text('retake_request_notes')->nullable()->after('retake_requested');
            }
            if (!Schema::hasColumn('exam_submissions', 'retake_granted_by')) {
                $table->unsignedBigInteger('retake_granted_by')->nullable()->after('retake_request_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->dropColumn(['allow_retake', 'retake_requested', 'retake_request_notes', 'retake_granted_by']);
        });
    }
};
