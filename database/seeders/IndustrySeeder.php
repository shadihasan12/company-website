<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            'consumer-ai' => ['Consumer AI', 'الذكاء الاصطناعي الاستهلاكي', 'sparkles'],
            'community' => ['Community & Non-profit', 'المجتمع والقطاع غير الربحي', 'users'],
            'fintech' => ['Fintech & Payments', 'التقنية المالية والمدفوعات', 'credit-card'],
            'retail' => ['E-commerce & Retail', 'التجارة الإلكترونية والتجزئة', 'shopping-bag'],
            'mobility' => ['Transport & Mobility', 'النقل والتنقّل', 'truck'],
            'health' => ['Health & Fitness', 'الصحة واللياقة', 'heart'],
            'hospitality' => ['Hospitality', 'الضيافة', 'building'],
        ];

        $index = 0;

        foreach ($industries as $slug => [$name, $arabicName, $icon]) {
            Industry::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => ['en' => $name, 'ar' => $arabicName],
                    'icon' => $icon,
                    'sort_order' => $index++,
                ],
            );
        }
    }
}
