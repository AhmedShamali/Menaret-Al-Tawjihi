<?php

namespace Tests\Feature;

use App\Models\EducationalContent;
use App\Models\Stage;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideographerGroupedLessonsTest extends TestCase
{
    use RefreshDatabase;

    protected User $videographer;
    protected Stage $stageScientific;
    protected Stage $stageLiterary;
    protected Stage $stageEntrepreneurship;
    protected Subject $subSci;
    protected Subject $subLit;
    protected Subject $subEnt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->videographer = User::create([
            'name'     => 'علي مصبح',
            'email'    => 'videographer_test@platform.ps',
            'password' => bcrypt('password123'),
            'role'     => 'videographer',
        ]);

        $this->stageScientific = Stage::create([
            'name'        => 'توجيهي علمي',
            'label_ar'    => 'الفرع العلمي - الثانوية العامة',
            'grade_level' => 12,
        ]);

        $this->stageLiterary = Stage::create([
            'name'        => 'توجيهي أدبي',
            'label_ar'    => 'الفرع الأدبي - الثانوية العامة',
            'grade_level' => 11,
        ]);

        $this->stageEntrepreneurship = Stage::create([
            'name'        => 'توجيهي ريادة',
            'label_ar'    => 'فرع الريادة والأعمال - الثانوية العامة',
            'grade_level' => 10,
        ]);

        $this->subSci = Subject::create([
            'stage_id'    => $this->stageScientific->id,
            'name_ar'     => 'اللغة العربية',
            'subject_key' => 'arabic_sci',
            'price_ils'   => 0,
        ]);

        $this->subLit = Subject::create([
            'stage_id'    => $this->stageLiterary->id,
            'name_ar'     => 'اللغة العربية (أدبي)',
            'subject_key' => 'arabic_lit',
            'price_ils'   => 0,
        ]);

        $this->subEnt = Subject::create([
            'stage_id'    => $this->stageEntrepreneurship->id,
            'name_ar'     => 'اللغة العربية',
            'subject_key' => 'arabic_ent',
            'price_ils'   => 0,
        ]);
    }

    public function test_library_groups_multi_branch_lesson_into_a_single_row_instead_of_duplicates()
    {
        $videoUrl = 'educational/videos/video_arabic_sarf.mp4';
        $title = 'الممنوع من الصرف (1) الأسئلة';

        // محاكاة إنشاء الدرس موزعاً على 3 فروع كما يحصل عند الرفع والتوزيع
        $c1 = EducationalContent::create([
            'subject_id'   => $this->subSci->id,
            'uploaded_by'  => $this->videographer->id,
            'title'        => $title,
            'type'         => 'video',
            'url_path'     => $videoUrl,
            'channel_name' => 'أحمد طلال علي مصبح',
            'file_size'    => '328.2 MB',
            'is_visible'   => true,
            'views_count'  => 5,
        ]);

        $c2 = EducationalContent::create([
            'subject_id'   => $this->subLit->id,
            'uploaded_by'  => $this->videographer->id,
            'title'        => $title,
            'type'         => 'video',
            'url_path'     => $videoUrl,
            'channel_name' => 'أحمد طلال علي مصبح',
            'file_size'    => '328.2 MB',
            'is_visible'   => true,
            'views_count'  => 10,
        ]);

        $c3 = EducationalContent::create([
            'subject_id'   => $this->subEnt->id,
            'uploaded_by'  => $this->videographer->id,
            'title'        => $title,
            'type'         => 'video',
            'url_path'     => $videoUrl,
            'channel_name' => 'أحمد طلال علي مصبح',
            'file_size'    => '328.2 MB',
            'is_visible'   => true,
            'views_count'  => 3,
        ]);

        // طلب صفحة سجل المحاضرات
        $response = $this->actingAs($this->videographer)->get(route('videographer.contents.index'));

        $response->assertOk();
        // التحقق من ظهور الدرس
        $response->assertSee('الممنوع من الصرف (1) الأسئلة');
        // التحقق من أن عدد الدروس الإجمالي المجمعة هو 1 وليس 3
        $contents = $response->viewData('contents');
        $this->assertEquals(1, $contents->total());

        // التحقق من أن السجل المجمع يحتوي على الفروع الثلاثة
        $firstLesson = $contents->first();
        $this->assertEquals(3, $firstLesson->branches_count);
        $this->assertEquals(18, $firstLesson->total_views);

        // التحقق من وجود نافذة المودال في الصفحة
        $response->assertSee('lessonDistributionModal');
        $response->assertSee('dist_modal_title');
        $response->assertSee('الأماكن والفروع الأكاديمية التي نزل فيها هذا الدرس');
    }

    public function test_single_branch_can_be_deleted_without_affecting_other_branches()
    {
        $videoUrl = 'educational/videos/video_test_branches.mp4';
        $title = 'درس تجريبي موزع';

        $cSci = EducationalContent::create([
            'subject_id'   => $this->subSci->id,
            'uploaded_by'  => $this->videographer->id,
            'title'        => $title,
            'type'         => 'video',
            'url_path'     => $videoUrl,
        ]);

        $cLit = EducationalContent::create([
            'subject_id'   => $this->subLit->id,
            'uploaded_by'  => $this->videographer->id,
            'title'        => $title,
            'type'         => 'video',
            'url_path'     => $videoUrl,
        ]);

        // حذف فرع العلمي فقط باستخدام scope=single
        $response = $this->actingAs($this->videographer)
            ->deleteJson(route('videographer.contents.destroy', ['id' => $cSci->id, 'scope' => 'single']));

        $response->assertOk();
        $this->assertDatabaseMissing('educational_contents', ['id' => $cSci->id]);
        $this->assertDatabaseHas('educational_contents', ['id' => $cLit->id]);
    }
}
