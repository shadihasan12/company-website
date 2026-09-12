<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Service;
use App\Models\Technology;
use App\Models\Testimonial;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_it_seeds_every_project_from_the_cv(): void
    {
        $this->assertSame(7, Project::count());
        $this->assertSame(3, Project::featured()->count());
    }

    public function test_services_match_the_navigation_configuration(): void
    {
        $configured = collect(config('site.services'))->pluck('key')->sort()->values();

        $this->assertEquals($configured, Service::pluck('key')->sort()->values());
    }

    public function test_projects_are_linked_to_services_and_technologies(): void
    {
        $wakil = Project::where('slug', 'wakil-topup')->firstOrFail();

        $this->assertEqualsCanonicalizing(
            ['mobile-apps', 'web-development', 'dashboards'],
            $wakil->services->pluck('key')->all(),
        );

        $this->assertTrue($wakil->technologies->contains('slug', 'flutter'));
        $this->assertSame('https://wakilcard.com', $wakil->website_url);
    }

    public function test_every_seeded_technology_reference_resolved(): void
    {
        // A typo in a seeder slug would silently attach nothing, leaving a
        // case study with no stack listed.
        foreach (Project::with('technologies')->get() as $project) {
            $this->assertNotEmpty(
                $project->technologies,
                "Project [{$project->slug}] has no technologies attached.",
            );
        }

        $this->assertSame(0, Technology::doesntHave('projects')->whereIn('slug', ['flutter', 'dart'])->count());
    }

    public function test_the_seeder_never_fabricates_metrics_or_testimonials(): void
    {
        // Invented numbers and quotes on a live site are a real liability;
        // they belong only in DemoContentSeeder.
        $this->assertSame(0, Testimonial::count());

        foreach (Project::all() as $project) {
            $this->assertEmpty($project->metrics, "Project [{$project->slug}] has seeded metrics.");
        }
    }
}
