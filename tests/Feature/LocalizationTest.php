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

    public function test_root_falls_back_when_an_unsupported_language_is_requested(): void
    {
        $this->withHeader('Accept-Language', 'ar,de;q=0.8')
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
        // Arabic is deferred: the plumbing remains but the locale is off.
        $this->get('/ar')->assertNotFound();
        $this->get('/fr')->assertNotFound();
    }

    public function test_styleguide_renders(): void
    {
        $this->get('/en/styleguide')->assertOk()->assertSee('Design system');
    }

    public function test_the_deferred_arabic_font_is_never_shipped(): void
    {
        // The Arabic face is ~90KB. While Arabic is off it must not appear
        // in the build output or the page.
        $this->get('/en')->assertDontSee('ibm-plex-sans-arabic', escape: false);
    }

    public function test_the_brand_name_renders_from_configuration(): void
    {
        $this->get('/en')->assertOk()->assertSee(config('site.name'));
    }
}
