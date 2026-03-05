<?php

namespace App\Filament\Widgets;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Published Exams', Exam::where('status', 'published')->count())
                ->description('Ready for sessions')
                ->color('success'),
            Stat::make('Active Sessions', ExamSession::where('status', 'active')->count())
                ->description('Currently running')
                ->color('warning'),
            Stat::make('Registered Students', Student::count())
                ->description('Total enrolled')
                ->color('info'),
        ];
    }
}
