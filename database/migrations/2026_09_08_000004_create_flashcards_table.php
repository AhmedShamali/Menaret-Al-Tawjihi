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
        if (!Schema::hasTable('flashcards')) {
            Schema::create('flashcards', function (Blueprint $table) {
                $table->id();
                $table->string('subject_name')->index(); // فيزياء، رياضيات، كيمياء، تاريخ...
                $table->string('branch')->default('all'); // scientific, literary, all
                $table->string('category')->default('عام'); // قوانين، مفاهيم، تواريخ، إعراب
                $table->text('front_text'); // الوجه الأمامي (السؤال أو المصطلح)
                $table->text('back_text'); // الوجه الخلفي (القانون، الإجابة أو الشرح)
                $table->string('difficulty')->default('medium'); // easy, medium, hard
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcards');
    }
};
