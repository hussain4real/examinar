<script setup lang="ts">
import { Head, Link, router, usePoll } from '@inertiajs/vue3';
import { useEchoPublic } from '@laravel/echo-vue';
import { Clock, Play, Trophy, Users } from 'lucide-vue-next';
import HelpBanner from '@/components/HelpBanner.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import StudentLayout from '@/layouts/StudentLayout.vue';

type ExamSession = {
    id: number;
    status: 'waiting' | 'active';
    started_at: string | null;
    ended_at: string | null;
    student_count: number;
    exam: {
        id: number;
        title: string;
        description: string | null;
        duration_minutes: number;
        pass_score: number;
    };
};

type PastAttempt = {
    id: number;
    score: number;
    total_points: number;
    status: string;
    submitted_at: string | null;
    session: {
        exam: {
            id: number;
            title: string;
            pass_score: number;
        };
    };
};

defineProps<{
    sessions: ExamSession[];
    pastAttempts: PastAttempt[];
}>();

// Listen for real-time session updates on the lobby channel
useEchoPublic('lobby', '.ExamSessionStarted', () => {
    router.reload({ only: ['sessions'] });
});

useEchoPublic('lobby', '.ExamSessionEnded', () => {
    router.reload({ only: ['sessions', 'pastAttempts'] });
});

// Fallback polling in case websocket connection drops
usePoll(15000, { only: ['sessions', 'pastAttempts'] });
</script>

<template>
    <Head title="Exam Lobby" />

    <StudentLayout>
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight">Exam Lobby</h1>
                <p class="mt-1 text-muted-foreground">
                    Available exams will appear here. Wait for your instructor
                    to start a session.
                </p>
            </div>

            <HelpBanner
                storage-key="lobby"
                title="How the Lobby Works"
                class="mb-6"
            >
                Available exams appear automatically when your instructor starts
                a session. Click
                <strong>Start Exam</strong>
                on an active session to begin. Your recent results also show at
                the bottom of this page.
            </HelpBanner>

            <!-- Active Sessions -->
            <section v-if="sessions.length > 0" class="mb-10">
                <h2 class="mb-4 text-lg font-semibold">Available Sessions</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <Card v-for="session in sessions" :key="session.id">
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <CardTitle>{{ session.exam.title }}</CardTitle>
                                <Badge
                                    :variant="
                                        session.status === 'active'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{
                                        session.status === 'active'
                                            ? 'In Progress'
                                            : 'Waiting'
                                    }}
                                </Badge>
                            </div>
                            <CardDescription v-if="session.exam.description">
                                {{ session.exam.description }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div
                                class="flex flex-wrap gap-4 text-sm text-muted-foreground"
                            >
                                <div class="flex items-center gap-1.5">
                                    <Clock class="size-4" />
                                    <span
                                        >{{
                                            session.exam.duration_minutes
                                        }}
                                        min</span
                                    >
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <Trophy class="size-4" />
                                    <span
                                        >Pass:
                                        {{ session.exam.pass_score }}%</span
                                    >
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <Users class="size-4" />
                                    <span
                                        >{{
                                            session.student_count
                                        }}
                                        students</span
                                    >
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter>
                            <Link
                                v-if="session.status === 'active'"
                                :href="`/student/exam/${session.id}`"
                                class="w-full"
                            >
                                <Button class="w-full gap-2">
                                    <Play class="size-4" />
                                    Start Exam
                                </Button>
                            </Link>
                            <Button
                                v-else
                                variant="secondary"
                                class="w-full"
                                disabled
                            >
                                Waiting for Instructor...
                            </Button>
                        </CardFooter>
                    </Card>
                </div>
            </section>

            <!-- Empty State -->
            <div
                v-if="sessions.length === 0"
                class="mb-10 flex flex-col items-center justify-center rounded-xl border border-dashed py-16 text-center"
            >
                <div class="mb-4 rounded-full bg-muted p-4">
                    <Clock class="size-8 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-medium">No Active Sessions</h3>
                <p class="mt-1 max-w-sm text-sm text-muted-foreground">
                    There are no exam sessions available right now. Your
                    instructor will start one soon.
                </p>
            </div>

            <!-- Past Attempts -->
            <section v-if="pastAttempts.length > 0">
                <h2 class="mb-4 text-lg font-semibold">Recent Results</h2>
                <div class="space-y-3">
                    <Link
                        v-for="attempt in pastAttempts"
                        :key="attempt.id"
                        :href="`/student/results/${attempt.id}`"
                        class="flex items-center justify-between rounded-lg border p-4 transition hover:bg-accent"
                    >
                        <div>
                            <p class="font-medium">
                                {{ attempt.session.exam.title }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{
                                    new Date(
                                        attempt.submitted_at!,
                                    ).toLocaleDateString()
                                }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">
                                {{ attempt.score }}/{{ attempt.total_points }}
                            </p>
                            <Badge
                                :variant="
                                    Math.round(
                                        (attempt.score / attempt.total_points) *
                                            100,
                                    ) >= attempt.session.exam.pass_score
                                        ? 'default'
                                        : 'destructive'
                                "
                                class="mt-1"
                            >
                                {{
                                    Math.round(
                                        (attempt.score / attempt.total_points) *
                                            100,
                                    )
                                }}%
                            </Badge>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </StudentLayout>
</template>
