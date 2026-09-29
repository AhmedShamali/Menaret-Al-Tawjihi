<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Support\MediaHelper;
use App\Models\Student;
use App\Models\User;

class MediaHelperTest extends TestCase
{
    public function test_url_with_full_http_url()
    {
        $url = 'https://example.com/images/avatar.jpg';
        $this->assertEquals($url, MediaHelper::url($url));
    }

    public function test_url_with_null_returns_fallback()
    {
        $this->assertNull(MediaHelper::url(null));
        $this->assertEquals('fallback.jpg', MediaHelper::url(null, 'fallback.jpg'));
    }

    public function test_avatar_url_fallback_for_student()
    {
        $avatar = MediaHelper::avatarUrl(null, 'رؤى حمدية', 'student');
        $this->assertStringContainsString('ui-avatars.com', $avatar);
        $this->assertStringContainsString(urlencode('رؤى حمدية'), $avatar);
    }

    public function test_student_model_photo_url_accessor()
    {
        $student = new Student([
            'name_ar' => 'رؤى حمدية',
            'name_en' => 'Roaa Hamdia',
        ]);

        $this->assertNotEmpty($student->photo_url);
        $this->assertStringContainsString('ui-avatars.com', $student->photo_url);

        // When photo is set to relative path
        $student->photo = 'students/photos/sample.png';
        $photoUrl = $student->photo_url;
        $this->assertNotEmpty($photoUrl);
        $this->assertTrue(str_contains($photoUrl, 'sample.png'));
    }

    public function test_user_model_photo_url_accessor()
    {
        $user = new User([
            'name' => 'أحمد شمالي',
            'role' => 'admin',
        ]);

        $this->assertNotEmpty($user->photo_url);
        $this->assertStringContainsString('ui-avatars.com', $user->photo_url);
    }
}
