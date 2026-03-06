<script setup lang="ts">
import { HelpCircle, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useHelpBanner } from '@/composables/useHelpBanner';

const props = withDefaults(
    defineProps<{
        storageKey: string;
        title?: string;
    }>(),
    {
        title: 'Quick Tip',
    },
);

const { isVisible, dismiss } = useHelpBanner(props.storageKey);
</script>

<template>
    <div
        v-if="isVisible"
        class="rounded-lg border border-blue-200 bg-blue-50/60 px-4 py-3 dark:border-blue-900/50 dark:bg-blue-950/30"
    >
        <div class="flex items-start gap-3">
            <HelpCircle
                class="mt-0.5 size-4 shrink-0 text-blue-600 dark:text-blue-400"
            />
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-blue-900 dark:text-blue-200">
                    {{ title }}
                </p>
                <div class="mt-0.5 text-sm text-blue-700 dark:text-blue-300/80">
                    <slot />
                </div>
            </div>
            <Button
                variant="ghost"
                size="icon-sm"
                class="shrink-0 text-blue-400 hover:bg-blue-100 hover:text-blue-600 dark:text-blue-500 dark:hover:bg-blue-900/40 dark:hover:text-blue-300"
                @click="dismiss"
            >
                <X class="size-4" />
                <span class="sr-only">Dismiss</span>
            </Button>
        </div>
    </div>
</template>
