<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Notifications\LeadReceived;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ScopeWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        Notification::fake();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Rami Saad',
            'email' => 'rami@example.com',
            'service' => 'mobile-apps',
            'stage' => 'designs-ready',
            'platforms' => ['ios', 'android'],
            'features' => ['accounts', 'payments', 'chat'],
            'timeline' => '1-3-months',
            'budget_range' => '25k-60k',
            'message' => 'Designs are done, we need engineering.',
            'website' => '',
            'loaded_at' => encrypt(now()->subMinute()->timestamp),
        ], $overrides);
    }

    public function test_the_wizard_renders_every_step(): void
    {
        $response = $this->get('/en/start-a-project')->assertOk();

        foreach (['service', 'platforms', 'features', 'stage', 'timing', 'details'] as $step) {
            $response->assertSee(__("estimator.steps.{$step}"));
        }
    }

    public function test_a_completed_scope_becomes_a_lead_with_its_answers(): void
    {
        $this->post('/en/start-a-project', $this->payload())
            ->assertRedirect('/en/contact/thank-you');

        $lead = Lead::sole();

        $this->assertSame('estimator', $lead->source);
        $this->assertSame('designs-ready', $lead->payload['stage']);
        $this->assertEqualsCanonicalizing(['ios', 'android'], $lead->payload['platforms']);
        $this->assertEqualsCanonicalizing(['accounts', 'payments', 'chat'], $lead->payload['features']);
        $this->assertSame('25k-60k', $lead->budget_range);
        $this->assertNotNull($lead->service_id);
    }

    public function test_the_team_is_notified_about_a_scope(): void
    {
        $this->post('/en/start-a-project', $this->payload());

        Notification::assertSentOnDemand(LeadReceived::class);
    }

    public function test_answers_outside_the_configured_options_are_rejected(): void
    {
        // The payload must never hold values the site does not offer.
        $this->post('/en/start-a-project', $this->payload([
            'platforms' => ['ios', 'smartwatch'],
            'features' => ['telepathy'],
            'stage' => 'whenever',
        ]))->assertSessionHasErrors(['platforms.1', 'features.0', 'stage']);

        $this->assertSame(0, Lead::count());
    }

    public function test_contact_details_are_still_required(): void
    {
        $this->post('/en/start-a-project', $this->payload(['name' => '', 'email' => '']))
            ->assertSessionHasErrors(['name', 'email']);
    }

    public function test_a_scope_with_no_optional_answers_still_captures(): void
    {
        $this->post('/en/start-a-project', [
            'name' => 'Minimal Person',
            'email' => 'min@example.com',
            'website' => '',
            'loaded_at' => encrypt(now()->subMinute()->timestamp),
        ])->assertRedirect('/en/contact/thank-you');

        $this->assertSame(1, Lead::count());
    }

    public function test_the_honeypot_and_fill_time_apply_here_too(): void
    {
        $this->post('/en/start-a-project', $this->payload(['website' => 'spam']))
            ->assertSessionHasErrors('website');

        $this->post('/en/start-a-project', $this->payload(['loaded_at' => encrypt(now()->timestamp)]))
            ->assertStatus(422);

        $this->assertSame(0, Lead::count());
    }

    public function test_primary_calls_to_action_lead_to_the_wizard(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee(url('/en/start-a-project'), escape: false);
    }

    public function test_a_service_page_carries_its_service_into_the_contact_form(): void
    {
        $this->get('/en/services/erp')
            ->assertOk()
            ->assertSee('contact?service=erp', escape: false);
    }
}
