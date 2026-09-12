<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            'language' => ['Dart', 'Kotlin', 'TypeScript', 'JavaScript', 'Python', 'PHP'],
            'framework' => ['Flutter', 'BLoC', 'Clean Architecture', 'NestJS', 'Express.js', 'Laravel'],
            'service' => ['Firebase', 'AWS Rekognition', 'WebRTC', 'WebSockets', 'Google Play Billing', 'Apple Pay', 'Sentry'],
            'tool' => ['GetIt', 'Injectable', 'Freezed', 'SQLite', 'dio', 'CI/CD'],
        ];

        $order = 0;

        foreach ($technologies as $category => $names) {
            foreach ($names as $name) {
                Technology::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'category' => $category,
                        'sort_order' => $order++,
                    ],
                );
            }
        }
    }
}
