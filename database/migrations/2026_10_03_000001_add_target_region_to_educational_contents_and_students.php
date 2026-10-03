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
        if (Schema::hasTable('educational_contents')) {
            Schema::table('educational_contents', function (Blueprint $table) {
                if (!Schema::hasColumn('educational_contents', 'target_region')) {
                    $table->string('target_region', 20)->default('all')->after('type');
                }
            });
        }

        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'region')) {
                    $table->string('region', 20)->nullable()->after('city');
                }
            });

            // ترحيل وتصنيف بيانات الطلبة الحاليين تلقائياً بناءً على حقل المدينة
            try {
                $students = DB::table('students')->whereNull('region')->orWhere('region', '')->get();
                $gazaKeywords = ['غزة', 'شمال غزة', 'خان يونس', 'رفح', 'دير البلح', 'الوسطى', 'جباليا', 'بيت لاهيا', 'بيت حانون'];

                foreach ($students as $student) {
                    $city = trim($student->city ?? '');
                    if (empty($city)) {
                        continue;
                    }

                    $isGaza = false;
                    foreach ($gazaKeywords as $kw) {
                        if (mb_strpos($city, $kw) !== false) {
                            $isGaza = true;
                            break;
                        }
                    }

                    $region = $isGaza ? 'gaza' : 'west_bank';
                    DB::table('students')->where('id', $student->id)->update(['region' => $region]);
                }
            } catch (\Throwable $e) {
                // تجاوز الخطأ في حال كانت البيئة لا تسمح بالتحديث الفوري
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('educational_contents')) {
            Schema::table('educational_contents', function (Blueprint $table) {
                if (Schema::hasColumn('educational_contents', 'target_region')) {
                    $table->dropColumn('target_region');
                }
            });
        }

        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (Schema::hasColumn('students', 'region')) {
                    $table->dropColumn('region');
                }
            });
        }
    }
};
