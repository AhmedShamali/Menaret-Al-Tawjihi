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
                if (!Schema::hasColumn('students', 'custom_discount_percent')) {
                    $table->decimal('custom_discount_percent', 5, 2)->default(0);
                }
                if (!Schema::hasColumn('students', 'custom_discount_fixed')) {
                    $table->decimal('custom_discount_fixed', 8, 2)->default(0);
                }
                if (!Schema::hasColumn('students', 'discount_notes')) {
                    $table->string('discount_notes', 255)->nullable();
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
                $columnsToDrop = [];
                if (Schema::hasColumn('students', 'custom_discount_percent')) {
                    $columnsToDrop[] = 'custom_discount_percent';
                }
                if (Schema::hasColumn('students', 'custom_discount_fixed')) {
                    $columnsToDrop[] = 'custom_discount_fixed';
                }
                if (Schema::hasColumn('students', 'discount_notes')) {
                    $columnsToDrop[] = 'discount_notes';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
