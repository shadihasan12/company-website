<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorsAndHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_an_unknown_page_renders_the_branded_404(): void
    {
        $this->get('/en/definitely-not-a-page')
            ->assertNotFound()
            ->assertSee(__('errors.404.title'))
            ->assertSee(__('errors.home'));
    }

    public function test_a_404_outside_the_locale_group_still_renders(): void
    {
        // No SetLocale middleware runs here, so route() has no locale unless
        // a default is registered at boot.
        $this->get('/not-a-locale-at-all')
            ->assertNotFound()
            ->assertSee(__('errors.404.title'));
    }

    public function test_error_pages_are_never_indexable(): void
    {
        config()->set('site.indexable', true);

        $this->get('/en/missing')
            ->assertNotFound()
            ->assertSee('noindex, nofollow', escape: false);
    }

    public function test_security_headers_are_present_on_every_response(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');
    }

    public function test_permissions_policy_switches_off_unused_apis(): void
    {
        $response = $this->get('/en')->assertOk();

        foreach (['camera=()', 'microphone=()', 'geolocation=()'] as $directive) {
            $this->assertStringContainsString($directive, $response->headers->get('Permissions-Policy'));
        }
    }

    public function test_hsts_is_not_sent_over_plain_http(): void
    {
        // Setting it in local development would pin localhost to HTTPS.
        $this->get('/en')->assertOk()->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_hsts_is_sent_over_https(): void
    {
        $this->get('https://localhost/en')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_error_pages_still_render_when_the_session_error_bag_is_absent(): void
    {
        // The footer newsletter form uses @error, which would otherwise
        // fatal on a view rendered outside the session middleware.
        $this->get('/fr')->assertNotFound()->assertSee(__('contact.newsletter.title'));
    }
}
