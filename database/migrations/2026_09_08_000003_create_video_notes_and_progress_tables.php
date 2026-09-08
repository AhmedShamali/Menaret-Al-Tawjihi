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
        // 1. جدول ملاحظات الطالب على توقيتات الفيديو (Timestamped Notes)
        if (!Schema::hasTable('video_notes')) {
            Schema::create('video_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('educational_content_id')->constrained('educational_contents')->onDelete('cascade');
                $table->integer('timestamp_seconds')->default(0); // توقيت الملاحظة بالثواني
                $table->text('note_text');
                $table->timestamps();
            });
        }

        // 2. جدول تقدم مشاهدة الفيديو لكل طالب
        if (!Schema::hasTable('video_progress')) {
            Schema::create('video_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('educational_content_id')->constrained('educational_contents')->onDelete('cascade');
                $table->integer('last_position_seconds')->default(0);
                $table->boolean('is_completed')->default(false);
                $table->timestamps();

                $table->unique(['student_id', 'educational_content_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_progress');
        Schema::dropIfExists('video_notes');
    }
};
