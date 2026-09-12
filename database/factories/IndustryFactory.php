<?php

namespace Database\Factories;

use App\Models\Industry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Industry> */
class IndustryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Fintech', 'Healthcare', 'Retail', 'Logistics', 'Education', 'Hospitality',
        ]);

        return [
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'name' => ['en' => $name],
            'icon' => 'building',
            'sort_order' => 0,
        ];
    }
}
