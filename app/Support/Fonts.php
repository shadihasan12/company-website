<?php

namespace App\Support;

use Illuminate\Support\Facades\Vite;

/**
 * Guards the `@fonts` directive against requesting a family that was not
 * built.
 *
 * Vite::fonts() throws on an unknown alias, so asking for the Arabic face
 * while it is commented out of vite.config.js would take every RTL page
 * down with a 500. Filtering here means enabling a locale degrades to the
 * Latin fallback instead of breaking the site.
 */
class Fonts
{
    /**
     * @param  list<string>  $aliases
     * @return list<string>
     */
    public static function available(array $aliases): array
    {
        $built = static::builtAliases();

        // Nothing built yet — during a first deploy, say. Let the directive
        // decide rather than second-guessing it.
        if ($built === null) {
            return $aliases;
        }

        return array_values(array_intersect($aliases, $built));
    }

    /**
     * @return list<string>|null
     */
    protected static function builtAliases(): ?array
    {
        // While the dev server is running the manifest is served by Vite
        // rather than written to disk.
        if (Vite::isRunningHot()) {
            return null;
        }

        $path = public_path('build/fonts-manifest.json');

        if (! file_exists($path)) {
            return null;
        }

        $manifest = json_decode((string) file_get_contents($path), true);

        return array_keys($manifest['families'] ?? []);
    }
}
