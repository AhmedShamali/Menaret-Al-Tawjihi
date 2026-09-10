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
            Stage::firstOrCreate(
                ['grade_level' => $item['grade_level']],
                [
                    'label_ar' => $item['label_ar'],
                    'icon'     => $item['icon'],
                ]
            );
        }
    }
}
