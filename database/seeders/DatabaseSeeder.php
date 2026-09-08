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

        // تعطيل فحص المفاتيح الأجنبية مؤقتاً حسب نوع السيرفر/قاعدة البيانات
        if ($dbDriver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } elseif ($dbDriver === 'pgsql') {
            DB::statement('SET session_replication_role = \'replica\';');
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
            PastExamSeeder::class,
            FlashcardSeeder::class,
        ]);

        // 4. إنشاء حساب مدير النظام (آمن ولا يتكرر ولا يحذف القديم)
        \App\Models\User::firstOrCreate(
            ['email' => 'ahmad@admin.ps'],
            [
                'name' => 'مدير النظام',
                'password' => bcrypt('123456789'),
                'role' => 'admin',
                'subject_id' => 1,
            ]
        );

        // إعادة تفعيل فحص المفاتيح الأجنبية
        if ($dbDriver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } elseif ($dbDriver === 'pgsql') {
            DB::statement('SET session_replication_role = \'origin\';');
        }
    }
}
