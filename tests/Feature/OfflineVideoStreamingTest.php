<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Stage;
use App\Models\Subject;
use App\Models\EducationalContent;
use Illuminate\Support\Facades\Storage;

class OfflineVideoStreamingTest extends TestCase
{
    use RefreshDatabase;

    public function test_video_stream_handles_cors_options_preflight(): void
    {
        $response = $this->call('OPTIONS', route('video.stream', ['filename' => 'test-video.mp4']));

        $response->assertStatus(204);
        $response->assertHeader('Access-Control-Allow-Origin', '*');
        $response->assertHeader('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS');
        $response->assertHeader('Access-Control-Allow-Headers', 'Range, Content-Type, Accept, Authorization');
    }

    public function test_video_stream_returns_stream_with_cors_and_byte_ranges_for_existing_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('educational/videos/sample_lesson.mp4', 'dummy-mp4-binary-content-for-testing');

        $response = $this->get(route('video.stream', ['filename' => 'educational/videos/sample_lesson.mp4']));

        $response->assertStatus(200);
        $response->assertHeader('Access-Control-Allow-Origin', '*');
        $response->assertHeader('Accept-Ranges', 'bytes');
    }

    public function test_video_stream_redirects_to_cloud_fallback_when_local_missing(): void
    {
        // When local file does not exist, MediaHelper generates cloud URL (or 404 if not found)
        // With a dummy filename that isn't on cloud or local, it aborts 404 cleanly
        $response = $this->get(route('video.stream', ['filename' => 'non_existent_random_video.mp4']));

        $this->assertTrue(in_array($response->getStatusCode(), [302, 404]));
    }

    public function test_public_views_do_not_expose_offline_download_or_vault_buttons(): void
    {
        $welcomeResponse = $this->get('/');
        $welcomeResponse->assertStatus(200);
        $welcomeResponse->assertDontSee('دروسي أوفلاين');

        $stage = Stage::create([
            'grade_level' => '12',
            'label_ar' => 'العلمي',
        ]);

        $subject = Subject::create([
            'subject_key' => 'math_test',
            'name_ar' => 'الرياضيات للتجربة',
            'stage_id' => $stage->id,
            'color' => '#1e3a8a',
            'price_ils' => 150,
        ]);

        $publicSubjectResponse = $this->get(route('subject.show', $subject->id));
        $publicSubjectResponse->assertStatus(200);
        $publicSubjectResponse->assertDontSee('id="btn_offline_', false);
        $publicSubjectResponse->assertDontSee('data-video-id', false);
    }

    public function test_enrolled_student_sees_offline_download_button_in_subject_view(): void
    {
        $stage = Stage::create([
            'grade_level' => '12',
            'label_ar' => 'العلمي',
        ]);

        $subject = Subject::create([
            'subject_key' => 'physics_sci',
            'name_ar' => 'الفيزياء',
            'stage_id' => $stage->id,
            'color' => '#1e3a8a',
            'price_ils' => 150,
        ]);

        $student = \App\Models\Student::create([
            'name_ar' => 'أحمد العلمي',
            'name_en' => 'Ahmed Sci',
            'email' => 'ahmed.sci@tawjihi.ps',
            'nid' => '900998877',
            'phone' => '0599998877',
            'password' => bcrypt('secret123'),
            'age' => 18,
            'gender' => 'male',
            'stage_id' => $stage->id,
            'status' => 'active',
            'monthly_fee' => 100.00,
        ]);

        // تسجيل الطالب في المادة
        \App\Models\Enrollment::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // إضافة درس فيديو مباشر للمادة
        $video = EducationalContent::create([
            'title' => 'شرح المتجهات والميكانيكا',
            'content_type' => 'video',
            'url_path' => 'educational/videos/vectors_lesson.mp4',
            'subject_id' => $subject->id,
            'order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('student.subjects.show', $subject->id));

        $response->assertStatus(200);
        $response->assertSee('id="btn_offline_' . $video->id . '"', false);
        $response->assertSee('data-video-id="' . $video->id . '"', false);
        $response->assertSee('ed-btn-offline-card', false);
    }
}
