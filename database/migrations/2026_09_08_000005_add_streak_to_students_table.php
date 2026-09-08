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
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'streak_count')) {
                    $table->integer('streak_count')->default(1);
                }
                if (!Schema::hasColumn('students', 'last_activity_date')) {
                    $table->date('last_activity_date')->nullable();
                }
                if (!Schema::hasColumn('students', 'total_points')) {
                    $table->integer('total_points')->default(50);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn(['streak_count', 'last_activity_date', 'total_points']);
            });
        }
    }
};
