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
        if (!Schema::hasTable('ai_conversations')) {
            Schema::create('ai_conversations', function (Blueprint $table) {
                $table->id();
                $table->string('actor_type')->default('student'); // 'student' or 'user'
                $table->unsignedBigInteger('actor_id');
                $table->string('title')->default('محادثة جديدة');
                $table->string('category')->default('general'); // 'general', 'physics', 'math', 'platform', etc.
                $table->string('symbol_mode')->default('ar'); // 'ar' or 'en'
                $table->timestamps();

                $table->index(['actor_type', 'actor_id']);
            });
        }

        if (!Schema::hasTable('ai_messages')) {
            Schema::create('ai_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('ai_conversations')->onDelete('cascade');
                $table->string('role')->default('user'); // 'user', 'assistant', 'system'
                $table->longText('content');
                $table->string('image_url')->nullable();
                $table->string('notation_mode')->nullable(); // 'ar' or 'en'
                $table->timestamps();

                $table->index('conversation_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
    }
};
