<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Stage;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_catalog_renders_as_pure_informational_directory()
    {
        $stage = Stage::create([
            'grade_level' => 122,
            'label_ar'    => 'الفرع العلمي - الثانوية العامة',
            'icon'        => '⚛️'
        ]);

        $subject = Subject::create([
            'stage_id'    => $stage->id,
            'name_ar'     => 'الفيزياء (علمي)',
            'subject_key' => 'physics_sci',
            'price_ils'   => 100,
            'description' => 'منهاج الفيزياء للتوجيهي'
        ]);

        $response = $this->get(route('courses.catalog'));

        $response->assertStatus(200);
        $response->assertSee('دليل المقررات والمناهج الدراسية');
        $response->assertSee('الفيزياء (علمي)');
        $response->assertSee('تفاصيل المنهاج');

        // Verify NO eCommerce or cart elements exist
        $response->assertDontSee('subject_ids[]');
        $response->assertDontSee('floatingCartBar');
        $response->assertDontSee('إتمام الدفع الفلسطيني');
        $response->assertDontSee('اختر مادتك الدراسية أو باقتك الوزارية الكاملة');
    }

    public function test_catalog_branch_filter()
    {
        $sciStage = Stage::create([
            'grade_level' => 122,
            'label_ar'    => 'الفرع العلمي',
            'icon'        => '⚛️'
        ]);

        $litStage = Stage::create([
            'grade_level' => 121,
            'label_ar'    => 'الفرع الأدبي',
            'icon'        => '📜'
        ]);

        Subject::create([
            'stage_id'    => $sciStage->id,
            'name_ar'     => 'الكيمياء (علمي)',
            'subject_key' => 'chemistry_sci',
            'price_ils'   => 100,
        ]);

        Subject::create([
            'stage_id'    => $litStage->id,
            'name_ar'     => 'التاريخ (أدبي)',
            'subject_key' => 'history_lit',
            'price_ils'   => 100,
        ]);

        $sciResponse = $this->get(route('courses.catalog', ['branch' => 'scientific']));
        $sciResponse->assertStatus(200);
        $sciResponse->assertSee('الكيمياء (علمي)');
        $sciResponse->assertDontSee('التاريخ (أدبي)');

        $litResponse = $this->get(route('courses.catalog', ['branch' => 'literary']));
        $litResponse->assertStatus(200);
        $litResponse->assertSee('التاريخ (أدبي)');
        $litResponse->assertDontSee('الكيمياء (علمي)');
    }
}
