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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'monthly_fee')) {
                $table->decimal('monthly_fee', 8, 2)->default(150.00)->after('stage_id');
            }
            if (!Schema::hasColumn('students', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'monthly_fee')) {
                $table->dropColumn('monthly_fee');
            }
            if (Schema::hasColumn('students', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
        });
    }
};
