<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    BarChart3,
    ClipboardCheck,
    GraduationCap,
    Monitor,
    Shield,
    Users,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const features = [
    {
        icon: ClipboardCheck,
        title: 'Smart Assessments',
        description:
            'Create exams with multiple question types, auto-grading, and flexible time controls.',
    },
    {
        icon: Shield,
        title: 'Anti-Cheat Protection',
        description:
            'Built-in proctoring detects tab switches, copy attempts, and screen exits in real time.',
    },
    {
        icon: BarChart3,
        title: 'Instant Analytics',
        description:
            'View scores, pass rates, and detailed breakdowns the moment students submit.',
    },
    {
        icon: Monitor,
        title: 'Live Sessions',
        description:
            'Monitor active exam sessions with real-time student progress and activity feeds.',
    },
    {
        icon: Users,
        title: 'Student Management',
        description:
            'Organize students, track histories, and manage access with a streamlined roster.',
    },
    {
        icon: GraduationCap,
        title: 'Instant Results',
        description:
            'Students get immediate feedback with scored answers and detailed explanations.',
    },
];
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-background text-foreground">
        <!-- Header -->
        <header
            class="sticky top-0 z-50 border-b border-border/40 bg-background/80 backdrop-blur-lg"
        >
            <div
                class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6"
            >
                <Link href="/" class="flex items-center gap-2.5">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-primary"
                    >
                        <GraduationCap class="size-5 text-primary-foreground" />
                    </div>
                    <span class="text-lg font-semibold tracking-tight"
                        >Examinar</span
                    >
                </Link>

                <nav class="flex items-center gap-3">
                    <template v-if="$page.props.auth.user">
                        <Button as-child>
                            <Link :href="dashboard()">Dashboard</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button variant="ghost" as-child>
                            <Link :href="login()">Log in</Link>
                        </Button>
                        <Button v-if="canRegister" as-child>
                            <Link :href="register()">Get Started</Link>
                        </Button>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,var(--color-primary)/0.06,transparent_60%)]"
            />
            <div
                class="mx-auto flex max-w-3xl flex-col items-center px-6 pt-24 pb-20 text-center lg:pt-36 lg:pb-28"
            >
                <div
                    class="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-muted/60 px-4 py-1.5 text-xs font-medium text-muted-foreground"
                >
                    <span
                        class="size-1.5 animate-pulse rounded-full bg-emerald-500"
                    />
                    Offline exam platform
                </div>

                <h1
                    class="text-4xl leading-tight font-bold tracking-tight sm:text-5xl lg:text-6xl lg:leading-[1.1]"
                >
                    Assessments made
                    <span
                        class="bg-gradient-to-r from-primary via-primary/70 to-primary bg-clip-text text-transparent"
                        >simple & secure</span
                    >
                </h1>

                <p
                    class="mt-6 max-w-xl text-lg leading-relaxed text-muted-foreground"
                >
                    Create, deliver, and grade exams on your local network.
                    Built-in anti-cheat monitoring and real-time analytics keep
                    everything fair and transparent.
                </p>

                <div
                    class="mt-10 flex flex-wrap items-center justify-center gap-4"
                >
                    <template v-if="!$page.props.auth.user">
                        <Button size="lg" as-child class="px-8">
                            <Link :href="canRegister ? register() : login()">
                                Start for Free
                            </Link>
                        </Button>
                        <Button
                            variant="outline"
                            size="lg"
                            as-child
                            class="px-8"
                        >
                            <Link :href="login()">Sign In</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button size="lg" as-child class="px-8">
                            <Link :href="dashboard()">Go to Dashboard</Link>
                        </Button>
                    </template>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="border-t border-border/40 bg-muted/30 py-20 lg:py-28">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mx-auto mb-14 max-w-2xl text-center">
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        Everything you need to run exams
                    </h2>
                    <p class="mt-3 text-muted-foreground">
                        A complete toolkit for educators and institutions, from
                        question authoring to result analysis.
                    </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="feature in features"
                        :key="feature.title"
                        class="transition-shadow hover:shadow-md"
                    >
                        <CardHeader>
                            <div
                                class="mb-2 flex size-10 items-center justify-center rounded-lg bg-primary/10"
                            >
                                <component
                                    :is="feature.icon"
                                    class="size-5 text-primary"
                                />
                            </div>
                            <CardTitle class="text-base">{{
                                feature.title
                            }}</CardTitle>
                            <CardDescription>{{
                                feature.description
                            }}</CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 lg:py-28">
            <div
                class="mx-auto flex max-w-2xl flex-col items-center px-6 text-center"
            >
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                    Ready to get started?
                </h2>
                <p class="mt-3 max-w-lg text-muted-foreground">
                    Set up your first exam in minutes. No credit card required.
                </p>
                <div class="mt-8">
                    <Button size="lg" as-child class="px-8">
                        <Link
                            :href="
                                $page.props.auth.user
                                    ? dashboard()
                                    : canRegister
                                      ? register()
                                      : login()
                            "
                        >
                            {{
                                $page.props.auth.user
                                    ? 'Go to Dashboard'
                                    : 'Create Free Account'
                            }}
                        </Link>
                    </Button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-border/40 py-8">
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-6 text-sm text-muted-foreground"
            >
                <span>&copy; {{ new Date().getFullYear() }} Examinar</span>
                <div class="flex items-center gap-1.5">
                    <GraduationCap class="size-4" />
                    <span>Assessments made simple</span>
                </div>
            </div>
        </footer>
    </div>
</template>
