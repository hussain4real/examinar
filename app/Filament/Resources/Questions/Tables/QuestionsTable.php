<?php

namespace App\Filament\Resources\Questions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('body')
                    ->html()
                    ->limit(80)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mcq' => 'info',
                        'true_false' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'mcq' => 'MCQ',
                        'true_false' => 'True/False',
                    })
                    ->sortable(),
                TextColumn::make('correct_answer')
                    ->limit(30),
                TextColumn::make('exams_count')
                    ->counts('exams')
                    ->label('Used in Exams')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'mcq' => 'Multiple Choice',
                        'true_false' => 'True / False',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
