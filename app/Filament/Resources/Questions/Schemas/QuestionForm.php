<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Question')
                    ->schema([
                        Select::make('type')
                            ->options([
                                'mcq' => 'Multiple Choice',
                                'true_false' => 'True / False',
                            ])
                            ->required()
                            ->live()
                            ->default('mcq'),

                        RichEditor::make('body')
                            ->label('Question Text')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Answer Options')
                    ->schema([
                        Repeater::make('options')
                            ->schema([
                                TextInput::make('text')
                                    ->required(),
                            ])
                            ->default([
                                ['text' => ''],
                                ['text' => ''],
                                ['text' => ''],
                                ['text' => ''],
                            ])
                            ->minItems(2)
                            ->maxItems(6)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('correct_answer')
                            ->label('Correct Answer (exact text of the correct option)')
                            ->required(),
                    ])
                    ->visible(fn (Get $get): bool => $get('type') === 'mcq'),

                Section::make('Answer')
                    ->schema([
                        Radio::make('correct_answer')
                            ->options([
                                'true' => 'True',
                                'false' => 'False',
                            ])
                            ->required(),
                    ])
                    ->visible(fn (Get $get): bool => $get('type') === 'true_false'),
            ]);
    }
}
