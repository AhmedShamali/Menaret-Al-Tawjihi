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
            if (!Schema::hasColumn('exams', 'starts_at')) {
                $table->timestamp('starts_at')->nullable()->after('duration_minutes');
            }
            if (!Schema::hasColumn('exams', 'ends_at')) {
                $table->timestamp('ends_at')->nullable()->after('starts_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'ends_at')) {
                $table->dropColumn('ends_at');
            }
            if (Schema::hasColumn('exams', 'starts_at')) {
                $table->dropColumn('starts_at');
            }
        });
    }
};
