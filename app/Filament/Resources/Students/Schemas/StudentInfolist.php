<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Student Information')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Registered'),
                    ])
                    ->columns(3),

                Section::make('Exam History')
                    ->schema([
                        TextEntry::make('exam_attempts_count')
                            ->state(fn ($record): int => $record->examAttempts()->count())
                            ->label('Total Attempts'),
                    ]),
            ]);
    }
}
