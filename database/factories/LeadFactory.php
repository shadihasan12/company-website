<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Lead> */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'company' => fake()->company(),
            'budget_range' => fake()->randomElement(['<5k', '5k-15k', '15k-50k', '50k+']),
            'timeline' => fake()->randomElement(['ASAP', '1-3 months', '3-6 months']),
            'message' => fake()->paragraph(),
            'source' => 'contact',
            'locale' => 'en',
        ];
    }
}
