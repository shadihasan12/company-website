<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadReceived;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        Notification::fake();
        RateLimiter::clear('');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Dana Khoury',
            'email' => 'dana@example.com',
            'company' => 'Example Ltd',
            'phone' => '+961 3 000 000',
            'service' => 'mobile-apps',
            'budget_range' => '10k-25k',
            'timeline' => 'asap',
            'message' => 'We need a Flutter app for our delivery fleet, launching in the Gulf.',
            'website' => '',
            // Old enough to clear the minimum fill time.
            'loaded_at' => encrypt(now()->subMinute()->timestamp),
        ], $overrides);
    }

    public function test_the_contact_page_renders_with_every_field(): void
    {
        $this->get('/en/contact')
            ->assertOk()
            ->assertSee(__('contact.fields.name'))
            ->assertSee(__('contact.fields.budget_range'))
            ->assertSee(__('contact.submit'));
    }

    public function test_arriving_from_a_service_page_preselects_that_service(): void
    {
        $this->get('/en/contact?service=erp')
            ->assertOk()
            ->assertSee('value="erp" selected', escape: false);
    }

    public function test_a_valid_enquiry_is_stored_and_redirects_to_thanks(): void
    {
        $this->post('/en/contact', $this->payload())
            ->assertRedirect('/en/contact/thank-you');

        $lead = Lead::sole();

        $this->assertSame('Dana Khoury', $lead->name);
        $this->assertSame('contact', $lead->source);
        $this->assertSame('new', $lead->status);
        $this->assertSame('en', $lead->locale);
        $this->assertNotNull($lead->service_id);
    }

    public function test_the_team_is_notified(): void
    {
        $this->post('/en/contact', $this->payload());

        Notification::assertSentOnDemand(LeadReceived::class);
    }

    public function test_a_lead_survives_a_mail_failure(): void
    {
        // Losing a genuine enquiry because SMTP is misconfigured would be
        // far worse than a missing notification.
        Notification::shouldReceive('route')->andThrow(new \RuntimeException('SMTP down'));

        $this->post('/en/contact', $this->payload())
            ->assertRedirect('/en/contact/thank-you');

        $this->assertSame(1, Lead::count());
    }

    public function test_validation_failures_return_to_the_form(): void
    {
        $this->from('/en/contact')
            ->post('/en/contact', $this->payload(['email' => 'not-an-email', 'message' => 'short']))
            ->assertRedirect('/en/contact')
            ->assertSessionHasErrors(['email', 'message']);

        $this->assertSame(0, Lead::count());
    }

    public function test_the_honeypot_rejects_a_filled_trap_field(): void
    {
        $this->post('/en/contact', $this->payload(['website' => 'https://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Lead::count());
    }

    public function test_a_submission_faster_than_a_human_is_rejected(): void
    {
        $this->post('/en/contact', $this->payload(['loaded_at' => encrypt(now()->timestamp)]))
            ->assertStatus(422);

        $this->assertSame(0, Lead::count());
    }

    public function test_a_tampered_timestamp_is_rejected_without_a_server_error(): void
    {
        $this->post('/en/contact', $this->payload(['loaded_at' => 'not-encrypted']))
            ->assertStatus(422);

        $this->assertSame(0, Lead::count());
    }

    public function test_an_unknown_service_or_budget_is_rejected(): void
    {
        $this->post('/en/contact', $this->payload(['service' => 'nope', 'budget_range' => 'free']))
            ->assertSessionHasErrors(['service', 'budget_range']);
    }

    public function test_submissions_are_rate_limited(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post('/en/contact', $this->payload(['email' => "person{$i}@example.com"]));
        }

        $this->post('/en/contact', $this->payload(['email' => 'flood@example.com']))
            ->assertStatus(429);
    }

    public function test_the_thank_you_page_renders(): void
    {
        $this->get('/en/contact/thank-you')
            ->assertOk()
            ->assertSee(__('contact.thanks.heading'));
    }

    public function test_the_newsletter_captures_an_email_as_its_own_source(): void
    {
        $this->from('/en')
            ->post('/en/newsletter', ['email' => 'reader@example.com', 'website' => ''])
            ->assertRedirect('/en')
            ->assertSessionHas('newsletter');

        $lead = Lead::sole();

        $this->assertSame('newsletter', $lead->source);
        $this->assertSame('reader@example.com', $lead->email);
    }

    public function test_the_newsletter_honeypot_works_too(): void
    {
        $this->post('/en/newsletter', ['email' => 'bot@example.com', 'website' => 'spam'])
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Lead::count());
    }

    public function test_visitor_input_still_cannot_set_internal_lead_fields(): void
    {
        $this->post('/en/contact', $this->payload(['status' => 'won', 'notes' => 'injected']));

        $lead = Lead::sole();

        $this->assertSame('new', $lead->status);
        $this->assertNull($lead->notes);
    }

    public function test_an_admin_can_export_leads_as_csv(): void
    {
        $this->post('/en/contact', $this->payload());

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin/leads')
            ->assertOk()
            ->assertSee('Export CSV');
    }

    public function test_the_whatsapp_float_renders_when_a_number_is_configured(): void
    {
        $this->get('/en')->assertOk()->assertSee('wa.me/'.config('site.contact.whatsapp'), escape: false);
    }
}
