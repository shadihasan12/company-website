<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Technology;
use App\Models\Testimonial;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechAndTestimonialsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_stack_lists_technologies_under_their_category(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/en')->assertOk();

        $response->assertSee(__('home.tech.categories.language'));
        $response->assertSee(__('home.tech.categories.framework'));
        $response->assertSee('Flutter');
        $response->assertSee('Laravel');
    }

    public function test_an_empty_category_column_is_skipped(): void
    {
        Technology::factory()->create(['category' => 'language', 'name' => 'Dart']);

        // "Tooling" rather than "Services": the latter also labels the
        // navigation and footer, so it would match regardless.
        $this->get('/en')
            ->assertOk()
            ->assertSee(__('home.tech.categories.language'))
            ->assertDontSee(__('home.tech.categories.tool'));
    }

    public function test_the_testimonial_section_is_hidden_while_there_are_no_real_quotes(): void
    {
        // Nothing is seeded on purpose: fabricated social proof on a live
        // site is a liability, so the section must degrade to nothing.
        $this->get('/en')->assertOk()->assertDontSee(__('home.testimonials.title'));
    }

    public function test_a_featured_testimonial_renders_with_its_attribution(): void
    {
        $client = Client::factory()->named()->create(['name' => 'Topline Digital']);

        Testimonial::factory()->featured()->create([
            'client_id' => $client->id,
            'author_name' => 'Dana Khoury',
            'author_title' => ['en' => 'Head of Product'],
            'quote' => ['en' => 'They shipped when they said they would.'],
            'rating' => 5,
        ]);

        $this->get('/en')
            ->assertOk()
            ->assertSee('Dana Khoury')
            ->assertSee('Head of Product')
            ->assertSee('Topline Digital')
            ->assertSee('They shipped when they said they would.');
    }

    public function test_an_unfeatured_testimonial_stays_off_the_homepage(): void
    {
        Testimonial::factory()->create(['quote' => ['en' => 'Not featured quote.']]);

        $this->get('/en')->assertOk()->assertDontSee('Not featured quote.');
    }

    public function test_an_unnamed_client_is_not_credited_on_a_testimonial(): void
    {
        $client = Client::factory()->create(['name' => 'Confidential Bank', 'is_named' => false]);

        Testimonial::factory()->featured()->create([
            'client_id' => $client->id,
            'author_name' => 'Anonymous Author',
        ]);

        $this->get('/en')
            ->assertOk()
            ->assertSee('Anonymous Author')
            ->assertDontSee('Confidential Bank');
    }

    public function test_a_testimonial_without_a_photo_falls_back_to_initials(): void
    {
        Testimonial::factory()->featured()->create([
            'author_name' => 'Rami Saad',
            'avatar_path' => null,
        ]);

        $this->get('/en')->assertOk()->assertSee('RS');
    }
}
