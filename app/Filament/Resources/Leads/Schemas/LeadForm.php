<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    /**
     * Leads arrive from the public site and are never authored here, so the
     * submitted fields are read-only. Only the internal columns are editable.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Submission')
                ->columns(2)
                ->schema([
                    Placeholder::make('name')->content(fn ($record) => $record?->name),
                    Placeholder::make('email')->content(fn ($record) => $record?->email),
                    Placeholder::make('phone')->content(fn ($record) => $record?->phone ?: '—'),
                    Placeholder::make('company')->content(fn ($record) => $record?->company ?: '—'),
                    Placeholder::make('service')->content(fn ($record) => (string) ($record?->service?->title ?: '—')),
                    Placeholder::make('budget_range')->label('Budget')->content(fn ($record) => $record?->budget_range ?: '—'),
                    Placeholder::make('timeline')->content(fn ($record) => $record?->timeline ?: '—'),
                    Placeholder::make('source')->content(fn ($record) => $record?->source ?: '—'),
                    Placeholder::make('message')->content(fn ($record) => $record?->message ?: '—')->columnSpanFull(),
                ]),

            Section::make('Internal')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'new' => 'New',
                            'contacted' => 'Contacted',
                            'qualified' => 'Qualified',
                            'proposal' => 'Proposal sent',
                            'won' => 'Won',
                            'lost' => 'Lost',
                            'spam' => 'Spam',
                        ])
                        ->required(),
                    DateTimePicker::make('contacted_at'),
                    Textarea::make('notes')->rows(4)->columnSpanFull(),
                ]),
        ]);
    }
}
