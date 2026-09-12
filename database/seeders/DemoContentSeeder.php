<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Project;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Obviously-fake content for laying out pages that have nothing real to
 * show yet — the results band, the testimonial carousel, the blog index.
 *
 * Kept OUT of DatabaseSeeder on purpose. Invented testimonials and metrics
 * are exactly the kind of placeholder that ships to production by accident,
 * and fabricated social proof on a live site is a genuine liability. Every
 * string here is prefixed so it is impossible to mistake for real content.
 *
 *   php artisan db:seed --class=DemoContentSeeder
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('Seeding DEMO content. Never run this against production.');

        foreach (Project::featured()->get() as $index => $project) {
            $project->update([
                'metrics' => [
                    ['label' => 'SAMPLE — Downloads', 'value' => 120000, 'suffix' => '+'],
                    ['label' => 'SAMPLE — Store rating', 'value' => 4.8, 'decimals' => 1],
                    ['label' => 'SAMPLE — Faster checkout', 'value' => 38, 'suffix' => '%'],
                ],
            ]);

            Testimonial::updateOrCreate(
                ['project_id' => $project->id, 'author_name' => 'SAMPLE — Replace before launch'],
                [
                    'client_id' => $project->client_id,
                    'author_title' => ['en' => 'SAMPLE — Job title'],
                    'quote' => ['en' => 'SAMPLE TESTIMONIAL — this is placeholder text for layout only. Replace it with a real, attributed client quote before this site goes live.'],
                    // Deliberately unrated: a rating here would feed a
                    // fabricated AggregateRating into the site's structured
                    // data, which is a manual-action risk with search
                    // engines rather than a harmless placeholder.
                    'rating' => null,
                    'is_featured' => true,
                    'sort_order' => $index,
                ],
            );
        }

        Post::factory()
            ->count(6)
            ->sequence(fn ($sequence) => [
                'slug' => 'sample-post-'.($sequence->index + 1),
                'title' => ['en' => 'SAMPLE POST '.($sequence->index + 1).' — replace before launch'],
                'author_name' => 'SAMPLE — Author',
                'category' => config('site.post_categories')[$sequence->index % count(config('site.post_categories'))],
                'body' => ['en' => '<h2>First heading</h2><p>'.fake()->paragraphs(3, true).'</p><h2>Second heading</h2><p>'.fake()->paragraphs(3, true).'</p><h3>A sub-heading</h3><p>'.fake()->paragraphs(2, true).'</p>'],
            ])
            ->create();
    }
}
