<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stage;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamSubmission;
use App\Models\StudentAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamProctoringAndGradingPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected User $admin;
    protected Student $student;
    protected Stage $stage;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stage = Stage::create([
            'grade_level' => 122,
            'label_ar'    => 'الثانوية العامة - الفرع العلمي',
            'icon'        => '📐',
        ]);

        $this->subject = Subject::create([
            'stage_id'    => $this->stage->id,
            'name_ar'     => 'الفيزياء العامة',
            'subject_key' => 'physics_scientific',
            'price_ils'   => 100,
        ]);

        $this->teacher = User::create([
            'name'        => 'أ. خالد العلمي',
            'email'       => 'khaled_teacher@platform.ps',
            'password'    => bcrypt('password123'),
            'role'        => 'teacher',
            'subject_id'  => $this->subject->id,
            'is_approved' => 1,
        ]);

        $this->admin = User::create([
            'name'        => 'إدارة المنصة',
            'email'       => 'admin_proctor@platform.ps',
            'password'    => bcrypt('password123'),
            'role'        => 'admin',
            'is_approved' => 1,
        ]);

        $this->student = Student::create([
            'name_ar'     => 'أحمد التوجيهي',
            'name_en'     => 'Ahmed Student',
            'nid'         => '401234567',
            'phone'       => '0599000111',
            'age'         => 18,
            'gender'      => 'male',
            'email'       => 'ahmed_student@platform.ps',
            'password'    => bcrypt('password123'),
            'stage_id'    => $this->stage->id,
            'status'      => 'active',
        ]);
    }

    /**
     * اختبار دقة محلل رابط صورة السؤال (Question image_url accessor)
     */
    public function test_question_image_url_accessor_resolves_correctly()
    {
        $exam = Exam::create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'stage_id'   => $this->stage->id,
            'title'      => 'اختبار الحركة الموجية',
            'duration'   => 30,
        ]);

        // 1. صورة برابط سحابي خارجي كامل
        $qCloud = Question::create([
            'exam_id'       => $exam->id,
            'type'          => 'mcq',
            'question_text' => 'ما هي قيمة التردد في الشكل؟',
            'image'         => 'https://example-supabase.co/storage/v1/object/public/uploads/questions/wave.png',
            'a'             => '10 Hz',
            'b'             => '20 Hz',
            'correct_answer'=> 'a',
            'points'        => 5,
        ]);

        $this->assertEquals(
            'https://example-supabase.co/storage/v1/object/public/uploads/questions/wave.png',
            $qCloud->image_url
        );

        // 2. صورة بمسار محلي نسبي
        $qLocal = Question::create([
            'exam_id'       => $exam->id,
            'type'          => 'mcq',
            'question_text' => 'ما هو فرق الطور؟',
            'image'         => 'questions/phase.png',
            'a'             => 'pi/2',
            'b'             => 'pi',
            'correct_answer'=> 'b',
            'points'        => 5,
        ]);

        $this->assertStringContainsString('questions/phase.png', $qLocal->image_url);

        // 3. سؤال بدون صورة
        $qNoImg = Question::create([
            'exam_id'       => $exam->id,
            'type'          => 'mcq',
            'question_text' => 'سؤال نظري بحت',
            'image'         => null,
            'a'             => 'أ',
            'b'             => 'ب',
            'correct_answer'=> 'a',
            'points'        => 5,
        ]);

        $this->assertNull($qNoImg->image_url);
    }

    /**
     * اختبار حجب نتيجة الاختبار تلقائياً وعدم إظهارها للطالب إلا بعد تصحيح المعلم أو إذنه
     */
    public function test_exam_result_is_withheld_by_default_until_teacher_grades_or_publishes()
    {
        // اختبار مقالي وموضوعي بدون إظهار فوري
        $exam = Exam::create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'stage_id'   => $this->stage->id,
            'title'      => 'امتحان الفيزياء الوزاري التجريبي',
            'duration'   => 45,
            'show_result_immediately' => false,
        ]);

        $qEssay = Question::create([
            'exam_id'       => $exam->id,
            'type'          => 'essay',
            'question_text' => 'اشرح تجربة شقي يانغ بالتفصيل واشتق القانون.',
            'points'        => 10,
        ]);

        // تقديم الطالب للامتحان
        $response = $this->actingAs($this->student, 'student')->post(route('student.exams.submit', $exam->id), [
            'answers' => [
                $qEssay->id => 'الإجابة النظرية المقدمة من الطالب للتجربة',
            ],
            'tab_switches_count' => 0,
            'screenshots_count'  => 0,
        ]);

        $response->assertJson(['success' => true]);

        $submission = ExamSubmission::where('exam_id', $exam->id)->where('student_id', $this->student->id)->first();
        $this->assertNotNull($submission);
        $this->assertFalse((bool) $submission->is_published);
        $this->assertEquals('pending', $submission->status);
        $this->assertFalse($submission->canStudentViewResult());

        // عندما يتوجه الطالب لصفحة النتيجة، يجب أن تخبره بأن النتيجة قيد المراجعة ولا تظهر له الإجابات
        $resultView = $this->actingAs($this->student, 'student')->get(route('student.exams.results', $submission->id));
        $resultView->assertOk();
        $resultView->assertSee('قيد المراجعة والتدقيق الأكاديمي');

        // الآن يقوم المعلم بتصحيح ورصد الدرجات واعتمادها
        $saveGradeResponse = $this->actingAs($this->teacher)->post(route('teacher.submissions.saveGrade', $submission->id), [
            'grades' => [
                $submission->answers()->first()->id => 9,
            ],
        ]);

        $saveGradeResponse->assertJson(['success' => true]);

        $submission->refresh();
        $this->assertEquals('graded', $submission->status);
        $this->assertTrue((bool) $submission->is_published);
        $this->assertEquals(9, $submission->total_earned_grade);
        $this->assertTrue($submission->canStudentViewResult());

        // بعد اعتماد المعلم، تصبح النتيجة معلنة ومتاحة للمراجعة للطالب
        $publishedResultView = $this->actingAs($this->student, 'student')->get(route('student.exams.results', $submission->id));
        $publishedResultView->assertOk();
        $publishedResultView->assertSee('الدرجة المحققة');
        $publishedResultView->assertSee('مراجعة الإجابات');
    }

    /**
     * اختبار تبديل حالة ظهور النتيجة (toggle-publish) من قبل المعلم أو المدير
     */
    public function test_teacher_can_toggle_publish_result_for_submission()
    {
        $exam = Exam::create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'stage_id'   => $this->stage->id,
            'title'      => 'امتحان قصير',
            'duration'   => 15,
            'show_result_immediately' => false,
        ]);

        $submission = ExamSubmission::create([
            'exam_id'            => $exam->id,
            'student_id'         => $this->student->id,
            'total_earned_grade' => 20,
            'status'             => 'pending',
            'is_published'       => false,
        ]);

        $this->assertFalse((bool) $submission->is_published);

        // المعلم ينقر على زر إعلان النتيجة
        $resPublish = $this->actingAs($this->teacher)->post(route('teacher.submissions.togglePublish', $submission->id));
        $resPublish->assertJson(['success' => true]);
        $submission->refresh();
        $this->assertTrue((bool) $submission->is_published);

        // المعلم ينقر مجدداً لحجب النتيجة
        $resWithhold = $this->actingAs($this->teacher)->post(route('teacher.submissions.togglePublish', $submission->id));
        $resWithhold->assertJson(['success' => true]);
        $submission->refresh();
        $this->assertFalse((bool) $submission->is_published);
    }

    /**
     * اختبار رصد مؤشرات الغش عند مغادرة الطالب للصفحة أو محاولة لقطات الشاشة
     */
    public function test_anti_cheating_detection_records_violations_and_flags_submission()
    {
        $exam = Exam::create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'stage_id'   => $this->stage->id,
            'title'      => 'امتحان خاضع للرقابة الأكاديمية',
            'duration'   => 20,
        ]);

        $q = Question::create([
            'exam_id'       => $exam->id,
            'type'          => 'mcq',
            'question_text' => 'سؤال محمي',
            'a'             => 'أ',
            'b'             => 'ب',
            'correct_answer'=> 'a',
            'points'        => 5,
        ]);

        $cheatingFlags = [
            ['type' => 'tab_switch', 'count' => 1, 'timestamp' => now()->toDateTimeString()],
            ['type' => 'tab_switch', 'count' => 2, 'timestamp' => now()->toDateTimeString()],
            ['type' => 'screenshot', 'count' => 1, 'timestamp' => now()->toDateTimeString()],
        ];

        // تقديم الامتحان مع رصد 2 مغادرة و 1 لقطة شاشة
        $response = $this->actingAs($this->student, 'student')->post(route('student.exams.submit', $exam->id), [
            'answers' => [
                $q->id => 'a',
            ],
            'tab_switches_count' => 2,
            'screenshots_count'  => 1,
            'cheating_flags'     => json_encode($cheatingFlags),
        ]);

        $response->assertJson(['success' => true]);

        $submission = ExamSubmission::where('exam_id', $exam->id)->where('student_id', $this->student->id)->first();
        $this->assertNotNull($submission);
        $this->assertEquals(2, $submission->tab_switches_count);
        $this->assertEquals(1, $submission->screenshots_count);
        $this->assertTrue((bool) $submission->has_cheating_risk);
        $this->assertNotEmpty($submission->cheating_flags);

        // التأكد من ظهور إشارة اشتباه الغش في صفحة المعلم
        $submissionsList = $this->actingAs($this->teacher)->get(route('teacher.submissions.index'));
        $submissionsList->assertOk();
        $submissionsList->assertSee('اشتباه غش');
        $submissionsList->assertSee('مغادرة');
    }

    /**
     * اختبار استجابة واجهة الإبلاغ اللحظي عن أحداث الغش أثناء الجلسة
     */
    public function test_realtime_cheating_incident_endpoint_logs_telemetry()
    {
        $exam = Exam::create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'stage_id'   => $this->stage->id,
            'title'      => 'امتحان مراقب لحظياً',
            'duration'   => 15,
        ]);

        $incidentResponse = $this->actingAs($this->student, 'student')->postJson(
            route('student.exams.cheatingIncident', $exam->id),
            [
                'incident_type' => 'tab_switch',
                'details'       => ['count' => 1],
            ]
        );

        $incidentResponse->assertOk();
        $incidentResponse->assertJson(['status' => 'logged']);
    }

    /**
     * اختبار عزل الفروع الأكاديمية: منع ظهور امتحانات الفرع الأدبي لطلاب الفرع العلمي
     */
    public function test_branch_isolation_prevents_literary_exam_from_appearing_to_scientific_student()
    {
        $literaryStage = Stage::create([
            'grade_level' => 121,
            'label_ar'    => 'الثانوية العامة - الفرع الأدبي',
            'icon'        => '📖',
        ]);

        $literarySubject = Subject::create([
            'stage_id'    => $literaryStage->id,
            'name_ar'     => 'التاريخ للفرع الأدبي',
            'subject_key' => 'history_literary',
            'price_ils'   => 80,
        ]);

        $literaryExam = Exam::create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $literarySubject->id,
            'stage_id'   => $literaryStage->id,
            'title'      => 'امتحان التاريخ - الفرع الأدبي فقط',
            'duration'   => 40,
        ]);

        $scientificExam = Exam::create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'stage_id'   => $this->stage->id,
            'title'      => 'امتحان الفيزياء للفرع العلمي',
            'duration'   => 40,
        ]);

        // الطالب العلمي يزور صفحة الامتحانات
        $indexResponse = $this->actingAs($this->student, 'student')->get(route('student.exams.index'));
        $indexResponse->assertOk();

        // يجب أن يرى امتحان العلمي
        $indexResponse->assertSee('امتحان الفيزياء للفرع العلمي');
        // ويُحجب تماماً امتحان الأدبي
        $indexResponse->assertDontSee('امتحان التاريخ - الفرع الأدبي فقط');
    }

    /**
     * اختبار رصد العلامات مع خصم الدرجات وسبب الخصم وملاحظات المعلم عند وجود اشتباه غش
     */
    public function test_teacher_can_grade_cheating_submission_with_deductions_and_reasons()
    {
        $exam = Exam::create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'stage_id'   => $this->stage->id,
            'title'      => 'امتحان الفيزياء المتقدمة',
            'duration'   => 30,
            'show_result_immediately' => false,
        ]);

        $q = Question::create([
            'exam_id'       => $exam->id,
            'type'          => 'essay',
            'question_text' => 'اشرح أثر دوبلر مع ذكر 3 تطبيقات عملية.',
            'points'        => 20,
        ]);

        // تقديم الامتحان
        $this->actingAs($this->student, 'student')->post(route('student.exams.submit', $exam->id), [
            'answers' => [
                $q->id => 'حل الطالب الكامل',
            ],
            'tab_switches_count' => 3,
            'screenshots_count'  => 0,
        ]);

        $submission = ExamSubmission::where('exam_id', $exam->id)->where('student_id', $this->student->id)->first();
        $this->assertNotNull($submission);
        $answer = $submission->answers()->first();
        $this->assertNotNull($answer);

        // المعلم يرصد 18 درجة ويخصم 5 بسبب تكرار الخروج
        $gradeResponse = $this->actingAs($this->teacher)->post(route('teacher.submissions.saveGrade', $submission->id), [
            'grades' => [
                $answer->id => 18,
            ],
            'deduction_amount' => 5,
            'deduction_reason' => 'خصم درجات لتكرار مغادرة صفحة الاختبار 3 مرات',
            'teacher_notes'    => 'إجابتك ممتازة ولكن تم الخصم بسبب مخالفة تعليمات النزاهة الأكاديمية.',
        ]);

        $gradeResponse->assertJson(['success' => true]);

        $submission->refresh();
        $this->assertEquals(13, $submission->total_earned_grade); // 18 - 5 = 13
        $this->assertEquals(5, $submission->deduction_amount);
        $this->assertEquals('خصم درجات لتكرار مغادرة صفحة الاختبار 3 مرات', $submission->deduction_reason);
        $this->assertEquals('إجابتك ممتازة ولكن تم الخصم بسبب مخالفة تعليمات النزاهة الأكاديمية.', $submission->teacher_notes);
        $this->assertEquals('graded', $submission->status);
        $this->assertTrue((bool) $submission->is_published);

        // عرض النتيجة للطالب والتأكد من إظهار بطاقة التنبيه بالخصم والملاحظات
        $studentResultView = $this->actingAs($this->student, 'student')->get(route('student.exams.results', $submission->id));
        $studentResultView->assertOk();
        $studentResultView->assertSee('تنبيه أكاديمي: تم تطبيق خصم درجات على هذا الاختبار');
        $studentResultView->assertSee('خصم درجات لتكرار مغادرة صفحة الاختبار 3 مرات');
        $studentResultView->assertSee('إجابتك ممتازة ولكن تم الخصم بسبب مخالفة تعليمات النزاهة الأكاديمية.');
    }

    /**
     * اختبار ظهور خيار إعادة الاختبار للطالب عند سماح المعلم له بالإعادة
     */
    public function test_retake_permission_allows_student_to_retake_and_shows_badge()
    {
        $exam = Exam::create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'stage_id'   => $this->stage->id,
            'title'      => 'امتحان كهرومغناطيسية',
            'duration'   => 25,
        ]);

        $submission = ExamSubmission::create([
            'exam_id'            => $exam->id,
            'student_id'         => $this->student->id,
            'total_earned_grade' => 5,
            'status'             => 'graded',
            'allow_retake'       => true,
            'is_published'       => true,
        ]);

        // زيارة صفحة الامتحانات للطالب
        $indexResponse = $this->actingAs($this->student, 'student')->get(route('student.exams.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('المعلّم أتاح لك إعادة تقديم الاختبار');
        $indexResponse->assertSee('إعادة الاختبار الآن');

        // يستطيع الطالب فتح قاعة الاختبار للإعادة دون منعه
        $takeResponse = $this->actingAs($this->student, 'student')->get(route('student.exams.take', $exam->id));
        $takeResponse->assertOk();
    }
}
