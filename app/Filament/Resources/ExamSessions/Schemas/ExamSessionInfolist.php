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
                        TextEntry::make('flagged_attempts')
                            ->label('Flagged Students')
                            ->state(fn ($record): int => $record->attempts()->where('flag_count', '>', 0)->count())
                            ->badge()
                            ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray'),
                        TextEntry::make('average_score')
                            ->label('Average Score')
                            ->state(function ($record): string {
                                $attempts = $record->attempts()->whereNotNull('score');
                                if ($attempts->count() === 0) {
                                    return '—';
                                }
                                $avg = $attempts->avg('score');
                                $total = $record->attempts()->first()?->total_points ?? 0;

                                return round($avg, 1).'/'.$total;
                            }),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
