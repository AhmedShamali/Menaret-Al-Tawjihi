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
        // 1. جدول اشتراكات الطلاب بالمواد (منهج كامل أو مخصص)
        if (!Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->string('status')->default('active'); // active, inactive, expired
                $table->string('access_mode')->default('all'); // all = كامل المنهج, custom = جزئية مخصصة
                $table->string('payment_status')->default('paid'); // paid, free, voucher
                $table->unsignedBigInteger('assigned_by')->nullable(); // المعلم أو المشرف
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'subject_id']);
            });
        }

        // 2. جدول تخصيص الفيديوهات والدروس للطلاب ذوي الاشتراكات المخصصة
        if (!Schema::hasTable('content_assignments')) {
            Schema::create('content_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
                $table->foreignId('educational_content_id')->constrained('educational_contents')->onDelete('cascade');
                $table->boolean('is_visible')->default(true);
                $table->timestamps();

                $table->unique(['enrollment_id', 'educational_content_id']);
            });
        }

        // 3. جدول بطاقات وكروت الشحن والتفعيل لطلاب التوجيهي في فلسطين
        if (!Schema::hasTable('activation_codes')) {
            Schema::create('activation_codes', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->string('access_mode')->default('all');
                $table->integer('price_ils')->default(100); // السعر بالشيكل
                $table->integer('duration_days')->default(365);
                $table->boolean('is_used')->default(false);
                $table->unsignedBigInteger('used_by_student_id')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activation_codes');
        Schema::dropIfExists('content_assignments');
        Schema::dropIfExists('enrollments');
    }
};
