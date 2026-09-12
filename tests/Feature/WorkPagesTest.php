<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Testimonial;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_index_lists_every_published_project(): void
    {
        $response = $this->get('/en/work')->assertOk();

        foreach (Project::published()->get() as $project) {
            $response->assertSee($project->name, escape: false);
        }
    }

    public function test_unpublished_projects_are_excluded(): void
    {
        Project::where('slug', 'reserva')->update(['is_published' => false]);

        $this->get('/en/work')->assertOk()->assertDontSee('Reserva');
    }

    public function test_filtering_by_service_narrows_the_list(): void
    {
        $this->get('/en/work?service=web-development')
            ->assertOk()
            ->assertSee('Wakil Topup')
            ->assertDontSee('Captain Car');
    }

    public function test_filtering_by_industry_narrows_the_list(): void
    {
        $this->get('/en/work?industry=hospitality')
            ->assertOk()
            ->assertSee('Reserva')
            ->assertDontSee('Captain Car');
    }

    public function test_filters_combine(): void
    {
        $this->get('/en/work?service=mobile-apps&technology=firebase')
            ->assertOk()
            ->assertSee('Reserva')
            ->assertDontSee('Captain Car');
    }

    public function test_a_filtered_view_is_a_shareable_url_that_keeps_its_state(): void
    {
        // Filters run server-side precisely so this is a real URL.
        $this->get('/en/work?industry=fintech')
            ->assertOk()
            ->assertSee('aria-current="true"', escape: false)
            ->assertSee('Wakil Topup');
    }

    public function test_an_impossible_combination_shows_an_empty_state_not_a_broken_page(): void
    {
        $this->get('/en/work?industry=hospitality&technology=aws-rekognition')
            ->assertOk()
            ->assertSee(__('work.index.empty'))
            ->assertSee(__('work.index.empty_action'));
    }

    public function test_only_facets_that_return_something_are_offered(): void
    {
        // Offering a filter that leads nowhere is worse than omitting it.
        $this->get('/en/work')->assertOk()->assertDontSee('Kotlin');
    }

    public function test_a_case_study_renders_its_narrative_and_facts(): void
    {
        $project = Project::where('slug', 'wakil-topup')->firstOrFail();

        $this->get('/en/work/wakil-topup')
            ->assertOk()
            ->assertSee($project->name, escape: false)
            ->assertSee((string) $project->summary)
            ->assertSee(__('work.show.stack'))
            ->assertSee('Topline Digital');
    }

    public function test_store_and_website_links_render_when_present(): void
    {
        $this->get('/en/work/wakil-topup')
            ->assertOk()
            ->assertSee('https://wakilcard.com', escape: false)
            ->assertSee(__('work.links.website'));
    }

    public function test_link_buttons_are_absent_when_no_url_exists(): void
    {
        $this->get('/en/work/reserva')
            ->assertOk()
            ->assertDontSee(__('work.links.google_play'));
    }

    public function test_the_gallery_and_results_stay_hidden_without_content(): void
    {
        $this->get('/en/work/reserva')
            ->assertOk()
            ->assertDontSee(__('work.gallery.title'))
            ->assertDontSee(__('work.show.results'));
    }

    public function test_a_gallery_renders_with_a_lightbox_when_screenshots_exist(): void
    {
        Project::where('slug', 'reserva')->update([
            'gallery' => ['projects/gallery/one.png', 'projects/gallery/two.png'],
        ]);

        $this->get('/en/work/reserva')
            ->assertOk()
            ->assertSee(__('work.gallery.title'))
            ->assertSee('x-data="lightbox(', escape: false);
    }

    public function test_a_testimonial_attached_to_the_project_is_shown(): void
    {
        $project = Project::where('slug', 'reserva')->firstOrFail();

        Testimonial::factory()->create([
            'project_id' => $project->id,
            'client_id' => null,
            'author_name' => 'Layla Habib',
            'quote' => ['en' => 'Bookings stopped double-clashing overnight.'],
        ]);

        $this->get('/en/work/reserva')
            ->assertOk()
            ->assertSee('Layla Habib')
            ->assertSee('Bookings stopped double-clashing overnight.');
    }

    public function test_the_last_case_study_wraps_round_rather_than_dead_ending(): void
    {
        $last = Project::published()->ordered()->get()->last();

        $this->get("/en/work/{$last->slug}")
            ->assertOk()
            ->assertSee(__('work.show.next'));
    }

    public function test_an_unpublished_project_is_not_reachable_directly(): void
    {
        Project::where('slug', 'reserva')->update(['is_published' => false]);

        $this->get('/en/work/reserva')->assertNotFound();
    }

    public function test_an_unknown_project_is_not_found(): void
    {
        $this->get('/en/work/nope')->assertNotFound();
    }

    public function test_an_unnamed_client_is_never_credited_on_a_case_study(): void
    {
        Project::where('slug', 'reserva')->first()->client->update([
            'is_named' => false,
            'anonymous_label' => ['en' => 'A hospitality group'],
        ]);

        $this->get('/en/work/reserva')
            ->assertOk()
            ->assertSee('A hospitality group')
            ->assertDontSee('Reserva Hospitality');
    }
}
