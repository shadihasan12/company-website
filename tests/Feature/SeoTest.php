<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Project;
use App\Models\Testimonial;
use App\Support\Seo;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_a_non_production_environment_is_never_indexable(): void
    {
        // A staging copy competing with production in search results is a
        // genuinely expensive mistake.
        $this->assertFalse(Seo::isIndexable());

        $this->get('/en')->assertOk()->assertSee('noindex, nofollow', escape: false);
    }

    public function test_indexing_can_be_forced_on(): void
    {
        config()->set('site.indexable', true);

        $this->get('/en')
            ->assertOk()
            ->assertSee('index, follow, max-image-preview:large', escape: false)
            ->assertDontSee('noindex', escape: false);
    }

    public function test_robots_closes_the_site_off_while_not_indexable(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /');
    }

    public function test_robots_opens_up_and_points_at_the_sitemap_once_indexable(): void
    {
        config()->set('site.indexable', true);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_every_page_carries_a_canonical_without_the_query_string(): void
    {
        $this->get('/en/work?service=mobile-apps&page=2')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.url('/en/work').'">', escape: false);
    }

    public function test_the_organization_node_is_emitted_on_every_page(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('"@type":"Organization"', escape: false)
            ->assertSee('"@id":"'.url('/').'#organization"', escape: false);
    }

    public function test_no_aggregate_rating_is_published_without_real_reviews(): void
    {
        // Fabricated review data is a manual-action risk, not a placeholder.
        $this->get('/en')->assertOk()->assertDontSee('AggregateRating', escape: false);
    }

    public function test_demo_content_cannot_produce_a_fabricated_rating(): void
    {
        $this->seed(DemoContentSeeder::class);

        $this->get('/en')->assertOk()->assertDontSee('AggregateRating', escape: false);
    }

    public function test_an_aggregate_rating_appears_once_rated_testimonials_exist(): void
    {
        Testimonial::factory()->create(['rating' => 5]);
        Testimonial::factory()->create(['rating' => 4]);

        $this->get('/en')
            ->assertOk()
            ->assertSee('"@type":"AggregateRating"', escape: false)
            ->assertSee('"ratingValue":4.5', escape: false)
            ->assertSee('"reviewCount":2', escape: false);
    }

    public function test_a_service_page_publishes_service_and_breadcrumb_schema(): void
    {
        $this->get('/en/services/mobile-apps')
            ->assertOk()
            ->assertSee('"@type":"Service"', escape: false)
            ->assertSee('"@type":"BreadcrumbList"', escape: false)
            ->assertSee('"@type":"FAQPage"', escape: false);
    }

    public function test_a_case_study_publishes_creative_work_and_breadcrumb_schema(): void
    {
        $this->get('/en/work/wakil-topup')
            ->assertOk()
            ->assertSee('"@type":"CreativeWork"', escape: false)
            ->assertSee('"@type":"BreadcrumbList"', escape: false);
    }

    public function test_open_graph_tags_describe_the_page(): void
    {
        $this->get('/en/services/erp')
            ->assertOk()
            ->assertSee('property="og:title"', escape: false)
            ->assertSee('property="og:url" content="'.url('/en/services/erp').'"', escape: false)
            ->assertSee('property="og:type" content="website"', escape: false);
    }

    public function test_a_post_declares_itself_as_an_article(): void
    {
        $post = Post::factory()->create(['slug' => 'og-post']);

        $this->get("/en/insights/{$post->slug}")
            ->assertOk()
            ->assertSee('property="og:type" content="article"', escape: false);
    }

    public function test_no_broken_image_tag_is_emitted_without_an_image(): void
    {
        // A dangling og:image is worse than none: it renders as a blank card.
        $this->get('/en')
            ->assertOk()
            ->assertDontSee('property="og:image"', escape: false)
            ->assertSee('name="twitter:card" content="summary"', escape: false);
    }

    public function test_hreflang_is_omitted_while_only_one_locale_is_live(): void
    {
        $this->get('/en')->assertOk()->assertDontSee('hreflang=', escape: false);
    }

    public function test_the_sitemap_lists_public_pages_and_published_records(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $response->assertSee(url('/en'), escape: false);
        $response->assertSee(url('/en/services/mobile-apps'), escape: false);
        $response->assertSee(url('/en/work/wakil-topup'), escape: false);
        $response->assertSee(url('/en/start-a-project'), escape: false);
    }

    public function test_the_sitemap_excludes_unpublished_and_private_pages(): void
    {
        Project::where('slug', 'reserva')->update(['is_published' => false]);
        Post::factory()->draft()->create(['slug' => 'hidden-draft']);

        $response = $this->get('/sitemap.xml')->assertOk();

        $response->assertDontSee('/work/reserva', escape: false);
        $response->assertDontSee('hidden-draft', escape: false);
        $response->assertDontSee('/styleguide', escape: false);
        $response->assertDontSee('/admin', escape: false);
    }

    public function test_the_blog_index_enters_the_sitemap_only_once_a_post_exists(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertDontSee(url('/en/insights').'<', escape: false);

        Post::factory()->create();

        $this->get('/sitemap.xml')->assertOk()->assertSee(url('/en/insights'), escape: false);
    }
}
