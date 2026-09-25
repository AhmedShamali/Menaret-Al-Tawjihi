<?php

namespace Tests\Feature;

use App\Models\ActivationCode;
use App\Models\Stage;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected Stage $stage;
    protected Subject $subject;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stage = Stage::create([
            'grade_level' => 12,
            'label_ar'    => 'العلمي',
        ]);

        $this->subject = Subject::create([
            'stage_id'    => $this->stage->id,
            'name_ar'     => 'الفيزياء',
            'subject_key' => 'physics',
            'price_ils'   => 150,
        ]);

        $this->student = Student::create([
            'name_ar'  => 'طالب الموبايل',
            'name_en'  => 'Mobile Student',
            'nid'      => '400555666',
            'email'    => 'mobile@tawjihi.ps',
            'password' => bcrypt('secret123'),
            'phone'    => '0599555666',
            'age'      => 18,
            'gender'   => 'male',
            'stage_id' => $this->stage->id,
            'status'   => 'active',
        ]);
    }

    public function test_api_login_returns_token_and_student_info(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'login'    => 'mobile@tawjihi.ps',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['token', 'student']);
    }

    public function test_api_dashboard_with_bearer_token_succeeds(): void
    {
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'login'    => 'mobile@tawjihi.ps',
            'password' => 'secret123',
        ]);
        $token = $loginRes->json('token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/student/dashboard');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_api_dashboard_without_auth_fails(): void
    {
        $response = $this->getJson('/api/v1/student/dashboard');
        $response->assertStatus(401);
    }

    public function test_api_redeem_voucher_with_bearer_token_activates_enrollment(): void
    {
        $voucher = ActivationCode::create([
            'code'          => 'TAWJIHI-TEST-999',
            'subject_id'    => $this->subject->id,
            'access_mode'   => 'all',
            'price_ils'     => 150,
            'duration_days' => 365,
            'is_used'       => false,
        ]);

        $loginRes = $this->postJson('/api/v1/auth/login', [
            'login'    => 'mobile@tawjihi.ps',
            'password' => 'secret123',
        ]);
        $token = $loginRes->json('token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/student/redeem-voucher', [
                'code' => 'TAWJIHI-TEST-999',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'status'     => 'active',
        ]);
    }
}
