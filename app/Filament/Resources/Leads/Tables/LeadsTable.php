<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            ->headerActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn ($livewire) => static::export($livewire->getFilteredTableQuery())),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    /**
     * Streams the currently filtered leads as CSV.
     *
     * Streamed rather than built in memory so exporting a large pipeline
     * does not depend on how much memory PHP happens to have.
     */
    protected static function export($query): StreamedResponse
    {
        $columns = ['id', 'created_at', 'name', 'email', 'phone', 'company', 'service', 'budget_range', 'timeline', 'source', 'status', 'message'];

        return Response::streamDownload(function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            $query->with('service')->chunk(200, function ($leads) use ($handle) {
                foreach ($leads as $lead) {
                    fputcsv($handle, [
                        $lead->id,
                        $lead->created_at?->toDateTimeString(),
                        $lead->name,
                        $lead->email,
                        $lead->phone,
                        $lead->company,
                        (string) ($lead->service?->title ?? ''),
                        $lead->budget_range,
                        $lead->timeline,
                        $lead->source,
                        $lead->status,
                        $lead->message,
                    ]);
                }
            });

            fclose($handle);
        }, 'leads-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }
}
