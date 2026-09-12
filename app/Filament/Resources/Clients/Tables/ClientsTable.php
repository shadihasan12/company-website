<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('logo_path')->label('')->square(),
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('display_name')
                    ->label('Shown publicly as')
                    ->color(fn ($record): string => $record->is_named ? 'success' : 'warning'),
                IconColumn::make('is_named')->label('Permitted')->boolean(),
                TextColumn::make('projects_count')->counts('projects')->label('Projects'),
                IconColumn::make('is_featured')->boolean()->toggleable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
