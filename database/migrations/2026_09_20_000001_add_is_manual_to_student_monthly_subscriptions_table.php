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
        Schema::table('student_monthly_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('student_monthly_subscriptions', 'is_manual')) {
                $table->boolean('is_manual')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_monthly_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('student_monthly_subscriptions', 'is_manual')) {
                $table->dropColumn('is_manual');
            }
        });
    }
};
