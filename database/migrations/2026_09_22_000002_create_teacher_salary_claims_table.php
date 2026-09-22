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
        if (!Schema::hasTable('teacher_salary_claims')) {
            Schema::create('teacher_salary_claims', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->integer('year');
                $table->integer('month');
                $table->text('message');
                $table->text('admin_reply')->nullable();
                $table->foreignId('replied_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('replied_at')->nullable();
                $table->string('status')->default('pending'); // pending, replied
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_salary_claims');
    }
};
