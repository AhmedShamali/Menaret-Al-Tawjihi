<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stage;
use App\Models\Subject;
use App\Models\EducationalContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EducationalContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected User $admin;
    protected Stage $stage;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->stage = Stage::create([
            'grade_level' => 12,
            'label_ar'    => 'الثانوية العامة - الفرع العلمي',
            'icon'        => '📐',
        ]);

        $this->subject = Subject::create([
            'stage_id'    => $this->stage->id,
            'name_ar'     => 'الرياضيات العلمية',
            'subject_key' => 'math_scientific',
            'price_ils'   => 120,
        ]);

        $this->teacher = User::create([
            'name'       => 'Teacher User',
            'email'      => 'teacher_test@platform.ps',
            'password'   => bcrypt('password123'),
            'role'       => 'teacher',
            'subject_id' => $this->subject->id,
            'is_approved'=> 1,
        ]);

        $this->admin = User::create([
            'name'       => 'Admin User',
            'email'      => 'admin_test@platform.ps',
            'password'   => bcrypt('password123'),
            'role'       => 'admin',
            'is_approved'=> 1,
        ]);
    }

    public function test_teacher_can_store_video_content()
    {
        $response = $this->actingAs($this->teacher)->postJson(route('teacher.educational_contents.store'), [
            'subject_id'   => $this->subject->id,
            'title'        => 'شرح درس التكامل بالتعويض',
            'type'         => 'video',
            'video_url'    => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'channel_name' => 'أ. حسام العلمي',
            'order'        => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['icon' => 'success']);

        $this->assertDatabaseHas('educational_contents', [
            'title'      => 'شرح درس التكامل بالتعويض',
            'subject_id' => $this->subject->id,
            'type'       => 'video',
        ]);
    }

    public function test_teacher_can_store_file_or_dossier_content()
    {
        $fakePdf = UploadedFile::fake()->create('dossier_unit_1.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->teacher)->postJson(route('teacher.educational_contents.store'), [
            'subject_id'      => $this->subject->id,
            'title'           => 'دوسية التفاضل والتكامل الشاملة 2026',
            'type'            => 'file',
            'file_upload_pdf' => $fakePdf,
            'channel_name'    => 'دوسية المنهاج الشاملة',
            'order'           => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['icon' => 'success']);

        $this->assertDatabaseHas('educational_contents', [
            'title'      => 'دوسية التفاضل والتكامل الشاملة 2026',
            'subject_id' => $this->subject->id,
            'type'       => 'file',
        ]);
    }

    public function test_teacher_can_store_both_video_and_file_without_check_violation()
    {
        $fakePdf = UploadedFile::fake()->create('worksheet.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->teacher)->postJson(route('teacher.educational_contents.store'), [
            'subject_id'      => $this->subject->id,
            'title'           => 'الدرس الأول: المعدلات المرتبطة بالزمن (فيديو + ورقة عمل)',
            'type'            => 'both',
            'video_url'       => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'file_upload_pdf' => $fakePdf,
            'channel_name'    => 'شرح ومرفق',
            'order'           => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['icon' => 'success']);

        $this->assertDatabaseHas('educational_contents', [
            'title'      => 'الدرس الأول: المعدلات المرتبطة بالزمن (فيديو + ورقة عمل)',
            'subject_id' => $this->subject->id,
        ]);
    }

    public function test_teacher_videos_view_renders_correctly()
    {
        EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'فيديو تجريبي للاختبار',
            'type'       => 'video',
            'url_path'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order'      => 1,
            'is_visible' => 1,
        ]);

        $response = $this->actingAs($this->teacher)->get(route('teacher.videos'));

        $response->assertStatus(200);
        $response->assertSee('إدارة ورفع الفيديوهات والشروحات المرئية');
        $response->assertSee('فيديو تجريبي للاختبار');
    }

    public function test_teacher_files_view_renders_correctly()
    {
        EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'ملزمة أسئلة سنوات سابقة',
            'type'       => 'file',
            'pdf_path'   => 'educational/files/test.pdf',
            'order'      => 1,
            'is_visible' => 1,
        ]);

        $response = $this->actingAs($this->teacher)->get(route('teacher.files'));

        $response->assertStatus(200);
        $response->assertSee('إدارة ورفع الملازم والدوسيات وأوراق العمل');
        $response->assertSee('ملزمة أسئلة سنوات سابقة');
    }

    public function test_admin_can_access_videos_and_files_views()
    {
        $responseVideos = $this->actingAs($this->admin)->get(route('admin.videos'));
        $responseVideos->assertStatus(200);

        $responseFiles = $this->actingAs($this->admin)->get(route('admin.files'));
        $responseFiles->assertStatus(200);
    }

    public function test_teacher_can_toggle_content_visibility()
    {
        $content = EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'درس محجوب أو متاح',
            'type'       => 'video',
            'url_path'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order'      => 1,
            'is_visible' => 1,
        ]);

        $response = $this->actingAs($this->teacher)->postJson(route('teacher.visibility.toggle', $content->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_visible' => 0]);
        $this->assertEquals(0, $content->fresh()->is_visible);

        $response2 = $this->actingAs($this->teacher)->postJson(route('teacher.visibility.toggle', $content->id));
        $response2->assertStatus(200);
        $response2->assertJson(['success' => true, 'is_visible' => 1]);
        $this->assertEquals(1, $content->fresh()->is_visible);
    }
}
