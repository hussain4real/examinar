<?php

namespace App\Filament\Resources\Exams\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExamInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Exam Details')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'published' => 'success',
                                'archived' => 'warning',
                            }),
                        TextEntry::make('description')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Settings')
                    ->schema([
                        TextEntry::make('duration_minutes')
                            ->label('Duration')
                            ->suffix(' minutes'),
                        TextEntry::make('pass_score')
                            ->label('Pass Score')
                            ->suffix('%'),
                        IconEntry::make('shuffle_questions')
                            ->boolean()
                            ->label('Shuffle Questions'),
                        TextEntry::make('questions_count')
                            ->state(fn ($record): int => $record->questions()->count())
                            ->label('Total Questions'),
                    ])
                    ->columns(2),

                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('creator.name')
                            ->label('Created By'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }
}
