<?php

namespace Tests\Feature;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

class LanguageSwitchTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_switch_to_english()
    {
        $response = $this->get('/change-language/en');
        $response->assertRedirect('/?lang=en');
        $response->assertCookie('app_locale', 'en');
        $response->assertSessionHas('locale', 'en');
    }

    public function test_user_can_switch_to_arabic()
    {
        $response = $this->get('/change-language/ar');
        $response->assertRedirect('/?lang=ar');
        $response->assertCookie('app_locale', 'ar');
        $response->assertSessionHas('locale', 'ar');
    }

    public function test_lang_route_redirects_cleanly_without_loops()
    {
        $response = $this->get('/lang/en');
        $response->assertRedirect('/?lang=en');
        $response->assertCookie('app_locale', 'en');
    }

    public function test_home_page_renders_in_english_with_lang_param()
    {
        $response = $this->get('/?lang=en');
        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Tawjihi Branches');
        $response->assertSee('Platform Services');
    }

    public function test_home_page_renders_in_arabic_by_default()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('الرئيسية');
        $response->assertSee('فروع التوجيهي');
    }
}
