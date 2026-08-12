<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stage;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // تم إزالة أوامر MySQL المتعارضة مع SQLite وتفريغ الجدول مباشرة
        Subject::truncate();

        $curriculum = [
            // --- المرحلة الإعدادية (7 - 9) ---
            7 => [
                ['name' => 'تربية إسلامية', 'key' => 'islamic_7', 'icon' => '🕌', 'color' => '#10b981'],
                ['name' => 'لغة عربية', 'key' => 'arabic_7', 'icon' => '📜', 'color' => '#a62626'],
                ['name' => 'رياضيات', 'key' => 'math_7', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'علوم عامة', 'key' => 'science_7', 'icon' => '🧪', 'color' => '#059669'],
                ['name' => 'دراسات اجتماعية', 'key' => 'social_7', 'icon' => '🌍', 'color' => '#d97706'],
                ['name' => 'تكنولوجيا', 'key' => 'tech_7', 'icon' => '💻', 'color' => '#475569'],
                ['name' => 'فنون وحرف', 'key' => 'arts_7', 'icon' => '🎨', 'color' => '#ec4899'],
                ['name' => 'لغة إنجليزية', 'key' => 'english_7', 'icon' => '🔤', 'color' => '#b45309'],
            ],
            8 => [
                ['name' => 'تربية إسلامية', 'key' => 'islamic_8', 'icon' => '🕌', 'color' => '#10b981'],
                ['name' => 'لغة عربية', 'key' => 'arabic_8', 'icon' => '📜', 'color' => '#a62626'],
                ['name' => 'رياضيات', 'key' => 'math_8', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'علوم عامة', 'key' => 'science_8', 'icon' => '🧪', 'color' => '#059669'],
                ['name' => 'دراسات اجتماعية', 'key' => 'social_8', 'icon' => '🌍', 'color' => '#d97706'],
                ['name' => 'تكنولوجيا', 'key' => 'tech_8', 'icon' => '💻', 'color' => '#475569'],
                ['name' => 'فنون وحرف', 'key' => 'arts_8', 'icon' => '🎨', 'color' => '#ec4899'],
                ['name' => 'لغة إنجليزية', 'key' => 'english_8', 'icon' => '🔤', 'color' => '#b45309'],
            ],
            9 => [
                ['name' => 'تربية إسلامية', 'key' => 'islamic_9', 'icon' => '🕌', 'color' => '#10b981'],
                ['name' => 'لغة عربية', 'key' => 'arabic_9', 'icon' => '📜', 'color' => '#a62626'],
                ['name' => 'رياضيات', 'key' => 'math_9', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'علوم عامة', 'key' => 'science_9', 'icon' => '🧪', 'color' => '#059669'],
                ['name' => 'دراسات اجتماعية', 'key' => 'social_9', 'icon' => '🌍', 'color' => '#d97706'],
                ['name' => 'تكنولوجيا', 'key' => 'tech_9', 'icon' => '💻', 'color' => '#475569'],
                ['name' => 'فنون وحرف', 'key' => 'arts_9', 'icon' => '🎨', 'color' => '#ec4899'],
                ['name' => 'لغة إنجليزية', 'key' => 'english_9', 'icon' => '🔤', 'color' => '#b45309'],
            ],

            // --- الصف العاشر ---
            10 => [
                ['name' => 'تربية إسلامية', 'key' => 'islamic_10', 'icon' => '🕌', 'color' => '#10b981'],
                ['name' => 'لغة عربية', 'key' => 'arabic_10', 'icon' => '🖋️', 'color' => '#a62626'],
                ['name' => 'رياضيات', 'key' => 'math_10', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'فيزياء', 'key' => 'physics_10', 'icon' => '⚛️', 'color' => '#dc2626'],
                ['name' => 'دراسات اجتماعية', 'key' => 'social_10', 'icon' => '🌍', 'color' => '#d97706'],
                ['name' => 'تكنولوجيا', 'key' => 'tech_10', 'icon' => '💻', 'color' => '#475569'],
                ['name' => 'فنون وحرف', 'key' => 'arts_10', 'icon' => '🎨', 'color' => '#ec4899'],
                ['name' => 'لغة إنجليزية', 'key' => 'english_10', 'icon' => '📖', 'color' => '#b45309'],
                ['name' => 'كيمياء', 'key' => 'chemistry_10', 'icon' => '⚗️', 'color' => '#0891b2'],
                ['name' => 'أحياء', 'key' => 'biology_10', 'icon' => '🧬', 'color' => '#059669'],
            ],

            // --- الحادي عشر أدبي (111) ---
            111 => [
                ['name' => 'تربية إسلامية', 'key' => 'islamic_11_lit', 'icon' => '🕌', 'color' => '#10b981'],
                ['name' => 'لغة عربية', 'key' => 'arabic_11_lit', 'icon' => '📜', 'color' => '#a62626'],
                ['name' => 'رياضيات', 'key' => 'math_11_lit', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'تاريخ', 'key' => 'history_11_lit', 'icon' => '🏛️', 'color' => '#b45309'],
                ['name' => 'تكنولوجيا', 'key' => 'tech_11_lit', 'icon' => '💻', 'color' => '#475569'],
                ['name' => 'جغرافيا', 'key' => 'geo_11_lit', 'icon' => '🗺️', 'color' => '#059669'],
                ['name' => 'لغة إنجليزية', 'key' => 'english_11_lit', 'icon' => '🔤', 'color' => '#d97706'],
                ['name' => 'ثقافة علمية', 'key' => 'sci_culture_11_lit', 'icon' => '💡', 'color' => '#0891b2'],
            ],

            // --- الحادي عشر علمي (112) ---
            112 => [
                ['name' => 'تربية إسلامية', 'key' => 'islamic_11_sci', 'icon' => '🕌', 'color' => '#10b981'],
                ['name' => 'لغة عربية', 'key' => 'arabic_11_sci', 'icon' => '📜', 'color' => '#a62626'],
                ['name' => 'رياضيات', 'key' => 'math_11_sci', 'icon' => '♾️', 'color' => '#1e3a8a'],
                ['name' => 'فيزياء', 'key' => 'physics_11_sci', 'icon' => '🚀', 'color' => '#dc2626'],
                ['name' => 'كيمياء', 'key' => 'chemistry_11_sci', 'icon' => '⚗️', 'color' => '#0891b2'],
                ['name' => 'تكنولوجيا', 'key' => 'tech_11_sci', 'icon' => '💻', 'color' => '#475569'],
                ['name' => 'أحياء', 'key' => 'biology_11_sci', 'icon' => '🔬', 'color' => '#059669'],
                ['name' => 'لغة إنجليزية', 'key' => 'english_11_sci', 'icon' => '🔤', 'color' => '#d97706'],
            ],

            // --- الثاني عشر أدبي - توجيهي (121) ---
            121 => [
                ['name' => 'تربية إسلامية', 'key' => 'islamic_12_lit', 'icon' => '🌙', 'color' => '#065f46'],
                ['name' => 'لغة عربية', 'key' => 'arabic_12_lit', 'icon' => '📜', 'color' => '#7f1d1d'],
                ['name' => 'رياضيات', 'key' => 'math_12_lit', 'icon' => '📐', 'color' => '#2563eb'],
                ['name' => 'تاريخ', 'key' => 'history_12_lit', 'icon' => '🏛️', 'color' => '#b45309'],
                ['name' => 'تكنولوجيا', 'key' => 'tech_12_lit', 'icon' => '💻', 'color' => '#475569'],
                ['name' => 'جغرافيا', 'key' => 'geo_12_lit', 'icon' => '🗺️', 'color' => '#059669'],
                ['name' => 'لغة إنجليزية', 'key' => 'english_12_lit', 'icon' => '📖', 'color' => '#b45309'],
                ['name' => 'ثقافة علمية', 'key' => 'sci_culture_12_lit', 'icon' => '💡', 'color' => '#0891b2'],
            ],

            // --- الثاني عشر علمي - توجيهي (122) ---
            122 => [
                ['name' => 'تربية إسلامية', 'key' => 'islamic_12_sci', 'icon' => '🌙', 'color' => '#065f46'],
                ['name' => 'لغة عربية', 'key' => 'arabic_12_sci', 'icon' => '📜', 'color' => '#7f1d1d'],
                ['name' => 'رياضيات', 'key' => 'math_12_sci', 'icon' => '📊', 'color' => '#1e40af'],
                ['name' => 'فيزياء', 'key' => 'physics_12_sci', 'icon' => '⚛️', 'color' => '#991b1b'],
                ['name' => 'كيمياء', 'key' => 'chemistry_12_sci', 'icon' => '🧪', 'color' => '#0e7490'],
                ['name' => 'تكنولوجيا', 'key' => 'tech_12_sci', 'icon' => '💻', 'color' => '#475569'],
                ['name' => 'أحياء', 'key' => 'biology_12_sci', 'icon' => '🧬', 'color' => '#15803d'],
                ['name' => 'لغة إنجليزية', 'key' => 'english_12_sci', 'icon' => '📖', 'color' => '#b45309'],
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
                            'icon'    => $sub['icon'],
                            'color'   => $sub['color'],
                        ]
                    );
                }
            }
        }
    }
}
