<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;

/**
 * Builds one input per active locale for a translatable column.
 *
 * While only English is live these render as ordinary fields. The moment a
 * second locale is uncommented in config/site.php they become tabs, with no
 * change to any resource.
 */
class Translatable
{
    /** @return list<Component|Field> */
    public static function text(string $name, string $label, bool $required = false): array
    {
        return static::build($name, $label, $required, fn (string $path) => TextInput::make($path)->maxLength(255));
    }

    /** @return list<Component|Field> */
    public static function textarea(string $name, string $label, bool $required = false, int $rows = 3): array
    {
        return static::build($name, $label, $required, fn (string $path) => Textarea::make($path)->rows($rows));
    }

    /** @return list<Component|Field> */
    public static function richEditor(string $name, string $label, bool $required = false): array
    {
        return static::build($name, $label, $required, fn (string $path) => RichEditor::make($path));
    }

    /**
     * @param  callable(string): Field  $factory
     * @return list<Component|Field>
     */
    protected static function build(string $name, string $label, bool $required, callable $factory): array
    {
        $locales = config('site.locales');
        $fallback = config('site.fallback_locale');

        if (count($locales) === 1) {
            $locale = array_key_first($locales);

            return [
                $factory("{$name}.{$locale}")
                    ->label($label)
                    ->required($required)
                    ->columnSpanFull(),
            ];
        }

        return [
            Tabs::make($label)
                ->tabs(collect($locales)->map(fn (array $meta, string $code) => Tabs\Tab::make($meta['native'])
                    ->schema([
                        $factory("{$name}.{$code}")
                            ->label($label)
                            // Only the fallback locale is mandatory, so a
                            // record can be saved before it is translated.
                            ->required($required && $code === $fallback)
                            ->columnSpanFull(),
                    ]))->values()->all())
                ->columnSpanFull(),
        ];
    }
}
