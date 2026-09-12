<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable()->since(),
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('company')->searchable()->toggleable(),
                TextColumn::make('service.title')->label('Service')->toggleable(),
                TextColumn::make('budget_range')->label('Budget')->badge()->color('gray')->toggleable(),
                TextColumn::make('source')->badge()->color('gray'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'won' => 'success',
                        'lost', 'spam' => 'danger',
                        default => 'info',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'qualified' => 'Qualified',
                    'proposal' => 'Proposal sent',
                    'won' => 'Won',
                    'lost' => 'Lost',
                    'spam' => 'Spam',
                ]),
                SelectFilter::make('source')->options([
                    'contact' => 'Contact form',
                    'estimator' => 'Cost estimator',
                    'newsletter' => 'Newsletter',
                ]),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
