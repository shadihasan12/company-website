<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PortfolioSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Seeders bootstrap content; the admin panel owns it afterwards.
 *
 * Re-running a seeder used to overwrite existing rows, which silently
 * destroyed imported screenshots, edited copy and store links — a real
 * incident during development, and one that would have cost the client
 * their uploads in production.
 */
class SeederSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_reseeding_does_not_destroy_uploaded_screenshots(): void
    {
        $project = Project::where('slug', 'wakil-topup')->firstOrFail();
        $project->update([
            'gallery' => ['projects/gallery/wakil-topup-01.webp'],
            'hero_image_path' => 'projects/gallery/wakil-topup-01.webp',
        ]);

        $this->seed(PortfolioSeeder::class);

        $project->refresh();

        $this->assertSame(['projects/gallery/wakil-topup-01.webp'], $project->gallery);
        $this->assertNotNull($project->hero_image_path);
    }

    public function test_reseeding_does_not_discard_edited_copy(): void
    {
        Project::where('slug', 'reserva')->firstOrFail()->update([
            'summary' => ['en' => 'Rewritten by the client in the admin panel.'],
            'metrics' => [['label' => 'Bookings', 'value' => 12000]],
        ]);

        $this->seed(PortfolioSeeder::class);

        $project = Project::where('slug', 'reserva')->firstOrFail();

        $this->assertSame('Rewritten by the client in the admin panel.', (string) $project->summary);
        $this->assertNotEmpty($project->metrics);
    }

    public function test_reseeding_does_not_discard_edited_service_copy(): void
    {
        Service::where('key', 'erp')->firstOrFail()->update([
            'title' => ['en' => 'Client Edited Title'],
        ]);

        $this->seed(ServiceSeeder::class);

        $this->assertSame('Client Edited Title', (string) Service::where('key', 'erp')->firstOrFail()->title);
    }

    public function test_seed_refresh_resets_deliberately(): void
    {
        Project::where('slug', 'reserva')->firstOrFail()->update([
            'summary' => ['en' => 'Temporary edit.'],
        ]);

        putenv('SEED_REFRESH=true');
        $this->seed(PortfolioSeeder::class);
        putenv('SEED_REFRESH');

        $this->assertNotSame(
            'Temporary edit.',
            (string) Project::where('slug', 'reserva')->firstOrFail()->summary,
        );
    }

    public function test_seeding_still_creates_missing_records(): void
    {
        Project::where('slug', 'reserva')->delete();

        $this->seed(PortfolioSeeder::class);

        $this->assertTrue(Project::where('slug', 'reserva')->exists());
    }

    public function test_store_links_survive_a_reseed(): void
    {
        // They live in the seeder data now, so they are restored rather
        // than wiped — but an admin edit must still win.
        $project = Project::where('slug', 'hollo-ai')->firstOrFail();

        $this->assertStringContainsString('apps.apple.com', (string) $project->app_store_url);

        $project->update(['app_store_url' => 'https://apps.apple.com/us/app/changed/id1']);
        $this->seed(PortfolioSeeder::class);

        $this->assertSame(
            'https://apps.apple.com/us/app/changed/id1',
            Project::where('slug', 'hollo-ai')->firstOrFail()->app_store_url,
        );
    }
}
