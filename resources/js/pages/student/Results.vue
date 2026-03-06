<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowLeft,
    CheckCircle,
    Clock,
    Copy,
    Eye,
    Flag,
    Maximize,
    MousePointer,
    Trophy,
    XCircle,
} from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import StudentLayout from '@/layouts/StudentLayout.vue';

type AnswerReview = {
    question_id: number;
    selected_answer: string;
    is_correct: boolean;
    question: {
        body: string;
        type: 'mcq' | 'true_false';
        options: string[] | null;
        correct_answer: string;
        points: number;
    };
};

type AntiCheatLog = {
    event_type: string;
    details: Record<string, unknown> | null;
    created_at: string;
};

defineProps<{
    attempt: {
        id: number;
        score: number;
        total_points: number;
        percentage: number | null;
        passed: boolean | null;
        started_at: string;
        submitted_at: string | null;
        flag_count: number;
    };
    exam: {
        title: string;
        pass_score: number;
    };
    answers: AnswerReview[];
    antiCheatLogs: AntiCheatLog[];
}>();

const eventLabels: Record<string, string> = {
    tab_switch: 'Tab Switch',
    fullscreen_exit: 'Fullscreen Exit',
    copy_attempt: 'Copy Attempt',
    right_click: 'Right Click',
};

const eventIcons: Record<string, typeof Eye> = {
    tab_switch: Eye,
    fullscreen_exit: Maximize,
    copy_attempt: Copy,
    right_click: MousePointer,
};

function formatTime(iso: string): string {
    return new Date(iso).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
}

function formatDuration(start: string, end: string): string {
    const diff = new Date(end).getTime() - new Date(start).getTime();
    const minutes = Math.floor(diff / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);
    return `${minutes}m ${seconds}s`;
}
</script>

