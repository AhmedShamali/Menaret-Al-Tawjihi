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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('title'); // عنوان الاختبار (مثلاً: نصفي الفصل الأول)
            $table->integer('duration_minutes')->default(60); // مدة الاختبار
            $table->integer('total_points')->default(0);
            $table->enum('status', [
                'pending',
                'draft',
                'published'
            ])->default('pending');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
