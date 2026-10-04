<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Stage;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class AdminStudentTeacherInquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_inquiry_to_teacher_creates_correct_admin_notification_and_redirects_to_inbox(): void
    {
        $admin = User::create([
            'name'     => 'م. أحمد شمالي (مدير المنصة)',
            'email'    => 'admin@stepvoro.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $teacher = User::create([
            'name'     => 'أ. خالد العلمي',
            'email'    => 'teacher@stepvoro.com',
            'password' => bcrypt('password123'),
            'role'     => 'teacher',
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
        ]);

        // 1. Student sends inquiry to teacher
        $response = $this->actingAs($student, 'student')->postJson(route('student.send.teacher'), [
            'teacher_id' => $teacher->id,
            'message'    => 'أستاذ لو سمحت ما هو حل السؤال الثالث في درس الديناميكا؟',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Check message created in database
        $msg = Message::where('student_id', $student->id)->where('teacher_id', $teacher->id)->first();
        $this->assertNotNull($msg);
        $this->assertEquals('student', $msg->sender_type);
        $this->assertEquals($teacher->id, $msg->teacher_id);
        $this->assertEquals('أستاذ لو سمحت ما هو حل السؤال الثالث في درس الديناميكا؟', $msg->message);

        // Check Admin Notification
        $adminNotif = DB::table('notifications')->where('notifiable_id', $admin->id)->first();
        $this->assertNotNull($adminNotif);
        $data = json_decode($adminNotif->data, true);
        $this->assertStringContainsString('استفسار دراسي جديد من طالب', $data['title']);
        $this->assertStringContainsString((string)$student->id, $data['action_url']);
        $this->assertStringContainsString('inbox', $data['action_url']);

        // 2. Admin clicks on notification -> MUST redirect to admin.messages.index with student_id, NOT academic-inquiries!
        $clickResponse = $this->actingAs($admin, 'web')->get(route('notifications.open', ['id' => $adminNotif->id]));
        $clickResponse->assertRedirect();
        $location = $clickResponse->headers->get('Location');
        $this->assertStringContainsString('inbox', $location);
        $this->assertStringContainsString('student_id=' . $student->id, $location);
        $this->assertStringNotContainsString('academic-inquiries', $location);

        // 3. Admin fetches messages for this student via AJAX -> MUST return the student's question with teacher name!
        $fetchResponse = $this->actingAs($admin, 'web')->getJson(route('admin.fetch', ['student_id' => $student->id]));
        $fetchResponse->assertStatus(200);
        $fetchResponse->assertJson(['status' => 'success']);
        
        $messages = $fetchResponse->json('messages');
        $this->assertCount(1, $messages);
        $this->assertEquals('أستاذ لو سمحت ما هو حل السؤال الثالث في درس الديناميكا؟', $messages[0]['message']);
        $this->assertEquals('student', $messages[0]['sender_type']);
        $this->assertEquals($teacher->id, $messages[0]['teacher_id']);
        $this->assertEquals($teacher->name, $messages[0]['teacher_name']);

        // 4. Verify message is marked as read after fetch
        $this->assertTrue((bool)$msg->fresh()->is_read);

        // 5. Admin inbox view loads properly with the student conversation
        $inboxViewResponse = $this->actingAs($admin, 'web')->get(route('admin.messages.index', ['student_id' => $student->id]));
        $inboxViewResponse->assertStatus(200);
        $inboxViewResponse->assertSee($student->name_ar);
        $inboxViewResponse->assertSee('أستاذ لو سمحت ما هو حل السؤال');
    }
}
