<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Support\SiteStats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unset_statistics_are_omitted_rather_than_guessed(): void
    {
        config()->set('site.stats', ['clients' => null, 'app_rating' => null, 'uptime' => null, 'nps' => null]);

        $keys = SiteStats::all()->pluck('key');

        $this->assertFalse($keys->contains('clients'));
        $this->assertFalse($keys->contains('app_rating'));
    }

    public function test_configured_statistics_appear_with_their_formatting(): void
    {
        Project::factory()->count(3)->create();
        config()->set('site.stats.app_rating', 4.8);
        config()->set('site.stats.uptime', 99.9);

        $this->get('/en')
            ->assertOk()
            ->assertSee('4.8')
            ->assertSee('99.9%');
    }

    public function test_the_project_count_is_computed_from_published_records(): void
    {
        Project::factory()->count(5)->create();
        Project::factory()->create(['is_published' => false]);

        $projects = SiteStats::all()->firstWhere('key', 'projects');

        $this->assertSame(5, $projects['value']);
    }

    public function test_the_band_hides_when_there_is_almost_nothing_to_show(): void
    {
        // One lonely statistic reads worse than none at all.
        config()->set('site.stats', []);
        config()->set('site.founded_year', null);

        $this->get('/en')->assertOk()->assertDontSee(__('home.stats.projects'));
    }

    public function test_the_real_figure_is_in_the_markup_so_it_survives_without_javascript(): void
    {
        Project::factory()->count(4)->create();

        $this->get('/en')->assertOk()->assertSee('>4</dd>', escape: false);
    }
}
