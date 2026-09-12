<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug($state ?? '')))
                        ->helperText('The real name. Only published if the toggle below is on.'),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    TextInput::make('website_url')->url()->columnSpanFull(),
                    FileUpload::make('logo_path')
                        ->label('Logo')
                        ->image()
                        ->directory('clients')
                        ->helperText('SVG or transparent PNG. Required to appear in the logo strip.')
                        ->columnSpanFull(),
                ]),

            Section::make('Permission to name')
                ->description('Naming a client publicly requires their written permission. Leave this off and the site shows the label below instead.')
                ->schema([
                    Toggle::make('is_named')
                        ->label('We have written permission to name this client')
                        ->live(),
                    ...collect(Translatable::text('anonymous_label', 'Anonymous label'))
                        ->map(fn ($field) => $field
                            ->hidden(fn (Get $get): bool => (bool) $get('is_named'))
                            ->helperText('Shown instead of the real name, e.g. "a leading regional bank".'))
                        ->all(),
                ]),

            Section::make('Display')
                ->columns(2)
                ->schema([
                    Toggle::make('is_featured'),
                    TextInput::make('sort_order')->numeric()->default(0),
                ]),
        ]);
    }
}
