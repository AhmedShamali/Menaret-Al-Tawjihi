<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إنشاء الصفوف أولاً (الصف السابع، الثامن...)
        $this->call(StageSeeder::class);

        // 2. إنشاء المواد ثانياً (رياضيات، فيزياء...) لكي تأخذ IDs
        $this->call(SubjectSeeder::class);

        // 3. الآن وبعد أن أصبحت المواد موجودة، ننشئ المدرس ونربطه بها
        \App\Models\User::create([
            'name' => 'مدير النظام',
            'email' => 'ahmad@admin.ps',
            'password' => bcrypt('123456789'),
            'role' => 'admin',
            'subject_id' => 1,
        ]);
    }
}
