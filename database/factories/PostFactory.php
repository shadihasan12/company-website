<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->sentence(6));

        return [
            'slug' => Str::slug($title),
            'title' => ['en' => $title],
            'excerpt' => ['en' => fake()->sentence(20)],
            'body' => ['en' => fake()->paragraphs(6, true)],
            'author_name' => fake()->name(),
            'reading_minutes' => fake()->numberBetween(3, 12),
            'published_at' => fake()->dateTimeBetween('-1 year'),
            'is_featured' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['published_at' => now()->addWeek()]);
    }
}
