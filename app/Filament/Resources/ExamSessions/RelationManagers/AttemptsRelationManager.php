<?php

namespace App\Filament\Resources\ExamSessions\RelationManagers;

use App\Models\ExamAttempt;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
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
                        'kicked' => 'danger',
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
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('kick')
                    ->label('Kick')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Kick Student')
                    ->modalDescription('This will end the student\'s exam, grade their current answers, and remove them from the session. This cannot be undone.')
                    ->modalSubmitActionLabel('Kick Student')
                    ->visible(fn (ExamAttempt $record): bool => $record->status === 'in_progress')
                    ->action(function (ExamAttempt $record): void {
                        $record->session->kickStudent($record);
                    })
                    ->successNotificationTitle('Student has been kicked from the exam.'),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attempt Details')
                    ->schema([
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
                    ])
                    ->columns(3),

                Section::make('Anti-Cheat Logs')
                    ->schema([
                        RepeatableEntry::make('antiCheatLogs')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Event'),
                                TableColumn::make('Details'),
                                TableColumn::make('Time'),
                            ])
                            ->schema([
                                TextEntry::make('event_type')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'tab_switch' => 'warning',
                                        'fullscreen_exit' => 'danger',
                                        'copy_attempt' => 'danger',
                                        'right_click' => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('details')
                                    ->formatStateUsing(function ($state): string {
                                        if (is_array($state)) {
                                            return collect($state)
                                                ->map(fn ($v, $k) => "{$k}: {$v}")
                                                ->implode(', ');
                                        }

                                        return $state ? (string) $state : '—';
                                    }),
                                TextEntry::make('created_at')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsible()
                    ->visible(fn ($record): bool => $record->antiCheatLogs()->exists()),
            ]);
    }
}
