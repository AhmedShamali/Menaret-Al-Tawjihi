<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Stage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_student_successfully(): void
    {
        $stage = Stage::create([
            'name' => 'العلمي',
            'label_ar' => 'الفرع العلمي',
            'label_en' => 'Scientific',
            'grade_level' => 122,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin_test_del@example.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $student = Student::create([
            'name_ar' => 'طالب للاختبار والحذف',
            'name_en' => 'Delete Test Student',
            'nid' => '999999998',
            'email' => 'student_to_delete@example.com',
            'password' => bcrypt('password123'),
            'age' => 18,
            'gender' => 'ذكر',
            'stage_id' => $stage->id,
            'status' => 'active',
            'phone' => '0599999999'
        ]);

        $response = $this->actingAs($admin, 'web')
            ->deleteJson('/admin/students/' . $student->id);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }
}
