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
        // تعطيل فحص المفاتيح الأجنبية مؤقتاً لتجنب أخطاء SQLite أثناء الـ Seeding
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        // 1. إنشاء الصفوف والمراحل أولاً
        $this->call(StageSeeder::class);

        // 2. إنشاء المواد ثانياً
        $this->call(SubjectSeeder::class);

        // 3. باقي الـ Seeders الخاصة بالمشروع
        $this->call([
            ActivitySeeder::class,
            DashboardSeeder::class,
            EducationalContentSeeder::class,
            ExamSeeder::class,
            ExamSubmissionSeeder::class,
            QuestionSeeder::class,
            SettingSeeder::class,
            StudentSeeder::class,
            SubmissionAnswerSeeder::class,
        ]);

        // 4. إنشاء حساب مدير النظام
        \App\Models\User::firstOrCreate(
            ['email' => 'ahmad@admin.ps'], // لتجنب تكرار إنشاء الحساب لو تم تشغيل السيدر أكثر من مرة
            [
                'name' => 'مدير النظام',
                'password' => bcrypt('123456789'),
                'role' => 'admin',
                'subject_id' => 1,
            ]
        );

        // إعادة تفعيل فحص المفاتيح الأجنبية لقاعدة بيانات SQLite
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }
}
