<?php

namespace App\Filament\Resources\Exams\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Schema $schema): Schema
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

                        TextInput::make('points')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1),

                        TextInput::make('order')
                            ->numeric()
                            ->default(0),
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

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('body')
                    ->html()
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mcq' => 'info',
                        'true_false' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'mcq' => 'MCQ',
                        'true_false' => 'True/False',
                    }),
                TextColumn::make('points')
                    ->sortable(),
                TextColumn::make('correct_answer')
                    ->limit(30),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
