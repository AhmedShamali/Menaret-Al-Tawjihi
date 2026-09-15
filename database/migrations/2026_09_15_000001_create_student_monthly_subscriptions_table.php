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
        if (!Schema::hasTable('student_monthly_subscriptions')) {
            Schema::create('student_monthly_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->string('academic_year', 16)->default('2026-2027');
                $table->unsignedTinyInteger('month'); // 1 to 12
                $table->decimal('amount', 8, 2)->default(150.00);
                $table->string('status')->default('unpaid'); // paid, pending, unpaid, waived
                $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'academic_year', 'month'], 'student_year_month_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_monthly_subscriptions');
    }
};
