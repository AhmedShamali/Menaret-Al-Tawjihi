<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Stage;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Support\SchemaHealer;

class PlatformIntegrityAndPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_healer_runs_cleanly(): void
    {
        // Must execute without any exceptions
        SchemaHealer::heal();
        $this->assertTrue(true);
    }

    public function test_admin_student_profile_loads_successfully_without_fee_amount_column_error(): void
    {
        $admin = User::create([
            'name'     => 'م. أحمد شمالي (المدير العام)',
            'email'    => 'admin@stepvoro.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $stage = Stage::create([
            'grade_level' => '122',
            'name'        => 'علمي',
            'name_ar'     => 'الفرع العلمي - الثانوية العامة',
            'label_ar'    => 'توجيهي علمي',
        ]);

        $student = Student::create([
            'name_ar'     => 'محمد حمادة نور الدين عابد',
            'name_en'     => 'Mohamed Hamada',
            'email'       => 'mohamed15@test.ps',
            'nid'         => '955112233',
            'phone'       => '0599575237',
            'gender'      => 'male',
            'age'         => 18,
            'password'    => bcrypt('password123'),
            'stage_id'    => $stage->id,
            'status'      => 'active',
            'region'      => 'gaza',
        ]);

        $subject = Subject::create([
            'subject_key' => 'math_12',
            'name'        => 'الرياضيات',
            'name_ar'     => 'الرياضيات',
            'stage_id'    => $stage->id,
            'price_ils'   => 500,
        ]);

        Enrollment::create([
            'student_id'     => $student->id,
            'subject_id'     => $subject->id,
            'status'         => 'active',
            'access_mode'    => 'all',
            'payment_status' => 'admin_grant',
            'activated_at'   => now(),
        ]);

        // Simulating the exact request from Screenshot 1: GET /admin/students/{id}
        $response = $this->actingAs($admin, 'web')->get(route('admin.students.show', $student->id));
        $response->assertStatus(200);
        $response->assertSee($student->name_ar);
        $response->assertSee('الموقف المالي الفصلي');
    }

    public function test_admin_students_records_all_renders_clean_arabic_pagination_without_raw_keys(): void
    {
        $admin = User::create([
            'name'     => 'م. أحمد شمالي (المدير العام)',
            'email'    => 'admin2@stepvoro.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $stage = Stage::create([
            'grade_level' => '122',
            'name'        => 'علمي',
            'name_ar'     => 'الفرع العلمي - الثانوية العامة',
            'label_ar'    => 'توجيهي علمي',
        ]);

        // Create 15 students to trigger pagination (> 10 per page)
        for ($i = 1; $i <= 15; $i++) {
            Student::create([
                'name_ar'     => 'طالب تجريبي ' . $i,
                'name_en'     => 'Test Student ' . $i,
                'email'       => "student_{$i}@test.ps",
                'nid'         => '9551100' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'phone'       => '05990000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'gender'      => 'male',
                'age'         => 18,
                'password'    => bcrypt('password123'),
                'stage_id'    => $stage->id,
                'status'      => 'active',
            ]);
        }

        // Simulating the exact request from Screenshot 2: GET /admin/students/records/all
        $response = $this->actingAs($admin, 'web')->get(route('admin.students.profile_all'));
        $response->assertStatus(200);

        // Verify clean Arabic pagination labels are rendered
        $response->assertSee('السابق');
        $response->assertSee('التالي');
        $response->assertSee('من أصل');

        // MUST NOT contain untranslated raw translation keys
        $response->assertDontSee('pagination.previous');
        $response->assertDontSee('pagination.next');
    }

    public function test_admin_system_heal_database_route_works(): void
    {
        $admin = User::create([
            'name'     => 'م. أحمد شمالي (المدير العام)',
            'email'    => 'admin3@stepvoro.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $response = $this->actingAs($admin, 'web')->get(route('admin.system.heal'));
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
