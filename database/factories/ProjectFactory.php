<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Industry;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Project> */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(2, true));

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'client_id' => Client::factory(),
            'industry_id' => Industry::factory(),
            'summary' => ['en' => fake()->sentence(18)],
            'problem' => ['en' => fake()->paragraph()],
            'solution' => ['en' => fake()->paragraph()],
            'outcome' => ['en' => fake()->paragraph()],
            'metrics' => [],
            'gallery' => [],
            'duration' => ['en' => fake()->numberBetween(2, 12).' months'],
            'completed_at' => fake()->dateTimeBetween('-3 years'),
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    /** A case study with the proof that actually converts. */
    public function evidenced(): static
    {
        return $this->state(fn () => [
            'gallery' => ['placeholders/screen-1.svg', 'placeholders/screen-2.svg'],
            'metrics' => [['label' => 'Downloads', 'value' => 50000, 'suffix' => '+']],
            'google_play_url' => 'https://play.google.com/store/apps/details?id=example',
        ]);
    }
}
