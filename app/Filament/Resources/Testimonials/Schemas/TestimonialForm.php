<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attribution')
                ->description('A named person with a title and a photo outperforms an anonymous quote by a wide margin.')
                ->columns(2)
                ->schema([
                    TextInput::make('author_name')->required(),
                    ...Translatable::text('author_title', 'Job title'),
                    Select::make('client_id')->relationship('client', 'name')->searchable()->preload(),
                    Select::make('project_id')->relationship('project', 'name')->searchable()->preload(),
                    FileUpload::make('avatar_path')->label('Photo')->image()->avatar()->directory('testimonials'),
                ]),

            Section::make('Quote')
                ->schema([
                    ...Translatable::textarea('quote', 'Quote', required: true, rows: 4),
                    TextInput::make('rating')->numeric()->minValue(1)->maxValue(5),
                    Toggle::make('is_featured'),
                    TextInput::make('sort_order')->numeric()->default(0),
                ]),
        ]);
    }
}
