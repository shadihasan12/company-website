<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Service;
use App\Support\Locale;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_index_lists_every_published_service(): void
    {
        $response = $this->get('/en/services')->assertOk();

        foreach (Service::published()->get() as $service) {
            $response->assertSee((string) $service->title);
        }
    }

    public function test_the_index_hides_unpublished_services(): void
    {
        Service::where('key', 'erp')->update(['is_published' => false]);

        $this->get('/en/services')->assertOk()->assertDontSee('ERP Systems');
    }

    /**
     * The locale prefix is the first route segment, so without the
     * middleware dropping it, Laravel would pass the string "en" as the
     * controller's first argument.
     */
    public function test_a_detail_page_resolves_its_model_despite_the_locale_prefix(): void
    {
        $this->get('/en/services/mobile-apps')
            ->assertOk()
            ->assertSee('Mobile Apps')
            ->assertSee('iOS &amp; Android, one codebase', escape: false);
    }

    public function test_the_locale_is_not_passed_to_controller_arguments(): void
    {
        $this->get('/en/services/erp')->assertOk()->assertDontSee('ERP Systems — en');
    }

    public function test_url_generation_still_works_after_the_locale_is_forgotten(): void
    {
        $this->get('/en/services/mobile-apps')->assertOk();

        $this->assertStringEndsWith('/en/services/erp', route('services.show', 'erp'));
        $this->assertStringEndsWith('/en/services', route('services.index'));
    }

    public function test_an_unpublished_service_is_not_reachable_directly(): void
    {
        Service::where('key', 'erp')->update(['is_published' => false]);

        $this->get('/en/services/erp')->assertNotFound();
    }

    public function test_an_unknown_service_is_not_found(): void
    {
        $this->get('/en/services/nope')->assertNotFound();
    }

    public function test_a_detail_page_renders_inclusions_timeline_and_faqs(): void
    {
        $service = Service::where('key', 'mobile-apps')->firstOrFail();

        $response = $this->get('/en/services/mobile-apps')->assertOk();

        $response->assertSee((string) $service->timeline);
        $response->assertSee($service->inclusions[0]);
        $response->assertSee($service->faqs[0]['question']);
        $response->assertSee($service->faqs[0]['answer']);
    }

    public function test_faqs_are_published_as_structured_data(): void
    {
        // In 2026 this is how search engines and AI assistants read them.
        $this->get('/en/services/mobile-apps')
            ->assertOk()
            ->assertSee('"@type":"FAQPage"', escape: false)
            ->assertSee('"acceptedAnswer"', escape: false);
    }

    public function test_pricing_falls_back_to_on_request_until_a_figure_exists(): void
    {
        $this->get('/en/services/mobile-apps')
            ->assertOk()
            ->assertSee(__('services_page.show.pricing_on_request'));
    }

    public function test_a_configured_starting_price_replaces_the_fallback(): void
    {
        Service::where('key', 'mobile-apps')->update(['starting_price' => ['en' => 'From $18,000']]);

        $this->get('/en/services/mobile-apps')
            ->assertOk()
            ->assertSee('From $18,000')
            ->assertDontSee(__('services_page.show.pricing_on_request'));
    }

    public function test_related_work_appears_only_for_services_with_case_studies(): void
    {
        // ERP and Custom Systems are advertised with nothing behind them;
        // the section must stay absent rather than render an empty claim.
        $this->get('/en/services/mobile-apps')->assertOk()->assertSee('Hollo AI — The Twin Platform');
        $this->get('/en/services/erp')->assertOk()->assertDontSee(__('services_page.show.stack'));
    }

    public function test_the_stack_shown_is_the_one_used_on_that_service(): void
    {
        $this->get('/en/services/mobile-apps')->assertOk()->assertSee('Flutter');
    }

    public function test_unpublished_projects_never_appear_as_related_work(): void
    {
        Project::query()->update(['is_published' => false]);

        $this->get('/en/services/mobile-apps')
            ->assertOk()
            ->assertDontSee('Hollo AI — The Twin Platform');
    }

    public function test_the_navigation_links_to_each_service(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee(url('/en/services/mobile-apps'), escape: false)
            ->assertSee(url('/en/services/erp'), escape: false);
    }

    public function test_locale_helpers_still_resolve_on_a_detail_page(): void
    {
        $this->get('/en/services/mobile-apps')->assertOk();

        $this->assertSame('en', Locale::current());
    }
}
