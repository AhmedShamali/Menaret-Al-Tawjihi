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
    Schema::table('subjects', function (Blueprint $table) {
        $table->string('teacher_name')->nullable()->after('name_ar');
    });
}

public function down(): void
{
    Schema::table('subjects', function (Blueprint $table) {
        $table->dropColumn('teacher_name');
    });
}
};
