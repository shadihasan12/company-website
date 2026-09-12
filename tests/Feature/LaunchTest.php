<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Post;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LaunchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        Notification::fake();
    }

    public function test_no_analytics_script_loads_without_a_configured_provider(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertDontSee('googletagmanager', escape: false)
            ->assertDontSee('plausible.io', escape: false);
    }

    public function test_analytics_never_loads_outside_production(): void
    {
        // Local and staging traffic in a client's reports is worse than no
        // data at all.
        config()->set('services.ga4.id', 'G-TEST123');

        $this->get('/en')->assertOk()->assertDontSee('googletagmanager', escape: false);
    }

    public function test_the_verification_tag_renders_when_configured(): void
    {
        config()->set('services.search_console.verification', 'abc123');

        $this->get('/en')
            ->assertOk()
            ->assertSee('name="google-site-verification" content="abc123"', escape: false);
    }

    public function test_a_captured_lead_flashes_a_conversion_for_analytics(): void
    {
        $this->post('/en/contact', [
            'name' => 'Dana Khoury',
            'email' => 'dana@example.com',
            'service' => 'mobile-apps',
            'budget_range' => '10k-25k',
            'message' => 'We need a Flutter app for our delivery fleet in the Gulf.',
            'website' => '',
            'loaded_at' => encrypt(now()->subMinute()->timestamp),
        ])->assertSessionHas('conversion');

        $this->assertSame(1, Lead::count());
    }

    public function test_the_conversion_flash_carries_the_lead_source(): void
    {
        $this->post('/en/newsletter', ['email' => 'reader@example.com', 'website' => '']);

        $this->assertSame('newsletter', session('conversion')['properties']['source']);
    }

    public function test_launch_check_blocks_on_an_unsafe_configuration(): void
    {
        config()->set('app.debug', true);

        $this->artisan('launch:check --strict')->assertFailed();
    }

    public function test_launch_check_blocks_while_demo_content_remains(): void
    {
        // Sample testimonials and posts reaching production is the failure
        // this command exists to prevent.
        Testimonial::factory()->create(['author_name' => 'SAMPLE — Replace before launch']);
        Post::factory()->create(['slug' => 'sample-post-1']);

        $this->artisan('launch:check')
            ->expectsOutputToContain('SAMPLE demo content')
            ->assertSuccessful();
    }

    public function test_launch_check_reports_a_missing_admin_account(): void
    {
        $this->assertFalse(User::where('is_admin', true)->exists());

        $this->artisan('launch:check')
            ->expectsOutputToContain('An admin account exists')
            ->assertSuccessful();
    }

    public function test_launch_check_is_only_advisory_without_strict(): void
    {
        $this->artisan('launch:check')->assertSuccessful();
    }

    public function test_the_content_audit_still_runs(): void
    {
        $this->artisan('content:audit')->assertSuccessful();
    }
}
