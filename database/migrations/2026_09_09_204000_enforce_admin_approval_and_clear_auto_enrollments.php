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
        // 1. تعيين الحالة الافتراضية لحسابات الطلاب لتكون 'pending' (بانتظار موافقة المدير)
        try {
            Schema::table('students', function (Blueprint $table) {
                $table->string('status', 50)->default('pending')->change();
            });
        } catch (\Throwable $e) {}

        // 2. معالجة حسابات الطلاب التي كانت تأخذ 'published' وتحويلها إلى 'pending'
        try {
            DB::table('students')
                ->where('status', 'published')
                ->update(['status' => 'pending']);
        } catch (\Throwable $e) {}

        // 3. إلغاء أي اشتراكات تلقائية تم توليدها للمواد بدون دفع معتمد أو موافقة مسبقة من الإدارة
        try {
            // جلب معرفات الطلاب الذين لديهم دفعات مكتملة ومعتمدة
            $paidStudentIds = DB::table('payments')
                ->where('status', 'completed')
                ->pluck('student_id')
                ->toArray();

            // حذف أو تحويل الاشتراكات التلقائية المجانية (التي كانت تظهر 'مشترك حالياً' لجميع المواد)
            // إلى معلقة بانتظار موافقة المدير وسداد الرسوم
            if (Schema::hasTable('enrollments')) {
                DB::table('enrollments')
                    ->whereNotIn('student_id', $paidStudentIds)
                    ->where('payment_status', 'free')
                    ->delete(); // مسح التسجيل التلقائي الشامل لكي لا يظهر 'مشترك حالياً' على كل المواد
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
