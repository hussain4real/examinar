<script setup lang="ts">
import { Head, router, useHttp } from '@inertiajs/vue3';
import { useEchoPublic } from '@laravel/echo-vue';
import {
    AlertTriangle,
    ChevronLeft,
    ChevronRight,
    Clock,
    Flag,
    OctagonX,
    Send,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import HelpBanner from '@/components/HelpBanner.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Question = {
    id: number;
    type: 'mcq' | 'true_false';
    body: string;
    options: string[] | null;
    points: number;
    order: number;
};

type Props = {
    examSession: {
        id: number;
        status: string;
        started_at: string | null;
        ended_at: string | null;
    };
    exam: {
        id: number;
        title: string;
        duration_minutes: number;
    };
    attempt: {
        id: number;
        user_id: number;
        started_at: string;
        status: string;
    };
    questions: Question[];
    existingAnswers: Record<number, string>;
};

const props = defineProps<Props>();

// State
const currentIndex = ref(0);
const answers = ref<Record<number, string>>({ ...props.existingAnswers });
const submitting = ref(false);
const showConfirmSubmit = ref(false);
const sessionEnded = ref(false);
const studentKicked = ref(false);

// Timer
const timeRemaining = ref(0);

function calculateTimeRemaining(): number {
    const startedAt = new Date(props.attempt.started_at).getTime();
    const durationMs = props.exam.duration_minutes * 60 * 1000;
    const endTime = startedAt + durationMs;
    return Math.max(0, Math.floor((endTime - Date.now()) / 1000));
}

timeRemaining.value = calculateTimeRemaining();

const timerInterval = setInterval(() => {
    timeRemaining.value = calculateTimeRemaining();
    if (timeRemaining.value <= 0) {
        clearInterval(timerInterval);
        autoSubmit();
    }
}, 1000);

