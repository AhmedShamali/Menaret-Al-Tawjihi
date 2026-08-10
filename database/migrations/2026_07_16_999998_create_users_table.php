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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // الاسم الرباعي
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable(); // رقم الجوال
            $table->string('major')->nullable(); // التخصص (مثلاً: ماجستير رياضيات)
            $table->text('bio')->nullable(); // نبذة تعريفية
            $table->string('photo')->nullable(); // الصورة الشخصية
            $table->enum('role', ['admin', 'teacher'])->default('teacher');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
