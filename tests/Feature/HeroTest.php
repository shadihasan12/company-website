<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_hero_renders_its_headline_and_calls_to_action(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee(__('home.hero.headline_lead'))
            ->assertSee(__('home.hero.headline_accent'))
            ->assertSee(__('home.hero.primary_cta'))
            ->assertSee(__('home.hero.secondary_cta'));
    }

    public function test_the_shipped_count_reflects_published_projects(): void
    {
        Project::factory()->count(4)->create();
        Project::factory()->create(['is_published' => false]);

        $this->get('/en')
            ->assertOk()
            ->assertSee(__('home.hero.proof', ['count' => 4]));
    }

    public function test_the_proof_line_is_hidden_when_there_is_nothing_to_claim(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertDontSee(__('home.hero.proof', ['count' => 0]));
    }

    public function test_the_meta_description_falls_back_to_the_hero_subhead(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee(__('home.hero.subhead'), escape: false);
    }

    public function test_the_primary_cta_falls_back_to_email_until_the_contact_page_exists(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('mailto:'.config('site.contact.email'), escape: false);
    }
}
