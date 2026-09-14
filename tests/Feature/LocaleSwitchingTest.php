<?php

namespace Tests\Feature;

use App\Support\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Both locales are live, so these exercise the switcher directly.
 */
class LocaleSwitchingTest extends TestCase
{
    // The home page reads services and projects from the database.
    use RefreshDatabase;

    public function test_both_locales_are_active(): void
    {
        $this->assertSame(['en', 'ar'], array_keys(Locale::all()));
    }

    public function test_alternates_offer_the_other_locale_for_the_current_route(): void
    {
        $this->get('/en/styleguide')->assertOk();
        $alternates = Locale::alternates();

        $this->assertCount(1, $alternates);
        $this->assertSame('ar', $alternates[0]['code']);
        $this->assertStringEndsWith('/ar/styleguide', $alternates[0]['url']);
        $this->assertSame('rtl', $alternates[0]['dir']);
    }

    public function test_switching_preserves_the_query_string(): void
    {
        $this->get('/en/styleguide?tab=colour&page=2')->assertOk();
        $url = Locale::urlFor('ar');

        $this->assertStringContainsString('/ar/styleguide', $url);
        $this->assertStringContainsString('tab=colour', $url);
        $this->assertStringContainsString('page=2', $url);
    }

    public function test_switching_does_not_leak_route_defaults_into_the_url(): void
    {
        // Route::view() stores `view` and `status` as route defaults; they
        // must never surface in a generated URL.
        $this->get('/en/styleguide')->assertOk();
        $url = Locale::urlFor('ar');

        $this->assertStringNotContainsString('view=', $url);
        $this->assertStringNotContainsString('status=', $url);
    }

    public function test_route_helper_defaults_to_the_active_locale(): void
    {
        $this->get('/en/styleguide')->assertOk();

        // URL::defaults() is set by the middleware, so views never need to
        // pass the locale explicitly.
        $this->assertStringEndsWith('/en/styleguide', route('styleguide'));
    }
}
