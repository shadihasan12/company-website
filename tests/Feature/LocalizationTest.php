<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    // The home page reads services and projects from the database.
    use RefreshDatabase;

    public function test_root_redirects_to_the_active_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_root_honours_a_supported_browser_language(): void
    {
        $this->withHeader('Accept-Language', 'ar,en;q=0.8')
            ->get('/')
            ->assertRedirect('/ar');
    }

    public function test_root_falls_back_when_an_unsupported_language_is_requested(): void
    {
        $this->withHeader('Accept-Language', 'de-DE,de;q=0.9')
            ->get('/')
            ->assertRedirect('/en');
    }

    public function test_english_home_renders_left_to_right(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('lang="en"', escape: false)
            ->assertSee('dir="ltr"', escape: false);
    }

    public function test_only_configured_locales_resolve(): void
    {
        $this->get('/ar')->assertOk();
        $this->get('/fr')->assertNotFound();
    }

    public function test_styleguide_renders(): void
    {
        $this->get('/en/styleguide')->assertOk()->assertSee('Design system');
    }

    public function test_the_arabic_font_is_never_shipped_to_english_pages(): void
    {
        // The Arabic face is ~90KB and English visitors never need it.
        $this->get('/en')->assertDontSee('ibm-plex-sans-arabic', escape: false);
        $this->get('/ar')->assertSee('ibm-plex-sans-arabic', escape: false);
    }

    public function test_the_brand_name_renders_from_configuration(): void
    {
        $this->get('/en')->assertOk()->assertSee(config('site.name'));
    }
}
