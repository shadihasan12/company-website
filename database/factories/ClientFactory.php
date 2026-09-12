<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Client> */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            // Default to unnamed: publishing a client name without written
            // permission is the riskier default to get wrong.
            'is_named' => false,
            'anonymous_label' => ['en' => 'A '.fake()->word().' company'],
            'logo_path' => null,
            'website_url' => null,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    public function named(): static
    {
        return $this->state(fn () => ['is_named' => true]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
