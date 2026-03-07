<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ServerInfo extends Widget
{
    protected string $view = 'filament.widgets.server-info';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -1;

    public function getLanIp(): string
    {
        $hostname = gethostname();
        $ip = gethostbyname($hostname);

        if ($ip === $hostname) {
            return 'Unable to detect';
        }

        return $ip;
    }

    public function getServerUrl(): string
    {
        return request()->getSchemeAndHttpHost();
    }

    public function getStudentUrl(): string
    {
        $ip = $this->getLanIp();

        if ($ip === 'Unable to detect') {
            return $ip;
        }

        return "http://{$ip}:8000";
    }
}
