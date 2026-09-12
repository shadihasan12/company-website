<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Support\Nav;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Tests\TestCase;

class LinkIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_homepage_service_cards_link_to_their_service_pages(): void
    {
        // These once pointed at an #anchor because Nav::link silently
        // swallowed a missing route parameter.
        $html = $this->get('/en')->assertOk()->getContent();

        foreach (Service::published()->get() as $service) {
            $this->assertStringContainsString(
                url("/en/services/{$service->slug}"),
                $html,
                "Service [{$service->slug}] is not linked from the homepage.",
            );
        }
    }

    public function test_homepage_case_study_cards_link_to_their_case_studies(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        foreach (Project::published()->featured()->get() as $project) {
            $this->assertStringContainsString(
                url("/en/work/{$project->slug}"),
                $html,
                "Project [{$project->slug}] is not linked from the homepage.",
            );
        }
    }

    public function test_industry_tiles_link_to_a_filtered_listing(): void
    {
        $this->get('/en')->assertOk()->assertSee('/en/work?industry=fintech', escape: false);
    }

    public function test_no_page_ships_a_dead_anchor_link(): void
    {
        $pages = ['/en', '/en/services', '/en/services/erp', '/en/work', '/en/work/wakil-topup', '/en/about', '/en/contact'];

        foreach ($pages as $page) {
            $html = $this->get($page)->assertOk()->getContent();

            $this->assertStringNotContainsString(
                'href="#"',
                $html,
                "[{$page}] contains a dead link.",
            );
        }
    }

    public function test_every_internal_link_on_the_homepage_resolves(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        preg_match_all('/href="([^"]+)"/', $html, $matches);

        $base = rtrim(config('app.url'), '/');

        $paths = collect($matches[1])
            // Routes render absolute URLs, so same-host links are reduced
            // to their path and everything external is dropped.
            ->map(fn (string $href) => str_starts_with($href, $base)
                ? substr($href, strlen($base))
                : $href)
            ->filter(fn (string $path) => str_starts_with($path, '/'))
            ->reject(fn (string $path) => str_starts_with($path, '/build/'))
            ->unique();

        $this->assertGreaterThan(10, $paths->count());

        foreach ($paths as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_a_missing_route_parameter_is_not_silently_swallowed_in_development(): void
    {
        config()->set('app.debug', true);

        $this->expectException(UrlGenerationException::class);

        Nav::link('services.show', '#');
    }

    public function test_a_missing_route_parameter_still_degrades_in_production(): void
    {
        // A programming mistake should not take a live page down.
        config()->set('app.debug', false);

        $this->assertSame('#', Nav::link('services.show', '#'));
    }

    public function test_an_unregistered_route_falls_back_without_complaint(): void
    {
        $this->assertSame('/fallback', Nav::link('route.that.does.not.exist', '/fallback'));
    }

    public function test_the_privacy_and_terms_pages_are_reachable_from_every_page(): void
    {
        // The site collects names, emails, phone numbers and IP addresses;
        // these cannot be dead links.
        $this->get('/en')
            ->assertOk()
            ->assertSee(url('/en/privacy'), escape: false)
            ->assertSee(url('/en/terms'), escape: false);

        $this->get('/en/privacy')->assertOk()->assertSee(__('legal.privacy.title'));
        $this->get('/en/terms')->assertOk()->assertSee(__('legal.terms.title'));
    }

    public function test_an_unknown_legal_document_is_not_found(): void
    {
        $this->get('/en/legal/nope')->assertNotFound();
    }

    public function test_launch_check_blocks_while_legal_copy_is_unreviewed(): void
    {
        $this->artisan('launch:check')
            ->expectsOutputToContain('TODO-LEGAL-REVIEW')
            ->assertSuccessful();
    }

    public function test_post_cards_link_to_their_posts(): void
    {
        $post = Post::factory()->create(['slug' => 'linked-post']);

        $this->get('/en/insights')->assertOk()->assertSee(url("/en/insights/{$post->slug}"), escape: false);
    }
}
