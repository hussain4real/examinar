<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class HelpBannerWidget extends Widget
{
    protected string $view = 'filament.widgets.help-banner';

    protected int|string|array $columnSpan = 'full';

    public string $message = '';

    public static bool $isLazy = false;

    /**
     * Hide from the dashboard — only used as a header widget on resource pages.
     */
    public static function canView(): bool
    {
        return false;
    }
}
