<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_services_grid_lists_every_published_service(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/en')->assertOk();

        foreach (Service::published()->get() as $service) {
            // Default escaping matters here: "Backend & Cloud" renders as "&amp;".
            $response->assertSee((string) $service->title);
        }
    }

    public function test_unpublished_services_are_hidden(): void
    {
        $this->seed(DatabaseSeeder::class);
        Service::query()->first()->update(['is_published' => false, 'title' => ['en' => 'Hidden Service']]);

        $this->get('/en')->assertOk()->assertDontSee('Hidden Service');
    }

    public function test_the_client_strip_shows_clients_we_may_name(): void
    {
        Client::factory()->named()->create(['name' => 'Topline Digital']);

        $this->get('/en')->assertOk()->assertSee('Topline Digital');
    }

    public function test_the_client_strip_never_exposes_an_unnamed_client(): void
    {
        // Publishing a client name without permission is the failure that
        // actually matters here.
        Client::factory()->create([
            'name' => 'Confidential Bank',
            'is_named' => false,
            'anonymous_label' => ['en' => 'A leading regional bank'],
        ]);

        $this->get('/en')->assertOk()->assertDontSee('Confidential Bank');
    }

    public function test_the_client_strip_disappears_when_there_is_nobody_to_show(): void
    {
        $this->get('/en')->assertOk()->assertDontSee(__('home.clients.title'));
    }

    public function test_each_client_is_rendered_twice_so_the_marquee_loops_seamlessly(): void
    {
        Client::factory()->named()->create(['name' => 'Metro Mobility']);

        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertSame(2, substr_count($html, 'Metro Mobility'));
    }
}
