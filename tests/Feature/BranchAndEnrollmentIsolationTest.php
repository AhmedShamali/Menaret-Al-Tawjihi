<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Student;
use App\Models\Stage;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Enrollment;
use App\Models\User;

class BranchAndEnrollmentIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Stage $sciStage;
    protected Stage $litStage;
    protected Subject $sciSubject;
    protected Subject $sciSubject2;
    protected Subject $litSubject;
    protected Exam $sciExam;
    protected Exam $litExam;
    protected Student $sciStudent;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. المراحل الدراسية (الفرع العلمي والفرع الأدبي)
        $this->sciStage = Stage::create([
            'grade_level' => '122',
            'name_ar' => 'الثاني عشر - الفرع العلمي',
            'label_ar' => 'الفرع العلمي',
        ]);

        $this->litStage = Stage::create([
            'grade_level' => '121',
            'name_ar' => 'الثاني عشر - الفرع الأدبي',
            'label_ar' => 'الفرع الأدبي',
        ]);

        $teacher = User::create([
            'name' => 'أستاذ المادة',
            'email' => 'teacher_iso@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'teacher',
        ]);

        // 2. المواد الدراسية
        $this->sciSubject = Subject::create([
            'name_ar' => 'الفيزياء - علمي',
            'subject_key' => 'PHYS-SCI',
            'stage_id' => $this->sciStage->id,
            'teacher_id' => $teacher->id,
            'price_ils' => 120,
        ]);

        $this->sciSubject2 = Subject::create([
            'name_ar' => 'الكيمياء - علمي',
            'subject_key' => 'CHEM-SCI',
            'stage_id' => $this->sciStage->id,
            'teacher_id' => $teacher->id,
            'price_ils' => 120,
        ]);

        $this->litSubject = Subject::create([
            'name_ar' => 'التاريخ - أدبي',
            'subject_key' => 'HIST-LIT',
            'stage_id' => $this->litStage->id,
            'teacher_id' => $teacher->id,
            'price_ils' => 100,
        ]);

        // 3. الامتحانات
        $this->sciExam = Exam::create([
            'title' => 'امتحان الفيزياء التجريبي',
            'subject_id' => $this->sciSubject->id,
            'stage_id' => $this->sciStage->id,
            'teacher_id' => $teacher->id,
            'duration_minutes' => 60,
        ]);

        $this->litExam = Exam::create([
            'title' => 'امتحان التاريخ الوزاري',
            'subject_id' => $this->litSubject->id,
            'stage_id' => $this->litStage->id,
            'teacher_id' => $teacher->id,
            'duration_minutes' => 60,
        ]);

        // 4. طالب في الفرع العلمي
        $this->sciStudent = Student::create([
            'name_ar' => 'محمد العلمي',
            'name_en' => 'Mohammed Sci',
            'email' => 'mohammed.sci@tawjihi.ps',
            'nid' => '900112233',
            'phone' => '0599112233',
            'password' => bcrypt('secret123'),
            'age' => 18,
            'gender' => 'male',
            'stage_id' => $this->sciStage->id,
            'status' => 'active',
            'monthly_fee' => 100.00,
        ]);
    }

    public function test_student_with_no_enrollments_sees_zero_subjects(): void
    {
        $response = $this->actingAs($this->sciStudent, 'student')
            ->get(route('student.subjects.index'));

        $response->assertStatus(200);
        $response->assertViewHas('subjects', function ($subjects) {
            return $subjects->isEmpty();
        });
        $response->assertDontSee('الفيزياء - علمي');
        $response->assertDontSee('التاريخ - أدبي');
    }

    public function test_student_sees_only_enrolled_subjects_of_their_original_branch(): void
    {
        // تسجيل في الفيزياء (علمي)
        Enrollment::create([
            'student_id' => $this->sciStudent->id,
            'subject_id' => $this->sciSubject->id,
            'status' => 'active',
            'access_mode' => 'all',
        ]);

        // اشتراك غير سليم في التاريخ (أدبي) للتحقق من العزل الصارم
        Enrollment::create([
            'student_id' => $this->sciStudent->id,
            'subject_id' => $this->litSubject->id,
            'status' => 'active',
            'access_mode' => 'all',
        ]);

        $response = $this->actingAs($this->sciStudent, 'student')
            ->get(route('student.subjects.index'));

        $response->assertStatus(200);
        // يجب أن يرى فيزياء (مادته المسجل بها في فرعه)
        $response->assertSee('الفيزياء - علمي');
        // ممنوع تماماً أن تظهر مادة التاريخ لأنها ليست من فرعه الأصلي العلمي
        $response->assertDontSee('التاريخ - أدبي');
        // ممنوع أن تظهر الكيمياء لأنه غير مسجل بها
        $response->assertDontSee('الكيمياء - علمي');
    }

    public function test_student_cannot_access_show_subject_of_another_branch(): void
    {
        $response = $this->actingAs($this->sciStudent, 'student')
            ->get(route('student.subjects.show', $this->litSubject->id));

        $response->assertRedirect(route('student.subjects.index'));
        $response->assertSessionHas('error');
    }

    public function test_student_cannot_access_show_subject_if_not_enrolled(): void
    {
        // مادة في نفس فرعه ولكن غير مسجل بها
        $response = $this->actingAs($this->sciStudent, 'student')
            ->get(route('student.subjects.show', $this->sciSubject2->id));

        $response->assertRedirect(route('student.subjects.index'));
        $response->assertSessionHas('error');
    }

    public function test_student_enrolled_in_own_branch_can_access_subject(): void
    {
        Enrollment::create([
            'student_id' => $this->sciStudent->id,
            'subject_id' => $this->sciSubject->id,
            'status' => 'active',
            'access_mode' => 'all',
        ]);

        $response = $this->actingAs($this->sciStudent, 'student')
            ->get(route('student.subjects.show', $this->sciSubject->id));

        $response->assertStatus(200);
        $response->assertSee('الفيزياء - علمي');
    }

    public function test_student_sees_only_exams_of_enrolled_subjects_in_original_branch(): void
    {
        // بدون اشتراك، لا يرى أي اختبار
        $responseNoEnrollment = $this->actingAs($this->sciStudent, 'student')
            ->get(route('student.exams.index'));

        $responseNoEnrollment->assertStatus(200);
        $responseNoEnrollment->assertDontSee('امتحان الفيزياء التجريبي');
        $responseNoEnrollment->assertDontSee('امتحان التاريخ الوزاري');

        // اشتراك في الفيزياء
        Enrollment::create([
            'student_id' => $this->sciStudent->id,
            'subject_id' => $this->sciSubject->id,
            'status' => 'active',
            'access_mode' => 'all',
        ]);

        $responseEnrolled = $this->actingAs($this->sciStudent, 'student')
            ->get(route('student.exams.index'));

        $responseEnrolled->assertStatus(200);
        $responseEnrolled->assertSee('امتحان الفيزياء التجريبي');
        $responseEnrolled->assertDontSee('امتحان التاريخ الوزاري');
    }

    public function test_student_cannot_take_exam_of_another_branch(): void
    {
        Enrollment::create([
            'student_id' => $this->sciStudent->id,
            'subject_id' => $this->litSubject->id,
            'status' => 'active',
            'access_mode' => 'all',
        ]);

        $response = $this->actingAs($this->sciStudent, 'student')
            ->get(route('student.exams.take', $this->litExam->id));

        $response->assertRedirect(route('student.exams.index'));
        $response->assertSessionHas('error');
    }

    public function test_student_cannot_take_exam_of_not_enrolled_subject(): void
    {
        // مادة علمية تابعة لفرعه ولكن غير مسجل بها
        $response = $this->actingAs($this->sciStudent, 'student')
            ->get(route('student.exams.take', $this->sciExam->id));

        $response->assertRedirect(route('student.exams.index'));
        $response->assertSessionHas('error');
    }

    public function test_student_cannot_submit_exam_of_another_branch_or_not_enrolled(): void
    {
        // محاولة تسليم امتحان فرع آخر
        $resLit = $this->actingAs($this->sciStudent, 'student')
            ->postJson(route('student.exams.submit', $this->litExam->id), []);

        $resLit->assertStatus(403);

        // محاولة تسليم امتحان مادة غير مسجل بها
        $resSciNotEnrolled = $this->actingAs($this->sciStudent, 'student')
            ->postJson(route('student.exams.submit', $this->sciExam->id), []);

        $resSciNotEnrolled->assertStatus(403);
    }

    public function test_checkout_blocks_selecting_subjects_from_different_branch(): void
    {
        $response = $this->actingAs($this->sciStudent, 'student')
            ->post(route('student.courses.checkout'), [
                'subject_ids' => [$this->litSubject->id],
            ]);

        $response->assertRedirect(route('student.courses.catalog'));
        $response->assertSessionHas('error');
    }

    public function test_api_subjects_and_details_strictly_enforces_branch_and_enrollment(): void
    {
        Enrollment::create([
            'student_id' => $this->sciStudent->id,
            'subject_id' => $this->sciSubject->id,
            'status' => 'active',
            'access_mode' => 'all',
        ]);

        // طلب قائمة المواد عبر API
        $resList = $this->withHeader('X-Student-Id', $this->sciStudent->id)
            ->getJson('/api/v1/student/subjects');

        $resList->assertStatus(200);
        $resList->assertJsonFragment(['name_ar' => 'الفيزياء - علمي']);
        $resList->assertJsonMissing(['name_ar' => 'التاريخ - أدبي']);
        $resList->assertJsonMissing(['name_ar' => 'الكيمياء - علمي']);

        // محاولة جلب تفاصيل مادة فرع آخر
        $resLitDetails = $this->withHeader('X-Student-Id', $this->sciStudent->id)
            ->getJson('/api/v1/student/subjects/' . $this->litSubject->id);

        $resLitDetails->assertStatus(403);

        // محاولة جلب تفاصيل مادة من نفس الفرع ولكن غير مسجل بها
        $resSci2Details = $this->withHeader('X-Student-Id', $this->sciStudent->id)
            ->getJson('/api/v1/student/subjects/' . $this->sciSubject2->id);

        $resSci2Details->assertStatus(403);
    }
}
