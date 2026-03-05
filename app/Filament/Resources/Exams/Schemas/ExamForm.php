<?php

namespace App\Filament\Resources\Exams\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Exam Details')
                    ->columns(2)
                    ->components([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        RichEditor::make('description')
                            ->columnSpanFull(),
                        TextInput::make('duration_minutes')
                            ->label('Duration (minutes)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(480)
                            ->default(60)
                            ->suffix('minutes'),
                        TextInput::make('pass_score')
                            ->label('Pass Score (%)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(50)
                            ->suffix('%'),
                    ]),
                Section::make('Settings')
                    ->columns(2)
                    ->components([
                        Toggle::make('shuffle_questions')
                            ->label('Shuffle Questions')
                            ->helperText('Randomize question order for each student'),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->default('draft')
                            ->required(),
                    ]),
            ]);
    }
}
