<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $dbDriver = config('database.default');

        // تعطيل فحص المفاتيح الأجنبية مؤقتاً بأمان تام (مع تجاوز القيود في بيئات السحاب)
        try {
            if ($dbDriver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } elseif ($dbDriver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
            } elseif ($dbDriver === 'pgsql') {
                DB::statement("SET session_replication_role = 'replica';");
            }
        } catch (\Throwable $e) {
            // في قواعد البيانات السحابية (مثل Render) التي تمنع صلاحيات السوبر يوزر، يتم المتابعة بأمان
        }

        // 1. المراحل والصفوف الدراسية أولاً (StageSeeder)
        $this->call(StageSeeder::class);

        // 2. إنشاء المستخدمين الأساسيين (مدير النظام ومعلم تجريبي)
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'ahmad@admin.ps'],
            [
                'name' => 'أحمد شمالي (مدير النظام)',
                'password' => bcrypt('123456789'),
                'role' => 'admin',
                'phone' => '0567897212',
                'major' => 'إدارة المنصة والإشراف الأكاديمي',
                'bio' => 'مشرف عام المنصة التعليمية الفلسطينية',
            ]
        );

        $teacher = \App\Models\User::firstOrCreate(
            ['email' => 'teacher@tawjihi.ps'],
            [
                'name' => 'أ. عصام الشريف',
                'password' => bcrypt('123456789'),
                'role' => 'teacher',
                'phone' => '0599000001',
                'major' => 'العلوم الفيزيائية والرياضيات',
                'bio' => 'معلم أول لمبحث الفيزياء للثانوية العامة بخبرة 15 عاماً',
            ]
        );

        // 3. إعدادات المنصة والهوية الرسمية وبوابات الدفع
        $this->call(SettingSeeder::class);

        // 4. إنشاء الطالب النموذجي
        $this->call(StudentSeeder::class);

        // 5. إنشاء وتحديث المواد الدراسية وربطها بالمراحل
        $this->call(SubjectSeeder::class);

        // 6. المحتوى التعليمي النموذجي والاختبارات التفاعلية للتوجيهي
        $this->call([
            EducationalContentSeeder::class,
            ExamSeeder::class,
        ]);

        // إعادة تفعيل فحص المفاتيح الأجنبية بأمان
        try {
            if ($dbDriver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } elseif ($dbDriver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            } elseif ($dbDriver === 'pgsql') {
                DB::statement("SET session_replication_role = 'origin';");
            }
        } catch (\Throwable $e) {
            // تجاوز الأخطاء بأمان
        }
    }
}
