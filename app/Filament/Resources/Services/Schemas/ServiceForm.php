<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('key')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Must match the key in config/site.php — it drives the navigation.'),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Select::make('icon')
                        ->required()
                        ->options(collect(config('site.services'))
                            ->pluck('icon', 'icon')
                            ->merge(['sparkles' => 'sparkles'])
                            ->map(fn (string $icon) => Str::headline($icon))),
                    TextInput::make('sort_order')->numeric()->default(0),
                    Toggle::make('is_published')->default(true),
                ]),

            Section::make('Copy')
                ->schema([
                    ...Translatable::text('title', 'Title', required: true),
                    ...Translatable::text('tagline', 'Tagline'),
                    ...Translatable::textarea('excerpt', 'Excerpt', rows: 3),
                    ...Translatable::richEditor('body', 'Page body'),
                ]),

            Section::make('Commercials')
                ->columns(2)
                ->collapsed()
                ->schema([
                    ...Translatable::text('starting_price', 'Starting price'),
                    ...Translatable::text('timeline', 'Typical timeline'),
                ]),

            Section::make('Details')
                ->collapsed()
                ->schema([
                    TagsInput::make('inclusions')
                        ->label("What's included")
                        ->helperText('One bullet per tag.')
                        ->columnSpanFull(),

                    Repeater::make('faqs')
                        ->label('FAQs')
                        ->helperText('Also published as FAQPage structured data.')
                        ->schema([
                            TextInput::make('question')->required(),
                            TextInput::make('answer')->required(),
                        ])
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
