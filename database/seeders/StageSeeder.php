<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stage;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تم إزالة Stage::truncate() لحماية البيانات الحالية من الحذف

        $data = [
            ['grade_level' => 7,   'label_ar' => 'الصف السابع', 'icon' => '📚'],
            ['grade_level' => 8,   'label_ar' => 'الصف الثامن', 'icon' => '📖'],
            ['grade_level' => 9,   'label_ar' => 'الصف التاسع', 'icon' => '🖋️'],
            ['grade_level' => 10,  'label_ar' => 'الصف العاشر', 'icon' => '🧪'],

            // الفرع الأدبي
            ['grade_level' => 111, 'label_ar' => 'الصف الحادي عشر - أدبي', 'icon' => '📜'],
            ['grade_level' => 121, 'label_ar' => 'الصف الثاني عشر - أدبي', 'icon' => '🎓'],

            // الفرع العلمي
            ['grade_level' => 112, 'label_ar' => 'الصف الحادي عشر - علمي', 'icon' => '📐'],
            ['grade_level' => 122, 'label_ar' => 'الصف الثاني عشر - علمي', 'icon' => '⚛️'],
        ];

        foreach ($data as $item) {
            // استخدام updateOrCreate للبحث برقم المستوى وتحديثه أو إضافته بأمان دون حذف البقية
            Stage::updateOrCreate(
                ['grade_level' => $item['grade_level']], // الشرط (الابحث عن الصف بهذا الرقم)
                [
                    'label_ar' => $item['label_ar'],
                    'icon'     => $item['icon'],
                ]
            );
        }
    }
}
