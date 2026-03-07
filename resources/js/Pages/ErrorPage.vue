<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { AlertTriangle, ArrowLeft, Home } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    status: number;
}>();

const title = computed(() => {
    return (
        {
            503: '503: Service Unavailable',
            500: '500: Server Error',
            404: '404: Page Not Found',
            403: '403: Forbidden',
        }[props.status] ?? `${props.status}: Error`
    );
});

const description = computed(() => {
    return (
        {
            503: 'Sorry, we are doing some maintenance. Please check back soon.',
            500: 'Whoops, something went wrong on our servers.',
            404: 'Sorry, the page you are looking for could not be found.',
            403: 'Sorry, you are forbidden from accessing this page.',
        }[props.status] ?? 'An unexpected error occurred.'
    );
});
</script>

<template>
    <Head :title="title" />

    <div class="flex min-h-screen items-center justify-center bg-background px-4">
        <div class="mx-auto max-w-md text-center">
            <div class="mb-6 flex justify-center">
                <div class="rounded-full bg-destructive/10 p-4">
                    <AlertTriangle class="size-12 text-destructive" />
                </div>
            </div>

            <h1 class="mb-2 text-4xl font-bold tracking-tight text-foreground">
                {{ status }}
            </h1>

            <h2 class="mb-4 text-lg font-medium text-muted-foreground">
                {{ title }}
            </h2>

            <p class="mb-8 text-muted-foreground">
                {{ description }}
            </p>

            <div class="flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                <Button as-child variant="outline">
                    <a href="javascript:history.back()">
                        <ArrowLeft class="mr-2 size-4" />
                        Go Back
                    </a>
                </Button>

                <Button as-child>
                    <Link href="/">
                        <Home class="mr-2 size-4" />
                        Home
                    </Link>
                </Button>
            </div>
        </div>
    </div>
</template>
