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

    public function test_student_deletion_with_all_cascading_relations_succeeds(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_test_del@menaret-tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $stage = Stage::first();
        $student = Student::create([
            'name_ar' => 'طالب للحذف',
            'name_en' => 'Delete Student',
            'nid' => '400000099',
            'email' => 'delete_me@tawjihi.ps',
            'password' => bcrypt('password123'),
            'phone' => '0599000099',
            'age' => 17,
            'gender' => 'ذكر',
            'stage_id' => $stage->id,
            'status' => 'pending',
        ]);

        // Create child records
        $payment = \App\Models\Payment::create([
            'student_id' => $student->id,
            'transaction_number' => 'TXN-TEST-123',
            'gateway' => 'jawwal_pay',
            'amount' => 150,
            'currency' => 'ILS',
            'status' => 'pending',
        ]);

        if (\Illuminate\Support\Facades\Schema::hasTable('student_monthly_subscriptions')) {
            \App\Models\StudentMonthlySubscription::create([
                'student_id' => $student->id,
                'academic_year' => 2026,
                'month' => 9,
                'month_name_ar' => 'أيلول',
                'status' => 'waiting_approval',
                'payment_id' => $payment->id,
            ]);
        }

        $response = $this->actingAs($admin)->deleteJson("/admin/students/{$student->id}");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }

    public function test_pending_payment_page_and_routes_accessible_for_pending_student(): void
    {
        $stage = Stage::first();
        $pendingStudent = Student::create([
            'name_ar' => 'طالب معلق',
            'name_en' => 'Pending Student',
            'nid' => '400000088',
            'email' => 'pending_std@tawjihi.ps',
            'password' => bcrypt('password123'),
            'phone' => '0599000088',
            'age' => 17,
            'gender' => 'ذكر',
            'stage_id' => $stage->id,
            'status' => 'pending',
        ]);

        auth('student')->login($pendingStudent);

        // GET /student/pending-approval must be 200 OK
        $this->get('/student/pending-approval')->assertStatus(200);

        // GET /student/pending-payment must also be 200 OK (no 405 / 419)
        $this->get('/student/pending-payment')->assertStatus(200);
    }

    public function test_admin_can_approve_pending_student_successfully(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin_approve@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $stage = Stage::first();
        $student = Student::create([
            'name_ar' => 'طالب تحت المراجعة',
            'name_en' => 'Review Student',
            'nid' => '400112233',
            'email' => 'review_std@tawjihi.ps',
            'password' => bcrypt('password123'),
            'phone' => '0599112233',
            'age' => 18,
            'gender' => 'ذكر',
            'stage_id' => $stage->id,
            'status' => 'pending',
        ]);

        $payment = \App\Models\Payment::create([
            'student_id' => $student->id,
            'transaction_number' => 'TXN-APPROVE-TEST',
            'gateway' => 'jawwal_pay',
            'amount' => 150,
            'currency' => 'ILS',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->postJson("/admin/students/{$student->id}/approve");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('active', $student->fresh()->status);
        $this->assertEquals('completed', $payment->fresh()->status);
    }

    public function test_student_freeze_workflow_displays_reason_and_restricts_access(): void
    {
        $admin = User::create([
            'name' => 'Admin Freeze',
            'email' => 'admin_freeze@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $stage = Stage::first();
        $student = Student::create([
            'name_ar' => 'أحمد المجمد',
            'name_en' => 'Ahmed Frozen',
            'nid' => '400998877',
            'email' => 'frozen_std@tawjihi.ps',
            'password' => bcrypt('password123'),
            'phone' => '0599998877',
            'age' => 18,
            'gender' => 'ذكر',
            'stage_id' => $stage->id,
            'status' => 'active',
        ]);

        // 1. Admin freezes the student with a specific reason
        $reason = 'عدم سداد الرسوم الدراسية لشهر أيلول';
        $freezeResponse = $this->actingAs($admin)->postJson("/admin/students/toggle-status/{$student->id}", [
            'freeze_reason' => $reason
        ]);
        $freezeResponse->assertStatus(200);
        $freezeResponse->assertJson(['success' => true, 'status' => 'suspended']);

        $refreshedStudent = $student->fresh();
        $this->assertEquals('suspended', $refreshedStudent->status);
        $this->assertEquals($reason, $refreshedStudent->freeze_reason);

        // 2. Student logs in or attempts to visit /student/dashboard
        auth('student')->login($refreshedStudent);
        $dashResponse = $this->get('/student/dashboard');
        $dashResponse->assertRedirect('/student/pending-approval');

        // 3. Student visits /student/pending-approval and sees the freeze reason
        $pendingPage = $this->get('/student/pending-approval');
        $pendingPage->assertStatus(200);
        $pendingPage->assertSee($reason);
        $pendingPage->assertSee('0567897212'); // WhatsApp supervisor contact
    }

    public function test_admin_can_sync_student_subjects_via_json_and_form(): void
    {
        $admin = User::create([
            'name' => 'Admin Subject Sync',
            'email' => 'admin_sync@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $stage = Stage::first();
        $student = Student::create([
            'name_ar' => 'طالب مزامنة المواد',
            'name_en' => 'Sync Subject Student',
            'nid' => '400554433',
            'email' => 'sync_std@tawjihi.ps',
            'password' => bcrypt('password123'),
            'phone' => '0599554433',
            'age' => 18,
            'gender' => 'ذكر',
            'stage_id' => $stage->id,
            'status' => 'active',
        ]);

        $subjects = \App\Models\Subject::take(3)->get();
        $subjectIds = $subjects->pluck('id')->toArray();

        // 1. Test via JSON (AJAX)
        $responseJson = $this->actingAs($admin)->postJson("/admin/students/{$student->id}/sync-subjects", [
            'subject_ids' => $subjectIds
        ]);
        $responseJson->assertStatus(200);
        $responseJson->assertJson(['success' => true]);
        $this->assertEquals(count($subjectIds), $student->enrolledSubjects()->count());

        // 2. Test via Standard Form POST (Fallback)
        $responseForm = $this->actingAs($admin)->post("/admin/students/{$student->id}/sync-subjects", [
            'subject_ids' => array_slice($subjectIds, 0, 1)
        ]);
        $responseForm->assertRedirect();
        $this->assertEquals(1, $student->enrolledSubjects()->count());
    }
}




