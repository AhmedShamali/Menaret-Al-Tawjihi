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
        if (!Schema::hasTable('past_exams')) {
            Schema::create('past_exams', function (Blueprint $table) {
                $table->id();
                $table->integer('year')->index(); // 2024, 2023, 2022...
                $table->string('session')->default('first'); // first = الدورة الأولى, second = الدورة الثانية, completion = الاستكمالية
                $table->string('branch')->default('scientific'); // scientific, literary, business, industrial
                $table->string('subject_name')->index(); // رياضيات، فيزياء، لغة عربية...
                $table->string('exam_paper_url')->nullable(); // ملف ورقة الامتحان
                $table->string('answer_key_url')->nullable(); // ملف نموذج الإجابة المعتمد وسلّم الدرجات
                $table->integer('total_marks')->default(100);
                $table->integer('downloads_count')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_exams');
    }
};
