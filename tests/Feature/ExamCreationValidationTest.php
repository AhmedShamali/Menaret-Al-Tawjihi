<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExamCreationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $stage = \App\Models\Stage::create([
            'grade_level' => 12,
            'label_ar'    => 'الثانوية العامة',
        ]);

        $this->subject = Subject::create([
            'stage_id'    => $stage->id,
            'name_ar'     => 'الرياضيات',
            'subject_key' => 'math',
            'price_ils'   => 0,
        ]);

        $this->teacher = User::create([
            'name'       => 'الأستاذ أحمد',
            'email'      => 'teacher_test@platform.ps',
            'password'   => bcrypt('password123'),
            'role'       => 'teacher',
            'subject_id' => $this->subject->id,
            'is_approved'=> 1,
        ]);
    }

    public function test_fails_with_arabic_message_when_question_has_no_text_and_no_image()
    {
        $response = $this->actingAs($this->teacher)->postJson(route('teacher.exams.store'), [
            'title' => 'اختبار تجريبي',
            'subject_id' => $this->subject->id,
            'duration_minutes' => 60,
            'questions' => [
                [
                    'type' => 'mcq',
                    'question_text' => 'السؤال الأول',
                    'points' => 5,
                ],
                [
                    'type' => 'mcq',
                    'question_text' => '', // السؤال الثاني فارغ
                    'points' => 5,
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['questions.1.question_text']);
        $errors = $response->json('errors');
        $this->assertTrue(str_contains($errors['questions.1.question_text'][0], '2'));
        $this->assertTrue(str_contains($errors['questions.1.question_text'][0], 'يرجى كتابة نص السؤال'));
    }

    public function test_succeeds_when_question_has_image_even_without_text()
    {
        $fakeImage = UploadedFile::fake()->create('diagram.png', 10, 'image/png');

        $response = $this->actingAs($this->teacher)->postJson(route('teacher.exams.store'), [
            'title' => 'اختبار تجريبي بالصور',
            'subject_id' => $this->subject->id,
            'duration_minutes' => 60,
            'questions' => [
                [
                    'type' => 'mcq',
                    'question_text' => 'سؤال أول عادي',
                    'points' => 5,
                    'a' => '1',
                    'b' => '2',
                    'correct_answer' => 'a'
                ],
                [
                    'type' => 'mcq',
                    'question_text' => '', // سؤال بصورة فقط
                    'image' => $fakeImage,
                    'points' => 5,
                    'a' => '1',
                    'b' => '2',
                    'correct_answer' => 'b'
                ]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('questions', [
            'question_text' => 'انظر الصورة المرفقة',
        ]);
    }
}