<template>
    <Head :title="`Results: ${exam.title}`" />

    <StudentLayout>
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
            <!-- Back link -->
            <Link
                href="/student/lobby"
                class="mb-6 inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="size-4" />
                Back to Lobby
            </Link>

            <!-- Summary Card -->
            <Card class="mb-8">
                <CardHeader>
                    <CardTitle class="text-xl">{{ exam.title }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <!-- Score Display -->
                    <div
                        class="mb-6 flex flex-col items-center gap-4 rounded-xl bg-muted/50 py-8"
                    >
                        <div
                            class="flex size-20 items-center justify-center rounded-full"
                            :class="
                                attempt.passed
                                    ? 'bg-green-100 dark:bg-green-900/30'
                                    : 'bg-red-100 dark:bg-red-900/30'
                            "
                        >
                            <Trophy
                                class="size-10"
                                :class="
                                    attempt.passed
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-red-600 dark:text-red-400'
                                "
                            />
                        </div>
                        <div class="text-center">
                            <p class="text-4xl font-bold">
                                {{ attempt.percentage?.toFixed(1) }}%
                            </p>
                            <p class="mt-1 text-lg text-muted-foreground">
                                {{ attempt.score }} /
                                {{ attempt.total_points }} points
                            </p>
                        </div>
                        <Badge
                            :variant="
                                attempt.passed ? 'default' : 'destructive'
                            "
                            class="px-4 py-1 text-sm"
                        >
                            {{ attempt.passed ? 'PASSED' : 'FAILED' }}
                        </Badge>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div class="rounded-lg border p-3">
                            <Trophy
                                class="mx-auto mb-1 size-5 text-muted-foreground"
                            />
                            <p class="font-medium">Pass Score</p>
                            <p class="text-muted-foreground">
                                {{ exam.pass_score }}%
                            </p>
                        </div>
                        <div class="rounded-lg border p-3">
                            <Clock
                                class="mx-auto mb-1 size-5 text-muted-foreground"
                            />
                            <p class="font-medium">Duration</p>
                            <p class="text-muted-foreground">
                                <template v-if="attempt.submitted_at">
                                    {{
                                        formatDuration(
                                            attempt.started_at,
                                            attempt.submitted_at,
                                        )
                                    }}
                                </template>
                                <template v-else>—</template>
                            </p>
                        </div>
                        <div class="rounded-lg border p-3">
                            <Flag
                                class="mx-auto mb-1 size-5 text-muted-foreground"
                            />
                            <p class="font-medium">Flags</p>
                            <p class="text-muted-foreground">
                                {{ attempt.flag_count }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Answer Review -->
            <h2 class="mb-4 text-lg font-semibold">Question Review</h2>
            <div class="space-y-4">
                <Card
                    v-for="(answer, index) in answers"
                    :key="answer.question_id"
                >
                    <CardHeader>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">
                                <CheckCircle
                                    v-if="answer.is_correct"
                                    class="size-5 text-green-600 dark:text-green-400"
                                />
                                <XCircle
                                    v-else
                                    class="size-5 text-red-600 dark:text-red-400"
                                />
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <CardTitle class="text-sm"
                                        >Question {{ index + 1 }}</CardTitle
                                    >
                                    <Badge variant="outline" class="text-xs">
                                        {{
                                            answer.is_correct
                                                ? answer.question.points
                                                : 0
                                        }}/{{ answer.question.points }} pts
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div
                            class="mb-4 text-sm"
                            v-html="answer.question.body"
                        />

                        <!-- Options review -->
                        <div
                            v-if="
                                answer.question.type === 'mcq' &&
                                answer.question.options
                            "
                            class="space-y-2"
                        >
                            <div
                                v-for="option in answer.question.options"
                                :key="option"
                                class="flex items-center gap-2 rounded-md border px-3 py-2 text-sm"
                                :class="{
                                    'border-green-500 bg-green-50 dark:bg-green-900/20':
                                        option ===
                                        answer.question.correct_answer,
                                    'border-red-500 bg-red-50 dark:bg-red-900/20':
                                        option === answer.selected_answer &&
                                        option !==
                                            answer.question.correct_answer,
                                }"
                            >
                                <span class="flex-1">{{ option }}</span>
                                <CheckCircle
                                    v-if="
                                        option ===
                                        answer.question.correct_answer
                                    "
                                    class="size-4 text-green-600"
                                />
                                <XCircle
                                    v-else-if="
                                        option === answer.selected_answer
                                    "
                                    class="size-4 text-red-600"
                                />
                            </div>
                        </div>

                        <!-- True/False review -->
                        <div v-else class="grid grid-cols-2 gap-2">
                            <div
                                v-for="opt in ['True', 'False']"
                                :key="opt"
                                class="rounded-md border px-3 py-2 text-center text-sm"
                                :class="{
                                    'border-green-500 bg-green-50 dark:bg-green-900/20':
                                        opt.toLowerCase() ===
                                        answer.question.correct_answer.toLowerCase(),
                                    'border-red-500 bg-red-50 dark:bg-red-900/20':
                                        opt.toLowerCase() ===
                                            answer.selected_answer?.toLowerCase() &&
                                        opt.toLowerCase() !==
                                            answer.question.correct_answer.toLowerCase(),
                                }"
                            >
                                {{ opt }}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Anti-Cheat Activity -->
            <section v-if="antiCheatLogs.length > 0" class="mt-8">
                <div class="mb-4 flex items-center gap-2">
                    <AlertTriangle class="size-5 text-amber-500" />
                    <h2 class="text-lg font-semibold">
                        Flagged Activity ({{ antiCheatLogs.length }})
                    </h2>
                </div>
                <Card>
                    <CardContent class="p-0">
                        <div class="divide-y">
                            <div
                                v-for="(log, i) in antiCheatLogs"
                                :key="i"
                                class="flex items-center gap-3 px-4 py-3"
                            >
                                <div
                                    class="flex size-8 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30"
                                >
                                    <component
                                        :is="
                                            eventIcons[log.event_type] ||
                                            AlertTriangle
                                        "
                                        class="size-4 text-amber-600 dark:text-amber-400"
                                    />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium">
                                        {{
                                            eventLabels[log.event_type] ||
                                            log.event_type
                                        }}
                                    </p>
                                    <p
                                        v-if="log.details"
                                        class="text-xs text-muted-foreground"
                                    >
                                        <template
                                            v-if="log.details.question_number"
                                        >
                                            Question
                                            {{ log.details.question_number }}
                                        </template>
                                        <template
                                            v-if="log.details.time_remaining"
                                        >
                                            &middot;
                                            {{
                                                Math.floor(
                                                    Number(
                                                        log.details
                                                            .time_remaining,
                                                    ) / 60,
                                                )
                                            }}m
                                            {{
                                                Number(
                                                    log.details.time_remaining,
                                                ) % 60
                                            }}s remaining
                                        </template>
                                    </p>
                                </div>
                                <span
                                    class="shrink-0 text-xs text-muted-foreground"
                                >
                                    {{ formatTime(log.created_at) }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </section>

            <!-- Back to lobby -->
            <div class="mt-8 text-center">
                <Link href="/student/lobby">
                    <Button variant="outline" class="gap-2">
                        <ArrowLeft class="size-4" />
                        Back to Lobby
                    </Button>
                </Link>
            </div>
        </div>
    </StudentLayout>
</template>
