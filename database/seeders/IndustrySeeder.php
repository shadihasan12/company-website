<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            'consumer-ai' => ['Consumer AI', 'sparkles'],
            'community' => ['Community & Non-profit', 'users'],
            'fintech' => ['Fintech & Payments', 'credit-card'],
            'retail' => ['E-commerce & Retail', 'shopping-bag'],
            'mobility' => ['Transport & Mobility', 'truck'],
            'health' => ['Health & Fitness', 'heart'],
            'hospitality' => ['Hospitality', 'building'],
        ];

        $index = 0;

        foreach ($industries as $slug => [$name, $icon]) {
            Industry::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => ['en' => $name],
                    'icon' => $icon,
                    'sort_order' => $index++,
                ],
            );
        }
    }
}
