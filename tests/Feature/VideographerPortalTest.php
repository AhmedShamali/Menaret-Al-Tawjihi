<?php

namespace Tests\Feature;

use App\Models\EducationalContent;
use App\Models\Stage;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VideographerPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Support\SchemaHealer::heal();
    }

    /**
     * اختبار تسجيل الدخول كمصور وإعادة التوجيه إلى لوحة تحكم المصور
     */
    public function test_videographer_can_login_and_is_redirected_to_videographer_dashboard()
    {
        $videographer = User::create([
            'name'     => 'مصور المنصة المعتمد',
            'email'    => 'cameraman@jesr.ps',
            'password' => Hash::make('secret123'),
            'role'     => 'videographer',
        ]);

        $response = $this->post('/login', [
            'email'    => 'cameraman@jesr.ps',
            'password' => 'secret123',
            'role'     => 'videographer',
        ]);

        $response->assertRedirect(route('videographer.dashboard'));
        $this->assertAuthenticatedAs($videographer);
    }

    /**
     * اختبار منع الطلاب والمعلمين من الدخول إلى لوحة تحكم واستوديو المصور
     */
    public function test_students_and_unauthorized_users_are_forbidden_from_videographer_routes()
    {
        $stage = Stage::create([
            'label_ar'    => 'الفرع العلمي',
            'grade_level' => 12,
        ]);

        $teacher = User::create([
            'name'     => 'أستاذ المادة',
            'email'    => 'teacher.forbidden@jesr.ps',
            'password' => Hash::make('secret123'),
            'role'     => 'teacher',
        ]);
        $response = $this->actingAs($teacher)->get(route('videographer.dashboard'));
        $response->assertStatus(403);

        $student = Student::create([
            'name_ar'  => 'طالب تجربة',
            'name_en'  => 'Test Student',
            'email'    => 'student.cam@jesr.ps',
            'password' => Hash::make('secret123'),
            'nid'      => '998877665',
            'phone'    => '0599112233',
            'status'   => 'active',
            'age'      => 18,
            'gender'   => 'male',
            'stage_id' => $stage->id,
        ]);
        $responseStudent = $this->actingAs($student, 'student')->get(route('videographer.dashboard'));
        $responseStudent->assertStatus(403);
    }

    /**
     * اختبار تصفح لوحة تحكم واستوديو ومكتبة المصور بنجاح
     */
    public function test_videographer_can_access_dashboard_create_studio_and_library()
    {
        $videographer = User::create([
            'name'     => 'مصور الاستوديو الأكاديمي',
            'email'    => 'studio.cam@jesr.ps',
            'password' => Hash::make('secret123'),
            'role'     => 'videographer',
        ]);

        $resDashboard = $this->actingAs($videographer)->get(route('videographer.dashboard'));
        $resDashboard->assertStatus(200);
        $resDashboard->assertSee('استوديو الإنتاج وتصوير المحاضرات');

        $resCreate = $this->actingAs($videographer)->get(route('videographer.contents.create'));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('رفع وتوزيع محاضرة مصورة على الفروع والمواد');

        $resIndex = $this->actingAs($videographer)->get(route('videographer.contents.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('مكتبة وسجل المحاضرات');
    }

    /**
     * الاختبار الجوهري: رفع محاضرة واحدة وتوزيعها تلقائياً على فروع متعددة (العلمي والأدبي)
     * بحيث تظهر تلقائياً لمعلمي وطلاب كلا الفرعين فوراً دون تكرار الرفع
     */
    public function test_multi_branch_auto_distribution_engine()
    {
        $videographer = User::create([
            'name'     => 'المصور أحمد الشملي',
            'email'    => 'ahmed.cam@jesr.ps',
            'password' => Hash::make('secret123'),
            'role'     => 'videographer',
        ]);

        // 1. إنشاء فرعين أكاديميين (علمي وأدبي)
        $stageScientific = Stage::create([
            'label_ar'    => 'الفرع العلمي',
            'grade_level' => 12,
        ]);

        $stageLiterary = Stage::create([
            'label_ar'    => 'الفرع الأدبي',
            'grade_level' => 11,
        ]);

        // 2. إنشاء مادة اللغة الإنجليزية في الفرعين
        $subScientificEng = Subject::create([
            'subject_key' => 'eng_scientific',
            'stage_id'    => $stageScientific->id,
            'name'        => 'English Scientific',
            'name_ar'     => 'اللغة الإنجليزية',
        ]);

        $subLiteraryEng = Subject::create([
            'subject_key' => 'eng_literary',
            'stage_id'    => $stageLiterary->id,
            'name'        => 'English Literary',
            'name_ar'     => 'اللغة الإنجليزية',
        ]);

        // 3. المصور يرفع فيديو محاضرة إنجليزي مع تحديد الفرعين معاً
        $response = $this->actingAs($videographer)->post(route('videographer.contents.store'), [
            'title'         => 'شرح قواعد الأزمنة Unit 1 - Tenses',
            'stage_ids'     => [$stageScientific->id, $stageLiterary->id],
            'common_name'   => 'اللغة الإنجليزية',
            'video_url'     => 'https://www.youtube.com/watch?v=sample123',
            'channel_name'  => 'استوديو التصوير الميداني',
            'order'         => 1,
            'target_region' => 'all',
        ]);

        $response->assertRedirect(route('videographer.contents.index'));
        $response->assertSessionHas('success');

        // 4. التحقق من إنشاء سجلين تلقائياً (سجل لكل فرع للمادة المطابقة)
        $this->assertDatabaseHas('educational_contents', [
            'subject_id'   => $subScientificEng->id,
            'uploaded_by'  => $videographer->id,
            'title'        => 'شرح قواعد الأزمنة Unit 1 - Tenses',
            'url_path'     => 'https://www.youtube.com/watch?v=sample123',
            'channel_name' => 'استوديو التصوير الميداني',
        ]);

        $this->assertDatabaseHas('educational_contents', [
            'subject_id'   => $subLiteraryEng->id,
            'uploaded_by'  => $videographer->id,
            'title'        => 'شرح قواعد الأزمنة Unit 1 - Tenses',
            'url_path'     => 'https://www.youtube.com/watch?v=sample123',
            'channel_name' => 'استوديو التصوير الميداني',
        ]);

        $this->assertEquals(2, EducationalContent::where('uploaded_by', $videographer->id)->count());

        // 5. التحقق من أن معلم مادة العلمي يرى المحتوى فوراً في مادته
        $contentsScientific = EducationalContent::where('subject_id', $subScientificEng->id)->get();
        $this->assertCount(1, $contentsScientific);
        $this->assertEquals('شرح قواعد الأزمنة Unit 1 - Tenses', $contentsScientific->first()->title);

        // 6. التحقق من أن طالب مادة الأدبي يرى المحتوى فوراً في مادته
        $contentsLiterary = EducationalContent::where('subject_id', $subLiteraryEng->id)->get();
        $this->assertCount(1, $contentsLiterary);
        $this->assertEquals('شرح قواعد الأزمنة Unit 1 - Tenses', $contentsLiterary->first()->title);
    }

    /**
     * اختبار حذف المصور للمحاضرة من مكتبته
     */
    public function test_videographer_can_delete_their_uploaded_content()
    {
        $videographer = User::create([
            'name'     => 'مصور للحذف',
            'email'    => 'del.cam@jesr.ps',
            'password' => Hash::make('secret123'),
            'role'     => 'videographer',
        ]);
        $stage = Stage::create(['label_ar' => 'الصناعي', 'grade_level' => 10]);
        $subject = Subject::create([
            'subject_key' => 'chemistry_ind',
            'stage_id'    => $stage->id,
            'name'        => 'Chemistry',
            'name_ar'     => 'كيمياء'
        ]);

        $content = EducationalContent::create([
            'subject_id'  => $subject->id,
            'uploaded_by' => $videographer->id,
            'title'       => 'درس للتجربة',
            'type'        => 'video',
            'url_path'    => 'https://youtube.com/sample',
        ]);

        $this->assertDatabaseHas('educational_contents', ['id' => $content->id]);

        $response = $this->actingAs($videographer)->delete(route('videographer.contents.destroy', $content->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('educational_contents', ['id' => $content->id]);
    }

    /**
     * اختبار قيام المدير العام بإنشاء حساب مصور من لوحة الإدارة المركزية
     */
    public function test_admin_can_create_a_videographer_account()
    {
        $admin = User::create([
            'name'     => 'مدير عام',
            'email'    => 'admin.createcam@jesr.ps',
            'password' => Hash::make('secret123'),
            'role'     => 'admin',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.teachers.store'), [
            'name'     => 'المصور الأكاديمي مصطفى',
            'email'    => 'mustafa.cam@jesr.ps',
            'password' => 'Pass@123456',
            'role'     => 'videographer',
            'phone'    => '0599123456',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'title'   => 'تم إنشاء وتفعيل حساب المصور بنجاح ✅',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'mustafa.cam@jesr.ps',
            'role'  => 'videographer',
            'name'  => 'المصور الأكاديمي مصطفى',
        ]);
    }
}
