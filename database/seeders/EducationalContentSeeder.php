<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationalContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // // ابحث عن مادة الفيزياء (مثلاً)
        // $physics = \App\Models\Subject::where('subject_key', 'like', '%physics%')->first();

        // if ($physics) {
        //     // إضافة فيديوهات شارحة
        //     \App\Models\EducationalContent::create([
        //         'subject_id' => $physics->id,
        //         'title' => 'شرح الوحدة الأولى: الميكانيكا',
        //         'type' => 'video',
        //         'url_path' => 'dQw4w9WgXcQ', // YouTube Video ID
        //         'channel_name' => 'روافد التعليمية - فلسطين',
        //     ]);

        //     \App\Models\EducationalContent::create([
        //         'subject_id' => $physics->id,
        //         'title' => 'حل أسئلة الفصل الأول',
        //         'type' => 'video',
        //         'url_path' => 'v8vI-Xp_6XU',
        //         'channel_name' => 'قناة التعليم الفلسطيني',
        //     ]);

        //     // إضافة كتب وملفات
        //     \App\Models\EducationalContent::create([
        //         'subject_id' => $physics->id,
        //         'title' => 'كتاب الفيزياء الوزاري PDF',
        //         'type' => 'file',
        //         'url_path' => 'physics_book.pdf',
        //         'file_size' => '12.5 MB',
        //     ]);
        // }
    }
}
