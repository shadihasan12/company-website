<?php

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Technology> */
class TechnologyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'slug' => Str::slug($name),
            'name' => Str::title($name),
            'category' => fake()->randomElement(['language', 'framework', 'service', 'tool']),
            'sort_order' => 0,
        ];
    }
}
