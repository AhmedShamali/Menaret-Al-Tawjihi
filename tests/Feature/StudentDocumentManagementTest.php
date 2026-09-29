<?php

namespace Tests\Feature;

use App\Models\Stage;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentDocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Stage $stage;
    protected User $admin;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('supabase');

        $this->stage = Stage::create([
            'label_ar'    => 'الفرع العلمي',
            'label_en'    => 'Scientific Branch',
            'grade_level' => 121,
            'is_active'   => true,
        ]);

        $this->admin = User::create([
            'name'     => 'الإدارة العامة',
            'email'    => 'admin@platform.test',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $this->student = Student::create([
            'name_ar'        => 'أحمد محمود العبد',
            'name_en'        => 'Ahmed Mahmoud',
            'nid'            => '401234567',
            'email'          => 'ahmed@student.test',
            'password'       => bcrypt('password123'),
            'plain_password' => 'password123',
            'age'            => 18,
            'gender'         => 'ذكر',
            'phone'          => '0599123456',
            'stage_id'       => $this->stage->id,
            'status'         => 'active',
            'id_photo'       => 'students/ids/sample_id.pdf',
        ]);

        // Mock fake file in public disk
        Storage::disk('public')->put('students/ids/sample_id.pdf', '%PDF-1.4 sample content');
    }

    public function test_student_model_identifies_pdf_documents()
    {
        $this->assertTrue($this->student->is_id_pdf);
        $this->assertStringContainsString('401234567', $this->student->id_photo_download_name);
        $this->assertStringEndsWith('.pdf', $this->student->id_photo_download_name);

        $imageStudent = Student::create([
            'name_ar'     => 'سارة حسن',
            'name_en'     => 'Sara Hassan',
            'nid'         => '407654321',
            'email'       => 'sara@student.test',
            'password'    => bcrypt('password123'),
            'age'         => 18,
            'gender'      => 'أنثى',
            'phone'       => '0599654321',
            'stage_id'    => $this->stage->id,
            'status'      => 'active',
            'id_photo'    => 'students/ids/sample_card.jpg',
        ]);

        $this->assertFalse($imageStudent->is_id_pdf);
        $this->assertStringEndsWith('.jpg', $imageStudent->id_photo_download_name);
    }

    public function test_admin_can_download_student_document()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.students.document.download', [$this->student->id, 'id_photo']));

        $response->assertStatus(200);
        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment', $disposition);
        $this->assertStringContainsString('401234567', $disposition);
    }

    public function test_admin_can_view_student_document_inline()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.students.document.view', [$this->student->id, 'id_photo']));

        $response->assertStatus(200);
        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('inline', $disposition);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_student_can_download_their_own_document()
    {
        $response = $this->actingAs($this->student, 'student')
            ->get(route('student.document.download', 'id_photo'));

        $response->assertStatus(200);
        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment', $disposition);
    }

    public function test_guest_cannot_download_student_documents()
    {
        $response = $this->get(route('admin.students.document.download', [$this->student->id, 'id_photo']));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_registration_accepts_pdf_and_images_up_to_10mb()
    {
        $pdfFile = UploadedFile::fake()->create('id_card.pdf', 4096, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.students.save'), [
                'name_ar'  => 'خالد عمر علي',
                'name_en'  => 'Khaled Omar',
                'email'    => 'khaled@platform.test',
                'password' => 'secret123',
                'nid'      => '409876543',
                'stage_id' => $this->stage->id,
                'phone'    => '0599887766',
                'id_photo' => $pdfFile,
            ]);

        $response->assertStatus(200);
        $created = Student::where('nid', '409876543')->first();
        $this->assertNotNull($created);
        $this->assertNotNull($created->id_photo);
        $this->assertTrue($created->is_id_pdf);
    }
}
