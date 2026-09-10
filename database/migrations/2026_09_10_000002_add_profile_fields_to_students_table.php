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
                if (!Schema::hasColumn('students', 'city')) {
                    $table->string('city')->nullable()->after('gender');
                }
                if (!Schema::hasColumn('students', 'school_name')) {
                    $table->string('school_name')->nullable()->after('city');
                }
                if (!Schema::hasColumn('students', 'guardian_phone')) {
                    $table->string('guardian_phone')->nullable()->after('whatsapp');
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
                if (Schema::hasColumn('students', 'guardian_phone')) {
                    $table->dropColumn('guardian_phone');
                }
                if (Schema::hasColumn('students', 'school_name')) {
                    $table->dropColumn('school_name');
                }
                if (Schema::hasColumn('students', 'city')) {
                    $table->dropColumn('city');
                }
            });
        }
    }
};
