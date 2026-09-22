<?php

namespace Tests\Feature;

use App\Models\Stage;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Stage::firstOrCreate(
            ['id' => 1],
            ['grade_level' => 122, 'name_ar' => 'الثانوية العامة - الفرع العلمي', 'label_ar' => 'الثانوية العامة - الفرع العلمي']
        );
    }

    protected function createAdmin(): User
    {
        return User::create([
            'name'     => 'المدير العام',
            'email'    => 'admin@test.ps',
            'password' => Hash::make('AdminPass123'),
            'role'     => 'admin',
        ]);
    }

    protected function createTestStudent(array $overrides = []): Student
    {
        return Student::create(array_merge([
            'name_ar'        => 'طالب تجريبي',
            'name_en'        => 'Test Student',
            'email'          => 'student@test.ps',
            'password'       => Hash::make('StudentPass123'),
            'plain_password' => 'StudentPass123',
            'nid'            => '900000001',
            'age'            => 18,
            'phone'          => '0599000001',
            'stage_id'       => 1,
            'status'         => 'active',
        ], $overrides));
    }

    public function test_admin_creating_student_persists_plain_password_and_hash(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->postJson(route('admin.students.save'), [
            'name_ar'  => 'محمد أحمد الفلسطيني',
            'email'    => 'mohammed.pal@tawjihi.ps',
            'password' => 'Palestine2026!',
            'nid'      => '900112233',
            'stage_id' => 1,
            'gender'   => 'male',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $student = Student::where('email', 'mohammed.pal@tawjihi.ps')->first();
        $this->assertNotNull($student);
        $this->assertEquals('Palestine2026!', $student->plain_password);
        $this->assertTrue(Hash::check('Palestine2026!', $student->password));
    }

    public function test_student_updating_password_syncs_plain_password(): void
    {
        $student = $this->createTestStudent([
            'email'          => 'khaled@test.ps',
            'nid'            => '988776655',
            'password'       => Hash::make('OldPass123'),
            'plain_password' => 'OldPass123',
        ]);

        $response = $this->actingAs($student, 'student')->postJson(route('student.profile.updatePassword'), [
            'old_password' => 'OldPass123',
            'new_password' => 'BrandNewPass999',
        ]);

        $response->assertStatus(200);
        $student->refresh();
        $this->assertEquals('BrandNewPass999', $student->plain_password);
        $this->assertTrue(Hash::check('BrandNewPass999', $student->password));
    }

    public function test_admin_can_reset_student_password_via_ajax(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createTestStudent([
            'email'          => 'sara@test.ps',
            'nid'            => '977665544',
            'password'       => Hash::make('InitialPwd'),
            'plain_password' => null, // حساب قديم لا يملك plain_password
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.students.resetPassword', $student->id), [
            'new_password' => 'SecretAdminSet#2026',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'        => true,
            'plain_password' => 'SecretAdminSet#2026',
        ]);

        $student->refresh();
        $this->assertEquals('SecretAdminSet#2026', $student->plain_password);
        $this->assertTrue(Hash::check('SecretAdminSet#2026', $student->password));
    }

    public function test_admin_can_reset_teacher_password_via_ajax(): void
    {
        $admin = $this->createAdmin();
        $teacher = User::create([
            'name'           => 'الأستاذ أحمد',
            'email'          => 'teacher@test.ps',
            'password'       => Hash::make('TeacherPass1'),
            'role'           => 'teacher',
            'plain_password' => null,
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.teachers.resetPassword', $teacher->id), [
            'new_password' => 'TeacherResetPass#2026',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'        => true,
            'plain_password' => 'TeacherResetPass#2026',
        ]);

        $teacher->refresh();
        $this->assertEquals('TeacherResetPass#2026', $teacher->plain_password);
        $this->assertTrue(Hash::check('TeacherResetPass#2026', $teacher->password));
    }

    public function test_profile_views_do_not_render_fake_123456_when_password_is_encrypted(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createTestStudent([
            'email'          => 'tariq@test.ps',
            'nid'            => '966554433',
            'password'       => Hash::make('CustomComplexPassword!#'),
            'plain_password' => null, // مشفرة وغير معروفة كنص صريح
        ]);

        $profileResponse = $this->actingAs($admin)->get(route('admin.students.profile', $student->id));
        $profileResponse->assertStatus(200);
        $profileResponse->assertDontSee('>123456<');
        $profileResponse->assertSee('مشفرة بأمان');

        $showResponse = $this->actingAs($admin)->get(route('admin.students.show', $student->id));
        $showResponse->assertStatus(200);
        $showResponse->assertDontSee('>123456<');
        $showResponse->assertSee('مشفرة بأمان');
    }

    public function test_profile_views_render_actual_plain_password_when_available(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createTestStudent([
            'email'          => 'omar@test.ps',
            'nid'            => '955443322',
            'password'       => Hash::make('VerifiedPlainPass123'),
            'plain_password' => 'VerifiedPlainPass123',
        ]);

        $profileResponse = $this->actingAs($admin)->get(route('admin.students.profile', $student->id));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('VerifiedPlainPass123');

        $showResponse = $this->actingAs($admin)->get(route('admin.students.show', $student->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('VerifiedPlainPass123');
    }
}
