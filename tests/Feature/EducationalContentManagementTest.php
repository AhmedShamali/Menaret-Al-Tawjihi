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

    public function test_admin_can_toggle_content_visibility()
    {
        $content = EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'درس محجوب أو متاح من الأدمن',
            'type'       => 'video',
            'url_path'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order'      => 1,
            'is_visible' => 1,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.visibility.toggle', $content->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_visible' => 0]);
        $this->assertEquals(0, $content->fresh()->is_visible);
    }

    public function test_download_file_returns_correct_response_for_stored_pdf()
    {
        Storage::fake('public');
        Storage::disk('public')->put('educational/files/sample_dossier.pdf', '%PDF-1.4 sample content');

        $content = EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'دوسية نموذجية',
            'type'       => 'file',
            'pdf_path'   => 'educational/files/sample_dossier.pdf',
            'order'      => 1,
            'is_visible' => 1,
        ]);

        $response = $this->get(route('content.download', $content->id));

        $response->assertStatus(200);
        $this->assertStringContainsString('%PDF-1.4 sample content', $response->streamedContent());
    }

    public function test_download_file_with_missing_file_redirects_with_error_and_never_returns_zero_bytes()
    {
        $content = EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'ملف مفقود',
            'type'       => 'file',
            'pdf_path'   => 'educational/files/non_existent.pdf',
            'order'      => 1,
            'is_visible' => 1,
        ]);

        $response = $this->get(route('content.download', $content->id));

        // Must redirect back with error flash message instead of outputting an empty 0-byte stream
        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_student_subject_view_separates_videos_and_files_properly()
    {
        $student = \App\Models\Student::create([
            'name_ar'  => 'طالب تجريبي للاختبار',
            'name_en'  => 'Test Student Show',
            'nid'      => '400000099',
            'email'    => 'student_show@platform.ps',
            'password' => bcrypt('password123'),
            'phone'    => '0599000099',
            'age'      => 17,
            'gender'   => 'male',
            'stage_id' => $this->stage->id,
            'status'   => 'active',
        ]);

        \App\Models\Enrollment::create([
            'student_id'  => $student->id,
            'subject_id'  => $this->subject->id,
            'status'      => 'active',
            'access_mode' => 'all',
        ]);

        // File-only content
        $fileOnly = EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'دوسية بدون فيديو',
            'type'       => 'file',
            'pdf_path'   => 'educational/files/test.pdf',
            'url_path'   => null,
            'order'      => 1,
            'is_visible' => 1,
        ]);

        // Real video content
        $videoOnly = EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'فيديو شرح الدرس',
            'type'       => 'video',
            'url_path'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order'      => 2,
            'is_visible' => 1,
        ]);

        $response = $this->actingAs($student, 'student')->get(route('student.subjects.show', $this->subject->id));

        $response->assertStatus(200);
        // Video tab should show the real video title
        $response->assertSee('فيديو شرح الدرس');
        // File should be listed in dossiers and work papers
        $response->assertSee('دوسية بدون فيديو');
        // Check that YouTube iframe is rendered
        $response->assertSee('embed/dQw4w9WgXcQ');
    }

    public function test_hidden_video_is_not_displayed_to_students()
    {
        $student = \App\Models\Student::create([
            'name_ar'  => 'طالب محجوب تجريبي',
            'name_en'  => 'Test Hidden Student',
            'nid'      => '400000098',
            'email'    => 'student2@tawjihi.ps',
            'password' => bcrypt('password123'),
            'phone'    => '0599000098',
            'age'      => 17,
            'gender'   => 'female',
            'stage_id' => $this->stage->id,
            'status'   => 'active',
        ]);

        \App\Models\Enrollment::create([
            'student_id'  => $student->id,
            'subject_id'  => $this->subject->id,
            'status'      => 'active',
            'access_mode' => 'all',
        ]);

        // Hidden content (is_visible = 0)
        EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'درس محجوب سري جدا',
            'type'       => 'video',
            'url_path'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order'      => 1,
            'is_visible' => 0,
        ]);

        $response = $this->actingAs($student, 'student')->get(route('student.subjects.show', $this->subject->id));
        $response->assertStatus(200);
        $response->assertDontSee('درس محجوب سري جدا');
    }

    public function test_admin_sidebar_contains_educational_content_management_links()
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Admin now has direct control over educational content as requested
        $response->assertSee(route('admin.videos'));
        $response->assertSee(route('admin.files'));
        $response->assertSee(route('admin.visibility'));

        // Teacher specific exams links are not shown to admin
        $response->assertDontSee(route('admin.exams.index'));
        $response->assertDontSee(route('admin.submissions.index'));

        // Subject pricing belongs under subscriptions
        $response->assertSee(route('admin.subjects.pricing'));
    }

    public function test_admin_can_upload_content_to_multiple_branches_simultaneously()
    {
        // 1. Setup two branches (e.g. Scientific and Literary) with matching subject (e.g. Arabic)
        $stageScientific = $this->stage; // Scientific
        $stageLiterary = Stage::create([
            'grade_level' => 121,
            'label_ar'    => 'الثانوية العامة - الفرع الأدبي',
            'icon'        => '📖',
        ]);

        $subjectScientificArabic = Subject::create([
            'stage_id'    => $stageScientific->id,
            'name_ar'     => 'اللغة العربية (علمي)',
            'subject_key' => 'arabic_sci',
            'price_ils'   => 100,
        ]);

        $subjectLiteraryArabic = Subject::create([
            'stage_id'    => $stageLiterary->id,
            'name_ar'     => 'اللغة العربية (أدبي)',
            'subject_key' => 'arabic_lit',
            'price_ils'   => 100,
        ]);

        $teacherScientific = User::create([
            'name'        => 'أستاذ عربي علمي',
            'email'       => 'teacher_sci@platform.ps',
            'password'    => bcrypt('password123'),
            'role'        => 'teacher',
            'subject_id'  => $subjectScientificArabic->id,
            'is_approved' => 1,
        ]);

        $teacherLiterary = User::create([
            'name'        => 'أستاذ عربي أدبي',
            'email'       => 'teacher_lit@platform.ps',
            'password'    => bcrypt('password123'),
            'role'        => 'teacher',
            'subject_id'  => $subjectLiteraryArabic->id,
            'is_approved' => 1,
        ]);

        $studentSci = \App\Models\Student::create([
            'name_ar'  => 'طالب علمي تجريبي',
            'name_en'  => 'Sci Student',
            'nid'      => '400000001',
            'email'    => 'sci_student@platform.ps',
            'password' => bcrypt('password123'),
            'phone'    => '0599000001',
            'age'      => 17,
            'gender'   => 'male',
            'stage_id' => $stageScientific->id,
            'status'   => 'active',
        ]);

        \App\Models\Enrollment::create([
            'student_id'  => $studentSci->id,
            'subject_id'  => $subjectScientificArabic->id,
            'status'      => 'active',
            'access_mode' => 'all',
        ]);

        $studentLit = \App\Models\Student::create([
            'name_ar'  => 'طالب أدبي تجريبي',
            'name_en'  => 'Lit Student',
            'nid'      => '400000002',
            'email'    => 'lit_student@platform.ps',
            'password' => bcrypt('password123'),
            'phone'    => '0599000002',
            'age'      => 17,
            'gender'   => 'female',
            'stage_id' => $stageLiterary->id,
            'status'   => 'active',
        ]);

        \App\Models\Enrollment::create([
            'student_id'  => $studentLit->id,
            'subject_id'  => $subjectLiteraryArabic->id,
            'status'      => 'active',
            'access_mode' => 'all',
        ]);

        // 2. Admin uploads a video selecting both branches
        $response = $this->actingAs($this->admin)->postJson(route('admin.educational_contents.store'), [
            'subject_id'   => $subjectScientificArabic->id,
            'stage_ids'    => [$stageScientific->id, $stageLiterary->id],
            'title'        => 'قواعد النحو: إعراب المبتدأ والخبر المشترك',
            'type'         => 'video',
            'video_url'    => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'channel_name' => 'إدارة المنصة التعليمية',
            'order'        => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['icon' => 'success']);

        // Assert database has records for both subjects with identical title and url
        $this->assertDatabaseHas('educational_contents', [
            'subject_id' => $subjectScientificArabic->id,
            'title'      => 'قواعد النحو: إعراب المبتدأ والخبر المشترك',
        ]);

        $this->assertDatabaseHas('educational_contents', [
            'subject_id' => $subjectLiteraryArabic->id,
            'title'      => 'قواعد النحو: إعراب المبتدأ والخبر المشترك',
        ]);

        // 3. Assert Scientific Teacher sees the video in their panel
        $teacherSciRes = $this->actingAs($teacherScientific)->get(route('teacher.videos'));
        $teacherSciRes->assertStatus(200);
        $teacherSciRes->assertSee('قواعد النحو: إعراب المبتدأ والخبر المشترك');

        // 4. Assert Literary Teacher ALSO sees the video in their panel (in parallel)
        $teacherLitRes = $this->actingAs($teacherLiterary)->get(route('teacher.videos'));
        $teacherLitRes->assertStatus(200);
        $teacherLitRes->assertSee('قواعد النحو: إعراب المبتدأ والخبر المشترك');

        // 5. Assert both Scientific & Literary students can see the lecture
        $studentSciRes = $this->actingAs($studentSci, 'student')->get(route('student.subjects.show', $subjectScientificArabic->id));
        $studentSciRes->assertStatus(200);
        $studentSciRes->assertSee('قواعد النحو: إعراب المبتدأ والخبر المشترك');

        $studentLitRes = $this->actingAs($studentLit, 'student')->get(route('student.subjects.show', $subjectLiteraryArabic->id));
        $studentLitRes->assertStatus(200);
        $studentLitRes->assertSee('قواعد النحو: إعراب المبتدأ والخبر المشترك');

        // 6. Assert deleting one branch's record does NOT delete the other branch's record
        $litContent = EducationalContent::where('subject_id', $subjectLiteraryArabic->id)->first();
        $delRes = $this->actingAs($this->admin)->delete(route('admin.educational_contents.destroy', $litContent->id));
        $this->assertDatabaseMissing('educational_contents', ['id' => $litContent->id]);
        $this->assertDatabaseHas('educational_contents', ['subject_id' => $subjectScientificArabic->id]);

        $teacherSciRes2 = $this->actingAs($teacherScientific)->get(route('teacher.videos'));
        $teacherSciRes2->assertSee('قواعد النحو: إعراب المبتدأ والخبر المشترك');
    }

    public function test_teacher_can_upload_direct_video_file()
    {
        Storage::fake('public');
        $videoFile = UploadedFile::fake()->create('lesson1.mp4', 1024, 'video/mp4');

        $response = $this->actingAs($this->teacher, 'web')->post(route('teacher.educational_contents.store'), [
            'subject_id' => $this->subject->id,
            'title'      => 'شرح مصور مرفوع مباشر',
            'type'       => 'video',
            'video_file' => $videoFile,
            'order'      => 1,
        ]);

        $response->assertStatus(200);

        $content = EducationalContent::where('title', 'شرح مصور مرفوع مباشر')->first();
        $this->assertNotNull($content);
        $this->assertStringContainsString('educational/videos/', $content->url_path);
        Storage::disk('public')->assertExists($content->url_path);
    }

    public function test_download_direct_video_returns_file()
    {
        Storage::fake('public');
        $filePath = 'educational/videos/test_lesson.mp4';
        Storage::disk('public')->put($filePath, 'fake video content');

        $content = EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'فيديو تجريبي للتحميل',
            'type'       => 'video',
            'url_path'   => $filePath,
            'order'      => 1,
            'is_visible' => 1,
        ]);

        $response = $this->get(route('content.downloadVideo', $content->id));
        $response->assertStatus(200);
        $this->assertEquals('fake video content', $response->streamedContent());
    }

    public function test_student_can_store_and_fetch_video_notes()
    {
        $student = \App\Models\Student::create([
            'name_ar'  => 'طالب الملاحظات',
            'name_en'  => 'Note Student',
            'nid'      => '400000077',
            'email'    => 'notestudent@platform.ps',
            'password' => bcrypt('password123'),
            'phone'    => '0599000077',
            'age'      => 17,
            'gender'   => 'male',
            'stage_id' => $this->stage->id,
            'status'   => 'active',
        ]);

        $content = EducationalContent::create([
            'subject_id' => $this->subject->id,
            'title'      => 'درس الملاحظات',
            'type'       => 'video',
            'url_path'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order'      => 1,
            'is_visible' => 1,
        ]);

        // 1. Store note
        $storeRes = $this->actingAs($student, 'student')->postJson('/student/video-notes', [
            'educational_content_id' => $content->id,
            'timestamp_seconds'      => 135,
            'note_text'              => 'قانون نيوتن الثاني هنا مهم جداً',
        ]);
        $storeRes->assertStatus(200);
        $storeRes->assertJson(['success' => true]);

        // 2. Fetch notes
        $fetchRes = $this->actingAs($student, 'student')->getJson("/student/video-notes/{$content->id}");
        $fetchRes->assertStatus(200);
        $fetchRes->assertJsonCount(1, 'notes');
        $fetchRes->assertJsonFragment(['formatted_time' => '02:15', 'note_text' => 'قانون نيوتن الثاني هنا مهم جداً']);

        // 3. Delete note
        $noteId = $fetchRes->json('notes.0.id');
        $delRes = $this->actingAs($student, 'student')->deleteJson("/student/video-notes/{$noteId}");
        $delRes->assertStatus(200);
        $delRes->assertJson(['success' => true]);
    }

    public function test_teacher_can_upload_video_using_chunked_upload()
    {
        $fileId = 'test_chunk_vid_' . time();
        $chunk1 = UploadedFile::fake()->create('part1.mp4', 50);
        $chunk2 = UploadedFile::fake()->create('part2.mp4', 50);

        // Upload chunk 0
        $res1 = $this->actingAs($this->teacher)->postJson(route('educational_contents.upload_chunk'), [
            'file_id'      => $fileId,
            'chunk_index'  => 0,
            'total_chunks' => 2,
            'file_name'    => 'large_lesson.mp4',
            'chunk'        => $chunk1,
        ]);
        $res1->assertStatus(200);
        $res1->assertJson(['done' => false, 'chunk_index' => 0]);

        // Upload chunk 1 (final)
        $res2 = $this->actingAs($this->teacher)->postJson(route('educational_contents.upload_chunk'), [
            'file_id'      => $fileId,
            'chunk_index'  => 1,
            'total_chunks' => 2,
            'file_name'    => 'large_lesson.mp4',
            'chunk'        => $chunk2,
        ]);
        $res2->assertStatus(200);
        $res2->assertJson(['done' => true]);
        $uploadedPath = $res2->json('uploaded_video_path');

        // Now save educational content using the uploaded path
        $storeRes = $this->actingAs($this->teacher)->postJson(route('teacher.educational_contents.store'), [
            'subject_id'          => $this->subject->id,
            'title'               => 'درس فيديو كبير مجزأ',
            'order'               => 10,
            'uploaded_video_path' => $uploadedPath,
            'formatted_size'      => '100 KB',
        ]);
        $storeRes->assertStatus(200);
        $storeRes->assertJson(['icon' => 'success']);

        $this->assertDatabaseHas('educational_contents', [
            'title'      => 'درس فيديو كبير مجزأ',
            'subject_id' => $this->subject->id,
            'url_path'   => $uploadedPath,
            'type'       => 'video',
        ]);
    }
}

