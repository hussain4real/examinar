<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResultController extends Controller
{
    public function __invoke(Request $request, ExamAttempt $examAttempt): Response
    {
        abort_if($examAttempt->user_id !== $request->user()->id, 403);
        abort_if($examAttempt->status === 'in_progress', 404);

        $examAttempt->load([
            'session.exam:id,title,pass_score,duration_minutes',
            'answers.question:id,type,body,options,correct_answer,points',
        ]);

        return Inertia::render('student/Results', [
            'attempt' => [
                'id' => $examAttempt->id,
                'score' => $examAttempt->score,
                'total_points' => $examAttempt->total_points,
                'percentage' => $examAttempt->percentage,
                'passed' => $examAttempt->passed,
                'started_at' => $examAttempt->started_at->toISOString(),
                'submitted_at' => $examAttempt->submitted_at?->toISOString(),
                'flag_count' => $examAttempt->flag_count,
            ],
            'exam' => [
                'title' => $examAttempt->session->exam->title,
                'pass_score' => $examAttempt->session->exam->pass_score,
            ],
            'answers' => $examAttempt->answers->map(fn ($a) => [
                'question_id' => $a->question_id,
                'selected_answer' => $a->selected_answer,
                'is_correct' => $a->is_correct,
                'question' => [
                    'body' => $a->question->body,
                    'type' => $a->question->type,
                    'options' => $a->question->options,
                    'correct_answer' => $a->question->correct_answer,
                    'points' => $a->question->points,
                ],
            ]),
        ]);
    }
}
