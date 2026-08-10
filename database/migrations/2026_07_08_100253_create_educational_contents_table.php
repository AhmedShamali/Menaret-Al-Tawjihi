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
            Schema::create('educational_contents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->enum('type', ['video', 'file']);
                $table->text('url_path');
                $table->string('channel_name')->nullable();
                $table->string('file_size')->nullable();
                $table->integer('order')->default(0);
                $table->integer('views_count')->default(0);
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_contents');
    }
};
