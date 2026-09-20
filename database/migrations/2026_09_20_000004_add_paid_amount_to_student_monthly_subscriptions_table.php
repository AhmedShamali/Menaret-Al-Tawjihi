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
        if (Schema::hasTable('student_monthly_subscriptions')) {
            Schema::table('student_monthly_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('student_monthly_subscriptions', 'paid_amount')) {
                    $table->decimal('paid_amount', 10, 2)->default(0.00)->after('amount');
                }
            });

            // تحديث السجلات السابقة المسددة ليكون المبلغ المدفوع مساوياً لمبلغ الاشتراك
            try {
                DB::table('student_monthly_subscriptions')
                    ->where('status', 'paid')
                    ->where('paid_amount', '<=', 0)
                    ->update([
                        'paid_amount' => DB::raw('amount'),
                    ]);
            } catch (\Throwable $e) {
                // تجاوز إذا لم تكن هناك بيانات بعد
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_monthly_subscriptions')) {
            Schema::table('student_monthly_subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('student_monthly_subscriptions', 'paid_amount')) {
                    $table->dropColumn('paid_amount');
                }
            });
        }
    }
};
