<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stage;

class StageSeeder extends Seeder
{
    public function run(): void
    {
        $tawjihiBranches = [
            [
                'grade_level' => 122,
                'label_ar'    => 'الفرع العلمي - الثانوية العامة (توجيهي علمي)',
                'icon'        => '⚛️',
            ],
            [
                'grade_level' => 121,
                'label_ar'    => 'الفرع الأدبي - الثانوية العامة (توجيهي أدبي)',
                'icon'        => '📜',
            ],
            [
                'grade_level' => 123,
                'label_ar'    => 'فرع الريادة والأعمال - الثانوية العامة (توجيهي ريادة)',
                'icon'        => '💼',
            ],
        ];

        foreach ($tawjihiBranches as $item) {
            Stage::updateOrCreate(
                ['grade_level' => $item['grade_level']],
                [
                    'label_ar' => $item['label_ar'],
                    'icon'     => $item['icon'],
                ]
            );
        }

        // حذف المراحل والصفوف غير التوجيهي (مثل السابع، الثامن، التاسع، العاشر)
        try {
            $defaultStage = Stage::where('grade_level', 122)->first();
            $oldStages = Stage::whereNotIn('grade_level', [121, 122, 123])->get();

            if ($oldStages->isNotEmpty() && $defaultStage) {
                $oldIds = $oldStages->pluck('id')->toArray();

                // فك القيود وإعادة ربط الطلاب والمعلمين بالفرع العلمي
                \App\Models\Student::whereIn('stage_id', $oldIds)->update(['stage_id' => $defaultStage->id]);
                \App\Models\User::whereIn('stage_id', $oldIds)->update(['stage_id' => $defaultStage->id]);
                \App\Models\Exam::whereIn('stage_id', $oldIds)->update(['stage_id' => $defaultStage->id]);
                \App\Models\Subject::whereIn('stage_id', $oldIds)->delete();

                // حذف المراحل القديمة نهائياً
                Stage::whereIn('id', $oldIds)->delete();
            }
        } catch (\Throwable $e) {
            // تجاوز الأخطاء بأمان
        }
    }
}
