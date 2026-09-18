<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Stage;
use App\Models\Subject;

class PlatformAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic stage & subject for views
        $stage = Stage::create([
            'grade_level' => '12',
            'label_ar' => 'العلمي',
        ]);

        Subject::create([
            'subject_key' => 'math',
            'name_ar' => 'الرياضيات',
            'stage_id' => $stage->id,
            'color' => '#1e3a8a',
            'price_ils' => 150,
        ]);
    }

    public function test_language_switcher_toggles_locale(): void
    {
        // Switch to English
        $response = $this->get('/lang/en');
        $response->assertSessionHas('locale', 'en');

        // Switch to Arabic
        $response = $this->get('/lang/ar');
        $response->assertSessionHas('locale', 'ar');
    }

    public function test_public_pages_return_200_ok(): void
    {
        $routes = [
            '/',
            '/login',
            '/register',
            '/tawjihi-calculator',
            '/tawjihi-archive',
            '/tawjihi-formulas',
            '/contact',
            '/faq',
            '/privacy',
            '/terms',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $this->assertEquals(200, $response->getStatusCode(), "Failed loading route: {$route}");
        }
    }

    public function test_admin_pages_with_authenticated_admin(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_test@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $adminRoutes = [
            '/admin/dashboard',
            '/admin/academic-inquiries',
            '/admin/settings',
            '/admin/subscriptions/monthly',
            '/admin/students/records/all',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $this->assertContains($response->getStatusCode(), [200, 302], "Admin route {$route} returned {$response->getStatusCode()}");
        }
    }

    public function test_teacher_pages_with_authenticated_teacher(): void
    {
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher_test@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'teacher',
        ]);

        $this->actingAs($teacher);

        $teacherRoutes = [
            '/teacher/videos',
            '/teacher/files',
            '/teacher/inbox',
            '/teacher/visibility',
        ];

        foreach ($teacherRoutes as $route) {
            $response = $this->get($route);
            $this->assertContains($response->getStatusCode(), [200, 302], "Teacher route {$route} returned {$response->getStatusCode()}");
        }
    }

    public function test_student_pages_with_authenticated_student(): void
    {
        $stage = Stage::first();

        $student = Student::create([
            'name_ar' => 'طالب تجريبي',
            'name_en' => 'Test Student',
            'nid' => '400000001',
            'email' => 'student_test@tawjihi.ps',
            'password' => bcrypt('password123'),
            'phone' => '0599000000',
            'age' => 17,
            'gender' => 'male',
            'stage_id' => $stage->id,
            'status' => 'active',
        ]);

        // Student guard login
        auth('student')->login($student);
        session(['student_id' => $student->id]);

        $studentRoutes = [
            '/student/dashboard',
            '/student/courses/catalog',
            '/student/study-planner',
            '/student/flashcards',
            '/student/channels',
            '/student/notifications',
            '/student/subscriptions',
            '/student/leaderboard',
            '/student/my-exams',
            '/student/profile',
        ];

        foreach ($studentRoutes as $route) {
            $response = $this->get($route);
            $this->assertContains($response->getStatusCode(), [200, 302], "Student route {$route} returned {$response->getStatusCode()}");
        }
    }

    public function test_google_auth_and_registration_are_completely_removed(): void
    {
        // 1. Verify login page does not contain Google OAuth button or route
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200);
        $loginRes->assertDontSee('auth.google');
        $loginRes->assertDontSee('الدخول بحساب Google');
        $loginRes->assertDontSee('googleAuthSection');

        // 2. Verify register page does not contain Google ID or Google badge
        $regRes = $this->get('/register');
        $regRes->assertStatus(200);
        $regRes->assertDontSee('name="google_id"', false);
        $regRes->assertDontSee('btn-google-auth');
        $regRes->assertDontSee('صورة Google المعتمدة');

        // 3. Verify Google auth routes are unregistered (404)
        $this->get('/auth/google')->assertStatus(404);
        $this->get('/auth/google/callback')->assertStatus(404);
    }
}

