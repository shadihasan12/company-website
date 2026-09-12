<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_screen(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_an_authenticated_but_unprivileged_user_is_refused(): void
    {
        // Registering an account must never imply access to content or leads.
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_an_admin_reaches_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin')
            ->assertOk();
    }

    /**
     * @return list<array{string}>
     */
    public static function resourcePages(): array
    {
        return [
            ['/admin/projects'],
            ['/admin/services'],
            ['/admin/clients'],
            ['/admin/testimonials'],
            ['/admin/posts'],
            ['/admin/industries'],
            ['/admin/technologies'],
            ['/admin/leads'],
        ];
    }

    #[DataProvider('resourcePages')]
    public function test_each_resource_listing_renders(string $url): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get($url)
            ->assertOk();
    }

    public function test_leads_cannot_be_created_from_the_panel(): void
    {
        // Leads only ever originate from the public site.
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin/leads/create')
            ->assertNotFound();
    }

    public function test_an_admin_can_edit_a_lead(): void
    {
        $lead = Lead::factory()->create();

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get("/admin/leads/{$lead->id}/edit")
            ->assertOk();
    }
}
