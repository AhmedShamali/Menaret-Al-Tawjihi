<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stage;
use Illuminate\Support\Facades\DB;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إيقاف فحص المفاتيح الأجنبية بطريقة متوافقة مع SQLite و MySQL
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        Stage::truncate();

        // إعادة تفعيل فحص المفاتيح الأجنبية
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

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
            Stage::create([
                'grade_level' => $item['grade_level'],
                'label_ar'    => $item['label_ar'],
                'icon'        => $item['icon'],
            ]);
        }
    }
}
