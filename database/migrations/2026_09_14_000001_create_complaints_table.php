<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('complaints')) {
            Schema::create('complaints', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('type')->default('استفسار عام');
                $table->string('category')->nullable();
                $table->string('subject')->nullable();
                $table->text('message');
                $table->text('reply')->nullable();
                $table->timestamp('replied_at')->nullable();
                $table->string('status')->default('new'); // new, pending, replied, resolved
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
