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
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('student_id')->nullable();
        $table->unsignedBigInteger('admin_id')->nullable();
        $table->unsignedBigInteger('teacher_id')->nullable(); // إضافة عمود للمدرس
        $table->enum('sender_type', ['student', 'admin', 'teacher']); // إضافة teacher للأنواع
        $table->text('message');
        $table->boolean('is_read')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
