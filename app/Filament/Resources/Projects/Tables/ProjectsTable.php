<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('hero_image_path')->label('')->square(),
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('client.name')->label('Client')->searchable()->toggleable(),
                TextColumn::make('industry.slug')->badge()->color('gray')->toggleable(),

                // Surfaces missing proof at a glance rather than burying it
                // inside each record.
                TextColumn::make('evidence')
                    ->label('Evidence')
                    ->badge()
                    ->state(fn ($record): string => $record->is_evidenced ? 'complete' : static::missing($record))
                    ->color(fn ($record): string => $record->is_evidenced ? 'success' : 'warning'),

                IconColumn::make('is_featured')->boolean()->toggleable(),
                IconColumn::make('is_published')->boolean(),
            ])
            ->filters([
                SelectFilter::make('services')->relationship('services', 'key')->multiple()->preload(),
                SelectFilter::make('industry')->relationship('industry', 'slug')->preload(),
                TernaryFilter::make('is_published'),
                TernaryFilter::make('needs_evidence')
                    ->label('Missing screenshots or results')
                    ->queries(
                        true: fn (Builder $query) => $query->where(fn (Builder $q) => $q
                            ->whereNull('gallery')->orWhere('gallery', '[]')
                            ->orWhereNull('metrics')->orWhere('metrics', '[]')),
                        false: fn (Builder $query) => $query,
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    protected static function missing($record): string
    {
        return collect([
            'screenshots' => blank($record->gallery),
            'results' => blank($record->metrics),
            'link' => ! $record->has_public_link,
        ])->filter()->keys()->implode(', ');
    }
}
