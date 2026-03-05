<?php

namespace App\Filament\Resources\ExamSessions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExamSessionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Session Info')
                    ->schema([
                        TextEntry::make('exam.title')
                            ->label('Exam'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'waiting' => 'gray',
                                'active' => 'success',
                                'completed' => 'info',
                            }),
                        TextEntry::make('started_at')
                            ->dateTime()
                            ->placeholder('Not started'),
                        TextEntry::make('ended_at')
                            ->dateTime()
                            ->placeholder('In progress'),
                    ])
                    ->columns(2),

                Section::make('Participation')
                    ->schema([
                        TextEntry::make('attempts_count')
                            ->state(fn ($record): int => $record->attempts()->count())
                            ->label('Total Students'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
