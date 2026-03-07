<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-6">
            <div class="flex items-center gap-x-2">
                <x-filament::icon icon="heroicon-o-server" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">LAN IP:</span>
                <span class="text-sm font-semibold">{{ $this->getLanIp() }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <x-filament::icon icon="heroicon-o-globe-alt" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Server URL:</span>
                <span class="text-sm font-semibold">{{ $this->getServerUrl() }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <x-filament::icon icon="heroicon-o-computer-desktop" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Student URL:</span>
                <span class="text-sm font-semibold">{{ $this->getStudentUrl() }}</span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
