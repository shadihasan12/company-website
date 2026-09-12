<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Support\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_index_lists_published_posts_newest_first(): void
    {
        Post::factory()->create(['title' => ['en' => 'Older Post'], 'published_at' => now()->subWeek()]);
        Post::factory()->create(['title' => ['en' => 'Newer Post'], 'published_at' => now()->subDay()]);

        $html = $this->get('/en/insights')->assertOk()->getContent();

        $this->assertLessThan(strpos($html, 'Older Post'), strpos($html, 'Newer Post'));
    }

    public function test_drafts_and_scheduled_posts_are_hidden_from_the_index(): void
    {
        Post::factory()->draft()->create(['title' => ['en' => 'Draft Post']]);
        Post::factory()->scheduled()->create(['title' => ['en' => 'Scheduled Post']]);

        $this->get('/en/insights')
            ->assertOk()
            ->assertDontSee('Draft Post')
            ->assertDontSee('Scheduled Post');
    }

    public function test_a_draft_is_not_reachable_by_its_url(): void
    {
        // The listing rule must hold for a direct visit too.
        $draft = Post::factory()->draft()->create(['slug' => 'secret-draft']);

        $this->get("/en/insights/{$draft->slug}")->assertNotFound();
    }

    public function test_a_scheduled_post_is_not_reachable_early(): void
    {
        $scheduled = Post::factory()->scheduled()->create(['slug' => 'next-week']);

        $this->get("/en/insights/{$scheduled->slug}")->assertNotFound();
    }

    public function test_filtering_by_category_narrows_the_list(): void
    {
        Post::factory()->create(['title' => ['en' => 'Mobile Piece'], 'category' => 'mobile']);
        Post::factory()->create(['title' => ['en' => 'AI Piece'], 'category' => 'ai']);

        $this->get('/en/insights?category=mobile')
            ->assertOk()
            ->assertSee('Mobile Piece')
            ->assertDontSee('AI Piece');
    }

    public function test_only_categories_with_published_posts_are_offered(): void
    {
        Post::factory()->create(['category' => 'mobile']);
        Post::factory()->draft()->create(['category' => 'company']);

        // Asserted on the filter link rather than the label: "Company" is
        // also a footer heading.
        $this->get('/en/insights')
            ->assertOk()
            ->assertSee('category=mobile', escape: false)
            ->assertDontSee('category=company', escape: false);
    }

    public function test_the_empty_state_distinguishes_no_posts_from_no_matches(): void
    {
        $this->get('/en/insights')->assertOk()->assertSee(__('blog.empty'));

        Post::factory()->create(['category' => 'mobile']);

        $this->get('/en/insights?category=ai')->assertOk()->assertSee(__('blog.empty_filtered'));
    }

    public function test_the_insights_link_appears_only_once_something_is_published(): void
    {
        // Sending a visitor to an empty page costs more than a missing link.
        $this->get('/en')->assertOk()->assertDontSee(__('nav.insights'));

        Post::factory()->create();

        $this->get('/en')->assertOk()->assertSee(__('nav.insights'));
    }

    public function test_a_post_renders_its_body_with_anchored_headings(): void
    {
        $post = Post::factory()->create([
            'slug' => 'clean-architecture',
            'title' => ['en' => 'Why clean architecture pays for itself'],
            'body' => ['en' => '<h2>The first year</h2><p>Body.</p><h2>The second year</h2><p>More.</p><h3>A detail</h3><p>Detail.</p>'],
        ]);

        $this->get("/en/insights/{$post->slug}")
            ->assertOk()
            ->assertSee('id="the-first-year"', escape: false)
            ->assertSee('href="#the-second-year"', escape: false)
            ->assertSee(__('blog.contents'));
    }

    public function test_the_contents_list_is_omitted_for_a_short_post(): void
    {
        // Two headings is a list, not a table of contents.
        $post = Post::factory()->create([
            'slug' => 'short',
            'body' => ['en' => '<h2>One</h2><p>Body.</p><h2>Two</h2><p>Body.</p>'],
        ]);

        $this->get("/en/insights/{$post->slug}")->assertOk()->assertDontSee(__('blog.contents'));
    }

    public function test_duplicate_headings_get_distinct_anchors(): void
    {
        $article = Article::fromHtml('<h2>Setup</h2><h2>Setup</h2><h2>Setup</h2>');

        $this->assertSame(['setup', 'setup-2', 'setup-3'], array_column($article->headings, 'id'));
    }

    public function test_an_author_supplied_heading_id_is_respected(): void
    {
        $article = Article::fromHtml('<h2 id="custom">Setup</h2>');

        $this->assertStringContainsString('id="custom"', $article->html);
        $this->assertStringNotContainsString('id="setup"', $article->html);
    }

    public function test_reading_time_is_never_zero_for_a_post_with_content(): void
    {
        $this->assertSame(1, Article::readingMinutes('<p>Three little words.</p>'));
        $this->assertSame(0, Article::fromHtml('')->readingMinutes);
    }

    public function test_a_post_publishes_article_structured_data(): void
    {
        $post = Post::factory()->create(['slug' => 'schema-post', 'author_name' => 'Shadi Hasan']);

        $this->get("/en/insights/{$post->slug}")
            ->assertOk()
            ->assertSee('"@type":"Article"', escape: false)
            ->assertSee('"datePublished"', escape: false)
            ->assertSee('Shadi Hasan');
    }

    public function test_related_posts_are_topped_up_when_the_category_is_thin(): void
    {
        $post = Post::factory()->create(['slug' => 'main', 'category' => 'mobile']);
        Post::factory()->create(['category' => 'mobile']);
        Post::factory()->count(3)->create(['category' => 'ai']);

        $related = $this->get("/en/insights/{$post->slug}")->assertOk()->viewData('related');

        // Three, not one: a half-empty section reads as a mistake.
        $this->assertCount(3, $related);
        $this->assertFalse($related->contains('id', $post->id));
    }

    public function test_related_posts_prefer_the_same_category(): void
    {
        $post = Post::factory()->create(['slug' => 'main', 'category' => 'mobile']);
        Post::factory()->create(['title' => ['en' => 'Same Category'], 'category' => 'mobile']);
        Post::factory()->count(3)->create(['category' => 'ai']);

        $response = $this->get("/en/insights/{$post->slug}")->assertOk();

        $response->assertSee(__('blog.related'));
        $response->assertSee('Same Category');
    }

    public function test_the_related_section_is_absent_for_the_only_post(): void
    {
        $post = Post::factory()->create(['slug' => 'lonely']);

        $this->get("/en/insights/{$post->slug}")->assertOk()->assertDontSee(__('blog.related'));
    }
}
