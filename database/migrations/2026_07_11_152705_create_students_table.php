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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('nid')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('age');
            $table->enum('gender', ['ذكر', 'أنثى']);
            $table->string('phone');
            $table->string('whatsapp');
            $table->string('status')->default('pending')->change();
            $table->string('photo')->nullable();
            $table->string('id_photo')->nullable();
            $table->foreignId('stage_id')->constrained('stages')->onDelete('cascade');
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
        Schema::dropIfExists('students');
    }
};
