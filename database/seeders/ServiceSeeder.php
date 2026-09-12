<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Seeds from config('site.services') and lang/{locale}/services.php, so
     * the navigation, the translations and the database cannot drift apart.
     */
    public function run(): void
    {
        $locales = array_keys(config('site.locales'));
        $fallback = config('site.fallback_locale');

        foreach (config('site.services') as $index => $service) {
            $key = $service['key'];

            Service::updateOrCreate(
                ['key' => $key],
                [
                    'slug' => $key,
                    'title' => $this->translate("services.{$key}.title", $locales),
                    'tagline' => $this->translate("services.{$key}.tagline", $locales),
                    'excerpt' => $this->translate("services.{$key}.excerpt", $locales),
                    'body' => $this->translate("services.{$key}.body", $locales),
                    'timeline' => $this->translate("services.{$key}.timeline", $locales),
                    // Lists are stored in the fallback locale only for now;
                    // translating them is part of enabling Arabic.
                    'inclusions' => __("services.{$key}.inclusions", locale: $fallback),
                    'faqs' => __("services.{$key}.faqs", locale: $fallback),
                    'icon' => $service['icon'],
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );
        }
    }

    /**
     * @param  list<string>  $locales
     * @return array<string, string>
     */
    protected function translate(string $key, array $locales): array
    {
        return collect($locales)
            ->mapWithKeys(fn (string $locale) => [$locale => __($key, locale: $locale)])
            ->all();
    }
}
