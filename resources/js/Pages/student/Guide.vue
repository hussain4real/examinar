<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowLeft,
    BookOpen,
    ClipboardCheck,
    Eye,
    Flag,
    Monitor,
    Trophy,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import StudentLayout from '@/layouts/StudentLayout.vue';
</script>

<template>
    <Head title="Student Guide" />

    <StudentLayout>
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
            <div class="mb-8 flex items-center gap-3">
                <Button variant="ghost" size="icon-sm" as-child>
                    <Link href="/dashboard">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">
                        Student Guide
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Everything you need to know about using Examinar.
                    </p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Getting Started -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <BookOpen class="size-5 text-primary" />
                            <CardTitle>Getting Started</CardTitle>
                        </div>
                        <CardDescription>
                            How to navigate the platform
                        </CardDescription>
                    </CardHeader>
                    <CardContent
                        class="space-y-3 text-sm text-muted-foreground"
                    >
                        <p>
                            Welcome to Examinar! Here&rsquo;s how the platform
                            works:
                        </p>
                        <ol class="list-inside list-decimal space-y-2">
                            <li>
                                <strong class="text-foreground"
                                    >Dashboard</strong
                                >
                                &mdash; Your home page shows overall stats:
                                total exams taken, average score, pass count,
                                and any active sessions.
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >Exam Lobby</strong
                                >
                                &mdash; This is where available exam sessions
                                appear. When your instructor starts a session,
                                it shows up here automatically.
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >Take the Exam</strong
                                >
                                &mdash; Click &ldquo;Start Exam&rdquo; on an
                                active session to begin answering questions.
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >View Results</strong
                                >
                                &mdash; After submitting, you&rsquo;ll see your
                                score, a question-by-question review, and any
                                flagged activity.
                            </li>
                        </ol>
                    </CardContent>
                </Card>

                <!-- Taking an Exam -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <ClipboardCheck class="size-5 text-primary" />
                            <CardTitle>Taking an Exam</CardTitle>
                        </div>
                        <CardDescription>
                            Step-by-step exam instructions
                        </CardDescription>
                    </CardHeader>
                    <CardContent
                        class="space-y-3 text-sm text-muted-foreground"
                    >
                        <ol class="list-inside list-decimal space-y-2">
                            <li>
                                <strong class="text-foreground"
                                    >Navigate questions</strong
                                >
                                using the numbered grid on the side or the
                                Previous/Next buttons.
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >Select your answer</strong
                                >
                                by clicking an option (MCQ) or True/False. Your
                                selection saves automatically.
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >Watch the timer</strong
                                >
                                in the top bar &mdash; it counts down and pulses
                                red in the last minute.
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >Submit when ready</strong
                                >
                                by clicking the Submit button. A confirmation
                                dialog shows how many questions you&rsquo;ve
                                answered.
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >Auto-submit</strong
                                >
                                &mdash; If time runs out, your exam is submitted
                                automatically with whatever answers you&rsquo;ve
                                given.
                            </li>
                        </ol>
                    </CardContent>
                </Card>

                <!-- Understanding Results -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <Trophy class="size-5 text-primary" />
                            <CardTitle>Understanding Results</CardTitle>
                        </div>
                        <CardDescription>
                            How scoring and review works
                        </CardDescription>
                    </CardHeader>
                    <CardContent
                        class="space-y-3 text-sm text-muted-foreground"
                    >
                        <ul class="list-inside list-disc space-y-2">
                            <li>
                                <strong class="text-foreground">Score</strong>
                                &mdash; Shown as a percentage with your raw
                                points (e.g. 5/7).
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >Pass/Fail</strong
                                >
                                &mdash; Determined by the pass score set by your
                                instructor (shown in the summary).
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >Question Review</strong
                                >
                                &mdash;
                                <span
                                    class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400"
                                    ><Eye class="inline size-3" /> Green</span
                                >
                                = correct answer,
                                <span
                                    class="inline-flex items-center gap-1 text-destructive"
                                    ><Eye class="inline size-3" /> Red</span
                                >
                                = your incorrect selection.
                            </li>
                            <li>
                                <strong class="text-foreground"
                                    >Recent Results</strong
                                >
                                &mdash; Your past attempts appear on the
                                Dashboard and Lobby for easy access.
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <!-- Anti-Cheat Rules -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <AlertTriangle class="size-5 text-amber-500" />
                            <CardTitle>Anti-Cheat Rules</CardTitle>
                        </div>
                        <CardDescription>
                            Monitored activities during exams
                        </CardDescription>
                    </CardHeader>
                    <CardContent
                        class="space-y-3 text-sm text-muted-foreground"
                    >
                        <p>
                            To ensure a fair exam environment, the following
                            activities are automatically detected and logged:
                        </p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div
                                class="flex items-start gap-2 rounded-md border p-3"
                            >
                                <Monitor
                                    class="mt-0.5 size-4 shrink-0 text-amber-500"
                                />
                                <div>
                                    <p class="font-medium text-foreground">
                                        Tab Switches
                                    </p>
                                    <p class="text-xs">
                                        Switching to another browser tab or
                                        window is recorded.
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-start gap-2 rounded-md border p-3"
                            >
                                <Monitor
                                    class="mt-0.5 size-4 shrink-0 text-amber-500"
                                />
                                <div>
                                    <p class="font-medium text-foreground">
                                        Fullscreen Exits
                                    </p>
                                    <p class="text-xs">
                                        Leaving fullscreen mode during the exam
                                        is flagged.
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-start gap-2 rounded-md border p-3"
                            >
                                <Monitor
                                    class="mt-0.5 size-4 shrink-0 text-amber-500"
                                />
                                <div>
                                    <p class="font-medium text-foreground">
                                        Copy Attempts
                                    </p>
                                    <p class="text-xs">
                                        Trying to copy exam content is blocked
                                        and logged.
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-start gap-2 rounded-md border p-3"
                            >
                                <Monitor
                                    class="mt-0.5 size-4 shrink-0 text-amber-500"
                                />
                                <div>
                                    <p class="font-medium text-foreground">
                                        Right Clicks
                                    </p>
                                    <p class="text-xs">
                                        Right-click context menus are disabled
                                        and logged.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="mt-3 flex items-start gap-2 rounded-md border border-amber-200 bg-amber-50/60 p-3 dark:border-amber-900/50 dark:bg-amber-950/30"
                        >
                            <Flag
                                class="mt-0.5 size-4 shrink-0 text-amber-600 dark:text-amber-400"
                            />
                            <p
                                class="text-sm text-amber-800 dark:text-amber-200"
                            >
                                Each violation increments your flag count, which
                                is visible to both you and your instructor. Keep
                                your flag count at zero for a clean exam record.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </StudentLayout>
</template>
