<?php

namespace App\Support;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Stringable;

/**
 * A value stored once per locale.
 *
 * Rendering it resolves to the active locale, so views stay unchanged
 * whether one language is live or three:
 *
 *     {{ $service->title }}            // active locale
 *     $service->title->in('ar')        // a specific locale
 *     $service->title->all()           // every locale
 *
 * Arabic is deferred but every translatable column already exists, so
 * enabling it is a content pass rather than a migration.
 *
 * ArrayAccess is implemented so `data_get($model, 'title.en')` resolves —
 * that is how the admin panel binds one form field per locale.
 *
 * @implements Arrayable<string, string>
 * @implements ArrayAccess<string, string|null>
 */
class TranslatedText implements Arrayable, ArrayAccess, JsonSerializable, Stringable
{
    /**
     * @param  array<string, string|null>  $values
     */
    public function __construct(protected array $values = []) {}

    /**
     * Build from whatever a caller supplies — an array of locales, a plain
     * string (treated as the fallback locale), or an existing instance.
     *
     * @param  self|array<string, string|null>|string|null  $value
     */
    public static function make(self|array|string|null $value): self
    {
        return match (true) {
            $value instanceof self => $value,
            is_array($value) => new self($value),
            $value === null => new self,
            default => new self([config('site.fallback_locale') => $value]),
        };
    }

    /**
     * The value for a locale, falling back to the configured fallback and
     * then to the first non-empty translation. Returns null only when the
     * field has no content in any locale.
     */
    public function in(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        foreach ([$locale, config('site.fallback_locale')] as $candidate) {
            if (filled($this->values[$candidate] ?? null)) {
                return $this->values[$candidate];
            }
        }

        // A partially translated record should still render something
        // rather than a blank space on the page.
        return collect($this->values)->first(fn ($value) => filled($value));
    }

    public function has(?string $locale = null): bool
    {
        return filled($this->values[$locale ?? app()->getLocale()] ?? null);
    }

    public function isEmpty(): bool
    {
        return $this->in() === null;
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return $this->values;
    }

    public function jsonSerialize(): mixed
    {
        return $this->values;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet(mixed $offset): ?string
    {
        return $this->values[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset]);
    }

    public function __toString(): string
    {
        return $this->in() ?? '';
    }
}
