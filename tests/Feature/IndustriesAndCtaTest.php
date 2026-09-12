<?php

namespace Tests\Feature;

use App\Models\Industry;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndustriesAndCtaTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_industry_with_published_work_is_listed_with_its_count(): void
    {
        $industry = Industry::factory()->create(['name' => ['en' => 'Fintech & Payments']]);
        Project::factory()->count(2)->create(['industry_id' => $industry->id]);

        $this->get('/en')
            ->assertOk()
            ->assertSee('Fintech &amp; Payments', escape: false)
            ->assertSee(trans_choice('home.industries.count', 2, ['count' => 2]));
    }

    public function test_an_industry_with_no_published_work_is_not_claimed(): void
    {
        // The section asserts experience, so it must be backed by a case study.
        Industry::factory()->create(['name' => ['en' => 'Aerospace']]);
        $unpublished = Industry::factory()->create(['name' => ['en' => 'Defence']]);
        Project::factory()->create(['industry_id' => $unpublished->id, 'is_published' => false]);

        $this->get('/en')
            ->assertOk()
            ->assertDontSee('Aerospace')
            ->assertDontSee('Defence');
    }

    public function test_the_industries_section_hides_when_nothing_qualifies(): void
    {
        $this->get('/en')->assertOk()->assertDontSee(__('home.industries.title'));
    }

    public function test_the_closing_cta_always_renders(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee(__('home.cta.title'))
            ->assertSee(__('home.cta.reassurance'));
    }

    public function test_the_whatsapp_button_disappears_when_no_number_is_configured(): void
    {
        config()->set('site.contact.whatsapp', null);

        $this->get('/en')->assertOk()->assertDontSee('wa.me', escape: false);
    }
}
