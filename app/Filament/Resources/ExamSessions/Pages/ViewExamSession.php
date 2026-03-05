<?php

namespace App\Filament\Resources\ExamSessions\Pages;

use App\Filament\Resources\ExamSessions\ExamSessionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExamSession extends ViewRecord
{
    protected static string $resource = ExamSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
