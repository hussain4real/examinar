<?php

namespace App\Filament\Resources\ExamSessions\RelationManagers;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'attempts';

    protected static ?string $title = 'Student Attempts';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'submitted', 'graded' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('score')
                    ->label('Score')
                    ->formatStateUsing(fn ($record): string => "{$record->score}/{$record->total_points}"),
                TextColumn::make('percentage')
                    ->label('Percentage')
                    ->suffix('%'),
                TextColumn::make('flag_count')
                    ->label('Flags')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state <= 3 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Student'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('score')
                    ->formatStateUsing(fn ($record): string => "{$record->score}/{$record->total_points}"),
                TextEntry::make('flag_count')
                    ->label('Anti-Cheat Flags')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state <= 3 => 'warning',
                        default => 'danger',
                    }),
                TextEntry::make('started_at')
                    ->dateTime(),
                TextEntry::make('submitted_at')
                    ->dateTime(),
                TextEntry::make('antiCheatLogs')
                    ->label('Anti-Cheat Events')
                    ->listWithLineBreaks()
                    ->formatStateUsing(fn ($state): string => is_array($state)
                        ? "[{$state['created_at']}] {$state['event_type']}"
                        : (string) $state
                    ),
            ]);
    }
}
