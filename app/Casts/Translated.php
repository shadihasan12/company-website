<?php

namespace App\Casts;

use App\Support\TranslatedText;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Casts a JSON column of per-locale strings to a {@see TranslatedText}.
 *
 * @implements CastsAttributes<TranslatedText, TranslatedText|array<string, string|null>|string|null>
 */
class Translated implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): TranslatedText
    {
        return new TranslatedText(
            is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        return [$key => json_encode(
            TranslatedText::make($value)->toArray(),
            JSON_UNESCAPED_UNICODE,
        )];
    }
}
