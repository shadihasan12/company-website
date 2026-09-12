<?php

namespace App\Filament\Resources\Technologies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TechnologyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Technology names are proper nouns — never translated.
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Select::make('category')->required()->default('tool')->options([
                    'language' => 'Language',
                    'framework' => 'Framework',
                    'service' => 'Service',
                    'tool' => 'Tool',
                ]),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }
}
