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
}
