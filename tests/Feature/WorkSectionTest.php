<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_featured_and_published_projects_appear(): void
    {
        Project::factory()->featured()->create(['name' => 'Shown Project']);
        Project::factory()->create(['name' => 'Not Featured']);
        Project::factory()->featured()->create(['name' => 'Unpublished', 'is_published' => false]);

        $this->get('/en')
            ->assertOk()
            ->assertSee('Shown Project')
            ->assertDontSee('Not Featured')
            ->assertDontSee('Unpublished');
    }

    public function test_the_section_hides_when_nothing_is_featured(): void
    {
        Project::factory()->create();

        $this->get('/en')->assertOk()->assertDontSee(__('home.work.title'));
    }

    public function test_metrics_render_with_their_prefix_suffix_and_decimals(): void
    {
        Project::factory()->featured()->create([
            'name' => 'Metric Project',
            'metrics' => [
                ['label' => 'Downloads', 'value' => 120000, 'suffix' => '+'],
                ['label' => 'Rating', 'value' => 4.8, 'decimals' => 1],
            ],
        ]);

        $this->get('/en')
            ->assertOk()
            ->assertSee('120,000+')
            ->assertSee('4.8');
    }

    public function test_a_project_without_a_screenshot_still_renders_a_branded_panel(): void
    {
        // The fallback must look deliberate, not like a broken image.
        Project::factory()->featured()->create(['name' => 'No Screenshot Yet']);

        // Scoped to the card's own image: the header and footer carry a
        // logo <img> of their own, which says nothing about this project.
        $this->get('/en')
            ->assertOk()
            ->assertDontSee('alt="No Screenshot Yet"', escape: false)
            ->assertSee('No Screenshot Yet');
    }

    public function test_the_live_link_badge_counts_only_populated_urls(): void
    {
        Project::factory()->featured()->create([
            'name' => 'Two Links',
            'website_url' => 'https://example.com',
            'google_play_url' => 'https://play.google.com/x',
            'app_store_url' => null,
        ]);

        $this->get('/en')
            ->assertOk()
            ->assertSee(trans_choice('home.work.live_on', 2));
    }
}
