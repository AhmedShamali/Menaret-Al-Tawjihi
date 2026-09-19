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
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_status_check');
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_gender_check');
            } catch (\Throwable $e) {}

           
        }
    }
}
