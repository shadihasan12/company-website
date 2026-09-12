<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            'consumer-ai' => 'Consumer AI',
            'community' => 'Community & Non-profit',
            'fintech' => 'Fintech & Payments',
            'retail' => 'E-commerce & Retail',
            'mobility' => 'Transport & Mobility',
            'health' => 'Health & Fitness',
            'hospitality' => 'Hospitality',
        ];

        foreach (array_values(array_keys($industries)) as $index => $slug) {
            Industry::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => ['en' => $industries[$slug]],
                    'sort_order' => $index,
                ],
            );
        }
    }
}
