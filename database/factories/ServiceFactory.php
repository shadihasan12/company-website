<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Service> */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(2, true));
        $key = Str::slug($title);

        return [
            'key' => $key,
            'slug' => $key,
            'title' => ['en' => $title],
            'tagline' => ['en' => fake()->sentence(4)],
            'excerpt' => ['en' => fake()->sentence(16)],
            'body' => ['en' => fake()->paragraphs(3, true)],
            'inclusions' => [],
            'faqs' => [],
            'icon' => 'sparkles',
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
