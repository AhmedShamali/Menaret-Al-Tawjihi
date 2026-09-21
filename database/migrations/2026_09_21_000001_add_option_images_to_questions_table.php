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
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'a_image')) {
                $table->string('a_image')->nullable()->after('a');
            }
            if (!Schema::hasColumn('questions', 'b_image')) {
                $table->string('b_image')->nullable()->after('b');
            }
            if (!Schema::hasColumn('questions', 'c_image')) {
                $table->string('c_image')->nullable()->after('c');
            }
            if (!Schema::hasColumn('questions', 'd_image')) {
                $table->string('d_image')->nullable()->after('d');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $cols = ['a_image', 'b_image', 'c_image', 'd_image'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('questions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
