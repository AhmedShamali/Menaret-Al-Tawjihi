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

        \App\Models\Enrollment::create([
            'student_id'  => $this->student->id,
            'subject_id'  => $this->subject->id,
            'status'      => 'active',
            'access_mode' => 'all',
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

    /**
     * اختبار تخزين صور خيارات أسئلة الاختيار من متعدد وعرضها للطالب
     */
    public function test_mcq_options_images_storage_and_rendering()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $exam = Exam::create([
            'teacher_id'       => $this->teacher->id,
            'subject_id'       => $this->subject->id,
            'stage_id'         => $this->stage->id,
            'title'            => 'اختبار الرياضيات والرسوم الهندسية',
            'duration_minutes' => 45,
        ]);

        $fileA = \Illuminate\Http\UploadedFile::fake()->create('opt_a.png', 10, 'image/png');
        $fileB = \Illuminate\Http\UploadedFile::fake()->create('opt_b.png', 10, 'image/png');

        $response = $this->actingAs($this->teacher)->post(route('teacher.exams.store'), [
            'title'            => 'اختبار الأشكال الهندسية',
            'subject_id'       => $this->subject->id,
            'stage_id'         => $this->stage->id,
            'duration_minutes' => 45,
            'questions'        => [
                [
                    'type'           => 'mcq',
                    'question_text'  => 'أي من الأشكال التالية يمثل دالة متزايدة؟',
                    'a'              => 'الشكل الأول',
                    'a_image'        => $fileA,
                    'b'              => 'الشكل الثاني',
                    'b_image'        => $fileB,
                    'c'              => 'الشكل الثالث',
                    'd'              => 'الشكل الرابع',
                    'correct_answer' => 'a',
                    'points'         => 10,
                ]
            ]
        ]);

        $response->assertCreated();

        $savedExam = Exam::where('title', 'اختبار الأشكال الهندسية')->firstOrFail();
        $q = $savedExam->questions()->firstOrFail();

        $this->assertNotNull($q->a_image);
        $this->assertNotNull($q->b_image);
        $this->assertNull($q->c_image);

        $this->assertStringContainsString('question_options', $q->getOptionImageUrl('a'));
        $this->assertStringContainsString('question_options', $q->getOptionImageUrl('b'));
        $this->assertNull($q->getOptionImageUrl('c'));

        // فحص ظهور صورة الخيار في قاعة تقديم الاختبار للطالب
        $takeView = $this->actingAs($this->student, 'student')->get(route('student.exams.take', $savedExam->id));
        $takeView->assertOk();
        $takeView->assertSee('الشكل الأول');
        $takeView->assertSee('ed-opt-img-wrapper');
    }

    /**
     * اختبار قيود جدولة وتوقيت الاختبارات (قادم، متاح، منتهي)
     */
    public function test_exam_scheduling_restrictions_for_student()
    {
        // 1. اختبار لم يبدأ وقته بعد (قادم في المستقبل)
        $futureExam = Exam::create([
            'teacher_id'       => $this->teacher->id,
            'subject_id'       => $this->subject->id,
            'stage_id'         => $this->stage->id,
            'title'            => 'امتحان الكيمياء المستقبلي',
            'duration_minutes' => 60,
            'starts_at'        => now()->addDays(2),
            'ends_at'          => now()->addDays(3),
        ]);

        Question::create([
            'exam_id'       => $futureExam->id,
            'type'          => 'mcq',
            'question_text' => 'سؤال مستقبلي',
            'a'             => 'خيار 1',
            'b'             => 'خيار 2',
            'correct_answer'=> 'a',
            'points'        => 5,
        ]);

        $this->assertTrue($futureExam->isUpcoming());
        $this->assertFalse($futureExam->isOpen());

        // محاولة دخول الطالب للاختبار القادم تمنعه وتعيد توجيهه مع رسالة تنبيه
        $takeFuture = $this->actingAs($this->student, 'student')->get(route('student.exams.take', $futureExam->id));
        $takeFuture->assertRedirect(route('student.exams.index'));
        $takeFuture->assertSessionHas('error');

        // 2. اختبار منتهي الفترة الزمنية (في الماضي)
        $expiredExam = Exam::create([
            'teacher_id'       => $this->teacher->id,
            'subject_id'       => $this->subject->id,
            'stage_id'         => $this->stage->id,
            'title'            => 'امتحان الفيزياء المنتهي',
            'duration_minutes' => 60,
            'starts_at'        => now()->subDays(3),
            'ends_at'          => now()->subHour(),
        ]);

        Question::create([
            'exam_id'       => $expiredExam->id,
            'type'          => 'mcq',
            'question_text' => 'سؤال قديم',
            'a'             => 'خيار 1',
            'b'             => 'خيار 2',
            'correct_answer'=> 'a',
            'points'        => 5,
        ]);

        $this->assertTrue($expiredExam->isExpired());
        $this->assertFalse($expiredExam->isOpen());

        // محاولة دخول الطالب للاختبار المنتهي تمنعه وتعيد توجيهه
        $takeExpired = $this->actingAs($this->student, 'student')->get(route('student.exams.take', $expiredExam->id));
        $takeExpired->assertRedirect(route('student.exams.index'));
        $takeExpired->assertSessionHas('error');

        // 3. اختبار متاح حالياً
        $activeExam = Exam::create([
            'teacher_id'       => $this->teacher->id,
            'subject_id'       => $this->subject->id,
            'stage_id'         => $this->stage->id,
            'title'            => 'امتحان متاح الآن',
            'duration_minutes' => 60,
            'starts_at'        => now()->subHour(),
            'ends_at'          => now()->addHours(5),
        ]);

        Question::create([
            'exam_id'       => $activeExam->id,
            'type'          => 'mcq',
            'question_text' => 'سؤال متاح',
            'a'             => 'خيار 1',
            'b'             => 'خيار 2',
            'correct_answer'=> 'a',
            'points'        => 5,
        ]);

        $this->assertTrue($activeExam->isOpen());

        // الطالب يدخل بنجاح للاختبار المتاح
        $takeActive = $this->actingAs($this->student, 'student')->get(route('student.exams.take', $activeExam->id));
        $takeActive->assertOk();
    }

    /**
     * اختبار صارم: الطالب يرى ويقدم اختبارات المواد المسجل بها فقط، ويُحجب عنه أي اختبار لمادة غير مسجل بها
     */
    public function test_student_only_sees_and_takes_exams_for_enrolled_subjects_and_non_enrolled_are_strictly_forbidden()
    {
        // مادة ثانية غير مسجل بها الطالب نهائياً (الدراسات التاريخية)
        $unregisteredSubject = Subject::create([
            'stage_id'    => $this->stage->id,
            'name_ar'     => 'الدراسات التاريخية',
            'subject_key' => 'history_unregistered',
            'price_ils'   => 120,
        ]);

        $unregisteredExam = Exam::create([
            'teacher_id'       => $this->teacher->id,
            'subject_id'       => $unregisteredSubject->id,
            'stage_id'         => $this->stage->id,
            'title'            => 'اختبار الدراسات التاريخية التجريبي',
            'duration_minutes' => 60,
        ]);

        // 1. فحص لوحة تحكم الطالب (Dashboard)
        $dashResponse = $this->actingAs($this->student, 'student')->get(route('student.dashboard'));
        $dashResponse->assertOk();
        $dashResponse->assertDontSee('اختبار الدراسات التاريخية التجريبي');

        // 2. فحص صفحة قائمة الاختبارات (My Exams)
        $examsIndexResponse = $this->actingAs($this->student, 'student')->get(route('student.exams.index'));
        $examsIndexResponse->assertOk();
        $examsIndexResponse->assertDontSee('اختبار الدراسات التاريخية التجريبي');

        // 3. محاولة فتح الاختبار غير المسجل بها -> يجب أن يتم منعه وتوجيهه لقائمة الاختبارات مع رسالة خطأ
        $takeForbidden = $this->actingAs($this->student, 'student')->get(route('student.exams.take', $unregisteredExam->id));
        $takeForbidden->assertRedirect(route('student.exams.index'));
        $takeForbidden->assertSessionHas('error');

        // 4. محاولة تسليم إجابات للاختبار غير المسجل به -> يجب أن يتم الرفض بـ 403 Forbidden
        $submitForbidden = $this->actingAs($this->student, 'student')->post(route('student.exams.submit', $unregisteredExam->id), [
            'answers' => [],
        ]);
        $submitForbidden->assertStatus(403);
    }

    /**
     * اختبار تنسيق ساعات ومواعيد فتح الاختبار والشارات الزمنية
     */
    public function test_exam_opening_hours_and_timing_schedule_formatting()
    {
        // 1. اختبار متاح دائماً بدون مواعيد
        $openExam = Exam::create([
            'teacher_id'       => $this->teacher->id,
            'subject_id'       => $this->subject->id,
            'stage_id'         => $this->stage->id,
            'title'            => 'اختبار متاح دائماً بدون قيود',
            'duration_minutes' => 30,
            'starts_at'        => null,
            'ends_at'          => null,
        ]);

        $this->assertStringContainsString('متاح للتقديم دائماً', $openExam->formatted_timing_text);
        $this->assertEquals('always_open', $openExam->timing_badge_data['status']);

        // 2. اختبار محدد بساعات نافذة في نفس اليوم
        $scheduledExam = Exam::create([
            'teacher_id'       => $this->teacher->id,
            'subject_id'       => $this->subject->id,
            'stage_id'         => $this->stage->id,
            'title'            => 'اختبار مجدول بساعات محددة',
            'duration_minutes' => 60,
            'starts_at'        => \Carbon\Carbon::parse('2026-10-15 09:00:00', 'Asia/Gaza'),
            'ends_at'          => \Carbon\Carbon::parse('2026-10-15 13:00:00', 'Asia/Gaza'),
        ]);

        $timingText = $scheduledExam->formatted_timing_text;
        $this->assertStringContainsString('2026/10/15', $timingText);
        $this->assertStringContainsString('09:00 ص', $timingText);
        $this->assertStringContainsString('01:00 م', $timingText);

        // 3. التحقق من ظهور ساعات ومواعيد الفتح في صفحة الاختبارات وقاعة الاختبار
        $indexResponse = $this->actingAs($this->student, 'student')->get(route('student.exams.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('ساعات وموعد فتح الاختبار');
        $indexResponse->assertSee($openExam->formatted_timing_text);

        $takeResponse = $this->actingAs($this->student, 'student')->get(route('student.exams.take', $openExam->id));
        $takeResponse->assertOk();
        $takeResponse->assertSee('ساعات وموعد فتح الاختبار الأكاديمي');
        $takeResponse->assertSee($openExam->formatted_timing_text);
    }
}
