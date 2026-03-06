<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { DoorOpen, LayoutDashboard, LogOut, User } from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();

const currentPath = page.url;
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background">
        <header
            class="sticky top-0 z-50 border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
        >
            <div
                class="mx-auto flex h-14 max-w-5xl items-center justify-between px-4 sm:px-6"
            >
                <div class="flex items-center gap-6">
                    <Link href="/dashboard" class="flex items-center gap-2">
                        <div
                            class="flex size-8 items-center justify-center rounded-md bg-primary text-primary-foreground"
                        >
                            <AppLogoIcon class="size-5 fill-current" />
                        </div>
                        <span class="text-lg font-semibold">Examinar</span>
                    </Link>

                    <nav class="hidden items-center gap-1 sm:flex">
                        <Link
                            href="/dashboard"
                            class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm transition"
                            :class="
                                currentPath === '/dashboard'
                                    ? 'bg-accent font-medium text-accent-foreground'
                                    : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                            "
                        >
                            <LayoutDashboard class="size-4" />
                            Dashboard
                        </Link>
                        <Link
                            href="/student/lobby"
                            class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm transition"
                            :class="
                                currentPath.startsWith('/student/lobby')
                                    ? 'bg-accent font-medium text-accent-foreground'
                                    : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                            "
                        >
                            <DoorOpen class="size-4" />
                            Exam Lobby
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-2 text-sm text-muted-foreground"
                    >
                        <User class="size-4" />
                        <span>{{ page.props.auth.user.name }}</span>
                    </div>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm text-muted-foreground transition hover:bg-accent hover:text-accent-foreground"
                    >
                        <LogOut class="size-4" />
                        Logout
                    </Link>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>
    </div>
</template>
