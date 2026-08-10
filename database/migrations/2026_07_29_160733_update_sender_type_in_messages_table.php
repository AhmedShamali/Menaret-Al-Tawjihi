<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // الخيار الأول: تعديله ليكون string بطول ممتاز (موصى به)
            $table->string('sender_type', 20)->default('student')->change();

            // أو الخيار الثاني: تعديل الـ ENUM ليشمل جميع الأدوار
            // $table->enum('sender_type', ['student', 'teacher', 'admin'])->default('student')->change();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender_type', 255)->change();
        });
    }
};
