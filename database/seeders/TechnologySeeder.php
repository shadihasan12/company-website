<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        // `sort_order` is what a project card ranks by, and a card has room
        // for four chips. Platforms lead so a case study is identifiable at a
        // glance — Flutter or React first, then the runtime behind it — with
        // languages and the supporting patterns after. The homepage stack
        // section fixes its own column order, so this only affects ranking.
        $technologies = [
            'framework' => ['Flutter', 'React.js', 'Next.js', 'Node.js', 'NestJS', 'Express.js', 'BLoC', 'Clean Architecture', 'Tailwind CSS', 'Laravel'],
            'language' => ['Dart', 'TypeScript', 'JavaScript', 'Python', 'Kotlin', 'PHP'],
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
