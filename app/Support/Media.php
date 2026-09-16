<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * The one place that decides where uploaded media lives.
 *
 * Screenshots, logos and avatars are addressed by relative path in the
 * database (`projects/gallery/wakil-topup-01.webp`), never by URL, so the
 * store behind them can change without touching a single row. Point
 * `MEDIA_DISK` at `s3` and every image is served from R2 instead of local
 * storage; nothing else in the application needs to know.
 *
 * This is deliberately separate from `FILESYSTEM_DISK`: that one governs
 * private application storage, which should not move just because public
 * media did.
 */
class Media
{
    public static function disk(): Filesystem
    {
        return Storage::disk(config('filesystems.media'));
    }

    /**
     * A browser-facing URL, or null for a missing path, so callers can use
     * it directly in a `@if` without a separate emptiness check.
     */
    public static function url(?string $path): ?string
    {
        return filled($path) ? static::disk()->url($path) : null;
    }

    public static function exists(?string $path): bool
    {
        return filled($path) && static::disk()->exists($path);
    }

    public static function put(string $path, string $contents): bool
    {
        return static::disk()->put($path, $contents);
    }
}
