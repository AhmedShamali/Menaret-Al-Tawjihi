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
        if (!Schema::hasTable('teacher_salaries')) {
            Schema::create('teacher_salaries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->unsignedSmallInteger('year')->default(2026);
                $table->unsignedTinyInteger('month'); // 1 to 12
                $table->decimal('basic_salary', 10, 2)->default(0.00);
                $table->decimal('bonus', 10, 2)->default(0.00);
                $table->decimal('deductions', 10, 2)->default(0.00);
                $table->decimal('net_salary', 10, 2)->default(0.00);
                $table->string('status')->default('paid'); // paid, pending
                $table->date('payment_date')->nullable();
                $table->string('payment_method')->default('تحويل بنكي'); // تحويل بنكي, جوال باي, بال باي, كاش
                $table->string('reference_no')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['teacher_id', 'year', 'month'], 'teacher_year_month_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_salaries');
    }
};
