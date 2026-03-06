<x-filament-widgets::widget>
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/50">
        <div class="flex items-start gap-x-3">
            <x-filament::icon icon="heroicon-o-information-circle" class="h-5 w-5 shrink-0 text-blue-500 dark:text-blue-400 mt-0.5" />
            <p class="text-sm text-blue-800 dark:text-blue-200">{{ $this->message }}</p>
        </div>
    </div>
</x-filament-widgets::widget>
