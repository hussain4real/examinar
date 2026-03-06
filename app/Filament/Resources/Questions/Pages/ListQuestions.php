<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Imports\QuestionImporter;
use App\Filament\Resources\Questions\QuestionResource;
use App\Filament\Widgets\HelpBannerWidget;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(QuestionImporter::class),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            HelpBannerWidget::make([
                'message' => 'Build your question bank here. Questions can be reused across multiple exams. Use CSV import for bulk uploads.',
            ]),
        ];
    }
}
