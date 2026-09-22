<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InquiryNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_submission_creates_complaint_and_admin_notification_with_inquiries_url(): void
    {
        $admin = User::create([
            'name'     => 'م. أحمد شمالي (مدير النظام)',
            'email'    => 'admin@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $response = $this->post(route('contact.submit'), [
            'name'    => 'محمد إبراهيم',
            'email'   => 'mohammed@test.ps',
            'phone'   => '0599123456',
            'type'    => 'استفسار أكاديمي عن المساقات',
            'message' => 'أود الاستفسار عن مواعيد بدء شروحات الفيزياء التوجيهي.',
        ]);

        $response->assertSessionHas('success');

        // Check Complaint record
        $complaint = Complaint::where('email', 'mohammed@test.ps')->first();
        $this->assertNotNull($complaint);
        $this->assertEquals('new', $complaint->status);
        $this->assertEquals('استفسار أكاديمي عن المساقات', $complaint->category);

        // Check Admin Notification
        $notif = DB::table('notifications')->where('notifiable_id', $admin->id)->first();
        $this->assertNotNull($notif);

        $data = json_decode($notif->data, true);
        $this->assertStringContainsString('academic-inquiries', $data['action_url']);
        $this->assertStringContainsString('open_id=' . $complaint->id, $data['action_url']);

        // Check clicking notification redirects to academic-inquiries
        $clickResponse = $this->actingAs($admin)->get(route('notifications.open', ['id' => $notif->id]));
        $clickResponse->assertRedirect();
        $this->assertStringContainsString('academic-inquiries', $clickResponse->headers->get('Location'));
        $this->assertStringContainsString('open_id=' . $complaint->id, $clickResponse->headers->get('Location'));
    }

    public function test_old_complaint_notification_pointing_to_inbox_redirects_to_academic_inquiries(): void
    {
        $admin = User::create([
            'name'     => 'م. أحمد شمالي (مدير النظام)',
            'email'    => 'admin2@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $complaint = Complaint::create([
            'name'     => 'أحمد محمود',
            'email'    => 'ahmed_m@test.ps',
            'message'  => 'مشكلة في الدفع',
            'status'   => 'new',
        ]);

        // Insert legacy notification with old action_url pointing to /admin/inbox
        $notifId = (string) Str::uuid();
        DB::table('notifications')->insert([
            'id'              => $notifId,
            'type'            => 'App\\Notifications\\AdminAlert',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id'   => $admin->id,
            'data'            => json_encode([
                'title'      => 'شكوى / استفسار جديد 📩',
                'message'    => 'وردت رسالة جديدة من أحمد محمود بخصوص مشكلة الدفع',
                'type'       => 'support',
                'action_url' => url('/admin/inbox'), // old legacy URL
                'email'      => 'ahmed_m@test.ps',
            ], JSON_UNESCAPED_UNICODE),
            'read_at'         => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // When admin clicks old notification, NotificationController intercepts it and redirects to academic-inquiries
        $response = $this->actingAs($admin)->get(route('notifications.open', ['id' => $notifId]));
        $response->assertRedirect();
        $this->assertStringContainsString('academic-inquiries', $response->headers->get('Location'));
    }
}
