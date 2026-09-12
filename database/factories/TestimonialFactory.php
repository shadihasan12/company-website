<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Testimonial> */
class TestimonialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'author_name' => fake()->name(),
            'author_title' => ['en' => fake()->jobTitle()],
            'quote' => ['en' => fake()->sentence(24)],
            'rating' => 5,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
