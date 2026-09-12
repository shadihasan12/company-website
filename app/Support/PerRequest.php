<?php

namespace App\Support;

use Closure;

/**
 * Memoises a value for the duration of one HTTP request.
 *
 * Deliberately not `once()`: on a static call site that caches for the
 * life of the process, which goes stale between requests under Octane and
 * inside tests — a bug this project has already hit once.
 *
 * The cache lives on the current Request's attribute bag, which is created
 * fresh for every request in every environment, including each call in a
 * test.
 */
class PerRequest
{
    public static function remember(string $key, Closure $callback): mixed
    {
        $attributes = request()->attributes;
        $key = "memo.{$key}";

        if (! $attributes->has($key)) {
            $attributes->set($key, $callback());
        }

        return $attributes->get($key);
    }
}
