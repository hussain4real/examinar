<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AdminGuide extends Page
{
    protected string $view = 'filament.pages.admin-guide';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Guide';

    protected static ?string $title = 'Admin Guide';

    protected static ?int $navigationSort = 99;
}
