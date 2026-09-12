<?php

namespace App\Filament\Resources\Industries\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IndustryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                TextInput::make('sort_order')->numeric()->default(0),
                ...Translatable::text('name', 'Name', required: true),
            ]);
    }
}
