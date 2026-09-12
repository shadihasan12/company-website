<?php

namespace App\Support;

use App\Models\Industry;
use App\Models\Project;
use Illuminate\Support\Collection;

/**
 * The credibility band under the case studies.
 *
 * Computed figures come from real records so they cannot drift away from
 * the portfolio. Manual figures come from config and are omitted entirely
 * when unset — the band should never show a number nobody can stand behind.
 */
class SiteStats
{
    /**
     * @return Collection<int, array{key: string, value: float|int, decimals: int, suffix: string}>
     */
    public static function all(): Collection
    {
        return PerRequest::remember('site.stats', fn () => static::compute());
    }

    /**
     * @return Collection<int, array{key: string, value: float|int, decimals: int, suffix: string}>
     */
    protected static function compute(): Collection
    {
        return collect([
            ...static::computed(),
            ...static::configured(),
        ])->values();
    }

    /**
     * Published case studies, memoised because the hero and the stats band
     * both need the same figure on the homepage.
     */
    public static function publishedProjects(): int
    {
        return PerRequest::remember('site.published-projects', fn () => Project::published()->count());
    }

    /**
     * @return list<array{key: string, value: float|int, decimals: int, suffix: string}>
     */
    protected static function computed(): array
    {
        $stats = [];

        if ($shipped = static::publishedProjects()) {
            $stats[] = static::stat('projects', $shipped, suffix: '');
        }

        $founded = config('site.founded_year');

        if ($founded && ($years = now()->year - $founded) > 0) {
            $stats[] = static::stat('years', $years, suffix: '+');
        }

        $industries = Industry::whereHas('projects', fn ($query) => $query->published())->count();

        if ($industries > 1) {
            $stats[] = static::stat('industries', $industries);
        }

        return $stats;
    }

    /**
     * @return list<array{key: string, value: float|int, decimals: int, suffix: string}>
     */
    protected static function configured(): array
    {
        $formats = [
            'clients' => ['decimals' => 0, 'suffix' => '+'],
            'app_rating' => ['decimals' => 1, 'suffix' => ''],
            'uptime' => ['decimals' => 1, 'suffix' => '%'],
            'nps' => ['decimals' => 0, 'suffix' => ''],
        ];

        return collect(config('site.stats'))
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value, string $key) => static::stat(
                $key,
                $value,
                $formats[$key]['decimals'] ?? 0,
                $formats[$key]['suffix'] ?? '',
            ))
            ->values()
            ->all();
    }

    /**
     * @return array{key: string, value: float|int, decimals: int, suffix: string}
     */
    protected static function stat(string $key, float|int $value, int $decimals = 0, string $suffix = ''): array
    {
        return compact('key', 'value', 'decimals', 'suffix');
    }
}
