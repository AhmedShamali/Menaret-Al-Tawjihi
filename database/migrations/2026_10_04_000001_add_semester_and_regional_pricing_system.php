<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. إضافة تسعيرات الفصول للضفة الغربية وقطاع غزة في جدول المواد
        Schema::table('subjects', function (Blueprint $table) {
            // تسعيرة الضفة الغربية والقدس
            if (!Schema::hasColumn('subjects', 'price_term_1')) {
                $table->decimal('price_term_1', 8, 2)->nullable()->after('color');
            }
            if (!Schema::hasColumn('subjects', 'price_term_2')) {
                $table->decimal('price_term_2', 8, 2)->nullable()->after('price_term_1');
            }
            if (!Schema::hasColumn('subjects', 'price_full_year')) {
                $table->decimal('price_full_year', 8, 2)->nullable()->after('price_term_2');
            }

            // تسعيرة قطاع غزة
            if (!Schema::hasColumn('subjects', 'price_term_1_gaza')) {
                $table->decimal('price_term_1_gaza', 8, 2)->nullable()->after('price_full_year');
            }
            if (!Schema::hasColumn('subjects', 'price_term_2_gaza')) {
                $table->decimal('price_term_2_gaza', 8, 2)->nullable()->after('price_term_1_gaza');
            }
            if (!Schema::hasColumn('subjects', 'price_full_year_gaza')) {
                $table->decimal('price_full_year_gaza', 8, 2)->nullable()->after('price_term_2_gaza');
            }
        });

        // تهيئة أسعار الفصول الأولية للمواد الحالية استناداً إلى سعر المادة الأساسي price_ils
        try {
            $subjects = DB::table('subjects')->get();
            foreach ($subjects as $sub) {
                $basePrice = (float)($sub->price_ils ?? 150.00);
                $termPrice = round($basePrice / 2, 2);
                if ($termPrice <= 0) $termPrice = $basePrice;

                $gazaTerm = round($termPrice * 0.6, 2); // تسعيرة مخفضة لغزة كمبدئية
                $gazaFull = round($basePrice * 0.6, 2);

                DB::table('subjects')->where('id', $sub->id)->update([
                    'price_term_1'           => $sub->price_term_1 ?? $termPrice,
                    'price_term_2'           => $sub->price_term_2 ?? $termPrice,
                    'price_full_year'        => $sub->price_full_year ?? $basePrice,
                    'price_term_1_gaza'      => $sub->price_term_1_gaza ?? $gazaTerm,
                    'price_term_2_gaza'      => $sub->price_term_2_gaza ?? $gazaTerm,
                    'price_full_year_gaza'   => $sub->price_full_year_gaza ?? $gazaFull,
                ]);
            }
        } catch (\Throwable $e) {}

        // 2. تحديث جدول تسجيلات المواد للطلاب enrollments
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'semester')) {
                $table->string('semester', 16)->default('both')->after('access_mode'); // term_1, term_2, both
            }
            if (!Schema::hasColumn('enrollments', 'region_applied')) {
                $table->string('region_applied', 20)->default('west_bank')->after('semester'); // west_bank, gaza
            }
            if (!Schema::hasColumn('enrollments', 'fee_amount')) {
                $table->decimal('fee_amount', 8, 2)->default(0.00)->after('region_applied');
            }
            if (!Schema::hasColumn('enrollments', 'paid_amount')) {
                $table->decimal('paid_amount', 8, 2)->default(0.00)->after('fee_amount');
            }
        });

        // 3. جدول الاشتراكات والذمم الفصلية للطلاب
        if (!Schema::hasTable('student_semester_subscriptions')) {
            Schema::create('student_semester_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
                $table->string('academic_year', 16)->default('2026-2027');
                $table->string('semester', 16)->default('term_1'); // term_1, term_2, both
                $table->string('region', 20)->default('west_bank'); // west_bank, gaza
                $table->decimal('amount', 8, 2)->default(0.00);
                $table->decimal('paid_amount', 8, 2)->default(0.00);
                $table->string('status', 20)->default('unpaid'); // paid, partial, pending, unpaid, waived
                $table->boolean('is_manual')->default(false);
                $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['student_id', 'academic_year', 'semester'], 'idx_student_year_semester');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_semester_subscriptions');

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['semester', 'region_applied', 'fee_amount', 'paid_amount']);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn([
                'price_term_1', 'price_term_2', 'price_full_year',
                'price_term_1_gaza', 'price_term_2_gaza', 'price_full_year_gaza'
            ]);
        });
    }
};
