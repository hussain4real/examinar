<?php

namespace App\Filament\Imports;

use App\Models\Question;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class QuestionImporter extends Importer
{
    protected static ?string $model = Question::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required', 'in:mcq,true_false'])
                ->example('mcq'),

            ImportColumn::make('body')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->example('What is the capital of France?'),

            ImportColumn::make('options')
                ->rules(['nullable', 'string'])
                ->castStateUsing(function (?string $state): ?array {
                    if (blank($state)) {
                        return null;
                    }

                    return array_map(
                        fn (string $option) => ['text' => trim($option)],
                        explode('|', $state),
                    );
                })
                ->example('London|Paris|Berlin|Madrid'),

            ImportColumn::make('correct_answer')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->example('Paris'),
        ];
    }

    public function resolveRecord(): Question
    {
        return new Question;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your question import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
