<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stage;
use App\Models\Subject;

class StageSeeder extends Seeder
{
    /**
     * تشغيل سيدر فروع التوجيهي (الثانوية العامة فقط)
     */
    public function run(): void
    {
        // حذف أي صفوف قديمة لا تنتمي للثانوية العامة (مثل الصف السابع، الثامن، التاسع، العاشر)
        try {
            $nonTawjihiStages = Stage::where('grade_level', '<', 120)->get();
            foreach ($nonTawjihiStages as $oldStage) {
                Subject::where('stage_id', $oldStage->id)->delete();
                $oldStage->delete();
            }
        } catch (\Throwable $e) {
            // تخطي في حال القيود
        }

        // فروع الثانوية العامة الفلسطينية المعتمدة
        $tawjihiBranches = [
            [
                'grade_level' => 122,
                'label_ar'    => 'الثانوية العامة - الفرع العلمي',
                'icon'        => '⚛️',
            ],
            [
                'grade_level' => 121,
                'label_ar'    => 'الثانوية العامة - الفرع الأدبي',
                'icon'        => '📜',
            ],
            [
                'grade_level' => 123,
                'label_ar'    => 'الثانوية العامة - فرع الريادة والأعمال',
                'icon'        => '💼',
            ],
        ];

        foreach ($tawjihiBranches as $branch) {
            Stage::updateOrCreate(
                ['grade_level' => $branch['grade_level']],
                [
                    'label_ar' => $branch['label_ar'],
                    'icon'     => $branch['icon'],
                ]
            );
        }
    }
}
