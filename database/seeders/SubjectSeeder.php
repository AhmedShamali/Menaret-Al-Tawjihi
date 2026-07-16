<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stage;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            // --- المرحلة الإعدادية ---
            7 => [
                ['name' => 'اللغة العربية', 'key' => 'arabic_7', 'icon' => '📜', 'color' => '#a62626'],
                ['name' => 'الرياضيات', 'key' => 'math_7', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'العلوم والحياة', 'key' => 'science_7', 'icon' => '🧪', 'color' => '#059669'],
                ['name' => 'اللغة الإنجليزية', 'key' => 'english_7', 'icon' => '🔤', 'color' => '#d97706'],
                ['name' => 'التكنولوجيا', 'key' => 'tech_7', 'icon' => '💻', 'color' => '#475569'],
                ['name' => 'التربية الإسلامية', 'key' => 'islamic_7', 'icon' => '🕌', 'color' => '#10b981'],
            ],
            8 => [
                ['name' => 'اللغة العربية', 'key' => 'arabic_8', 'icon' => '📜', 'color' => '#a62626'],
                ['name' => 'الرياضيات', 'key' => 'math_8', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'العلوم والحياة', 'key' => 'science_8', 'icon' => '🧪', 'color' => '#059669'],
                ['name' => 'اللغة الإنجليزية', 'key' => 'english_8', 'icon' => '🔤', 'color' => '#d97706'],
                ['name' => 'التكنولوجيا', 'key' => 'tech_8', 'icon' => '💻', 'color' => '#475569'],
            ],
            9 => [
                ['name' => 'اللغة العربية', 'key' => 'arabic_9', 'icon' => '📜', 'color' => '#a62626'],
                ['name' => 'الرياضيات', 'key' => 'math_9', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'العلوم والحياة', 'key' => 'science_9', 'icon' => '🧪', 'color' => '#059669'],
                ['name' => 'اللغة الإنجليزية', 'key' => 'english_9', 'icon' => '🔤', 'color' => '#d97706'],
                ['name' => 'التكنولوجيا', 'key' => 'tech_9', 'icon' => '💻', 'color' => '#475569'],
            ],

            // --- المرحلة الثانوية ---
            10 => [
                ['name' => 'اللغة العربية', 'key' => 'arabic_10', 'icon' => '🖋️', 'color' => '#a62626'],
                ['name' => 'الرياضيات', 'key' => 'math_10', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'الفيزياء', 'key' => 'physics_10', 'icon' => '⚛️', 'color' => '#dc2626'],
                ['name' => 'الكيمياء', 'key' => 'chemistry_10', 'icon' => '⚗️', 'color' => '#0891b2'],
                ['name' => 'الأحياء', 'key' => 'biology_10', 'icon' => '🧬', 'color' => '#10b981'],
                ['name' => 'اللغة الإنجليزية', 'key' => 'english_10', 'icon' => '📖', 'color' => '#d97706'],
            ],
            11 => [ // الفرع العلمي كمثال
                ['name' => 'الرياضيات العلمي', 'key' => 'math_11_sci', 'icon' => '♾️', 'color' => '#1e3a8a'],
                ['name' => 'الفيزياء', 'key' => 'physics_11', 'icon' => '🚀', 'color' => '#dc2626'],
                ['name' => 'الكيمياء', 'key' => 'chemistry_11', 'icon' => '⚗️', 'color' => '#0891b2'],
                ['name' => 'الأحياء', 'key' => 'biology_11', 'icon' => '🔬', 'color' => '#059669'],
                ['name' => 'اللغة العربية', 'key' => 'arabic_11', 'icon' => '🖋️', 'color' => '#a62626'],
                ['name' => 'اللغة الإنجليزية', 'key' => 'english_11', 'icon' => '🔤', 'color' => '#d97706'],
            ],
            12 => [ // التوجيهي العلمي
                ['name' => 'الرياضيات (1)', 'key' => 'math_12_1', 'icon' => '📊', 'color' => '#1e40af'],
                ['name' => 'الرياضيات (2)', 'key' => 'math_12_2', 'icon' => '📈', 'color' => '#1e3a8a'],
                ['name' => 'الفيزياء', 'key' => 'physics_12', 'icon' => '⚛️', 'color' => '#991b1b'],
                ['name' => 'الكيمياء', 'key' => 'chemistry_12', 'icon' => '🧪', 'color' => '#0e7490'],
                ['name' => 'الأحياء', 'key' => 'biology_12', 'icon' => '🧬', 'color' => '#15803d'],
                ['name' => 'اللغة العربية', 'key' => 'arabic_12', 'icon' => '📜', 'color' => '#7f1d1d'],
                ['name' => 'اللغة الإنجليزية', 'key' => 'english_12', 'icon' => '📖', 'color' => '#b45309'],
                ['name' => 'التربية الإسلامية', 'key' => 'islamic_12', 'icon' => '🌙', 'color' => '#065f46'],
            ],
        ];

        foreach ($curriculum as $grade => $subjects) {
            $stage = Stage::where('grade_level', $grade)->first();

            if ($stage) {
                foreach ($subjects as $sub) {
                    Subject::updateOrCreate(
                        ['subject_key' => $sub['key'], 'stage_id' => $stage->id],
                        [
                            'name_ar' => $sub['name'],
                            'icon' => $sub['icon'],
                            'color' => $sub['color'],
                        ]
                    );
                }
            }
        }
    }
}
