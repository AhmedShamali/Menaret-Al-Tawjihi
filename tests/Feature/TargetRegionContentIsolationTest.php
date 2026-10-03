<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stage;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\EducationalContent;
use App\Models\Exam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class TargetRegionContentIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected User $admin;
    protected Stage $stage;
    protected Subject $subject;
    protected Student $gazaStudent;
    protected Student $westBankStudent;

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
            'name_ar'     => 'الفيزياء',
            'subject_key' => 'physics_scientific',
            'price_ils'   => 0,
            'is_free'     => true,
        ]);

        $this->teacher = User::create([
            'name'       => 'الأستاذ أحمد',
            'email'      => 'teacher_physics@platform.ps',
            'password'   => bcrypt('password123'),
            'role'       => 'teacher',
            'subject_id' => $this->subject->id,
            'is_approved'=> 1,
        ]);

        $this->admin = User::create([
            'name'       => 'المدير العام',
            'email'      => 'admin_test@platform.ps',
            'password'   => bcrypt('password123'),
            'role'       => 'admin',
            'is_approved'=> 1,
        ]);

        // طالب غزة
        $this->gazaStudent = Student::create([
            'name_ar'  => 'محمد الغزاوي',
            'name_en'  => 'Mohammed Al-Ghazzawi',
            'email'    => 'gaza_std@tawjihi.ps',
            'nid'      => '900000001',
            'stage_id' => $this->stage->id,
            'city'     => 'غزة',
            'region'   => 'gaza',
            'age'      => 18,
            'gender'   => 'ذكر',
            'phone'    => '0599000001',
            'password' => bcrypt('password123'),
            'status'   => 'active',
        ]);

        Enrollment::create([
            'student_id'     => $this->gazaStudent->id,
            'subject_id'     => $this->subject->id,
            'status'         => 'active',
            'access_mode'    => 'all',
            'payment_status' => 'paid',
        ]);

        // طالب الضفة
        $this->westBankStudent = Student::create([
            'name_ar'  => 'خالد المقدسي',
            'name_en'  => 'Khaled Al-Maqdisi',
            'email'    => 'wb_std@tawjihi.ps',
            'nid'      => '900000002',
            'stage_id' => $this->stage->id,
            'city'     => 'رام الله والبيرة',
            'region'   => 'west_bank',
            'age'      => 18,
            'gender'   => 'ذكر',
            'phone'    => '0599000002',
            'password' => bcrypt('password123'),
            'status'   => 'active',
        ]);

        Enrollment::create([
            'student_id'     => $this->westBankStudent->id,
            'subject_id'     => $this->subject->id,
            'status'         => 'active',
            'access_mode'    => 'all',
            'payment_status' => 'paid',
        ]);
    }

    public function test_teacher_can_upload_content_with_target_region()
    {
        $response = $this->actingAs($this->teacher)->postJson(route('teacher.educational_contents.store'), [
            'subject_id'    => $this->subject->id,
            'title'         => 'شرح الحركة التوافقية لطلاب غزة',
            'video_url'     => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'type'          => 'video',
            'order'         => 1,
            'target_region' => 'gaza',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('educational_contents', [
            'title'         => 'شرح الحركة التوافقية لطلاب غزة',
            'target_region' => 'gaza',
        ]);
    }

    public function test_student_only_sees_content_for_their_region_and_common_content()
    {
        // 1. محتوى مشترك للجميع
        EducationalContent::create([
            'subject_id'    => $this->subject->id,
            'title'         => 'درس عام مشترك للجميع',
            'type'          => 'video',
            'url_path'      => 'https://www.youtube.com/watch?v=common1',
            'order'         => 1,
            'is_visible'    => 1,
            'target_region' => 'all',
        ]);

        // 2. محتوى مخصص لقطاع غزة
        EducationalContent::create([
            'subject_id'    => $this->subject->id,
            'title'         => 'شرح منهاج غزة الخاص',
            'type'          => 'video',
            'url_path'      => 'https://www.youtube.com/watch?v=gaza1',
            'order'         => 2,
            'is_visible'    => 1,
            'target_region' => 'gaza',
        ]);

        // 3. محتوى مخصص للضفة الغربية
        EducationalContent::create([
            'subject_id'    => $this->subject->id,
            'title'         => 'شرح منهاج الضفة الخاص',
            'type'          => 'video',
            'url_path'      => 'https://www.youtube.com/watch?v=wb1',
            'order'         => 3,
            'is_visible'    => 1,
            'target_region' => 'west_bank',
        ]);

        // فحص طالب غزة: يرى المشترك وغزة، ولا يرى محتوى الضفة
        $responseGaza = $this->actingAs($this->gazaStudent, 'student')->get(route('student.subjects.show', $this->subject->id));
        $responseGaza->assertStatus(200);
        $responseGaza->assertSee('درس عام مشترك للجميع');
        $responseGaza->assertSee('شرح منهاج غزة الخاص');
        $responseGaza->assertDontSee('شرح منهاج الضفة الخاص');

        // فحص طالب الضفة: يرى المشترك والضفة، ولا يرى محتوى غزة
        $responseWB = $this->actingAs($this->westBankStudent, 'student')->get(route('student.subjects.show', $this->subject->id));
        $responseWB->assertStatus(200);
        $responseWB->assertSee('درس عام مشترك للجميع');
        $responseWB->assertSee('شرح منهاج الضفة الخاص');
        $responseWB->assertDontSee('شرح منهاج غزة الخاص');
    }

    public function test_student_cannot_download_files_of_opposing_region()
    {
        $filePath = 'educational/files/wb_only.pdf';
        Storage::disk('public')->put($filePath, 'wb pdf dummy content');

        $wbContent = EducationalContent::create([
            'subject_id'    => $this->subject->id,
            'title'         => 'دوسية ملخص الضفة',
            'type'          => 'file',
            'pdf_path'      => $filePath,
            'order'         => 1,
            'is_visible'    => 1,
            'target_region' => 'west_bank',
        ]);

        // طالب غزة يحاول تحميل دوسية الضفة مباشرة عبر الرابط -> يجب حظره وتحويله مع رسالة خطأ
        $response = $this->actingAs($this->gazaStudent, 'student')->get(route('content.download', $wbContent->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_teacher_and_admin_see_all_regional_contents()
    {
        EducationalContent::create([
            'subject_id'    => $this->subject->id,
            'title'         => 'فيديو غزة التعليمي',
            'type'          => 'video',
            'url_path'      => 'https://www.youtube.com/watch?v=gz111',
            'order'         => 1,
            'is_visible'    => 1,
            'target_region' => 'gaza',
        ]);

        EducationalContent::create([
            'subject_id'    => $this->subject->id,
            'title'         => 'فيديو الضفة التعليمي',
            'type'          => 'video',
            'url_path'      => 'https://www.youtube.com/watch?v=wb222',
            'order'         => 2,
            'is_visible'    => 1,
            'target_region' => 'west_bank',
        ]);

        // المعلم في لوحة الفيديوهات يرى المحتويين معاً
        $response = $this->actingAs($this->teacher)->get(route('teacher.videos'));
        $response->assertStatus(200);
        $response->assertSee('فيديو غزة التعليمي');
        $response->assertSee('فيديو الضفة التعليمي');
        $response->assertSee('غزة العزة 🌿');
        $response->assertSee('الضفة والقدس 🏛️');
    }

    public function test_teacher_can_create_exam_with_target_region()
    {
        $response = $this->actingAs($this->teacher)->postJson(route('teacher.exams.store'), [
            'subject_id'       => $this->subject->id,
            'title'            => 'اختبار فيزياء لطلاب غزة',
            'duration_minutes' => 45,
            'pass_marks'       => 50,
            'target_region'    => 'gaza',
            'questions'        => [
                [
                    'type'          => 'mcq',
                    'question_text' => 'ما هي وحدة قياس القوة؟',
                    'points'        => 5,
                    'a'             => 'نيوتن',
                    'b'             => 'جول',
                    'c'             => 'واط',
                    'd'             => 'فولت',
                    'correct_answer'=> 'a',
                ],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('exams', [
            'title'         => 'اختبار فيزياء لطلاب غزة',
            'target_region' => 'gaza',
        ]);
    }

    public function test_student_only_sees_exams_for_their_region_and_common_exams()
    {
        // 1. اختبار عام مشترك
        Exam::create([
            'subject_id'       => $this->subject->id,
            'title'            => 'اختبار الفيزياء العام المشترك',
            'duration_minutes' => 60,
            'status'           => 'published',
            'target_region'    => 'all',
        ]);

        // 2. اختبار مخصص لقطاع غزة
        Exam::create([
            'subject_id'       => $this->subject->id,
            'title'            => 'اختبار فيزياء منهاج غزة',
            'duration_minutes' => 60,
            'status'           => 'published',
            'target_region'    => 'gaza',
        ]);

        // 3. اختبار مخصص للضفة الغربية
        Exam::create([
            'subject_id'       => $this->subject->id,
            'title'            => 'اختبار فيزياء منهاج الضفة',
            'duration_minutes' => 60,
            'status'           => 'published',
            'target_region'    => 'west_bank',
        ]);

        // فحص طالب غزة
        $responseGaza = $this->actingAs($this->gazaStudent, 'student')->get(route('student.exams.index'));
        $responseGaza->assertStatus(200);
        $responseGaza->assertSee('اختبار الفيزياء العام المشترك');
        $responseGaza->assertSee('اختبار فيزياء منهاج غزة');
        $responseGaza->assertDontSee('اختبار فيزياء منهاج الضفة');

        // فحص طالب الضفة
        $responseWB = $this->actingAs($this->westBankStudent, 'student')->get(route('student.exams.index'));
        $responseWB->assertStatus(200);
        $responseWB->assertSee('اختبار الفيزياء العام المشترك');
        $responseWB->assertSee('اختبار فيزياء منهاج الضفة');
        $responseWB->assertDontSee('اختبار فيزياء منهاج غزة');
    }

    public function test_student_cannot_take_or_submit_exam_of_opposing_region()
    {
        $wbExam = Exam::create([
            'subject_id'       => $this->subject->id,
            'title'            => 'امتحان توجيهي الضفة والقدس',
            'duration_minutes' => 60,
            'status'           => 'published',
            'target_region'    => 'west_bank',
        ]);

        $gazaExam = Exam::create([
            'subject_id'       => $this->subject->id,
            'title'            => 'امتحان توجيهي قطاع غزة',
            'duration_minutes' => 60,
            'status'           => 'published',
            'target_region'    => 'gaza',
        ]);

        // طالب غزة يحاول دخول اختبار الضفة -> يتم تحويله مع رسالة خطأ
        $responseTake = $this->actingAs($this->gazaStudent, 'student')->get(route('student.exams.take', $wbExam->id));
        $responseTake->assertRedirect(route('student.exams.index'));
        $responseTake->assertSessionHas('error');

        // طالب غزة يحاول تسليم إجابات على اختبار الضفة -> يتم رفضه بـ 403
        $responseSubmit = $this->actingAs($this->gazaStudent, 'student')->post(route('student.exams.submit', $wbExam->id), [
            'answers' => [],
        ]);
        $responseSubmit->assertStatus(403);

        // طالب الضفة يحاول دخول اختبار غزة
        $responseWBTake = $this->actingAs($this->westBankStudent, 'student')->get(route('student.exams.take', $gazaExam->id));
        $responseWBTake->assertRedirect(route('student.exams.index'));
        $responseWBTake->assertSessionHas('error');
    }

    public function test_teacher_and_admin_see_all_regional_exams()
    {
        Exam::create([
            'subject_id'       => $this->subject->id,
            'title'            => 'امتحان إقليمي لغزة',
            'duration_minutes' => 60,
            'status'           => 'published',
            'target_region'    => 'gaza',
        ]);

        Exam::create([
            'subject_id'       => $this->subject->id,
            'title'            => 'امتحان إقليمي للضفة',
            'duration_minutes' => 60,
            'status'           => 'published',
            'target_region'    => 'west_bank',
        ]);

        // المعلم يرى كافة الاختبارات مع شاراتها
        $resTeacher = $this->actingAs($this->teacher)->get(route('teacher.exams.index'));
        $resTeacher->assertStatus(200);
        $resTeacher->assertSee('امتحان إقليمي لغزة');
        $resTeacher->assertSee('امتحان إقليمي للضفة');
        $resTeacher->assertSee('قطاع غزة');
        $resTeacher->assertSee('الضفة والقدس');

        // المدير يرى كافة الاختبارات
        $resAdmin = $this->actingAs($this->admin)->get(route('admin.exams.index'));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('امتحان إقليمي لغزة');
        $resAdmin->assertSee('امتحان إقليمي للضفة');
    }
}
