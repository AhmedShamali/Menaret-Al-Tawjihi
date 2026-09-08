<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stage = \App\Models\Stage::where('grade_level', 122)->first() ?? \App\Models\Stage::first();

        if ($stage) {
            \App\Models\Student::updateOrCreate(
                ['email' => 'student@tawjihi.ps'],
                [
                    'name_ar' => 'محمد أحمد خليل',
                    'name_en' => 'Mohammed Khalil',
                    'nid' => '405123456',
                    'password' => bcrypt('123456789'),
                    'age' => 18,
                    'gender' => 'ذكر',
                    'phone' => '0599123456',
                    'whatsapp' => '0599123456',
                    'stage_id' => $stage->id,
                    'status' => 'active',
                    'streak_count' => 7,
                    'total_points' => 120,
                    'last_activity_date' => now()->toDateString(),
                ]
            );
        }
    }
}
