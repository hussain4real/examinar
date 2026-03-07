<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\AntiCheatLog;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExamController extends Controller
{
    /**
     * Start or resume an exam attempt.
     */
    public function show(Request $request, ExamSession $examSession): Response|RedirectResponse
    {
        if (! $examSession->isActive()) {
            return redirect()->route('student.lobby')
                ->with('error', 'This exam session is not currently active.');
        }

        $user = $request->user();

        // Find existing attempt or create a new one
        $attempt = ExamAttempt::query()
            ->where('exam_session_id', $examSession->id)
            ->where('user_id', $user->id)
            ->first();

        if ($attempt && $attempt->status === 'submitted') {
            return redirect()->route('student.results', $attempt);
        }

        if (! $attempt) {
            $attempt = ExamAttempt::create([
                'exam_session_id' => $examSession->id,
                'user_id' => $user->id,
                'started_at' => now(),
                'total_points' => $examSession->exam->total_points,
                'status' => 'in_progress',
                'flag_count' => 0,
            ]);
        }

        $exam = $examSession->exam;
        $questions = $exam->questions()->get();

        if ($exam->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        // Strip correct_answer from questions sent to the client
        $questions = $questions->map(fn ($q) => [
            'id' => $q->id,
            'type' => $q->type,
            'body' => $q->body,
            'options' => $q->options ? array_map(fn ($o) => is_array($o) ? $o['text'] : $o, $q->options) : null,
            'points' => $q->pivot->points,
            'order' => $q->pivot->order,
        ]);

        // Get existing answers for this attempt
        $existingAnswers = $attempt->answers()
            ->pluck('selected_answer', 'question_id')
            ->toArray();

        return Inertia::render('student/ExamTake', [
            'examSession' => [
                'id' => $examSession->id,
                'status' => $examSession->status,
                'started_at' => $examSession->started_at?->toISOString(),
                'ended_at' => $examSession->ended_at?->toISOString(),
            ],
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'duration_minutes' => $exam->duration_minutes,
            ],
            'attempt' => [
                'id' => $attempt->id,
                'user_id' => $attempt->user_id,
                'started_at' => $attempt->started_at->toISOString(),
                'status' => $attempt->status,
            ],
            'questions' => $questions,
            'existingAnswers' => $existingAnswers,
        ]);
    }

    /**
     * Check session status (polling fallback for websocket).
     */
    public function status(Request $request, ExamSession $examSession): array
    {
        $attempt = ExamAttempt::query()
            ->where('exam_session_id', $examSession->id)
            ->where('user_id', $request->user()->id)
            ->first();

        return [
            'status' => $examSession->status,
            'attempt_status' => $attempt?->status,
        ];
    }

    /**
     * Save a single answer.
     */
    public function answer(Request $request, ExamSession $examSession): array
    {
        if (! $examSession->isActive()) {
            return ['saved' => false, 'session_ended' => true];
        }

        $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'selected_answer' => ['required', 'string'],
        ]);

        $attempt = ExamAttempt::query()
            ->where('exam_session_id', $examSession->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        Answer::updateOrCreate(
            [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $request->input('question_id'),
            ],
            [
                'selected_answer' => $request->input('selected_answer'),
            ]
        );

        return ['saved' => true];
    }

    /**
     * Submit the entire exam.
     */
    public function submit(Request $request, ExamSession $examSession): RedirectResponse
    {
        $attempt = ExamAttempt::query()
            ->where('exam_session_id', $examSession->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'in_progress')
            ->first();

        if (! $attempt) {
            $submitted = ExamAttempt::query()
                ->where('exam_session_id', $examSession->id)
                ->where('user_id', $request->user()->id)
                ->whereIn('status', ['submitted', 'kicked'])
                ->first();

            if ($submitted) {
                return redirect()->route('student.results', $submitted);
            }

            return redirect()->route('student.lobby');
        }

        $this->gradeAttempt($attempt);

        return redirect()->route('student.results', $attempt);
    }

    /**
     * Log an anti-cheat event.
     */
    public function logAntiCheat(Request $request, ExamSession $examSession): array
    {
        $request->validate([
            'event_type' => ['required', 'string', 'in:tab_switch,fullscreen_exit,copy_attempt,right_click'],
            'details' => ['nullable', 'array'],
        ]);

        $attempt = ExamAttempt::query()
            ->where('exam_session_id', $examSession->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'in_progress')
            ->first();

        if (! $attempt) {
            return ['logged' => false];
        }

        AntiCheatLog::create([
            'exam_attempt_id' => $attempt->id,
            'event_type' => $request->input('event_type'),
            'details' => $request->input('details'),
        ]);

        $attempt->increment('flag_count');

        return ['logged' => true, 'flag_count' => $attempt->fresh()->flag_count];
    }

    /**
     * Grade an attempt by comparing answers against correct answers.
     */
    private function gradeAttempt(ExamAttempt $attempt): void
    {
        $attempt->load(['answers', 'session.exam.questions']);

        $questions = $attempt->session->exam->questions->keyBy('id');

        $score = 0;

        foreach ($attempt->answers as $answer) {
            $question = $questions->get($answer->question_id);

            if (! $question) {
                continue;
            }

            $isCorrect = strtolower(trim($answer->selected_answer)) === strtolower(trim($question->correct_answer));

            $answer->update(['is_correct' => $isCorrect]);

            if ($isCorrect) {
                $score += $question->pivot->points;
            }
        }

        $attempt->update([
            'score' => $score,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);
    }
}
