<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_translatable_columns_round_trip_through_the_database(): void
    {
        $project = Project::factory()->create([
            'summary' => ['en' => 'English summary', 'ar' => 'ملخص عربي'],
        ]);

        $project->refresh();

        $this->assertSame('English summary', $project->summary->in('en'));
        $this->assertSame('ملخص عربي', $project->summary->in('ar'));
    }

    public function test_an_unnamed_client_is_never_exposed_by_name(): void
    {
        $client = Client::factory()->create([
            'name' => 'Acme Bank Internal Name',
            'is_named' => false,
            'anonymous_label' => ['en' => 'A leading regional bank'],
        ]);

        $this->assertSame('A leading regional bank', $client->display_name);
        $this->assertNotSame($client->name, $client->display_name);
    }

    public function test_a_permitted_client_is_shown_by_name(): void
    {
        $client = Client::factory()->named()->create(['name' => 'Topline Digital']);

        $this->assertSame('Topline Digital', $client->display_name);
    }

    public function test_a_project_without_proof_is_not_evidenced(): void
    {
        $project = Project::factory()->create();

        $this->assertFalse($project->is_evidenced);
    }

    public function test_a_project_with_screenshots_metrics_and_a_link_is_evidenced(): void
    {
        $project = Project::factory()->evidenced()->create();

        $this->assertTrue($project->is_evidenced);
    }

    public function test_published_scope_hides_drafts_and_scheduled_posts(): void
    {
        Post::factory()->create();
        Post::factory()->draft()->create();
        Post::factory()->scheduled()->create();

        $this->assertSame(1, Post::published()->count());
    }

    public function test_visitor_input_cannot_set_a_leads_internal_status(): void
    {
        $lead = Lead::create([
            'name' => 'Test Person',
            'email' => 'test@example.com',
            'status' => 'won',
            'notes' => 'injected',
        ]);

        $this->assertSame('new', $lead->status);
        $this->assertNull($lead->notes);
    }
}
