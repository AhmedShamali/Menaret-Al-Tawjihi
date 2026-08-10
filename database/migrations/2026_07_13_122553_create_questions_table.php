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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['mcq', 'essay']); // موضوعي أو مقالي
            $table->text('question_text');
            $table->string('image')->nullable(); // ⬅️ تم إضافة حقل الصورة هنا بنجاح
            $table->string('a')->nullable(); // خيارات الموضوعي
            $table->string('b')->nullable();
            $table->string('c')->nullable();
            $table->string('d')->nullable();
            $table->char('correct_answer', 1)->nullable();
            $table->boolean('require_file')->default(false); // للمقالي
            $table->integer('points')->default(5);
            $table->boolean('is_placement')->default(false); // إذا كان true فهو سؤال فاقد تعليمي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
