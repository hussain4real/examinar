<?php

namespace App\Filament\Resources\ExamSessions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExamSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Session Setup')
                    ->schema([
                        Select::make('exam_id')
                            ->relationship('exam', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Exam'),

                        Select::make('status')
                            ->options([
                                'waiting' => 'Waiting',
                                'active' => 'Active',
                                'completed' => 'Completed',
                            ])
                            ->default('waiting')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