const formattedTime = computed(() => {
    const minutes = Math.floor(timeRemaining.value / 60);
    const seconds = timeRemaining.value % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

const isTimeLow = computed(() => timeRemaining.value < 60);

// Questions navigation
const currentQuestion = computed(() => props.questions[currentIndex.value]);
const totalQuestions = computed(() => props.questions.length);
const answeredCount = computed(() => Object.keys(answers.value).length);

function goToQuestion(index: number): void {
    if (index >= 0 && index < totalQuestions.value) {
        currentIndex.value = index;
    }
}

// HTTP helpers for non-navigation requests
const answerHttp = useHttp({
    question_id: 0,
    selected_answer: '',
});

const antiCheatHttp = useHttp({
    event_type: '',
    details: {} as Record<string, unknown> | undefined,
});

// Answer handling
function selectAnswer(questionId: number, answer: string): void {
    answers.value[questionId] = answer;
    saveAnswer(questionId, answer);
}

function saveAnswer(questionId: number, selectedAnswer: string): void {
    answerHttp.question_id = questionId;
    answerHttp.selected_answer = selectedAnswer;
    answerHttp.post(`/student/exam/${props.examSession.id}/answer`, {
        onSuccess: (response: any) => {
            if (response?.session_ended) {
                autoSubmit();
            }
        },
    });
}

// Submission
function confirmSubmit(): void {
    showConfirmSubmit.value = true;
}

function cancelSubmit(): void {
    showConfirmSubmit.value = false;
}

function submitExam(): void {
    submitting.value = true;
    router.post(
        `/student/exam/${props.examSession.id}/submit`,
        {},
        {
            preserveScroll: true,
        },
    );
}

function autoSubmit(): void {
    if (submitting.value) return;
    submitting.value = true;
    sessionEnded.value = true;
    setTimeout(() => {
        router.post(`/student/exam/${props.examSession.id}/submit`);
    }, 3000);
}

function handleKicked(): void {
    if (submitting.value) return;
    submitting.value = true;
    studentKicked.value = true;
    setTimeout(() => {
        router.post(`/student/exam/${props.examSession.id}/submit`);
    }, 4000);
}

// Listen for session end
useEchoPublic(
    `exam-session.${props.examSession.id}`,
    '.ExamSessionEnded',
    () => {
        autoSubmit();
    },
);

// Listen for individual kick
useEchoPublic(
    `exam-session.${props.examSession.id}`,
    '.StudentKicked',
    (event: { user_id: number }) => {
        if (event.user_id === props.attempt.user_id) {
            handleKicked();
        }
    },
);

// Poll session status as fallback for websocket failures
const statusPollInterval = setInterval(async () => {
    try {
        const res = await fetch(`/student/exam/${props.examSession.id}/status`);
        if (res.ok) {
            const data = await res.json();
            if (data.attempt_status === 'kicked') {
                clearInterval(statusPollInterval);
                handleKicked();
            } else if (data.status === 'completed') {
                clearInterval(statusPollInterval);
                autoSubmit();
            }
        }
    } catch {
        // Network error — next poll will retry
    }
}, 10000);

// Anti-cheat
const flagCount = ref(0);

function logAntiCheatEvent(
    eventType: string,
    details?: Record<string, unknown>,
): void {
    antiCheatHttp.event_type = eventType;
    antiCheatHttp.details = details;
    antiCheatHttp.post(`/student/exam/${props.examSession.id}/anti-cheat`, {
        onSuccess: (response: any) => {
            if (response?.flag_count !== undefined) {
                flagCount.value = response.flag_count;
            }
        },
    });
}

function getEventContext(): Record<string, unknown> {
    return {
        question_number: currentIndex.value + 1,
        question_id: currentQuestion.value?.id,
        time_remaining: timeRemaining.value,
        answered: answeredCount.value,
    };
}

function handleVisibilityChange(): void {
    if (document.hidden) {
        logAntiCheatEvent('tab_switch', getEventContext());
    }
}

function handleContextMenu(e: MouseEvent): void {
    e.preventDefault();
    logAntiCheatEvent('right_click', getEventContext());
}

function handleCopy(e: ClipboardEvent): void {
    e.preventDefault();
    logAntiCheatEvent('copy_attempt', getEventContext());
}

function handleFullscreenChange(): void {
    if (!document.fullscreenElement) {
        logAntiCheatEvent('fullscreen_exit', getEventContext());
    }
}

onMounted(() => {
    document.addEventListener('visibilitychange', handleVisibilityChange);
    document.addEventListener('contextmenu', handleContextMenu);
    document.addEventListener('copy', handleCopy);
    document.addEventListener('fullscreenchange', handleFullscreenChange);

    // Try to enter fullscreen
    document.documentElement.requestFullscreen?.().catch(() => {
        // Fullscreen not supported or denied — that's fine
    });
});

onBeforeUnmount(() => {
    clearInterval(timerInterval);
    clearInterval(statusPollInterval);
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    document.removeEventListener('contextmenu', handleContextMenu);
    document.removeEventListener('copy', handleCopy);
    document.removeEventListener('fullscreenchange', handleFullscreenChange);

    if (document.fullscreenElement) {
        document.exitFullscreen?.();
    }
});
</script>

<template>
    <Head :title="`Exam: ${exam.title}`" />

    <div class="flex min-h-screen flex-col bg-background select-none">
        <!-- Top Bar -->
        <header
            class="sticky top-0 z-50 border-b bg-background/95 backdrop-blur"
        >
            <div
                class="mx-auto flex h-14 max-w-5xl items-center justify-between px-4 sm:px-6"
            >
                <div class="flex items-center gap-3">
                    <h1 class="text-base font-semibold sm:text-lg">
                        {{ exam.title }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Flag indicator -->
                    <div
                        v-if="flagCount > 0"
                        class="flex items-center gap-1 text-destructive"
                    >
                        <Flag class="size-4" />
                        <span class="text-xs font-medium">{{ flagCount }}</span>
                    </div>

                    <!-- Progress -->
                    <Badge variant="secondary" class="hidden sm:inline-flex">
                        {{ answeredCount }}/{{ totalQuestions }} answered
                    </Badge>

                    <!-- Timer -->
                    <div
                        class="flex items-center gap-1.5 rounded-md px-3 py-1.5 font-mono text-sm font-semibold"
                        :class="{
                            'animate-pulse bg-destructive/10 text-destructive':
                                isTimeLow,
                            'bg-muted': !isTimeLow,
                        }"
                    >
                        <Clock class="size-4" />
                        {{ formattedTime }}
                    </div>
                </div>
            </div>
        </header>

        <div class="mx-auto w-full max-w-5xl px-4 pt-4 sm:px-6">
            <HelpBanner storage-key="exam-take" title="Exam Tips">
                Your answers save automatically. Use the question grid to
                navigate. Don&rsquo;t switch tabs or leave fullscreen &mdash;
                it&rsquo;s monitored.
            </HelpBanner>
        </div>

        <!-- Main Content -->
        <div
            class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 px-4 py-6 sm:flex-row sm:px-6"
        >
            <!-- Question Navigation Sidebar -->
            <aside class="order-2 sm:order-1 sm:w-48 sm:shrink-0">
                <div class="sticky top-20">
                    <p
                        class="mb-2 text-xs font-medium text-muted-foreground uppercase"
                    >
                        Questions
                    </p>
                    <div class="grid grid-cols-5 gap-1.5 sm:grid-cols-4">
                        <button
                            v-for="(q, i) in questions"
                            :key="q.id"
                            @click="goToQuestion(i)"
                            class="flex size-9 items-center justify-center rounded-md text-sm font-medium transition"
                            :class="{
                                'bg-primary text-primary-foreground':
                                    i === currentIndex,
                                'bg-primary/20 text-primary hover:bg-primary/30':
                                    i !== currentIndex && answers[q.id],
                                'bg-muted hover:bg-accent':
                                    i !== currentIndex && !answers[q.id],
                            }"
                        >
                            {{ i + 1 }}
                        </button>
                    </div>

                    <!-- Submit button for sidebar -->
                    <div class="mt-6 hidden sm:block">
                        <Button
                            variant="default"
                            class="w-full gap-2"
                            @click="confirmSubmit"
                            :disabled="submitting"
                        >
                            <Send class="size-4" />
                            Submit Exam
                        </Button>
                    </div>
                </div>
            </aside>

            <!-- Question Area -->
            <div class="order-1 flex-1 sm:order-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-base">
                                Question {{ currentIndex + 1 }} of
                                {{ totalQuestions }}
                            </CardTitle>
                            <Badge variant="outline"
                                >{{ currentQuestion.points }} pt{{
                                    currentQuestion.points !== 1 ? 's' : ''
                                }}</Badge
                            >
                        </div>
                    </CardHeader>
                    <CardContent>
                        <!-- Question Body -->
                        <div
                            class="mb-6 text-base leading-relaxed"
                            v-html="currentQuestion.body"
                        />

                        <!-- MCQ Options -->
                        <div
                            v-if="
                                currentQuestion.type === 'mcq' &&
                                currentQuestion.options
                            "
                            class="space-y-3"
                        >
                            <button
                                v-for="(
                                    option, optIndex
                                ) in currentQuestion.options"
                                :key="optIndex"
                                @click="
                                    selectAnswer(currentQuestion.id, option)
                                "
                                class="flex w-full items-start gap-3 rounded-lg border p-4 text-left transition"
                                :class="{
                                    'border-primary bg-primary/5 ring-2 ring-primary':
                                        answers[currentQuestion.id] === option,
                                    'hover:bg-accent':
                                        answers[currentQuestion.id] !== option,
                                }"
                            >
                                <span
                                    class="flex size-7 shrink-0 items-center justify-center rounded-full border text-sm font-medium"
                                    :class="{
                                        'border-primary bg-primary text-primary-foreground':
                                            answers[currentQuestion.id] ===
                                            option,
                                        'border-muted-foreground/30':
                                            answers[currentQuestion.id] !==
                                            option,
                                    }"
                                >
                                    {{ String.fromCharCode(65 + optIndex) }}
                                </span>
                                <span class="pt-0.5">{{ option }}</span>
                            </button>
                        </div>

                        <!-- True/False Options -->
                        <div
                            v-else-if="currentQuestion.type === 'true_false'"
                            class="grid grid-cols-2 gap-3"
                        >
                            <button
                                v-for="opt in ['True', 'False']"
                                :key="opt"
                                @click="selectAnswer(currentQuestion.id, opt)"
                                class="rounded-lg border p-4 text-center font-medium transition"
                                :class="{
                                    'border-primary bg-primary/5 ring-2 ring-primary':
                                        answers[currentQuestion.id] === opt,
                                    'hover:bg-accent':
                                        answers[currentQuestion.id] !== opt,
                                }"
                            >
                                {{ opt }}
                            </button>
                        </div>

                        <!-- Navigation -->
                        <div class="mt-8 flex items-center justify-between">
                            <Button
                                variant="outline"
                                :disabled="currentIndex === 0"
                                @click="goToQuestion(currentIndex - 1)"
                                class="gap-1.5"
                            >
                                <ChevronLeft class="size-4" />
                                Previous
                            </Button>

                            <Button
                                v-if="currentIndex < totalQuestions - 1"
                                @click="goToQuestion(currentIndex + 1)"
                                class="gap-1.5"
                            >
                                Next
                                <ChevronRight class="size-4" />
                            </Button>

                            <Button
                                v-else
                                @click="confirmSubmit"
                                class="gap-1.5"
                                :disabled="submitting"
                            >
                                <Send class="size-4" />
                                Submit
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Mobile submit -->
                <div class="mt-4 sm:hidden">
                    <Button
                        class="w-full gap-2"
                        @click="confirmSubmit"
                        :disabled="submitting"
                    >
                        <Send class="size-4" />
                        Submit Exam
                    </Button>
                </div>
            </div>
        </div>

        <!-- Session Ended Overlay -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
            >
                <div
                    v-if="sessionEnded && !studentKicked"
                    class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60"
                >
                    <div
                        class="mx-4 w-full max-w-sm rounded-xl bg-background p-6 text-center shadow-lg"
                    >
                        <div
                            class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-destructive/10"
                        >
                            <OctagonX
                                class="size-7 text-destructive"
                            />
                        </div>
                        <h2 class="text-lg font-semibold">
                            Exam Session Ended
                        </h2>
                        <p
                            class="mt-2 text-sm text-muted-foreground"
                        >
                            The examiner has ended this session. Your
                            answers have been saved and are being
                            submitted automatically.
                        </p>
                        <div
                            class="mt-4 flex items-center justify-center gap-2 text-sm text-muted-foreground"
                        >
                            <svg
                                class="size-4 animate-spin"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                />
                            </svg>
                            Redirecting to results&hellip;
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Student Kicked Overlay -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
            >
                <div
                    v-if="studentKicked"
                    class="fixed inset-0 z-[120] flex items-center justify-center bg-black/60"
                >
                    <div
                        class="mx-4 w-full max-w-sm rounded-xl bg-background p-6 text-center shadow-lg"
                    >
                        <div
                            class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-destructive/10"
                        >
                            <AlertTriangle
                                class="size-7 text-destructive"
                            />
                        </div>
                        <h2 class="text-lg font-semibold">
                            You Have Been Removed
                        </h2>
                        <p
                            class="mt-2 text-sm text-muted-foreground"
                        >
                            The examiner has removed you from this exam
                            session. Your answers have been saved and
                            graded.
                        </p>
                        <div
                            class="mt-4 flex items-center justify-center gap-2 text-sm text-muted-foreground"
                        >
                            <svg
                                class="size-4 animate-spin"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                />
                            </svg>
                            Redirecting to results&hellip;
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Confirm Submit Modal -->
        <Teleport to="body">
            <div
                v-if="showConfirmSubmit"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50"
            >
                <div
                    class="mx-4 w-full max-w-md rounded-xl bg-background p-6 shadow-lg"
                >
                    <div class="mb-4 flex items-center gap-3">
                        <div
                            class="flex size-10 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30"
                        >
                            <AlertTriangle
                                class="size-5 text-amber-600 dark:text-amber-400"
                            />
                        </div>
                        <h2 class="text-lg font-semibold">Submit Exam?</h2>
                    </div>

                    <p class="mb-2 text-sm text-muted-foreground">
                        You've answered <strong>{{ answeredCount }}</strong> of
                        <strong>{{ totalQuestions }}</strong> questions.
                    </p>

                    <p
                        v-if="answeredCount < totalQuestions"
                        class="mb-6 text-sm text-destructive"
                    >
                        {{ totalQuestions - answeredCount }} question(s) are
                        still unanswered.
                    </p>
                    <p v-else class="mb-6 text-sm text-muted-foreground">
                        All questions answered. Ready to submit?
                    </p>

                    <div class="flex gap-3">
                        <Button
                            variant="outline"
                            class="flex-1"
                            @click="cancelSubmit"
                        >
                            Go Back
                        </Button>
                        <Button
                            class="flex-1"
                            :disabled="submitting"
                            @click="submitExam"
                        >
                            {{
                                submitting ? 'Submitting...' : 'Confirm Submit'
                            }}
                        </Button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
