<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    Select::make('client_id')->relationship('client', 'name')->searchable()->preload(),
                    Select::make('industry_id')->relationship('industry', 'slug')->searchable()->preload(),
                    Select::make('services')->relationship('services', 'key')->multiple()->preload(),
                    Select::make('technologies')->relationship('technologies', 'name')->multiple()->preload()->searchable(),
                ]),

            Section::make('Case study')
                ->description('Which problem, what we did, what changed. Outcomes with numbers are what actually convert.')
                ->schema([
                    ...Translatable::textarea('summary', 'Summary', rows: 2),
                    ...Translatable::richEditor('problem', 'The problem'),
                    ...Translatable::richEditor('solution', 'What we built'),
                    ...Translatable::richEditor('outcome', 'The outcome'),
                    ...Translatable::text('duration', 'Duration'),
                ]),

            Section::make('Results')
                ->description('Drives the animated results band. Leave empty rather than guessing — invented figures on a live page are a liability.')
                ->schema([
                    Repeater::make('metrics')
                        ->hiddenLabel()
                        ->schema([
                            TextInput::make('label')->required(),
                            TextInput::make('value')->numeric()->required(),
                            TextInput::make('prefix')->maxLength(4),
                            TextInput::make('suffix')->maxLength(4),
                            TextInput::make('decimals')->numeric()->default(0),
                        ])
                        ->columns(5)
                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                        ->columnSpanFull(),
                ]),

            Section::make('Imagery')
                ->description('3–8 screenshots per project. Without them a case study reads as a claim rather than evidence.')
                ->schema([
                    FileUpload::make('hero_image_path')
                        ->image()
                        ->directory('projects/hero')
                        ->imageEditor()
                        ->maxSize(4096),
                    FileUpload::make('gallery')
                        ->label('Screenshots')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->directory('projects/gallery')
                        ->maxSize(4096)
                        ->columnSpanFull(),
                ]),

            Section::make('Links & publishing')
                ->columns(2)
                ->schema([
                    TextInput::make('website_url')->url()->prefixIcon('heroicon-o-globe-alt'),
                    TextInput::make('app_store_url')->url()->label('App Store URL'),
                    TextInput::make('google_play_url')->url()->label('Google Play URL'),
                    DatePicker::make('completed_at'),
                    TextInput::make('sort_order')->numeric()->default(0),
                    Toggle::make('is_featured'),
                    Toggle::make('is_published')->default(true),
                ]),
        ]);
    }
}
