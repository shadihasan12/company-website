<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Lead;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AboutAndCareersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        Notification::fake();
    }

    public function test_the_about_page_renders_story_and_values(): void
    {
        $response = $this->get('/en/about')->assertOk();

        $response->assertSee(__('about.heading'));
        $response->assertSee(__('about.story')[0]);

        foreach (config('site.about.values') as $value) {
            $response->assertSee(__("about.values.{$value['key']}.title"));
        }
    }

    public function test_the_milestones_timeline_hides_until_real_entries_exist(): void
    {
        // Inventing a founding story would be fabrication, so the section
        // stays absent rather than showing placeholder years.
        $this->get('/en/about')->assertOk()->assertDontSee(__('about.milestones_title'));
    }

    public function test_configured_milestones_render(): void
    {
        config()->set('site.about.milestones', [['year' => 2021, 'key' => 'founded']]);

        // Touch the group first: addLines() writes straight into the loaded
        // cache, so adding a line to a group that has not been read yet
        // marks it loaded and hides the rest of the file.
        __('about.heading');
        app('translator')->addLines(['about.milestones.founded' => 'Clean Cody was founded in Beirut.'], 'en');

        $this->get('/en/about')
            ->assertOk()
            ->assertSee('2021')
            ->assertSee('Clean Cody was founded in Beirut.');
    }

    public function test_the_about_page_shows_clients_we_may_name(): void
    {
        Client::factory()->named()->create(['name' => 'Metro Mobility']);

        $this->get('/en/about')->assertOk()->assertSee('Metro Mobility');
    }

    public function test_careers_is_absent_while_hiring_is_switched_off(): void
    {
        $this->get('/en/careers')->assertNotFound();
        $this->get('/en')->assertOk()->assertDontSee(__('careers.title'));
    }

    public function test_enabling_careers_publishes_the_page_and_the_footer_link(): void
    {
        $this->enableCareers();

        $this->get('/en/careers')->assertOk()->assertSee(__('careers.heading'));
        $this->get('/en')->assertOk()->assertSee(__('careers.title'));
    }

    public function test_an_application_is_captured_as_its_own_lead_source(): void
    {
        $this->enableCareers();

        $this->post('/en/careers', [
            'name' => 'Nour Aziz',
            'email' => 'nour@example.com',
            'role' => 'Flutter engineer',
            'portfolio' => 'https://github.com/example',
            'message' => 'I have shipped four Flutter apps to both stores over three years.',
            'website' => '',
            'loaded_at' => encrypt(now()->subMinute()->timestamp),
        ])->assertSessionHas('application');

        $lead = Lead::sole();

        $this->assertSame('career', $lead->source);
        $this->assertSame('Flutter engineer', $lead->payload['role']);
        $this->assertSame('https://github.com/example', $lead->payload['portfolio']);
    }

    public function test_an_application_cannot_be_posted_while_careers_are_off(): void
    {
        $this->post('/en/careers', ['name' => 'X', 'email' => 'x@example.com'])->assertNotFound();

        $this->assertSame(0, Lead::count());
    }

    public function test_the_application_form_rejects_a_bad_portfolio_url(): void
    {
        $this->enableCareers();

        $this->post('/en/careers', [
            'name' => 'Nour Aziz',
            'email' => 'nour@example.com',
            'portfolio' => 'not a url',
            'message' => 'I have shipped four Flutter apps to both stores over three years.',
            'website' => '',
            'loaded_at' => encrypt(now()->subMinute()->timestamp),
        ])->assertSessionHasErrors('portfolio');
    }

    /**
     * Rebuilds the application with hiring switched on.
     *
     * The careers routes are registered at boot, so the flag has to be in
     * the environment before the application is refreshed — setting config
     * afterwards would leave the routes unregistered.
     */
    protected function enableCareers(): void
    {
        putenv('CAREERS_ENABLED=true');
        $_ENV['CAREERS_ENABLED'] = 'true';

        $this->refreshApplication();

        // Refreshing the application opens a new in-memory SQLite database,
        // so the schema and seed data have to be rebuilt alongside it.
        $this->artisan('migrate');
        $this->seed(DatabaseSeeder::class);
    }

    protected function tearDown(): void
    {
        putenv('CAREERS_ENABLED');
        unset($_ENV['CAREERS_ENABLED']);

        parent::tearDown();
    }
}
