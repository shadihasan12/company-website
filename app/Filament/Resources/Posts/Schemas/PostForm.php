<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Content')
                ->schema([
                    ...Translatable::text('title', 'Title', required: true),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Changing this on a published post breaks existing links.'),
                    ...Translatable::textarea('excerpt', 'Excerpt', rows: 3),
                    ...Translatable::richEditor('body', 'Body'),
                ]),

            Section::make('Publishing')
                ->columns(2)
                ->schema([
                    FileUpload::make('cover_image_path')->label('Cover image')->image()->imageEditor()->directory('posts'),
                    TextInput::make('author_name'),
                    TextInput::make('reading_minutes')->numeric()->suffix('min'),
                    DateTimePicker::make('published_at')
                        ->helperText('Empty is a draft. A future date schedules it.'),
                    Toggle::make('is_featured'),
                ]),
        ])->columns(1);
    }
}
