<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeds real, verifiable content only.
     *
     * Invented metrics, testimonials and blog posts live in
     * DemoContentSeeder and must be requested explicitly.
     */
    public function run(): void
    {
        $this->call([
            IndustrySeeder::class,
            TechnologySeeder::class,
            ServiceSeeder::class,
            PortfolioSeeder::class,
        ]);

        $this->command?->newLine();
        $this->command?->info('Seeded. Run `php artisan content:audit` to see what content is still missing.');
    }
}
