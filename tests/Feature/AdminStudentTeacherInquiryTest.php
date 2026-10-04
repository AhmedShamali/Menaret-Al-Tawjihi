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

    public function test_student_inquiry_to_teacher_notifies_only_teacher_and_does_not_appear_in_admin_inbox(): void
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

        // 2. Check Teacher receives notification, but Admin does NOT receive notification
        $teacherNotif = DB::table('notifications')->where('notifiable_id', $teacher->id)->first();
        $this->assertNotNull($teacherNotif);
        $teacherData = json_decode($teacherNotif->data, true);
        $this->assertStringContainsString('استفسار', $teacherData['title']);

        $adminNotif = DB::table('notifications')->where('notifiable_id', $admin->id)->first();
        $this->assertNull($adminNotif, 'Admin should not be spammed with teacher-student questions');

        // 3. Admin fetches messages for this student via AJAX -> MUST ONLY return admin support messages (empty for teacher inquiries)
        $fetchResponse = $this->actingAs($admin, 'web')->getJson(route('admin.fetch', ['student_id' => $student->id]));
        $fetchResponse->assertStatus(200);
        $fetchResponse->assertJson(['status' => 'success']);
        
        $messages = $fetchResponse->json('messages');
        $this->assertCount(0, $messages, 'Teacher inquiries should NOT appear in admin support inbox');
    }

    public function test_student_message_to_admin_creates_admin_notification_and_appears_in_admin_inbox(): void
    {
        $admin = User::create([
            'name'     => 'م. أحمد شمالي (مدير المنصة)',
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

        $student = Student::create([
            'name_ar'     => 'محمود أحمد حماد',
            'name_en'     => 'Mahmoud Ahmed',
            'email'       => 'mahmoud@test.ps',
            'nid'         => '955112244',
            'phone'       => '0599112233',
            'gender'      => 'male',
            'age'         => 18,
            'password'    => bcrypt('password123'),
            'stage_id'    => $stage->id,
            'status'      => 'active',
        ]);

        // 1. Student sends support message to administration
        $response = $this->actingAs($student, 'student')->postJson(route('student.support.send'), [
            'admin_id' => $admin->id,
            'message'  => 'السلام عليكم، أود تفعيل حسابي للفصل الدراسي الأول.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // 2. Check Admin Notification
        $adminNotif = DB::table('notifications')->where('notifiable_id', $admin->id)->first();
        $this->assertNotNull($adminNotif);
        $data = json_decode($adminNotif->data, true);
        $this->assertStringContainsString('رسالة جديدة من طالب', $data['title']);
        $this->assertStringContainsString((string)$student->id, $data['action_url']);
        $this->assertStringContainsString('inbox', $data['action_url']);

        // 3. Admin clicks notification -> redirects to admin inbox with student_id
        $clickResponse = $this->actingAs($admin, 'web')->get(route('notifications.open', ['id' => $adminNotif->id]));
        $clickResponse->assertRedirect();
        $location = $clickResponse->headers->get('Location');
        $this->assertStringContainsString('inbox', $location);
        $this->assertStringContainsString('student_id=' . $student->id, $location);

        // 4. Admin fetches messages for this student via AJAX -> Returns the message
        $fetchResponse = $this->actingAs($admin, 'web')->getJson(route('admin.fetch', ['student_id' => $student->id]));
        $fetchResponse->assertStatus(200);
        $fetchResponse->assertJson(['status' => 'success']);
        
        $messages = $fetchResponse->json('messages');
        $this->assertCount(1, $messages);
        $this->assertEquals('السلام عليكم، أود تفعيل حسابي للفصل الدراسي الأول.', $messages[0]['message']);
        $this->assertEquals('student', $messages[0]['sender_type']);

        // 5. Admin sends reply to student
        $replyResponse = $this->actingAs($admin, 'web')->postJson(route('admin.send'), [
            'student_id' => $student->id,
            'message'    => 'أهلاً بك محمود، تم تفعيل اشتراكك للفصل الأول بنجاح.',
        ]);

        $replyResponse->assertStatus(200);
        $replyResponse->assertJson(['status' => 'success']);

        // Verify reply message exists in DB
        $replyMsg = Message::where('student_id', $student->id)->where('sender_type', 'admin')->first();
        $this->assertNotNull($replyMsg);
        $this->assertEquals('أهلاً بك محمود، تم تفعيل اشتراكك للفصل الأول بنجاح.', $replyMsg->message);
    }
}
