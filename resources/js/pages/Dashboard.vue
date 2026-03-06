<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Award,
    BarChart3,
    BookOpen,
    CheckCircle,
    Flag,
    Radio,
    XCircle,
} from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import StudentLayout from '@/layouts/StudentLayout.vue';

type RecentAttempt = {
    id: number;
    exam_title: string;
    score: number;
    total_points: number;
    percentage: number | null;
    passed: boolean | null;
    flag_count: number;
    submitted_at: string;
};

defineProps<{
    stats: {
        total_exams: number;
        average_score: number | null;
        passed_count: number;
        active_sessions: number;
    };
    recentAttempts: RecentAttempt[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <StudentLayout>
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
                <p class="mt-1 text-muted-foreground">
                    Welcome back! Here's an overview of your exam activity.
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle
                            class="text-sm font-medium text-muted-foreground"
                            >Exams Taken</CardTitle
                        >
                        <BookOpen class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">
                            {{ stats.total_exams }}
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle
                            class="text-sm font-medium text-muted-foreground"
                            >Average Score</CardTitle
                        >
                        <BarChart3 class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">
                            {{
                                stats.average_score !== null
                                    ? `${stats.average_score}%`
                                    : '—'
                            }}
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle
                            class="text-sm font-medium text-muted-foreground"
                            >Exams Passed</CardTitle
                        >
                        <Award class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">
                            {{ stats.passed_count }}
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle
                            class="text-sm font-medium text-muted-foreground"
                            >Active Sessions</CardTitle
                        >
                        <Radio class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">
                            {{ stats.active_sessions }}
                        </p>
                        <Link
                            v-if="stats.active_sessions > 0"
                            href="/student/lobby"
                            class="mt-1 inline-flex items-center gap-1 text-xs text-primary hover:underline"
                        >
                            Go to Lobby
                            <ArrowRight class="size-3" />
                        </Link>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Results -->
            <section>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Recent Results</h2>
                    <Link
                        v-if="recentAttempts.length > 0"
                        href="/student/lobby"
                        class="text-sm text-muted-foreground hover:text-foreground"
                    >
                        View all
                    </Link>
                </div>

                <div v-if="recentAttempts.length > 0" class="space-y-3">
                    <Link
                        v-for="attempt in recentAttempts"
                        :key="attempt.id"
                        :href="`/student/results/${attempt.id}`"
                        class="block"
                    >
                        <Card class="transition hover:border-foreground/20">
                            <CardContent class="flex items-center gap-4 py-4">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-full"
                                    :class="
                                        attempt.passed
                                            ? 'bg-green-100 dark:bg-green-900/30'
                                            : 'bg-red-100 dark:bg-red-900/30'
                                    "
                                >
                                    <CheckCircle
                                        v-if="attempt.passed"
                                        class="size-5 text-green-600 dark:text-green-400"
                                    />
                                    <XCircle
                                        v-else
                                        class="size-5 text-red-600 dark:text-red-400"
                                    />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-medium">
                                        {{ attempt.exam_title }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ attempt.submitted_at }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <Badge
                                        v-if="attempt.flag_count > 0"
                                        variant="destructive"
                                        class="gap-1"
                                    >
                                        <Flag class="size-3" />
                                        {{ attempt.flag_count }}
                                    </Badge>
                                    <span class="font-medium"
                                        >{{ attempt.score }}/{{
                                            attempt.total_points
                                        }}</span
                                    >
                                    <Badge
                                        :variant="
                                            attempt.passed
                                                ? 'default'
                                                : 'destructive'
                                        "
                                    >
                                        {{ attempt.percentage?.toFixed(1) }}%
                                    </Badge>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>

                <Card v-else>
                    <CardContent class="py-12 text-center">
                        <BookOpen
                            class="mx-auto mb-3 size-10 text-muted-foreground"
                        />
                        <p class="text-lg font-medium">No exams taken yet</p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Head to the Exam Lobby to start your first exam.
                        </p>
                        <Link href="/student/lobby" class="mt-4 inline-block">
                            <Button variant="outline" class="gap-2">
                                Go to Lobby
                                <ArrowRight class="size-4" />
                            </Button>
                        </Link>
                    </CardContent>
                </Card>
            </section>
        </div>
    </StudentLayout>
</template>
