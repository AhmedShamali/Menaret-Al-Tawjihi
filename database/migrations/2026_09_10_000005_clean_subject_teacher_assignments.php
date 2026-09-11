<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // جلب معرّفات جميع المستخدمين الذين هم مدراء نظام وليسوا معلمين
            $adminIds = User::where('role', 'admin')->pluck('id')->toArray();

            if (!empty($adminIds)) {
                // إزالة ارتباط مدير النظام كمعلّم لجميع المواد الدراسية
                DB::table('subjects')
                    ->whereIn('user_id', $adminIds)
                    ->update(['user_id' => null]);

                if (Schema::hasColumn('subjects', 'teacher_id')) {
                    DB::table('subjects')
                        ->whereIn('teacher_id', $adminIds)
                        ->update(['teacher_id' => null]);
                }
            }
        } catch (\Throwable $e) {
            // في حال وجود أي قيود يتم التجاوز بأمان
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
