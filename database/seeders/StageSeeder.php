<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $data = [
                ['grade_level' => 7,  'label_ar' => 'الصف السابع', 'icon' => '📚'],
                ['grade_level' => 8,  'label_ar' => 'الصف الثامن', 'icon' => '📖'],
                ['grade_level' => 9,  'label_ar' => 'الصف التاسع', 'icon' => '🖋️'],
                ['grade_level' => 10, 'label_ar' => 'الصف العاشر', 'icon' => '🧪'],
                ['grade_level' => 11, 'label_ar' => 'الصف الحادي عشر', 'icon' => '📐'],
                ['grade_level' => 12, 'label_ar' => 'الصف الثاني عشر (توجيهي)', 'icon' => '🎓'],
            ];

            foreach ($data as $item) {
                \App\Models\Stage::create($item);
            }
        }
    }

