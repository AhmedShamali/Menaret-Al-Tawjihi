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
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->string('transaction_number')->unique(); // e.g. PAL-2026-XXXX
                $table->string('gateway'); // jawwal_pay, palpay, bop, voucher
                $table->decimal('amount', 8, 2);
                $table->string('currency')->default('ILS');
                $table->string('status')->default('completed'); // completed, pending, failed
                $table->text('payment_details')->nullable(); // JSON or details like phone, ref_no, note
                $table->text('items')->nullable(); // JSON array of subjects enrolled
                $table->string('receipt_path')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
